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

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-file-excel-o"></i> Upload PP (Permintaan Pembelian)</h3>
            </div>
            <div class="card-body">
                <p class="mb-2">Import data PP dari file Excel ke tabel <code>sc_im.lap_pp_win</code>.</p>
                <p class="text-muted small mb-0">
                    Template: 1 baris header, data mulai baris 2.<br>
                    Kolom: <code>pp, jurnal, date_, username, brg, qty, lns, gdg, nama_user, nama_barang, unit, spec, job, rem_pp, rem_pd, date_pakai, uniqueidpd</code>
                </p>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('tools/proses/uploadpp') ?>" class="btn btn-primary">
                    <i class="fa fa-upload"></i> Buka Upload PP
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-file-excel-o"></i> Upload PO (Purchase Order)</h3>
            </div>
            <div class="card-body">
                <p class="mb-2">Import data PO dari file Excel ke tabel <code>sc_im.lap_po_win</code>.</p>
                <p class="text-muted small mb-0">
                    Template: 1 baris header, data mulai baris 2.<br>
                    Kolom: <code>obl, jurnal, date_, sub, brg, gdgobl, qty, lns, val, job, unit, uniqueidobd, nama_supplier, nama_barang, jobname, priceobl, rempd, tglkirim</code>
                </p>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('tools/proses/uploadpo') ?>" class="btn btn-success">
                    <i class="fa fa-upload"></i> Buka Upload PO
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-cubes"></i> Upload Saldo Awal Persediaan</h3>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    Import saldo awal persediaan dari file Excel ke staging
                    <code>sc_im.stock_persediaan_opening</code>, lalu validasi dan posting ke
                    buku besar persediaan <code>sc_trx.stkblc</code>.
                </p>
                <p class="text-muted small mb-0">
                    Template: <b>Template_Import_Stock_Persediaan.xlsx</b>, sheet
                    <code>template_stock_persediaan</code>, 1 baris header, data mulai baris 2.<br>
                    Kolom wajib: <code>tanggal_saldo, idbranch, kode_gudang, kode_barang, qty_saldo</code>.<br>
                    Kolom lain: <code>cabang, nama_gudang, nama_barang, satuan, batch_spec,
                    nilai_saldo, harga_rata_rata, keterangan, status_validasi</code>.<br>
                    Sheet <code>perlu_rekonsiliasi</code> tidak ikut diproses; baris berstatus
                    <code>JANGAN IMPORT</code> otomatis dilewati.<br>
                    Alur: Upload &rarr; <code>fn_validasi_stock_persediaan_opening()</code> &rarr;
                    <code>fn_posting_stock_persediaan_opening()</code>.
                </p>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('tools/import/uploadstock') ?>" class="btn btn-warning">
                    <i class="fa fa-upload"></i> Buka Upload Saldo Awal
                </a>
            </div>
        </div>
    </div>
</div>
