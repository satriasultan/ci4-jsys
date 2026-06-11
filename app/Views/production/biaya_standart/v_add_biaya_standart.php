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
        border-left-color: #024086;
        border-bottom-color: #034081;
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
    /* ===== Custom Table Style ===== */

    .custom-table {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 14px;
    }

    .custom-table thead {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        color: #fff;
    }

    .custom-table thead th {
        border: none;
        padding: 12px 10px;
        font-weight: 500;
    }

    .custom-table tbody tr {
        background-color: #ffffff;
        transition: all 0.2s ease-in-out;
    }

    .custom-table tbody tr:hover {
        background-color: #f1f5f9;
    }

    .custom-table tbody td {
        padding: 10px;
        vertical-align: middle;
        border-top: 1px solid #e5e7eb;
    }

    .custom-table input[type="checkbox"] {
        transform: scale(1.1);
        cursor: pointer;
    }



    /* ===============================
   TABLE HEADER STYLE - CORPORATE
================================ */

    #tabspktransfersdtl {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        width: 100%;
    }

    /* HEADER */
    #tabspktransfersdtl thead th {
        background: linear-gradient(135deg, #1f2937, #374151);
        color: #ffffff;
        font-weight: 600;
        text-align: center;
        padding: 10px 8px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    /* Rounded corner atas */
    #tabspktransfersdtl thead th:first-child {
        border-top-left-radius: 8px;
    }

    #tabspktransfersdtl thead th:last-child {
        border-top-right-radius: 8px;
    }

    /* BODY CELL */
    #tabspktransfersdtl tbody td {
        padding: 8px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    /* Zebra row */
    #tabspktransfersdtl tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    /* Hover row */
    #tabspktransfersdtl tbody tr:hover {
        background-color: #e9f2ff;
        transition: all 0.2s ease-in-out;
    }

    /* Alignment sesuai 6 kolom */
    #tabspktransfersdtl tbody td:nth-child(1), /* checkbox */
    #tabspktransfersdtl tbody td:nth-child(4), /* satuan */
    #tabspktransfersdtl tbody td:nth-child(5)  /* quantity */
    {
        text-align: center;
    }


    /* Table Head Untuk Total Row */

     #totalQty, #totalNilai {
         border: 2px solid #6c757d !important; /* Border abu-abu tebal */
         background-color: #f8f9fa;            /* Warna latar sedikit kontras */
         border-radius: 4px;                   /* Membuat sudut sedikit melengkung */
     }

