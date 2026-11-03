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
                <h3 class="card-title"><i class="fa fa-upload"></i> Form Upload Saldo Awal</h3>
            </div>
            <form action="<?= esc($upload_url) ?>" method="post" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <div class="form-group">
                        <label for="import">File Excel Saldo Persediaan (.xls / .xlsx)</label>
                        <input type="file" id="import" name="import" accept=".xls,.xlsx" required>
                        <p class="help-block mb-0">
                            File <b>Template_Import_Stock_Persediaan.xlsx</b>: hanya sheet
                            <code><?= esc($sheet_name ?? 'template_stock_persediaan') ?></code> yang diproses,
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
                <h3 class="card-title">Urutan Header Template Saldo Awal</h3>
            </div>
            <div class="card-body">
                <ol class="mb-0 small pl-3">
                    <?php foreach ($header_columns as $column) { ?>
                        <li><?= esc($column) ?></li>
                    <?php } ?>
                </ol>
                <p class="text-muted small mb-0 mt-2">
                    Nama header boleh tidak berurutan. Header wajib:
                    <code>tanggal_saldo, idbranch, kode_gudang, kode_barang, qty_saldo</code>.<br>
                    Header versi ramah pengguna (<code>Tanggal Saldo</code>, <code>Cabang (idbranch)</code>,
                    <code>Kode Gudang</code>, <code>Kode Barang</code>, <code>Qty Saldo</code>) juga diterima.<br>
                    Kolom lain (<code>cabang, nama_gudang, nama_barang, satuan, batch_spec, nilai_saldo,
                    harga_rata_rata, keterangan, status_validasi</code>) bersifat opsional.<br>
                    Baris dengan status mengandung <code>JANGAN IMPORT</code> dilewati; baris dengan kunci saldo
                    sama (tanggal, cabang, gudang, barang, satuan, batch) otomatis digabung qty &amp; nilainya.<br>
                    Kolom status, kode unik, inputby dan inputdate diisi otomatis oleh sistem.
                </p>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-check-square-o"></i> Tahap 1 &mdash; Validasi Staging</h3>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    Tombol di bawah menjalankan fungsi <code>sc_im.fn_validasi_stock_persediaan_opening()</code>:
                    setiap baris <code>sc_im.stock_persediaan_opening</code> diperiksa (cabang, gudang, barang,
                    qty tidak nol, panjang kode barang maksimal 20 karakter, tanggal saldo wajib diisi).
                    Baris tanpa masalah berubah menjadi <b>VALID</b>, baris bermasalah tetap <b>DRAFT</b>
                    dengan catatan pada kolom <code>validasi_catatan</code>.
                </p>
                <div class="row small">
                    <div class="col-sm-6">
                        <ul class="list-unstyled mb-2">
                            <li>Baris staging: <b><?= number_format((int) ($sync['baris'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>DRAFT: <b><?= number_format((int) ($sync['draft'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>VALID (siap posting): <b><?= number_format((int) ($sync['valid'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>POSTED: <b><?= number_format((int) ($sync['posted'] ?? 0), 0, ',', '.') ?></b></li>
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        <ul class="list-unstyled mb-2">
                            <li>Baris bermasalah: <b><?= number_format((int) ($sync['bermasalah'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>Qty belum terposting: <b><?= number_format((float) ($sync['qty'] ?? 0), 4, ',', '.') ?></b></li>
                            <li>Nilai belum terposting: <b><?= number_format((float) ($sync['nilai'] ?? 0), 2, ',', '.') ?></b></li>
                            <li>Gudang / barang unik: <b><?= number_format((int) ($sync['gudang'] ?? 0), 0, ',', '.') ?></b>
                                / <b><?= number_format((int) ($sync['barang'] ?? 0), 0, ',', '.') ?></b></li>
                        </ul>
                    </div>
                </div>
                <form action="<?= esc($validasi_url ?? base_url('tools/proses/uploadStockAwal/validasi')) ?>" method="post"
                      onsubmit="return confirm('Jalankan fungsi sc_im.fn_validasi_stock_persediaan_opening() sekarang?');">
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-check"></i> Jalankan Validasi Staging
                    </button>
                    <span class="text-muted small ml-2">SELECT * FROM sc_im.fn_validasi_stock_persediaan_opening();</span>
                </form>
            </div>
        </div>

        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-exchange"></i> Tahap 2 &mdash; Import ke Transaksi</h3>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    Tombol di bawah menjalankan fungsi <code>sc_im.fn_posting_stock_persediaan_opening()</code>:
                    baris berstatus <b>VALID</b> diposting ke buku besar persediaan
                    <code>sc_trx.stkblc</code> dengan dokumen <code>SA</code> (Saldo Awal), history
                    <b>SALDO AWAL</b> dan <code>ctype = SLD</code>. Baris yang sudah terposting dilewati
                    sehingga aman dijalankan berulang; setelah sukses statusnya berubah menjadi <b>POSTED</b>.
                </p>
                <p class="text-muted small mb-2">
                    <code>qty_sld</code> dan kartu stok <code>sc_mst.stkgdw</code> dihitung otomatis oleh
                    trigger <code>tr_trx_stkblc</code>, bukan diisi manual oleh upload.
                    <code>pricelst_sld</code> (nilai persediaan berjalan) belum dipelihara trigger tersebut,
                    sehingga baris saldo awal disimpan dengan <code>pricelst_sld = 0</code>; nilai saldo asli
                    tetap tersimpan di <code>sc_im.stock_persediaan_opening.nilai_saldo</code>.
                </p>
                <div class="row small">
                    <div class="col-sm-6">
                        <ul class="list-unstyled mb-2">
                            <li><code>sc_trx.stkblc</code> total: <b><?= number_format((int) ($sync['stkblc'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>Baris <code>ctype = SLD</code>: <b><?= number_format((int) ($sync['stkblc_sld'] ?? 0), 0, ',', '.') ?></b></li>
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        <ul class="list-unstyled mb-2">
                            <li>History <b>SALDO AWAL</b>: <b><?= number_format((int) ($sync['stkblc_opening'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>Gudang terpakai di <code>stkblc</code>: <b><?= number_format((int) ($sync['stkblc_gudang'] ?? 0), 0, ',', '.') ?></b></li>
                        </ul>
                    </div>
                </div>
                <form action="<?= esc($import_url ?? base_url('tools/proses/uploadStockAwal/import')) ?>" method="post"
                      onsubmit="return confirm('Posting semua baris VALID ke sc_trx.stkblc sekarang?');">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-play"></i> Import Saldo Awal ke Transaksi
                    </button>
                    <span class="text-muted small ml-2">SELECT * FROM sc_im.fn_posting_stock_persediaan_opening('USER');</span>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Staging Saldo Awal Terakhir (<?= count($rows) ?>)</h3>
            </div>
            <div class="card-body table-responsive" style="overflow-x:auto;">
                <table id="example1" class="table table-bordered table-striped table-sm" style="white-space:nowrap;">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>tanggal_saldo</th>
                        <th>idbranch</th>
                        <th>cabang</th>
                        <th>kode_gudang</th>
                        <th>nama_gudang</th>
                        <th>kode_barang</th>
                        <th>nama_barang</th>
                        <th>satuan</th>
                        <th>batch_spec</th>
                        <th>qty_saldo</th>
                        <th>nilai_saldo</th>
                        <th>harga_rata_rata</th>
                        <th>status_validasi</th>
                        <th>validasi_catatan</th>
                        <th>keterangan</th>
                        <th>inputby</th>
                        <th>inputdate</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row) {
                        $status = strtoupper(trim((string) ($row->status_validasi ?? '')));
                        $badge  = $status === 'VALID' ? 'badge-success'
                            : ($status === 'POSTED' ? 'badge-primary'
                            : ($status === 'CANCELLED' ? 'badge-danger' : 'badge-secondary'));
                        ?>
                        <tr>
                            <td><?= esc($row->id) ?></td>
                            <td><?= esc($row->tanggal_saldo) ?></td>
                            <td><?= esc($row->idbranch) ?></td>
                            <td><?= esc($row->cabang ?? '') ?></td>
                            <td><?= esc($row->kode_gudang) ?></td>
                            <td><?= esc($row->nama_gudang ?? '') ?></td>
                            <td><?= esc($row->kode_barang) ?></td>
                            <td><?= esc($row->nama_barang ?? '') ?></td>
                            <td><?= esc($row->satuan ?? '') ?></td>
                            <td><?= esc($row->batch_spec ?? '') ?></td>
                            <td class="text-right"><?= number_format((float) $row->qty_saldo, 4, ',', '.') ?></td>
                            <td class="text-right"><?= number_format((float) $row->nilai_saldo, 2, ',', '.') ?></td>
                            <td class="text-right"><?= $row->harga_rata_rata === null ? '' : number_format((float) $row->harga_rata_rata, 6, ',', '.') ?></td>
                            <td><span class="badge <?= $badge ?>"><?= esc($status) ?></span></td>
                            <td><?= esc($row->validasi_catatan ?? '') ?></td>
                            <td><?= esc($row->keterangan ?? '') ?></td>
                            <td><?= esc($row->inputby ?? '') ?></td>
                            <td><?= esc($row->inputdate ?? '') ?></td>
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
