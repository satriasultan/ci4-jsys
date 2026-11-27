<style type="text/css">
    /* Baris input detail inline pada tabel Detail Barang (pola grid PNM / PMK Barang) */
    #tabdeliveryorderdtl tfoot tr.so-inline-row td {
        padding: 4px 3px;
        vertical-align: middle;
        background-color: #fff8e1;
    }

    #tabdeliveryorderdtl tfoot tr.so-inline-row .form-control-sm {
        font-size: 11px;
        padding: 2px 4px;
        min-width: 62px;
        height: 28px !important;
        min-height: 28px !important;
    }

    /* Dropdown satuan (native select): font diperkecil agar teks satuan
       tetap terlihat utuh pada kolom Satuan yang sempit */
    #tabdeliveryorderdtl tfoot tr.so-inline-row select.so-inline-unit {
        min-width: 60px;
        width: 100% !important;
        font-size: 11px !important;
        padding: 1px 16px 1px 3px !important;
        line-height: 1.2;
        background-position: right 2px center !important;
        background-size: 10px 8px;
        text-overflow: ellipsis;
    }

    /* Kolom Satuan diberi lebar minimum agar dropdown satuan tidak
       menyempit sampai teksnya tidak terlihat */
    #tabdeliveryorderdtl thead th.so-col-unit,
    #tabdeliveryorderdtl tbody td.so-col-unit-cell {
        width: 72px !important;
        min-width: 72px !important;
    }

    #tabdeliveryorderdtl tfoot tr.so-inline-row .select2-container {
        min-width: 96px;
    }

    /* Input tanggal (ETD / ETA) pada baris inline: format dd-mm-yyyy */
    #tabdeliveryorderdtl tfoot tr.so-inline-row input.so-inline-date {
        min-width: 96px;
        text-align: center;
    }

    /* Gudang pada baris inline */
    #tabdeliveryorderdtl tfoot tr.so-inline-row select.so-inline-gudang {
        min-width: 130px;
    }

    /* Select2 dalam baris input dibuat ringkas (pola grid inline PNM / PMK) */
    #tabdeliveryorderdtl tfoot tr.so-inline-row .select2-container .select2-selection--single {
        height: 28px !important;
        min-height: 28px !important;
    }

    #tabdeliveryorderdtl tfoot tr.so-inline-row .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 5px !important;
        padding-right: 18px !important;
        line-height: 26px !important;
        font-size: 11px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #tabdeliveryorderdtl tfoot tr.so-inline-row .select2-container .select2-selection--single .select2-selection__arrow {
        top: 1px !important;
        height: 26px !important;
        right: 1px !important;
        width: 16px !important;
    }

    /* Empty state detail barang (pola grid inline PNM / PMK) */
    #tabdeliveryorderdtl tbody td.dataTables_empty {
        padding: 16px 10px !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        background: #ffffff !important;
        text-align: center;
        cursor: pointer;
    }

    #tabdeliveryorderdtl tbody td.dataTables_empty:hover {
        background: #eef6ff !important;
        color: #1e3a5f !important;
    }

    #tabdeliveryorderdtl tbody td.dataTables_empty i {
        margin-right: 6px;
        font-size: 15px;
        color: #1e3a5f;
    }

    /* Pesan empty state disembunyikan selama baris input inline terbuka */
    #tabdeliveryorderdtl.so-has-draft tbody td.dataTables_empty {
        display: none !important;
    }

    #tabdeliveryorderdtl tfoot tr.so-inline-row.table-info td {
        background-color: #d1ecf1;
    }

    /* Kolom Aksi: letak tombol disamakan dengan grid inline PNM / PMK Barang
       (hanya tombol update + hapus; tombol urutan pindah ke kotak pencarian) */
    #tabdeliveryorderdtl thead th.so-col-aksi,
    #tabdeliveryorderdtl tbody td.so-col-aksi-cell,
    #tabdeliveryorderdtl tfoot td.so-col-aksi {
        width: 76px !important;
        min-width: 76px !important;
        text-align: center !important;
        white-space: nowrap !important;
        padding-left: 4px !important;
        padding-right: 4px !important;
    }

    /* Tombol urutan â–² / â–¼ di kiri kotak pencarian DataTables */
    .dataTables_filter.so-dt-filter {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .so-dt-move {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        vertical-align: middle;
    }

    .so-dt-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0;
        font-size: 12px;
        line-height: 1;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background .12s ease, transform .12s ease;
    }

    .so-dt-btn:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(15, 23, 42, .22);
    }

    .so-dt-btn-up {
        background: #0284c7;
    }

    .so-dt-btn-down {
        background: #0f766e;
    }

    #tabdeliveryorderdtl tfoot td.so-col-no {
        width: 30px !important;
        min-width: 30px !important;
        text-align: center !important;
        padding-left: 2px !important;
        padding-right: 2px !important;
    }

    /* Tombol aksi kecil di dalam baris (draft inline & baris tersimpan) */
    #tabdeliveryorderdtl tfoot tr.so-inline-row .so-inline-btn,
    #tabdeliveryorderdtl tbody .so-row-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        margin: 0 1px;
        padding: 0;
        font-size: 12px;
        line-height: 1;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background .12s ease, transform .12s ease;
    }

    #tabdeliveryorderdtl tfoot tr.so-inline-row .so-inline-btn:hover,
    #tabdeliveryorderdtl tbody .so-row-btn:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(15, 23, 42, .22);
    }

    #tabdeliveryorderdtl tfoot tr.so-inline-row .so-inline-save {
        background: #16a34a;
    }

    #tabdeliveryorderdtl tfoot tr.so-inline-row .so-inline-cancel {
        background: #64748b;
    }

    #tabdeliveryorderdtl tbody .so-row-btn-edit {
        background: #d97706;
    }

    #tabdeliveryorderdtl tbody .so-row-btn-delete {
        background: #dc2626;
    }

    /* Hint format angka pada grid detail (desimal titik, maksimal 4 digit) */
    .so-num-hint {
        margin-top: 6px;
        font-size: 11.5px;
        line-height: 1.5;
        color: #64748b;
    }

    .so-num-hint i {
        margin-right: 4px;
        color: #1e3a5f;
    }

    .so-num-hint b {
        color: #1e3a5f;
    }

    .so-num-hint code {
        padding: 0 3px;
        font-size: 11px;
        color: #1e3a5f;
        background: #eef6ff;
        border-radius: 3px;
    }

    /* Hint shortcut keyboard di bawah grid detail (Alt+N untuk tambah item) */
    .so-key-hint {
        margin-top: 4px;
        padding: 5px 8px;
        font-size: 11.5px;
        line-height: 1.6;
        color: #475569;
        background: #f1f5f9;
        border-left: 3px solid #0284c7;
        border-radius: 4px;
    }

    .so-key-hint i {
        margin-right: 5px;
        color: #0284c7;
    }

    .so-key-hint kbd {
        display: inline-block;
        min-width: 16px;
        padding: 1px 5px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.4;
        color: #1e3a5f;
        text-align: center;
        white-space: nowrap;
        background: #ffffff;
        border: 1px solid #bfdbfe;
        border-bottom-width: 2px;
        border-radius: 4px;
    }
    /* --- Select2: teks (kode idbarang / idgudang) bisa diseleksi & dicopy Ctrl+C --- */
    .select2-container .select2-selection,
    .select2-container .select2-selection__rendered,
    .select2-container .select2-selection__choice,
    .select2-results__option,
    .select2-results__option .select2-result-repository__description,
    .select2-results__option .so-select2-copy,
    .select2-results__option span {
        -webkit-user-select: text !important;
        -moz-user-select: text !important;
        -ms-user-select: text !important;
        user-select: text !important;
    }

    /* satu klik pada field terpilih langsung menyeleksi seluruh kode */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        -webkit-user-select: all !important;
        user-select: all !important;
        cursor: text;
    }

    .select2-results__option .so-select2-copy {
        cursor: text;
    }

    /* ===== Stok akhir (sc_trx.stkblc s/d tanggal DO) + tanda seru ===== */
    #tabdeliveryorderdtl td.so-col-stok,
    #tabdeliveryorderdtl th.so-col-stok {
        white-space: nowrap;
    }

    #tabdeliveryorderdtl .do-stok-val {
        font-variant-numeric: tabular-nums;
    }

    #tabdeliveryorderdtl .do-stok-warn,
    .so-num-hint .do-stok-warn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 14px;
        height: 14px;
        margin-left: 3px;
        padding: 0 3px;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        color: #ffffff;
        background: #dc2626;
        border-radius: 7px;
        cursor: help;
        vertical-align: middle;
    }
    /* ===== Kebijakan harga: form gudang tanpa informasi harga =====
       Aktif bila container memakai class "do-no-price" (lihat
       v_add_deliveryorder_inline.php). Kolom tetap ada di DOM supaya
       perhitungan JS & jumlah kolom (colspan) tidak berubah. */
    .do-no-price .do-col-price,
    .do-no-price .do-price-summary {
        display: none !important;
    }
</style>
