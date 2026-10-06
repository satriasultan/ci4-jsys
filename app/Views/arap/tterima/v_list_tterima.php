<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1 class="m-0">
                    <?php echo ucwords(strtolower(trim($title))); ?>
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">

                    <div
                            class="float-right"
                            style="margin-right:10px; vertical-align:middle; padding-top:0.7%;"
                    >
                        <i style="color:transparent;">
                            <?php echo $t; ?>
                        </i>
                        Menu ID <?php echo $version; ?>
                    </div>

                    <input
                            type="hidden"
                            id="classmenu"
                            value="<?= str_replace('.', '_', $kodemenu) ?>"
                            required
                    >

                    <?php foreach ($y as $y1) { ?>

                        <?php if (trim($y1->kodemenu) != trim($kodemenu)) { ?>

                            <li class="breadcrumb-item">
                                <a href="<?php echo base_url(trim($y1->linkmenu)); ?>">
                                    <i class="fa <?php echo trim($y1->iconmenu); ?>"></i>
                                    <?php echo trim($y1->namamenu); ?>
                                </a>
                            </li>

                        <?php } else { ?>

                            <li class="breadcrumb-item active">
                                <i class="fa <?php echo trim($y1->iconmenu); ?>"></i>
                                <?php echo trim($y1->namamenu); ?>
                            </li>

                        <?php } ?>

                    <?php } ?>

                </ol>
            </div>

        </div>
    </div>
</div>


<!-- =========================================================
     MESSAGE
========================================================= -->

<?php echo $message; ?>
<?php echo $showUnfinish; ?>


<!-- =========================================================
     MAIN CARD
========================================================= -->

<div class="row">
    <div class="col-sm-12">

        <div class="card shadow-sm">

            <!-- =================================================
                 CARD HEADER
            ================================================== -->

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center">

                    <div class="btn-group">

                        <button
                                type="button"
                                class="btn btn-primary btn-sm dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                        >
                            <i class="fa fa-bars me-1"></i>
                            Menu
                        </button>

                        <div class="dropdown-menu">

                            <?php if (
                                    isset($dtl_akses['a_input']) &&
                                    trim($dtl_akses['a_input']) === 't'
                            ): ?>

                                <a
                                        class="dropdown-item"
                                        href="<?= base_url('arap/transaksi/addTterima') ?>"
                                >
                                    <i class="fa fa-plus text-success me-1"></i>
                                    Input
                                </a>

                            <?php endif; ?>


                            <a
                                    class="dropdown-item"
                                    href="#"
                                    onclick="reload_tableTterimaTrx(); return false;"
                            >
                                <i class="fa fa-refresh text-primary me-1"></i>
                                Reload
                            </a>

                        </div>

                    </div>


                    <!-- TRACEABILITY INFO -->

                    <div class="text-muted small d-none d-md-block">

                        <i class="fa fa-link me-1"></i>

                        Document Traceability

                    </div>

                </div>

            </div>


            <!-- =================================================
                 CARD BODY
            ================================================== -->

            <div class="card-body">

                <div
                        class="tab-pane fade show active"
                        id="tterima-content"
                        role="tabpanel"
                >

                    <div class="row">

                        <div class="col-md-12">

                            <div
                                    class="table-responsive"
                                    style="overflow-x:auto;"
                            >

                                <div class="table-responsive w-100">
                                    <table
                                            id="tableTterimaTrx"
                                            class="table table-bordered table-striped table-hover align-middle w-100"
                                    >
                                        <thead class="table-dark">
                                        <tr>
                                            <th class="text-center" style="width:50px;">
                                                No.
                                            </th>

                                            <th class="text-center" style="width:100px;">
                                                Action
                                            </th>

                                            <th class="text-center">
                                                Document
                                            </th>

                                            <th class="text-center">
                                                Tanggal
                                            </th>
                                            <th class="text-center">
                                                Nama Supplier
                                            </th>


                                            <th class="text-center">
                                                Currency
                                            </th>

                                            <th class="text-center">
                                                Nilai
                                            </th>

                                            <th class="text-center">
                                                Keterangan
                                            </th>

                                            <th class="text-center">
                                                Status Dokumen
                                            </th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>



<!-- =========================================================
     MODAL TRACEABILITY DOKUMEN
========================================================= -->

