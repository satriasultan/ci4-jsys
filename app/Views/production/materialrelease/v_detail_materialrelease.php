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
                <h1 class="m-0">Material Release</h1>
            </div>

            <!-- RIGHT -->
            <div class="col-sm-6 d-flex justify-content-end align-items-center">

                <!-- VERSION -->
                <div style="margin-right: 10px; font-size: 13px;">
                    Menu ID <?php echo $version; ?>
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
    <!-- <form action="<?= base_url('production/trans/final_input_materialrelease') ?>" method="post" id="formStandarCostMst"> -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Material Release</h3>
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
                            <div class="col-md-3" >

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="cabang">Cabang / Job</label>
                                            <select name="cabang" id="cabang" class="form-control" required></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>No. Material Release</label>
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
                                        
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="pemohon">Pemohon</label>
                                            <input type="text" name="pemohon" id="pemohon" class="form-control" maxlength="50" placeholder="Pemohon" readonly value="<?= esc(strtoupper($userlogin)) ?>">
                                        </div>
                                    </div>
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
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Dari Gudang</label>
                                            <select name="idlocation" id="idlocation" class="form-select select2 " required style="width:100%">
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="nmlocation" class="form-control col-sm-12" id="nmlocation" maxlength="20"  style="text-transform: uppercase;" readonly>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">No. Terima Antar Bagian</label>
                                            <select name="tabno" id="tabno" class="form-select select2 " style="width:100%">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">No. Work Order Execution</label>
                                            <select name="woeno" id="woeno" class="form-select select2 " style="width:100%">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">No. Work Order</label>
                                            <select name="wono" id="wono" class="form-select select2 " style="width:100%">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">No. BOM</label>
                                            <select name="bomno" id="bomno" class="form-select select2 " style="width:100%">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Barang Jadi</label>
                                            <select name="idbarang_jadi" id="idbarang_jadi" class="form-select select2 " style="width:100%">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" >
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Build For</label>
                                            <div class="form-group">
                                                <input type="text" name="buildfor" id="buildfor" class="form-control ratakanan jtsseparator" placeholder="Build Qty For" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="from">Unit</label>
                                            <select name="buildunit" id="buildunit" class="form-control">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">No. Batch Reff</label>
                                            <div class="form-group">
                                                <input type="text" name="batchno" id="batchno" class="form-control" placeholder="No. Batch Reff">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="from">Untuk Bagian</label>
                                            <select name="bagian" id="bagian" class="form-control" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan</label>
                                            <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Catatan / Informasi..." style="text-transform:uppercase;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- END ROW 2-->
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 card-primary">
                <div class="card-header clearfix">
                    <h3 class="card-title">
                        Detail Barang
                    </h3>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table id="trx_materialreleasedtl"
                                class="table table-hover align-middle custom-table">
                            <thead>
                            <tr>
                                <th class="text-center" width="40">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th width="40">Sub</th>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Spec</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <!-- <th class="text-end">Standart Cost</th>
                                <th class="text-end">Total Cost</th> -->
                                <th>Keterangan</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>

                <!-- FOOTER -->
                <div class="card-footer bg-light">

                    <a href="<?= base_url('production/trans/materialrelease') ?>"
                        class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a>

                    <button type="button"
                            id="btnLaporanJurnal"
                            class="btn btn-primary btn-lg"
                            style="margin-left: 8px;">
                        <i class="fa fa-book"></i> Laporan Jurnal Transaksi
                    </button>


                </div>

            </div>
        </div>
    <!-- </form> -->
        <!-- /.card -->
    <!--/.col (left) -->
    <!-- right column -->
    <div class="col-md-6">

    </div>
    <!--/.col (right) -->
</div>




<!-- =================MODAL Material Release  ================= -->
<div class="modal fade" id="modalDtlMaterialRelease" tabindex="-1" role="dialog" aria-labelledby="modalDtlMaterialRelease" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDtlMaterialReleaseTitle">
                    Input Material Release
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- FORM -->
            <form id="formMaterialReleaseMaterialDtl">

                <div class="modal-body">

                    <input type="hidden" name="idurut" id="idurutMaterial">

                    <!-- ROW 1 -->
                    <div class="row g-3">
                        <div class="col-md-1 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input"
                                    type="checkbox"
                                    id="issub"
                                    name="issub"
                                    value="T">

                                <label class="form-check-label" for="issub">
                                    Substitute Item
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3" id="divItemBom">
                            <label class="form-label">BOM Item</label>
                            <select id="idbarang_bom"
                                    name="idbarang_bom"
                                    class="form-select select2"
                                    style="width:100%">
                            </select>
                        </div>

                        <div class="col-md-3" id="divItemSub" style="display:none;">
                            <label class="form-label">Substitute Item</label>
                            <select id="idbarang"
                                    name="idbarang"
                                    class="form-select select2"
                                    style="width:100%">
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Item Name</label>
                            <input type="text"
                                    name="nmbarang"
                                    id="nmbarang"
                                    class="form-control"
                                    placeholder="Nama Barang"
                                    style="text-transform:uppercase"
                                    readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">QTY</label>
                            <input type="text"
                                    name="qty"
                                    id="qty"
                                    class="form-control jtsseparator ratakanan"
                                    placeholder="Qty Barang"
                                    style="text-transform:uppercase">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Unit</label>
                            <input name="unit"
                                    id="unit"
                                    class="form-control"
                                    readonly>
                        </div>



                    </div>
                    <!-- ROW 2 -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Description Detail</label>
                            <textarea name="description"
                                        id="description"
                                        rows="3"
                                        class="form-control"
                                        style="text-transform:uppercase"></textarea>
                        </div>

                    </div>

                </div>


                <!-- FOOTER -->
                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-primary saveBomDetail"
                            onclick="saveMaterialReleaseDetail('formMaterialReleaseMaterialDtl')">
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




