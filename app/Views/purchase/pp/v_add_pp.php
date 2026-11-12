<!-- =========================================================
     v_add_pp.php - Corporate Style
     Referensi tampilan : Master Item (master/item/v_input.php)
     Catatan : perubahan hanya pada font, warna, dan spacing.
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

    /* Jangkar posisi dropdown Select2 No. Capex.
       Parent input dibuat position: relative supaya dropdown Select2
       (yang di-append ke parent ini) rata kiri tepat di bawah input,
       tidak bergeser mengikuti offset modal. */
    #modalDetailPP .capex-field {
        position: relative;
    }

    #modalDetailPP .capex-field .select2-container,
    #modalDetailPP .capex-field .select2-dropdown {
        min-width: 0 !important;
        max-width: 100% !important;
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
                        <h3 class="card-title"><i class="fa fa-address-card"></i> <?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Permintaan Pembelian</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Permintaan Pembelian
                            </div>
                            <div class="row">
                                <div class="row col-md-6">
                                    <!-- CABANG / JOB -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cabang">Cabang / Job <span style="color: red;">*</span></label>
                                            <select name="cabang" id="cabang" class="form-control" required
                                                    data-session-branch-id="<?= esc($sessionBranchId) ?>"
                                                    data-session-branch-name="<?= esc($sessionBranchName) ?>"
                                                    data-can-choose-branch="<?= $canChooseBranch ? '1' : '0' ?>"></select>
                                        </div>
                                    </div>

                                    <!-- PEMOHON (dipindah ke atas) -->
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


                                </div>
                                <div class="row col-md-6">
                                    <!-- TANGGAL -->
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

                                    <!-- ESTIMASI PAKAI -->
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

                                    <!-- KETERANGAN (lebih pendek & full width) -->
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <label for="keterangan">Keterangan PP <span style="color: red;">*</span></label>
                                            <textarea name="keterangan"
                                                      id="keterangan"
                                                      class="form-control"
                                                      rows="3"
                                                      placeholder="Catatan / Informasi..."
                                                      style="text-transform:uppercase;"></textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="20" value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>" style="text-transform: uppercase;" readonly>
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
                                    onclick="btnInputDetail()">
                                <i class="fa fa-plus"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-warning btn-lg action-btn btn-update-detail"
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
                        Input Item Detail
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
                            <div class="col-md-3">
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


                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="form-group capex-field">
                                    <label>No. Capex</label>
                                    <select name="capexno" id="capexno"
                                            class="form-control select2"
                                            style="width:100%"></select>
                                </div>
                            </div>
                        </div>

                        <!-- ROW 3 -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label>Keterangan <span style="color: red;">*</span></label>
                                    <textarea type="text"
                                              name="description"
                                              rows="3"
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


</div>
<!-- /.corp-form-container -->

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