<div
        class="modal fade"
        id="modalTraceability"
        tabindex="-1"
        aria-labelledby="modalTraceabilityLabel"
        aria-hidden="true"
>

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">


            <!-- =================================================
                 MODAL HEADER
            ================================================== -->

            <div class="modal-header bg-primary text-white">

                <div>

                    <h5
                            class="modal-title mb-0"
                            id="modalTraceabilityLabel"
                    >
                        <i class="fa fa-link me-2"></i>
                        Document Traceability
                    </h5>

                    <small class="opacity-75">
                        Pemeriksaan kelengkapan dokumen Tanda Terima Supplier
                    </small>

                </div>


                <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                ></button>

            </div>


            <!-- =================================================
                 MODAL BODY
            ================================================== -->

            <div class="modal-body">


                <!-- =============================================
                     DOCUMENT INFORMATION
                ============================================== -->

                <div class="card border mb-3">

                    <div class="card-header bg-light">

                        <strong>
                            <i class="fa fa-file-text-o me-1"></i>
                            Informasi Tanda Terima
                        </strong>

                    </div>


                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-md-3">

                                <label class="form-label small text-muted">
                                    Document
                                </label>

                                <input
                                        type="text"
                                        id="trace_docno"
                                        class="form-control form-control-sm"
                                        readonly
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label small text-muted">
                                    Tanggal
                                </label>

                                <input
                                        type="text"
                                        id="trace_docdate"
                                        class="form-control form-control-sm"
                                        readonly
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label small text-muted">
                                    Supplier
                                </label>

                                <input
                                        type="text"
                                        id="trace_supplier"
                                        class="form-control form-control-sm"
                                        readonly
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label small text-muted">
                                    Status
                                </label>

                                <input
                                        type="text"
                                        id="trace_status"
                                        class="form-control form-control-sm"
                                        readonly
                                >

                            </div>

                        </div>

                    </div>

                </div>



                <!-- =============================================
                     TRACEABILITY SUMMARY
                ============================================== -->

                <div class="row g-3 mb-3">


                    <!-- TOTAL -->

                    <div class="col-md-3">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body">

                                <div class="d-flex align-items-center">

                                    <div
                                            class="rounded-circle bg-primary bg-opacity-10 p-3 me-3"
                                    >
                                        <i class="fa fa-files-o text-primary"></i>
                                    </div>

                                    <div>

                                        <div class="small text-muted">
                                            Total Dokumen
                                        </div>

                                        <div
                                                class="fs-4 fw-bold"
                                                id="trace_total"
                                        >
                                            0
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- COMPLETE -->

                    <div class="col-md-3">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body">

                                <div class="d-flex align-items-center">

                                    <div
                                            class="rounded-circle bg-success bg-opacity-10 p-3 me-3"
                                    >
                                        <i class="fa fa-check text-success"></i>
                                    </div>

                                    <div>

                                        <div class="small text-muted">
                                            Lengkap
                                        </div>

                                        <div
                                                class="fs-4 fw-bold text-success"
                                                id="trace_complete"
                                        >
                                            0
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- INCOMPLETE -->

                    <div class="col-md-3">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body">

                                <div class="d-flex align-items-center">

                                    <div
                                            class="rounded-circle bg-danger bg-opacity-10 p-3 me-3"
                                    >
                                        <i class="fa fa-times text-danger"></i>
                                    </div>

                                    <div>

                                        <div class="small text-muted">
                                            Belum Ada
                                        </div>

                                        <div
                                                class="fs-4 fw-bold text-danger"
                                                id="trace_missing"
                                        >
                                            0
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- PROGRESS -->

                    <div class="col-md-3">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body">

                                <div class="small text-muted mb-1">
                                    Kelengkapan
                                </div>

                                <div
                                        class="progress"
                                        style="height:8px;"
                                >

                                    <div
                                            id="trace_progress"
                                            class="progress-bar"
                                            role="progressbar"
                                            style="width:0%;"
                                    ></div>

                                </div>

                                <div
                                        class="text-end small mt-1"
                                        id="trace_progress_text"
                                >
                                    0%
                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- =============================================
                     CHECKLIST DOKUMEN
                ============================================== -->

                <div class="card border">

                    <div class="card-header bg-light">

                        <div class="d-flex justify-content-between align-items-center">

                            <strong>
                                <i class="fa fa-check-square-o me-1"></i>
                                Checklist Kelengkapan Dokumen
                            </strong>


                            <span
                                    class="badge bg-secondary"
                                    id="trace_check_status"
                            >
                                Belum Dicek
                            </span>

                        </div>

                    </div>


                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table
                                    class="table table-bordered table-hover mb-0"
                                    id="tableTraceability"
                            >

                                <thead class="table-light text-center align-middle">

                                <tr>

                                    <th style="width:60px;">
                                        No.
                                    </th>

                                    <th style="width:220px;">
                                        Jenis Dokumen
                                    </th>

                                    <th style="width:180px;">
                                        Nomor Dokumen
                                    </th>

                                    <th style="width:130px;">
                                        Tanggal
                                    </th>

                                    <th style="width:150px;">
                                        Status
                                    </th>

                                    <th>
                                        Keterangan
                                    </th>

                                </tr>

                                </thead>


                                <tbody>


                                <!-- INVOICE -->

                                <tr
                                        data-document="invoice"
                                >

                                    <td class="text-center">
                                        1
                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            <i class="fa fa-file-text-o text-primary me-1"></i>
                                            Invoice
                                        </div>

                                        <small class="text-muted">
                                            Dokumen tagihan supplier
                                        </small>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-no"
                                                id="trace_noinvoice"
                                                placeholder="Nomor invoice"
                                        >

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-date"
                                                id="trace_tglinvoice"
                                                placeholder="DD-MM-YYYY"
                                        >

                                    </td>

                                    <td>

                                        <select
                                                class="form-select form-select-sm trace-status"
                                                id="trace_status_invoice"
                                        >

                                            <option value="BELUM">
                                                Belum Ada
                                            </option>

                                            <option value="ADA">
                                                Ada
                                            </option>

                                            <option value="NA">
                                                Tidak Berlaku
                                            </option>

                                        </select>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-note"
                                                id="trace_note_invoice"
                                                placeholder="Keterangan"
                                        >

                                    </td>

                                </tr>



                                <!-- FAKTUR PAJAK -->

                                <tr
                                        data-document="faktur_pajak"
                                >

                                    <td class="text-center">
                                        2
                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            <i class="fa fa-file-text-o text-danger me-1"></i>
                                            Faktur Pajak / FP
                                        </div>

                                        <small class="text-muted">
                                            Dokumen perpajakan
                                        </small>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-no"
                                                id="trace_nofp"
                                                placeholder="Nomor faktur pajak"
                                        >

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-date"
                                                id="trace_tglfp"
                                                placeholder="DD-MM-YYYY"
                                        >

                                    </td>

                                    <td>

                                        <select
                                                class="form-select form-select-sm trace-status"
                                                id="trace_status_fp"
                                        >

                                            <option value="BELUM">
                                                Belum Ada
                                            </option>

                                            <option value="ADA">
                                                Ada
                                            </option>

                                            <option value="NA">
                                                Tidak Berlaku
                                            </option>

                                        </select>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-note"
                                                id="trace_note_fp"
                                                placeholder="Keterangan"
                                        >

                                    </td>

                                </tr>



                                <!-- SURAT JALAN -->

                                <tr
                                        data-document="surat_jalan"
                                >

                                    <td class="text-center">
                                        3
                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            <i class="fa fa-truck text-warning me-1"></i>
                                            Surat Jalan
                                        </div>

                                        <small class="text-muted">
                                            Bukti pengiriman barang
                                        </small>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-no"
                                                id="trace_nosj"
                                                placeholder="Nomor surat jalan"
                                        >

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-date"
                                                id="trace_tglsj"
                                                placeholder="DD-MM-YYYY"
                                        >

                                    </td>

                                    <td>

                                        <select
                                                class="form-select form-select-sm trace-status"
                                                id="trace_status_sj"
                                        >

                                            <option value="BELUM">
                                                Belum Ada
                                            </option>

                                            <option value="ADA">
                                                Ada
                                            </option>

                                            <option value="NA">
                                                Tidak Berlaku
                                            </option>

                                        </select>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-note"
                                                id="trace_note_sj"
                                                placeholder="Keterangan"
                                        >

                                    </td>

                                </tr>



                                <!-- PURCHASE ORDER -->

                                <tr
                                        data-document="purchase_order"
                                >

                                    <td class="text-center">
                                        4
                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            <i class="fa fa-shopping-cart text-info me-1"></i>
                                            Purchase Order / PO
                                        </div>

                                        <small class="text-muted">
                                            Referensi pemesanan
                                        </small>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-no"
                                                id="trace_nopo"
                                                placeholder="Nomor PO"
                                        >

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-date"
                                                id="trace_tglpo"
                                                placeholder="DD-MM-YYYY"
                                        >

                                    </td>

                                    <td>

                                        <select
                                                class="form-select form-select-sm trace-status"
                                                id="trace_status_po"
                                        >

                                            <option value="BELUM">
                                                Belum Ada
                                            </option>

                                            <option value="ADA">
                                                Ada
                                            </option>

                                            <option value="NA">
                                                Tidak Berlaku
                                            </option>

                                        </select>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-note"
                                                id="trace_note_po"
                                                placeholder="Keterangan"
                                        >

                                    </td>

                                </tr>



                                <!-- BAST -->

                                <tr
                                        data-document="bast"
                                >

                                    <td class="text-center">
                                        5
                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            <i class="fa fa-file-text text-success me-1"></i>
                                            BAST
                                        </div>

                                        <small class="text-muted">
                                            Berita Acara Serah Terima
                                        </small>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-no"
                                                id="trace_nobast"
                                                placeholder="Nomor BAST"
                                        >

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-date"
                                                id="trace_tglbast"
                                                placeholder="DD-MM-YYYY"
                                        >

                                    </td>

                                    <td>

                                        <select
                                                class="form-select form-select-sm trace-status"
                                                id="trace_status_bast"
                                        >

                                            <option value="BELUM">
                                                Belum Ada
                                            </option>

                                            <option value="ADA">
                                                Ada
                                            </option>

                                            <option value="NA">
                                                Tidak Berlaku
                                            </option>

                                        </select>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-note"
                                                id="trace_note_bast"
                                                placeholder="Keterangan"
                                        >

                                    </td>

                                </tr>



                                <!-- DOKUMEN PENDUKUNG -->

                                <tr
                                        data-document="pendukung"
                                >

                                    <td class="text-center">
                                        6
                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            <i class="fa fa-paperclip text-secondary me-1"></i>
                                            Dokumen Pendukung
                                        </div>

                                        <small class="text-muted">
                                            Dokumen tambahan
                                        </small>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-no"
                                                id="trace_nopendukung"
                                                placeholder="Nomor dokumen"
                                        >

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-date"
                                                id="trace_tglpendukung"
                                                placeholder="DD-MM-YYYY"
                                        >

                                    </td>

                                    <td>

                                        <select
                                                class="form-select form-select-sm trace-status"
                                                id="trace_status_pendukung"
                                        >

                                            <option value="BELUM">
                                                Belum Ada
                                            </option>

                                            <option value="ADA">
                                                Ada
                                            </option>

                                            <option value="NA">
                                                Tidak Berlaku
                                            </option>

                                        </select>

                                    </td>

                                    <td>

                                        <input
                                                type="text"
                                                class="form-control form-control-sm trace-note"
                                                id="trace_note_pendukung"
                                                placeholder="Keterangan"
                                        >

                                    </td>

                                </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>



                <!-- =============================================
                     TRACEABILITY NOTE
                ============================================== -->

                <div class="alert alert-info mt-3 mb-0">

                    <div class="d-flex">

                        <div class="me-2">
                            <i class="fa fa-info-circle"></i>
                        </div>

                        <div>

                            <strong>Document Traceability</strong>

                            <div class="small mt-1">

                                Checklist ini digunakan untuk memastikan
                                dokumen pendukung Tanda Terima telah tersedia
                                sebelum proses verifikasi / pembayaran.

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 MODAL FOOTER
            ================================================== -->

            <div class="modal-footer">

                <button
                        type="button"
                        class="btn btn-secondary btn-sm"
                        data-bs-dismiss="modal"
                >
                    <i class="fa fa-times me-1"></i>
                    Tutup
                </button>


                <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        id="btnSaveTraceability"
                >
                    <i class="fa fa-save me-1"></i>
                    Simpan Checklist
                </button>

            </div>

        </div>

    </div>

