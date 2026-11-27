<?= $this->include('sales/postsales/_corp_theme') ?>
<div class="corp-form-container">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div>
            <div class="col-sm-6 d-flex justify-content-end align-items-center">
                <div style="margin-right: 10px; font-size: 13px;">Menu ID <?php echo $version; ?></div>
                <ol class="breadcrumb mb-0">
                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>">
                    <?php foreach ($y as $y1) { ?>
                        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                            <li class="breadcrumb-item"><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></a></li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ol>
            </div>
        </div>
    </div>
</div>
<?php echo $message;?>
<?php
    $isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
        $disabled = $isIT ? '' : 'disabled';

    /* =====================================================
     * AUTOFILL INPUT AWAL CABANG / JOB
     * Cabang mengikuti session user login.
     * Hanya PT JATIM TAMAN STEEL yang boleh memilih bebas.
     * ===================================================== */
    $sessionBranchId   = trim((string) session()->get('idbranchjob'));
    $sessionBranchName = strtoupper(trim((string) session()->get('nmbranchjob')));
    $sessionBranchKey  = preg_replace('/[^A-Z0-9]/', '', $sessionBranchName);

    /* Cabang yang TIDAK boleh memilih cabang lain (terkunci ke cabang session):
       PLANT I / PLANT II (PLAN 1 / PLAN 2). */
    $isPlantBranch      = strpos($sessionBranchKey, 'PLANT') !== false
                        || strpos($sessionBranchKey, 'PLAN1') !== false
                        || strpos($sessionBranchKey, 'PLAN2') !== false;

    /* Hanya kantor pusat "01 JATIM TAMAN STEEL" yang boleh memilih cabang bebas. */
    $isHeadOfficeBranch = ($sessionBranchId === '01')
                        || strpos($sessionBranchKey, 'JATIMTAMANSTEEL') !== false;

    $canChooseBranch    = $isHeadOfficeBranch && !$isPlantBranch;
?>
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('sales/postsales/finalEntryPenjualan') ?>" method="post" id="formPO">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Penjualan</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Master
                            </div>
                            <div class="row">

                                <!-- LEFT COLUMN -->
                                <div class="col-md-3">

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="cabang">Cabang / Job</label>
                                                <select name="cabang" id="cabang" class="form-control" required
                                                    data-session-branch-id="<?= esc($sessionBranchId) ?>"
                                                    data-session-branch-name="<?= esc($sessionBranchName) ?>"
                                                    data-can-choose-branch="<?= $canChooseBranch ? '1' : '0' ?>"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>No. Jurnal Penjualan</label>

                                                    <div class="d-flex">
                                                        <input type="text" name="prefix" id="prefix" class="form-control me-1" maxlength="3" style="text-transform: uppercase;" pattern="[A-Za-z0-9]{1,3}" title="Prefix hanya boleh huruf dan angka, maksimal 3 karakter">

                                                        <span class="px-2 align-self-center">/</span>

                                                        <input type="text"
                                                            name="infix"
                                                            id="infix"
                                                            class="form-control mx-1"
                                                            readonly>

                                                        <span class="px-2 align-self-center">/</span>

                                                        <input type="text" name="suffix" id="suffix" class="form-control ms-1" maxlength="6" pattern="^[A-Za-z0-9]{0,2}[0-9]{4}$" title="Maksimal 6 karakter. 4 karakter terakhir wajib angka." style="text-transform: uppercase;">

                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="20"     value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>" style="text-transform: uppercase;" readonly>

                                    </div>

                                    <!-- Grade pelanggan: dipakai saat menyimpan detail, tidak diinput manual -->
                                    <input type="hidden" name="gradecustomer" id="gradecustomer" value="<?= isset($dtldata['gradecustomer']) ? esc(trim((string) $dtldata['gradecustomer'])) : '' ?>">

                                </div>

                                <!-- RIGHT COLUMN -->
                                <div class="col-md-2">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="docdate">Tanggal</label>
                                                <input type="text"
                                                name="docdate"
                                                id="docdate"
                                                class="form-control"
                                                placeholder="Tanggal">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="jthtempo">Jatuh Tempo</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        name="jthtempo"
                                                        id="jthtempo"
                                                        class="form-control ratakanan jaccseparator"
                                                        placeholder="0.00">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">hari</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="kdcustomer">Customer</label>
                                                <select name="kdcustomer" id="kdcustomer" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatcustomer">Alamat Customer</label>
                                                <textarea name="alamatcustomer"
                                                    id="alamatcustomer"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Customer"
                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="kdcustomerdeliv">Deliver To</label>
                                                <select name="kdcustomerdeliv" id="kdcustomerdeliv" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatcustomerdeliv">Alamat Kirim Ke</label>
                                                <textarea name="alamatcustomerdeliv"
                                                    id="alamatcustomerdeliv"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Kirim Ke"
                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="row align-items-end">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="currcode" class="form-label">Mata Uang</label>
                                                    <select name="currcode"
                                                            id="currcode"
                                                            class="form-select select2"
                                                            required>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="kurs" class="form-label">Nilai Tukar</label>
                                                    <input type="text"
                                                        name="kurs"
                                                        id="kurs"
                                                        class="form-control ratakanan jaccseparator"
                                                        placeholder="Nilai tukar akan muncul disini"
                                                        >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="idtax">Pajak</label>
                                                <select name="idtax" id="idtax" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="margin-top: 30px;">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        name="isinclusive"
                                                        id="isinclusive"
                                                        <?= (isset($dtldata['isinclusive']) && strtoupper(trim((string) $dtldata['isinclusive'])) === 'YES') ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="isinclusive">
                                                        Inclusive
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="kdsalesman">Salesman</label>
                                                <select name="kdsalesman" id="kdsalesman" class="form-control" required>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="margin-top: 30px;">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        name="isopenprice"
                                                        id="isopenprice"
                                                        <?= (isset($dtldata['isopenprice']) && strtoupper(trim((string) $dtldata['isopenprice'])) === 'YES') ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="isopenprice">
                                                        Open Price
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                </div>
            </div>


            <div class="card mt-3 card-primary">
                <div class="card-header clearfix">
                    <h3 class="card-title">
                        <i class="fa fa-list-alt"></i> Detail Barang
                        <button type="button"
                                class="btn btn-info pj-tarik-do"
                                data-bs-toggle="tooltip"
                                title="Tarik sebagian/seluruh detail dari Delivery Order"
                                id="btnTarikDO">
                            <i class="fa fa-download"></i> Tarik DO
                        </button>
                    </h3>

                    <div class="header-actions">
                        <button type="button"
                                class="btn btn-success btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Input Data (Ctrl + Q / Alt+N)"
                                id="btnAddDetail">
                            <i class="fa fa-plus"></i>
                        </button>

                        <button type="button"
                                class="btn btn-warning btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Update Data (Ctrl + E)"
                                id="btnUpdateDetail">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button type="button"
                                class="btn btn-danger btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Hapus Data (Ctrl + D)"
                                id="btnDeleteDetail">
                            <i class="fa fa-trash"></i>
                        </button>


                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive" style="overflow-x: auto;">
                                <table id="tabpenjualandtl" class="table table-bordered table-striped sales-dtl-table" style="width:100%;" cellspacing="0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="checkAll">
                                            </th>
                                            <th>No. SJ / DO</th>
                                            <th>SO</th>
                                            <th>ID Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Principal</th>
                                            <th>Gudang</th>
                                            <th>Spec</th>
                                            <th class="pj-col-unit">Satuan</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Multi Disc</th>
                                            <th>Nilai</th>
                                            <th>Keterangan</th>
                                            <th>Stok</th>
                                            <th class="pj-col-aksi">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot id="pjInlineFoot">
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Hint format angka detail: desimal pakai titik, maksimal 4 digit -->
                            <div class="pj-num-hint">
                                <i class="fa fa-info-circle"></i>
                                Desimal gunakan titik (<b>.</b>), maksimal <b>4</b> angka di belakang koma
                                &mdash; contoh: <code>1.5</code> atau <code>0.0001</code>.
                                Angka bulat cukup ditulis tanpa koma, mis. <code>5</code>.
                                <br>
                                Baris detail dapat diambil dari <b>No. SO</b> / <b>No. SJ</b> (pilih salah satu pada baris input),
                                atau klik tombol <b>Tarik DO</b> di atas tabel untuk menarik sebagian / seluruh detail Delivery Order.
                                Qty / Harga / Multi Disc dapat diubah langsung pada baris.
                            </div>

                            <!-- Hint shortcut keyboard: tambah item (Alt+N) -->
                            <div class="pj-num-hint pj-key-hint">
                                <i class="fa fa-bolt"></i>
                                Tambah item: tekan <kbd>Alt</kbd> + <kbd>N</kbd> (atau <kbd>Ctrl</kbd> + <kbd>Q</kbd>),
                                atau klik tombol <b>+</b> di atas tabel &mdash;
                                <kbd>Enter</kbd> menyimpan baris, <kbd>Esc</kbd> membatalkan baris.
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan"
                                            id="keterangan"
                                            class="form-control"
                                            rows="3"
                                            placeholder="Keterangan"
                                            style="text-transform:uppercase;"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="carabayar">Cara Bayar</label>
                                        <input name="carabayar"
                                            id="carabayar"
                                            class="form-control"
                                            type="text"
                                            maxlength="30"
                                            placeholder="Cara Bayar"
                                            style="text-transform:uppercase;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12 ">
                                    <div class="col-md-4" style="float: right;">
                                        <div class="form-group mb-2">
                                            <div class="row">
                                                <div class="col-md-6 text-end">
                                                    <label for="dpp" class="mt-2">DPP</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text"
                                                        name="dpp"
                                                        id="dpp"
                                                        class="form-control jaccseparator ratakanan"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 ">
                                    <div class="col-md-4" style="float: right;">
                                        <div class="form-group mb-2">
                                            <div class="row">
                                                <div class="col-md-6 text-end">
                                                    <label for="jumlahpajak" class="mt-2">Jumlah Pajak</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text"
                                                        name="jumlahpajak"
                                                        id="jumlahpajak"
                                                        class="form-control jaccseparator ratakanan"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 ">
                                    <div class="col-md-4" style="float: right;">
                                        <div class="form-group mb-3">
                                            <div class="row">
                                                <div class="col-md-6 text-end">
                                                    <label for="total" class="mt-2">Total</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text"
                                                        name="total"
                                                        id="total"
                                                        style="font-weight: bold;"
                                                        class="form-control jaccseparator ratakanan"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                </div>

                <div class="card-footer bg-light">
                    <a href="<?= base_url('sales/postsales/clearEntryPenjualan') ?>"
                        onclick="return confirm('Are you sure clear this entry?')"
                        class="btn btn-default float-left">
                        <i class="fa fa-arrow-left"></i>
                        Kembali
                    </a>
                    <?php if ($typeform != 'DETAIL' && $dtldata != null): ?>
                        <button type="submit"
                                onclick="return confirm('Finish Entry?')"
                                class="btn btn-success btn-lg float-right">
                            <i class="fa fa-save"></i> Simpan Final Data
                        </button>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </form>
        <!-- /.card -->
    <!--/.col (left) -->
    <!-- right column -->
    <div class="col-md-6">

    </div>
    <!--/.col (right) -->
</div>

<!-- ================= MODAL TARIK DELIVERY ORDER ================= -->
<div class="modal fade" id="modalTarikDO" tabindex="-1" role="dialog" aria-labelledby="modalTarikDOLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalTarikDOLabel">
                    <i class="fa fa-download"></i> Tarik Delivery Order
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <div class="row g-1 mb-1">
                    <div class="col-8">
                        <input type="text" id="doSearch" class="form-control form-control-sm" placeholder="Cari No. DO / Customer...">
                    </div>
                    <div class="col-4 text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnSearchDO">
                            <i class="fa fa-search"></i> Cari
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCollapseDO">
                            <i class="fa fa-compress"></i> Tutup
                        </button>
                    </div>
                </div>

                <div class="do-pick-wrap">
                    <table class="table table-sm table-bordered do-pick-table mb-0" id="tblDOList" cellspacing="0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th width="22"></th>
                                <th width="24"><input type="checkbox" id="doChkAll" title="Centang semua detail DO yang sudah dibuka"></th>
                                <th>No. DO</th>
                                <th width="82">Tanggal</th>
                                <th>Customer</th>
                                <th width="108" class="text-end">Total</th>
                                <th width="58">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="7" class="text-center text-muted py-2">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="do-pick-note">
                    Klik baris DO untuk membuka detailnya, lalu centang detail yang mau ditarik (boleh sebagian).
                    <b>Sisa qty</b> = Qty DO dikurangi qty yang sudah masuk SJ,
                    dokumen penjualan ini, dan dokumen penjualan final.
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-success btn-lg" id="btnPullDO">
                    <i class="fa fa-download"></i> Tarik ke Penjualan (<span id="doPickCount">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    /* Baris input detail inline pada tabel Detail Barang (pola grid Sales Order) */
    #tabpenjualandtl tfoot tr.pj-inline-row td {
        padding: 4px 3px;
        vertical-align: middle;
        background-color: #fff8e1;
    }

    #tabpenjualandtl tfoot tr.pj-inline-row .form-control-sm {
        font-size: 11px;
        padding: 2px 4px;
        min-width: 62px;
        height: 28px !important;
        min-height: 28px !important;
    }

    /* Sel readonly pada baris edit (No. SJ / SO / barang / satuan) */
    #tabpenjualandtl tfoot tr.pj-inline-row td.pj-inline-ro {
        font-size: 11px;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 140px;
    }

    /* Kolom Satuan diberi lebar minimum agar isi kolom tetap terlihat */
    #tabpenjualandtl thead th.pj-col-unit,
    #tabpenjualandtl tbody td.pj-col-unit-cell {
        width: 72px !important;
        min-width: 72px !important;
    }

    #tabpenjualandtl tfoot tr.pj-inline-row .select2-container {
        min-width: 96px;
    }

    /* Select2 dalam baris input dibuat ringkas (pola grid inline SO) */
    #tabpenjualandtl tfoot tr.pj-inline-row .select2-container .select2-selection--single {
        height: 28px !important;
        min-height: 28px !important;
    }

    #tabpenjualandtl tfoot tr.pj-inline-row .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 5px !important;
        padding-right: 18px !important;
        line-height: 26px !important;
        font-size: 11px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #tabpenjualandtl tfoot tr.pj-inline-row .select2-container .select2-selection--single .select2-selection__arrow {
        top: 1px !important;
        height: 26px !important;
        right: 1px !important;
        width: 16px !important;
    }

    /* Empty state detail barang (pola grid inline Sales Order) */
    #tabpenjualandtl tbody td.dataTables_empty {
        padding: 16px 10px !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        background: #ffffff !important;
        text-align: center;
        cursor: pointer;
    }

    #tabpenjualandtl tbody td.dataTables_empty:hover {
        background: #eef6ff !important;
        color: #1e3a5f !important;
    }

    #tabpenjualandtl tbody td.dataTables_empty i {
        margin-right: 6px;
        font-size: 15px;
        color: #1e3a5f;
    }

    /* Pesan empty state disembunyikan selama baris input inline terbuka */
    #tabpenjualandtl.pj-has-draft tbody td.dataTables_empty {
        display: none !important;
    }

    #tabpenjualandtl tfoot tr.pj-inline-row.table-info td {
        background-color: #d1ecf1;
    }


    /* Kolom Aksi: letak tombol disamakan dengan grid inline Sales Order */
    #tabpenjualandtl thead th.pj-col-aksi,
    #tabpenjualandtl tbody td.pj-col-aksi-cell,
    #tabpenjualandtl tfoot td.pj-col-aksi {
        width: 76px !important;
        min-width: 76px !important;
        text-align: center !important;
        white-space: nowrap !important;
        padding-left: 4px !important;
        padding-right: 4px !important;
    }

    #tabpenjualandtl tfoot td.pj-col-no {
        width: 30px !important;
        min-width: 30px !important;
        text-align: center !important;
        padding-left: 2px !important;
        padding-right: 2px !important;
    }

    /* Tombol aksi kecil di dalam baris (draft inline & baris tersimpan) */
    #tabpenjualandtl tfoot tr.pj-inline-row .pj-inline-btn,
    #tabpenjualandtl tbody .pj-row-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        margin: 0 1px;
        padding: 0;
        font-size: 12px;
        line-height: 1;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background .12s ease, transform .12s ease;
    }

    #tabpenjualandtl tfoot tr.pj-inline-row .pj-inline-btn:hover,
    #tabpenjualandtl tbody .pj-row-btn:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(15, 23, 42, .22);
    }

    #tabpenjualandtl tfoot tr.pj-inline-row .pj-inline-save {
        background: #16a34a;
    }

    #tabpenjualandtl tfoot tr.pj-inline-row .pj-inline-cancel {
        background: #64748b;
    }

    #tabpenjualandtl tbody .pj-row-btn-edit {
        background: #d97706;
    }

    #tabpenjualandtl tbody .pj-row-btn-delete {
        background: #dc2626;
    }

    /* Hint format angka pada grid detail (desimal titik, maksimal 4 digit) */
    .pj-num-hint {
        margin-top: 6px;
        font-size: 11.5px;
        line-height: 1.5;
        color: #64748b;
    }

    .pj-num-hint i {
        margin-right: 4px;
        color: #1e3a5f;
    }

    .pj-num-hint b {
        color: #1e3a5f;
    }

    .pj-num-hint code {
        padding: 0 3px;
        font-size: 11px;
        color: #1e3a5f;
        background: #eef6ff;
        border-radius: 3px;
    }

    /* Hint shortcut keyboard di bawah grid detail (Alt+N untuk tambah item) */
    .pj-key-hint {
        margin-top: 4px;
        padding: 5px 8px;
        font-size: 11.5px;
        line-height: 1.6;
        color: #475569;
        background: #f1f5f9;
        border-left: 3px solid #0284c7;
        border-radius: 4px;
    }

    .pj-key-hint i {
        margin-right: 5px;
        color: #0284c7;
    }

    .pj-key-hint kbd {
        display: inline-block;
        min-width: 16px;
        padding: 1px 5px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.4;
        color: #1e3a5f;
        text-align: center;
        white-space: nowrap;
        background: #ffffff;
        border: 1px solid #bfdbfe;
        border-bottom-width: 2px;
        border-radius: 4px;
    }
    /* ===== Tombol Tarik DO: menempel di kanan judul card "Detail Barang" ===== */
    .corp-form-container .card-header h3.card-title .pj-tarik-do {
        margin-left: 12px;
        padding: 6px 16px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.35;
        border-radius: 4px;
    }

    /* ===== Modal Tarik DO: parent-child DO header/detail, compact ===== */
    #modalTarikDO .modal-body { padding: 8px 10px 4px 10px; }
    #modalTarikDO .modal-footer { padding: 6px 10px; }

    #modalTarikDO .do-pick-wrap {
        max-height: 56vh;
        overflow: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    #modalTarikDO .do-pick-table { font-size: 11.5px; margin-bottom: 0; }
    #modalTarikDO .do-pick-table > thead > tr > th {
        padding: 3px 6px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        background-color: #0d6efd;
        color: #fff;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    #modalTarikDO .do-pick-table > tbody > tr > td {
        padding: 2px 6px;
        vertical-align: middle;
    }

    #modalTarikDO tr.do-pick-parent { cursor: pointer; }
    #modalTarikDO tr.do-pick-parent:hover > td { background-color: #eef5ff; }
    #modalTarikDO tr.do-pick-parent.do-open > td { background-color: #dbe9fb; font-weight: 600; }
    #modalTarikDO .do-pick-toggle { color: #0d6efd; text-align: center; }
    #modalTarikDO .do-pick-col-no { font-family: Consolas, monospace; }
    #modalTarikDO .do-pick-col-cust { text-transform: uppercase; }

    #modalTarikDO tr.do-pick-child > td.do-pick-child-cell {
        padding: 0 0 2px 20px;
        background-color: #fbfcfe;
        border-top: 0;
    }

    #modalTarikDO .do-pick-dtl { font-size: 11px; background-color: #fff; margin-bottom: 2px; }
    #modalTarikDO .do-pick-dtl > thead > tr > th {
        padding: 2px 5px;
        font-size: 10.5px;
        font-weight: 600;
        background-color: #eef2f8;
        color: #33475b;
        white-space: nowrap;
        border-bottom: 1px solid #dee2e6;
    }
    #modalTarikDO .do-pick-dtl > tbody > tr > td { padding: 1px 5px; vertical-align: middle; }
    #modalTarikDO .do-pick-dtl > tbody > tr:hover > td { background-color: #fffbe6; }
    #modalTarikDO .do-pick-dtl tr.do-pick-dtl-done > td { color: #a3aab5; background-color: #f7f7f7; }
    #modalTarikDO .do-pick-item { font-family: Consolas, monospace; color: #0b5ed7; }

    #modalTarikDO .do-pick-note {
        margin-top: 4px;
        font-size: 11px;
        line-height: 1.35;
        color: #6c757d;
    }

    #modalTarikDO .do-pick-loading { padding: 2px 4px; font-size: 11px; }

    #modalTarikDO input[type="checkbox"] { margin: 0; vertical-align: middle; cursor: pointer; }
