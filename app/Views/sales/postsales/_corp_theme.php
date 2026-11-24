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
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        box-shadow: var(--corp-shadow);
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

    /* hilangkan clearfix bootstrap (::before/::after) agar tombol bisa mentok kanan */
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
        transform: none;
        will-change: auto;
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

    /* tanda wajib isi (*) */
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
        font-size: 13px;
        color: var(--corp-text);
        background-color: #ffffff;
        height: auto;
        min-height: 36px;
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
    }

    /* Keterangan pada header dibuat lebih pendek agar tampilan ramping */
    .corp-form-container #keterangan {
        min-height: 84px;
        height: auto;
    }

    .corp-form-container .form-control:disabled,
    .corp-form-container .form-control[readonly] {
        background-color: var(--corp-bg-disabled);
        color: var(--corp-text-muted);
        opacity: 1;
        cursor: not-allowed;
        border-color: var(--corp-border);
    }

    /* =========================================================
       SELECT2 (corporate override)
    ========================================================= */
    .corp-form-container .select2-container .select2-selection--single {
        height: 36px !important;
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
        line-height: 34px !important;
        padding-left: 10px !important;
        padding-right: 24px !important;
        font-size: 13px;
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
        height: 34px !important;
        right: 6px;
    }

    .corp-form-container .select2-dropdown {
        border-color: var(--corp-border) !important;
        border-radius: 4px;
        font-size: 13px;
    }

    .corp-form-container .select2-results__option--highlighted {
        background-color: var(--corp-primary) !important;
        color: #ffffff !important;
    }

    /* =========================================================
       CHECKBOX
    ========================================================= */
    .corp-form-container input[type="checkbox"] {
        width: 15px;
        height: 15px;
        accent-color: var(--corp-primary);
        cursor: pointer;
        vertical-align: middle;
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
    .corp-form-container #tabppdtl {
        background-color: #ffffff;
        margin-bottom: 0 !important;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        overflow: hidden;
        font-size: 13px;
    }

    .corp-form-container #tabppdtl thead th {
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

    .corp-form-container #tabppdtl thead th:first-child {
        text-align: center;
    }

    .corp-form-container #tabppdtl thead th span.dt-column-order::before,
    .corp-form-container #tabppdtl thead th span.dt-column-order::after {
        color: rgba(255, 255, 255, 0.55) !important;
    }

    .corp-form-container #tabppdtl tbody td {
        padding: 10px 12px !important;
        border-color: var(--corp-border) !important;
        vertical-align: middle;
        color: var(--corp-text);
    }

    .corp-form-container #tabppdtl tbody tr:hover {
        background-color: var(--corp-primary-light) !important;
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

    /* icon judul modal (dipertahankan walau judul diubah oleh JS) */
    .corp-form-container #modalDetailPPLabel::before {
        content: "\f044"; /* fa-pencil-square-o */
        font-family: FontAwesome, "Font Awesome 5 Free", sans-serif;
        font-weight: normal;
        margin-right: 8px;
    }

    .corp-form-container #modalNewSpecLabel::before {
        content: "\f0fe"; /* fa-plus-square-o */
        font-family: FontAwesome, "Font Awesome 5 Free", sans-serif;
        font-weight: normal;
        margin-right: 8px;
    }

    .corp-form-container .modal-body {
        padding: 20px 22px;
        background: var(--corp-bg-light);
    }

    /* =========================================================
       MODAL FORM CONTROL - UKURAN SERAGAM
       Samakan tinggi input, select, dan Select2 di semua modal.
    ========================================================= */
    .corp-form-container .modal .form-control,
    .corp-form-container .modal select.form-control,
    .corp-form-container .modal input.form-control {
        box-sizing: border-box;
        width: 100%;
        height: 36px !important;
        min-height: 36px !important;
        padding: 7px 10px;
        font-size: 13px;
        line-height: 20px;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
    }

    .corp-form-container .modal textarea.form-control {
        height: auto !important;
        min-height: 76px !important;
        padding: 8px 10px;
        line-height: 1.4;
        resize: vertical;
    }

    .corp-form-container .modal .select2-container {
        width: 100% !important;
    }

    .corp-form-container .modal .select2-container .select2-selection--single {
        box-sizing: border-box;
        height: 36px !important;
        min-height: 36px !important;
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px !important;
    }

    .corp-form-container .modal .select2-container .select2-selection--single .select2-selection__rendered {
        height: 34px;
        line-height: 34px !important;
        padding-left: 10px !important;
        padding-right: 28px !important;
        font-size: 13px;
    }

    .corp-form-container .modal .select2-container .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
        right: 6px;
    }

    .corp-form-container .modal .form-group {
        margin-bottom: 16px;
    }

    .corp-form-container .modal-footer {
        background: var(--corp-bg-light) !important;
        border-top: 1px solid var(--corp-border);
        padding: 12px 22px;
    }

    .corp-form-container #btnNew {
        margin-left: 8px;
        white-space: nowrap;
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
       QUICK DATE (ESTIMASI PAKAI) BUTTONS
    ========================================================= */
    .corp-form-container #estpakai_buttons {
        margin-top: 6px;
    }

    .corp-form-container .estpakai-btn {
        padding: 5px 12px !important;
        background-color: #ffffff !important;
        color: var(--corp-primary) !important;
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px !important;
        font-size: 11.5px !important;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .corp-form-container .estpakai-btn:hover {
        background-color: var(--corp-primary-light) !important;
        border-color: var(--corp-primary) !important;
        color: var(--corp-primary) !important;
    }

    .corp-form-container .estpakai-btn.active {
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
    }



    /* =========================================================
       ACCESSIBILITY: HIGH CONTRAST & READABLE FORM FIELDS
       Prioritas: teks input hitam, ukuran lebih besar, mudah dibaca
    ========================================================= */
    .corp-form-container .form-control,
    .corp-form-container .custom-select,
    .corp-form-container select,
    .corp-form-container input,
    .corp-form-container textarea,
    .modal .form-control,
    .modal .custom-select,
    .modal select,
    .modal input,
    .modal textarea {
        color: #111111 !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        line-height: 1.45 !important;
        letter-spacing: 0.15px;
        border-color: #9ca3af !important;
    }

    .corp-form-container .form-control,
    .corp-form-container .custom-select,
    .modal .form-control,
    .modal .custom-select {
        min-height: 40px;
        padding: 8px 11px !important;
        background-color: #ffffff;
    }

    .corp-form-container textarea.form-control,
    .modal textarea.form-control {
        min-height: 96px;
        line-height: 1.5 !important;
        resize: vertical;
    }

    /* Field readonly/disabled tetap terbaca jelas */
    .corp-form-container .form-control:disabled,
    .corp-form-container .form-control[readonly],
    .corp-form-container select:disabled,
    .modal .form-control:disabled,
    .modal .form-control[readonly],
    .modal select:disabled {
        color: #202020 !important;
        -webkit-text-fill-color: #202020 !important;
        opacity: 1 !important;
        background-color: #e9edf2 !important;
    }

    /* Placeholder jangan terlalu pucat */
    .corp-form-container .form-control::placeholder,
    .modal .form-control::placeholder {
        color: #555555 !important;
        opacity: 1 !important;
        font-weight: 500 !important;
    }

    /* Label dibuat lebih besar dan tegas */
    .corp-form-container label,
    .modal label {
        color: #111827 !important;
        font-size: 13px !important;
        font-weight: 700 !important;
    }

    /* Select2 single select */
    .corp-form-container .select2-container--default .select2-selection--single,
    .modal .select2-container--default .select2-selection--single {
        min-height: 40px !important;
        height: 40px !important;
        border: 1px solid #9ca3af !important;
        background-color: #ffffff !important;
    }

    .corp-form-container .select2-container--default .select2-selection--single .select2-selection__rendered,
    .modal .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #111111 !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        line-height: 38px !important;
        padding-left: 11px !important;
    }

    .corp-form-container .select2-container--default .select2-selection--multiple,
    .modal .select2-container--default .select2-selection--multiple {
        min-height: 40px !important;
        border-color: #9ca3af !important;
        background-color: #ffffff !important;
    }

    .corp-form-container .select2-container--default .select2-selection--multiple .select2-selection__choice,
    .modal .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #111111 !important;
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    /* Opsi dropdown Select2 */
    .select2-container--default .select2-results__option {
        color: #111111 !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        padding: 8px 10px !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        color: #ffffff !important;
        background-color: #1e3a5f !important;
    }

    /* Tombol tetap jelas dan mudah dikenali */
    .corp-form-container .btn,
    .modal .btn {
        font-size: 13px;
        font-weight: 700;
    }

    /* Fokus keyboard/input lebih terlihat */
    .corp-form-container .form-control:focus,
    .corp-form-container select:focus,
    .corp-form-container textarea:focus,
    .modal .form-control:focus,
    .modal select:focus,
    .modal textarea:focus {
        color: #111111 !important;
        border-color: #1e3a5f !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.18) !important;
        outline: none;
    }
    /* Select2 dalam modal Detail PP */
    #modalDetailPP .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }

    #modalDetailPP .select2-container--open {
        z-index: 1065 !important;
    }

    #modalDetailPP .select2-dropdown {
        box-sizing: border-box !important;
        max-width: 100% !important;
        z-index: 1065 !important;
    }

    #modalDetailPP .select2-selection {
        width: 100% !important;
        box-sizing: border-box !important;
    }



    /* ============================================
       J-ACCOUNTING - DATATABLE HIGH CONTRAST
       LARGE TEXT ACCESSIBILITY
       ============================================ */

    /* FONT GLOBAL DATATABLE */
    #tableDetailPP,
    #tableDetailPP_wrapper {
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 15px !important;
        -webkit-font-smoothing: auto !important;
    }

    /* ISI TABEL */
    #tableDetailPP tbody tr td {
        color: #000000 !important;
        background: #FFFFFF !important;
        font-family: Arial, sans-serif !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        line-height: 1.5 !important;
        padding: 13px 11px !important;
        border: 1px solid #A0A0A0 !important;
        vertical-align: middle !important;
    }

    /* HEADER TABEL */
    #tableDetailPP thead th {
        background: #19385F !important;
        color: #FFFFFF !important;
        font-family: Arial, sans-serif !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        padding: 14px 11px !important;
        border: 1px solid #19385F !important;
    }

    /* HOVER BARIS */
    #tableDetailPP tbody tr:hover td {
        background: #FFF0B3 !important;
        color: #000000 !important;
    }

    /* BARIS TERPILIH */
    #tableDetailPP tbody tr.selected td {
        background: #DCEBFA !important;
        color: #000000 !important;
        font-weight: 700 !important;
    }

    /* CHECKBOX */
    #tableDetailPP input[type="checkbox"] {
        width: 19px !important;
        height: 19px !important;
        accent-color: #19385F;
        cursor: pointer;
    }

    /* SEARCH DATATABLE */
    #tableDetailPP_wrapper .dataTables_filter label {
        color: #000000 !important;
        font-size: 15px !important;
        font-weight: 700 !important;
    }

    #tableDetailPP_wrapper .dataTables_filter input {
        color: #000000 !important;
        background: #FFFFFF !important;
        border: 1px solid #666666 !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        min-height: 40px !important;
    }

    /* PAGINATION INFO */
    #tableDetailPP_wrapper .dataTables_info {
        color: #222222 !important;
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    /* EMPTY TABLE */
    #tableDetailPP tbody td.dataTables_empty {
        color: #000000 !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        padding: 18px !important;
    }

    /* BORDER TABLE */
    #tableDetailPP {
        border: 1px solid #777777 !important;
        border-collapse: collapse !important;
    }

