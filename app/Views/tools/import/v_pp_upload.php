<?php
$flash = session()->getFlashdata('message');
$rows  = $list_detail ?? [];
$sync  = $sync_summary ?? [];
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
                <h3 class="card-title"><i class="fa fa-upload"></i> Form Upload PP</h3>
            </div>
            <form action="<?= esc($upload_url) ?>" method="post" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <div class="form-group">
                        <label for="import">File Excel PP (.xls / .xlsx)</label>
                        <input type="file" id="import" name="import" accept=".xls,.xlsx" required>
                        <p class="help-block mb-0">
                            File <b>Template_Import_Outstanding_PP.xlsx</b>: hanya sheet
                            <code><?= esc($sheet_name ?? 'template_import_pp') ?></code> yang diproses,
                            baris 1 = header, data mulai baris 2. Maksimal 10 MB.
                        </p>
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
                        <i class="fa fa-upload"></i> Upload &amp; Proses
                    </button>
                </div>
            </form>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Urutan Header Template PP</h3>
            </div>
            <div class="card-body">
                <ol class="mb-0 small pl-3">
                    <?php foreach ($header_columns as $column) { ?>
                        <li><?= esc($column) ?></li>
                    <?php } ?>
                </ol>
                <p class="text-muted small mb-0 mt-2">
                    Nama header boleh tidak berurutan. Header wajib:
                    No. PP, Jurnal, Tanggal, UserID, Kode Barang, Qty, Nama User, Nama Barang,
                    Satuan, Keterangan PP Header, Keterangan Detail, Tanggal Pakai, Job.
                    Kolom lain (Qty Realisasi, Spec, Tanggal Dokumen PP, Estimasi Tanggal Pakai) bersifat opsional.
                    Kolom status, inputby, inputdate diisi otomatis oleh sistem.
                </p>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-exchange"></i> Sinkronisasi PP ke Transaksi</h3>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    Tombol di bawah menjalankan prosedur <code>sc_im.sp_import_lap_pp_to_trx()</code>:
                    baris <code>sc_im.lap_pp_win</code> dipetakan menjadi header <code>sc_trx.pp</code>
                    dan detail <code>sc_trx.pp_dtl</code> (Qty PP = sisa qty, status detail P/F).
                    Proses ini aman dijalankan berulang: dokumen yang sudah ada tidak diduplikasi,
                    hanya qty dan status detail yang disegarkan.
                </p>
                <div class="row small">
                    <div class="col-sm-6">
                        <ul class="list-unstyled mb-2">
                            <li>Baris <code>lap_pp_win</code>: <b><?= number_format((int) ($sync['baris_pp'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>No. PP (PA/PB) unik: <b><?= number_format((int) ($sync['total_pp'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>Belum masuk transaksi: <b><?= number_format((int) ($sync['belum_pp'] ?? 0), 0, ',', '.') ?></b></li>
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        <ul class="list-unstyled mb-2">
                            <li><code>sc_trx.pp</code>: <b><?= number_format((int) ($sync['trx_pp'] ?? 0), 0, ',', '.') ?></b> header</li>
                            <li><code>sc_trx.pp_dtl</code>: <b><?= number_format((int) ($sync['trx_pp_dtl'] ?? 0), 0, ',', '.') ?></b> detail</li>
                            <li>Sudah masuk transaksi: <b><?= number_format((int) ($sync['sudah_pp'] ?? 0), 0, ',', '.') ?></b> No. PP</li>
                        </ul>
                    </div>
                </div>
                <form action="<?= esc($import_url ?? base_url('tools/proses/uploadpp/import')) ?>" method="post"
                      onsubmit="return confirm('Jalankan prosedur sc_im.sp_import_lap_pp_to_trx() sekarang?');">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-play"></i> Jalankan Import PP ke Transaksi
                    </button>
                    <span class="text-muted small ml-2">CALL sc_im.sp_import_lap_pp_to_trx();</span>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data PP Terakhir (<?= count($rows) ?>)</h3>
            </div>
            <div class="card-body table-responsive" style="overflow-x:auto;">
                <table id="example1" class="table table-bordered table-striped table-sm" style="white-space:nowrap;">
                    <thead>
                    <tr>
                        <th>pp</th>
                        <th>jurnal</th>
                        <th>date_</th>
                        <th>username</th>
                        <th>brg</th>
                        <th>qty</th>
                        <th>qty_realisasi</th>
                        <th>lns</th>
                        <th>gdg</th>
                        <th>nama_user</th>
                        <th>nama_barang</th>
                        <th>unit</th>
                        <th>spec</th>
                        <th>job</th>
                        <th>rem_pp</th>
                        <th>rem_pd</th>
                        <th>date_pakai</th>
                        <th>docdate</th>
                        <th>estpakai</th>
                        <th>uniqueidpd</th>
                        <th>inputby</th>
                        <th>inputdate</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row) { ?>
                        <tr>
                            <td><?= esc($row->pp) ?></td>
                            <td><?= esc($row->jurnal) ?></td>
                            <td><?= esc($row->date_) ?></td>
                            <td><?= esc($row->username) ?></td>
                            <td><?= esc($row->brg) ?></td>
                            <td><?= esc($row->qty) ?></td>
                            <td><?= esc($row->qty_realisasi ?? '') ?></td>
                            <td><?= esc($row->lns) ?></td>
                            <td><?= esc($row->gdg) ?></td>
                            <td><?= esc($row->nama_user) ?></td>
                            <td><?= esc($row->nama_barang) ?></td>
                            <td><?= esc($row->unit) ?></td>
                            <td><?= esc($row->spec) ?></td>
                            <td><?= esc($row->job) ?></td>
                            <td><?= esc($row->rem_pp) ?></td>
                            <td><?= esc($row->rem_pd) ?></td>
                            <td><?= esc($row->date_pakai) ?></td>
                            <td><?= esc($row->docdate ?? '') ?></td>
                            <td><?= esc($row->estpakai ?? '') ?></td>
                            <td><?= esc($row->uniqueidpd) ?></td>
                            <td><?= esc($row->inputby) ?></td>
                            <td><?= esc($row->inputdate) ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#example1').DataTable({
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "pageLength": 10,
            "ordering": false,
            "scrollX": true,
            "deferRender": true
        });
    });
</script>
