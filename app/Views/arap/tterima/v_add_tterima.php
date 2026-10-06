
<style>
    /* =========================================================
       CHECKLIST DOKUMEN - VERTICAL
    ========================================================= */
    .document-check-header {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .document-check-header-title {
        margin-bottom: 8px;
        font-weight: 600;
    }

    .document-check-items {
        display: flex !important;
        flex-direction: column !important;
        width: 100%;
        gap: 6px;
    }

    .document-check-item {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        min-height: 38px;
        padding: 7px 10px;
        border: 1px solid #dee2e6;
        background: #fff;
    }

    .document-check-item .form-check {
        display: flex;
        align-items: center;
        margin: 0;
        min-width: 0;
    }

    .document-check-item .form-check-label {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        cursor: pointer;
        white-space: nowrap;
    }

    .document-check-item .form-check-input {
        margin-top: 0;
        flex-shrink: 0;
    }

    .document-check-status {
        flex-shrink: 0;
        margin-left: 10px;
        font-size: 11px;
        white-space: nowrap;
    }

    .document-check-footer {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }

    .check-inline-control {
        min-width: 110px;
    }

    @media (max-width: 991.98px) {
        .document-check-item {
            min-height: 40px;
        }
    }

    <style>
         /* =========================================================
            SELECT2 - Z INDEX PALING BELAKANG
         ========================================================= */

     .select2-container {
         z-index: 1 !important;
     }

    .select2-container--open {
        z-index: 1 !important;
    }

    .select2-dropdown {
        z-index: 1 !important;
    }

    .select2-container .select2-selection {
        z-index: 1 !important;
    }

    .select2-container .select2-results {
        z-index: 1 !important;
    }


    /* =========================================================
       SELECT2 DI HALAMAN UTAMA
       HARUS BERADA DI BAWAH MODAL BACKDROP
    ========================================================= */

    .select2-container {
        z-index: 1 !important;
    }

    .select2-container--open {
        z-index: 1 !important;
    }

    .select2-dropdown {
        z-index: 1 !important;
    }

    /* =========================================================
       SELECT2 DI DALAM MODAL
       TETAP DI ATAS MODAL CONTENT
    ========================================================= */

    .modal .select2-container {
        z-index: 1056 !important;
    }

    .modal .select2-container--open {
        z-index: 1056 !important;
    }

    .modal .select2-dropdown {
        z-index: 1057 !important;
    }
</style>

<style>
    /* =========================================================
       CHECKLIST CARD - FOOTER STICK TO BOTTOM
    ========================================================= */
    .tt-card.checklist-card {
        display: flex;
        flex-direction: column;
    }

    .tt-card.checklist-card .card-body {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
    }

    .tt-card.checklist-card .document-check-header {
        flex: 1 1 auto;
    }

    .tt-card.checklist-card .checklist-card-footer {
        margin-top: auto !important;
        flex-shrink: 0;
    }
</style>



<style>
    /* =========================================================
       FOOTER CARD CHECKLIST
    ========================================================= */
    .checklist-card-footer {
        padding: 10px 14px !important;
        background: #f8f9fa !important;
        border-top: 1px solid #dee2e6 !important;
    }

    .checklist-card-footer .btn {
        min-width: 88px;
        font-weight: 600;
    }

    .checklist-card-footer #btnCetakTterima {
        min-width: 150px;
    }

    @media (max-width: 575.98px) {
        .checklist-card-footer .d-flex {
            justify-content: stretch !important;
        }

        .checklist-card-footer .btn {
            flex: 1 1 0;
        }
    }

    @media print {
        .checklist-card-footer {
            display: none !important;
        }
    }
</style>



<style>
    /* =========================================================
       CLEAN CARD HEADER
       - tanpa icon
       - tanpa subtitle
       - teks kontras
       - ringkas
    ========================================================= */
    .tt-header-clean {
        min-height: 52px;
        padding: 10px 14px !important;
        background: #4f5968 !important;
        border-bottom: 0 !important;
    }

    .tt-header-clean .tt-card-title {
        color: #ffffff !important;
        font-size: 15px !important;
        line-height: 1.25;
        font-weight: 600 !important;
        letter-spacing: .1px;
        margin: 0 !important;
    }

    .tt-header-clean #lbl_status {
        color: #ffffff !important;
        background: rgba(255,255,255,.14) !important;
        border: 1px solid rgba(255,255,255,.35) !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        padding: 4px 8px !important;
        line-height: 1;
    }

    /* Header card 3 juga dibuat konsisten, tetapi tetap mempertahankan
       judul fungsionalnya. */
    .tt-card > .card-header .tt-header-main {
        min-height: 32px;
    }

    .tt-card > .card-header .tt-header-main .tt-card-title {
        color: #ffffff;
        font-weight: 600;
    }
</style>



<style>
    /* =========================================================
       CHECKLIST DOKUMEN - CHECKED HIJAU MUDA
       LETAKKAN PALING BAWAH / SETELAH CSS CHECKLIST LAIN
    ========================================================= */

    .document-check-items .document-check-item.checked {
        background-color: #edf8f0 !important;
        background: #edf8f0 !important;

        border: 1px solid #46bd5e !important;
        border-color: #79d390 !important;

        color: #495057 !important;
        box-shadow: none !important;
    }

    /* Label tetap warna normal */
    .document-check-items
    .document-check-item.checked
    .form-check-label {
        color: #495057 !important;
    }

    /* Icon tetap warna normal */
    .document-check-items
    .document-check-item.checked
    .form-check-label i {
        color: #495057 !important;
    }

    /* Status OK tetap warna abu-abu */
    .document-check-items
    .document-check-item.checked
    .document-check-status {
        color: #6c757d !important;
        background: transparent !important;
        border: 0 !important;
        font-weight: 500 !important;
    }

    /* Checkbox */
    .document-check-items
    .document-check-item.checked
    .form-check-input {
        accent-color: #198754;
    }

    /* =========================================================
       ITEM BELUM
    ========================================================= */

    .document-check-items
    .document-check-item:not(.checked) {
        background-color: #ffffff !important;
        background: #ffffff !important;

        border-color: #e1e5e9 !important;
        color: #495057 !important;
    }

    .document-check-items
    .document-check-item:not(.checked)
    .document-check-status {
        color: #6c757d !important;
        background: transparent !important;
    }
