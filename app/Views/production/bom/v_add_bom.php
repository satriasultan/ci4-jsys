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







     /* =========================================================
    TAB DETAIL — ELEGAN MODERN
    ========================================================= */
    .detail-tab-wrapper {
        padding: 0;
    }

    .detail-tab-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-bottom: 1px solid #e2e8f0;
        border-radius: 8px 8px 0 0;
    }

    .detail-tab-header .tab-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-tab-header .tab-title i {
        color: #3b82f6;
        font-size: 1.1rem;
    }

    .detail-tab-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Tombol aksi — modern, proporsional */
    .btn-action {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: none;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .btn-action.btn-add {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
    }
    .btn-action.btn-add:hover {
        background: linear-gradient(135deg, #34d399, #10b981);
        color: #fff;
    }

    .btn-action.btn-edit {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
    }
    .btn-action.btn-edit:hover {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #fff;
    }

    .btn-action.btn-delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
    }
    .btn-action.btn-delete:hover {
        background: linear-gradient(135deg, #f87171, #ef4444);
        color: #fff;
    }

    /* Body tabel */
    .detail-tab-body {
        padding: 0;
        background: #ffffff;
        border-radius: 0 0 8px 8px;
    }

    .detail-tab-body .table-responsive {
        border-radius: 0 0 8px 8px;
        overflow: hidden;
    }

    /* Table modern */
    .table-modern {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #1e293b, #334155);
        color: #fff;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border: none;
        white-space: nowrap;
    }

    .table-modern thead th:first-child {
        border-top-left-radius: 0;
    }

    .table-modern tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
        color: #334155;
    }

    .table-modern tbody tr {
        transition: background 0.15s ease;
    }

    .table-modern tbody tr:hover {
        background: #f8fafc;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    /* Checkbox */
    .table-modern input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #3b82f6;
    }

    /* Footer total */
    .table-modern tfoot th {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 14px;
        border-top: 2px solid #e2e8f0;
        letter-spacing: 0.3px;
    }

    .table-modern tfoot th.text-end {
        text-align: right;
    }

    .table-modern tfoot th.total-value {
        background: #dbeafe;
        color: #1e40af;
        font-size: 0.95rem;
        text-align: right;
    }

    /* Alert total BOM */
    .total-bom-box {
        margin-top: 20px;
        padding: 16px 24px;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-left: 4px solid #3b82f6;
        border-radius: 8px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
    }

    .total-bom-box .label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e40af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .total-bom-box .value {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e3a8a;
    }

    /* Tab navigation modern */
    #detailBarangTab.nav-tabs {
        border-bottom: 1px solid #e2e8f0;
        padding: 0 20px;
        background: #f8fafc;
    }

    #detailBarangTab .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #64748b;
        font-weight: 500;
        padding: 14px 20px;
        transition: all 0.2s ease;
    }

    #detailBarangTab .nav-link:hover {
        color: #3b82f6;
        border-bottom-color: #bfdbfe;
    }

    #detailBarangTab .nav-link.active {
        color: #1e40af;
        border-bottom-color: #3b82f6;
        background: transparent;
        font-weight: 600;
    }

