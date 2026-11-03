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
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-upload"></i> Form Upload PO</h3>
            </div>
            <form action="<?= esc($upload_url) ?>" method="post" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <div class="form-group">
                        <label for="import">File Excel PO (.xls / .xlsx)</label>
                        <input type="file" id="import" name="import" accept=".xls,.xlsx" required>
                        <p class="help-block mb-0">
                            File <b>Template_Import_Outstanding_PO.xlsx</b>: hanya sheet
                            <code><?= esc($sheet_name ?? 'template_import') ?></code> yang diproses,
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
                    <button type="submit" class="btn btn-success float-right">
                        <i class="fa fa-upload"></i> Upload &amp; Proses
                    </button>
                </div>
            </form>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Urutan Header Template PO</h3>
            </div>
            <div class="card-body">
                <ol class="mb-0 small pl-3">
                    <?php foreach ($header_columns as $column) { ?>
                        <li><?= esc($column) ?></li>
                    <?php } ?>
                </ol>
                <p class="text-muted small mb-0 mt-2">
                    Nama header boleh tidak berurutan, tetapi harus lengkap (No. PO s/d Exclude/Include).
                    Kolom Nilai DPP, Nilai Pajak, Nilai Konversi, Batch, ETD Notes bersifat opsional.
                    Kolom <code>uniqueidpo</code> dan <code>imported_at</code> diisi otomatis oleh sistem.
                </p>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-exchange"></i> Sinkronisasi PO ke Transaksi</h3>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    Tombol di bawah menjalankan prosedur <code>sc_im.sp_import_template_po()</code>:
                    baris <code>sc_im.lap_po_win</code> dipetakan menjadi header <code>sc_trx.po</code>
                    dan detail <code>sc_trx.po_dtl</code>. Proses ini aman dijalankan berulang:
                    dokumen yang sudah ada tidak diduplikasi, hanya data header dan detail yang disegarkan.
                </p>
                <div class="row small">
                    <div class="col-sm-6">
                        <ul class="list-unstyled mb-2">
                            <li>Baris <code>lap_po_win</code>: <b><?= number_format((int) ($sync['baris_po'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>No. PO unik: <b><?= number_format((int) ($sync['total_po'] ?? 0), 0, ',', '.') ?></b></li>
                            <li>Belum masuk transaksi: <b><?= number_format((int) ($sync['belum_po'] ?? 0), 0, ',', '.') ?></b></li>
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        <ul class="list-unstyled mb-2">
                            <li><code>sc_trx.po</code>: <b><?= number_format((int) ($sync['trx_po'] ?? 0), 0, ',', '.') ?></b> header</li>
                            <li><code>sc_trx.po_dtl</code>: <b><?= number_format((int) ($sync['trx_po_dtl'] ?? 0), 0, ',', '.') ?></b> detail</li>
                            <li>Sudah masuk transaksi: <b><?= number_format((int) ($sync['sudah_po'] ?? 0), 0, ',', '.') ?></b> No. PO</li>
                        </ul>
                    </div>
                </div>
                <form action="<?= esc($import_url ?? base_url('tools/proses/uploadpo/import')) ?>" method="post"
                      onsubmit="return confirm('Jalankan prosedur sc_im.sp_import_template_po() sekarang?');">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-play"></i> Jalankan Import PO ke Transaksi
                    </button>
                    <span class="text-muted small ml-2">CALL sc_im.sp_import_template_po();</span>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data PO Terakhir (<?= count($rows) ?>)</h3>
            </div>
            <div class="card-body table-responsive" style="overflow-x:auto;">
                <table id="example1" class="table table-bordered table-striped table-sm" style="white-space:nowrap;">
                    <thead>
                    <tr>
                        <th>no_po</th>
                        <th>keterangan_po</th>
                        <th>kode_supplier</th>
                        <th>nama_supplier</th>
                        <th>kode_barang</th>
                        <th>nama_barang</th>
                        <th>keterangan_detail</th>
                        <th>qty_po</th>
                        <th>satuan</th>
                        <th>harga_po</th>
                        <th>total_price</th>
                        <th>real_price</th>
                        <th>ppn_nonppn</th>
                        <th>mata_uang</th>
                        <th>kurs</th>
                        <th>category</th>
                        <th>tahun_po</th>
                        <th>etd</th>
                        <th>docdate</th>
                        <th>senddate</th>
                        <th>cabang</th>
                        <th>user_pemohon</th>
                        <th>exclude_include</th>
                        <th>nilai_dpp</th>
                        <th>nilai_pajak</th>
                        <th>nilai_konversi</th>
                        <th>batch</th>
                        <th>etdnotes</th>
                        <th>uniqueidpo</th>
                        <th>imported_at</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row) { ?>
                        <tr>
                            <td><?= esc($row->no_po ?? '') ?></td>
                            <td><?= esc($row->keterangan_po ?? '') ?></td>
                            <td><?= esc($row->kode_supplier ?? '') ?></td>
                            <td><?= esc($row->nama_supplier ?? '') ?></td>
                            <td><?= esc($row->kode_barang ?? '') ?></td>
                            <td><?= esc($row->nama_barang ?? '') ?></td>
                            <td><?= esc($row->keterangan_detail ?? '') ?></td>
                            <td class="text-right"><?= esc($row->qty_po ?? '') ?></td>
                            <td><?= esc($row->satuan ?? '') ?></td>
                            <td class="text-right"><?= esc($row->harga_po ?? '') ?></td>
                            <td class="text-right"><?= esc($row->total_price ?? '') ?></td>
                            <td class="text-right"><?= esc($row->real_price ?? '') ?></td>
                            <td><?= esc($row->ppn_nonppn ?? '') ?></td>
                            <td><?= esc($row->mata_uang ?? '') ?></td>
                            <td class="text-right"><?= esc($row->kurs ?? '') ?></td>
                            <td><?= esc($row->category ?? '') ?></td>
                            <td class="text-right"><?= esc($row->tahun_po ?? '') ?></td>
                            <td><?= esc($row->etd ?? '') ?></td>
                            <td><?= esc($row->docdate ?? '') ?></td>
                            <td><?= esc($row->senddate ?? '') ?></td>
                            <td><?= esc($row->cabang ?? '') ?></td>
                            <td><?= esc($row->user_pemohon ?? '') ?></td>
                            <td><?= esc($row->exclude_include ?? '') ?></td>
                            <td class="text-right"><?= esc($row->nilai_dpp ?? '') ?></td>
                            <td class="text-right"><?= esc($row->nilai_pajak ?? '') ?></td>
                            <td class="text-right"><?= esc($row->nilai_konversi ?? '') ?></td>
                            <td><?= esc($row->batch ?? '') ?></td>
                            <td><?= esc($row->etdnotes ?? '') ?></td>
                            <td><?= esc($row->uniqueidpo ?? '') ?></td>
                            <td><?= esc($row->imported_at ?? '') ?></td>
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