</style>



<style>
    /* =========================================================
       FINAL CLEANUP - CHECKLIST DOKUMEN
       Status OK dibuat netral / tanpa warna
    ========================================================= */

    .document-check-items {
        gap: 5px !important;
    }

    .document-check-item {
        min-height: 36px !important;
        padding: 6px 9px !important;
        border: 1px solid #e1e5e9 !important;
        background: #fff !important;
        color: #495057 !important;
        border-radius: 0 !important;
    }

    .document-check-item .form-check-label {
        font-size: 13px;
        font-weight: 500;
    }

    .document-check-item .form-check-label i {
        color: #6c757d !important;
        width: 15px;
        text-align: center;
    }

    /* Jangan beri warna background/border saat checked */
    .document-check-item.checked {
        background: #fff !important;
        border-color: #e1e5e9 !important;
        color: #495057 !important;
        box-shadow: none !important;
    }

    /* Status OK = netral */
    .document-check-item.checked .document-check-status,
    .document-check-status {
        color: #6c757d !important;
        background: transparent !important;
        border: 0 !important;
        font-weight: 500;
    }

    /* Checkbox tetap menggunakan tampilan Bootstrap normal */
    .document-check-item .form-check-input {
        width: 15px;
        height: 15px;
        margin-right: 7px;
    }

    /* Footer checklist lebih rapi */
    .document-check-footer {
        margin-top: 10px !important;
        padding-top: 9px;
        border-top: 1px solid #e9ecef;
        gap: 7px !important;
        font-size: 12px;
    }

    .check-inline-control {
        height: 30px;
    }

    /* Hilangkan efek warna status item dari CSS lama */
    .document-check-item.checked::before,
    .document-check-item.checked::after {
        background: transparent !important;
        color: inherit !important;
        border-color: transparent !important;
    }

    /* Card checklist tetap bersih */
    .document-check-header-title {
        color: #495057 !important;
        font-size: 13px;
        margin-bottom: 8px !important;
    }
</style>
<style>
    /* =========================================================
       SELECT2 DALAM MODAL
       FIX POSITION DROPDOWN
    ========================================================= */

    #modalUpdateTterimaDtl .select2-container {
        width: 100% !important;
    }

    #modalUpdateTterimaDtl .select2-container--open {
        width: 100% !important;
    }

    #modalUpdateTterimaDtl .select2-dropdown {
        width: 100% !important;
        left: 0 !important;
        box-sizing: border-box !important;
    }

    #modalUpdateTterimaDtl .select2-container--open
    .select2-dropdown--below {
        top: 100% !important;
    }

    #modalUpdateTterimaDtl .select2-container--open
    .select2-dropdown--above {
        top: auto !important;
        bottom: 100% !important;
    }
</style>



<style>
    /* =========================================================
       TANDA TERIMA - CARD BACKGROUND
    ========================================================= */
    .tt-card {
        background: #eef1f4 !important;
        border-color: #d5dbe1 !important;
    }

    .tt-card .card-body {
        background: #eef1f4 !important;
    }

    .tt-card .card-footer {
        background: #e5e9ed !important;
        border-top-color: #d5dbe1 !important;
    }

    /* Area input tetap putih agar field mudah dibaca */
    .tt-card .form-control,
    .tt-card .form-select,
    .tt-card .input-group-text {
        background-color: #ffffff;
    }

    /* Field disabled tetap sedikit abu-abu */
    .tt-card .form-control:disabled,
    .tt-card .form-control[readonly] {
        background-color: #e3e7eb;
    }

    /* DOKUMEN PENDUKUNG / REFERENSI */
    .tt-reference-box {
        border-top: 1px solid #d5dbe1;
        margin-top: 10px;
        padding-top: 10px;
    }

    .tt-reference-box .form-label {
        margin-bottom: 4px;
    }
</style>

