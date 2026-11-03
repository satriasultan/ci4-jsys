<style>

    .section-block {
        background-color: #e8e8e8;
        border-left: 4px solid #007bff;
        /* border-bottom: 4px solid #007bff;  */
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.4s ease,
                    border-color 0.4s ease;

        transform: scale(1);
        will-change: transform;
    }

    .section-header {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 20px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 8px;
    }

    .section-block:hover {
        /* transform: scale(1.02); */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-left-color: #0056b3;
        border-bottom-color: #0056b3;
    }

    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
    }

    .form-control:disabled, .form-control[readonly] {
        background-color: #dfdfdf;
        opacity: 1;
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(
            to right,
            #007bff 0%,
            #cfe2ff 30%,
            #e8e8e8 100%
        );
        margin: 24px 0;
        border: none;
    }


    .date-options-container {
        padding: 10px 0;
    }

    .date-options-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .date-option {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .date-option:hover {
        background-color: #f0f0f0;
    }

    .date-option.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .date-option .option-label {
        font-size: 14px;
    }


</style>
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

<?php echo $message;?>
<?php if (session()->getFlashdata('error')) : ?>

    <div class="alert alert-danger">

        <?= session()->getFlashdata('error') ?>

    </div>

<?php endif; ?>
<?php
    $isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
    $disabled = $isIT ? '' : 'disabled';

    /* =====================================================
     * VALIDASI INPUT AWAL CABANG / JOB
     * Cabang mengikuti session user login.
     * Hanya PT JATIM TAMAN STEEL yang boleh memilih bebas.
     * ===================================================== */
    $sessionBranchId   = trim((string) session()->get('idbranchjob'));
    $sessionBranchName = strtoupper(trim((string) session()->get('nmbranchjob')));

    // Normalisasi agar variasi penulisan (PT / PT. / spasi ganda) tetap dikenali
    $sessionBranchKey = preg_replace('/[^A-Z0-9]/', '', $sessionBranchName);
    $canChooseBranch  = $sessionBranchKey !== ''
        && strpos($sessionBranchKey, 'PTJATIMTAMANSTEEL') === 0;
?>
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('purchase/trans/finalEntryPP') ?>" method="post" id="formPP">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Permintaan Pembelian</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Permintaan Pembelian
                            </div>
                            <div class="row">

                                <!-- LEFT COLUMN -->
                                <div class="col-md-6">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cabang">Cabang / Job <span style="color: red;">*</span></label>
                                                <select name="cabang" id="cabang" class="form-control" required
                                                    data-session-branch-id="<?= esc($sessionBranchId) ?>"
                                                    data-session-branch-name="<?= esc($sessionBranchName) ?>"
                                                    data-can-choose-branch="<?= $canChooseBranch ? '1' : '0' ?>"></select>
                                            </div>
                                        </div>


                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No. Jurnal <span style="color: red;">*</span></label>

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
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="docdate">Tanggal <span style="color: red;">*</span></label>
                                                    <input type="text"
                                                    name="docdate"
                                                    id="docdate"
                                                    class="form-control"
                                                    placeholder="Tanggal">
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="estpakai">Estimasi Tanggal Pakai <span style="color: red;">*</span></label>
                                                <input type="text"
                                                    name="estpakai"
                                                    id="estpakai"
                                                    class="form-control"
                                                    placeholder="Estimasi Tanggal Pakai">
                                            </div>
                                        </div>
                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label for="pemohon">Pemohon <span style="color: red;">*</span></label>
                                                <input type="text"
                                                    name="pemohon"
                                                    id="pemohon"
                                                    class="form-control"
                                                    maxlength="50"
                                                    placeholder="Pemohon"
                                                    readonly
                                                    value="<?= esc(strtoupper($userlogin)) ?>">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- RIGHT COLUMN -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan <span style="color: red;">*</span></label>
                                        <textarea name="keterangan"
                                                id="keterangan"
                                                class="form-control"
                                                rows="10"
                                                placeholder="Catatan / Informasi..."
                                                style="text-transform:uppercase;"></textarea>
                                    </div>
                                </div>

                            </div>

                        </div>
                </div>
            </div>

            <div class="card mt-3 card-primary">
                <div class="card-header clearfix">
                    <h3 class="card-title">
                        Detail Barang
                    </h3>

                    <div class="float-right d-flex align-items-center gap-2">
                        <button type="button"
                                class="btn btn-success btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Input Data"
                                onclick="btnInputDetail()">
                            <i class="fa fa-plus"></i>
                        </button>

                        <button type="button"
                                class="btn btn-warning btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Update Data"
                                onclick="btnUpdateDetail()">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button type="button"
                                class="btn btn-danger btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Hapus Data"
                                onclick="btnDeleteDetail()">
                            <i class="fa fa-trash"></i>
                        </button>


                    </div>
                </div>
                <div class="card-body">
                    <table id="tabppdtl" class="table table-bordered table-striped">
                        <thead class="bg-primary text-white">
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>ID Barang</th>
                            <th>Nama Barang</th>
                            <th>Spesifikasi</th>
                            <th>No. Capex</th>
                            <!--Status Detail Tidak Perlu Dimunculkan-->
                            <!--<th>Status</th>-->
                            <th>Satuan</th>
                            <th>Quantity</th>
                            <th>Keterangan</th>
                        </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    <!-- <a href="<?= base_url('purchase/trans/pp') ?>"
                        class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a> -->
                    <a href="<?= base_url('purchase/trans/clearEntryPP') ?>"
                        onclick="return confirm('Are you sure clear this entry?')"
                        class="btn btn-default float-left">
                        <i class="fa fa-arrow-left"></i>
                        Back
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
<div class="modal fade" id="modalDetailPP" tabindex="-1" role="dialog" aria-labelledby="modalDetailPPLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDetailPPLabel">
                    </i> Input Item Detail
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM -->
            <form id="formPPDetail">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="idurut" id="idurut">
                    <input type="hidden" name="docno" id="docno">
                    <input type="hidden" name="unit_default"     id="unit_default">
                    <!-- <input type="hidden" name="status" id="status" value="P">
                    <input type="hidden" name="chold" id="chold" value="NO"> -->

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ID Barang <span style="color: red;">*</span></label>
                                <select name="idbarang" id="idbarang"
                                        class="form-control select2"
                                        style="width:100%" required></select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nama Barang <span style="color: red;">*</span></label>
                                <input type="text"
                                        name="nmbarang"
                                        id="nmbarang"
                                        class="form-control"
                                        placeholder="Nama Barang"
                                        required
                                        style="text-transform:uppercase" readonly>
                            </div>
                        </div>
                        <!-- SPEC -->
                        <div class="col-md-4">

                            <label for="batch" class="form-label">
                                Batch / Specification
                            </label>

                            <div class="d-flex align-items-center">

                                <div class="flex-grow-1">

                                    <select
                                            name="batch"
                                            id="batch"
                                            class="form-control select2"
                                            style="width: 100%;"
                                    >
                                    </select>

                                </div>

                                <button
                                        type="button"
                                        id="btnNew"
                                        class="btn btn-primary ms-1"
                                        onclick="new_spec()"
                                        title="New Specification"
                                >
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
                                <label>Quantity <span style="color: red;">*</span></label>
                                <input type="text"
                                       name="qty"
                                       id="qty"
                                       required
                                       class="form-control jtsseparator ratakanan"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Satuan</label>
                            <select name="unit" id="unit"
                                    class="form-control"
                                    style="width:100%"></select>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Informasi Stock</label>
                                <input type="text"
                                       id="qtystock"
                                       class="form-control jtsseparator ratakanan"
                                       placeholder="0.00"
                                       readonly>
                                <input type="hidden"
                                       name="qtystock"
                                       id="qtystock_raw"
                                       value="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>No. Capex</label>
                                <input type="text"
                                        name="capexno"
                                        id="capexno"
                                        maxlength="30"
                                        class="form-control"
                                        style="text-transform: uppercase;"
                                        placeholder="No. Capex Jika Ada">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Keterangan<span style="color: red;">*</span></label>
                                <textarea type="text"
                                        name="description"
                                        rows="4"
                                        style="text-transform: uppercase;"
                                        id="description"
                                        required
                                        class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">

                    <button type="button"
                        class="btn btn-primary"
                        onclick="savePPDetail()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="$('#modalDetailPP').modal('hide')">
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

<div
        class="modal fade"
        id="modalNewSpec"
        tabindex="-1"
        aria-labelledby="modalNewSpecLabel"
        aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">

                <h5
                        class="modal-title"
                        id="modalNewSpecLabel"
                >
                    New Batch / Specification
                </h5>

                <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                ></button>

            </div>


            <!-- BODY -->
            <div class="modal-body">

                <div class="mb-3">

                    <label
                            for="newbatch"
                            class="form-label"
                    >
                        Batch / Specification
                    </label>

                    <input
                            type="text"
                            class="form-control"
                            id="newbatch"
                            name="newbatch"
                            autocomplete="off"
                            style="text-transform: uppercase;"
                    >

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                >
                    Cancel
                </button>


                <button
                        type="button"
                        class="btn btn-primary"
                        onclick="save_new_spec()"
                >
                    <i class="fa fa-save"></i>
                    Process
                </button>

            </div>

        </div>

    </div>

</div>


<script type="application/javascript" src="<?= base_url('assets/pagejs/purchase/pp.js') ?>"></script>
<script type="text/javascript">
    /* ==========================================================
     * VALIDASI INPUT AWAL CABANG / JOB (KHUSUS ADDPP)
     * - Cabang otomatis mengikuti cabang user login
     * - Hanya user PT JATIM TAMAN STEEL yang boleh memilih bebas
     * - PLAN I / PLAN 1 / PLANT I dianggap cabang yang sama
     * ========================================================== */
    function normalizeBranchKey(name) {
        return (name || '').toString().toUpperCase().replace(/[^A-Z0-9]/g, '');
    }

    function canonicalBranchKey(name) {
        var key = normalizeBranchKey(name);

        var aliases = {
            'PLANI': 'PLANTI',
            'PLAN1': 'PLANTI',
            'PLANT1': 'PLANTI',
            'PLANII': 'PLANTII',
            'PLAN2': 'PLANTII',
            'PLANT2': 'PLANTII',
            'PTJATIMTAMANSTEEL': 'PTJATIMTAMANSTEELMFG'
        };

        return aliases[key] || key;
    }

    function findBranchByName(branchName, callback) {
        var keyword = (branchName || '').toString().trim();

        if (keyword === '') {
            callback(null);
            return;
        }

        function pick(items) {
            var expected = canonicalBranchKey(keyword);

            for (var i = 0; i < items.length; i++) {
                var itemName = items[i].nmbranch || items[i].text || '';

                if (canonicalBranchKey(itemName) === expected) {
                    return items[i];
                }
            }

            return null;
        }

        $.ajax({
            type: 'POST',
            url: HOST_URL + 'api/globalmodule/list_branchjob',
            data: {
                _search_: keyword,
                _page_: 1,
                _perpage_: 50,
                _draw_: true,
                _start_: 1
            },
            dataType: 'json'
        }).done(function (data) {
            var items = (data && data.items) ? data.items : [];
            var match = pick(items);

            if (match) {
                callback(match);
                return;
            }

            // Fallback: ambil seluruh cabang lalu cocokkan nama
            $.ajax({
                type: 'POST',
                url: HOST_URL + 'api/globalmodule/list_branchjob',
                data: {
                    _search_: '',
                    _page_: 1,
                    _perpage_: 100,
                    _draw_: true,
                    _start_: 1
                },
                dataType: 'json'
            }).done(function (allData) {
                var allItems = (allData && allData.items) ? allData.items : [];
                callback(pick(allItems));
            }).fail(function () {
                callback(null);
            });
        }).fail(function () {
            callback(null);
        });
    }

    function findBranchFromSession(branchId, branchName, callback) {
        if (branchId !== '') {
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_branchjob',
                data: { var: branchId },
                dataType: 'json'
            }).done(function (data) {
                var items = (data && data.items) ? data.items : [];

                if (items.length > 0) {
                    callback(items[0]);
                    return;
                }

                findBranchByName(branchName, callback);
            }).fail(function () {
                findBranchByName(branchName, callback);
            });
            return;
        }

        findBranchByName(branchName, callback);
    }

    function applyInitialBranch(item, canChoose) {
        var $cabang = $('#cabang');

        if (!$cabang.length || !item) {
            return;
        }

        var branchId = (item.id || item.idbranch || '').toString().trim();
        var branchName = (item.nmbranch || item.text || '').toString().trim();

        if (branchId === '') {
            return;
        }

        // Jangan biarkan option tanpa label bila nama cabang tidak ditemukan.
        if (branchName === '') {
            branchName = branchId;
        }

        var optionExists = $cabang.find('option').filter(function () {
            return ($(this).val() || '').toString() === branchId;
        }).length > 0;

        if (canChoose) {
            // PT JATIM TAMAN STEEL: tetap default ke cabang login,
            // tetapi user boleh mencari / memilih cabang lain.
            if (!optionExists) {
                $cabang.append(new Option(branchName, branchId, true, true));
            } else {
                $cabang.val(branchId);
            }

            $cabang.prop('disabled', false);
        } else {
            // User lain: kunci ke cabang login.
            $cabang.empty();
            $cabang.append(new Option(branchName, branchId, true, true));
            $cabang.prop('disabled', true);
        }

        // Catatan: Select2 4.0.13 tidak menyediakan method disable/enable,
        // cukup gunakan prop('disabled') agar container ikut ter-update.
        $cabang.trigger('change');

        // Select2 tetap menampilkan tombol clear (allowClear: true) walau
        // elemen sedang disabled. Klik-nya sudah diabaikan plugin, tetapi
        // tombolnya membingungkan user, jadi sembunyikan saat dikunci.
        if ($cabang.prop('disabled')) {
            $cabang
                .next('.select2-container')
                .find('.select2-selection__clear')
                .remove();
        }
    }


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
            var draftDocno = dtldata.docno.toString().trim();

            $('#docno').val(draftDocno).prop('readonly', true);

            // Nomor PP draft sudah final. Seeder prefix / infix / suffix diambil
            // dari docno header karena savePPDetail() menyusun docno dari ketiga
            // field tersebut (bukan dari #docno). Tanpa seed, nomor PP bisa kosong
            // atau berubah sehingga detail tidak masuk ke header PP yang sudah ada.
            var draftDocnoParts = draftDocno.split('/');

            if (draftDocnoParts.length === 3) {
                $('#prefix').val(draftDocnoParts[0].trim());
                $('#infix').val(draftDocnoParts[1].trim());
                $('#suffix').val(draftDocnoParts[2].trim());
            }

            // Penanda untuk pp.js: nomor PP jangan digenerate ulang.
            window.ppDraftDocno = draftDocno;

            // Nomor PP draft sudah final, jadi ketiga bagian nomor dikunci
            // agar docno yang dikirim savePPDetail() tetap sama dengan header.
            $('#prefix').prop('readonly', true);
            $('#infix').prop('readonly', true);
            $('#suffix').prop('readonly', true);
        }

        /* VALIDASI INPUT AWAL CABANG / JOB */
        var hasDraftPP = dtldata && dtldata.docno && dtldata.docno.toString().trim() !== '';
        var draftCabang = hasDraftPP ? (dtldata.cabang || '').toString().trim() : '';

        var $cabangInput = $('#cabang');
        var sessionBranchId = ($cabangInput.attr('data-session-branch-id') || '').toString().trim();
        var sessionBranchName = ($cabangInput.attr('data-session-branch-name') || '').toString().trim();
        var canChooseBranch = ($cabangInput.attr('data-can-choose-branch') || '0').toString() === '1';

        // Dokumen draft: cabang header sudah tersimpan, jadi ikuti data header.
        var initialBranchId = draftCabang !== '' ? draftCabang : sessionBranchId;
        var initialBranchName = draftCabang !== '' ? '' : sessionBranchName;

        // Draft selalu dikunci agar cabang header tidak berubah diam-diam.
        var lockBranch = !canChooseBranch || hasDraftPP;

        findBranchFromSession(initialBranchId, initialBranchName, function (item) {
            if (!item) {
                if (!lockBranch) {
                    return;
                }

                if (initialBranchId !== '') {
                    // Fallback: tetap kunci memakai data yang tersedia
                    // agar user tidak terhambat bila lookup nama gagal.
                    item = {
                        id: initialBranchId,
                        nmbranch: initialBranchName || initialBranchId
                    };
                } else {
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Cabang / Job user login tidak ditemukan. Silakan hubungi IT.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });

                    return;
                }
            }

            applyInitialBranch(item, !lockBranch);
        });


        // DOC DATE = hari ini
        let today = moment();

        // EST PAKAI = +7 hari
        let nextWeek = moment().add(7, 'days');

        $('#docdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            startDate: today,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        // set default value ke input
        $('#docdate').val(today.format('DD-MM-YYYY'));

        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            const dateStr = picker.startDate.format('DD-MM-YYYY');
            $(this).val(dateStr);

            // Update estpakai berdasarkan docdate baru
            const prefix = $('#prefix').val() || 'JI';
            setupEstpakai(prefix);
        });

        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });



        // $('#estpakai').daterangepicker({
        //     autoUpdateInput: false,
        //     singleDatePicker: true,
        //     showDropdowns: true,
        //     startDate: nextWeek,
        //     locale: { format: 'DD-MM-YYYY' },
        //     cancelLabel: 'Clear'
        // });

        // // set default value ke input
        // $('#estpakai').val(nextWeek.format('DD-MM-YYYY'));

        // $('#estpakai').on('apply.daterangepicker', function(ev, picker) {
        //     $(this).val(picker.startDate.format('DD-MM-YYYY'));
        // });

        // $('#estpakai').on('cancel.daterangepicker', function(ev, picker) {
        //     $(this).val('');
        // });

    // Contoh penggunaan dengan prefix
    // Panggil ketika prefix berubah atau saat load
    $(document).ready(function() {
        // Misal dapat prefix dari input lain
        const prefix = $('#prefix').val() || 'JI'; // contoh

        // Setup berdasarkan prefix
        setupEstpakai(prefix);

        // Event listener untuk perubahan prefix
        $('#prefix').on('change', function() {
            const newPrefix = $(this).val();
            setupEstpakai(newPrefix);
        });

        // Tambahan: tombol untuk menampilkan opsi pilihan
        $('#btnShowOptions').on('click', function() {
            const prefix = $('#prefix').val() || 'JI';
            showDateOptions(prefix);
        });
    });


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