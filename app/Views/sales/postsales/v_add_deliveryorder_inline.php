<?= $this->include('sales/postsales/_corp_theme') ?>
<?php
    /* =====================================================
     * KEBIJAKAN HARGA
     * Form Delivery Order dipakai bagian gudang, sehingga informasi
     * harga tidak boleh tampil: kolom Harga / Hrg Satuan / Disc % /
     * Nilai pada grid, ringkasan DPP / Pajak / Total, serta harga pada
     * modal Tarik SO (Total SO, Harga detail, nilai outstanding).
     * Set $hidePrice = false bila harga perlu ditampilkan kembali.
     * ===================================================== */
    $hidePrice = true;
?>
<div class="corp-form-container<?= $hidePrice ? ' do-no-price' : '' ?>">

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
    /* =====================================================
     * AUTOFILL INPUT AWAL CABANG / JOB (sama dengan addSalesOrder)
     * Cabang mengikuti session user login.
     * Hanya PT JATIM TAMAN STEEL yang boleh memilih bebas.
     * ===================================================== */
    $sessionBranchId   = trim((string) session()->get('idbranchjob'));
    $sessionBranchName = strtoupper(trim((string) session()->get('nmbranchjob')));
    $sessionBranchKey  = preg_replace('/[^A-Z0-9]/', '', $sessionBranchName);

    $isPlantBranch      = strpos($sessionBranchKey, 'PLANT') !== false
                        || strpos($sessionBranchKey, 'PLAN1') !== false
                        || strpos($sessionBranchKey, 'PLAN2') !== false;

    $isHeadOfficeBranch = ($sessionBranchId === '01')
                        || strpos($sessionBranchKey, 'JATIMTAMANSTEEL') !== false;

    $canChooseBranch    = $isHeadOfficeBranch && !$isPlantBranch;

    $typeform  = $typeform ?? 'INPUT';
    $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail');
    $openPrice = in_array(strtoupper(trim((string) ($dtldata['isopenprice'] ?? ''))), ['YES', '1', 'Y', 'T', 'TRUE', 'ON'], true);
