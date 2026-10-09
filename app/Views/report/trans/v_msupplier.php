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
    #tblPreviewMS_wrapper { overflow-x: auto; }
    #tblPreviewMS { font-size: 12.5px; }
    #tblPreviewMS thead th {
        white-space: nowrap;
        background: linear-gradient(135deg, #1f2937, #374151) !important;
        color: #fff;
    }
    #tblPreviewMS tbody td { white-space: nowrap; }
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
                            <li class="breadcrumb-item"><a href="<?= base_url(trim($y1->linkmenu)) ?>"><i class="fa <?= trim($y1->iconmenu) ?>"></i> <?= trim($y1->namamenu) ?></a></li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active"><i class="fa <?= trim($y1->iconmenu) ?>"></i> <?= trim($y1->namamenu) ?></li>
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
                <h3 class="card-title mb-0" style="color:white;"><i class="fa fa-filter"></i> Filter</h3>
            </div>
            <div class="card-body">
                <form id="formLaporanParam"
                      action="<?= base_url('report/trans/downloadLaporanMasterSupplier') ?>"
                      method="post" target="downloadFrame">

                    <input type="hidden" id="jenisLaporan" name="jenisLaporan" value="<?= esc($jenisLaporan) ?>">

                    <div class="row">
                       <div class="col-md-6">
                            <div class="form-group">
                                <label>Supplier</label>
                                <select name="kdsupplier" id="lapKdsupplier"
                                        class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer d-flex flex-wrap" style="gap:8px;">
                <button type="button" class="btn btn-tarik-modern" id="btnTarikData" onclick="tarikData()">
                    <i class="fa fa-search"></i> Tarik Data
                </button>
                <button type="button" class="btn btn-excel-modern" id="btnCetakExcel" onclick="submitLaporan()" disabled>
                    <i class="fa fa-file-excel-o"></i> Cetak Excel
                </button>
                <button type="button" class="btn btn-default" onclick="resetLaporanParam()">
                    <i class="fa fa-refresh"></i> Reset
                </button>
            </div>

            <div class="card-body" id="previewWrapper" style="display:none;">
                <div id="tblPreviewMS_wrapper">
                    <table id="tblPreviewMS" class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>CP</th>
                                <th>Phone</th>
                                <th>Fax</th>
                                <th>Alamat</th>
                                <th>Kode Post</th>
                                <th>NPWP</th>
                                <th>Keterangan</th>
                                <th>Kota</th>
                                <th>Market</th>
                                <th>Email</th>
                                <th>Jabatan</th>
                                <th>NPKP</th>
                                <th>Periode Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody id="tblPreviewMSBody">
                            <tr><td colspan="16" class="text-center text-muted">Belum ada data.</td></tr>
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
                    <nav><ul class="pagination pagination-sm mb-0" id="pagerPP"></ul></nav>
                </div>
            </div>
        </div>
    </div>
</div>

<iframe name="downloadFrame" style="display:none;"></iframe>
<script src="<?= base_url('assets/pagejs/' . $pagejs) ?>"></script>