<?php
$flash        = session()->getFlashdata('message');
$batches      = $list_batches ?? [];
$batchId      = (int) ($selected_batch_id ?? 0);
$batch        = $selected_batch ?? [];
$rows         = $list_detail ?? [];
$problems     = $list_problems ?? [];
$sum          = $summary ?? [];
$branches     = $branch_options ?? [];
$coaOptions   = $coa_options ?? [];
$headers      = $header_columns ?? [];
$detailLimit  = (int) ($detail_limit ?? 300);
$problemLimit = (int) ($problem_limit ?? 50);

$statusBadge = static function ($status) {
    switch (strtoupper(trim((string) $status))) {
        case 'PROCESSED':
            return 'badge-success';
        case 'PARTIAL':
            return 'badge-warning';
        case 'VALIDATED':
            return 'badge-info';
        case 'VALIDATION_ERROR':
            return 'badge-danger';
        case 'PROCESSING':
            return 'badge-primary';
        default:
            return 'badge-secondary';
    }
};

$defaultBranch = ['idbranch' => '', 'cabang' => ''];

foreach ($branches as $branchRow) {
    if (strtoupper((string) ($branchRow['cdefault'] ?? '')) === 'YES') {
        $defaultBranch = $branchRow;
        break;
    }
}

$processBranch = ! empty($batch['idbranch']) ? (string) $batch['idbranch'] : (string) ($defaultBranch['idbranch'] ?? '');
$processCabang = ! empty($batch['cabang']) ? (string) $batch['cabang'] : (string) ($defaultBranch['cabang'] ?? '');
?>
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

<?php if (! empty($flash)) { echo $flash; } ?>