</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">

            <!-- LEFT -->
            <div class="col-sm-6">
                <h1 class="m-0">Bill Of Material</h1>
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
    <form action="<?= base_url('production/trans/final_input_bom') ?>" method="post" id="formStandarCostMst">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> BOM</h3>
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pemohon">Pemohon</label>
                                            <input type="text" name="pemohon" id="pemohon" class="form-control" maxlength="50" placeholder="Pemohon" readonly value="<?= esc(strtoupper($userlogin)) ?>">
                                        </div>
                                    </div>
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
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>No. BOM</label>
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
                                            <label class="form-label">Barang Jadi</label>
                                            <select name="idbarang_jadi" id="idbarang_jadi" class="form-select select2 " style="width:100%">
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Build For</label>
                                                <div class="form-group">
                                                    <input type="text" name="buildfor" id="buildfor" class="form-control ratakanan jtsseparator" placeholder="Build Qty For" required>
                                                </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="from">Unit</label>
                                            <select name="buildunit" id="buildunit" class="form-control">
                                                <option value="">-- Pilih --</option>
                                                <option value="Actual Cost">Actual Cost</option>
                                                <option value="Last Cost">Last Cost</option>
                                                <option value="New Cost">New Cost</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Minimum QTY</label>
                                            <div class="form-group">
                                                <input type="text" name="minimumqty" id="minimumqty" class="form-control ratakanan jtsseparator" placeholder="Build Qty For" >
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="col-md-6" >
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
            </div>

            <div class="card mt-3 card-primary">
                <!-- ============================================================
                    NAVIGATION TAB
                    ============================================================ -->
                <div class="card-header p-0">
                    <ul class="nav nav-tabs" id="detailBarangTab" role="tablist">

                        <li class="nav-item">
                            <a class="nav-link active"
                            id="material-tab"
                            data-bs-toggle="tab"
                            href="#material"
                            role="tab">
                                <i class="fa fa-cubes mr-1"></i>
                                Material
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link"
                            id="cost-tab"
                            data-bs-toggle="tab"
                            href="#cost"
                            role="tab">
                                <i class="fa fa-money-bill mr-1"></i>
                                Cost
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link"
                            id="wip-tab"
                            data-bs-toggle="tab"
                            href="#wip"
                            role="tab">
                                <i class="fa fa-industry mr-1"></i>
                                Barang WIP
                            </a>
                        </li>

                    </ul>
                </div>

                <!-- ============================================================
                    TAB CONTENT
                    ============================================================ -->
                <div class="card-body">
                    <div class="tab-content">

                        <!-- ====================================================
                            TAB MATERIAL
                            ==================================================== -->
                        <div class="tab-pane fade show active"
                            id="material"
                            role="tabpanel">

                            <div class="detail-tab-wrapper">

                                <!-- HEADER -->
                                <div class="detail-tab-header">
                                    <h3 class="tab-title">
                                        <i class="fa fa-cubes"></i>
                                        Detail Barang - Material
                                    </h3>

                                    <div class="detail-tab-actions">
                                        <button type="button"
                                                class="btn-action btn-add"
                                                title="Tambah Data (Ctrl + Q)"
                                                id="btnAddDetailMaterial">
                                            <i class="fa fa-plus"></i>
                                        </button>

                                        <button type="button"
                                                id="btnUpdateDetail"
                                                class="btn-action btn-edit"
                                                title="Update Data"
                                                onclick="updateDetailBomMaterial()">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <button type="button"
                                                class="btn-action btn-delete"
                                                title="Hapus Data"
                                                onclick="btnDeleteDetailMaterial()">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- BODY -->
                                <div class="detail-tab-body">
                                    <div class="table-responsive">
                                        <table id="tmp_bommaterialdtl"
                                            class="table table-modern">

                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="50">
                                                        <input type="checkbox" id="checkAll">
                                                    </th>
                                                    <th>ID Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th class="text-end">Qty</th>
                                                    <th>Satuan</th>
                                                    <th class="text-end">Standart Cost</th>
                                                    <th class="text-end">Total Cost</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <th colspan="6" class="text-end">
                                                        TOTAL NILAI MATERIAL
                                                    </th>
                                                    <th id="ttlmaterial" class="total-value">
                                                        0
                                                    </th>
                                                </tr>
                                            </tfoot>

                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ====================================================
                            TAB COST
                            ==================================================== -->
                        <div class="tab-pane fade"
                            id="cost"
                            role="tabpanel">

                            <div class="detail-tab-wrapper">

                                <!-- HEADER -->
                                <div class="detail-tab-header">
                                    <h3 class="tab-title">
                                        <i class="fa fa-money-bill"></i>
                                        Detail - Cost
                                    </h3>

                                    <div class="detail-tab-actions">
                                        <button type="button"
                                                class="btn-action btn-add"
                                                title="Tambah Data (Ctrl + Q)"
                                                id="btnAddDetailCost">
                                            <i class="fa fa-plus"></i>
                                        </button>

                                        <button type="button"
                                                id="btnUpdateDetailCost"
                                                class="btn-action btn-edit"
                                                title="Update Data"
                                                onclick="updateDetailBomCost()">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <button type="button"
                                                class="btn-action btn-delete"
                                                title="Hapus Data"
                                                onclick="btnDeleteDetailCost()">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- BODY -->
                                <div class="detail-tab-body">
                                    <div class="table-responsive">
                                        <table id="tmp_bomcostdtl"
                                            class="table table-modern">

                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="50">
                                                        <input type="checkbox" id="checkAllCost">
                                                    </th>
                                                    <th>ID Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th class="text-end">Qty</th>
                                                    <th>Satuan</th>
                                                    <th class="text-end">Standart Cost</th>
                                                    <th class="text-end">Total Cost</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <th colspan="6" class="text-end">
                                                        TOTAL NILAI COST
                                                    </th>
                                                    <th id="ttlcost" class="total-value">
                                                        0
                                                    </th>
                                                </tr>
                                            </tfoot>

                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ====================================================
                            TAB WIP
                            ==================================================== -->
                        <div class="tab-pane fade"
                            id="wip"
                            role="tabpanel">

                            <div class="detail-tab-wrapper">

                                <!-- HEADER -->
                                <div class="detail-tab-header">
                                    <h3 class="tab-title">
                                        <i class="fa fa-industry"></i>
                                        Detail - Barang WIP
                                    </h3>

                                    <div class="detail-tab-actions">
                                        <button type="button"
                                                class="btn-action btn-add"
                                                title="Tambah Data (Ctrl + Q)"
                                                id="btnAddDetailWip">
                                            <i class="fa fa-plus"></i>
                                        </button>

                                        <button type="button"
                                                id="btnUpdateDetailWip"
                                                class="btn-action btn-edit"
                                                title="Update Data"
                                                onclick="updateDetailBomWip()">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <button type="button"
                                                class="btn-action btn-delete"
                                                title="Hapus Data"
                                                onclick="btnDeleteDetailWip()">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- BODY -->
                                <div class="detail-tab-body">
                                    <div class="table-responsive">
                                        <table id="tmp_bomwipdtl"
                                            class="table table-modern">

                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="50">
                                                        <input type="checkbox" id="checkAllWip">
                                                    </th>
                                                    <th>ID Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th class="text-end">Qty</th>
                                                    <th>Satuan</th>
                                                    <th class="text-end">Standart Cost</th>
                                                    <th class="text-end">Total Cost</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <th colspan="6" class="text-end">
                                                        TOTAL NILAI WIP
                                                    </th>
                                                    <th id="ttlwip" class="total-value">
                                                        0
                                                    </th>
                                                </tr>
                                            </tfoot>

                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ====================================================
                            TOTAL BOM
                            ==================================================== -->
                        <div class="total-bom-box">
                            <span class="label">Total BOM :</span>
                            <span class="value" id="ttlprice">0</span>
                        </div>

                    </div>
                </div>

                <!-- ============================================================
                    FOOTER
                    ============================================================ -->
                <div class="card-footer bg-light">

                    <a href="<?= base_url('production/trans/clear_bom_Tmp') ?>"
                    class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a>

                    <?php if ($typeform != 'DETAIL' && $dtldata != null): ?>
                        <button type="submit"
                                onclick="return confirm('Simpan Data?')"
                                class="btn btn-success btn-lg float-right">
                            <i class="fa fa-save"></i>
                            Simpan Final Data
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




