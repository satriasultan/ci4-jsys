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
    <form action="<?= base_url('sales/postsales/finalEntrySalesOrder') ?>" method="post" id="formPO">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Sales Order</h3>
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
                                                <label>No. Jurnal Sales Order</label>

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

                                    <!-- No. PO Customer = data header -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="pocust">No. PO Customer</label>
                                                <input name="pocust"
                                                    id="pocust"
                                                    class="form-control"
                                                    type="text"
                                                    maxlength="30"
                                                    placeholder="No. PO Customer"
                                                    style="text-transform:uppercase;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- No. DP (dropdown) -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="nodp">No. DP</label>
                                                <select name="nodp"
                                                    id="nodp"
                                                    class="form-control"
                                                    data-placeholder="No. DP"></select>
                                            </div>
                                        </div>
                                    </div>
                                        
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
                                                <label for="delivdate">Tanggal Pengiriman</label>
                                                <input type="text"
                                                    name="delivdate"
                                                    id="delivdate"
                                                    class="form-control"
                                                    placeholder="Tanggal Pengiriman">
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
                                        <!-- Grade Actual disembunyikan; elemen tetap ada karena nilainya dipakai JS (#gradecustomer) -->
                                        <div class="col-md-6 d-none" style="margin-top: -27px;">
                                            <div class="form-group">
                                                <label for="gradecustomer">Grade Actual</label>
                                                <input name="gradecustomer"
                                                    id="gradecustomer"
                                                    class="form-control"
                                                    type="text"
                                                    placeholder="Grade Actual" readonly
                                                    style="text-transform:uppercase;">
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-secondary" title="Change Grade Temp.">
                                                    <i class="fa fa-edit"></i> Change Grade Temp.
                                                </button>
                                            </div>
                                        </div> -->
                                        <div class="col-md-6" style="margin-top: -27px;">
                                            <div class="form-group">
                                                <button type="button"
                                                    id="btnChangeGradeTemp"
                                                    class="btn btn-warning"
                                                    title="Change Grade Temp.">
                                                    <i class="fa fa-edit"></i> Change Grade Temp.
                                                </button>
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
                                        <!-- <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatkirim">Alamat Kirim</label>
                                                <textarea name="alamatkirim"
                                                    id="alamatkirim"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Kirim"
                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div> -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="idtax">Pajak</label>
                                                <select name="idtax" id="idtax" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="margin-top: 30px;">
                                            <div class="form-group">
                                                <!-- Radio untuk Interngroup -->
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                        type="checkbox" 
                                                        name="isinclusive" 
                                                        id="isinclusive"
                                                        <?= isset($data['isinclusive']) && $data['isinclusive'] === 'YES' ? 'checked' : '' ?>>
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
                                                <!-- Open Price mengikuti grade customer (otomatis, tidak diinput manual) -->
                                                <div class="form-check" title="Open Price mengikuti grade customer">
                                                    <input class="form-check-input" 
                                                        type="checkbox" 
                                                        name="isopenprice" 
                                                        id="isopenprice"
                                                        disabled
                                                        <?= in_array(strtoupper(trim((string) ($data['isopenprice'] ?? ''))), ['YES', '1', 'Y', 'T', 'TRUE', 'ON'], true) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="isopenprice">
                                                        Open Price
                                                        <small class="text-muted">(<span id="isopenpriceState"><?= in_array(strtoupper(trim((string) ($data['isopenprice'] ?? ''))), ['YES', '1', 'Y', 'T', 'TRUE', 'ON'], true) ? 'YES' : 'NO' ?></span>)</small>
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
                                <table id="tabsalesorderdtl" class="table table-bordered table-striped sales-dtl-table" style="width:100%;" cellspacing="0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="checkAll">
                                            </th>
                                            <!-- <th>PO</th> -->
                                            <th>ID Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Spec</th>
                                            <th class="so-col-unit">Satuan</th>
                                            <th>Qty</th>
                                            <!-- <th>Bonus Qty</th> -->
                                            <th>Harga</th>
                                            <!-- <th>Vol/Item</th>
                                            <th>Biaya 1</th>
                                            <th>Biaya 2</th> -->
                                            <th>Nilai</th>
                                            <th>ETD</th>
                                            <th>ETA</th>
                                            <th>Gudang</th>
                                            <th style="max-width: 200px;">BOM Description</th>
                                            <!-- <th>Keterangan PP</th> -->
                                            <th class="so-col-aksi">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot id="soInlineFoot">
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Hint format angka detail: desimal pakai titik, maksimal 4 digit -->
                            <div class="so-num-hint">
                                <i class="fa fa-info-circle"></i>
                                Desimal gunakan titik (<b>.</b>), maksimal <b>4</b> angka di belakang koma
                                &mdash; contoh: <code>1.5</code> atau <code>0.0001</code>.
                                Angka bulat cukup ditulis tanpa koma, mis. <code>5</code>.
                                <br>
                                ETD / ETA memakai format <b>dd-mm-yyyy</b> (contoh: <code>25-12-2026</code>)
                                &mdash; klik kolom tanggal untuk memilih lewat kalender. Gudang dipilih dari daftar gudang asal.
                            </div>

                            <!-- Hint shortcut keyboard: tambah item (Alt+N) -->
                            <div class="so-num-hint so-key-hint">
                                <i class="fa fa-bolt"></i>
                                Tambah item: tekan <kbd>Alt</kbd> + <kbd>N</kbd> (atau <kbd>Ctrl</kbd> + <kbd>Q</kbd>),
                                atau klik tombol <b>+</b> di atas tabel &mdash;
                                <kbd>Enter</kbd> menyimpan baris, <kbd>Esc</kbd> membatalkan baris.
                                <br>
                                Edit item: centang baris lalu tekan <kbd>Ctrl</kbd> + <kbd>E</kbd>
                                (atau klik tombol <b>Update</b> / pensil di kolom Aksi) &mdash;
                                baris terbuka langsung di tabel seperti input inline, tanpa modal.
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
                                
                                <!-- <div class="col-md-12">
                                    <div class="form-group text-end">
                                        <button type="button" class="btn btn-lg btn-primary text-white" title="Down Payment">
                                            <i class="fa fa-money"></i> 
                                        </button>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <hr>

                </div>
                <div class="card-footer bg-light">
                    <!-- <a href="<?= base_url('sales/postsales/salesorder') ?>"
                        class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a> -->
                    <a href="<?= base_url('sales/postsales/clearEntrySalesOrder') ?>" 
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




<!-- ================= MODAL TAX DETAIL ================= -->
<!-- CATATAN: modal ini tidak lagi dipakai untuk Update detail.
     Edit detail sekarang INLINE di baris tabel (tombol Update / pensil / Ctrl+E).
     Markup dipertahankan hanya agar referensi id lama tidak error. -->
<div class="modal fade" id="modalDetailSalesOrder" tabindex="-1" role="dialog" aria-labelledby="modalDetailSalesOrderLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDetailSalesOrderLabel">
                    </i> Input Item Detail
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM -->
            <form id="formSalesOrderDetail">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="idurut" id="idurut">
                    <input type="hidden" name="docno" id="docno">
                    <!-- <input type="hidden" name="status" id="status" value="P">-->
                    <input type="hidden" name="uniqueid" id="uniqueid" value="NO">

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ID Barang</label>
                                <select name="idbarang" id="idbarang"
                                        class="form-control select2"></select>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text"
                                        name="nmbarang"
                                        id="nmbarang"
                                        class="form-control"
                                        placeholder="Nama Barang"
                                        style="text-transform:uppercase" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Principal</label>
                                <select name="idprincipal" id="idprincipal"
                                        class="form-control select2"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gudang</label>
                                <select name="idgudang" id="idgudang"
                                        class="form-control select2"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="idspec" class="form-label">
                                Batch / Specification
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <select
                                            name="idspec"
                                            id="idspec"
                                            class="form-control select2"
                                            style="width: 100%;">
                                    </select>
                                </div>
                                <button
                                        type="button"
                                        id="btnNew"
                                        class="btn btn-primary ms-1"
                                        onclick="new_spec()"
                                        title="New Specification">
                                    <i class="fa fa-plus"></i>
                                    New
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- ROW 2 -->
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Satuan</label>
                                <input name="unit" id="unit"
                                        class="form-control select2"
                                        style="width:100%" readonly>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="text"
                                        name="qty"
                                        id="qty"
                                        class="form-control jaccseparator ratakanan"
                                        inputmode="decimal"
                                        title="Gunakan titik (.) untuk desimal, maksimal 4 angka di belakang koma"
                                        placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Harga</label>
                                <input type="text"
                                        name="harga"
                                        id="harga"
                                        class="form-control jaccseparator ratakanan"
                                        inputmode="decimal"
                                        title="Gunakan titik (.) untuk desimal, maksimal 4 angka di belakang koma"
                                        placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Multi Disc (%)</label>
                                <input type="text"
                                        name="multidisc"
                                        id="multidisc"
                                        class="form-control jaccseparator ratakanan"
                                        inputmode="decimal"
                                        title="Gunakan titik (.) untuk desimal, maksimal 4 angka di belakang koma"
                                        placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Nilai</label>
                                <input type="text"
                                        name="nilai"
                                        id="nilai"
                                        class="form-control jaccseparator ratakanan"
                                        readonly
                                        title="Nilai dihitung otomatis (Qty x Harga - Multi Disc)"
                                        placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>BOM Description</label>
                                <textarea type="text"
                                        name="bomdesc"
                                        rows="4"
                                        style="text-transform: uppercase;"
                                        id="bomdesc"
                                        class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    
                    <button type="button"
                            class="btn btn-primary"
                            onclick="saveSalesOrderDetail()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="$('#modalDetailSalesOrder').modal('hide')">
                        <i class="fa fa-times"></i> Batal
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>


<!-- ============================================= -->
<!-- MODAL NEW BATCH / SPEC -->
<!-- ============================================= -->
<div class="modal fade" id="modalNewSpec" tabindex="-1" aria-labelledby="modalNewSpecLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalNewSpecLabel">New Batch / Specification</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <div class="mb-3">
                    <label for="newbatch" class="form-label">Batch / Specification</label>
                    <input type="text" class="form-control" id="newbatch" name="newbatch"
                           autocomplete="off" style="text-transform: uppercase;">
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="save_new_spec()">
                    <i class="fa fa-save"></i> Process
                </button>
            </div>

        </div>
    </div>
</div>


</div>

<style type="text/css">
    /* Baris input detail inline pada tabel Detail Barang (pola grid PNM / PMK Barang) */
    #tabsalesorderdtl tfoot tr.so-inline-row td {
        padding: 4px 3px;
        vertical-align: middle;
        background-color: #fff8e1;
    }

    #tabsalesorderdtl tfoot tr.so-inline-row .form-control-sm {
        font-size: 11px;
        padding: 2px 4px;
        min-width: 62px;
        height: 28px !important;
        min-height: 28px !important;
    }

    /* Dropdown satuan (native select): font diperkecil agar teks satuan
       tetap terlihat utuh pada kolom Satuan yang sempit */
    #tabsalesorderdtl tfoot tr.so-inline-row select.so-inline-unit {
        min-width: 60px;
        width: 100% !important;
        font-size: 11px !important;
        padding: 1px 16px 1px 3px !important;
        line-height: 1.2;
        background-position: right 2px center !important;
        background-size: 10px 8px;
        text-overflow: ellipsis;
    }

    /* Kolom Satuan diberi lebar minimum agar dropdown satuan tidak
       menyempit sampai teksnya tidak terlihat */
    #tabsalesorderdtl thead th.so-col-unit,
    #tabsalesorderdtl tbody td.so-col-unit-cell {
        width: 72px !important;
        min-width: 72px !important;
    }

    #tabsalesorderdtl tfoot tr.so-inline-row .select2-container {
        min-width: 96px;
    }

    /* Input tanggal (ETD / ETA) pada baris inline: format dd-mm-yyyy */
    #tabsalesorderdtl tfoot tr.so-inline-row input.so-inline-date {
        min-width: 96px;
        text-align: center;
    }

    /* Gudang pada baris inline */
    #tabsalesorderdtl tfoot tr.so-inline-row select.so-inline-gudang {
        min-width: 130px;
    }

    /* Select2 dalam baris input dibuat ringkas (pola grid inline PNM / PMK) */
    #tabsalesorderdtl tfoot tr.so-inline-row .select2-container .select2-selection--single {
        height: 28px !important;
        min-height: 28px !important;
    }

    #tabsalesorderdtl tfoot tr.so-inline-row .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 5px !important;
        padding-right: 18px !important;
        line-height: 26px !important;
        font-size: 11px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #tabsalesorderdtl tfoot tr.so-inline-row .select2-container .select2-selection--single .select2-selection__arrow {
        top: 1px !important;
        height: 26px !important;
        right: 1px !important;
        width: 16px !important;
    }

    /* Empty state detail barang (pola grid inline PNM / PMK) */
    #tabsalesorderdtl tbody td.dataTables_empty {
        padding: 16px 10px !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        background: #ffffff !important;
        text-align: center;
        cursor: pointer;
    }

    #tabsalesorderdtl tbody td.dataTables_empty:hover {
        background: #eef6ff !important;
        color: #1e3a5f !important;
    }

    #tabsalesorderdtl tbody td.dataTables_empty i {
        margin-right: 6px;
        font-size: 15px;
        color: #1e3a5f;
    }

    /* Pesan empty state disembunyikan selama baris input inline terbuka */
    #tabsalesorderdtl.so-has-draft tbody td.dataTables_empty {
        display: none !important;
    }

    #tabsalesorderdtl tfoot tr.so-inline-row.table-info td {
        background-color: #d1ecf1;
    }

    /* Kolom Aksi: letak tombol disamakan dengan grid inline PNM / PMK Barang
       (hanya tombol update + hapus; tombol urutan pindah ke kotak pencarian) */
    #tabsalesorderdtl thead th.so-col-aksi,
    #tabsalesorderdtl tbody td.so-col-aksi-cell,
    #tabsalesorderdtl tfoot td.so-col-aksi {
        width: 76px !important;
        min-width: 76px !important;
        text-align: center !important;
        white-space: nowrap !important;
        padding-left: 4px !important;
        padding-right: 4px !important;
    }

    /* Tombol urutan ▲ / ▼ di kiri kotak pencarian DataTables */
    .dataTables_filter.so-dt-filter {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .so-dt-move {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        vertical-align: middle;
    }

    .so-dt-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0;
        font-size: 12px;
        line-height: 1;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background .12s ease, transform .12s ease;
    }

    .so-dt-btn:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(15, 23, 42, .22);
    }

    .so-dt-btn-up {
        background: #0284c7;
    }

    .so-dt-btn-down {
        background: #0f766e;
    }

    #tabsalesorderdtl tfoot td.so-col-no {
        width: 30px !important;
        min-width: 30px !important;
        text-align: center !important;
        padding-left: 2px !important;
        padding-right: 2px !important;
    }

    /* Tombol aksi kecil di dalam baris (draft inline & baris tersimpan) */
    #tabsalesorderdtl tfoot tr.so-inline-row .so-inline-btn,
    #tabsalesorderdtl tbody .so-row-btn {
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

    #tabsalesorderdtl tfoot tr.so-inline-row .so-inline-btn:hover,
    #tabsalesorderdtl tbody .so-row-btn:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(15, 23, 42, .22);
    }

    #tabsalesorderdtl tfoot tr.so-inline-row .so-inline-save {
        background: #16a34a;
    }

    #tabsalesorderdtl tfoot tr.so-inline-row .so-inline-cancel {
        background: #64748b;
    }

    #tabsalesorderdtl tbody .so-row-btn-edit {
        background: #d97706;
    }

    #tabsalesorderdtl tbody .so-row-btn-delete {
        background: #dc2626;
    }

    /* Hint format angka pada grid detail (desimal titik, maksimal 4 digit) */
    .so-num-hint {
        margin-top: 6px;
        font-size: 11.5px;
        line-height: 1.5;
        color: #64748b;
    }

    .so-num-hint i {
        margin-right: 4px;
        color: #1e3a5f;
    }

    .so-num-hint b {
        color: #1e3a5f;
    }

    .so-num-hint code {
        padding: 0 3px;
        font-size: 11px;
        color: #1e3a5f;
        background: #eef6ff;
        border-radius: 3px;
    }

    /* Hint shortcut keyboard di bawah grid detail (Alt+N untuk tambah item) */
    .so-key-hint {
        margin-top: 4px;
        padding: 5px 8px;
        font-size: 11.5px;
        line-height: 1.6;
        color: #475569;
        background: #f1f5f9;
        border-left: 3px solid #0284c7;
        border-radius: 4px;
    }

    .so-key-hint i {
        margin-right: 5px;
        color: #0284c7;
    }

    .so-key-hint kbd {
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
    /* --- Select2: teks (kode idbarang / idgudang) bisa diseleksi & dicopy Ctrl+C --- */
    .select2-container .select2-selection,
    .select2-container .select2-selection__rendered,
    .select2-container .select2-selection__choice,
    .select2-results__option,
    .select2-results__option .select2-result-repository__description,
    .select2-results__option .so-select2-copy,
    .select2-results__option span {
        -webkit-user-select: text !important;
        -moz-user-select: text !important;
        -ms-user-select: text !important;
        user-select: text !important;
    }

    /* satu klik pada field terpilih langsung menyeleksi seluruh kode */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        -webkit-user-select: all !important;
        user-select: all !important;
        cursor: text;
    }

    .select2-results__option .so-select2-copy {
        cursor: text;
    }