?>
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('sales/postsales/finalEntryDeliveryOrder') ?>" method="post" id="formDO">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?= $typeTitle ?> Delivery Order</h3>
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
                                        <div class="col-md-12">
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
                                                <label>No. Jurnal Delivery Order</label>

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
                                            <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="30" value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>" style="text-transform: uppercase;" readonly>

                                    </div>

                                    <!-- No. Sales Order (terisi otomatis saat Tarik SO) -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="docnoso">No. Sales Order</label>
                                                <input name="docnoso"
                                                    id="docnoso"
                                                    class="form-control"
                                                    type="text"
                                                    maxlength="30"
                                                    placeholder="Tarik dari Sales Order"
                                                    readonly
                                                    style="text-transform:uppercase;">
                                                <input type="hidden" name="idrev" id="idrev">
                                                <input type="hidden" id="nmcustomer">
                                                <input type="hidden" id="gradecustomer">
                                                <input type="hidden" id="nmcustomerdeliv">
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="pemohon">Pemohon</label>
                                                <input type="text"
                                                    name="pemohon"
                                                    id="pemohon"
                                                    class="form-control"
                                                    maxlength="100"
                                                    placeholder="Pemohon"
                                                    style="text-transform:uppercase;">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- CUSTOMER COLUMN -->
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
                                                <label for="kdcustomerdeliv">Kirim Ke</label>
                                                <select name="kdcustomerdeliv" id="kdcustomerdeliv" class="form-control select2"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatcustomerdeliv">Alamat Kirim</label>
                                                <textarea name="alamatcustomerdeliv"
                                                    id="alamatcustomerdeliv"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Kirim"
                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- PAJAK / PEMBAYARAN COLUMN -->
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="currcode" class="form-label">Mata Uang</label>
                                                <select name="currcode"
                                                        id="currcode"
                                                        class="form-select select2"
                                                        required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="kurs" class="form-label">Kurs</label>
                                                <input name="kurs"
                                                    id="kurs"
                                                    class="form-control ratakanan jaccseparator"
                                                    type="text"
                                                    placeholder="1.00">
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
                                                        id="isinclusive">
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
                                                <div class="form-check" title="Open Price mengikuti grade customer">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        name="isopenprice"
                                                        id="isopenprice"
                                                        disabled
                                                        <?= $openPrice ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="isopenprice">
                                                        Open Price
                                                        <small class="text-muted">(<span id="isopenpriceState"><?= $openPrice ? 'YES' : 'NO' ?></span>)</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
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


                            </div>

                        </div>
                </div>
            </div>

            <div class="card mt-3 card-primary">
                <div class="card-header clearfix">
                    <h3 class="card-title">
                        <i class="fa fa-list-alt"></i> Detail Barang
                        <button type="button"
                                class="btn btn-info do-tarik-so"
                                data-bs-toggle="tooltip"
                                title="Tarik sebagian/seluruh detail dari Sales Order"
                                id="btnTarikSO">
                            <i class="fa fa-download"></i> Tarik SO
                        </button>
                    </h3>

                    <div class="header-actions">
                        <span class="so-dt-move">
                            <button type="button"
                                    class="so-dt-btn so-dt-btn-up"
                                    data-bs-toggle="tooltip"
                                    title="Naikkan urutan baris terpilih (centang 1 baris)"
                                    id="btnMoveUp">
                                <i class="fa fa-arrow-up"></i>
                            </button>
                            <button type="button"
                                    class="so-dt-btn so-dt-btn-down"
                                    data-bs-toggle="tooltip"
                                    title="Turunkan urutan baris terpilih (centang 1 baris)"
                                    id="btnMoveDown">
                                <i class="fa fa-arrow-down"></i>
                            </button>
                        </span>

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
                                <table id="tabdeliveryorderdtl" class="table table-bordered table-striped sales-dtl-table" style="width:100%;" cellspacing="0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="checkAll">
                                            </th>
                                            <th>Item SO</th>
                                            <th>Item DO</th>
                                            <th>Spec</th>
                                            <th class="so-col-unit">Satuan</th>
                                            <th>Qty SO</th>
                                            <th>Qty</th>
                                            <th class="do-col-price">Harga</th>
                                            <th class="do-col-price">Hrg Satuan</th>
                                            <th class="do-col-price">Disc %</th>
                                            <th class="do-col-price">Nilai</th>
                                            <th class="so-col-stok" title="Stok akhir sc_trx.stkblc sampai tanggal DO">Stok</th>
                                            <th>Gudang</th>
                                            <th style="max-width: 200px;">Keterangan</th>
                                            <th class="so-col-aksi">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot id="doInlineFoot">
                                    </tfoot>
                                </table>
                            </div>


                            <!-- Hint format angka detail (sama dengan addSalesOrder) -->
                            <div class="so-num-hint">
                                <i class="fa fa-info-circle"></i>
                                Desimal gunakan titik (<b>.</b>), maksimal <b>4</b> angka di belakang koma
                                &mdash; contoh: <code>1.5</code> atau <code>0.0001</code>.
                                Angka bulat cukup ditulis tanpa koma, mis. <code>5</code>.
                                <br>
                                Spec / Batch dan Gudang dipilih dari daftar &mdash; stok akhir dihitung otomatis
                                dari <b>sc_trx.stkblc</b> sampai <b>tanggal DO</b>.
                                Tanda <span class="do-stok-warn" style="vertical-align:middle;">!</span>
                                berarti stok kosong / qty melebihi stok (baris tersebut hanya boleh <b>Simpan Draft</b>).
                            </div>

                            <!-- Hint shortcut keyboard: tambah item (Alt+N) -->
                            <div class="so-num-hint so-key-hint">
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
                            </div>
                        </div>

                        <!-- SUMMARY (harga: disembunyikan untuk gudang) -->
                        <div class="col-md-6 do-price-summary">
                            <div class="row">
                                <div class="col-md-6 text-end">
                                    <label for="doDpp" class="mt-2">DPP</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text"
                                        id="doDpp"
                                        style="font-weight: bold;"
                                        class="form-control jaccseparator ratakanan"
                                        readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 text-end">
                                    <label for="doPajak" class="mt-2">Pajak</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text"
                                        id="doPajak"
                                        style="font-weight: bold;"
                                        class="form-control jaccseparator ratakanan"
                                        readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 text-end">
                                    <label for="doTotal" class="mt-2">Total</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text"
                                        id="doTotal"
                                        style="font-weight: bold;"
                                        class="form-control jaccseparator ratakanan"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                </div>
                <div class="card-footer bg-light">
                    <a href="<?= base_url('sales/postsales/clearEntryDeliveryOrder') ?>"
                        onclick="return confirm('Are you sure clear this entry?')"
                        class="btn btn-default float-left">
                        <i class="fa fa-arrow-left"></i>
                        Kembali
                    </a>
                    <?php if ($typeform != 'DETAIL'): ?>
                        <button type="submit"
                                id="btnFinalizeDO"
                                class="btn btn-success btn-lg float-right">
                            <i class="fa fa-save"></i> Simpan Final Data
                        </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-primary btn-lg float-right me-2" id="btnSimpanDO">
                        <i class="fa fa-save"></i> Simpan Draft
                    </button>
                </div>

            </div>
        </div>
    </form>
        <!-- /.card -->
    <!--/.col (left) -->