<!-- ================= MODAL LAPORAN JURNAL TRANSAKSI ================= -->
<div class="modal fade" id="modalLaporanJurnal" tabindex="-1" aria-labelledby="modalLaporanJurnalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLaporanJurnalLabel">
                    <i class="fa fa-book"></i> Laporan Jurnal Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableLaporanJurnal" class="table table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Doc No</th>
                                <th>Pemohon</th>
                                <th>COA</th>
                                <th>Nama Perkiraan</th>
                                <th class="text-end">Debet</th>
                                <th class="text-end">Kredit</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript" src="<?= base_url('assets/pagejs/production/materialrelease/materialrelease_trx.js') ?>"></script>
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
        // $('#activedate').daterangepicker({
        //     autoUpdateInput: false,
        //     singleDatePicker: true,
        //     showDropdowns: true,
        //     locale: { format: 'YYYY-MM-DD' },
        //     cancelLabel: 'Clear'
        // });
        //
        // // handler apply/cancel
        // $('#activedate').on('apply.daterangepicker', function(ev, picker) {
        //     $(this).val(picker.startDate.format('YYYY-MM-DD'));
        //     // jika butuh validasi bootstrapValidator:
        //     // $('#formInputTransfers').bootstrapValidator('updateStatus', 'activedate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'activedate');
        // });
        // $('#activedate').on('cancel.daterangepicker', function(ev, picker) {
        //     $(this).val('');
        // });


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

<script type="text/javascript">
    /* ===== LAPORAN JURNAL TRANSAKSI ===== */
    function formatNumberJurnal(value) {
        return parseFloat(value || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    $(document).ready(function () {
        $('#btnLaporanJurnal').on('click', function () {
            var docno = ($('#docno').val() || '').trim();

            if (docno === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Doc No kosong',
                    text: 'Dokumen belum memiliki nomor.'
                });
                return;
            }

            $('#tableLaporanJurnal tbody').html(
                '<tr><td colspan="8" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>'
            );
            $('#modalLaporanJurnal').modal('show');

            $.ajax({
                url: '<?= base_url('production/trans/laporan_jurnal_transaksi') ?>',
                type: 'POST',
                dataType: 'json',
                data: { docno: docno },
                success: function (response) {
                    if (!response.status) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: response.messages });
                        return;
                    }

                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableLaporanJurnal')) {
                        $('#tableLaporanJurnal').DataTable().destroy();
                    }

                    $('#tableLaporanJurnal').DataTable({
                        data: response.data,
                        destroy: true,
                        paging: false,
                        searching: false,
                        ordering: false,
                        info: false,
                        columns: [
                            {
                                data: null,
                                render: function (data, type, row, meta) {
                                    return (row.urutan == 1) ? '' : (meta.row + 1);
                                }
                            },
                            { data: 'trxdate', render: function (d) { return d || ''; } },
                            { data: 'docno', render: function (d) { return d || ''; } },
                            {
                                data: 'nmpemohon',
                                render: function (d, type, row) { return (row.urutan == 1) ? '' : (d || ''); }
                            },
                            {
                                data: 'idcoa',
                                render: function (d, type, row) { return (row.urutan == 1) ? '' : (d || ''); }
                            },
                            {
                                data: 'nmcoa',
                                render: function (d, type, row) {
                                    return (row.urutan == 1) ? '<b style="font-size:16px">TOTAL</b>' : (d || '');
                                }
                            },
                            {
                                data: 'debet',
                                className: 'text-right',
                                render: function (d) { return formatNumberJurnal(d || 0); }
                            },
                            {
                                data: 'kredit',
                                className: 'text-right',
                                render: function (d) { return formatNumberJurnal(d || 0); }
                            }
                        ],
                        createdRow: function (row, data) {
                            if (data.urutan == 1) {
                                $(row).css({ 'font-weight': 'bold', 'background-color': '#f1f1f1' });
                            }
                        }
                    });

                    setTimeout(function () {
                        $('#tableLaporanJurnal').DataTable().columns.adjust();
                    }, 300);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal mengambil laporan jurnal' });
                }
            });
        });
    });
</script>