<style>
    /* =========================================================
       TANDA TERIMA - CORPORATE UI
       Komposisi field/ID tetap dipertahankan
    ========================================================= */

    :root {
        --tt-red: #b5121b;
        --tt-red-dark: #8f0e15;
        --tt-navy: #46515f;
        --tt-card: #eef1f4;
        --tt-border: #d7dde3;
        --tt-border-soft: #e2e6ea;
        --tt-text: #343a40;
        --tt-muted: #6c757d;
        --tt-green-bg: #edf8f0;
        --tt-green-border: #c9e8d0;
    }

    /* ---------- CARD ---------- */
    .tt-card {
        background: var(--tt-card) !important;
        border: 1px solid var(--tt-border) !important;
        border-radius: 2px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        overflow: hidden;
    }

    .tt-card .card-body {
        background: var(--tt-card) !important;
    }

    .tt-card .card-footer {
        background: #e5e9ed !important;
        border-top: 1px solid var(--tt-border) !important;
    }

    /* ---------- CARD HEADER ---------- */
    .tt-header-clean {
        min-height: 50px;
        padding: 10px 14px !important;
        background: var(--tt-navy) !important;
        border: 0 !important;
    }

    .tt-header-clean .tt-card-title,
    .tt-card > .card-header .tt-card-title {
        color: #fff !important;
        font-size: 14px !important;
        line-height: 1.25;
        font-weight: 600 !important;
        letter-spacing: .15px;
        margin: 0 !important;
    }

    .tt-header-clean #lbl_status {
        color: #fff !important;
        background: rgba(255,255,255,.12) !important;
        border: 1px solid rgba(255,255,255,.28) !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        padding: 4px 8px !important;
        line-height: 1;
    }

    .tt-card > .card-header:not(.tt-header-clean) {
        padding: 9px 14px !important;
        background: var(--tt-navy) !important;
        border-bottom: 0 !important;
    }

    /* ---------- LABEL & FIELD ---------- */
    .tt-card .form-label {
        margin-bottom: 5px;
        color: #495057;
        font-size: 12px;
        font-weight: 600;
    }

    .tt-card .form-control,
    .tt-card .form-select,
    .tt-card .input-group-text {
        border-color: #cfd6dd;
        border-radius: 2px !important;
        background-color: #fff;
        color: var(--tt-text);
    }

    .tt-card .form-control,
    .tt-card .form-select {
        min-height: 34px;
        font-size: 12px;
    }

    .tt-card textarea.form-control {
        min-height: 58px;
        resize: vertical;
    }

    .tt-card .form-control:focus,
    .tt-card .form-select:focus {
        border-color: #9faab5;
        box-shadow: 0 0 0 .12rem rgba(70,81,95,.12);
    }

    .tt-card .form-control:disabled,
    .tt-card .form-control[readonly] {
        background-color: #e3e7eb;
        color: #000000;
    }

    /* ---------- DOCUMENT NUMBER ---------- */
    .doc-number .input-group-text {
        min-width: 18px;
        justify-content: center;
        padding: 0 4px;
        color: #7a838d;
        background: #e8ebee;
        border-color: #cfd6dd;
    }

    /* ---------- VALUE FIELDS ---------- */
    #dpp,
    #jumlahpajak,
    #total {
        font-weight: 600;
    }

    #total {
        border-color: #aeb7c0;
        background: #f9fafb !important;
    }

    /* ---------- REFERENCE AREA ---------- */
    .tt-reference-box {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--tt-border);
    }

    .tt-reference-box .form-label {
        margin-bottom: 4px;
        font-size: 11px;
    }

    /* ---------- CHECKLIST ---------- */
    .tt-card.checklist-card {
        display: flex;
        flex-direction: column;
    }

    .tt-card.checklist-card > .card-body {
        flex: 1 1 auto;
        min-height: 0;
        display: flex;
        flex-direction: column;
    }

    .document-check-header {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
    }

    .document-check-header-title {
        display: none;
    }

    .document-check-items {
        display: flex !important;
        flex-direction: column !important;
        width: 100%;
        gap: 5px !important;
    }

    .document-check-item {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        min-height: 35px !important;
        padding: 6px 9px !important;
        border: 1px solid var(--tt-border-soft) !important;
        border-radius: 2px !important;
        background: #fff !important;
        color: var(--tt-text) !important;
        transition: background-color .12s ease, border-color .12s ease;
    }

    .document-check-item .form-check {
        display: flex;
        align-items: center;
        min-width: 0;
        margin: 0;
    }

    .document-check-item .form-check-label {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        cursor: pointer;
        white-space: nowrap;
        color: #495057 !important;
        font-size: 12px;
        font-weight: 500;
    }

    .document-check-item .form-check-label i {
        width: 15px;
        text-align: center;
        color: #7a838d !important;
    }

    .document-check-item .form-check-input {
        width: 15px;
        height: 15px;
        margin-top: 0;
        margin-right: 7px;
        flex-shrink: 0;
    }

    .document-check-status {
        flex-shrink: 0;
        margin-left: 10px;
        font-size: 11px;
        font-weight: 500;
        color: #7a838d !important;
        background: transparent !important;
        border: 0 !important;
        white-space: nowrap;
    }

    /* CHECKED = hijau muda pupus, font tetap normal */
    .document-check-item.checked {
        background: var(--tt-green-bg) !important;
        border-color: var(--tt-green-border) !important;
        color: var(--tt-text) !important;
        box-shadow: none !important;
    }

    .document-check-item.checked .form-check-label,
    .document-check-item.checked .form-check-label i,
    .document-check-item.checked .document-check-status {
        color: #495057 !important;
    }

    .document-check-item.checked .form-check-input {
        accent-color: #198754;
    }

    /* ---------- CHECKLIST FOOTER ---------- */
    .document-check-footer {
        margin-top: 10px !important;
        padding-top: 9px;
        border-top: 1px solid #dfe4e8;
        gap: 7px !important;
        font-size: 12px;
    }

    .checklist-card-footer {
        margin-top: auto !important;
        flex-shrink: 0;
        padding: 10px 14px !important;
        background: #e5e9ed !important;
        border-top: 1px solid var(--tt-border) !important;
    }

    .checklist-card-footer .btn {
        min-width: 88px;
        border-radius: 2px !important;
        font-size: 12px;
        font-weight: 600;
    }

    .checklist-card-footer #btnCetakTterima {
        min-width: 150px;
    }

    /* ---------- ALLOCATION ---------- */
    .allocation-box {
        border: 1px solid var(--tt-border);
        background: #fff;
        border-radius: 2px;
        overflow: hidden;
    }

    #tabtterimadtl {
        margin-bottom: 0 !important;
    }

    #tabtterimadtl thead th {
        background: #e7ebef;
        color: #495057;
        border-color: #d2d8de;
        font-size: 11px;
        font-weight: 600;
        vertical-align: middle;
    }

    #tabtterimadtl tbody td {
        font-size: 11px;
        vertical-align: middle;
    }

    .allocation-box .btn {
        border-radius: 2px !important;
    }

    /* ---------- DETAIL ACTION TOOLBAR ---------- */
    .detail-action-toolbar {
        padding: 0;
        margin: 0;
        background: transparent !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .btn-detail-action {
        width: 42px;
        height: 36px;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 3px !important;
        font-size: 14px !important;
        line-height: 1 !important;
        box-shadow: none !important;
    }

    .btn-detail-action i {
        margin: 0 !important;
    }

    .btn-detail-action:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    #btnAddDetail {
        background: #00c7a5 !important;
        border-color: #00c7a5 !important;
        color: #fff !important;
    }

    #btnEditDetail {
        background: #ffbf2f !important;
        border-color: #ffbf2f !important;
        color: #fff !important;
    }

    #btnDeleteDetail {
        background: #ef6170 !important;
        border-color: #ef6170 !important;
        color: #fff !important;
    }

    .detail-row-check {
        width: 16px;
        height: 16px;
        cursor: pointer;
        margin: 0;
    }

    #checkAll {
        width: 16px;
        height: 16px;
        cursor: pointer;
        margin: 0;
    }

    /* ---------- SELECT2 NORMAL PAGE ---------- */
    .select2-container {
        z-index: 1 !important;
    }

    .select2-container--open,
    .select2-dropdown {
        z-index: 1 !important;
    }

    .select2-container .select2-selection {
        min-height: 34px !important;
        border-color: #cfd6dd !important;
        border-radius: 2px !important;
    }

    .select2-container--default .select2-selection--single {
        height: 34px !important;
    }

    .select2-container--default
    .select2-selection--single
    .select2-selection__rendered {
        line-height: 32px !important;
        font-size: 12px;
    }

    .select2-container--default
    .select2-selection--single
    .select2-selection__arrow {
        height: 32px !important;
    }

    /* ---------- SELECT2 DI MODAL ---------- */
    .modal .select2-container {
        z-index: 1056 !important;
    }

    .modal .select2-container--open {
        z-index: 1056 !important;
    }

    .modal .select2-dropdown {
        z-index: 1057 !important;
    }

    #modalUpdateTterimaDtl .select2-container {
        width: 100% !important;
    }

    #modalUpdateTterimaDtl .select2-dropdown {
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* ---------- MODAL ---------- */
    #modalUpdateTterimaDtl .modal-content {
        border: 1px solid #cfd6dd;
        border-radius: 2px !important;
        box-shadow: 0 8px 28px rgba(0,0,0,.18);
    }

    #modalUpdateTterimaDtl .modal-header {
        background: var(--tt-navy) !important;
        border-bottom: 0;
    }

    #modalUpdateTterimaDtl .modal-title {
        font-size: 14px;
        font-weight: 600;
    }

    #modalUpdateTterimaDtl .modal-footer {
        border-top: 1px solid var(--tt-border);
    }

    /* ---------- RESPONSIVE ---------- */
    @media (max-width: 991.98px) {
        .document-check-item {
            min-height: 38px !important;
        }
    }

    @media (max-width: 575.98px) {
        .checklist-card-footer .d-flex {
            justify-content: stretch !important;
        }

        .checklist-card-footer .btn {
            flex: 1 1 0;
        }
    }

    @media print {
        .checklist-card-footer {
            display: none !important;
        }
    }