</style>

<?php
/* Cache-busting JS detail Sales Order (agar perubahan grid inline langsung terpakai) */
$soJsRel = 'assets/pagejs/sales/postsales/salesorder/salesorder.js';
$soJsAbs = (defined('FCPATH') ? FCPATH : '') . $soJsRel;
$soJsVer = is_file($soJsAbs) ? filemtime($soJsAbs) : date('YmdHis');

/* Cache-busting autofill cabang / job (mengikuti session user) */
$soBranchJsRel = 'assets/pagejs/sales/postsales/sales_branch_autofill.js';
$soBranchJsAbs = (defined('FCPATH') ? FCPATH : '') . $soBranchJsRel;
$soBranchJsVer = is_file($soBranchJsAbs) ? filemtime($soBranchJsAbs) : date('YmdHis');
?>
<script type="application/javascript" src="<?= base_url($soJsRel) ?>?v=<?= $soJsVer ?>"></script>
<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/postsales/sales_shortcut.js') ?>"></script>
<script type="application/javascript" src="<?= base_url($soBranchJsRel) ?>?v=<?= $soBranchJsVer ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
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
            // TAMBAHKAN update kurs di sini
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
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

       // Event change untuk input manual + validasi
        $('#docdate').on('change', function() {
            var docdate = $(this).val();
            var currcode = $('[name="currcode"]').val();
            
            // Validasi docdate
            if (docdate && !validateDocdate(docdate)) {
                // Gunakan logindate atau nilai yang sudah ada di datepicker sebagai default
                var defaultDate = $(this).data('last-valid');
                if (!defaultDate) {
                    // Ambil dari datepicker jika ada
                    var drp = $(this).data('daterangepicker');
                    if (drp) {
                        defaultDate = drp.startDate.format('DD-MM-YYYY');
                    } else {
                        // Fallback ke logindate dari response
                        defaultDate = $('#logindate_hidden').val() || moment().format('DD-MM-YYYY');
                    }
                }
                $(this).val(defaultDate);
                return;
            }
            
            // Simpan nilai valid
            if (docdate) {
                $(this).data('last-valid', docdate);
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

        // Fungsi validasi docdate
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



        $('#delivdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#delivdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'delivdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'delivdate');
        });
        $('#delivdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        <?php if (($typeform ?? '') === 'INPUT'): ?>
        // Tanggal Pengiriman otomatis terisi tanggal hari ini (dd-mm-yyyy)
        if (!$('#delivdate').val()) {
            var todayDeliv = moment();
            var drpDeliv = $('#delivdate').data('daterangepicker');
            if (drpDeliv) {
                drpDeliv.setStartDate(todayDeliv);
                drpDeliv.setEndDate(todayDeliv);
            }
            $('#delivdate').val(todayDeliv.format('DD-MM-YYYY'));
        }
        <?php endif; ?>


        
        $('#periodemulai').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodemulai').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodemulai').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        
        $('#periodeakhir').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodeakhir').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodeakhir').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        

    });

</script>