</style>
<style>
    /* ===== Production: corporate overrides (add/detail) ===== */
    .corp-form-container .card-header {
        background: #1e3a5f !important;
        color: #ffffff !important;
    }
    .corp-form-container table thead th {
        background: #1e3a5f !important;
        color: #ffffff !important;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        border: none !important;
        vertical-align: middle;
        white-space: nowrap;
    }
    .corp-form-container table thead th span.dt-column-order::before,
    .corp-form-container table thead th span.dt-column-order::after {
        color: rgba(255,255,255,0.55) !important;
    }
    .corp-form-container table tbody td { vertical-align: middle; }

    /* Highlight baris detail yang sedang disorot via keyboard (panah atas/bawah + spasi) */
    .corp-form-container .sales-dtl-table tbody tr.row-active,
    .corp-form-container .sales-dtl-table tbody tr.row-active td {
        background-color: #fff3cd !important;
    }
</style>
<style>
    /* ===== Material Release: tabel detail item (kontras menengah, elegan) ===== */
    .corp-form-container .sales-dtl-table,
    .corp-form-container .dataTables_wrapper {
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 14px !important;
        -webkit-font-smoothing: auto !important;
    }
    .corp-form-container .sales-dtl-table thead th {
        background: #1e3a5f !important;
        color: #ffffff !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        padding: 11px 12px !important;
        border: 1px solid #1e3a5f !important;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        white-space: nowrap;
        vertical-align: middle;
    }
    .corp-form-container .sales-dtl-table thead th span.dt-column-order::before,
    .corp-form-container .sales-dtl-table thead th span.dt-column-order::after {
        color: rgba(255,255,255,0.55) !important;
    }
    .corp-form-container .sales-dtl-table tbody tr td {
        color: #1f2937 !important;
        background: #ffffff !important;
        font-family: Arial, "Segoe UI", sans-serif !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        line-height: 1.5 !important;
        padding: 10px 12px !important;
        border: 1px solid #cbd5e1 !important;
        vertical-align: middle !important;
    }
    .corp-form-container .sales-dtl-table tbody tr:nth-child(even):not(.row-active) td {
        background: #fafbfd !important;
    }
    .corp-form-container .sales-dtl-table tbody tr:hover:not(.row-active) td {
        background: #e8eef5 !important;
        color: #1f2937 !important;
    }
    .corp-form-container .sales-dtl-table tbody tr.selected td {
        background: #dbeafe !important;
        color: #1f2937 !important;
        font-weight: 600 !important;
    }
    .corp-form-container .sales-dtl-table tbody td.dataTables_empty {
        color: #64748b !important;
        background: #ffffff !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        padding: 16px !important;
        text-align: center;
    }
    .corp-form-container .sales-dtl-table input[type="checkbox"] {
        width: 17px !important;
        height: 17px !important;
        accent-color: #1e3a5f !important;
        cursor: pointer;
    }
    .corp-form-container .dataTables_wrapper .dataTables_filter label,
    .corp-form-container .dataTables_wrapper .dataTables_length label,
    .corp-form-container .dataTables_wrapper .dataTables_info {
        color: #475569 !important;
        font-size: 13px !important;
        font-weight: 600 !important;
    }
    .corp-form-container .dataTables_wrapper .dataTables_filter input,
    .corp-form-container .dataTables_wrapper .dataTables_length select {
        color: #1f2937 !important;
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
        min-height: 36px !important;
    }
    .corp-form-container .dataTables_wrapper .pagination .page-link {
        color: #1e3a5f !important;
        border-color: #cbd5e1 !important;
        font-size: 13px !important;
        font-weight: 600 !important;
    }
    .corp-form-container .dataTables_wrapper .pagination .page-item.active .page-link {
        background: #1e3a5f !important;
        border-color: #1e3a5f !important;
        color: #ffffff !important;
    }
    /* Highlight baris yang disorot via keyboard (panah atas/bawah + spasi) */
    .corp-form-container .sales-dtl-table tbody tr.row-active,
    .corp-form-container .sales-dtl-table tbody tr.row-active td,
    .corp-form-container .sales-dtl-table tbody tr.row-active:nth-child(even) td,
    .corp-form-container .sales-dtl-table tbody tr.row-active:hover td {
        background: #fff3cd !important;
        background-color: #fff3cd !important;
        color: #1f2937 !important;
    }
</style>
