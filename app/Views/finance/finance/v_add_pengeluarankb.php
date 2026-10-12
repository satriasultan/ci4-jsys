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
<?php
    $isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
    $disabled = $isIT ? '' : 'disabled';
?>
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('ka/finance/finalEntryPengeluaranKB') ?>" method="post" id="formPO">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Header</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Pengeluaran Kas/Bank
                            </div>
                            <div class="row">

                                <!-- LEFT COLUMN -->
                                <div class="col-md-3">

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="cabang">Cabang / Job</label>
                                                <select name="cabang" id="cabang" class="form-control" required></select>
                                            </div>
                                        </div>


                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>No. Bukti</label>

                                                <div class="d-flex">
                                                    <input type="text"
                                                        name="prefix"
                                                        id="prefix"
                                                        class="form-control me-1"
                                                        maxlength="3"
                                                        style="text-transform: uppercase;"
                                                        pattern="[A-Z0-9]+">

                                                    <span class="px-2 align-self-center">/</span>

                                                    <input type="text"
                                                        name="infix"
                                                        id="infix"
                                                        class="form-control mx-1"
                                                        readonly>

                                                    <span class="px-2 align-self-center">/</span>

                                                    <input type="text"
                                                        name="sufix"
                                                        id="sufix"
                                                        class="form-control ms-1"
                                                        readonly>

                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="20"     value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>" style="text-transform: uppercase;" readonly>
                                            <input type="hidden" name="nmprkkas" class="form-control col-sm-12" id="nmprkkas">


                                    </div>

                                </div>

                                <!-- RIGHT COLUMN -->
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="docdate">Tanggal</label>
                                                <input type="text"
                                                name="docdate"
                                                id="docdate"
                                                class="form-control"
                                                placeholder="Tanggal">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pp">Referensi</label>
                                                <input type="text"
                                                name="pp"
                                                id="pp"
                                                class="form-control"
                                                placeholder="Referensi" style="text-transform:uppercase;" >
                                            </div>
                                        </div>
                                    </div>
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
                                                    class="form-control ratakanan jtsseparator"
                                                    placeholder="Nilai tukar akan muncul disini"
                                                    >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="kdsupplier">Supplier</label>
                                                <select name="kdsupplier" id="kdsupplier" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatsupplier">Alamat Supplier</label>
                                                <textarea name="alamatsupplier"
                                                    id="alamatsupplier"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Supplier"

                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-md-12" >
                                                <div class="form-group">
                                                    <label for="keterangan">Keterangan</label>
                                                    <textarea name="keterangan"
                                                        id="keterangan"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Keterangan"
                                                        style="text-transform:uppercase;" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: -25px;">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="prkkas">Perk. Kas/Bank</label>
                                                    <select name="prkkas" id="prkkas" class="form-control" required>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="total">Nilai</label>
                                                    <input type="text"
                                                            name="total"
                                                            id="total"
                                                            class="form-control jtsseparator ratakanan">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                </div>
            </div>


                <!-- =====================================================
                     CARD 3 : ALOKASI / BUKTI PENERIMAAN BARANG
                ====================================================== -->
                <div class="card mt-3 card-primary">
                    <div class="card-header d-flex align-items-center flex-wrap gap-2">
                        <h3 class="card-title mb-0 me-3">Bukti Alokasi Nilai</h3>
                    </div>

                    <div class="card-body">
                        <!--                <hr class="my-4">-->

                        <div class="row g-3">


                            <!-- =========================================
                                 LEFT : ALOKASI
                            ========================================== -->

                            <div class="col-12">
                                <!-- TARIK PENERIMAAN - TETAP -->
                                <div class="d-flex justify-content-between align-items-center mb-2 w-100">

                                    <!-- KIRI : TARIK PENERIMAAN -->
                                    <button type="button" class="btn btn-primary" onclick="btnTarikPenerimaanTterima()">
                                        <i class="fas fa-search"></i> Tarik Penerimaan
                                    </button>


                                    <!-- KANAN : ACTION DETAIL -->
                                    <div class="d-flex align-items-center gap-1 detail-action-toolbar">

                                        <!-- TAMBAH -->
                                        <button type="button"
                                                class="btn btn-success btn-sm btn-detail-action"
                                                id="btnAddDetail"
                                                onclick="addDetailPengeluaranKB()"
                                                title="Tambah Detail"
                                                aria-label="Tambah Detail">
                                            <i class="fa fa-plus"></i>
                                        </button>

                                        <!-- EDIT -->
                                        <button type="button"
                                                class="btn btn-warning btn-sm btn-detail-action"
                                                id="btnEditDetail"
                                                onclick="editSelectedTterimaDetail()"
                                                title="Edit Detail"
                                                aria-label="Edit Detail"
                                                disabled>
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <!-- DELETE -->
                                        <button type="button"
                                                class="btn btn-danger btn-sm btn-detail-action"
                                                id="btnDeleteDetail"
                                                onclick="deleteSelectedTterimaDetail()"
                                                title="Hapus Detail"
                                                aria-label="Hapus Detail"
                                                disabled>
                                            <i class="fa fa-trash"></i>
                                        </button>

                                    </div>

                                </div>

                            </div>


                            <div class="allocation-box">

                                <div class="table-responsive">

                                    <table id="tabpengeluarankbdtl"
                                           class="table table-bordered table-hover table-sm mb-0"
                                           width="100%">

                                        <thead class="table-light">

                                        <tr>

                                            <th width="30">
                                                <input type="checkbox"
                                                       id="checkAll">
                                            </th>

                                            <th>No. Bukti</th>
                                            <th>Perkiraan</th>
                                            <th>
                                                Keterangan
                                            </th>
                                            <th class="text-center">
                                                DK
                                            </th>
                                            <th>
                                                Cost/Profit Center
                                            </th>
                                            <th class="text-end">
                                                Nilai
                                            </th>

                                        </tr>

                                        </thead>


                                        <tbody id="bodyTterimaDetail">

                                        <tr>

                                            <td colspan="7"
                                                class="allocation-empty">

                                                <i class="fa fa-box-open text-muted"></i>

                                                Belum ada penerimaan
                                                yang dialokasikan.

                                            </td>

                                        </tr>

                                        </tbody>

                                    </table>

                                </div>

                                <!-- TOTAL ALOKASI NILAI -->
                                <div class="d-flex justify-content-end align-items-center border-top bg-light px-1 py-2">
                                    <label for="totalalokasi"
                                           class="form-label mb-0 me-2 fw-semibold">
                                        Total Alokasi
                                    </label>

                                    <input type="text"
                                           name="totalalokasi"
                                           id="totalalokasi"
                                           class="form-control form-control-sm text-end"
                                           style="max-width: 220px; font-size: 17px; font-weight: 700;"
                                           value="0"
                                           readonly>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('ka/finance/clearfinalPengeluaranKB'); ?>"
                               class="btn btn-secondary"
                               onclick="return confirm('Clear entry Tanda Terima?')">
                                <i class="fa fa-arrow-left me-1"></i>
                                Kembali
                            </a>

                            <?php if ($typeform != 'DETAIL') : ?>
                                <button type="button"
                                        id="btnSimpan"
                                        class="btn btn-success"
                                        onclick="finalPengeluaranKB()">

                                    <i class="fa fa-save me-1"></i>
                                    Simpan Final Entry Kas Bank

                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
    </form>
        </div>




    <!-- =========================================================
         MODAL ADD / EDIT DETAIL TANDA TERIMA
    ========================================================= -->
    <div class="modal fade"
         id="modalDetailPengeluaranKB"
         tabindex="-1"
         aria-labelledby="modalDetailPengeluaranKBLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content border-0 shadow">

                <!-- =================================================
                     HEADER MODAL
                ================================================== -->
                <div class="modal-header bg-primary text-white">

                    <div>
                        <h5 class="modal-title mb-0"
                            id="modalDetailPengeluaranKBLabel">
                            Detail Tanda Terima
                        </h5>

                        <small class="opacity-75">
                            Input detail transaksi
                        </small>
                    </div>

                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>


                <!-- =================================================
                     FORM DETAIL
                ================================================== -->
                <form id="formPengeluaranKBDetail" autocomplete="off">

                    <div class="modal-body">

                        <!-- =================================================
                             HIDDEN DETAIL
                             JANGAN gunakan idurut/docno header
                        ================================================== -->

                        <input type="hidden"
                               name="idurut_detail"
                               id="detail_idurut">

                        <input type="hidden"
                               name="uniqueid"
                               id="detail_uniqueid">

                        <input type="hidden"
                               name="docno_detail"
                               id="detail_docno">

                        <!--inisialisasi pembatasan -->
                        <input type="hidden"
                               name="nilai_ref"
                               id="nilai_ref">

                        <input type="hidden"
                               name="jurnalref"
                               id="jurnalref">
                        <input type="hidden" name="nmperkiraan" class="form-control col-sm-12" id="nmperkiraan">
                        <!-- =================================================
                             HEADER REFERENCE
                        ================================================== -->
                        <div class="row g-3 mb-3">

                            <div class="col-md-4">

                                <label class="form-label fw-semibold">
                                    Supplier
                                </label>

                                <input type="text"
                                       id="detail_supplier"
                                       class="form-control bg-light"
                                       readonly
                                       placeholder="Supplier">

                            </div>

                            <div class="col-md-4">

                                <label class="form-label fw-semibold">
                                    No. Jurnal
                                </label>

                                <input type="text"
                                       id="detail_docno_display"
                                       class="form-control bg-light"
                                       readonly
                                       placeholder="No. Jurnal">

                            </div>

                            <div class="col-md-4">

                                <label class="form-label fw-semibold">
                                    ID Header
                                </label>

                                <input type="text"
                                       id="detail_idurut_display"
                                       class="form-control bg-light"
                                       readonly
                                       placeholder="ID Header">

                            </div>

                        </div>


                        <hr class="my-3">


                        <!-- =================================================
                             ROW 1
                        ================================================== -->
                        <div class="row g-3">

                            <!-- NO BUKTI -->
                            <div class="col-md-4">

                                <label for="nobukti"
                                       class="form-label fw-semibold">
                                    No. Bukti
                                </label>

                                <input type="text"
                                       name="nobukti"
                                       id="nobukti"
                                       class="form-control"
                                       maxlength="50"
                                       placeholder="Nomor Bukti"
                                       style="text-transform:uppercase;">

                            </div>


                            <!-- PERKIRAAN -->
                            <div class="col-md-8">

                                <label for="perkiraan"
                                       class="form-label fw-semibold">
                                    Perkiraan
                                </label>

                                <select name="perkiraan"
                                        id="perkiraan"
                                        class="form-select"
                                        style="width:100%;">

                                    <option value="">
                                        -- Pilih Perkiraan --
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- =================================================
                             ROW 2
                        ================================================== -->
                        <div class="row g-3 mt-1">

                            <!-- DK -->
                            <div class="col-md-2">

                                <label for="dk"
                                       class="form-label fw-semibold">
                                    DK
                                </label>

                                <select name="dk"
                                        id="dk"
                                        class="form-select">

                                    <option value="D">
                                        D
                                    </option>

                                    <option value="K">
                                        K
                                    </option>

                                </select>

                            </div>


                            <!-- COST / PROFIT CENTER -->
                            <div class="col-md-4">

                                <label for="costcenter"
                                       class="form-label fw-semibold">
                                    Cost / Profit Center
                                </label>

                                <select name="costcenter"
                                        id="costcenter"
                                        class="form-select"
                                        style="width:100%;">

                                    <option value="">
                                        -- Pilih Cost / Profit Center --
                                    </option>

                                    <option value="01.01">
                                        01.01 - PLANT I
                                    </option>

                                    <option value="01.02">
                                        01.02 - PLANT II
                                    </option>

                                </select>

                            </div>


                            <!-- KETERANGAN -->
                            <div class="col-md-6">

                                <label for="keterangan_dtl"
                                       class="form-label fw-semibold">
                                    Keterangan
                                </label>

                                <textarea name="keterangan_dtl"
                                          id="keterangan_dtl"
                                          class="form-control"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Keterangan detail..."
                                          style="text-transform:uppercase;"></textarea>

                            </div>

                        </div>


                        <!-- =================================================
                             ROW 3
                        ================================================== -->
                        <div class="row g-3 mt-1">

                            <!-- NILAI -->
                            <div class="col-md-5 ms-auto">

                                <label for="nilai"
                                       class="form-label fw-semibold">
                                    Nilai
                                </label>

                                <div class="input-group">

                                <span class="input-group-text">
                                    Rp
                                </span>

                                    <input type="text"
                                           name="nilai"
                                           id="nilai"
                                           class="form-control text-end fw-bold jtsseparator ratakanan"
                                           value="0"
                                           placeholder="0"
                                           inputmode="decimal">

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         FOOTER
                    ================================================== -->
                    <div class="modal-footer bg-light">

                        <button type="button"
                                class="btn btn-primary"
                                id="btnSavePengeluaranKBDetail"
                                onclick="savePengeluaranKBDetail()">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                onclick="$('#modalDetailPengeluaranKB').modal('hide')">
                            <i class="fa fa-times"></i> Batal
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

<script type="application/javascript" src="<?= base_url('assets/pagejs/finance/pengeluarankb.js') ?>"></script>
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
            const dateStr = picker.startDate.format('DD-MM-YYYY');
            $(this).val(dateStr);

            // Update estpakai berdasarkan docdate baru
            const prefix = $('#prefix').val() || 'JI';
            setupEstpakai(prefix);
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });



        $('#senddate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#senddate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'senddate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'senddate');
        });
        $('#senddate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
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

        document.addEventListener('DOMContentLoaded', function() {

            document.addEventListener('keydown', function(e) {

                const activeTag = document.activeElement.tagName;

                if (
                    e.ctrlKey &&
                    e.shiftKey &&
                    e.code === 'KeyE' &&
                    !['INPUT','TEXTAREA','SELECT'].includes(activeTag)
                ) {
                    e.preventDefault();
                    console.log('Shortcut triggered');
                    btnUpdateDetail();
                }

            });

        });

    });

</script>