</div>



<!-- =========================================================
     MODAL FILTER
========================================================= -->

<div
        class="modal fade"
        id="filter"
        tabindex="-1"
        aria-labelledby="filterLabel"
        aria-hidden="true"
>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form id="form-filter">

                <div class="modal-header">

                    <h5
                            class="modal-title"
                            id="filterLabel"
                    >
                        <i class="fa fa-filter me-1"></i>
                        Filtering Tanda Terima
                    </h5>

                    <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                    ></button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">


                        <!-- TANGGAL -->

                        <div class="col-md-6">

                            <label class="form-label">
                                Tanggal Dokumen
                            </label>

                            <input
                                    type="text"
                                    class="form-control tglrange"
                                    id="tglrange"
                                    name="tglrange"
                                    value=""
                                    data-date-format="dd-mm-yyyy"
                            >

                        </div>


                        <!-- STATUS -->

                        <div class="col-md-6">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                    class="form-select"
                                    id="status_filter"
                                    name="status_filter"
                            >

                                <option value="ALL">
                                    Semua Status
                                </option>

                                <option value="I">
                                    DRAFT USER
                                </option>

                                <option value="C">
                                    CLOSE
                                </option>

                                <option value="O">
                                    OPEN
                                </option>

                                <option value="R">
                                    BATAL
                                </option>

                            </select>

                        </div>


                        <!-- SUPPLIER -->

                        <div class="col-md-6">

                            <label class="form-label">
                                Kode Supplier
                            </label>

                            <input
                                    type="text"
                                    class="form-control"
                                    id="kdsupplier_filter"
                                    name="kdsupplier"
                                    placeholder="Kode supplier"
                            >

                        </div>


                        <!-- INVOICE -->

                        <div class="col-md-6">

                            <label class="form-label">
                                No. Invoice
                            </label>

                            <input
                                    type="text"
                                    class="form-control"
                                    id="noinvoice_filter"
                                    name="noinvoice"
                                    placeholder="Nomor invoice"
                            >

                        </div>

                    </div>

                </div>


                <div class="modal-footer justify-content-between">

                    <button
                            type="button"
                            id="btn-reset"
                            class="btn btn-light btn-sm"
                    >
                        <i class="fa fa-refresh me-1"></i>
                        Reset
                    </button>


                    <button
                            type="button"
                            id="btn-filter"
                            class="btn btn-primary btn-sm"
                    >
                        <i class="fa fa-search me-1"></i>
                        Filter
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