</style>

<!--<form action="--><?php /*= base_url('arap/transaksi/finalTterima'); */?>"
<form action="#"
      method="post"
      id="formTterima">

    <div class="container-fluid">

        <!-- =====================================================
             TOP ROW : CARD 1 + CARD 2
        ====================================================== -->
        <div class="row g-3 align-items-stretch">

            <!-- CARD 1 : TANDA TERIMA -->
            <div class="col-lg-8">
                <div class="card tt-card h-100 mb-0">
                    <div class="card-header tt-header-clean">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="tt-card-title">
                                <?= ($typeform == 'INPUT')
                                        ? 'Input Tanda Terima'
                                        : (($typeform == 'UPDATE')
                                                ? 'Edit Tanda Terima'
                                                : 'Detail Tanda Terima'); ?>
                            </div>

                            <span id="lbl_status" class="tt-status status-draft">DRAFT</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- =============================================
                             ROW 1
                        ============================================== -->

                        <div class="row g-3">


                            <!-- CABANG -->

                            <div class="col-lg-2 col-md-4">

                                <label for="cabang"
                                       class="form-label">

                                    Cabang / Job

                                    <span class="text-danger">*</span>

                                </label>

                                <select name="cabang"
                                        id="cabang"
                                        class="form-select"
                                        required>

                                </select>

                            </div>


                            <!-- NO TT -->

                            <div class="col-lg-3 col-md-8">

                                <label class="form-label">
                                    No. Tanda Terima
                                </label>

                                <div class="input-group doc-number">

                                    <!-- PREFIX -->
                                    <input type="text"
                                           name="prefix"
                                           id="prefix"
                                           class="form-control prefix"
                                           maxlength="3"
                                           style="text-transform:uppercase;"
                                           pattern="[A-Za-z0-9]{1,3}"
                                           title="Prefix hanya boleh huruf dan angka, maksimal 3 karakter">


                                    <span class="input-group-text">
                        /
                    </span>


                                    <!-- INFIX -->
                                    <input type="text"
                                           name="infix"
                                           id="infix"
                                           class="form-control infix"
                                           readonly>


                                    <span class="input-group-text">
                        /
                    </span>


                                    <!-- SUFFIX -->
                                    <input type="text"
                                           name="suffix"
                                           id="suffix"
                                           class="form-control suffix"
                                           maxlength="6"
                                           pattern="^[A-Za-z0-9]{0,2}[0-9]{4}$"
                                           title="Maksimal 6 karakter. 4 karakter terakhir wajib angka."
                                           style="text-transform:uppercase;">

                                </div>


                                <input type="hidden"
                                       name="docno"
                                       id="docno"
                                       value="<?= isset($dtldata['docno'])
                                               ? esc(trim($dtldata['docno']))
                                               : ''; ?>">

                            </div>


                            <!-- TANGGAL -->

                            <div class="col-lg-2 col-md-4">

                                <label for="docdate"
                                       class="form-label">

                                    Tanggal TT

                                </label>

                                <input type="text"
                                       name="docdate"
                                       id="docdate"
                                       class="form-control"
                                       placeholder="DD-MM-YYYY"
                                       autocomplete="off">

                            </div>

                            <!-- SUPPLIER -->

                            <div class="col-lg-4">

                                <label for="kdsupplier"
                                       class="form-label">

                                    Supplier

                                    <span class="text-danger">*</span>

                                </label>

                                <select name="kdsupplier"
                                        id="kdsupplier"
                                        class="form-select select2"
                                        required>

                                </select>

                            </div>




                        </div>




                        <!-- =============================================
                             DETAIL TANDA TERIMA
                        ============================================== -->




                        <!-- =============================================
                             SUPPLIER
                        ============================================== -->



                        <hr class="my-3">

                        <div class="row g-3">



                            <!-- TGL JATUH TEMPO -->

                            <div class="col-lg-2 col-md-4">

                                <label for="tgljthtempo"
                                       class="form-label">

                                    Tgl. Jatuh Tempo

                                </label>

                                <input type="text"
                                       name="tgljthtempo"
                                       id="tgljthtempo"
                                       class="form-control"
                                       placeholder="DD-MM-YYYY"
                                       readonly>

                            </div>


                            <!-- JATUH TEMPO -->

                            <div class="col-lg-1 col-md-4">

                                <label for="jthtempo"
                                       class="form-label">

                                    Jatuh Tempo

                                </label>

                                <div class="input-group">

                                    <input type="text"
                                           name="jthtempo"
                                           id="jthtempo"
                                           class="form-control text-end ratakanan jtsseparator"
                                           placeholder="0">

                                    <span class="input-group-text">
                        hari
                    </span>

                                </div>

                            </div>
                            <!-- PERKIRAAN / AKUN -->
                            <div class="col-lg-6 col-md-4">

                                <label for="coabank" class="form-label">
                                    Perkiraan / Akun
                                </label>

                                <select name="coabank"
                                        id="coabank"
                                        class="form-select"
                                        style="width:100%;">
                                </select>
                                <input type="hidden"
                                       name="nmcoabank"
                                       id="nmcoabank">
                            </div>

                            <!-- ALAMAT -->

                            <div class="col-lg-5">

                                <label for="alamatsupplier"
                                       class="form-label">

                                    Alamat Supplier

                                </label>

                                <textarea name="alamatsupplier"
                                          id="alamatsupplier"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Alamat Supplier"
                                          style="text-transform:uppercase;"></textarea>

                            </div>


                            <!-- PAJAK -->

                            <div class="col-lg-3">

                                <label for="idtax"
                                       class="form-label">

                                    Pajak

                                </label>

                                <div class="input-group">

                                    <select name="idtax"
                                            id="idtax"
                                            class="form-select select2"
                                            required>

                                    </select>

                                    <div class="input-group-text">

                                        <input type="checkbox"
                                               name="isinclusive"
                                               id="isinclusive"
                                               class="form-check-input mt-0 me-1">

                                        <label for="isinclusive"
                                               class="small mb-0">

                                            Inclusive

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row g-3 mt-3">

                            <!-- CURRENCY + KURS -->
                            <div class="col-lg-3 col-md-5">

                                <div class="row g-2">

                                    <!-- CURRENCY -->
                                    <div class="col-6">

                                        <label for="currcode" class="form-label">
                                            Curr.
                                        </label>

                                        <select name="currcode"
                                                id="currcode"
                                                class="form-select select2"
                                                required>
                                        </select>

                                    </div>

                                    <!-- KURS -->
                                    <div class="col-6">

                                        <label for="kurs" class="form-label">
                                            Kurs
                                        </label>

                                        <input type="text"
                                               name="kurs"
                                               id="kurs"
                                               class="form-control text-end"
                                               placeholder="1"
                                               readonly>

                                    </div>

                                </div>

                            </div>


                            <!-- KETERANGAN -->
                            <div class="col-lg-9 col-md-7">

                                <label for="keterangan" class="form-label">
                                    Keterangan
                                </label>

                                <textarea name="keterangan"
                                          id="keterangan"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Keterangan Tanda Terima..."
                                          style="text-transform:uppercase;"></textarea>

                            </div>

                        </div>
                        <hr class="my-3">


                        <!-- =============================================
                             DOKUMEN TAGIHAN
                             Dipindahkan ke area Checklist / Referensi
                        ============================================== -->

                        <!-- =============================================
                             NILAI
                        ============================================== -->

                        <div class="row g-3">


                            <div class="col-lg-3">

                                <label for="dpp"
                                       class="form-label">

                                    DPP

                                </label>

                                <input type="text"
                                       name="dpp"
                                       id="dpp"
                                       class="form-control text-end jtsseparator ratakanan"
                                       value="0">

                            </div>


                            <div class="col-lg-3">

                                <label for="jumlahpajak"
                                       class="form-label">

                                    Jumlah Pajak

                                </label>

                                <input type="text"
                                       name="jumlahpajak"
                                       id="jumlahpajak"
                                       class="form-control text-end jtsseparator ratakanan"
                                       value="0">

                            </div>


                            <div class="col-lg-6">

                                <label for="total"
                                       class="form-label">

                                    Total Nilai

                                </label>

                                <input type="text"
                                       name="total"
                                       id="total"
                                       class="form-control text-end jtsseparator ratakanan"
                                       value="0">

                            </div>

                        </div>

                        <!-- KETERANGAN -->


                    </div>
                </div>
            </div>

            <!-- CARD 2 : CHECKLIST DOKUMEN -->
            <div class="col-lg-4">
                <div class="card tt-card checklist-card h-100 mb-0">
                    <div class="card-header tt-header-clean">
                        <div class="tt-card-title">
                            Checklist Dokumen
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="document-check-header h-100">
                            <div class="document-check-items">
                                <div class="document-check-item"
                                     id="box_cekinvoice">

                                    <div class="form-check">

                                        <input class="form-check-input cek-dokumen"
                                               type="checkbox"
                                               name="cekinvoice"
                                               id="cekinvoice"
                                               value="1">

                                        <label class="form-check-label"
                                               for="cekinvoice">

                                            <i class="fa fa-file-invoice text-primary"></i>
                                            Invoice

                                        </label>

                                    </div>

                                    <span class="document-check-status">
                                                            Belum
                                                        </span>

                                </div>
                                <div class="document-check-item"
                                     id="box_ceksj">

                                    <div class="form-check">

                                        <input class="form-check-input cek-dokumen"
                                               type="checkbox"
                                               name="ceksj"
                                               id="ceksj"
                                               value="1">

                                        <label class="form-check-label"
                                               for="ceksj">

                                            <i class="fa fa-truck text-primary"></i>
                                            Surat Jalan

                                        </label>

                                    </div>

                                    <span class="document-check-status">
                                                            Belum
                                                        </span>

                                </div>
                                <div class="document-check-item"
                                     id="box_cekpenerimaan">

                                    <div class="form-check">

                                        <input class="form-check-input cek-dokumen"
                                               type="checkbox"
                                               name="cekpenerimaan"
                                               id="cekpenerimaan"
                                               value="1">

                                        <label class="form-check-label"
                                               for="cekpenerimaan">

                                            <i class="fa fa-box text-primary"></i>
                                            Penerimaan

                                        </label>

                                    </div>

                                    <span class="document-check-status">
                                                            Belum
                                                        </span>

                                </div>
                                <div class="document-check-item"
                                     id="box_cekfakturpajak">

                                    <div class="form-check">

                                        <input class="form-check-input cek-dokumen"
                                               type="checkbox"
                                               name="cekfakturpajak"
                                               id="cekfakturpajak"
                                               value="1">

                                        <label class="form-check-label"
                                               for="cekfakturpajak">

                                            <i class="fa fa-receipt text-primary"></i>
                                            Faktur Pajak

                                        </label>

                                    </div>

                                    <span class="document-check-status">
                                                            Belum
                                                        </span>

                                </div>
                                <div class="document-check-item"
                                     id="box_cekbeaimport">

                                    <div class="form-check">

                                        <input class="form-check-input cek-dokumen"
                                               type="checkbox"
                                               name="cekbeaimport"
                                               id="cekbeaimport"
                                               value="1">

                                        <label class="form-check-label"
                                               for="cekbeaimport">

                                            <i class="fa fa-ship text-primary"></i>
                                            Import

                                        </label>

                                    </div>

                                    <span class="document-check-status">
                                                            Belum
                                                        </span>

                                </div>
                                <div class="document-check-item"
                                     id="box_cekdokumen">

                                    <div class="form-check">

                                        <input class="form-check-input cek-dokumen"
                                               type="checkbox"
                                               name="cekdokumen"
                                               id="cekdokumen"
                                               value="1">

                                        <label class="form-check-label"
                                               for="cekdokumen">

                                            <i class="fa fa-folder-open text-primary"></i>
                                            Pendukung

                                        </label>

                                    </div>

                                    <span class="document-check-status">
                                                            Belum
                                                        </span>

                                </div>
                            </div>

                            <div class="document-check-footer">

                                <!-- ROW STATUS -->
                                <div class="row align-items-center g-2 w-100">

                                    <div class="col-auto">
            <span class="fw-semibold">
                Dokumen Pendukung
            </span>
                                    </div>

                                    <div class="col-auto">
                                        <span class="fw-bold" id="check_count">0</span>
                                    </div>

                                    <div class="col-auto">
            <span id="lbl_check_status"
                  class="tt-status status-draft">
            </span>
                                    </div>

                                </div>

                                <!-- <hr class="my-2 w-100">-->

                                <!-- DOKUMEN REFERENSI -->
                                <div class="tt-reference-box">

                                    <!-- NO INVOICE -->
                                    <div class="mb-2">
                                        <label for="noinvoice"
                                               class="form-label mb-1 fw-semibold">
                                            No. Invoice
                                        </label>

                                        <input type="text"
                                               name="noinvoice"
                                               id="noinvoice"
                                               class="form-control form-control-sm"
                                               maxlength="50"
                                               placeholder="Nomor Invoice"
                                               style="text-transform:uppercase;">
                                    </div>

                                    <!-- NO SJ + NO AJU -->
                                    <div class="row g-2 mb-2">

                                        <div class="col-md-6">
                                            <label for="nosj"
                                                   class="form-label mb-1 fw-semibold">
                                                No. Surat Jalan
                                            </label>

                                            <input type="text"
                                                   name="nosj"
                                                   id="nosj"
                                                   class="form-control form-control-sm"
                                                   maxlength="50"
                                                   placeholder="Nomor Surat Jalan"
                                                   style="text-transform:uppercase;">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="noaju"
                                                   class="form-label mb-1 fw-semibold">
                                                No. Aju
                                            </label>

                                            <input type="text"
                                                   name="noaju"
                                                   id="noaju"
                                                   class="form-control form-control-sm"
                                                   maxlength="50"
                                                   placeholder="Nomor Aju"
                                                   style="text-transform:uppercase;">
                                        </div>

                                    </div>

                                    <!-- NO BL + NO AWB -->
                                    <div class="row g-2 mb-2">

                                        <div class="col-md-6">
                                            <label for="nobl"
                                                   class="form-label mb-1 fw-semibold">
                                                No. BL
                                            </label>

                                            <input type="text"
                                                   name="nobl"
                                                   id="nobl"
                                                   class="form-control form-control-sm"
                                                   maxlength="50"
                                                   placeholder="Nomor BL"
                                                   style="text-transform:uppercase;">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="noawb"
                                                   class="form-label mb-1 fw-semibold">
                                                No. AWB
                                            </label>

                                            <input type="text"
                                                   name="noawb"
                                                   id="noawb"
                                                   class="form-control form-control-sm"
                                                   maxlength="50"
                                                   placeholder="Nomor AWB"
                                                   style="text-transform:uppercase;">
                                        </div>

                                    </div>

                                    <!-- REFERENSI BK + FAKTUR PAJAK -->
                                    <div class="row g-2">

                                        <div class="col-md-6">
                                            <label for="nobkrev"
                                                   class="form-label mb-1 fw-semibold">
                                                Referensi No BK
                                            </label>

                                            <input type="text"
                                                   name="nobkrev"
                                                   id="nobkrev"
                                                   class="form-control form-control-sm"
                                                   maxlength="30"
                                                   placeholder="Referensi Nomor BK"
                                                   style="text-transform:uppercase;">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="nofakturpajak"
                                                   class="form-label mb-1 fw-semibold">
                                                No. Faktur Pajak
                                            </label>

                                            <input type="text"
                                                   name="nofakturpajak"
                                                   id="nofakturpajak"
                                                   class="form-control form-control-sm"
                                                   maxlength="50"
                                                   placeholder="Nomor Faktur Pajak"
                                                   style="text-transform:uppercase;">
                                        </div>

                                    </div>

                                </div>

                                <!-- FOOTER CHECKLIST -->
                                <div class="card-footer checklist-card-footer">
                                    <div class="d-flex justify-content-end gap-2">

                                        <?php if ($typeform != 'DETAIL') : ?>
                                            <button type="button"
                                                    class="btn btn-success btn-sm"
                                                    id="btnSimpanChecklist"
                                                    onclick="saveTterimaHeader()">
                                                <i class="fa fa-save me-1"></i>
                                                Simpan Checklist
                                            </button>
                                        <?php endif; ?>

                                        <button type="button"
                                                class="btn btn-secondary btn-sm"
                                                id="btnCetakTterima"
                                                onclick="window.print();">
                                            <i class="fa fa-print me-1"></i>
                                            Cetak Tanda Terima
                                        </button>

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
            <div class="card tt-card mt-3">
                <div class="card-header">
                    <div class="tt-header-main">

                        <div>
                            <div class="tt-card-title">Bukti Alokasi Nilai</div>
                        </div>
                    </div>
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
                                            onclick="addDetailTterima()"
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

                                <table id="tabtterimadtl"
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
                        <a href="<?= base_url('arap/transaksi/clearEntryTterima'); ?>"
                           class="btn btn-secondary"
                           onclick="return confirm('Clear entry Tanda Terima?')">
                            <i class="fa fa-arrow-left me-1"></i>
                            Kembali
                        </a>

                        <?php if ($typeform != 'DETAIL') : ?>
                            <button type="button"
                                    id="btnSimpan"
                                    class="btn btn-success"
                                    onclick="finalTterima()">

                                <i class="fa fa-save me-1"></i>
                                Simpan Final Tanda Terima

                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>




