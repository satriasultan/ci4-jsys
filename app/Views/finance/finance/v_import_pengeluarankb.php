<!-- Replace app/Views/finance/finance/v_import_pengeluarankb.php with this view. -->
<style>
    .btn-preview-data {
        background: linear-gradient(135deg, #b5121b 0%, #8a1c08 100%);
        color: #fff;
        border: 2px solid #701606;
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: .3px;
        padding: 10px 28px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(138, 28, 8, 0.45);
        transition: all 0.2s ease-in-out;
        animation: bk-preview-pulse 1.8s infinite;
    }

    .btn-preview-data:hover {
        background: linear-gradient(135deg, #d0181f 0%, #b5121b 100%);
        color: #fff;
        border-color: #b5121b;
        box-shadow: 0 6px 16px rgba(138, 28, 8, 0.55);
        transform: translateY(-2px);
    }

    .btn-preview-data:active {
        background: #701606;
        color: #fff;
        transform: translateY(0);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .btn-preview-data:focus-visible {
        outline: 3px solid rgba(181, 18, 27, 0.3);
        outline-offset: 2px;
    }

    @keyframes bk-preview-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(181, 18, 27, 0.55); }
        70%  { box-shadow: 0 0 0 14px rgba(181, 18, 27, 0); }
        100% { box-shadow: 0 0 0 0 rgba(181, 18, 27, 0); }
    }
</style>


<?php
$h = static fn($v) => esc((string)($v ?? ''), 'html');
$money = static fn($v) => number_format((float)($v ?? 0), 2, ',', '.');
$detailsByHeader = [];
foreach (($previewDetails ?? []) as $d) $detailsByHeader[(string)$d['header_id']][] = $d;
?>
<div class="content-header">
    <div class="container-fluid"><div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><?= $h($title ?? 'Import Pengeluaran Kas/Bank') ?></h1></div>
            <div class="col-sm-6"><ol class="breadcrumb float-sm-end">
                    <span class="me-2">Menu ID <?= $h($version ?? '') ?></span>
                    <?php foreach (($y ?? []) as $y1): ?>
                        <?php if (trim((string)$y1->kodemenu) !== trim((string)($kodemenu ?? ''))): ?>
                            <li class="breadcrumb-item"><a href="<?= base_url(trim((string)$y1->linkmenu)) ?>"><i class="fa <?= $h($y1->iconmenu) ?>"></i> <?= $h($y1->namamenu) ?></a></li>
                        <?php else: ?><li class="breadcrumb-item active"><?= $h($y1->namamenu) ?></li><?php endif; ?>
                    <?php endforeach; ?>
                </ol></div>
        </div></div>
</div>

<?php foreach (['success'=>'success','error'=>'danger','warning'=>'warning'] as $key=>$type): ?>
    <?php if (session()->getFlashdata($key)): ?><div class="alert alert-<?= $type ?> alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= session()->getFlashdata($key) ?></div><?php endif; ?>
<?php endforeach; ?>

<style>
    .bk-import .card-header{background:#8a1c08;color:#fff}
    .bk-import .table td,.bk-import .table th{vertical-align:middle;white-space:nowrap;font-size:.82rem}
    .bk-import .table .error-cell{white-space:normal;min-width:260px}
    .bk-import .metric{border:1px solid #ddd;padding:12px;border-left:4px solid #8a1c08;background:#fff}
    .bk-import .metric strong{display:block;font-size:1.35rem}
</style>
<div class="row bk-import">
    <div class="col-12"><div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fa fa-upload"></i> Upload &amp; Preview Import</h3></div>
            <form action="<?= base_url('ka/finance/prosesImportPengeluaranKB') ?>" method="post" enctype="multipart/form-data" id="form-import-bk">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <strong>Alur:</strong> Upload → Preview/validasi → Konfirmasi.
                        Data yang dikonfirmasi <strong>diposting</strong> ke <code>sc_trx.pengeluarankb</code> dan jurnal <code>transaction_dt</code> (BANKOT).
                        <ul class="mb-0 mt-2">
                            <li>Format XLSX/XLS/CSV, maksimal 10 MB. Export BK yang memiliki baris HEADER/CONTENT juga didukung.</li>
                            <li>Header wajib mengikuti template. Satu NOMOR BK adalah satu header, maksimal 25 detail.</li>
                            <li>Format nomor contoh: <code>BKT/2608/PT0012</code>.</li>
                            <li>Nomor BK yang sudah ada pada draft/transaksi ditandai ERROR dan tidak ditimpa otomatis.</li>
                        </ul>
                    </div>
                    <div class="row align-items-center mb-3">
                        <div class="col-md-3"><label>Template Import</label></div>
                        <div class="col-md-9"><a href="<?= base_url().'/assets/files/pengeluarankb/EXPORT_BK_UPLOAD_READY.xlsx' ?>" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download XLS Template (25 Detail)</a></div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-md-3 col-form-label" for="file_import">File Import <span class="text-danger">*</span></label>
                        <div class="col-md-9"><input type="file" name="file_import" id="file_import" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Untuk mencegah proses ganda, setiap upload menghasilkan batch preview baru.</small></div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" id="btnPreviewData"
                            class="btn btn-preview-data">
                        <i class="fa fa-search"></i> Preview Data
                    </button>
                    <a href="<?= base_url('ka/finance/pengeluarankb') ?>" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div></div>

    <?php if (!empty($previewBatch)): ?>
        <div class="col-12"><div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fa fa-table"></i> Preview Batch #<?= (int)$previewBatch['id'] ?> — <?= $h($previewBatch['nama_file']) ?></h3></div>
                <div class="card-body">
                    <?php $closedDocs = array_values(array_filter($previewHeaders ?? [], fn($hd) => $hd['status'] === 'ERROR' && stripos((string)($hd['error_message'] ?? ''), 'sudah TUTUP (closing)') !== false)); ?>
                    <?php if (!empty($closedDocs)): ?>
                        <div class="alert alert-danger mb-3">
                            <i class="fa fa-lock"></i>
                            <strong><?= count($closedDocs) ?> dokumen</strong> berada pada periode yang sudah
                            <strong>TUTUP (closing)</strong> sehingga statusnya
                            <span class="badge badge-danger">INVALID</span> dan tidak dapat diproses/dikonfirmasi.
                            <div class="mt-1 small">Nomor BK: <?= $h(implode(', ', array_map(fn($d) => (string)$d['docno'], $closedDocs))) ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2"><div class="metric"><span>Total Dokumen</span><strong><?= (int)$previewBatch['jumlah_baris'] ?></strong></div></div>
                        <div class="col-md-4 mb-2"><div class="metric"><span>Valid</span><strong class="text-success"><?= (int)$previewBatch['jumlah_valid'] ?></strong></div></div>
                        <div class="col-md-4 mb-2"><div class="metric"><span>Error</span><strong class="text-danger"><?= (int)$previewBatch['jumlah_gagal'] ?></strong></div></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped" id="tbl-preview-kb">
                            <thead class="thead-dark"><tr><th>Baris Excel</th><th>NOMOR BK</th><th>Tanggal</th><th>Supplier</th><th>Bank</th><th>Total Pembayaran</th><th>Detail</th><th>Status</th><th class="error-cell">Pesan Validasi</th></tr></thead>
                            <tbody>
                            <?php foreach (($previewHeaders ?? []) as $hd): $dtls = $detailsByHeader[(string)$hd['id']] ?? []; $detailErrors = array_values(array_filter($dtls, fn($d)=>$d['status']==='ERROR')); ?>
                                <tr class="<?= $hd['status']==='ERROR' ? 'table-danger' : 'table-success' ?>">
                                    <td><?= (int)$hd['nomor_baris'] ?></td><td><?= $h($hd['docno']) ?></td><td><?= $h($hd['docdate']) ?></td>
                                    <td><?= $h($hd['nmsupplier'] ?: $hd['kdsupplier']) ?></td><td><?= $h($hd['prkkas']) ?></td>
                                    <td class="text-right"><?= $money($hd['total_pembayaran']) ?> <?= $h($hd['currcode']) ?></td>
                                    <td><?= count($dtls) ?></td>
                                    <td><?php if ($hd['status']==='VALID'): ?><span class="badge badge-success">VALID</span><?php else: ?><span class="badge badge-danger">ERROR</span><?php endif; ?></td>
                                    <td class="error-cell"><?= $h($hd['error_message']) ?><?php foreach ($detailErrors as $de): ?><div><b>Detail <?= (int)$de['slot_no'] ?>:</b> <?= $h($de['error_message']) ?></div><?php endforeach; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-warning mb-0 mt-3">Konfirmasi akan membuat transaksi Pengeluaran Kas/Bank dan jurnal BANKOT. NOMOR BK yang sudah ada di transaksi akan membatalkan seluruh batch.</div>
                </div>
                <div class="card-footer d-flex flex-wrap" style="gap:8px">
                    <?php if ($previewBatch['status']==='PREVIEW' && (int)$previewBatch['jumlah_gagal']===0 && (int)$previewBatch['jumlah_valid']>0): ?>
                        <form action="<?= base_url('ka/finance/simpanImportPengeluaranKB') ?>" method="post" onsubmit="return confirm('Konfirmasi batch dan posting ke transaksi + jurnal?');">
                            <?= csrf_field() ?><input type="hidden" name="batch_token" value="<?= $h($previewBatch['batch_token']) ?>">
                            <button class="btn btn-sm btn-success" type="submit"><i class="fa fa-check"></i> Konfirmasi Batch</button>
                        </form>
                    <?php elseif ($previewBatch['status']!=='PREVIEW'): ?><span class="badge badge-secondary p-2">Status batch: <?= $h($previewBatch['status']) ?></span>
                    <?php else: ?><button class="btn btn-sm btn-success" disabled><i class="fa fa-check"></i> Perbaiki error sebelum konfirmasi</button><?php endif; ?>
                    <?php if ($previewBatch['status']==='PREVIEW'): ?>
                        <form action="<?= base_url('ka/finance/cancelImportPengeluaranKB') ?>" method="post" onsubmit="return confirm('Batalkan preview batch ini?');">
                            <?= csrf_field() ?><input type="hidden" name="batch_token" value="<?= $h($previewBatch['batch_token']) ?>">
                            <button class="btn btn-sm btn-secondary" type="submit"><i class="fa fa-times"></i> Batalkan Preview</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div></div>
    <?php endif; ?>

    <div class="col-12"><div class="card">
            <div class="card-header" style="background:#444;color:#fff"><h3 class="card-title"><i class="fa fa-history"></i> Riwayat Import Terakhir</h3></div>
            <div class="card-body p-0 table-responsive"><table class="table table-sm table-bordered table-striped mb-0">
                    <thead class="thead-dark"><tr><th>ID</th><th>Tanggal</th><th>User</th><th>File</th><th>Status</th><th class="text-right">Dokumen</th><th class="text-right">Valid</th><th class="text-right">Error</th><th>Keterangan</th></tr></thead>
                    <tbody><?php if (empty($recent)): ?><tr><td colspan="9" class="text-center text-muted">Belum ada riwayat import.</td></tr>
                    <?php else: foreach ($recent as $r): ?><tr>
                        <td><?= (int)$r['id'] ?></td><td><?= $h($r['created_at']) ?></td><td><?= $h($r['created_by']) ?></td><td><?= $h($r['nama_file']) ?></td>
                        <td><?= $h($r['status']) ?></td><td class="text-right"><?= (int)$r['jumlah_baris'] ?></td><td class="text-right"><?= (int)$r['jumlah_valid'] ?></td><td class="text-right"><?= (int)$r['jumlah_gagal'] ?></td><td><?= $h($r['keterangan']) ?></td>
                        </tr><?php endforeach; endif; ?></tbody>
                </table></div>
        </div></div>
</div>
