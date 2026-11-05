<!-- =========================================================
     v_add_lpb.php - Corporate Style
     Referensi tampilan : purchase/po/v_add_po.php (versi corporate)
     Catatan : perubahan hanya pada font, warna, spacing, dan
               kerapian layout. Nama field, id, dan fungsi
               JavaScript tidak diubah.
========================================================== -->
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
        box-sizing: border-box;
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

    /* angka rata kanan (class global .ratakanan) */
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

    /* select2 dalam kondisi disabled (header terkunci oleh lpb.js) */
    .corp-form-container .select2-container--disabled .select2-selection--single {
        background-color: var(--corp-bg-disabled) !important;
        border-color: var(--corp-border) !important;
    }

    .corp-form-container .select2-container--disabled .select2-selection--single .select2-selection__rendered {
        color: #202020 !important;
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

    .corp-form-container #btnNew {
        margin-left: 8px;
        white-space: nowrap;
    }

    .corp-form-container #btnNew i {
        margin-right: 4px;
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
       TABLE DETAIL (id: tablpbdtl)
    ========================================================= */
    .corp-form-container #tablpbdtl {
        background-color: #ffffff;
        margin-bottom: 0 !important;
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px;
        overflow: hidden;
        font-size: 13px;
        width: 100%;
    }

    .corp-form-container #tablpbdtl thead th {
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

    .corp-form-container #tablpbdtl thead th:first-child {
        text-align: center;
    }

    .corp-form-container #tablpbdtl thead th span.dt-column-order::before,
    .corp-form-container #tablpbdtl thead th span.dt-column-order::after {
        color: rgba(255, 255, 255, 0.55) !important;
    }

    .corp-form-container #tablpbdtl tbody td {
        padding: 10px 12px !important;
        border-color: var(--corp-border) !important;
        vertical-align: middle;
        color: var(--corp-text);
    }

    .corp-form-container #tablpbdtl tbody tr:nth-child(even) {
        background-color: #fbfcfe;
    }

    .corp-form-container #tablpbdtl tbody tr:hover {
        background-color: var(--corp-primary-light) !important;
    }

    .corp-form-container #tablpbdtl tbody tr.selected {
        background-color: #dbeafe !important;
    }

    .corp-form-container #tablpbdtl tbody td.dataTables_empty {
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
    .corp-form-container .corp-divider {
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
    .corp-form-container #modalDetailLPBLabel::before {
        content: "0fe"; /* fa-plus-square-o */
        font-family: FontAwesome, "Font Awesome 5 Free", sans-serif;
        font-weight: normal;
        margin-right: 8px;
    }

    .corp-form-container #modalUpdateLPBLabel::before {
        content: "044"; /* fa-pencil-square-o */
        font-family: FontAwesome, "Font Awesome 5 Free", sans-serif;
        font-weight: normal;
        margin-right: 8px;
    }

    .corp-form-container #modalNewSpecLabel::before {
        content: "0fe"; /* fa-plus-square-o */
        font-family: FontAwesome, "Font Awesome 5 Free", sans-serif;
        font-weight: normal;
        margin-right: 8px;
    }

    /* tombol close modal (Bootstrap 5) */
    .corp-form-container .modal-header .btn-close {
        opacity: 0.9;
        box-shadow: none;
    }

    .corp-form-container .modal-header .btn-close:hover {
        opacity: 1;
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

    .corp-form-container .modal .select2-selection--single {
        height: 40px !important;
        min-height: 40px !important;
    }

    .corp-form-container .modal .form-group {
        margin-bottom: 16px;
    }

    .corp-form-container .modal .input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    /* =========================================================
       RINGKASAN NILAI (DPP / PAJAK / TOTAL)
    ========================================================= */
    .corp-form-container .corp-summary {
        background: var(--corp-bg-section);
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        padding: 14px 16px;
        box-shadow: var(--corp-shadow);
    }

    .corp-form-container .corp-summary-title {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: var(--corp-primary);
        padding-bottom: 9px;
        margin-bottom: 12px;
        border-bottom: 1px solid var(--corp-border);
    }

    .corp-form-container .corp-summary-title i {
        margin-right: 6px;
    }

    .corp-form-container .corp-summary-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .corp-form-container .corp-summary-row:last-child {
        margin-bottom: 0;
    }

    .corp-form-container .corp-summary-row label {
        flex: 0 0 45%;
        margin-bottom: 0;
        font-size: 12px;
        font-weight: 600;
        color: var(--corp-text);
    }

    .corp-form-container .corp-summary-row .form-control {
        flex: 1 1 auto;
        text-align: right;
        font-weight: 600;
    }

    .corp-form-container .corp-summary-total label {
        color: var(--corp-primary);
        font-weight: 700;
    }

    .corp-form-container .corp-summary-total .form-control {
        background: var(--corp-primary-light) !important;
        border-color: var(--corp-primary) !important;
        color: var(--corp-primary) !important;
        font-weight: 700;
        font-size: 15px;
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

        .corp-form-container .corp-summary-row {
            flex-wrap: wrap;
        }

        .corp-form-container .corp-summary-row label {
            flex: 0 0 100%;
        }
    }


    /* =========================================================
       CORPORATE HIGH CONTRAST OVERRIDE
       Diselaraskan dengan v_add_po.php; hanya styling.
    ========================================================= */
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
    .corp-form-container #tablpbdtl,
    .corp-form-container #tablpbdtl_wrapper {
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 15px !important;
        -webkit-font-smoothing: auto !important;
    }

    .corp-form-container #tablpbdtl thead th {
        background: #19385f !important;
        color: #ffffff !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        padding: 14px 11px !important;
        border: 1px solid #19385f !important;
    }

    .corp-form-container #tablpbdtl tbody tr td {
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

    .corp-form-container #tablpbdtl tbody tr:hover td {
        background: #fff0b3 !important;
        color: #000000 !important;
    }

    .corp-form-container #tablpbdtl tbody tr.selected td {
        background: #dcebfa !important;
        color: #000000 !important;
        font-weight: 700 !important;
    }

    .corp-form-container #tablpbdtl tbody td.dataTables_empty {
        color: #000000 !important;
        background: #ffffff !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        padding: 18px !important;
        text-align: center;
    }

    .corp-form-container #tablpbdtl input[type="checkbox"] {
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
    <?php
    $isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
    $disabled = $isIT ? '' : 'disabled';
    ?>
    <div class="row">
        <!-- left column -->
        <form action="<?= base_url('purchase/trans/finalEntryLPB') ?>" method="post" id="formPO">
            <div class="col-md-12">
                <!-- jquery validation -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-file-text-o"></i> <?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Penerimaan Pembelian</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Penerimaan Pembelian
                            </div>
                            <div class="row">

                                <!-- KOLOM 1 : IDENTITAS DOKUMEN -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label for="cabang">Cabang / Job</label>
                                        <select name="cabang" id="cabang" class="form-control" required></select>
                                    </div>

                                    <div class="form-group">
                                        <label>No. Jurnal Penerimaan Pembelian</label>

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

                                <!-- KOLOM 2 : TANGGAL -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label for="docdate">Tanggal</label>
                                        <input type="text"
                                               name="docdate"
                                               id="docdate"
                                               class="form-control"
                                               placeholder="Tanggal">
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="jthtempo">Jatuh Tempo</label>
                                        <div class="input-group">
                                            <input type="text"
                                                   name="jthtempo"
                                                   id="jthtempo"
                                                   class="form-control ratakanan jtsseparator"
                                                   placeholder="0.00">
                                            <div class="input-group-append">
                                                <span class="input-group-text">hari</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KOLOM 3 : SUPPLIER & PAJAK -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label for="kdsupplier">Supplier</label>
                                        <select name="kdsupplier" id="kdsupplier" class="form-control select2" required></select>
                                    </div>

                                    <div class="form-group">
                                        <label for="alamatsupplier">Alamat Supplier</label>
                                        <textarea name="alamatsupplier"
                                                  id="alamatsupplier"
                                                  class="form-control"
                                                  rows="2"
                                                  placeholder="Alamat Supplier"
                                                  style="text-transform:uppercase;"></textarea>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="idtax">Pajak</label>
                                        <select name="idtax" id="idtax" class="form-control select2"></select>

                                        <!-- Inclusive -->
                                        <div class="form-check corporate-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   disabled
                                                   name="isinclusive"
                                                   id="isinclusive"
                                                    <?= isset($data['isinclusive']) && $data['isinclusive'] === 'YES' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="isinclusive">
                                                Inclusive
                                            </label>
                                        </div>
                                    </div>

                                    <!-- field biaya/vol (disembunyikan, tetap dipakai lpb.js) -->
                                    <div class="d-none">
                                        <div class="form-group">
                                            <label for="biayavol">Biaya/Vol</label>
                                            <input name="biayavol"
                                                   id="biayavol"
                                                   class="form-control jtsseparator ratakanan"
                                                   type="text"
                                                   placeholder="Biaya/Vol"
                                                   style="text-transform:uppercase;">
                                        </div>
                                        <div class="form-group">
                                            <label for="biayavol2">Biaya2/Vol</label>
                                            <input name="biayavol2"
                                                   id="biayavol2"
                                                   class="form-control jtsseparator ratakanan"
                                                   type="text"
                                                   placeholder="Biaya2/Vol"
                                                   style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                </div>

                                <!-- KOLOM 4 : MATA UANG & DOKUMEN -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label for="currcode">Mata Uang</label>
                                        <select name="currcode"
                                                id="currcode"
                                                class="form-select select2"
                                                required>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="kurs">Nilai Tukar</label>
                                        <input type="text"
                                               name="kurs"
                                               id="kurs"
                                               class="form-control ratakanan jtsseparator"
                                               placeholder="Nilai tukar akan muncul disini">
                                    </div>

                                    <div class="form-group">
                                        <label for="nofaktur">No. Faktur</label>
                                        <input name="nofaktur"
                                               id="nofaktur"
                                               class="form-control"
                                               type="text"
                                               maxlength="30"
                                               placeholder="No. Faktur"
                                               style="text-transform:uppercase;">
                                    </div>

                                    <div class="form-group mb-0">
                                        <label for="nosj">No. SJ</label>
                                        <input name="nosj"
                                               id="nosj"
                                               class="form-control"
                                               type="text"
                                               maxlength="30"
                                               placeholder="No. SJ"
                                               style="text-transform:uppercase;">
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>


                <div class="card mt-3 card-primary">
                    <div class="card-header clearfix">
                        <h3 class="card-title">
                            <i class="fa fa-list-alt"></i> Detail PO & Barang
                        </h3>

                        <div class="header-actions">
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table id="tablpbdtl" class="table table-bordered table-striped" style="width:100%;" cellspacing="0">
                                        <thead class="bg-primary text-white">
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="checkAll">
                                            </th>
                                            <th>PO</th>
                                            <th>ID Barang</th>
                                            <th>Nama Barang</th>
                                            <th>No. Capex</th>
                                            <th>Gudang</th>
                                            <th>Batch</th>
                                            <th>ETD</th>
                                            <th>ETD Notes</th>
                                            <th>Satuan</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <!--                                        <th>Multi Disc</th>-->
                                            <!--                                        <th>Vol/Item</th>-->
                                            <!--                                        <th>Biaya 1</th>-->
                                            <!--                                        <th>Biaya 2</th>-->
                                            <th>Nilai</th>
                                            <th>Keterangan PO</th>
                                            <th>Keterangan PP</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr class="corp-divider">

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-0">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea name="keterangan"
                                              id="keterangan"
                                              class="form-control"
                                              rows="4"
                                              placeholder="Keterangan"
                                              style="text-transform:uppercase;"></textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="corp-summary">
                                    <div class="corp-summary-title">
                                        <i class="fa fa-calculator"></i> Ringkasan Nilai
                                    </div>

                                    <div class="corp-summary-row">
                                        <label for="dpp">DPP</label>
                                        <input type="text"
                                               name="dpp"
                                               id="dpp"
                                               class="form-control jtsseparator ratakanan"
                                               readonly>
                                    </div>

                                    <div class="corp-summary-row">
                                        <label for="jumlahpajak">Jumlah Pajak</label>
                                        <input type="text"
                                               name="jumlahpajak"
                                               id="jumlahpajak"
                                               class="form-control jtsseparator ratakanan"
                                               readonly>
                                    </div>

                                    <div class="corp-summary-row corp-summary-total">
                                        <label for="total">Total</label>
                                        <input type="text"
                                               name="total"
                                               id="total"
                                               class="form-control jtsseparator ratakanan"
                                               readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light">
                        <a href="<?= base_url('purchase/trans/clearEntryLPB') ?>"
                           onclick="return confirm('Are you sure clear this entry?')"
                           class="btn btn-default float-left btn-lg">
                            <i class="fa fa-arrow-left"></i>
                            Back
                        </a>
                        <button type="button"
                                id="btnDeleteLPB"
                                class="btn btn-danger float-left btn-lg">
                            <i class="fa fa-trash"></i>
                            Hapus Penerimaan
                        </button>

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




    <!-- ================= MODAL INPUT DETAIL ================= -->
    <div class="modal fade" id="modalDetailLPB" tabindex="-1" role="dialog" aria-labelledby="modalDetailLPBLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="modalDetailLPBLabel">
                        Input Item Detail
                    </h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- FORM -->
                <form id="formLPBDetail">
                    <div class="modal-body">

                        <!-- hidden -->
                        <input type="hidden" name="idurut" id="idurut">
                        <input type="hidden" name="docno" id="docno">

                        <!-- ROW 1 -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>PO</label>
                                    <select name="docnopo" id="docnopo"
                                            class="form-control select2"
                                            style="width:100%"></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer bg-light">
                        <button type="button"
                                class="btn btn-primary"
                                onclick="saveLPBDetail()">
                            <i class="fa fa-save"></i> Process
                        </button>
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                onclick="$('#modalDetailLPB').modal('hide')">
                            <i class="fa fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- ================= MODAL UPDATE DETAIL ================= -->
    <div class="modal fade" id="modalUpdateLPB" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLPBLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="modalUpdateLPBLabel">
                        Edit Item Detail
                    </h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- FORM -->
                <form id="formLPBUpdate">
                    <div class="modal-body">

                        <!-- hidden -->
                        <input type="hidden" name="idurut" id="idurut">
                        <input type="hidden" name="uniqueid" id="uniqueid">
                        <input type="hidden" name="docno" id="docno">
                        <input type="hidden" name="docnopo" id="docnopo">

                        <!-- ROW 1 -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>PO</label>
                                    <input name="docnopomodal" id="docnopomodal"
                                           class="form-control"
                                           style="width:100%" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>ID Barang</label>
                                    <input name="idbarang" id="idbarang"
                                           class="form-control"
                                           style="width:100%" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
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

                        <div class="row g-3">

                            <!-- PRINCIPAL -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="idprincipal">Principal</label>

                                    <select
                                            name="idprincipal"
                                            id="idprincipal"
                                            class="form-control select2"
                                            style="width: 100%;"
                                    >
                                    </select>
                                </div>
                            </div>

                            <!-- GUDANG -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="idgudang">Gudang</label>

                                    <select
                                            name="idgudang"
                                            id="idgudang"
                                            class="form-control select2"
                                            style="width: 100%;"
                                    >
                                    </select>
                                </div>
                            </div>

                            <!-- BATCH -->
                            <div class="col-md-4">
                                <label for="batch" class="form-label">Batch</label>

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
                                            title="New Batch"
                                    >
                                        <i class="fa fa-plus"></i>
                                        New
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- ROW 1.5 - ETD -->
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="etd">ETD</label>
                                    <input type="text"
                                           name="etd"
                                           id="etd"
                                           class="form-control"
                                           placeholder="DD-MM-YYYY"
                                           autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="etdnotes">ETD Notes</label>
                                    <input type="text"
                                           name="etdnotes"
                                           id="etdnotes"
                                           class="form-control"
                                           placeholder="Keterangan ETD"
                                           maxlength="100"
                                           style="text-transform:uppercase;"
                                           autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <!-- ROW 2 -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Satuan</label>
                                    <input name="unit" id="unit"
                                           class="form-control select2"
                                           style="width:100%" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text"
                                           name="qty"
                                           id="qty"
                                           class="form-control jtsseparator ratakanan"
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Bonus Quantity</label>
                                    <input type="text"
                                           name="qtybonus"
                                           id="qtybonus"
                                           class="form-control jtsseparator ratakanan"
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga</label>
                                    <input type="text"
                                           name="harga"
                                           id="harga"
                                           class="form-control jtsseparator ratakanan"
                                           placeholder="0.00">
                                </div>
                            </div>

                            <!-- 🔧 MULTI DISC, VOL/ITEM, BIAYA 1, BIAYA 2 DI-HIDE -->
                            <div class="col-md-2 d-none">
                                <div class="form-group">
                                    <label>Multi Disc (%)</label>
                                    <input type="text" name="multidisc" id="multidisc" class="form-control jtsseparator ratakanan" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-2 d-none">
                                <div class="form-group">
                                    <label>Vol/Item</label>
                                    <input type="text" name="volitem" id="volitem" class="form-control jtsseparator ratakanan" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-2 d-none">
                                <div class="form-group">
                                    <label>Biaya 1</label>
                                    <input type="text" name="biaya" id="biaya" class="form-control jtsseparator ratakanan" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-2 d-none">
                                <div class="form-group">
                                    <label>Biaya 2</label>
                                    <input type="text" name="biaya2" id="biaya2" class="form-control jtsseparator ratakanan" placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nilai</label>
                                    <input type="text"
                                           name="nilai"
                                           id="nilai"
                                           class="form-control jtsseparator ratakanan"
                                           readonly
                                           placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Keterangan PO</label>
                                    <textarea type="text"
                                              name="descriptionpo"
                                              rows="4"
                                              style="text-transform: uppercase;"
                                              id="descriptionpo"
                                              class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Keterangan PP</label>
                                    <textarea type="text"
                                              name="descriptionpp"
                                              rows="4"
                                              style="text-transform: uppercase;"
                                              id="descriptionpp"
                                              class="form-control" readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer bg-light">
                        <button type="button"
                                class="btn btn-primary"
                                onclick="saveLPBDetail()">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                onclick="$('#modalUpdateLPB').modal('hide')">
                            <i class="fa fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- MODAL NEW BATCH -->
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
                        New Batch
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
                            Batch
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
</div><!-- /.corp-form-container -->

<script type="application/javascript" src="<?= base_url('assets/pagejs/purchase/lpb.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();

        var dtldata = <?= json_encode($dtldata ?? null) ?>;

        if (dtldata && dtldata.docno && dtldata.docno.trim() !== '') {
            $('#docno').val(dtldata.docno.trim()).prop('readonly', true);
        }

        // ============================
        // DOC DATE
        // ============================
        $('#docdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // ============================
        // ETD (di modal update)
        // ============================
        $('#etd').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        $('#etd').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });
        $('#etd').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // ============================
        // Peride (opsional, kalau dipakai di filter)
        // ============================
        $('#periodemulai').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
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
            locale: { format: 'DD-MM-YYYY' },
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