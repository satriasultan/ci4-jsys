<?php /* Proses Tutup Bulan - Accounting Closing (dibangun ulang) */ ?>

<style>
    /* =========================================================
       CARD PERIODE CLOSING
       Catatan: JANGAN memakai class "info-box"/"info-box-icon" karena
       sudah dipakai AdminLTE (display:flex, min-height:80px, dll) sehingga
       isi card menjadi berjejer ke samping. Semua class di bawah ini khusus
       halaman closing.
       ========================================================= */
    .closing-card .closing-cell {
        display: block;
        width: 100%;
        height: 100%;
        min-height: 92px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px 14px;
        background: #f9fafb;
        box-shadow: none;
    }
    .closing-card .closing-cell.is-info    { background: #eef5ff; border-color: #d7e3f5; }
    .closing-card .closing-cell.is-success { background: #f2fbf5; border-color: #d6ecdc; }
    .closing-card .closing-cell.is-danger  { background: #fdf4f4; border-color: #f3d7d7; }
    .closing-card .closing-cell.is-warning { background: #fffaf0; border-color: #f3e3c3; }

    .closing-card .closing-cell-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 3px;
    }
    .closing-card .closing-cell-value {
        display: block;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
        color: #111827;
        word-break: break-word;
    }
    .closing-card .closing-cell-value.money { font-size: 13.5px; }
    .closing-card .closing-cell-note {
        display: block;
        margin-top: 3px;
        font-size: 11.5px;
        line-height: 1.35;
        color: #6b7280;
    }
    .closing-card .closing-cell-value .badge { font-size: 12px; padding: 5px 10px; }
    .closing-card .closing-cell-title { color: #ffffff; font-size: 14px; }

    .closing-action .btn { margin-bottom: 6px; }
    .closing-action .closing-step { display: block; }

    #closingTab { padding: 4px 12px 0 12px; margin-bottom: 0; }
    #closingTab .nav-link { white-space: nowrap; }

    #tblDiagnostik td, #tblDiagnostik th,
    #tblUnclassified td, #tblUnclassified th,
    #tblLog td, #tblLog th,
    #tblPeriode td, #tblPeriode th,
    #tblYearEnd td, #tblYearEnd th { font-size: 12.5px; vertical-align: middle; }
    .badge-soft-warning { background:#fff3cd; color:#8a6d3b; }
    .badge-soft-success { background:#d1e7dd; color:#0f5132; }
    .badge-soft-danger  { background:#f8d7da; color:#842029; }
    .badge-soft-secondary { background:#e2e3e5; color:#41464b; }
    .closing-step { font-size: 12px; color:#6b7280; }
</style>

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

<?php
$statusClosing = isset($current['status_closing']) ? strtoupper(trim($current['status_closing'])) : 'OPEN';
$flagproses    = isset($current['flagproses']) ? strtoupper(trim($current['flagproses'])) : 'OPEN';
$periodeAktif  = isset($current['periode']) ? trim($current['periode']) : $periode;

$badgeStatus = 'badge-soft-secondary';
if ($statusClosing === 'CLOSED')   { $badgeStatus = 'badge-soft-danger'; }
if ($statusClosing === 'REVIEW')   { $badgeStatus = 'badge-soft-warning'; }
if ($statusClosing === 'CHECKING') { $badgeStatus = 'badge-soft-warning'; }
if ($statusClosing === 'REOPENED') { $badgeStatus = 'badge-soft-success'; }
if ($statusClosing === 'OPEN')     { $badgeStatus = 'badge-soft-success'; }

$badgeLock = ($flagproses === 'TUTUP') ? 'badge-soft-danger' : 'badge-soft-success';
?>


<div class="row closing-card">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="card-title mb-0 closing-cell-title">
                    <i class="fa fa-calendar-check-o"></i> Periode Closing
                </h3>
                <span class="closing-step" style="color:#e5e7eb;">
                    <i class="fa fa-sign-in"></i> Periode mengikuti tanggal login (session logindate)
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-2">
                        <div class="closing-cell is-info">
                            <span class="closing-cell-label">Periode Closing (yymm)</span>
                            <span class="closing-cell-value"><?= htmlspecialchars($periodeAktif) ?></span>
                            <span class="closing-cell-note">Dari logindate <?= htmlspecialchars($logindate) ?></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <div class="closing-cell">
                            <span class="closing-cell-label">Rentang Tanggal</span>
                            <span class="closing-cell-value">
                                <?= date('d-m-Y', strtotime(!empty($current['tanggal_mulai']) ? $current['tanggal_mulai'] : $tglmulai)) ?>
                                s/d
                                <?= date('d-m-Y', strtotime(!empty($current['tanggal_akhir']) ? $current['tanggal_akhir'] : $tglakhir)) ?>
                            </span>
                            <span class="closing-cell-note">Bulan berjalan dari tanggal login</span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <div class="closing-cell">
                            <span class="closing-cell-label">Status Closing</span>
                            <span class="closing-cell-value"><span class="badge <?= $badgeStatus ?>"><?= $statusClosing ?></span></span>
                            <span class="closing-cell-note">OPEN &rarr; CHECKING &rarr; REVIEW &rarr; CLOSED</span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <div class="closing-cell <?= ($flagproses === 'TUTUP') ? 'is-danger' : 'is-success' ?>">
                            <span class="closing-cell-label">Lock Transaksi</span>
                            <span class="closing-cell-value"><span class="badge <?= $badgeLock ?>"><?= $flagproses ?></span></span>
                            <span class="closing-cell-note">TUTUP = transaksi ditolak</span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <div class="closing-cell">
                            <span class="closing-cell-label">Trial Balance (Closing)</span>
                            <span class="closing-cell-value money">
                                D: <?= isset($current['closing_debet']) ? number_format((float) $current['closing_debet'], 2, ',', '.') : '0,00' ?><br>
                                K: <?= isset($current['closing_kredit']) ? number_format((float) $current['closing_kredit'], 2, ',', '.') : '0,00' ?>
                            </span>
                            <span class="closing-cell-note">Jurnal POSTED periode ini</span>
                        </div>
                    </div>
                </div>

                <?php if ($flagproses === 'TUTUP'): ?>
                    <div class="alert alert-danger mb-0 mt-2">
                        <i class="fa fa-lock"></i>
                        Periode <b><?= htmlspecialchars($periodeAktif) ?></b> sudah <b>TUTUP</b>.
                        Transaksi pada rentang tanggal periode ini akan <b>ditolak</b> (modul transaksi &amp; posting jurnal).
                        Ditutup oleh <b><?= htmlspecialchars(trim($current['closed_by'] ?? '')) ?></b>
                        <?= !empty($current['closed_at']) ? ' pada ' . date('d-m-Y H:i', strtotime($current['closed_at'])) : '' ?>
                        , disetujui <b><?= htmlspecialchars(trim($current['approved_by'] ?? '')) ?></b>.
                        Gunakan <b>Buka Kembali</b> bila perlu koreksi transaksi.
                    </div>
                <?php elseif ($statusClosing === 'REOPENED'): ?>
                    <div class="alert alert-warning mb-0 mt-2">
                        <i class="fa fa-unlock"></i> Periode pernah dibuka kembali. Alasan:
                        <?= htmlspecialchars(trim($current['reopen_reason'] ?? '-')) ?>
                        (<?= htmlspecialchars(trim($current['reopened_by'] ?? '-')) ?>
                        <?= !empty($current['reopened_at']) ? date('d-m-Y H:i', strtotime($current['reopened_at'])) : '' ?>).
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $message ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0" style="color:white;"><i class="fa fa-play-circle"></i> Tahapan Proses Closing</h3>
            </div>
            <div class="card-body closing-action">
                <input type="hidden" id="closingIdurut" value="<?= (int) $idurut ?>">
                <input type="hidden" id="closingPeriode" value="<?= htmlspecialchars($periodeAktif) ?>">
                <input type="hidden" id="closingNamalogin" value="<?= htmlspecialchars($namalogin) ?>">

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label>Periode Closing (yymm)</label>
                        <input type="text" class="form-control" id="closingPeriodeInput" value="<?= htmlspecialchars($periodeAktif) ?>" readonly>
                        <small class="text-muted">Otomatis dari tanggal login (session <b>logindate</b>): <?= htmlspecialchars($logindate) ?>.</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Tanggal Mulai</label>
                        <input type="text" class="form-control" id="closingTglMulai" readonly
                               value="<?= !empty($current['tanggal_mulai']) ? date('d-m-Y', strtotime($current['tanggal_mulai'])) : htmlspecialchars(date('d-m-Y', strtotime($tglmulai))) ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Tanggal Akhir</label>
                        <input type="text" class="form-control" id="closingTglAkhir" readonly
                               value="<?= !empty($current['tanggal_akhir']) ? date('d-m-Y', strtotime($current['tanggal_akhir'])) : htmlspecialchars(date('d-m-Y', strtotime($tglakhir))) ?>">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>Approver</label>
                        <input type="text" class="form-control" id="closingApprover" value="<?= htmlspecialchars($namalogin) ?>" readonly>
                        <small class="text-muted">Terisi otomatis dari sesi login.</small>
                    </div>
                </div>

                <hr>

                <div class="d-flex flex-wrap" style="gap:6px;">
                    <button type="button" class="btn btn-secondary" id="btnClosingRegister">
                        <i class="fa fa-save"></i> 1. Registrasi Periode
                    </button>
                    <button type="button" class="btn btn-info" id="btnClosingDiagnostik">
                        <i class="fa fa-stethoscope"></i> 2. Diagnostik
                    </button>
                    <button type="button" class="btn btn-primary" id="btnClosingMulai">
                        <i class="fa fa-hourglass-start"></i> 3. Mulai Closing
                    </button>
                    <button type="button" class="btn btn-danger" id="btnClosingTutup">
                        <i class="fa fa-lock"></i> 4. Tutup Periode
                    </button>
                    <button type="button" class="btn btn-success" id="btnClosingOtomatis">
                        <i class="fa fa-bolt"></i> Proses Tutup Bulan (Otomatis)
                    </button>
                    <button type="button" class="btn btn-outline-warning" id="btnClosingBuka">
                        <i class="fa fa-unlock"></i> Buka Kembali
                    </button>
                </div>
                <div class="mt-2 closing-step">
                    Urutan tahapan: Registrasi &rarr; Diagnostik &rarr; Mulai Closing &rarr; Tutup Periode.
                    Tombol <b>Otomatis</b> menjalankan seluruh tahapan sekaligus.
                    Approver terisi otomatis dari sesi login.
                </div>
            </div>
        </div>
    </div>
</div>



<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0 closing-cell-title"><i class="fa fa-list-alt"></i> Data &amp; Tahapan Closing</h3>
            </div>
            <ul class="nav nav-tabs" id="closingTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-diag-btn" data-bs-toggle="tab" data-bs-target="#tab-diag" type="button" role="tab">
                            <i class="fa fa-stethoscope"></i> Diagnostik &amp; Trial Balance
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-log-btn" data-bs-toggle="tab" data-bs-target="#tab-log" type="button" role="tab">
                            <i class="fa fa-history"></i> Riwayat Proses
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-periode-btn" data-bs-toggle="tab" data-bs-target="#tab-periode" type="button" role="tab">
                            <i class="fa fa-list"></i> Daftar Periode
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-yearend-btn" data-bs-toggle="tab" data-bs-target="#tab-yearend" type="button" role="tab">
                            <i class="fa fa-book"></i> Jurnal Penutup Tahunan
                        </button>
                    </li>
                </ul>
            <div class="card-body">
                <div class="tab-content" id="closingTabContent">

                    <!-- TAB 1: DIAGNOSTIK -->
                    <div class="tab-pane fade show active" id="tab-diag" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Diagnostik bersumber dari jurnal <b>POSTED</b> dengan <b>accounting_effect = YES</b> pada rentang tanggal periode.
                            Target: <b>JOURNAL_UNBALANCED = 0</b>, <b>UNCLASSIFIED_COA = 0</b>, dan <b>total debet = total kredit</b>.
                            <div class="mt-2">
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="<?= base_url('report/trans/bukubesar') ?>"><i class="fa fa-book"></i> Lap. Buku Besar</a>
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="<?= base_url('report/trans/labarugi') ?>"><i class="fa fa-line-chart"></i> Lap. Laba Rugi</a>
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="<?= base_url('report/trans/analisamutasi') ?>"><i class="fa fa-cubes"></i> Analisa Mutasi Stock</a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-2">Hasil Diagnostik Periode</h5>
                                <div class="table-responsive">
                                    <table id="tblDiagnostik" class="table table-bordered table-striped" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th style="width:170px;">Check Code</th>
                                                <th>Metrik</th>
                                                <th style="width:160px; text-align:right;">Nilai</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tblDiagnostikBody">
                                            <tr><td colspan="4" class="text-center text-muted">Klik tombol <b>Diagnostik</b> untuk memuat data.</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-2">Trial Balance (closing_debet / closing_kredit)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" style="width:100%;">
                                        <tbody id="tblTrialBalanceBody">
                                            <tr><td class="text-muted">Belum dihitung.</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <h5 class="mb-2 mt-3">COA Transaksi Belum Diklasifikasi</h5>
                                <div class="table-responsive">
                                    <table id="tblUnclassified" class="table table-bordered table-striped" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th style="width:110px;">COA</th>
                                                <th>Nama Akun</th>
                                                <th style="width:140px; text-align:right;">Debet</th>
                                                <th style="width:140px; text-align:right;">Kredit</th>
                                                <th style="width:150px; text-align:center;">Klasifikasi Cepat</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tblUnclassifiedBody">
                                            <tr><td colspan="5" class="text-center text-muted">Belum ada data.</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: RIWAYAT PROSES -->
                    <div class="tab-pane fade" id="tab-log" role="tabpanel">
                        <div class="table-responsive">
                            <table id="tblLog" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">#</th>
                                        <th style="width:140px;">Aksi</th>
                                        <th style="width:110px; text-align:center;">Status Lama</th>
                                        <th style="width:110px; text-align:center;">Status Baru</th>
                                        <th style="width:120px;">Oleh</th>
                                        <th style="width:150px; text-align:center;">Waktu</th>
                                        <th>Alasan / Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($log)): ?>
                                    <tr><td colspan="7" class="text-center text-muted">Belum ada riwayat proses untuk periode ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($log as $l): ?>
                                        <tr>
                                            <td><?= (int) $l['id'] ?></td>
                                            <td><b><?= htmlspecialchars(trim($l['action'])) ?></b></td>
                                            <td class="text-center"><?= htmlspecialchars(trim((string) $l['old_status'])) ?></td>
                                            <td class="text-center"><?= htmlspecialchars(trim((string) $l['new_status'])) ?></td>
                                            <td><?= htmlspecialchars(trim((string) $l['action_by'])) ?></td>
                                            <td class="text-center"><?= !empty($l['action_at']) ? date('d-m-Y H:i:s', strtotime($l['action_at'])) : '' ?></td>
                                            <td style="white-space:normal; max-width:420px;">
                                                <?= htmlspecialchars(trim((string) $l['reason'])) ?>
                                                <?php if (!empty($l['detail'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(trim((string) $l['detail'])) ?></small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <!-- TAB 5: DAFTAR PERIODE -->
                    <div class="tab-pane fade" id="tab-periode" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Proses closing hanya untuk <b>periode bulan tanggal login</b>
                            (periode <b><?= htmlspecialchars($periodeAktif) ?></b> dari logindate <?= htmlspecialchars($logindate) ?>).
                            Tabel di bawah bersifat informasi (tidak dapat memilih periode lain).
                        </div>
                        <div class="table-responsive">
                            <table id="tblPeriode" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th style="width:110px; text-align:center;">Periode</th>
                                        <th style="width:180px; text-align:center;">Rentang</th>
                                        <th style="width:100px; text-align:center;">Status</th>
                                        <th style="width:90px; text-align:center;">Lock</th>
                                        <th style="width:150px; text-align:right;">TB Debet</th>
                                        <th style="width:150px; text-align:right;">TB Kredit</th>
                                        <th style="width:110px;">Ditutup</th>
                                        <th style="width:110px;">Approved</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($list)): ?>
                                    <tr><td colspan="9" class="text-center text-muted">Belum ada periode pada sc_trx.closeperiod.</td></tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($list as $p): ?>
                                        <?php
                                        $st = strtoupper(trim($p['status_closing']));
                                        $fp = strtoupper(trim($p['flagproses']));
                                        $b1 = 'badge-soft-secondary';
                                        if ($st === 'CLOSED')   { $b1 = 'badge-soft-danger'; }
                                        if ($st === 'REVIEW')   { $b1 = 'badge-soft-warning'; }
                                        if ($st === 'CHECKING') { $b1 = 'badge-soft-warning'; }
                                        if ($st === 'OPEN' || $st === 'REOPENED') { $b1 = 'badge-soft-success'; }
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="text-center">
                                                <?= htmlspecialchars(trim($p['periode'])) ?>
                                                <?php if (trim($p['periode']) === $periodeAktif): ?>
                                                    <br><span class="badge badge-soft-success" style="font-size:10px;">PERIODE LOGIN</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= !empty($p['tanggal_mulai']) ? date('d-m-Y', strtotime($p['tanggal_mulai'])) : '-' ?>
                                                s/d
                                                <?= !empty($p['tanggal_akhir']) ? date('d-m-Y', strtotime($p['tanggal_akhir'])) : '-' ?>
                                            </td>
                                            <td class="text-center"><span class="badge <?= $b1 ?>"><?= $st ?></span></td>
                                            <td class="text-center">
                                                <span class="badge <?= $fp === 'TUTUP' ? 'badge-soft-danger' : 'badge-soft-success' ?>"><?= $fp ?></span>
                                            </td>
                                            <td class="text-right"><?= number_format((float) $p['closing_debet'], 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format((float) $p['closing_kredit'], 2, ',', '.') ?></td>
                                            <td><?= htmlspecialchars(trim((string) $p['closed_by'])) ?></td>
                                            <td><?= htmlspecialchars(trim((string) $p['approved_by'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <!-- TAB 6: JURNAL PENUTUP TAHUNAN -->
                    <div class="tab-pane fade" id="tab-yearend" role="tabpanel">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            Daftar jurnal yang berkaitan dengan closing / jurnal penutup tahunan, bersumber dari
                            <b>sc_trx.jurnal_hd</b> (header) dan <b>sc_trx.jurnal_dt</b> (baris detail).
                            Jurnal penutup tahunan (journal_type <b>CLSPER</b>, doctype <b>CLOSING</b>) menolkan saldo akun
                            <b>PNL</b> ke akun laba/rugi dan dibuat berstatus <b>DRAFT</b>; posting tetap melalui menu jurnal yang ada.
                            <b>Jangan</b> diposting setelah periode ditutup (posting ke periode CLOSED akan ditolak sistem).
                        </div>

                        <div class="row">
                            <div class="col-md-5 mb-2">
                                <label>COA Laba/Rugi Tahun Berjalan (akun lawan)</label>
                                <select id="closingCoaLabaRugi" class="form-control select2" style="width:100%"></select>
                                <small class="text-muted">
                                    Harus terdaftar aktif sebagai kelas <b>BS</b>. COA transaksi yang belum diklasifikasi
                                    dapat ditandai lewat tombol <b>Klasifikasi Cepat</b> pada tab Diagnostik.
                                </small>
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100" id="btnClosingYearEnd">
                                    <i class="fa fa-magic"></i> Siapkan Jurnal Penutup (DRAFT)
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tblYearEnd" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:70px;">ID</th>
                                        <th style="width:160px;">Doc No</th>
                                        <th style="width:100px; text-align:center;">Tipe</th>
                                        <th style="width:110px; text-align:center;">Doctype</th>
                                        <th style="width:120px; text-align:center;">Tanggal</th>
                                        <th style="width:100px; text-align:center;">Status</th>
                                        <th style="width:140px; text-align:right;">Debet</th>
                                        <th style="width:140px; text-align:right;">Kredit</th>
                                        <th style="width:70px; text-align:center;">Baris</th>
                                        <th>Keterangan</th>
                                        <th style="width:110px;">Dibuat</th>
                                        <th style="width:90px; text-align:center;">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($closingjournal)): ?>
                                    <tr><td colspan="12" class="text-center text-muted">Belum ada jurnal yang berkaitan dengan closing.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($closingjournal as $j): ?>
                                        <tr>
                                            <td><?= (int) $j['id'] ?></td>
                                            <td><?= htmlspecialchars(trim((string) $j['docno'])) ?></td>
                                            <td class="text-center"><?= htmlspecialchars(trim((string) $j['journal_type'])) ?></td>
                                            <td class="text-center"><?= htmlspecialchars(trim((string) $j['doctype'])) ?></td>
                                            <td class="text-center"><?= !empty($j['trxdate']) ? date('d-m-Y', strtotime($j['trxdate'])) : '' ?></td>
                                            <td class="text-center">
                                                <?php $js = strtoupper(trim((string) $j['status'])); ?>
                                                <span class="badge <?= $js === 'POSTED' ? 'badge-soft-success' : 'badge-soft-secondary' ?>"><?= $js ?></span>
                                            </td>
                                            <td class="text-right"><?= number_format((float) $j['total_debet'], 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format((float) $j['total_kredit'], 2, ',', '.') ?></td>
                                            <td class="text-center"><?= (int) $j['jumlah_baris'] ?></td>
                                            <td style="white-space:normal;"><?= htmlspecialchars(trim((string) $j['keterangan'])) ?></td>
                                            <td><?= htmlspecialchars(trim((string) $j['createdby'])) ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-xs btn-outline-primary btnClosingYearEndDetail"
                                                        data-id="<?= (int) $j['id'] ?>">Lihat</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div><!-- /.tab-content -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalClosingDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"><i class="fa fa-book"></i> Detail Jurnal Penutup</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="modalClosingDetailBody">
                <div class="text-center text-muted">Memuat...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript" src="<?= base_url('assets/pagejs/tools/ptb.js') ?>"></script>
<script type="text/javascript">
    $(function () {
        $('#tblLog').DataTable({ "order": [[0, "desc"]], "pageLength": 25, "scrollX": true });
        $('#tblPeriode').DataTable({ "order": [[0, "asc"]], "pageLength": 25, "scrollX": true });
        $('#tblYearEnd').DataTable({ "order": [[0, "desc"]], "pageLength": 25, "scrollX": true });

        closingInitSelect2('#closingCoaLabaRugi', '');
    });
</script>