<div class="row">
    <div class="col-xl-4 col-lg-5">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-upload"></i> Upload Saldo Awal AR</h3>
            </div>
            <form action="<?= esc($upload_url) ?>" method="post" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <div class="form-group">
                        <label for="import">File Excel Opening AR (.xls / .xlsx)</label>
                        <input type="file" id="import" name="import" accept=".xls,.xlsx" required>

                        <p class="help-block mb-0">
                            File <b>Template_opening_ar.xlsx</b>: hanya sheet
                            <code><?= esc($sheet_name) ?></code> yang diproses, baris 1 = header,
                            data mulai baris 2. Maksimal 10 MB.
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_saldo">Tanggal Saldo (dipakai bila kolom tanggal_saldo kosong)</label>
                        <input type="date" class="form-control" id="tanggal_saldo" name="tanggal_saldo"
                               value="<?= esc($tanggal_default) ?>" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="tanggungjawab" required>
                        <label class="form-check-label" for="tanggungjawab">Saya bertanggung jawab atas data yang saya upload.</label>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= esc($template_url) ?>" class="btn btn-default">
                        <i class="fa fa-download"></i> Download Template
                    </a>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fa fa-upload"></i> Upload &amp; Validasi
                    </button>
                </div>
            </form>
        </div>


        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Urutan Header Template</h3>
            </div>
            <div class="card-body" style="max-height: 260px; overflow-y: auto;">
                <ol class="pl-3 mb-0 small">
                    <?php foreach ($headers as $column) { ?>
                        <li><code><?= esc($column) ?></code></li>
                    <?php } ?>
                </ol>
            </div>
            <div class="card-footer small text-muted">
                Kolom <b>kode_prk</b> = perkiraan dokumen (dari file), <b>counter COA</b> (perkiraan lawan)
                diisi pada form Proses.
            </div>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Ringkasan</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><th>Batch staging</th><td class="text-right"><?= number_format((int) ($sum['batch'] ?? 0), 0, ',', '.') ?></td></tr>
                        <tr><th>Baris staging</th><td class="text-right"><?= number_format((int) ($sum['baris'] ?? 0), 0, ',', '.') ?></td></tr>
                        <tr><th>READY</th><td class="text-right"><?= number_format((int) ($sum['ready'] ?? 0), 0, ',', '.') ?></td></tr>
                        <tr><th>ERROR</th><td class="text-right"><?= number_format((int) ($sum['error'] ?? 0), 0, ',', '.') ?></td></tr>
                        <tr><th>Warning</th><td class="text-right"><?= number_format((int) ($sum['warning'] ?? 0), 0, ',', '.') ?></td></tr>
                        <tr><th>Sudah diproses</th><td class="text-right"><?= number_format((int) ($sum['processed'] ?? 0), 0, ',', '.') ?></td></tr>
                        <tr><th>Nilai belum diposting</th><td class="text-right"><?= number_format((float) ($sum['nilai_belum'] ?? 0), 2, ',', '.') ?></td></tr>
                        <tr><th>Transaksi OPENAR</th><td class="text-right"><?= number_format((int) ($sum['trx'] ?? 0), 0, ',', '.') ?></td></tr>
                        <tr>
                            <th>Debet / Kredit OPENAR</th>
                            <td class="text-right">
                                <?= number_format((float) ($sum['debet'] ?? 0), 2, ',', '.') ?> /
                                <?= number_format((float) ($sum['kredit'] ?? 0), 2, ',', '.') ?>
                            </td>
                        </tr>
                        <tr><th>Customer terposting</th><td class="text-right"><?= number_format((int) ($sum['customer'] ?? 0), 0, ',', '.') ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="col-xl-8 col-lg-7">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-list"></i> Daftar Batch Opening AR</h3>
            </div>
            <div class="card-body table-responsive" style="overflow-x:auto;">
                <table class="table table-bordered table-striped table-sm" style="white-space:nowrap;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Batch No</th>
                        <th>Tanggal Saldo</th>
                        <th>Status</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">READY</th>
                        <th class="text-right">ERROR</th>
                        <th class="text-right">Posted</th>
                        <th class="text-right">Nilai</th>
                        <th>Counter COA</th>
                        <th>Oleh</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($batches === []) { ?>
                        <tr><td colspan="12" class="text-center text-muted">Belum ada batch. Upload file terlebih dahulu.</td></tr>
                    <?php } ?>
                    <?php foreach ($batches as $row) {
                        $rowId    = (int) ($row['batch_id'] ?? 0);
                        $isActive = $rowId === $batchId;
                        ?>
                        <tr class="<?= $isActive ? 'table-active' : '' ?>">
                            <td><?= $rowId ?></td>
                            <td>
                                <a href="<?= esc($upload_url . '?batch=' . $rowId) ?>"><b><?= esc($row['batch_no'] ?? '') ?></b></a><br>
                                <small class="text-muted"><?= esc($row['filename'] ?? '') ?></small>
                            </td>
                            <td><?= esc($row['tanggal_saldo'] ?? '') ?></td>
                            <td><span class="badge <?= $statusBadge($row['status'] ?? '') ?>"><?= esc($row['status'] ?? '') ?></span></td>
                            <td class="text-right"><?= number_format((int) ($row['total_rows'] ?? 0), 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format((int) ($row['ready_rows'] ?? 0), 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format((int) ($row['error_rows'] ?? 0), 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format((int) ($row['processed_rows'] ?? 0), 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format((float) ($row['nilai_total'] ?? 0), 2, ',', '.') ?></td>
                            <td><?= esc($row['counter_idcoa'] ?? '') ?></td>
                            <td>
                                <?= esc($row['createdby'] ?? '') ?><br>
                                <small class="text-muted"><?= esc($row['createddate'] ?? '') ?></small>
                            </td>
                            <td>
                                <form action="<?= esc($validasi_url) ?>" method="post" style="display:inline-block;">
                                    <input type="hidden" name="batch_id" value="<?= $rowId ?>">
                                    <button type="submit" class="btn btn-xs btn-info" title="Validasi staging">
                                        <i class="fa fa-check-square-o"></i> Validasi
                                    </button>
                                </form>
                                <a href="<?= esc($upload_url . '?batch=' . $rowId) ?>" class="btn btn-xs btn-default" title="Pilih batch">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer small text-muted">
                Baris ERROR tidak menghalangi proses: baris READY tetap diposting dan batch berstatus
                <b>PARTIAL</b>.
            </div>
        </div>
    </div>
</div>



<?php if ($batchId > 0) { ?>
<div class="row">
    <div class="col-md-5">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-play-circle"></i> Proses Batch #<?= $batchId ?></h3>
            </div>
            <form action="<?= esc($proses_url) ?>" method="post" role="form"
                  onsubmit="return confirm('Proses batch #<?= $batchId ?> ke sc_trx.transaction_dt (OPENAR)?');">
                <input type="hidden" name="batch_id" value="<?= $batchId ?>">
                <div class="card-body">
                    <dl class="row mb-2 small">
                        <dt class="col-4">Batch No</dt><dd class="col-8"><?= esc($batch['batch_no'] ?? '') ?></dd>
                        <dt class="col-4">Status</dt>
                        <dd class="col-8"><span class="badge <?= $statusBadge($batch['status'] ?? '') ?>"><?= esc($batch['status'] ?? '') ?></span></dd>
                        <dt class="col-4">Tanggal Saldo</dt><dd class="col-8"><?= esc($batch['tanggal_saldo'] ?? '') ?></dd>
                        <dt class="col-4">Total baris</dt><dd class="col-8"><?= number_format((int) ($batch['total_rows'] ?? 0), 0, ',', '.') ?></dd>
                    </dl>
                    <div class="form-group">
                        <label for="counter_idcoa">Counter COA (perkiraan lawan) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="counter_idcoa" name="counter_idcoa"
                               list="coa-list" maxlength="20" required
                               value="<?= esc($batch['counter_idcoa'] ?? '') ?>"
                               placeholder="contoh: 311101">
                        <datalist id="coa-list">
                            <?php foreach ($coaOptions as $coa) { ?>
                                <option value="<?= esc($coa['idcoa'] ?? '') ?>"><?= esc($coa['nmcoa'] ?? '') ?></option>
                            <?php } ?>
                        </datalist>
                        <p class="help-block mb-0 small">
                            Perkiraan lawan untuk seluruh baris batch ini (mis. akun saldo awal / ekuitas).
                            Kode perkiraan tiap baris tetap memakai <b>kode_prk</b> dari file.
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="idbranch">Cabang (idbranch)</label>
                        <select class="form-control" id="idbranch" name="idbranch">
                            <option value="">- pakai cabang default -</option>
                            <?php foreach ($branches as $branchRow) { ?>
                                <option value="<?= esc($branchRow['idbranch'] ?? '') ?>"
                                    <?= ((string) ($branchRow['idbranch'] ?? '') === $processBranch) ? 'selected' : '' ?>>
                                    <?= esc(($branchRow['idbranch'] ?? '') . ' - ' . ($branchRow['cabang'] ?? '')) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label for="cabang">Nama Cabang</label>
                        <input type="text" class="form-control" id="cabang" name="cabang" maxlength="30"
                               value="<?= esc($processCabang) ?>">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-play"></i> Proses ke Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>



    <div class="col-md-7">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa fa-exclamation-triangle"></i> Baris ERROR / Warning
                    (maks. <?= $problemLimit ?>)
                </h3>
            </div>
            <div class="card-body table-responsive" style="overflow-x:auto;">
                <table class="table table-bordered table-sm" style="white-space:nowrap;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>No Jurnal</th>
                        <th>Customer</th>
                        <th>Perkiraan</th>
                        <th>DK</th>
                        <th class="text-right">Nilai</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($problems === []) { ?>
                        <tr><td colspan="8" class="text-center text-muted">Tidak ada baris bermasalah pada batch ini.</td></tr>
                    <?php } ?>
                    <?php foreach ($problems as $row) { ?>
                        <tr>
                            <td><?= esc($row['row_no'] ?? '') ?></td>
                            <td><?= esc($row['no_jurnal'] ?? '') ?></td>
                            <td><?= esc($row['kode_customer'] ?? '') ?></td>
                            <td><?= esc($row['kode_prk'] ?? '') ?></td>
                            <td><?= esc($row['dk'] ?? '') ?></td>
                            <td class="text-right"><?= number_format((float) ($row['nilai'] ?? 0), 2, ',', '.') ?></td>
                            <td><span class="badge <?= $statusBadge($row['row_status'] ?? '') ?>"><?= esc($row['row_status'] ?? '') ?></span></td>
                            <td style="min-width:320px;white-space:normal;">
                                <?php if (! empty($row['error_message'])) { ?>
                                    <div class="text-danger"><?= esc($row['error_message']) ?></div>
                                <?php } ?>
                                <?php if (! empty($row['warning_message'])) { ?>
                                    <div class="text-warning"><?= esc($row['warning_message']) ?></div>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer small text-muted">
                Baris ERROR tidak diposting. Perbaiki file lalu upload ulang (batch baru); baris yang sudah
                <b>PROCESSED</b> tidak akan diposting dua kali.
            </div>
        </div>
    </div>
</div>



<div class="row">
    <div class="col-12">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa fa-table"></i> Detail Staging Batch #<?= $batchId ?>
                    (maks. <?= $detailLimit ?> baris)
                </h3>
            </div>
            <div class="card-body table-responsive" style="overflow-x:auto;">
                <table id="dtl_ar" class="table table-bordered table-striped table-sm" style="white-space:nowrap;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>tanggal_saldo</th>
                        <th>no_jurnal</th>
                        <th>kode_customer</th>
                        <th>nama_customer</th>
                        <th>kode_prk</th>
                        <th>tanggal_dokumen</th>
                        <th>tanggal_jatuh_tempo</th>
                        <th>dk</th>
                        <th>mata_uang</th>
                        <th>kurs</th>
                        <th class="text-right">nilai</th>
                        <th>umur_hari</th>
                        <th>kode_salesman</th>
                        <th>status</th>
                        <th>error</th>
                        <th>warning</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($rows === []) { ?>
                        <tr><td colspan="17" class="text-center text-muted">Belum ada baris staging pada batch ini.</td></tr>
                    <?php } ?>
                    <?php foreach ($rows as $row) { ?>
                        <tr>
                            <td><?= esc($row['row_no'] ?? '') ?></td>
                            <td><?= esc($row['tanggal_saldo'] ?? '') ?></td>
                            <td><?= esc($row['no_jurnal'] ?? '') ?></td>
                            <td><?= esc($row['kode_customer'] ?? '') ?></td>
                            <td><?= esc($row['nama_customer'] ?? '') ?></td>
                            <td><?= esc($row['kode_prk'] ?? '') ?></td>
                            <td><?= esc($row['tanggal_dokumen'] ?? '') ?></td>
                            <td><?= esc($row['tanggal_jatuh_tempo'] ?? '') ?></td>
                            <td><?= esc($row['dk'] ?? '') ?></td>
                            <td><?= esc($row['mata_uang'] ?? '') ?></td>
                            <td class="text-right"><?= number_format((float) ($row['kurs'] ?? 0), 2, ',', '.') ?></td>
                            <td class="text-right"><?= number_format((float) ($row['nilai'] ?? 0), 2, ',', '.') ?></td>
                            <td class="text-right"><?= esc($row['umur_hari'] ?? '') ?></td>
                            <td><?= esc($row['kode_salesman'] ?? '') ?></td>
                            <td><span class="badge <?= $statusBadge($row['row_status'] ?? '') ?>"><?= esc($row['row_status'] ?? '') ?></span></td>
                            <td class="text-danger"><?= esc($row['error_message'] ?? '') ?></td>
                            <td class="text-warning"><?= esc($row['warning_message'] ?? '') ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer small text-muted">
                Sumber data: <code>sc_im.import_ar</code>. Monitoring batch:
                <code>sc_im.v_import_ar_monitor</code>.
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script type="text/javascript">
    $(function () {
        if ($.fn.DataTable && $('#dtl_ar tbody tr').length > 1) {
            $('#dtl_ar').DataTable({
                "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
                "pageLength": 25,
                "ordering": false,
                "scrollX": true,
                "deferRender": true
            });
        }
    });
</script>