</div>



<!-- ================= MODAL TARIK SALES ORDER ================= -->
<div class="modal fade" id="modalTarikSO" tabindex="-1" role="dialog" aria-labelledby="modalTarikSOLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalTarikSOLabel">
                    <i class="fa fa-download"></i> Tarik Sales Order
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <div class="row g-1 mb-1">
                    <div class="col-8">
                        <input type="text" id="soSearch" class="form-control form-control-sm" placeholder="Cari No. SO / Customer...">
                    </div>
                    <div class="col-4 text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnSearchSO">
                            <i class="fa fa-search"></i> Cari
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCollapseSO">
                            <i class="fa fa-compress"></i> Tutup
                        </button>
                    </div>
                </div>

                <div class="so-pick-wrap">
                    <div class="so-pick-loading-overlay" id="soPickLoading">
                        <div class="so-pick-loading-box">
                            <i class="fa fa-spinner fa-spin"></i>
                            <span id="soPickLoadingText">Memuat daftar Sales Order...</span>
                        </div>
                    </div>
                    <table class="table table-sm table-bordered so-pick-table mb-0" id="tblSOList" cellspacing="0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th width="22"></th>
                                <th width="24"><input type="checkbox" id="soChkAll" title="Centang semua detail"></th>
                                <th>No. SO</th>
                                <th width="82">Tanggal</th>
                                <th>Customer</th>
                                <th width="108" class="text-end do-col-price">Total</th>
                                <th width="112" class="text-end">Outstanding</th>
                                <th width="58">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="8" class="so-pick-empty"><i class="fa fa-spinner fa-spin"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="so-pick-note">
                    Hanya SO <b>outstanding</b> yang tampil (masih ada sisa <i>qty</i> yang belum di-DO);
                    kolom <b>Outstanding</b> = sisa qty &amp; jumlah item.
                    Klik baris SO untuk membuka detailnya (detail tampil <b>menjorok</b> di bawah SO induknya),
                    lalu centang detail yang mau ditarik (boleh sebagian).
                    <b>Satu DO hanya untuk satu SO.</b>
                    Item yang sudah ditarik / sudah full DO otomatis tidak bisa dicentang.
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-success btn-lg" id="btnPullSO">
                    <i class="fa fa-download"></i> Tarik ke DO (<span id="soPickCount">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>

</div><!-- /corp-form-container -->

<?= $this->include('sales/postsales/_deliveryorder_grid_style') ?>

<style type="text/css">
    /* Tombol Tarik SO: menempel di kanan judul card "Detail Barang" (diperbesar) */
    .corp-form-container .card-header h3.card-title .do-tarik-so {
        margin-left: 12px;
        padding: 6px 16px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.35;
        border-radius: 4px;
    }

    /* Baris edit in-place (tbody): tampilan sama dengan baris input di footer */
    #tabdeliveryorderdtl tbody tr.so-inline-edit > td {
        padding: 4px 3px;
        vertical-align: middle;
        background-color: #fff8e1;
    }

    #tabdeliveryorderdtl tbody tr.so-inline-edit .form-control-sm {
        font-size: 11px;
        padding: 2px 4px;
        min-width: 62px;
        height: 28px !important;
        min-height: 28px !important;
    }

    #tabdeliveryorderdtl tbody tr.so-inline-edit select.so-inline-unit {
        min-width: 60px;
        width: 100% !important;
        font-size: 11px !important;
        padding: 1px 16px 1px 3px !important;
        line-height: 1.2;
        text-overflow: ellipsis;
    }

    #tabdeliveryorderdtl tbody tr.so-inline-edit select.so-inline-gudang { min-width: 130px; }
    #tabdeliveryorderdtl tbody tr.so-inline-edit .select2-container { min-width: 96px; }

    #tabdeliveryorderdtl tbody tr.so-inline-edit .select2-container .select2-selection--single {
        height: 28px !important;
        min-height: 28px !important;
    }

    #tabdeliveryorderdtl tbody tr.so-inline-edit .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 5px !important;
        padding-right: 18px !important;
        line-height: 26px !important;
        font-size: 11px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #tabdeliveryorderdtl tbody tr.so-inline-edit .select2-container .select2-selection--single .select2-selection__arrow {
        top: 1px !important;
        height: 26px !important;
        right: 1px !important;
        width: 16px !important;
    }

    #tabdeliveryorderdtl tbody tr.so-inline-edit .so-inline-btn {
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
    }

    #tabdeliveryorderdtl tbody tr.so-inline-edit .so-inline-save { background: #16a34a; }
    #tabdeliveryorderdtl tbody tr.so-inline-edit .so-inline-cancel { background: #64748b; }

    /* ===== Modal Tarik SO: parent-child SO header/detail, compact ===== */
    #modalTarikSO .modal-body { padding: 8px 10px 4px 10px; }
    #modalTarikSO .modal-footer { padding: 6px 10px; }

    #modalTarikSO .so-pick-wrap {
        position: relative;
        max-height: 56vh;
        overflow: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    #modalTarikSO .so-pick-table { font-size: 11.5px; margin-bottom: 0; }
    #modalTarikSO .so-pick-table > thead > tr > th {
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
    #modalTarikSO .so-pick-table > tbody > tr > td {
        padding: 2px 6px;
        vertical-align: middle;
    }

    /* Kolom Outstanding (sisa qty & sisa nilai) */
    #modalTarikSO .so-pick-sisa {
        display: block;
        font-weight: 600;
        color: #0b5ed7;
        white-space: nowrap;
    }
    #modalTarikSO .so-pick-sisaq {
        display: block;
        font-size: 10px;
        color: #6c757d;
        white-space: nowrap;
    }
    #modalTarikSO tr.so-pick-parent.so-pick-done > td { opacity: .65; }

    #modalTarikSO tr.so-pick-parent { cursor: pointer; }
    #modalTarikSO tr.so-pick-parent:hover > td { background-color: #eef5ff; }
    #modalTarikSO tr.so-pick-parent.so-open > td { background-color: #dbe9fb; font-weight: 600; }
    #modalTarikSO .so-pick-toggle { color: #0d6efd; text-align: center; }
    #modalTarikSO .so-pick-col-no { font-family: Consolas, monospace; }
    #modalTarikSO .so-pick-col-cust { text-transform: uppercase; }

    /* Detail SO = anak dari header SO: menjorok ke dalam + garis penghubung */
    #modalTarikSO tr.so-pick-child > td.so-pick-child-cell {
        padding: 2px 8px 6px 34px;
        background-color: #f8fafc;
        border-top: 0;
        border-left: 3px solid #0d6efd;
    }

    #modalTarikSO .so-pick-dtl-wrap {
        padding: 0 0 0 10px;
        background: #ffffff;
        border-left: 2px dashed #c7d7f3;
        border-radius: 0 3px 3px 0;
    }

    #modalTarikSO .so-pick-dtl-title {
        padding: 3px 4px 2px 4px;
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: .02em;
        text-transform: uppercase;
        color: #33475b;
    }

    #modalTarikSO .so-pick-dtl-title i {
        margin-right: 4px;
        color: #0d6efd;
    }

    #modalTarikSO .so-pick-tree {
        margin-right: 4px;
        color: #9db6d8;
        font-size: 11px;
    }

    /* Loading modal: overlay di dalam area daftar SO (tidak menggeser baris) */
    #modalTarikSO .so-pick-loading-overlay {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .72);
        z-index: 3;
    }

    #modalTarikSO .so-pick-loading-overlay.show {
        display: flex;
    }

    #modalTarikSO .so-pick-loading-box {
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #33475b;
        background: #ffffff;
        border: 1px solid #cfe2ff;
        border-radius: 6px;
        box-shadow: 0 4px 14px rgba(13, 110, 253, .12);
    }

    #modalTarikSO .so-pick-loading-box i {
        margin-right: 6px;
        color: #0d6efd;
    }

    #modalTarikSO .so-pick-empty {
        padding: 10px 8px;
        font-size: 11.5px;
        color: #6c757d;
        text-align: center;
    }

    #modalTarikSO .so-pick-empty i {
        margin-right: 6px;
        color: #0d6efd;
    }

    #modalTarikSO .so-pick-dtl { font-size: 11px; background-color: #fff; margin-bottom: 2px; }
    #modalTarikSO .so-pick-dtl > thead > tr > th {
        padding: 2px 5px;
        font-size: 10.5px;
        font-weight: 600;
        background-color: #eef2f8;
        color: #33475b;
        white-space: nowrap;
        border-bottom: 1px solid #dee2e6;
    }
    #modalTarikSO .so-pick-dtl > tbody > tr > td { padding: 1px 5px; vertical-align: middle; }
    #modalTarikSO .so-pick-dtl > tbody > tr:hover > td { background-color: #fffbe6; }
    #modalTarikSO .so-pick-dtl tr.so-pick-dtl-done > td { color: #a3aab5; background-color: #f7f7f7; }
    #modalTarikSO .so-pick-item { font-family: Consolas, monospace; color: #0b5ed7; }

    #modalTarikSO .so-pick-note {
        margin-top: 4px;
        font-size: 11px;
        line-height: 1.35;
        color: #6c757d;
    }

    #modalTarikSO .so-pick-loading { padding: 2px 4px; font-size: 11px; }

    #modalTarikSO input[type="checkbox"] { margin: 0; vertical-align: middle; cursor: pointer; }
</style>

<?php
/* Cache-busting JS add Delivery Order (grid inline mengikuti addSalesOrder) */
$doJsRel = 'assets/pagejs/sales/postsales/deliveryorder/deliveryorder_inline.js';
$doJsAbs = (defined('FCPATH') ? FCPATH : '') . $doJsRel;
$doJsVer = is_file($doJsAbs) ? filemtime($doJsAbs) : time();
?>
<script type="text/javascript">
    window.DO_ADD = {
        existingDocno: '<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno']), 'js') : '' ?>',
        openPrice: '<?= $openPrice ? 'true' : 'false' ?>',
        finalizeUrl: '<?= base_url('sales/postsales/finalEntryDeliveryOrder') ?>'
    };
</script>
<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/postsales/sales_shortcut.js') ?>"></script>
<script type="application/javascript" src="<?= base_url($doJsRel) ?>?v=<?= $doJsVer ?>"></script>