</style>

<?php
/* Cache-busting JS detail Penjualan (agar perubahan grid inline langsung terpakai) */
$pjJsRel = 'assets/pagejs/sales/postsales/penjualan/penjualan.js';
$pjJsAbs = (defined('FCPATH') ? FCPATH : '') . $pjJsRel;
$pjJsVer = is_file($pjJsAbs) ? filemtime($pjJsAbs) : date('YmdHis');

/* Cache-busting JS grid inline Penjualan */
$pjInlineJsRel = 'assets/pagejs/sales/postsales/penjualan/penjualan_inline.js';
$pjInlineJsAbs = (defined('FCPATH') ? FCPATH : '') . $pjInlineJsRel;
$pjInlineJsVer = is_file($pjInlineJsAbs) ? filemtime($pjInlineJsAbs) : date('YmdHis');

/* Cache-busting autofill cabang / job (mengikuti session user) */
$pjBranchJsRel = 'assets/pagejs/sales/postsales/sales_branch_autofill.js';
$pjBranchJsAbs = (defined('FCPATH') ? FCPATH : '') . $pjBranchJsRel;
$pjBranchJsVer = is_file($pjBranchJsAbs) ? filemtime($pjBranchJsAbs) : date('YmdHis');
?>
<script type="application/javascript" src="<?= base_url($pjJsRel) ?>?v=<?= $pjJsVer ?>"></script>
<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/postsales/sales_shortcut.js') ?>"></script>
<script type="application/javascript" src="<?= base_url($pjBranchJsRel) ?>?v=<?= $pjBranchJsVer ?>"></script>
<script type="application/javascript" src="<?= base_url($pjInlineJsRel) ?>?v=<?= $pjInlineJsVer ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();

        var dtldata = <?= json_encode($dtldata ?? null) ?>;

        if (dtldata && dtldata.docno && dtldata.docno.trim() !== '') {
            $('#docno').val(dtldata.docno.trim()).prop('readonly', true);
        }

        $('#docdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));

            var docdate = picker.startDate.format('DD-MM-YYYY');
            var currcode = $('[name="currcode"]').val();
            if (currcode && docdate) {
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_currency' + '?var=' + currcode + '&docdate=' + encodeURIComponent(docdate),
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    if (datax.items && datax.items.length > 0) {
                        setJtsValue('[name="kurs"]', convertToDbNumber(datax.items[0].kurs || 1));
                    }
                });
            }
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Event change untuk input manual + validasi
        $('#docdate').on('change', function() {
            var docdate = $(this).val();
            var currcode = $('[name="currcode"]').val();

            if (docdate && !validateDocdate(docdate)) {
                var drp = $(this).data('daterangepicker');
                var defaultDate = drp ? drp.startDate.format('DD-MM-YYYY') : moment().format('DD-MM-YYYY');
                $(this).val(defaultDate);
                return;
            }

            if (currcode && docdate) {
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_currency' + '?var=' + currcode + '&docdate=' + encodeURIComponent(docdate),
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    if (datax.items && datax.items.length > 0) {
                        setJtsValue('[name="kurs"]', convertToDbNumber(datax.items[0].kurs || 1));
                    }
                });
            } else if (!docdate) {
                setJtsValue('[name="kurs"]', 1);
            }
        });

        // Fungsi validasi docdate (harus di dalam bulan infix)
        function validateDocdate(docdate) {
            if (!docdate) return false;

            var docMoment = moment(docdate, 'DD-MM-YYYY', true);
            if (!docMoment.isValid()) {
                Swal.fire('Error', 'Format docdate tidak valid (DD-MM-YYYY)', 'warning');
                return false;
            }

            var infix = $('#infix').val() || '';
            if (infix.length === 4) {
                var yy = infix.substring(0, 2);
                var mm = infix.substring(2, 4);
                var year = 2000 + parseInt(yy, 10);
                var month = parseInt(mm, 10) - 1;
                var startDate = moment([year, month, 1]);
                var endDate = moment(startDate).endOf('month');

                if (!docMoment.isBetween(startDate, endDate, 'day', '[]')) {
                    Swal.fire('Error', 'Docdate harus di bulan ' + startDate.format('MMMM YYYY'), 'warning');
                    return false;
                }
            }

            return true;
        }
    });
</script>

</div>

