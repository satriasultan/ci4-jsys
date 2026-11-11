<!-- v_add_transfer_location -->

<style>
    /* =========================================================
       CORPORATE THEME VARIABLES
    ========================================================= */
    .corp-form-container {
        --corp-primary:       #1e3a5f;
        --corp-primary-hover: #152a45;
        --corp-primary-light: #e8eef5;
        --corp-text:          #1a202c;
        --corp-text-muted:    #64748b;
        --corp-border:        #cbd5e1;
        --corp-bg-light:      #f8fafc;
        --corp-bg-section:    #ffffff;
        --corp-bg-disabled:   #f1f5f9;
        --corp-success:       #16a34a;
        --corp-warning:       #ffc107;
        --corp-danger:        #dc2626;
        --corp-radius:        6px;
        --corp-shadow:        0 1px 3px rgba(15, 23, 42, 0.06);

        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        color: var(--corp-text);
        font-size: 13.5px;
    }

    /* =========================================================
       CARD
    ========================================================= */
    .corp-form-container .card {
        border: 1px solid var(--corp-border) !important;
        border-radius: var(--corp-radius) !important;
        box-shadow: var(--corp-shadow) !important;
        overflow: hidden;
        background: var(--corp-bg-section);
    }

    /* warna background header mengikuti global corporate header (layout) */
    .corp-form-container .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #ffffff;
        border-bottom: none;
    }

    .corp-form-container .card-header h3 {
        float: none;
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.4;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #ffffff;
    }

    .corp-form-container .card-header h3 i {
        margin-right: 8px;
        font-size: 14px;
    }

    /* hilangkan clearfix bootstrap agar tombol bisa mentok kanan */
    .corp-form-container .card-header::before,
    .corp-form-container .card-header::after {
        display: none !important;
    }

    .corp-form-container .card-header .card-title {
        flex: 0 1 auto;
        margin: 0 auto 0 0;
    }

    .corp-form-container .card-header .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
        float: none;
        flex: 0 0 auto;
    }

    .corp-form-container .card-body {
        padding: 22px;
        background: var(--corp-bg-light);
    }

    .corp-form-container .card-footer {
        background: var(--corp-bg-light) !important;
        border-top: 1px solid var(--corp-border) !important;
        padding: 14px 22px;
    }

    .corp-form-container .card-footer::after {
        content: "";
        display: block;
        clear: both;
    }
    /* =========================================================
       SECTION BLOCK
    ========================================================= */
    .corp-form-container .section-block {
        background: var(--corp-bg-section);
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        padding: 20px 22px;
        margin-bottom: 0;
        box-shadow: var(--corp-shadow);
        transition: none;
    }

    .corp-form-container .section-block:hover {
        box-shadow: var(--corp-shadow);
        border-color: var(--corp-border);
    }

    .corp-form-container .section-header {
        display: flex;
        align-items: center;
        font-size: 13px;
        font-weight: 700;
        color: var(--corp-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--corp-border);
    }

    .corp-form-container .section-header i {
        margin-right: 8px;
        font-size: 14px;
        color: var(--corp-primary);
    }

    /* =========================================================
       FORM ELEMENTS
    ========================================================= */
    .corp-form-container .form-group {
        margin-bottom: 16px;
    }

    .corp-form-container label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--corp-text);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 6px;
    }

    .corp-form-container label span[style*="color: red"] {
        color: var(--corp-danger) !important;
        font-weight: 700;
    }

    .corp-form-container .form-control,
    .corp-form-container .form-select,
    .corp-form-container select.form-control {
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 13.5px;
        color: var(--corp-text);
        background-color: #ffffff;
        height: auto;
        min-height: 40px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .corp-form-container .form-control:focus,
    .corp-form-container .form-select:focus,
    .corp-form-container select.form-control:focus {
        border-color: var(--corp-primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        outline: none;
    }

    .corp-form-container textarea.form-control {
        min-height: 76px;
        resize: vertical;
        line-height: 1.45;
    }

    .corp-form-container .form-control:disabled,
    .corp-form-container .form-control[readonly] {
        background-color: var(--corp-bg-disabled);
        color: #202020;
        opacity: 1;
        cursor: not-allowed;
        border-color: var(--corp-border);
    }

    /* angka rata kanan (dipakai class global .ratakanan) */
    .corp-form-container .ratakanan,
    .corp-form-container .text-end {
        text-align: right;
    }

    /* input group (Jatuh Tempo - hari) */
    .corp-form-container .input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .corp-form-container .input-group-text {
        background-color: var(--corp-bg-disabled);
        border: 1px solid var(--corp-border);
        border-left: none;
        border-radius: 0 4px 4px 0;
        color: var(--corp-text-muted);
        font-size: 12.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 8px 12px;
        min-height: 40px;
        display: flex;
        align-items: center;
    }

    .corp-form-container .input-group .btn {
        border-radius: 0 4px 4px 0;
        padding: 8px 12px;
        margin-left: -1px;
    }


    /* =========================================================
       SELECT2 (corporate override)
    ========================================================= */
    .corp-form-container .select2-container {
        width: 100% !important;
    }

    .corp-form-container .select2-container .select2-selection--single {
        height: 40px !important;
        min-height: 40px !important;
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px !important;
        background-color: #ffffff;
    }

    .corp-form-container .select2-container--focus .select2-selection--single,
    .corp-form-container .select2-container--open .select2-selection--single {
        border-color: var(--corp-primary) !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 11px !important;
        padding-right: 26px !important;
        font-size: 13.5px;
        color: var(--corp-text) !important;
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__placeholder {
        color: var(--corp-text-muted);
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__clear {
        font-size: 16px;
        color: var(--corp-text-muted);
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 6px;
    }

    .corp-form-container .select2-dropdown {
        border-color: var(--corp-border) !important;
        border-radius: 4px;
        font-size: 13.5px;
    }

    .corp-form-container .select2-results__option--highlighted,
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--corp-primary) !important;
        color: #ffffff !important;
    }

    /* =========================================================
       CHECKBOX & FORM CHECK
    ========================================================= */
    .corp-form-container input[type="checkbox"] {
        width: 15px;
        height: 15px;
        accent-color: var(--corp-primary);
        cursor: pointer;
        vertical-align: middle;
    }

    .corp-form-container .form-check.corporate-check {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        min-height: 20px;
    }

    .corp-form-container .form-check.corporate-check .form-check-input {
        margin: 0;
        width: 16px;
        height: 16px;
        border: 1px solid #9ca3af;
        accent-color: var(--corp-primary);
    }

    .corp-form-container .form-check.corporate-check .form-check-label {
        margin: 0;
        font-size: 12.5px;
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0.2px;
        color: #111827;
        cursor: pointer;
    }


    /* =========================================================
       BUTTONS
    ========================================================= */
    .corp-form-container .btn {
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 9px 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        box-shadow: none;
        transition: all 0.15s ease;
    }

    .corp-form-container .btn i {
        margin-right: 5px;
    }

    .corp-form-container .btn-lg {
        font-size: 12.5px;
        padding: 9px 20px;
    }

    .corp-form-container .btn-primary,
    .corp-form-container .btn-primary:focus {
        background-color: var(--corp-primary);
        border-color: var(--corp-primary);
        color: #ffffff;
    }

    .corp-form-container .btn-primary:hover {
        background-color: var(--corp-primary-hover);
        border-color: var(--corp-primary-hover);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(30, 58, 95, 0.25);
    }

    .corp-form-container .btn-success {
        background-color: var(--corp-success);
        border-color: var(--corp-success);
        color: #ffffff;
    }

    .corp-form-container .btn-success:hover,
    .corp-form-container .btn-success:focus {
        background-color: #15803d;
        border-color: #15803d;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25);
    }

    .corp-form-container .btn-warning {
        background-color: var(--corp-warning);
        border-color: var(--corp-warning);
        color: #212529;
    }

    .corp-form-container .btn-warning:hover,
    .corp-form-container .btn-warning:focus {
        background-color: #e0a800;
        border-color: #d39e00;
        color: #212529;
        box-shadow: 0 2px 6px rgba(180, 140, 0, 0.25);
    }

    .corp-form-container .btn-danger {
        background-color: var(--corp-danger);
        border-color: var(--corp-danger);
        color: #ffffff;
    }

    .corp-form-container .btn-danger:hover,
    .corp-form-container .btn-danger:focus {
        background-color: #b91c1c;
        border-color: #b91c1c;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
    }

    .corp-form-container .btn-info {
        background-color: var(--corp-primary);
        border-color: var(--corp-primary);
        color: #ffffff;
    }

    .corp-form-container .btn-info:hover,
    .corp-form-container .btn-info:focus {
        background-color: var(--corp-primary-hover);
        border-color: var(--corp-primary-hover);
        color: #ffffff;
    }

    .corp-form-container .btn-default,
    .corp-form-container .btn-secondary {
        background-color: #ffffff;
        border-color: var(--corp-border);
        color: var(--corp-text);
    }

    .corp-form-container .btn-default:hover,
    .corp-form-container .btn-secondary:hover,
    .corp-form-container .btn-secondary:focus {
        background-color: var(--corp-bg-disabled);
        border-color: var(--corp-text-muted);
        color: var(--corp-text);
    }

    /* Tombol aksi pada header card */
    .corp-form-container .card-header .btn {
        padding: 8px 12px;
        font-size: 13px;
        line-height: 1;
        border-radius: 4px;
    }

    .corp-form-container .card-header .btn i {
        margin-right: 0;
    }

    .corp-form-container .card-header .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.25);
    }

    .corp-form-container .card-header .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        min-height: 32px;
    }

    /* =========================================================
       TABLE DETAIL
    ========================================================= */
    .corp-form-container #tmptabspktransfersdtl {
        background-color: #ffffff;
        margin-bottom: 0 !important;
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px;
        overflow: hidden;
        font-size: 13px;
        width: 100%;
    }

    .corp-form-container #tmptabspktransfersdtl thead th {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
        padding: 11px 12px !important;
        border: none !important;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-align: left;
        text-transform: uppercase;
        vertical-align: middle;
        white-space: nowrap;
    }

    .corp-form-container #tmptabspktransfersdtl thead th:first-child {
        text-align: center;
    }

    .corp-form-container #tmptabspktransfersdtl thead th span.dt-column-order::before,
    .corp-form-container #tmptabspktransfersdtl thead th span.dt-column-order::after {
        color: rgba(255, 255, 255, 0.55) !important;
    }

    .corp-form-container #tmptabspktransfersdtl tbody td {
        padding: 10px 12px !important;
        border-color: var(--corp-border) !important;
        vertical-align: middle;
        color: var(--corp-text);
    }

    .corp-form-container #tmptabspktransfersdtl tbody tr:nth-child(even) {
        background-color: #fbfcfe;
    }

    .corp-form-container #tmptabspktransfersdtl tbody tr:hover {
        background-color: var(--corp-primary-light) !important;
    }

    .corp-form-container #tmptabspktransfersdtl tbody tr.selected {
        background-color: #dbeafe !important;
    }

    .corp-form-container #tmptabspktransfersdtl tbody td.dataTables_empty {
        color: var(--corp-text-muted);
        font-weight: 600;
        padding: 18px !important;
        text-align: center;
    }

    /* DataTables control */
    .corp-form-container .dataTables_wrapper .dataTables_length,
    .corp-form-container .dataTables_wrapper .dataTables_filter,
    .corp-form-container .dataTables_wrapper .dataTables_info {
        font-size: 12.5px;
        color: var(--corp-text-muted);
    }

    .corp-form-container .dataTables_wrapper .dataTables_length select,
    .corp-form-container .dataTables_wrapper .dataTables_filter input {
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 12.5px;
        color: var(--corp-text);
        background-color: #ffffff;
        outline: none;
    }

    .corp-form-container .dataTables_wrapper .dataTables_filter input:focus,
    .corp-form-container .dataTables_wrapper .dataTables_length select:focus {
        border-color: var(--corp-primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
    }

    .corp-form-container .dataTables_wrapper .pagination .page-item.active .page-link {
        background-color: var(--corp-primary);
        border-color: var(--corp-primary);
        color: #ffffff;
    }

    .corp-form-container .dataTables_wrapper .pagination .page-link {
        color: var(--corp-primary);
        border-color: var(--corp-border);
        font-size: 12.5px;
    }

    /* pembatas antar blok */
    .corp-form-container .po-divider {
        border: 0;
        height: 1px;
        background: var(--corp-border);
        margin: 20px 0;
    }


    /* =========================================================
       MODAL
    ========================================================= */
    .corp-form-container .modal-content {
        border: none;
        border-radius: var(--corp-radius);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25);
    }

    .corp-form-container .modal-header {
        color: #ffffff;
        border-bottom: none;
    }

    .corp-form-container .modal-header .modal-title {
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #ffffff;
    }

    /* icon judul modal (judul diubah oleh JS, jadi icon dipasang via CSS) */
    .corp-form-container #modalDetailPOLabel::before {
        content: "\f0fe"; /* fa-plus-square-o */
        font-family: FontAwesome, "Font Awesome 5 Free", sans-serif;
        font-weight: normal;
        margin-right: 8px;
    }

    .corp-form-container #modalUpdatePOLabel::before {
        content: "\f044"; /* fa-pencil-square-o */
        font-family: FontAwesome, "Font Awesome 5 Free", sans-serif;
        font-weight: normal;
        margin-right: 8px;
    }

    /* header modal History Harga dibuat corporate (menggantikan bg-info) */
    .corp-form-container .modal-header.bg-info {
        background: var(--corp-primary) !important;
    }

    .corp-form-container .modal-body {
        padding: 20px 22px;
        background: var(--corp-bg-light);
    }

    .corp-form-container .modal-footer {
        background: var(--corp-bg-light) !important;
        border-top: 1px solid var(--corp-border);
        padding: 12px 22px;
    }

    /* ukuran seragam untuk input & select2 di dalam modal */
    .corp-form-container .modal .form-control,
    .corp-form-container .modal select.form-control,
    .corp-form-container .modal input.form-control {
        box-sizing: border-box;
        width: 100%;
        height: 40px !important;
        min-height: 40px !important;
        padding: 8px 11px;
        font-size: 13.5px;
        line-height: 20px;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
    }

    .corp-form-container .modal textarea.form-control {
        height: auto !important;
        min-height: 76px !important;
        padding: 8px 11px;
        line-height: 1.45;
        resize: vertical;
    }

    .corp-form-container .modal .select2-container {
        width: 100% !important;
    }

    .corp-form-container .modal .select2-container--open {
        z-index: 1065 !important;
    }

    .corp-form-container .modal .select2-dropdown {
        box-sizing: border-box !important;
        max-width: 100% !important;
        z-index: 1065 !important;
    }

    .corp-form-container .modal .form-group {
        margin-bottom: 16px;
    }

    /* tabel di dalam modal (History Harga) */
    .corp-form-container .modal table thead th {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        border-color: var(--corp-border) !important;
        padding: 10px 12px !important;
        vertical-align: middle;
    }

    .corp-form-container .modal table tbody td {
        font-size: 13px;
        color: var(--corp-text);
        border-color: var(--corp-border) !important;
        vertical-align: middle;
    }

    /* =========================================================
       RINGKASAN NILAI (DPP / PAJAK / TOTAL)
    ========================================================= */
    .corp-form-container .po-summary {
        background: var(--corp-bg-section);
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        padding: 14px 16px;
        box-shadow: var(--corp-shadow);
    }

    .corp-form-container .po-summary-title {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: var(--corp-primary);
        padding-bottom: 9px;
        margin-bottom: 12px;
        border-bottom: 1px solid var(--corp-border);
    }

    .corp-form-container .po-summary-title i {
        margin-right: 6px;
    }

    .corp-form-container .po-summary-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .corp-form-container .po-summary-row:last-child {
        margin-bottom: 0;
    }

    .corp-form-container .po-summary-row label {
        flex: 0 0 45%;
        margin-bottom: 0;
        font-size: 12px;
        font-weight: 600;
        color: var(--corp-text);
    }

    .corp-form-container .po-summary-row .form-control {
        flex: 1 1 auto;
        text-align: right;
        font-weight: 600;
    }

    .corp-form-container .po-summary-total label {
        color: var(--corp-primary);
        font-weight: 700;
    }

    .corp-form-container .po-summary-total .form-control {
        background: var(--corp-primary-light) !important;
        border-color: var(--corp-primary) !important;
        color: var(--corp-primary) !important;
        font-weight: 700;
        font-size: 15px;
    }

    /* =========================================================
       TOMBOL PILIHAN CEPAT TANGGAL PENGIRIMAN
       (class .senddate-btn dibuat oleh assets/pagejs/purchase/po.js)
    ========================================================= */
    .corp-form-container #senddate_buttons {
        margin-top: 8px;
    }

    .corp-form-container #senddate_buttons > div {
        display: flex;
        flex-wrap: wrap;
        gap: 6px !important;
    }

    .corp-form-container .senddate-btn {
        padding: 5px 12px !important;
        background-color: #ffffff !important;
        color: var(--corp-primary) !important;
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .corp-form-container .senddate-btn:hover {
        background-color: var(--corp-primary-light) !important;
        border-color: var(--corp-primary) !important;
        color: var(--corp-primary) !important;
    }

    .corp-form-container .senddate-btn.active {
        background-color: var(--corp-primary) !important;
        border-color: var(--corp-primary) !important;
        color: #ffffff !important;
    }

    /* =========================================================
       PAGE HEADER / BREADCRUMB
    ========================================================= */
    .corp-form-container .content-header {
        padding: 4px 0 14px;
    }

    .corp-form-container .content-header h1 {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.3px;
        color: var(--corp-primary);
        text-transform: uppercase;
    }

    .corp-form-container .content-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        font-size: 12px;
    }

    .corp-form-container .content-header .breadcrumb-item,
    .corp-form-container .content-header .breadcrumb-item a {
        color: var(--corp-text-muted);
        font-size: 12px;
    }

    .corp-form-container .content-header .breadcrumb-item a:hover {
        color: var(--corp-primary);
        text-decoration: none;
    }

    .corp-form-container .content-header .breadcrumb-item.active {
        color: var(--corp-primary);
        font-weight: 600;
    }

    /* =========================================================
       ALERT (pesan proses dari controller)
    ========================================================= */
    .corp-form-container .alert {
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        font-size: 13px;
        padding: 11px 16px;
    }

    .corp-form-container .alert-info {
        background-color: var(--corp-primary-light);
        border-color: #bcd0e6;
        color: var(--corp-primary);
    }

    /* =========================================================
       HEADER PO TERKUNCI (dipakai oleh po.js)
    ========================================================= */
    .corp-form-container .po-locked {
        pointer-events: none !important;
    }

    .corp-form-container .form-control.po-locked {
        background-color: var(--corp-bg-disabled) !important;
        opacity: 1;
    }

    .corp-form-container .select2-container.po-locked .select2-selection {
        background-color: var(--corp-bg-disabled) !important;
        opacity: 1;
    }

    .corp-form-container .select2-container.po-locked .select2-selection__clear {
        display: none !important;
    }

    .corp-form-container .form-check.po-locked {
        opacity: 0.85;
    }

    /* =========================================================
       VALIDATION
    ========================================================= */
    .corp-form-container .is-invalid,
    .corp-form-container .form-control.is-invalid {
        border-color: var(--corp-danger) !important;
    }

    .corp-form-container .invalid-feedback {
        display: block;
        color: var(--corp-danger);
        font-size: 11.5px;
        margin-top: 4px;
    }

    /* =========================================================
       RESPONSIVE TWEAK
    ========================================================= */
    @media (max-width: 768px) {
        .corp-form-container .card-body {
            padding: 14px;
        }

        .corp-form-container .section-block {
            padding: 16px 14px;
        }

        .corp-form-container .card-header {
            flex-wrap: wrap;
        }

        .corp-form-container .po-summary-row {
            flex-wrap: wrap;
        }

        .corp-form-container .po-summary-row label {
            flex: 0 0 100%;
        }
    }


    /* =========================================================
       PO HIGH CONTRAST OVERRIDE
       Selaras dengan standar aksesibilitas v_add_pp.php.
       CSS ONLY: tidak mengubah field, ID, name, JS, atau proses.
    ========================================================= */
    .corp-form-container,
    .corp-form-container .form-control,
    .corp-form-container .form-select,
    .corp-form-container select,
    .corp-form-container textarea {
        color: #111111 !important;
        -webkit-font-smoothing: auto !important;
    }

    .corp-form-container .form-control,
    .corp-form-container .form-select,
    .corp-form-container select.form-control,
    .corp-form-container .modal .form-control,
    .corp-form-container .modal select.form-control,
    .corp-form-container .modal input.form-control {
        color: #111111 !important;
        background-color: #ffffff !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        border: 1px solid #777777 !important;
    }

    .corp-form-container .form-control::placeholder,
    .corp-form-container textarea::placeholder {
        color: #555555 !important;
        opacity: 1 !important;
        font-weight: 500 !important;
    }

    .corp-form-container .form-control:disabled,
    .corp-form-container .form-control[readonly],
    .corp-form-container .select2-container--disabled .select2-selection {
        color: #111111 !important;
        background-color: #e9ecef !important;
        opacity: 1 !important;
    }

    .corp-form-container label,
    .corp-form-container .form-check-label,
    .corp-form-container .input-group-text {
        color: #111111 !important;
        font-size: 13px !important;
        font-weight: 700 !important;
    }

    /* Select2: teks pilihan dan dropdown lebih tegas */
    .corp-form-container .select2-container .select2-selection--single .select2-selection__rendered,
    .corp-form-container .select2-container .select2-selection--multiple,
    .corp-form-container .select2-results__option,
    .corp-form-container .select2-search__field {
        color: #111111 !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 15px !important;
        font-weight: 600 !important;
    }

    .corp-form-container .select2-container .select2-selection--single {
        border-color: #777777 !important;
    }

    .corp-form-container .select2-results__option--highlighted,
    .corp-form-container .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #1e3a5f !important;
        color: #ffffff !important;
    }

    /* DataTable detail PO: high contrast + large text */
    .corp-form-container #tmptabspktransfersdtl,
    .corp-form-container #tmptabspktransfersdtl_wrapper {
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 15px !important;
        -webkit-font-smoothing: auto !important;
    }

    .corp-form-container #tmptabspktransfersdtl thead th {
        background: #19385f !important;
        color: #ffffff !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        padding: 14px 11px !important;
        border: 1px solid #19385f !important;
    }

    .corp-form-container #tmptabspktransfersdtl tbody tr td {
        color: #000000 !important;
        background: #ffffff !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        line-height: 1.5 !important;
        padding: 13px 11px !important;
        border: 1px solid #a0a0a0 !important;
        vertical-align: middle !important;
    }

    .corp-form-container #tmptabspktransfersdtl tbody tr:hover td {
        background: #fff0b3 !important;
        color: #000000 !important;
    }

    .corp-form-container #tmptabspktransfersdtl tbody tr.selected td {
        background: #dcebfa !important;
        color: #000000 !important;
        font-weight: 700 !important;
    }

    .corp-form-container #tmptabspktransfersdtl tbody td.dataTables_empty {
        color: #000000 !important;
        background: #ffffff !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        padding: 18px !important;
        text-align: center;
    }

    .corp-form-container #tmptabspktransfersdtl input[type="checkbox"] {
        width: 19px !important;
        height: 19px !important;
        accent-color: #19385f !important;
        cursor: pointer;
    }

    /* Kontrol DataTable: search, length, info, pagination */
    .corp-form-container .dataTables_wrapper .dataTables_filter label,
    .corp-form-container .dataTables_wrapper .dataTables_length label,
    .corp-form-container .dataTables_wrapper .dataTables_info {
        color: #111111 !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 14px !important;
        font-weight: 700 !important;
    }

    .corp-form-container .dataTables_wrapper .dataTables_filter input,
    .corp-form-container .dataTables_wrapper .dataTables_length select {
        color: #111111 !important;
        background: #ffffff !important;
        border: 1px solid #666666 !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        min-height: 40px !important;
    }

    .corp-form-container .dataTables_wrapper .pagination .page-link {
        color: #19385f !important;
        border-color: #777777 !important;
        font-size: 14px !important;
        font-weight: 700 !important;
    }

    .corp-form-container .dataTables_wrapper .pagination .page-item.active .page-link {
        background: #19385f !important;
        border-color: #19385f !important;
        color: #ffffff !important;
    }

    /* Isi tabel pada modal, termasuk History Harga */
    .corp-form-container .modal table tbody td {
        color: #111111 !important;
        background: #ffffff;
        font-size: 14px !important;
        font-weight: 600 !important;
        border-color: #a0a0a0 !important;
    }

    .corp-form-container .modal table thead th {
        background: #19385f !important;
        color: #ffffff !important;
        font-size: 13px !important;
        font-weight: 700 !important;
    }

    .corp-form-container .modal .modal-title {
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    /* Fokus input terlihat jelas */
    .corp-form-container .form-control:focus,
    .corp-form-container .form-select:focus,
    .corp-form-container .select2-container--focus .select2-selection,
    .corp-form-container .modal .form-control:focus {
        border-color: #1e3a5f !important;
        color: #111111 !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.20) !important;
        outline: none !important;
    }



    /* =========================================================
       SELECT2 - CORPORATE HIGH CONTRAST (FINAL OVERRIDE)
       Samakan ukuran, font, border, dan warna dengan form-control
    ========================================================= */
    .corp-form-container .select2-container,
    .corp-form-container .select2-container--default {
        width: 100% !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 15px !important;
    }

    .corp-form-container .select2-container--default .select2-selection--single {
        height: 40px !important;
        min-height: 40px !important;
        display: flex !important;
        align-items: center !important;
        background: #ffffff !important;
        border: 1px solid #777777 !important;
        border-radius: 4px !important;
        box-sizing: border-box !important;
    }

    .corp-form-container .select2-container--default .select2-selection--single .select2-selection__rendered {
        width: 100% !important;
        padding: 0 32px 0 11px !important;
        line-height: 38px !important;
        color: #111111 !important;
        font-size: 15px !important;
        font-weight: 600 !important;
    }

    .corp-form-container .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #555555 !important;
        opacity: 1 !important;
        font-weight: 500 !important;
    }

    .corp-form-container .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 0 !important;
        right: 5px !important;
    }

    .corp-form-container .select2-container--default .select2-selection--multiple {
        min-height: 40px !important;
        background: #ffffff !important;
        border: 1px solid #777777 !important;
        border-radius: 4px !important;
        box-sizing: border-box !important;
        color: #111111 !important;
        font-size: 15px !important;
        font-weight: 600 !important;
    }

    .corp-form-container .select2-container--default.select2-container--focus .select2-selection,
    .corp-form-container .select2-container--default.select2-container--open .select2-selection {
        border-color: #1e3a5f !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.20) !important;
        outline: none !important;
    }

    /* Dropdown list dan setiap pilihan */
    .corp-form-container .select2-dropdown {
        background: #ffffff !important;
        border: 1px solid #777777 !important;
        border-radius: 4px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16) !important;
        z-index: 1070 !important;
    }

    .corp-form-container .select2-results__option {
        padding: 9px 11px !important;
        background: #ffffff !important;
        color: #111111 !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        line-height: 1.4 !important;
    }

    .corp-form-container .select2-container--default .select2-results__option--highlighted[aria-selected],
    .corp-form-container .select2-container--default .select2-results__option--highlighted[aria-selected="true"] {
        background: #1e3a5f !important;
        color: #ffffff !important;
    }

    .corp-form-container .select2-container--default .select2-results__option[aria-selected="true"] {
        background: #dce6f1 !important;
        color: #111111 !important;
        font-weight: 700 !important;
    }

    /* Kotak pencarian dropdown */
    .corp-form-container .select2-search--dropdown .select2-search__field {
        min-height: 38px !important;
        padding: 6px 9px !important;
        background: #ffffff !important;
        color: #111111 !important;
        border: 1px solid #777777 !important;
        border-radius: 3px !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 15px !important;
        font-weight: 600 !important;
    }

    .corp-form-container .select2-search--dropdown .select2-search__field:focus {
        border-color: #1e3a5f !important;
        box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.16) !important;
        outline: none !important;
    }

    /* Dropdown Select2 yang ditempatkan di dalam modal */
    .corp-form-container .modal .select2-container {
        width: 100% !important;
    }

    .corp-form-container .modal .select2-dropdown,
    .corp-form-container .modal .select2-container--open {
        z-index: 1075 !important;
    }

    /* Select2 disabled tetap terbaca */
    .corp-form-container .select2-container--default.select2-container--disabled .select2-selection {
        background: #eeeeee !important;
        border-color: #999999 !important;
        opacity: 1 !important;
    }

    .corp-form-container .select2-container--default.select2-container--disabled .select2-selection__rendered {
        color: #333333 !important;
    }



    /* =========================================================
       SELECT2 OPEN DROPDOWN - SAMAKAN DENGAN v_add_pp
       Fokus pada panel dropdown, search box, dan option aktif.
       Tidak mengubah konfigurasi AJAX / formatter / business logic.
       ========================================================= */
    .select2-container--open {
        z-index: 1075 !important;
    }

    .select2-container--open .select2-dropdown {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0 0 4px 4px !important;
        box-shadow: 0 3px 8px rgba(0, 0, 0, .12) !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }

    .select2-container--open .select2-search--dropdown {
        display: block !important;
        padding: 6px !important;
        background: #ffffff !important;
        border-bottom: 1px solid #d5dce5 !important;
    }

    .select2-container--open .select2-search--dropdown .select2-search__field {
        width: 100% !important;
        min-height: 38px !important;
        padding: 7px 10px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 4px !important;
        background: #ffffff !important;
        color: #111827 !important;
        font-family: Arial, 'Segoe UI', sans-serif !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        outline: none !important;
        box-sizing: border-box !important;
    }

    .select2-container--open .select2-search--dropdown .select2-search__field:focus {
        border-color: #1e3a5f !important;
        box-shadow: 0 0 0 2px rgba(30, 58, 95, .12) !important;
    }

    .select2-container--open .select2-results > .select2-results__options {
        max-height: 240px !important;
        overflow-y: auto !important;
        background: #ffffff !important;
    }

    .select2-container--open .select2-results__option {
        min-height: 40px !important;
        padding: 10px 12px !important;
        color: #111827 !important;
        background: #ffffff !important;
        font-family: Arial, 'Segoe UI', sans-serif !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        line-height: 20px !important;
        border: 0 !important;
    }

    .select2-container--open .select2-results__option--highlighted[aria-selected] {
        color: #ffffff !important;
        background: #1e3a5f !important;
    }

    .select2-container--open .select2-results__option[aria-selected="true"] {
        color: #111827 !important;
        background: #e8eef5 !important;
        font-weight: 700 !important;
    }

    .select2-container--open .select2-results__option--highlighted[aria-selected="true"] {
        color: #ffffff !important;
        background: #1e3a5f !important;
    }

    .select2-container--open .select2-results__option .select2-result-repository__description {
        color: inherit !important;
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    .select2-container--open .select2-results__option .fa {
        color: inherit !important;
        margin: 0 4px !important;
    }

    .select2-container--open .select2-results__message {
        padding: 10px 12px !important;
        color: #374151 !important;
        font-size: 14px !important;
        font-weight: 600 !important;
    }



    /* =========================================================
       PENYESUAIAN POSISI CABANG / JOB
       Mengikuti ritme spacing form v_add_pp: label dan Select2
       diberi ruang atas agar tidak menempel ke garis section.
    ========================================================= */
    .corp-form-container .form-group:has(> label[for="cabang"]) {
        padding-top: 0px;
    }

    .corp-form-container .form-group:has(> label[for="cabang"]) > label {
        margin-bottom: 8px;
        line-height: 1.35;
    }

    .corp-form-container .form-group:has(> label[for="cabang"]) .select2-container {
        margin-top: 1px;
    }

    /* Select2 rendered text: center vertically tanpa margin negatif */
    .corp-form-container .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
        height: auto !important;
        margin-top: 0 !important;
        line-height: 38px !important;
        box-sizing: border-box !important;
    }


    /* Pajak: beri ruang antara Select2 dan checkbox Inclusive */
    .corp-form-container #idtax + .select2-container {
        display: block !important;
        width: 100% !important;
        margin-bottom: 10px !important;
        float: none !important;
        clear: both !important;
    }

    .corp-form-container #idtax + .select2-container + .form-check.corporate-check {
        display: flex !important;
        position: relative !important;
        clear: both !important;
        float: none !important;
        width: 100% !important;
        min-height: 22px !important;
        margin: 0 0 4px 0 !important;
        padding: 0 !important;
        align-items: center !important;
        gap: 8px !important;
        z-index: 1;
    }

    .corp-form-container #idtax + .select2-container + .form-check.corporate-check .form-check-input {
        position: static !important;
        flex: 0 0 16px !important;
        margin: 0 !important;
    }

    .corp-form-container #idtax + .select2-container + .form-check.corporate-check .form-check-label {
        display: inline-block !important;
        margin: 0 !important;
        line-height: 1.4 !important;
    }