</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">

            <!-- LEFT -->
            <div class="col-sm-6">
                <h1 class="m-0">Biaya Standart Produksi</h1>
            </div>

            <!-- RIGHT -->
            <div class="col-sm-6 d-flex justify-content-end align-items-center">

                <!-- VERSION -->
                <div style="margin-right: 10px; font-size: 13px;">
                    Versi: <?php echo $version; ?>
                </div>

                <!-- BREADCRUMB -->
                <ol class="breadcrumb mb-0">

                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>">

                    <?php foreach ($y as $y1) { ?>
                        <?php if (trim($y1->kodemenu) != trim($kodemenu)) { ?>
                            <li class="breadcrumb-item">
                                <a href="<?= base_url(trim($y1->linkmenu)) ?>">
                                    <i class="fa <?= trim($y1->iconmenu); ?>"></i>
                                    <?= trim($y1->namamenu); ?>
                                </a>
                            </li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active">
                                <i class="fa <?= trim($y1->iconmenu); ?>"></i>
                                <?= trim($y1->namamenu); ?>
                            </li>
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
?>
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('production/trans/final_input_biaya_standart') ?>" method="post" id="formStandarCostMst">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Biaya Standart</h3>
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
                                <div class="col-md-6" >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cabang">Cabang / Job</label>
                                                <select name="cabang" id="cabang" class="form-control" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pemohon">Pemohon</label>
                                                <input type="text" name="pemohon" id="pemohon" class="form-control" maxlength="50" placeholder="Pemohon" readonly value="<?= esc(strtoupper($userlogin)) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No. Jurnal</label>
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
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="docdate">Tanggal</label>
                                                    <input type="text"
                                                    name="docdate"
                                                    id="docdate"
                                                    class="form-control"
                                                    placeholder="Tanggal">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="activedate">Active Date</label>
                                                    <input type="text"
                                                           name="activedate"
                                                           id="activedate"
                                                           class="form-control"
                                                           placeholder="Active Date">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Penyesuaian 1</label>
                                                <input type="number"
                                                       name="penyesuaian_a"
                                                       id="penyesuaian_a"
                                                       class="form-control"
                                                       placeholder="Penyesuaian 1" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Penyesuaian 2</label>
                                                <select name="penyesuaian_b" id="penyesuaian_b" class="form-select select2 getLastStock" style="width:100%" disabled>
                                                    <option value="">-- Pilih --</option>
                                                    <option value="Nilai">Nilai</option>
                                                    <option value="Persen">Persen</option>
                                                    <option value="Persen Rounded">Persen Rounded</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="from">Dari</label>
                                                <select name="dari_bagian" id="dari_bagian" class="form-control" disabled>
                                                    <option value="">-- Pilih --</option>
                                                    <option value="Actual Cost">Actual Cost</option>
                                                    <option value="Last Cost">Last Cost</option>
                                                    <option value="New Cost">New Cost</option>

                                                </select>
                                            </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger w-100" disabled>
                                                <i class="fa fa-sliders"></i> Adjustment
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6  border border-dark" >
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="keterangan">Keterangan</label>
                                                <textarea name="keterangan" id="keterangan" class="form-control" rows="5" placeholder="Catatan / Informasi..." style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div> <!-- END ROW 2-->

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
                                data-toggle="tooltip"
                                title="Input Data (Ctrl + Q)"
                                id="btnAddDetail">
                            <i class="fa fa-plus"></i>
                        </button>

                        <button type="button"
                                id="btnUpdateDetail"
                                class="btn btn-warning btn-lg action-btn"
                                title="Update Data"
                                onclick="updateStandartCost()">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button type="button"
                                class="btn btn-danger btn-lg action-btn"
                                data-toggle="tooltip"
                                title="Hapus Data"
                                onclick="btnDeleteDetail()">
                            <i class="fa fa-trash"></i>
                        </button>


                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table id="tmp_stdcost" class="table table-hover align-middle custom-table">
                            <thead>
                            <tr>
                                <th class="text-center" width="40">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th class="text-end">Actual Std Cost</th>
                                <th class="text-end">Last Std Cost</th>
                                <th class="text-end">New Std Cost</th>
                                <th>Keterangan</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-light">
                    <a href="<?= base_url('production/trans/clearBiayaStandartTmp') ?>"
                        class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <?php if ($typeform != 'DETAIL' && $dtldata != null): ?>
                        <button type="submit"
                                onclick="return confirm('Simpan Data?')"
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
<div class="modal fade" id="modalDtlStandartCost" tabindex="-1" role="dialog" aria-labelledby="modalDtlStandartCost" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDtlStandartCostTitle">
                    Input Item Detail
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- FORM -->
            <form id="formStandartCostDtl">

                <div class="modal-body">

                    <input type="hidden" name="idurut" id="idurut">

                    <!-- ROW 1 -->
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">ID Barang</label>
                            <select name="idbarang" id="idbarang"
                                    class="form-select select2 "
                                    style="width:100%"></select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Nama Barang</label>
                            <input type="text"
                                   name="nmbarang"
                                   id="nmbarang"
                                   class="form-control"
                                   placeholder="Nama Barang"
                                   style="text-transform:uppercase"
                                   readonly>
                        </div>


                        <div class="col-md-2">
                            <label class="form-label">Satuan</label>
                            <input name="unit"
                                   id="unit"
                                   class="form-control"
                                   readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Actual Cost</label>
                            <input type="text"
                                   name="actualcost"
                                   id="actualcost"
                                   class="form-control jtsseparator text-end getsisastock"
                                   placeholder="0.00" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Last Std Cost</label>
                            <input type="text"
                                   name="lastcost"
                                   id="lastcost"
                                   class="form-control jtsseparator text-end getsisastock"
                                   placeholder="0.00" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">New Std Cost</label>
                            <input type="text"
                                   name="newcost"
                                   id="newcost"
                                   class="form-control jtsseparator text-end"
                                   placeholder="0.00" required>
                        </div>

                    </div>


                    <!-- ROW 2 -->
                    <div class="row g-3 mt-1">
<?php /*
                        <div class="col-md-2">
                            <label class="form-label">Debit / Kredit</label>
                            <select name="dk" id="dk" class="form-select select2" required>
                                <option value="">-- Pilih --</option>
                                <option value="D">D</option>
                                <option value="K">K</option>
                            </select>
                        </div>


                        <div class="col-md-2">
                            <label class="form-label">Mata Uang</label>
                            <input name="currency"
                                   id="currency"
                                   class="form-control"
                                   readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Harga</label>
                            <input type="text"
                                   name="valqty"
                                   id="valqty"
                                   class="form-control jtsseparator text-end"
                                   placeholder="0.00" required>
                        </div>
 */ ?>
                        <div class="col-md-6">
                            <label class="form-label">Description Detail</label>
                            <textarea name="description_detail"
                                      id="description_detail"
                                      rows="3"
                                      class="form-control"
                                      style="text-transform:uppercase"></textarea>
                        </div>

                    </div>

                </div>


                <!-- FOOTER -->
                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-primary save_biaya_standart"
                            onclick="save_biaya_standart()">
                        <i class="fa fa-save"></i> Simpan
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>



<script type="application/javascript" src="<?= base_url('assets/pagejs/production/biaya_standart/biaya_standart.js') ?>"></script>
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
            locale: { format: 'YYYY-MM-DD' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        $('#activedate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'YYYY-MM-DD' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#activedate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'activedate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'activedate');
        });
        $('#activedate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        $('#estpakai').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'YYYY-MM-DD' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#estpakai').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'estpakai', 'NOT_VALIDATED').bootstrapValidator('validateField', 'estpakai');
        });
        $('#estpakai').on('cancel.daterangepicker', function(ev, picker) {
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



    });

</script>