<!-- =========================================================
     TRACEABILITY HELPER
========================================================= -->

<script>

    function openTterimaTraceability(data)
    {
        /*
         * Fungsi ini sengaja dibuat generic.
         *
         * Nantinya tterima.js dapat memanggil:
         *
         * openTterimaTraceability({
         *     docno: row.docno,
         *     docdate: row.docdate,
         *     supplier: row.nmsupplier,
         *     status: row.status
         * });
         */

        data = data || {};

        $('#trace_docno').val(
            data.docno || ''
        );

        $('#trace_docdate').val(
            data.docdate || ''
        );

        $('#trace_supplier').val(
            data.supplier || data.nmsupplier || ''
        );

        $('#trace_status').val(
            data.status || ''
        );


        /*
         * Reset summary.
         */

        $('#trace_total').text('6');
        $('#trace_complete').text('0');
        $('#trace_missing').text('0');

        $('#trace_progress')
            .css('width', '0%');

        $('#trace_progress_text')
            .text('0%');

        $('#trace_check_status')
            .removeClass(
                'bg-success bg-danger bg-warning'
            )
            .addClass('bg-secondary')
            .text('Belum Dicek');


        /*
         * Reset checklist.
         */

        $('#tableTraceability .trace-status')
            .val('BELUM');

        $('#tableTraceability .trace-no')
            .val('');

        $('#tableTraceability .trace-date')
            .val('');

        $('#tableTraceability .trace-note')
            .val('');


        /*
         * Tampilkan modal.
         */

        const modalElement =
            document.getElementById(
                'modalTraceability'
            );

        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

        modal.show();
    }


    /*
     * Hitung summary checklist.
     */

    function calculateTraceability()
    {
        const total =
            $('#tableTraceability .trace-status').length;

        let complete = 0;
        let missing = 0;


        $('#tableTraceability .trace-status')
            .each(function () {

                const status =
                    $(this).val();

                if (status === 'ADA') {
                    complete++;
                }

                if (status === 'BELUM') {
                    missing++;
                }

            });


        const percentage =
            total > 0
                ? Math.round(
                    (
                        complete /
                        total
                    ) * 100
                )
                : 0;


        $('#trace_total')
            .text(total);

        $('#trace_complete')
            .text(complete);

        $('#trace_missing')
            .text(missing);


        $('#trace_progress')
            .css(
                'width',
                percentage + '%'
            );


        $('#trace_progress_text')
            .text(
                percentage + '%'
            );


        const $status =
            $('#trace_check_status');


        $status
            .removeClass(
                'bg-secondary bg-success bg-danger bg-warning'
            );


        if (percentage === 100) {

            $status
                .addClass('bg-success')
                .text('Lengkap');

        } else if (complete > 0) {

            $status
                .addClass('bg-warning')
                .text('Sebagian');

        } else {

            $status
                .addClass('bg-danger')
                .text('Belum Lengkap');

        }
    }


    /*
     * Update otomatis ketika checklist berubah.
     */

    $(document).on(
        'change',
        '#tableTraceability .trace-status',
        function () {

            calculateTraceability();

        }
    );


    $(document).ready(function () {

        /*
         * DataTables existing.
         */

        if (
            $.fn.DataTable &&
            !$.fn.DataTable.isDataTable(
                '#tableTterimaTrx'
            )
        ) {

            $('#tableTterimaTrx').DataTable({
                processing: true,
                serverSide: true,

                ajax: {
                    url:
                        HOST_URL +
                        'arap/transaksi/listTterimaTrx',

                    type: 'POST'
                }
            });

        }


        /*
         * Date range filter.
         */

        if (
            $.fn.daterangepicker
        ) {

            $('.tglrange').daterangepicker({

                autoUpdateInput: false,

                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD-MM-YYYY'
                }

            });


            $('.tglrange').on(
                'apply.daterangepicker',
                function (
                    ev,
                    picker
                ) {

                    $(this).val(
                        picker.startDate.format(
                            'DD-MM-YYYY'
                        ) +
                        ' - ' +
                        picker.endDate.format(
                            'DD-MM-YYYY'
                        )
                    );

                }
            );


            $('.tglrange').on(
                'cancel.daterangepicker',
                function () {

                    $(this).val('');

                }
            );

        }


        /*
         * Tombol reset filter.
         */

        $('#btn-reset').on(
            'click',
            function () {

                $('#form-filter')[0].reset();

                $('.tglrange').val('');

                reload_tableTterimaTrx();

            }
        );


        /*
         * Tombol filter.
         *
         * Menggunakan parameter existing
         * DataTables.
         */

        $('#btn-filter').on(
            'click',
            function () {

                if (
                    $.fn.DataTable.isDataTable(
                        '#tableTterimaTrx'
                    )
                ) {

                    const table =
                        $('#tableTterimaTrx')
                            .DataTable();

                    table.ajax.reload(
                        null,
                        false
                    );

                }

                const modalElement =
                    document.getElementById(
                        'filter'
                    );

                const modal =
                    bootstrap.Modal.getInstance(
                        modalElement
                    );

                if (modal) {
                    modal.hide();
                }

            }
        );


        /*
         * Save checklist.
         *
         * Untuk tahap pertama hanya
         * melakukan validasi UI.
         *
         * Endpoint backend dapat
         * ditambahkan di tterima.js.
         */

        $('#btnSaveTraceability').on(
            'click',
            function () {

                calculateTraceability();

                const total =
                    $('#tableTraceability .trace-status').length;

                const complete =
                    $('#tableTraceability .trace-status')
                        .filter(function () {
                            return $(this).val() === 'ADA';
                        })
                        .length;


                if (complete === 0) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Dokumen Belum Dicek',
                        text:
                            'Belum ada dokumen yang ditandai tersedia.'
                    });

                    return;

                }


                Swal.fire({
                    icon: 'success',
                    title: 'Checklist Siap Disimpan',
                    text:
                        'Checklist traceability sudah divalidasi.'
                });

            }
        );

    });

</script>


<!-- =========================================================
     MAIN JAVASCRIPT
========================================================= -->

<script
        type="application/javascript"
        src="<?= base_url('assets/pagejs/arap/tterima.js') ?>"
></script>
