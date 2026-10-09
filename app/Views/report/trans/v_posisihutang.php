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
    #tblPreviewPSH_wrapper { overflow-x: auto; }
    #tblPreviewPSH { font-size: 12.5px; }
    #tblPreviewPSH thead th {
        white-space: nowrap;
        background: linear-gradient(135deg, #1f2937, #374151) !important;
        color: #fff;
    }
    #tblPreviewPSH tbody td { white-space: nowrap; }
</style>

<!-- content-header: sama seperti biasanya -->

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title mb-0" style="color:white;"><i class="fa fa-filter"></i> Filter</h3>
            </div>
            <div class="card-body">
                <form id="formLaporanParam"
                      action="<?= base_url('report/trans/downloadLaporanPosisiHutang') ?>"
                      method="post" target="downloadFrame">

                    <input type="hidden" id="jenisLaporan" name="jenisLaporan" value="<?= esc($jenisLaporan) ?>">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="lapTglRange"
                                       name="tglrange" placeholder="dd-mm-yyyy - dd-mm-yyyy" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cabang / Job</label>
                                <select name="cabang" id="lapCabang" class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Perkiraan (COA)</label>
                                <select name="kdprk" id="lapKdprk" class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Supplier</label>
                                <select name="kdsupplier" id="lapKdsupplier" class="form-control select2" style="width:100%"></select>
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
                <div id="tblPreviewPSH_wrapper">
                    <table id="tblPreviewPSH" class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Prk</th>
                                <th>Nama Prk</th>
                                <th>Kode Supplier</th>
                                <th>Nama Supplier</th>
                                <th>Saldo Awal</th>
                                <th>Debet</th>
                                <th>Kredit</th>
                                <th>Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody id="tblPreviewPSHBody">
                            <tr><td colspan="9" class="text-center text-muted">Belum ada data.</td></tr>
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