<!-- =================MODAL BOM MATERIAL  ================= -->
<div class="modal fade" id="modalDtlBomMaterial" tabindex="-1" role="dialog" aria-labelledby="modalDtlBomMaterial" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDtlBomMaterialTitle">
                    Input Bom Material
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- FORM -->
            <form id="formBOMMaterialDtl">

                <div class="modal-body">

                    <input type="hidden" name="idurut" id="idurutMaterial">
                    <input type="hidden" name="uniqueidMaterial" id="uniqueidMaterial">

                    <!-- ROW 1 -->
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Item ID</label>
                            <select name="idbarangMaterial" id="idbarangMaterial"
                                    class="form-select select2 "
                                    style="width:100%"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Item Name</label>
                            <input type="text"
                                   name="nmbarangmaterial"
                                   id="nmbarangmaterial"
                                   class="form-control"
                                   placeholder="Nama Barang"
                                   style="text-transform:uppercase"
                                   readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">QTY</label>
                            <input type="text"
                                    name="qtymaterial"
                                    id="qtymaterial"
                                    class="form-control jtsseparator ratakanan"
                                    placeholder="Qty Barang"
                                    onchange="hitungTotalCostMaterial()"
                                    onkeyup="hitungTotalCostMaterial()"
                                    style="text-transform:uppercase">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Unit</label>
                            <input name="unitMaterial"
                                   id="unitMaterial"
                                   class="form-control"
                                   readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Standart Cost</label>
                            <input type="text"
                                   name="standartcostmaterial"
                                   id="standartcostmaterial"
                                   onchange="hitungTotalCostMaterial()"
                                   onkeyup="hitungTotalCostMaterial()"
                                   class="form-control jtsseparator ratakanan"
                                   placeholder="Standart Cost"
                                   readonly
                                   style="text-transform:uppercase">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Total Cost</label>
                            <input type="text"
                                   name="totalcostmaterial"
                                   id="totalcostmaterial"
                                   class="form-control jtsseparator ratakanan"
                                   placeholder="Total cost material"
                                   style="text-transform:uppercase" readonly>
                        </div>



                    </div>
                    <!-- ROW 2 -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Description Detail</label>
                            <textarea name="description_detail_material"
                                      id="description_detail_material"
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
                            onclick="saveBomDetail('formBOMMaterialDtl','MATERIAL')">
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