<!-- Modal TTerima -->

<!-- =========================================================
     MODAL ADD / EDIT DETAIL TANDA TERIMA
========================================================= -->
<!-- =========================================================
     MODAL ADD / EDIT DETAIL TANDA TERIMA
========================================================= -->
<div class="modal fade"
     id="modalUpdateTterimaDtl"
     tabindex="-1"
     aria-labelledby="modalUpdateTterimaDtlLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <!-- =================================================
                 HEADER MODAL
            ================================================== -->
            <div class="modal-header bg-primary text-white">

                <div>
                    <h5 class="modal-title mb-0"
                        id="modalUpdateTterimaDtlLabel">
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
            <form id="formTterimaDtl"
                  autocomplete="off">

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
                                No. Tanda Terima
                            </label>

                            <input type="text"
                                   id="detail_docno_display"
                                   class="form-control bg-light"
                                   readonly
                                   placeholder="No. Tanda Terima">

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
                            class="btn btn-success"
                            id="btnSaveTterimaDtl"
                            onclick="saveTterimaDetail()">

                        <i class="fa fa-save me-1"></i>
                        Simpan

                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        <i class="fa fa-times me-1"></i>
                        Batal

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>














<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

    $(document).ready(function () {

        const dtldata =
                <?= json_encode($dtldata ?? null); ?>;


        /* =====================================================
           DOCNO
        ====================================================== */

        if (dtldata && dtldata.docno) {

            $('#docno')
                .val($.trim(dtldata.docno))
                .prop('readonly', true);

        }


        /* =====================================================
           DATE PICKER
        ====================================================== */

        function initDatePicker(selector) {

            if (typeof $.fn.daterangepicker !== 'function') {
                console.warn('daterangepicker belum tersedia:', selector);
                return;
            }

            $(selector).daterangepicker({

                autoUpdateInput: false,
                singleDatePicker: true,
                showDropdowns: true,

                locale: {
                    format: 'DD-MM-YYYY',
                    cancelLabel: 'Clear'
                }

            });


            $(selector).on(
                'apply.daterangepicker',
                function (ev, picker) {

                    $(this).val(
                        picker.startDate.format('DD-MM-YYYY')
                    );

                    if (selector === '#docdate') {
                        calculateTglJatuhTempo();
                    }

                }
            );


            $(selector).on(
                'cancel.daterangepicker',
                function () {

                    $(this).val('');

                }
            );

        }


        initDatePicker('#docdate');


        /* =====================================================
           CHECKLIST
        ====================================================== */

        function updateDocumentChecking() {

            const checkboxes =
                $('.cek-dokumen');

            const total =
                checkboxes.length;

            const checked =
                checkboxes.filter(':checked').length;


            $('#check_count').text(checked);


            checkboxes.each(function () {

                const id =
                    $(this).attr('id');

                const box =
                    $('#box_' + id);

                const status =
                    box.find('.document-check-status');


                if ($(this).is(':checked')) {

                    box.addClass('checked');

                    status.text('OK');

                } else {

                    box.removeClass('checked');

                    status.text('Belum');

                }

            });


            const badge =
                $('#lbl_check_status');

            const statusText =
                $('#checking_status_text');

            function setCheckingStatus(value) {
                if (statusText.length) {
                    statusText.val(value);
                }
            }


            if (checked === 0) {

                badge
                    .removeClass('status-partial status-complete')
                    .addClass('status-draft')
                    .text('BELUM LENGKAP');

                setCheckingStatus('Belum dilakukan');

            }
            else if (checked < total) {

                badge
                    .removeClass('status-draft status-complete')
                    .addClass('status-partial')
                    .text('PARTIAL');

                setCheckingStatus(
                    'Partial - ' +
                    checked +
                    ' dari ' +
                    total +
                    ' dokumen'
                );

            }
            else {

                badge
                    .removeClass('status-draft status-partial')
                    .addClass('status-complete')
                    .text('LENGKAP');

                setCheckingStatus('Semua dokumen sudah diperiksa');

            }

        }


        $('.cek-dokumen').on(
            'change',
            updateDocumentChecking
        );


        updateDocumentChecking();


        /* =====================================================
           SUPPLIER
        ====================================================== */

        $('#kdsupplier').on(
            'change',
            function () {

                if (!$(this).val()) {

                    $('#modal_supplier').val('');

                    return;

                }

                $('#modal_supplier').val(
                    $('#kdsupplier option:selected').text()
                );

            }
        );


        // Hitung ulang tanggal jatuh tempo setelah supplier
        // mengisi nilai jthtempo.
        setTimeout(function () {
            calculateTglJatuhTempo();
        }, 100);


        /* =====================================================
           TANGGAL JATUH TEMPO
           Tanggal TT + jumlah hari jatuh tempo
        ====================================================== */

        function formatDateTT(date) {

            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();

            return day + '-' + month + '-' + year;
        }


        function calculateTglJatuhTempo() {

            const docdateValue =
                $.trim($('#docdate').val());

            const days =
                parseInt(
                    String($('#jthtempo').val())
                        .replace(/[^0-9-]/g, ''),
                    10
                );

            if (!docdateValue || isNaN(days)) {
                $('#tgljthtempo').val('');
                return;
            }

            const parts =
                docdateValue.split('-');

            if (parts.length !== 3) {
                $('#tgljthtempo').val('');
                return;
            }

            const day   = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1;
            const year  = parseInt(parts[2], 10);

            const dateTT =
                new Date(year, month, day);

            if (isNaN(dateTT.getTime())) {
                $('#tgljthtempo').val('');
                return;
            }

            dateTT.setDate(
                dateTT.getDate() + days
            );

            $('#tgljthtempo')
                .val(formatDateTT(dateTT));
        }


        $('#docdate').on(
            'change',
            calculateTglJatuhTempo
        );


        $('#jthtempo').on(
            'keyup change',
            calculateTglJatuhTempo
        );


        /* =====================================================
           INVOICE
        ====================================================== */

        $('#noinvoice').on(
            'keyup change',
            function () {

                $('#modal_invoice').val(
                    $(this).val()
                );

            }
        );


        /* =====================================================
           AMBIL PENERIMAAN
        ====================================================== */

        $('#btnAmbilPenerimaan').on(
            'click',
            function () {

                const supplier =
                    $('#kdsupplier').val();


                if (!supplier) {

                    alert(
                        'Silakan pilih supplier terlebih dahulu.'
                    );

                    return;

                }


                $('#modal_supplier').val(
                    $('#kdsupplier option:selected').text()
                );


                $('#modal_invoice').val(
                    $('#noinvoice').val()
                );


                const modal =
                    new bootstrap.Modal(
                        document.getElementById(
                            'modalPenerimaan'
                        )
                    );


                modal.show();


                if (
                    typeof loadPenerimaanTterima ===
                    'function'
                ) {

                    loadPenerimaanTterima(
                        supplier,
                        $('#noinvoice').val()
                    );

                }

            }
        );


        /* =====================================================
           CHECK ALL
        ====================================================== */

        $('#checkAllPenerimaan').on(
            'change',
            function () {

                $('#tablePenerimaan tbody input[type="checkbox"]')
                    .prop(
                        'checked',
                        $(this).is(':checked')
                    );

            }
        );


        /* =====================================================
           FILTER
        ====================================================== */

        $('#filterPenerimaan').on(
            'keyup',
            function () {

                const value =
                    $(this).val().toLowerCase();


                $('#tablePenerimaan tbody tr')
                    .filter(function () {

                        $(this).toggle(
                            $(this).text()
                                .toLowerCase()
                                .indexOf(value) > -1
                        );

                    });

            }
        );


        /* =====================================================
           PREFIX
        ====================================================== */

        const prefix =
            $('#prefix').val() || 'JI';


        if (
            typeof setupEstpakai ===
            'function'
        ) {

            setupEstpakai(prefix);

        }


        $('#prefix').on(
            'change',
            function () {

                if (
                    typeof setupEstpakai ===
                    'function'
                ) {

                    setupEstpakai(
                        $(this).val()
                    );

                }

            }
        );

    });

</script>

<script src="<?= base_url('assets/pagejs/arap/tterima.js'); ?>"></script>
