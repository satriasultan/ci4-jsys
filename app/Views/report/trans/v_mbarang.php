<style>
    .btn-excel-modern {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none; color: #fff; font-weight: 600;
        border-radius: 8px; padding: 10px 20px;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        transition: all 0.25s ease;
    }
    .btn-excel-modern:hover {
        background: linear-gradient(135deg, #34d399, #10b981);
        color: #fff; transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.45);
    }
    .btn-excel-modern:disabled {
        background: #cbd5e1; box-shadow: none;
        transform: none; cursor: not-allowed;
    }
    .btn-tarik-modern {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border: none; color: #fff; font-weight: 600;
        border-radius: 8px; padding: 10px 20px;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        transition: all 0.25s ease;
    }
    .btn-tarik-modern:hover {
        background: linear-gradient(135deg, #60a5fa, #3b82f6);
        color: #fff; transform: translateY(-2px);
    }
    .btn-tarik-modern:disabled {
        background: #cbd5e1; box-shadow: none;
        transform: none; cursor: not-allowed;
    }
    #tblPreviewMB_wrapper { overflow-x: auto; }
    #tblPreviewMB { font-size: 12.5px; }
    #tblPreviewMB thead th { white-space: nowrap; background: linear-gradient(
                    135deg,
                    #1f2937,
                    #374151
            ) !important; }
    #tblPreviewMB tbody td { white-space: nowrap; }
    .preview-info {
        font-size: 12px; color: #64748b;
        padding: 6px 10px; background: #f8fafc;
        border-radius: 6px; display: inline-block;
    }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= ucwords(strtolower(trim($title))) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right:10px; vertical-align:middle; padding-top:0.7%;">
                        <i style="color:transparent;"><?= $t ?></i> Menu ID <?= $version ?>
                    </div>
                    <?php foreach ($y as $y1) { ?>
                        <?php if (trim($y1->kodemenu) != trim($kodemenu)) { ?>
                            <li class="breadcrumb-item">
                                <a href="<?= base_url(trim($y1->linkmenu)) ?>">
                                    <i class="fa <?= trim($y1->iconmenu) ?>"></i> <?= trim($y1->namamenu) ?>
                                </a>
                            </li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active">
                                <i class="fa <?= trim($y1->iconmenu) ?>"></i> <?= trim($y1->namamenu) ?>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<?= $message ?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title mb-0" style="color: white;">
                    <i class="fa fa-filter"></i> Filter
                </h3>
            </div>

            <div class="card-body">
                <form id="formLaporanParam"
                      action="<?= base_url('report/trans/downloadLaporanMasterBarang') ?>"
                      method="post"
                      target="downloadFrame">

                    <input type="hidden" id="jenisLaporan" name="jenisLaporan"
                           value="<?= esc($jenisLaporan) ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ID Barang</label>
                                <select name="idbarang" id="lapIdbarang"
                                        class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-wrap" style="gap:8px;">
                    <button type="button" class="btn btn-tarik-modern" id="btnTarikData" onclick="tarikData()">
                        <i class="fa fa-search"></i> Tarik Data
                    </button>
                    <button type="button" class="btn btn-excel-modern" id="btnCetakExcel"
                            onclick="submitLaporan()" disabled>
                        <i class="fa fa-file-excel-o"></i> Cetak Excel
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetLaporanParam()">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </div>
            </div>

            <div class="card-body" id="previewWrapper" style="display:none;">
                <div id="tblPreviewMB_wrapper">
                    <table id="tblPreviewMB" class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Qty Minimum</th>
                                <th>Prk Persediaan</th>
                                <th>Satuan</th>
                                <th>Keterangan</th>
                                <th>Discontinue</th>
                                <th>Create By</th>
                                <th>Terakhir Simpan</th>
                                <th>Group_</th>
                                <th>Berat</th>
                                <th>Panjang</th>
                                <th>Lebar</th>
                                <th>Tinggi</th>
                                <th>Jenis Barang</th>
                                <th>Golongan</th>
                                <th>Jenis Produk</th>
                                <th>Kelompok Barang</th>
                                <th>Launching Date</th>
                                <th>Gudang Default</th>
                                <th>Spec</th>
                                <th>Principal</th>
                                <th>Batas Expired Date</th>
                                <th>Fast Moving</th>
                                <th>With Serial No.</th>
                                <th>Prk Surat Jalan</th>
                                <th>Volume</th>
                                <th>Lokasi Reff</th>
                                <th>Satuan DinKes</th>
                                <th>Konversi Dinkes</th>
                                <th>JOB</th>
                                <th>Approval</th>
                                <th>Tgl Approved</th>
                                <th>Approved by</th>
                                <th>Prk Revenue</th>
                                <th>Prk HPP</th>
                                <th>Prk Produksi</th>
                                <th>Kategori Barang</th>
                                <th>Gross Weight</th>
                                <th>Kode Barang Tax</th>
                                <th>Satuan Tax</th>
                            </tr>
                        </thead>
                        <tbody id="tblPreviewMBBody">
                            <tr><td colspan="42" class="text-center text-muted">Belum ada data.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
                    <div>
                        <label class="mr-2 mb-0">Baris per halaman:</label>
                        <select id="perpageSelect" class="form-control form-control-sm d-inline-block"
                                style="width:80px;" onchange="changePerpage()">
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                        </select>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagerPP"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<iframe name="downloadFrame" style="display:none;"></iframe>

<script src="<?= base_url('assets/pagejs/' . $pagejs) ?>"></script>