<!-- =================MODAL BOM COST  ================= -->
<div class="modal fade" id="modalDtlBomCost" tabindex="-1" role="dialog" aria-labelledby="modalDtlBomCost" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDtlBomCostTitle">
                    Input Bom Cost
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- FORM -->
            <form id="formBOMCostDtl">

                <div class="modal-body">

                    <input type="hidden" name="idurut" id="idurutCost">
                    <input type="hidden" name="uniqueidCost" id="uniqueidCost">

                    <!-- ROW 1 -->
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Item ID</label>
                            <select name="idbarangCost" id="idbarangCost"
                                    class="form-select select2 "
                                    style="width:100%"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Item Name</label>
                            <input type="text"
                                   name="nmbarangcost"
                                   id="nmbarangcost"
                                   class="form-control"
                                   placeholder="Nama Barang"
                                   style="text-transform:uppercase"
                                   readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">QTY</label>
                            <input type="text"
                                    name="qtycost"
                                    id="qtycost"
                                    class="form-control jtsseparator ratakanan"
                                    placeholder="Qty Barang"
                                    onchange="hitungTotalCostCost()"
                                    onkeyup="hitungTotalCostCost()"
                                    style="text-transform:uppercase">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Unit</label>
                            <input name="unitCost"
                                   id="unitCost"
                                   class="form-control"
                                   readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Standart Cost</label>
                            <input type="text"
                                   name="standartcostcost"
                                   id="standartcostcost"
                                   onchange="hitungTotalCostCost()"
                                   onkeyup="hitungTotalCostCost()"
                                   class="form-control jtsseparator ratakanan"
                                   placeholder="Standart Cost"
                                   readonly
                                   style="text-transform:uppercase">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Total Cost</label>
                            <input type="text"
                                   name="totalcostcost"
                                   id="totalcostcost"
                                   class="form-control jtsseparator ratakanan"
                                   placeholder="Total cost cost"
                                   style="text-transform:uppercase" readonly>
                        </div>



                    </div>
                    <!-- ROW 2 -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Description Detail</label>
                            <textarea name="description_detail_cost"
                                      id="description_detail_cost"
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
                            onclick="saveBomDetail('formBOMCostDtl','COST')">
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




<!-- =================MODAL BOM WIP  ================= -->
<div class="modal fade" id="modalDtlBomWip" tabindex="-1" role="dialog" aria-labelledby="modalDtlBomWip" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDtlBomWipTitle">
                    Input Bom Wip
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- FORM -->
            <form id="formBOMWipDtl">

                <div class="modal-body">

                    <input type="hidden" name="idurut" id="idurutWip">
                    <input type="hidden" name="uniqueidWip" id="uniqueidWip">

                    <!-- ROW 1 -->
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Item ID</label>
                            <select name="idbarangWip" id="idbarangWip"
                                    class="form-select select2 "
                                    style="width:100%"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Item Name</label>
                            <input type="text"
                                   name="nmbarangwip"
                                   id="nmbarangwip"
                                   class="form-control"
                                   placeholder="Nama Barang"
                                   style="text-transform:uppercase"
                                   readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">QTY</label>
                            <input type="text"
                                    name="qtywip"
                                    id="qtywip"
                                    class="form-control jtsseparator ratakanan"
                                    placeholder="Qty Barang"
                                    onchange="hitungTotalCostWip()"
                                    onkeyup="hitungTotalCostWip()"
                                    style="text-transform:uppercase">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Unit</label>
                            <input name="unitWip"
                                   id="unitWip"
                                   class="form-control"
                                   readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Standart Cost</label>
                            <input type="text"
                                   name="standartcostwip"
                                   id="standartcostwip"
                                   onchange="hitungTotalCostWip()"
                                   onkeyup="hitungTotalCostWip()"
                                   class="form-control jtsseparator ratakanan"
                                   placeholder="Standart Cost"
                                   readonly
                                   style="text-transform:uppercase">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Total Cost</label>
                            <input type="text"
                                   name="totalcostwip"
                                   id="totalcostwip"
                                   class="form-control jtsseparator ratakanan"
                                   placeholder="Total cost cost"
                                   style="text-transform:uppercase" readonly>
                        </div>



                    </div>
                    <!-- ROW 2 -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Description Detail</label>
                            <textarea name="description_detail_wip"
                                      id="description_detail_wip"
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
                            onclick="saveBomDetail('formBOMWipDtl','WIP')">
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



<script type="application/javascript" src="<?= base_url('assets/pagejs/production/bom/bom.js') ?>"></script>
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