</style>
<div class="corp-form-container">

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
            <?= session()->getFlashdata('error'); ?>
        </div>

    <?php endif; ?>
    <?php
    $isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
    $disabled = $isIT ? '' : 'disabled';
    ?>
    <div class="row">
        <!-- left column -->
        <form action="<?= base_url('persediaan/trans/finalTransferLocation') ?>" method="post" id="formTransferLocationMasters">
            <div class="col-md-12">
                <!-- jquery validation -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-exchange"></i> <?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Transfer Antar Lokasi</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Transfer Antar Gudang
                            </div>
                            <div class="row">

                                <!-- KOLOM 1 : IDENTITAS DOKUMEN -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label for="cabang">Cabang / Job</label>
                                        <select name="cabang" id="cabang" class="form-control" required></select>
                                    </div>

                                    <div class="form-group">
                                        <label>No. Jurnal</label>

                                        <div class="d-flex align-items-center">
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

                                    <input type="hidden" name="docno" id="docno" maxlength="20" value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>" style="text-transform: uppercase;" readonly>
                                </div>

                                <!-- KOLOM 2 : TANGGAL & REFERENSI -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label for="docdate">Tanggal Transfer</label>
                                        <input type="text"
                                               name="docdate"
                                               id="docdate"
                                               class="form-control"
                                               placeholder="Tanggal">
                                    </div>

                                    <div class="form-group">
                                        <label for="docref">No. Referensi / SPKB</label>
                                        <input name="docref"
                                               id="docref"
                                               class="form-control"
                                               placeholder="No. Referensi / SPKB">
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="pemohon">Pemohon</label>
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

                                <!-- KOLOM 3 : GUDANG ASAL -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label for="idlocation_from">Dari Gudang</label>
                                        <select name="idlocation_from"
                                                id="idlocation_from"
                                                class="form-control"
                                                required></select>
                                    </div>

                                    <div class="form-group">
                                        <label for="idlocation_transit">Gudang Transit</label>
                                        <select name="idlocation_transit"
                                                id="idlocation_transit"
                                                class="form-control"
                                                required></select>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan"
                                                  id="keterangan"
                                                  class="form-control"
                                                  rows="2"
                                                  placeholder="Catatan / Informasi..."
                                                  style="text-transform:uppercase;"></textarea>
                                    </div>
                                </div>

                                <!-- KOLOM 4 : TUJUAN TRANSFER -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label for="cabang_sent">Cabang / Job (Sent)</label>
                                        <select name="cabang_sent"
                                                id="cabang_sent"
                                                class="form-control"
                                                required></select>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="idlocation_to">Gudang Kirim</label>
                                        <select name="idlocation_to"
                                                id="idlocation_to"
                                                class="form-control"
                                                required></select>
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
                                    title="Input Data"
                                    id="btnAddDetail">
                                <i class="fa fa-plus"></i>
                            </button>

                            <button type="button"
                                    id="btnUpdateDetail"
                                    class="btn btn-warning btn-lg action-btn"
                                    data-bs-toggle="tooltip"
                                    title="Update Data"
                                    onclick="updateTransferLocationDetail()">
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table id="tmptabspktransfersdtl" class="table table-bordered table-striped" style="width:100%;" cellspacing="0">
                                        <thead class="bg-primary text-white">
                                        <tr>
                                            <th class="text-center" width="40">
                                                <input type="checkbox" id="checkAll">
                                            </th>
                                            <th>ID Barang</th>
                                            <th>Nama Barang</th>
                                            <th class="text-center">Satuan</th>

                                            <!-- Kolom Qty Input -->
                                            <th class="text-end">
                                                Qty Input
                                                <i class="bi bi-info-circle"
                                                   title="Quantity sesuai satuan input user"></i>
                                            </th>

                                            <!-- Kolom Unit Default -->
                                            <th class="text-center">
                                                Unit Default
                                                <i class="bi bi-info-circle"
                                                   title="Satuan default untuk stock (base unit)"></i>
                                            </th>

                                            <!-- Kolom Qty Base -->
                                            <th class="text-end">
                                                Qty Base
                                                <i class="bi bi-info-circle"
                                                   title="Qty setelah konversi ke unit default (yang dipakai untuk reserve stock)"></i>
                                            </th>

                                            <th>Keterangan</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="<?= base_url('persediaan/trans/transfer_lokasi') ?>"
                           class="btn btn-default float-left">
                            <i class="fa fa-arrow-left"></i>
                            Back
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
<div class="modal fade" id="modalDetailSPK" tabindex="-1" role="dialog" aria-labelledby="modalDetailPPLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDetailPPLabel">
                    Input Item Detail
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM -->
            <form id="formSPKtransfersDtl">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="idurut" id="idurut">
                    <input type="hidden" name="iduniq" id="iduniq">
                    <!-- Hidden fields -->
                    <input type="hidden" name="unit_default" id="unit_default">
                    <input type="hidden" name="qty_base"     id="qty_base">
                    <!--<input type="hidden" name="docno" id="docno">-->
                    <!-- <input type="hidden" name="status" id="status" value="P">
                    <input type="hidden" name="chold" id="chold" value="NO"> -->

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ID Barang</label>
                                <!-- ✅ BENAR -->
                                <select name="idbarang" id="idbarang" class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>

                        <div class="col-md-4">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Batch / Specification</label>
                                <select name="batch"
                                        id="batch"
                                        class="form-control select2"
                                        style="width:100%"
                                        data-placeholder="Pilih Batch / Specification"></select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>QTY Stock</label>
                                <input type="text"
                                       name="qtystock"
                                       id="qtystock"
                                       class="form-control"
                                       placeholder="Qty Stock"
                                       style="text-transform:uppercase" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="row">
                        <div class="col-md-3">
                            <label>Satuan</label>
                            <select name="unit" id="unit" class="form-control" style="width:100%"></select>
                        </div>

                        <div class="col-md-2">
                            <label>Konversi</label>
                            <input name="conversion_factor" id="conversion_factor" class="form-control" readonly>
                            <input type="hidden" name="qtystock_raw" id="qtystock_raw" value="0">
                        </div>

                        <div class="col-md-2">
                            <label>Qty Sent</label>
                            <input name="qty" id="qty" class="form-control jtsseparator ratakanan">
                        </div>

                        <div class="col-md-5">   <!-- 🔥 3+2+2+5 = 12 -->
                            <label>Keterangan</label>
                            <textarea name="description" id="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    <button type="button"
                            class="btn btn-primary"
                            onclick="saveTransferStock()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="$('#modalDetailSPK').modal('hide')">
                        <i class="fa fa-times"></i> Batal
                    </button>


                </div>
            </form>
        </div>
    </div>
</div>
</div><!-- /.corp-form-container -->



<script type="application/javascript" src="<?= base_url('assets/pagejs/persediaan/transferlocation/stock_transfers_location.js') ?>"></script>
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
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });



        $('#estpakai').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#estpakai').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
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
