/*
 * =========================================================================
 * pmk_brng_add.js  —  KHUSUS HALAMAN INPUT PEMAKAIAN BARANG (I.Q.A.6)
 * URL : persediaan/trans/add_pmk_brng_mst
 * -------------------------------------------------------------------------
 * Modul ini menggantikan modal #modalDetailpmk dengan grid inline editing.
 * Hanya di-load oleh app/Views/persediaan/pmk_brng/v_add_pmk_brg.php.
 * Tidak mengubah file/JS modul lain (Purchase/PO, Master Barang, Master Gudang).
 *
 * Fitur:
 *  - Grid input langsung (custom jQuery table, tanpa library baru).
 *  - 1 baris kosong selalu tersedia untuk input cepat.
 *  - Checkbox per baris + Select All; tombol Update/Hapus memakai baris terpilih.
 *  - TAB / ENTER memindah kolom; TAB/ENTER di kolom terakhir menyimpan baris
 *    dan otomatis menambah baris baru.
 *  - Gudang (select2), ID Barang (select2 + pencarian AJAX), Nama/Satuan/Harga
 *    terisi otomatis dari endpoint modul ini.
 * ========================================================================= */

var pmk_ADD_FIELDS = ['idlocation', 'idbarang', 'batch', 'unit', 'qty', 'description'];

var pmkAddState = {
    rows: [],          // baris tersimpan (dari server)
    locations: [],     // master gudang
    locationsAll: [],
    saving: false,
    loaded: false,
    finalizing: false, // guard agar Simpan Final tidak dobel proses
    isSaving: false,   // guard tambahan: true selama proses Simpan Final berjalan
    submitArmed: false // true = submit form diizinkan (hanya sekali)
};

/* Konfirmasi sebelum Simpan Final.
   false = langsung simpan (paling cepat, sesuai permintaan). */
var pmk_ADD_CONFIRM_FINAL = false;

/* Menyimpan tombol terakhir yang ditekan: dipakai agar dropdown Select2
   hanya auto-open saat fokus masuk lewat TAB (bukan lewat klik mouse,
   supaya tidak bentrok dengan toggle bawaan Select2). */
var pmkAddLastKey = '';

/* Debug: set true sementara untuk melihat log handler cancel di console. */
var pmk_ADD_DEBUG = false;

function pmkAddLog() {
    if (!pmk_ADD_DEBUG || !window.console || !console.log) {
        return;
    }

    console.log.apply(console, ['[pmk-GRID]'].concat(Array.prototype.slice.call(arguments)));
}

/* ------------------------------------------------------------------ */
/* HELPER                                                              */
/* ------------------------------------------------------------------ */
function pmkAddExists() {
    return $('#pmkGrid').length > 0;
}

function pmkAddEsc(text) {
    return $('<div>').text(text === null || text === undefined ? '' : String(text)).html();
}

function pmkAddNum(value) {
    if (typeof convertToDbNumber === 'function') {
        return Number(convertToDbNumber(value)) || 0;
    }

    return Number(String(value === null || value === undefined ? '' : value).replace(/,/g, '')) || 0;
}

function pmkAddDec(value, dec) {
    var n = pmkAddNum(value);

    return n.toLocaleString('en-US', {
        minimumFractionDigits: dec,
        maximumFractionDigits: dec
    });
}

function pmkAddFormatText(value, dec) {
    if (typeof jtsFormatNumber === 'function') {
        return jtsFormatNumber(value, dec);
    }

    var s = String(value === null || value === undefined ? '' : value).replace(/[^0-9.]/g, '');

    if (s === '') {
        return '';
    }

    var parts = s.split('.');
    var intPart = parts.shift() || '';
    var decPart = parts.join('').substring(0, dec);

    intPart = intPart.replace(/^0+(?=\d)/, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return decPart === '' ? intPart : (intPart || '0') + '.' + decPart;
}

function pmkAddToast(message, icon) {
    Swal.fire({
        toast: true,
        position: 'bottom-end',
        icon: icon || 'success',
        title: message,
        showConfirmButton: false,
        timer: 1300,
        timerProgressBar: true
    });
}

/** Nomor jurnal dari #docno atau prefix/infix/suffix */
function pmkAddDocno() {
    var docno = $.trim($('#docno').val() || '');

    if (docno !== '') {
        return docno;
    }

    var prefix = $.trim($('#prefix').val() || '').toUpperCase();
    var infix = $.trim($('#infix').val() || '');
    var suffix = $.trim($('#suffix').val() || '').toUpperCase();

    return (prefix && infix && suffix) ? (prefix + '/' + infix + '/' + suffix) : '';
}

function pmkAddLocName(idlocation) {
    var id = $.trim(idlocation || '');
    var name = '';
    var source = (pmkAddState.locationsAll && pmkAddState.locationsAll.length)
        ? pmkAddState.locationsAll
        : pmkAddState.locations;

    $.each(source || [], function (i, loc) {
        if ($.trim(loc.idlocation || '') === id) {
            name = $.trim(loc.nmlocation || id);
            return false;
        }
    });

    return name || id;
}

function pmkAddLocData(selected) {
    var data = [{ id: '', text: '-- Pilih Gudang --' }];
    var sel = $.trim(selected || '');

    $.each(pmkAddState.locations || [], function (i, loc) {
        var id = $.trim(loc.idlocation || '');
        var name = $.trim(loc.nmlocation || id);

        if (id === '') {
            return;
        }

        data.push({ id: id, text: id + ' - ' + name, selected: (id === sel) });
    });

    return data;
}

/* ------------------------------------------------------------------ */
/* MASTER GUDANG (endpoint read-only milik modul ini)                  */
/* ------------------------------------------------------------------ */
function pmkAddLoadLocations(callback) {
    if (pmkAddState.locations.length > 0) {
        if (callback) callback();
        return;
    }

    $.ajax({
        url: HOST_URL + 'persediaan/trans/list_location_pmk_brng',
        type: 'POST',
        dataType: 'json',
        data: { branch: $.trim($('#cabang').val() || '') },
        success: function (res) {
            var items = (res && res.items) ? res.items : [];

            pmkAddState.locationsAll = items.slice();
            pmkAddState.locations = items;

            if (callback) callback();
        },
        error: function () {
            pmkAddState.locationsAll = [];
            pmkAddState.locations = [];

            if (callback) callback();

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Daftar gudang tidak dapat dimuat.'
            });
        }
    });
}

function pmkAddRefreshLocationSelects() {
    $('#pmkGridBody tr.pmk-row-draft').each(function () {
        var $row = $(this);
        var $loc = $row.find('select.pmk-loc');

        if (!$loc.length) {
            return;
        }

        pmkAddInitLocationSelect($loc, $loc.val());
    });
}

/* ------------------------------------------------------------------ */
/* LOAD + RENDER BARIS TERSIMPAN                                       */
/* ------------------------------------------------------------------ */
/**
 * Versi promise dari pemuatan baris detail.
 * @param {boolean} focusDraft fokuskan baris input setelah render
 * @param {boolean} withDraft  buat baris input baru setelah render (default: false)
 */
function pmkAddLoadRowsAsync(focusDraft, withDraft) {
    var dfd = $.Deferred();

    if (!pmkAddExists()) {
        return dfd.resolve().promise();
    }

    $.ajax({
        url: HOST_URL + 'persediaan/trans/list_pmk_brng_dtl_grid',
        type: 'POST',
        dataType: 'json',
        success: function (res) {
            pmkAddState.rows = (res && res.items) ? res.items : [];
            pmkAddState.loaded = true;

            pmkAddRender({ withDraft: !!withDraft });

            if (focusDraft) {
                pmkAddFocusDraft();
            }

            dfd.resolve();
        },
        error: function () {
            dfd.reject();
        }
    });

    return dfd.promise();
}

function pmkAddLoadRows(focusDraft, withDraft) {
    if (withDraft === undefined) {
        withDraft = !!focusDraft;
    }

    pmkAddLoadRowsAsync(focusDraft, withDraft).fail(function () {
        Swal.fire({
            icon: 'error',
            title: 'Server Error',
            text: 'Data detail tidak dapat dimuat.'
        });
    });
}

/**
 * Tampilkan empty state bila tabel benar-benar tidak punya baris.
 * Baris input TIDAK dibuat otomatis di sini.
 */
function pmkAddRenderEmptyState() {
    var $body = $('#pmkGridBody');

    $body.find('tr.pmk-empty-row').remove();

    if ($body.find('tr').length > 0) {
        return;
    }

    $body.append(
        '<tr class="pmk-empty-row">' +
        '  <td colspan="12" tabindex="0">' +
        '      <i class="fa fa-inbox"></i> Belum ada data detail. ' +
        '      Klik tombol <b>+</b> (atau tekan <b>Alt+N</b>) untuk menambah barang.' +
        '  </td>' +
        '</tr>'
    );
}

/**
 * Render ulang isi grid dari pmkAddState.rows.
 * @param {object} options { withDraft: bool } -> default TANPA baris input baru
 */
function pmkAddRender(options) {
    options = options || {};

    pmkAddRemoveEditors();

    var $body = $('#pmkGridBody');
    $body.empty();

    $.each(pmkAddState.rows, function (i, row) {
        $body.append(pmkAddSavedRow(row));
    });

    if (options.withDraft) {
        pmkAddNewDraft(false);
    }

    pmkAddRenderEmptyState();
    pmkAddRecalcTotal();
    pmkAddSelectionState();
}

function pmkAddSavedRow(row) {
    var idurut = pmkAddEsc(row.idurut);
    var locName = pmkAddLocName(row.idlocation);
    var desc = $.trim(row.description || '');

    var stokTampil = pmkAddNum(row.stok !== undefined && row.stok !== null ? row.stok : 0);
    var stokFisik = pmkAddNum(row.stok_fisik !== undefined && row.stok_fisik !== null
        ? row.stok_fisik
        : stokTampil);
    var stokMax = pmkAddNum(row.stok_max !== undefined && row.stok_max !== null
        ? row.stok_max
        : stokTampil);
    var qtySaved = pmkAddNum(row.qty);

    var booked = stokFisik - stokTampil;

    if (booked < 0) {
        booked = 0;
    }

    var stokTitle = 'Stok fisik gudang: ' + pmkAddDec(stokFisik, 2)
        + ' | Draft: ' + pmkAddDec(booked, 2)
        + ' | Maks baris ini: ' + pmkAddDec(stokMax, 2);

    return ''
        + '<tr class="pmk-row-saved" data-idurut="' + idurut + '"'
        + '    data-idlocation="' + pmkAddEsc($.trim(row.idlocation || '')) + '"'
        + '    data-idbarang="' + pmkAddEsc($.trim(row.idbarang || '')) + '"'
        + '    data-batch="' + pmkAddEsc($.trim(row.batch || '').toUpperCase()) + '"'
        + '    data-stok="' + stokTampil + '"'
        + '    data-stok-fisik="' + stokFisik + '"'
        + '    data-stok-max="' + stokMax + '"'
        + '    data-qty-saved="' + qtySaved + '">'
        + '  <td class="pmk-col-no"><input type="checkbox" class="row-check" value="' + idurut + '"></td>'
        + '  <td title="' + pmkAddEsc(row.idlocation + ' - ' + locName) + '">'
        + '      <span class="pmk-readonly-text">' + pmkAddEsc(locName) + '</span></td>'
        + '  <td>' + pmkAddEsc(row.idbarang) + '</td>'
        + '  <td title="' + pmkAddEsc(row.nmbarang) + '">'
        + '      <span class="pmk-readonly-text">' + pmkAddEsc(row.nmbarang) + '</span></td>'
        + '  <td>' + pmkAddEsc(row.batch) + '</td>'
        + '  <td class="text-center">' + pmkAddEsc(row.unit) + '</td>'
        + '  <td class="text-end"><span class="pmk-readonly-number">' + pmkAddDec(row.qty, 2) + '</span></td>'
        + '  <td class="text-end"><span class="pmk-readonly-number">' + pmkAddDec(row.val, 4) + '</span></td>'
        + '  <td class="text-end"><span class="pmk-readonly-number">' + pmkAddDec(row.valsum, 2) + '</span></td>'
        + '  <td class="text-end" title="' + pmkAddEsc(stokTitle) + '">'
        + '      <span class="pmk-readonly-number pmk-stock-text">'
        +      pmkAddDec(stokTampil, 2) + '</span></td>'
        + '  <td title="' + pmkAddEsc(desc) + '">'
        + '      <span class="pmk-readonly-text">' + pmkAddEsc(desc) + '</span></td>'
        + '  <td class="pmk-col-aksi">'
        + '      <button type="button" class="pmk-btn pmk-btn-edit" title="Edit baris">'
        + '          <i class="fa fa-pencil"></i></button>'
        + '      <button type="button" class="pmk-btn pmk-btn-delete" title="Hapus baris">'
        + '          <i class="fa fa-trash"></i></button>'
        + '  </td>'
        + '</tr>';
}


/* ------------------------------------------------------------------ */
/* BARIS EDITOR (draft baru / edit baris tersimpan)                    */
/* ------------------------------------------------------------------ */
function pmkAddEditorRow(data) {
    data = data || {};

    var isEdit = $.trim(String(data.idurut || '')) !== '';

    var $tr = $('<tr class="pmk-row-draft"></tr>')
        .attr('data-idurut', data.idurut || '')
        .attr('data-iduniq', data.iduniq || '')
        .attr('data-mode', isEdit ? 'edit' : 'new')
        .attr('data-qty-saved', isEdit ? pmkAddNum(data.qty) : '')
        .attr('data-stok-fisik', (data.stok_fisik !== undefined && data.stok_fisik !== null)
            ? pmkAddNum(data.stok_fisik)
            : '')
        .attr('data-stok-max', (data.stok_max !== undefined && data.stok_max !== null)
            ? pmkAddNum(data.stok_max)
            : '')
        .toggleClass('pmk-row-edit', isEdit);

    /* 1. checkbox (baris editor belum bisa dicentang) */
    $tr.append($('<td class="pmk-col-no"></td>').html(isEdit
        ? '<i class="fa fa-pencil-square-o" style="color:#d97706"></i>'
        : '<i class="fa fa-plus-circle" style="color:#16a34a"></i>'));

    /* 2. gudang (select2) */
    $tr.append($('<td class="pmk-cell"></td>').append(
        $('<select class="pmk-loc" data-field="idlocation" style="width:100%"></select>')
    ));

    /* 3. id barang (select2 + pencarian) */
    $tr.append($('<td class="pmk-cell"></td>').append(
        $('<select class="pmk-item" data-field="idbarang" style="width:100%"></select>')
    ));

    /* 4. nama barang (auto-fill, readonly) */
    $tr.append($('<td class="pmk-cell"></td>').append(
        $('<input type="text" class="pmk-cell-input" data-field="nmbarang" readonly tabindex="-1" placeholder="Otomatis">')
            .val(data.nmbarang || '')
    ));

    /* 5. batch / spec (Select2, tergantung ID Barang) */
    $tr.append($('<td class="pmk-cell pmk-cell-batch"></td>').append(
        $('<select class="pmk-batch" data-field="batch" style="width:100%"></select>')
    ));

    /* 6. satuan (dropdown native, diisi dari master satuan barang) */
    $tr.append($('<td class="pmk-cell"></td>').append(
        $('<select class="pmk-cell-select pmk-input" data-field="unit"></select>')
            .append(new Option('-- Pilih --', ''))
    ));

    /* 7. qty */
    $tr.append($('<td class="pmk-cell"></td>').append(
        $('<input type="text" class="pmk-cell-input text-end pmk-num pmk-input" data-field="qty" data-dec="2" placeholder="0.00">')
            .val(data.qty !== undefined && data.qty !== null ? pmkAddDec(data.qty, 2) : '')
    ));

    /* 8. harga (auto-fill dari average cost, readonly) */
    $tr.append($('<td class="pmk-cell"></td>').append(
        $('<input type="text" class="pmk-cell-input text-end" data-field="val" data-dec="4" readonly tabindex="-1" placeholder="0.0000">')
            .val(data.val !== undefined && data.val !== null ? pmkAddDec(data.val, 4) : '')
    ));

    /* 9. nilai = qty x harga (readonly) */
    $tr.append($('<td class="pmk-cell"></td>').append(
        $('<input type="text" class="pmk-cell-input text-end" data-field="valsum" readonly tabindex="-1" placeholder="0.00">')
            .val(data.valsum !== undefined && data.valsum !== null ? pmkAddDec(data.valsum, 2) : '')
    ));

    /* 10. stok tersedia (readonly, info sisa stok gudang terpilih) */
    $tr.append($('<td class="pmk-cell"></td>').append(
        $('<input type="text" class="pmk-cell-input text-end" data-field="stok" readonly tabindex="-1" placeholder="0.00">')
            .val(data.stok !== undefined && data.stok !== null ? pmkAddDec(data.stok, 2) : '')
    ));

    /* 11. keterangan */
    $tr.append($('<td class="pmk-cell"></td>').append(
        $('<input type="text" class="pmk-cell-input pmk-input" data-field="description" maxlength="200" placeholder="Keterangan" style="text-transform:uppercase">')
            .val(data.description || '')
    ));

    /* 11. aksi: simpan + batal (X) */
    var $act = $('<td class="pmk-col-aksi"></td>');

    $act.append('<button type="button" class="pmk-btn pmk-btn-save" data-act="save" title="Simpan baris (Enter)">' +
        '<i class="fa fa-check"></i></button>');

    $act.append('<button type="button" class="pmk-btn pmk-btn-cancel" data-act="cancel" title="Batal / tutup baris (X / Esc)">' +
        '<i class="fa fa-times"></i></button>');

    $tr.append($act);

    return $tr;
}

function pmkAddNewDraft(focus) {
    if (!pmkAddExists()) {
        return null;
    }

    /* hapus empty state bila ada */
    $('#pmkGridBody tr.pmk-empty-row').remove();

    var $draft = $('#pmkGridBody tr.pmk-row-draft[data-mode="new"]');

    if ($draft.length) {
        if (focus) {
            pmkAddFocus($draft.last(), 'idlocation');
        }
        return $draft.last();
    }

    var $tr = pmkAddEditorRow({});
    $('#pmkGridBody').append($tr);

    pmkAddSetupSelects($tr, null);
    pmkAddBindRow($tr);
    pmkAddRecalcTotal();
    pmkAddSelectionState();

    if (focus) {
        pmkAddFocus($tr, 'idlocation');
    }

    return $tr;
}

function pmkAddFocusDraft() {
    var $draft = $('#pmkGridBody tr.pmk-row-draft[data-mode="new"]').last();

    if ($draft.length) {
        pmkAddFocus($draft, 'idlocation');
    }
}

function pmkAddRemoveEditors() {
    $('#pmkGridBody tr.pmk-row-draft').each(function () {
        var $row = $(this);

        $row.find('select').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        $row.remove();
    });
}


/* ------------------------------------------------------------------ */
/* SETUP DROPDOWN PER BARIS (gudang select2, barang select2, satuan)    */
/* ------------------------------------------------------------------ */
function pmkAddDropdownParent() {
    var $parent = $('.corp-form-container').first();

    return $parent.length ? $parent : $(document.body);
}

/* ------------------------------------------------------------------ */
/* KEYBOARD SUPPORT SELECT2 (TAB => buka pencarian)                    */
/* ------------------------------------------------------------------ */
/** Ambil search field milik dropdown Select2 yang sedang terbuka */
function pmkAddSelect2SearchField($select) {
    var $search = $();
    var inst = $select.data('select2');

    if (inst && inst.$dropdown) {
        $search = inst.$dropdown.find('.select2-search__field');
    }

    if (!$search.length) {
        $search = $('.select2-container--open .select2-search__field').last();
    }

    return $search;
}

/**
 * Fokuskan kolom pencarian Select2 yang sedang terbuka.
 * Hanya bekerja untuk Select2 yang berada di dalam #pmkGrid,
 * sehingga tidak mengganggu dropdown modul lain.
 * Dipanggil berulang (0 ms & 80 ms) karena Select2 kadang
 * memindahkan fokus setelah animasi/penutupan dropdown lain.
 */
function pmkAddFocusOpenSearch() {
    var $open = $('#pmkGrid .select2-container--open');

    if (!$open.length) {
        return false;   // dropdown grid tidak sedang terbuka
    }

    var $select = $open.first().prev('select.select2-hidden-accessible');
    var $search = $select.length ? pmkAddSelect2SearchField($select) : $();

    if (!$search.length) {
        $search = $('.select2-container--open .select2-search__field').last();
    }

    if ($search.length) {
        try {
            $search.trigger('focus');
        } catch (e) {
            /* abaikan */
        }

        if (document.activeElement !== $search[0] && typeof $search[0].focus === 'function') {
            try {
                $search[0].focus();
            } catch (e2) {
                /* abaikan */
            }
        }
    }

    return $search.length > 0;
}

/** Buka dropdown Select2 (langsung fokus ke kolom pencarian) */
function pmkAddOpenSelect2($select) {
    if (!$select || !$select.length) {
        return;
    }

    /* jangan menyentuh elemen yang sudah dilepas dari DOM */
    if (!$.contains(document, $select[0])) {
        return;
    }

    if (!$select.hasClass('select2-hidden-accessible')) {
        return;
    }

    var inst = $select.data('select2');

    if (!(inst && typeof inst.isOpen === 'function' && inst.isOpen())) {
        $select.select2('open');
    }

    /* pastikan search field benar-benar fokus */
    pmkAddFocusOpenSearch();
    window.setTimeout(pmkAddFocusOpenSearch, 0);
    window.setTimeout(pmkAddFocusOpenSearch, 80);
}

/**
 * Fokus masuk ke Select2 lewat TAB => buka dropdown (search siap diketik).
 * Tidak dijalankan untuk klik mouse agar tidak bentrok dengan toggle Select2.
 */
function pmkAddBindSelect2AutoOpen($tr) {
    $tr.off('focusin.pmkopen', '.select2-selection')
        .on('focusin.pmkopen', '.select2-selection', function () {
            if (pmkAddLastKey !== 'Tab') {
                return;
            }

            var $select = $(this).closest('td').find('select.select2-hidden-accessible').first();

            if ($select.length) {
                pmkAddOpenSelect2($select);
            }
        });
}

/** (re)inisialisasi select2 gudang pada satu baris */
function pmkAddInitLocationSelect($loc, selected) {
    if ($loc.hasClass('select2-hidden-accessible')) {
        $loc.select2('destroy');
    }

    $loc.empty();
    $loc.select2({
        placeholder: 'Pilih Gudang',
        allowClear: true,
        width: '100%',
        dropdownParent: pmkAddDropdownParent(),
        data: pmkAddLocData(selected)
    });

    if ($.trim(selected || '') !== '') {
        $loc.val($.trim(selected)).trigger('change.select2');
    }
}

/**
 * Inisialisasi ulang Select2 kolom Batch / Spec untuk SATU baris.
 *  - Selalu destroy dulu agar tidak terjadi duplikasi saat baris di-render ulang.
 *  - disabled + placeholder "Pilih Barang Terlebih Dahulu" bila ID Barang kosong.
 *  - Data batch diambil via AJAX berdasarkan ID Barang pada baris yang sama.
 *
 * @param {jQuery} $tr    baris tabel
 * @param {object} opts   { enabled: bool, selected: string }
 */
function pmkAddInitBatchSelect($tr, opts) {
    opts = opts || {};

    var $batch = $tr.find('select.pmk-batch');

    if (!$batch.length) {
        return;
    }

    /* hindari duplikasi inisialisasi */
    if ($batch.hasClass('select2-hidden-accessible')) {
        try {
            $batch.select2('destroy');
        } catch (err) {
            /* abaikan */
        }
    }

    $batch.empty();

    var itemId = $.trim($tr.find('select[data-field="idbarang"]').val() || '');
    var enabled = !!opts.enabled && itemId !== '';
    var selected = $.trim(opts.selected || '');

    /* nilai batch tersimpan (mode edit) tetap ditampilkan walau tidak ada di master */
    if (enabled && selected !== '') {
        $batch.append(new Option(selected, selected, true, true));
    }

    var config = {
        placeholder: enabled ? 'Pilih Batch / Spec' : 'Pilih Barang Terlebih Dahulu',
        allowClear: true,
        width: '100%',
        dropdownParent: pmkAddDropdownParent(),
        disabled: !enabled,
        minimumInputLength: 0,
        language: {
            inputTooShort: function () {
                return 'Ketik batch / spec...';
            },
            searching: function () {
                return 'Loading...';
            },
            loadingMore: function () {
                return 'Loading...';
            },
            noResults: function () {
                return 'Batch / Spec tidak ditemukan untuk barang ini';
            }
        }
    };

    if (enabled) {
        config.ajax = {
            url: HOST_URL + 'persediaan/trans/list_batch_pmk_brng',
            type: 'POST',
            dataType: 'json',
            delay: 200,
            data: function (params) {
                return {
                    idbarang: $.trim($tr.find('select[data-field="idbarang"]').val() || ''),
                    idlocation: $.trim($tr.find('select[data-field="idlocation"]').val() || ''),
                    term: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function (res, params) {
                params.page = params.page || 1;

                var items = (res && res.items) ? res.items : [];

                return {
                    results: $.map(items, function (it) {
                        var batch = $.trim(it.batch || it.text || it.id || '');

                        return {
                            id: batch,
                            text: batch,
                            batch: batch
                        };
                    }),
                    pagination: { more: !!(res && res.more) }
                };
            },
            cache: false
        };
    }

    $batch.select2(config);

    /* nilai awal (mode edit) */
    if (enabled && selected !== '') {
        $batch.val(selected).trigger('change.select2');
    }

    /* ENTER / pilih batch -> muat stok lalu lanjut ke kolom Satuan */
    $batch.off('.pmkbatch').on('select2:select.pmkbatch select2:clear.pmkbatch', function () {
        pmkAddLoadStock($tr, function () {
            pmkAddFocus($tr, 'unit');
        });
    });
}

function pmkAddSetupSelects($tr, data) {
    var $loc = $tr.find('select.pmk-loc');
    var $item = $tr.find('select.pmk-item');
    var $unit = $tr.find('select.pmk-unit, select[data-field="unit"]');

    /* ---------------- GUDANG (select2) ---------------- */
    pmkAddInitLocationSelect($loc, data && data.idlocation ? $.trim(data.idlocation) : '');

    /* TAB masuk -> dropdown + search langsung aktif */
    pmkAddBindSelect2AutoOpen($tr);

    $loc.off('.pmkloc').on('select2:select.pmkloc', function () {
        if ($item.val()) {
            /* item mengikuti gudang: reset bila gudang diganti */
            $item.val(null).trigger('change');

            $tr.find('[data-field="nmbarang"]').val('');
            pmkAddFillUnits($tr, [], '');
            pmkAddInitBatchSelect($tr, { enabled: false });   // batch ikut reset
        }

        /* gudang berubah -> informasi stok tidak valid lagi */
        pmkAddResetStock($tr);

        pmkAddFocus($tr, 'idbarang');
    });

    /* ---------------- ID BARANG (select2 + ajax) ---------------- */
    $item.select2({
        placeholder: 'Cari ID / Nama Barang',
        allowClear: true,
        width: '100%',
        dropdownParent: pmkAddDropdownParent(),
        minimumInputLength: 0,
        ajax: {
            url: HOST_URL + 'persediaan/trans/search_item_pmk_brng',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    idlocation: $.trim($loc.val() || '')
                };
            },
            processResults: function (res, params) {
                params.page = params.page || 1;

                var items = (res && res.items) ? res.items : [];

                return {
                    results: $.map(items, function (it) {
                        return {
                            id: $.trim(it.idbarang || ''),
                            text: $.trim(it.nmbarang || '') || $.trim(it.idbarang || ''),
                            idbarang: $.trim(it.idbarang || ''),
                            nmbarang: $.trim(it.nmbarang || '')
                        };
                    }),
                    pagination: { more: !!(res && res.more) }
                };
            },
            cache: false
        },
        templateResult: function (repo) {
            if (repo.loading) {
                return repo.text;
            }

            return '<div><b>' + pmkAddEsc(repo.idbarang || repo.id) + '</b>' +
                ' <i class="fa fa-circle-o"></i> ' +
                pmkAddEsc(repo.nmbarang || repo.text) + '</div>';
        },
        templateSelection: function (repo) {
            return repo.nmbarang || repo.text || repo.id || '';
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    if (data && data.idbarang) {
        $item.append(new Option(data.nmbarang || data.idbarang, data.idbarang, true, true))
            .trigger('change.select2');
    }

    $item.off('.pmkitem').on('select2:select.pmkitem', function (e) {
        var item = e.params.data || {};

        $tr.find('[data-field="nmbarang"]').val(item.nmbarang || '');

        /* ID Barang berubah -> Batch/Spec direset & diaktifkan kembali */
        pmkAddInitBatchSelect($tr, { enabled: true });

        pmkAddLoadItemInfo($tr, item.idbarang);

        /* muat stok tersedia (belum ada batch -> total stok gudang) */
        pmkAddLoadStock($tr);

        pmkAddFocus($tr, 'batch');
    });

    $item.on('select2:clear.pmkitem', function () {
        $tr.find('[data-field="nmbarang"]').val('');
        pmkAddFillUnits($tr, [], '');
        pmkAddInitBatchSelect($tr, { enabled: false });   // tanpa barang -> batch disabled
        pmkAddResetStock($tr);
    });

    /* ---------------- BATCH / SPEC (select2, tergantung ID Barang) ---------------- */
    pmkAddInitBatchSelect($tr, {
        enabled: !!(data && data.idbarang),
        selected: (data && data.batch) ? $.trim(data.batch) : ''
    });

    /* ---------------- SATUAN (dropdown native) ---------------- */
    if (data && data.idbarang) {
        pmkAddLoadItemInfo($tr, data.idbarang, true);
    }

    $unit.off('.pmkunit').on('change.pmkunit', function () {
        pmkAddFocus($tr, 'qty');
    });
}


/* ------------------------------------------------------------------ */
/* AUTO-FILL: NAMA BARANG, SATUAN, HARGA (endpoint modul ini)          */
/* ------------------------------------------------------------------ */
function pmkAddFillUnits($tr, units, selected) {
    var $unit = $tr.find('select[data-field="unit"]');
    var def = $.trim(selected || '');
    var found = false;

    $unit.empty().append(new Option('-- Pilih --', ''));

    $.each(units || [], function (i, it) {
        var unit = $.trim(it.idunit || it.unit || '');

        if (unit === '') {
            return;
        }

        $unit.append(new Option(unit, unit));

        if (unit === def) {
            found = true;
        }
    });

    if (def !== '' && !found) {
        $unit.append(new Option(def, def));
    }

    if (def === '' && (units || []).length > 0) {
        def = $.trim(units[0].idunit || units[0].unit || '');
    }

    $unit.val(def || '');
}

function pmkAddLoadItemInfo($tr, idbarang, preservePrice) {
    idbarang = $.trim(idbarang || '');

    if (idbarang === '') {
        return;
    }

    $.ajax({
        url: HOST_URL + 'persediaan/trans/get_item_pmk_brng',
        type: 'POST',
        dataType: 'json',
        data: { idbarang: idbarang },
        success: function (res) {
            if (!res || !res.success || !res.data) {
                return;
            }

            var item = res.data;

            $tr.find('[data-field="nmbarang"]').val(item.nmbarang || '');
            pmkAddFillUnits($tr, res.units || [], item.unit || '');

            if (!preservePrice) {
                var $val = $tr.find('[data-field="val"]');

                if ($.trim($val.val() || '') === '' || pmkAddNum($val.val()) === 0) {
                    $val.val(pmkAddDec(item.lastprice || 0, 4));
                }
            }

            pmkAddRecalcRow($tr);
            pmkAddRecalcTotal();
        }
    });
}

/* ------------------------------------------------------------------ */
/* STOK TERSEDIA (endpoint read-only milik modul PMK)                  */
/* Sumber stok: sc_trx.stkblc (SUM qty_in - qty_out per barang/gudang/  */
/* batch). Harga rata-rata: sc_trx.stkblc_avgcost.                     */
/* ------------------------------------------------------------------ */
function pmkAddStockOf($tr) {
    var attr = $.trim($tr.attr('data-stok') || '');

    if (attr !== '') {
        return pmkAddNum(attr);
    }

    var $inp = $tr.find('[data-field="stok"]');

    if ($inp.length) {
        return pmkAddNum($inp.val());
    }

    return pmkAddNum($tr.find('.pmk-stock-text').text());
}

/** Stok fisik gudang (TANPA dikurangi draft). */
function pmkAddStockFisikOf($tr) {
    var attr = $.trim($tr.attr('data-stok-fisik') || '');

    if (attr !== '') {
        return pmkAddNum(attr);
    }

    return pmkAddStockOf($tr);
}

/** Qty yang sudah tersimpan di server untuk baris ini (0 = baris baru). */
function pmkAddSavedQtyOf($tr) {
    if ($.trim($tr.attr('data-idurut') || '') === '') {
        return 0;
    }

    return pmkAddNum($tr.attr('data-qty-saved'));
}

/** Batas Qty untuk baris ini (= stok fisik - draft baris lain). */
function pmkAddStockMaxOf($tr) {
    var attr = $.trim($tr.attr('data-stok-max') || '');

    if (attr !== '') {
        return pmkAddNum(attr);
    }

    return pmkAddStockOf($tr);
}

/**
 * Tulis nilai stok tersedia ke baris (input untuk baris editor,
 * span readonly untuk baris tersimpan).
 *
 * @param {jQuery} $tr
 * @param {number} fisik  stok fisik gudang (sc_trx.stkblc)
 * @param {number} booked total qty draft user untuk grup ini (sc_tmp.pmk_brng_dtl)
 */
function pmkAddApplyStock($tr, fisik, booked) {
    if (!$tr || !$tr.length) {
        return;
    }

    fisik  = pmkAddNum(fisik);
    booked = pmkAddNum(booked);

    var own  = pmkAddSavedQtyOf($tr);
    var lain = booked - own;

    if (lain < 0) {
        lain = 0;
    }

    /* stok tampil  = fisik - seluruh draft grup (rumus buffering) */
    var tampil = fisik - booked;

    if (tampil < 0) {
        tampil = 0;
    }

    /* batas Qty baris ini = fisik - draft baris LAIN */
    var maks = fisik - lain;

    if (maks < 0) {
        maks = 0;
    }

    $tr.attr('data-stok-fisik', fisik);
    $tr.attr('data-stok', tampil);
    $tr.attr('data-stok-max', maks);

    var title = 'Stok fisik gudang: ' + pmkAddDec(fisik, 2)
        + ' | Draft: ' + pmkAddDec(booked, 2)
        + ' | Maks baris ini: ' + pmkAddDec(maks, 2);

    var $inp = $tr.find('[data-field="stok"]');

    if ($inp.length) {
        $inp.val(pmkAddDec(tampil, 2)).attr('title', title);
    }

    var $span = $tr.find('.pmk-stock-text');

    if ($span.length) {
        $span.text(pmkAddDec(tampil, 2));
        $span.closest('td').attr('title', title);
    }

    pmkAddRecalcRow($tr);
    pmkAddRecalcTotal();
}

function pmkAddSetStock($tr, fisik, avgcost, preservePrice, booked) {
    fisik   = pmkAddNum(fisik);
    avgcost = pmkAddNum(avgcost);

    if (booked === undefined || booked === null) {
        booked = 0;
    }

    if (!preservePrice && avgcost > 0) {
        $tr.find('[data-field="val"]').val(pmkAddDec(avgcost, 4));
    }

    pmkAddApplyStock($tr, fisik, booked);
}

function pmkAddResetStock($tr) {
    if (!$tr || !$tr.length) {
        return;
    }

    $tr.removeAttr('data-stok');
    $tr.removeAttr('data-stok-fisik');
    $tr.removeAttr('data-stok-max');
    $tr.find('[data-field="stok"]').val('').removeAttr('title');
    $tr.find('[data-field="val"]').val('');
    pmkAddRecalcRow($tr);
    pmkAddRecalcTotal();
}

/* ------------------------------------------------------------------ */
/* SINKRONISASI STOK ANTAR BARIS (TUGAS 1)                             */
/* Satu request per kombinasi gudang + barang + batch. Server         */
/* mengembalikan stok fisik (sc_trx.stkblc) dan total draft user      */
/* (sc_tmp.pmk_brng_dtl). Stok tersedia tiap baris = fisik - draft    */
/* baris LAIN (qty baris itu sendiri tidak ikut mengurangi).          */
/* ------------------------------------------------------------------ */
var pmkAddStockSyncTimer = null;
var pmkAddStockSyncSeq = 0;

function pmkAddStockGroups() {
    var groups = {};

    if (!pmkAddExists()) {
        return groups;
    }

    $('#pmkGridBody tr.pmk-row-saved, #pmkGridBody tr.pmk-row-draft').each(function () {
        var $tr = $(this);

        /* baris tersimpan memakai span readonly -> ambil dari data-attribute */
        var idlocation = $.trim($tr.find('select[data-field="idlocation"]').val() || '')
            || $.trim($tr.attr('data-idlocation') || '');
        var idbarang = $.trim($tr.find('select[data-field="idbarang"]').val() || '')
            || $.trim($tr.attr('data-idbarang') || '');
        var batch = $.trim($tr.find('[data-field="batch"]').val() || '')
            || $.trim($tr.attr('data-batch') || '');

        batch = batch.toUpperCase();

        if (idlocation === '' || idbarang === '') {
            return;
        }

        var key = idlocation + '|' + idbarang + '|' + batch;

        if (!groups[key]) {
            groups[key] = {
                idlocation: idlocation,
                idbarang: idbarang,
                batch: batch,
                rows: []
            };
        }

        groups[key].rows.push($tr);
    });

    return groups;
}

function pmkAddSyncStock() {
    var groups = pmkAddStockGroups();
    var seq = ++pmkAddStockSyncSeq;

    $.each(groups, function (key, group) {
        $.ajax({
            url: HOST_URL + 'persediaan/trans/get_stock_pmk_brng',
            type: 'POST',
            dataType: 'json',
            data: {
                idlocation: group.idlocation,
                idbarang: group.idbarang,
                batch: group.batch
            },
            success: function (res) {
                /* abaikan respons bila sudah ada sinkronisasi lebih baru */
                if (seq !== pmkAddStockSyncSeq) {
                    return;
                }

                if (!res || !res.success) {
                    return;
                }

                var fisik  = pmkAddNum(res.stok_fisik !== undefined ? res.stok_fisik : res.stok);
                var booked = pmkAddNum(res.stok_tmp);

                $.each(group.rows, function (i, $tr) {
                    if (!$tr.closest('body').length) {
                        return;   /* baris sudah dihapus / dirender ulang */
                    }

                    pmkAddApplyStock($tr, fisik, booked);
                });
            }
        });
    });
}

/** Debounce: hindari request beruntun saat Qty diketik cepat. */
function pmkAddScheduleStockSync() {
    if (pmkAddStockSyncTimer) {
        clearTimeout(pmkAddStockSyncTimer);
    }

    pmkAddStockSyncTimer = setTimeout(function () {
        pmkAddStockSyncTimer = null;
        pmkAddSyncStock();
    }, 250);
}

/**
 * Ambil stok tersedia untuk kombinasi Gudang + ID Barang + Batch.
 * @param {jQuery} $tr
 * @param {function} [callback] dipanggil setelah request selesai (success/error)
 * @param {boolean} [preservePrice] true = jangan timpa harga yang sudah ada
 */
function pmkAddLoadStock($tr, callback, preservePrice) {
    if (!$tr || !$tr.length) {
        if (callback) callback(null);
        return;
    }

    var idlocation = $.trim($tr.find('select[data-field="idlocation"]').val() || '');
    var idbarang   = $.trim($tr.find('select[data-field="idbarang"]').val() || '');
    var batch      = $.trim($tr.find('[data-field="batch"]').val() || '');

    if (idlocation === '' || idbarang === '') {
        pmkAddResetStock($tr);
        if (callback) callback(null);
        return;
    }

    var seq = (parseInt($tr.attr('data-stok-seq') || '0', 10) || 0) + 1;
    $tr.attr('data-stok-seq', seq);

    $.ajax({
        url: HOST_URL + 'persediaan/trans/get_stock_pmk_brng',
        type: 'POST',
        dataType: 'json',
        data: {
            idlocation: idlocation,
            idbarang: idbarang,
            batch: batch,
            idurut: $.trim($tr.attr('data-idurut') || '')
        },
        success: function (res) {
            /* abaikan respons lama bila user sudah mengganti pilihan */
            if (parseInt($tr.attr('data-stok-seq') || '0', 10) !== seq) {
                if (callback) callback(null);
                return;
            }

            if (!res || !res.success) {
                pmkAddResetStock($tr);
                if (callback) callback(res || null);
                return;
            }

            var fisik = (res.stok_fisik !== undefined && res.stok_fisik !== null)
                ? res.stok_fisik
                : (res.stok || 0);

            /* total qty draft user untuk grup ini (buffering) */
            var booked = (res.stok_tmp !== undefined && res.stok_tmp !== null)
                ? res.stok_tmp
                : 0;

            pmkAddSetStock($tr, fisik, res.avgcost || 0, !!preservePrice, booked);

            /* baris lain dengan kombinasi sama ikut disinkronkan */
            pmkAddScheduleStockSync();

            if (callback) callback(res);
        },
        error: function () {
            pmkAddResetStock($tr);
            if (callback) callback(null);
        }
    });
}

/* ------------------------------------------------------------------ */
/* NILAI BARIS & TOTAL                                                 */
/* ------------------------------------------------------------------ */
function pmkAddCollect($tr) {
    return {
        idurut: $.trim($tr.attr('data-idurut') || ''),
        iduniq: $.trim($tr.attr('data-iduniq') || ''),
        idlocation: $.trim($tr.find('select[data-field="idlocation"]').val() || ''),
        idbarang: $.trim($tr.find('select[data-field="idbarang"]').val() || ''),
        nmbarang: $.trim($tr.find('[data-field="nmbarang"]').val() || ''),
        batch: $.trim($tr.find('[data-field="batch"]').val() || '').toUpperCase(),
        unit: $.trim($tr.find('[data-field="unit"]').val() || ''),
        qty: pmkAddNum($tr.find('[data-field="qty"]').val()),
        val: pmkAddNum($tr.find('[data-field="val"]').val()),
        stok: pmkAddStockOf($tr),
        stok_max: pmkAddStockMaxOf($tr),
        description: $.trim($tr.find('[data-field="description"]').val() || '').toUpperCase()
    };
}

function pmkAddRecalcRow($tr) {
    if (!$tr || !$tr.length) {
        return { qty: 0, val: 0, valsum: 0 };
    }

    var d = pmkAddCollect($tr);
    var valsum = Number((d.qty * d.val).toFixed(4));

    $tr.find('[data-field="valsum"]').val(valsum > 0 ? pmkAddDec(valsum, 2) : '');

    return { qty: d.qty, val: d.val, valsum: valsum };
}

function pmkAddRecalcTotal() {
    var totalQty = 0;
    var totalNilai = 0;

    $.each(pmkAddState.rows, function (i, row) {
        totalQty += pmkAddNum(row.qty);
        totalNilai += pmkAddNum(row.valsum);
    });

    $('#pmkGridBody tr.pmk-row-draft[data-mode="new"]').each(function () {
        var hitung = pmkAddRecalcRow($(this));

        totalQty += hitung.qty;
        totalNilai += hitung.valsum;
    });

    $('#totalQty').text(pmkAddDec(totalQty, 2));
    $('#totalNilai').text(pmkAddDec(totalNilai, 2));
}

/* ------------------------------------------------------------------ */
/* FOKUS & NAVIGASI KEYBOARD                                           */
/* ------------------------------------------------------------------ */
function pmkAddFocus($tr, field) {
    if (!$tr || !$tr.length) {
        return;
    }

    try {
        $tr[0].scrollIntoView({ block: 'nearest' });
    } catch (e) {
        /* abaikan browser lama */
    }

    var $el = $tr.find('[data-field="' + field + '"]').first();

    if (!$el.length) {
        return;
    }

    if ($el.hasClass('select2-hidden-accessible')) {
        pmkAddOpenSelect2($el);
        return;
    }

    $el.trigger('focus');

    if ($el.is('input[type="text"]')) {
        try {
            var el = $el[0];
            el.setSelectionRange(el.value.length, el.value.length);
        } catch (e2) {
            /* tipe input tidak mendukung selection range */
        }
    }
}

function pmkAddNext($tr, field, backward) {
    var idx = pmk_ADD_FIELDS.indexOf(field);
    var next;

    if (idx < 0) {
        return;
    }

    next = backward ? pmk_ADD_FIELDS[idx - 1] : pmk_ADD_FIELDS[idx + 1];

    if (next) {
        pmkAddFocus($tr, next);
        return;
    }

    pmkAddLastField($tr);
}

function pmkAddLastField($tr) {
    var d = pmkAddCollect($tr);
    var hasData = (d.idlocation !== '' || d.idbarang !== '' || d.batch !== '' ||
        d.qty > 0 || d.val > 0 || d.description !== '');

    if (!hasData) {
        pmkAddFocus($tr, 'idlocation');
        return;
    }

    pmkAddSave($tr, true);
}


/* ------------------------------------------------------------------ */
/* EVENT PER BARIS (TAB / ENTER / FORMAT ANGKA)                        */
/* ------------------------------------------------------------------ */
function pmkAddBindRow($tr) {
    /* format angka saat mengetik qty & harga */
    $tr.find('.pmk-num').off('.pmknum').on('input.pmknum', function () {
        var dec = parseInt($(this).attr('data-dec'), 10);
        var el = this;

        if (isNaN(dec)) {
            dec = 2;
        }

        if (el.selectionStart === null || el.selectionStart === (el.value || '').length) {
            el.value = pmkAddFormatText(el.value, dec);
        }

        pmkAddRecalcRow($tr);
        pmkAddRecalcTotal();
    }).on('blur.pmknum', function () {
        var dec = parseInt($(this).attr('data-dec'), 10);

        if (isNaN(dec)) {
            dec = 2;
        }

        this.value = pmkAddFormatText(this.value, dec);
        pmkAddRecalcRow($tr);
        pmkAddRecalcTotal();
    });

    /* QTY berubah -> stok tersedia baris lain pada kombinasi gudang + barang +
       batch yang sama ikut dihitung ulang (buffering stok) */
    $tr.find('[data-field="qty"]').off('change.pmkstock').on('change.pmkstock', function () {
        pmkAddScheduleStockSync();
    });

    /* ENTER = kolom berikutnya; ENTER di kolom terakhir = simpan baris
       (ESC ditangani global lewat delegation di pmkAddBindStatic) */
    $tr.find('.pmk-input').off('.pmkkey').on('keydown.pmkkey', function (e) {
        if (e.key !== 'Enter' && e.keyCode !== 13) {
            return;
        }

        e.preventDefault();

        var field = $(this).attr('data-field') || '';

        if (field === 'description') {
            pmkAddLastField($tr);
            return;
        }

        pmkAddNext($tr, field, e.shiftKey);
    });

    /* TAB di kolom terakhir = simpan baris + tambah baris baru */
    $tr.find('[data-field="description"]').off('.pmktab').on('keydown.pmktab', function (e) {
        if ((e.key === 'Tab' || e.keyCode === 9) && !e.shiftKey) {
            e.preventDefault();
            pmkAddLastField($tr);
        }
    });

    /* Tandai navigasi TAB dari dalam baris (untuk auto-open Select2) */
    $tr.off('keydown.pmknavtrack').on('keydown.pmknavtrack', function (e) {
        if (e.key === 'Tab' || e.keyCode === 9) {
            pmkAddLastKey = 'Tab';
        }
    });

    /* Tombol Simpan & Batal (X) TIDAK di-bind di sini.
       Keduanya memakai event delegation global di pmkAddBindStatic()
       agar tetap bekerja untuk baris yang dibuat/di-render ulang. */
}

/* ------------------------------------------------------------------ */
/* BATAL BARIS (tombol X / Esc)                                        */
/* ------------------------------------------------------------------ */
/** Fokuskan baris tersimpan (checkbox) agar navigasi keyboard tetap jalan */
function pmkAddFocusRow($row) {
    if (!$row || !$row.length) {
        return;
    }

    try {
        $row[0].scrollIntoView({ block: 'nearest' });
    } catch (e) {
        /* browser lama */
    }

    var $check = $row.find('input.row-check').first();

    if ($check.length) {
        $check.trigger('focus');
        return;
    }

    $row.find('.pmk-btn-edit').first().trigger('focus');
}

/**
 * Lepas Select2 dari semua <select> dalam satu baris (sebelum baris dihapus).
 */
function pmkAddDestroyRowSelects($tr) {
    if (!$tr || !$tr.length) {
        return;
    }

    $tr.find('select').each(function () {
        var $sel = $(this);

        try {
            if ($sel.hasClass('select2-hidden-accessible')) {
                if ($sel.data('select2') && $sel.data('select2').isOpen()) {
                    $sel.select2('close');
                }

                $sel.select2('destroy');
            }
        } catch (err) {
            /* abaikan: elemen mungkin sudah dilepas */
        }
    });
}

/**
 * Kembalikan satu baris edit menjadi baris readonly dengan data awal
 * (tanpa request ke server, tanpa mengganggu baris lain).
 *
 * @return {boolean} true bila baris berhasil di-revert
 */
function pmkAddRevertEditRow($tr) {
    var idurut = $.trim($tr.attr('data-idurut') || '');
    var row = null;

    $.each(pmkAddState.rows, function (i, r) {
        if (String(r.idurut) === String(idurut)) {
            row = r;
            return false;
        }
    });

    if (!row) {
        return false;
    }

    pmkAddDestroyRowSelects($tr);

    var $saved = $(pmkAddSavedRow(row));
    $tr.replaceWith($saved);

    pmkAddRecalcTotal();
    pmkAddSelectionState();
    pmkAddFocusRow($saved);

    /* qty kembali ke nilai tersimpan -> stok grup dihitung ulang */
    pmkAddScheduleStockSync();

    return true;
}

/**
 * Batalkan baris yang sedang diinput/diedit.
 *  - BARIS BARU  : <tr> LANGSUNG DIHAPUS dari DOM.
 *                  Baris kosong baru hanya dibuat bila tabel menjadi kosong.
 *  - BARIS EDIT  : nilai dikembalikan ke data awal, baris tetap ada (readonly).
 *
 * @return {boolean} true bila berhasil dibatalkan
 */
function pmkAddCancelRow($tr) {
    if (!$tr || !$tr.length) {
        pmkAddLog('cancel: baris tidak ditemukan');
        return false;
    }

    try {
        var mode = String($tr.attr('data-mode') || '');
        var $prevSaved = $tr.prevAll('tr.pmk-row-saved').first();

        /* hanya baris editor (new/edit) yang bisa dibatalkan */
        if (mode !== 'edit' && mode !== 'new') {
            return false;
        }

        pmkAddLog('cancel: mode =', mode, 'idurut =', $tr.attr('data-idurut'));

        /* ---------- BARIS EDIT: revert nilai, baris tetap ada ---------- */
        if (mode === 'edit') {
            if (pmkAddRevertEditRow($tr)) {
                pmkAddToast('Perubahan baris dibatalkan', 'info');
                pmkAddLog('cancel: baris edit di-revert');
                return true;
            }

            /* data asli tidak ketemu -> fallback render ulang */
            pmkAddRender();
            pmkAddLog('cancel: fallback render untuk baris edit');
            return true;
        }

        /* ---------- BARIS BARU: hapus elemen <tr> dari DOM ---------- */
        /* tutup dropdown Select2 grid yang mungkin sedang terbuka */
        $('#pmkGrid .select2-container--open').each(function () {
            var $select = $(this).prev('select.select2-hidden-accessible');

            if ($select.length) {
                try {
                    $select.select2('close');
                } catch (err) {
                    /* abaikan */
                }
            }
        });

        pmkAddDestroyRowSelects($tr);

        $tr.remove();   // <-- baris baru benar-benar dihapus dari DOM

        pmkAddLog('cancel: baris baru dihapus, sisa baris =', $('#pmkGridBody tr').length);

        pmkAddToast('Baris baru dibatalkan', 'info');

        /* Baris kosong TIDAK dibuat otomatis.
           Bila tabel menjadi kosong, tampilkan empty state. */
        pmkAddRenderEmptyState();

        pmkAddRecalcTotal();
        pmkAddSelectionState();

        /* fokus ke baris sebelumnya (valid), atau ke empty state */
        if ($prevSaved.length) {
            pmkAddFocusRow($prevSaved);
        } else {
            var $lastRow = $('#pmkGridBody tr.pmk-row-saved').last();

            if ($lastRow.length) {
                pmkAddFocusRow($lastRow);
            } else {
                $('#pmkGridBody tr.pmk-empty-row td').first().trigger('focus');
            }
        }

        return true;
    } catch (err) {
        console.error('pmkAddCancelRow gagal:', err);
        return false;
    }
}

/* ------------------------------------------------------------------ */
/* VALIDASI & SIMPAN BARIS                                             */
/* ------------------------------------------------------------------ */
function pmkAddInvalid($tr, field, message) {
    var $el = $tr.find('[data-field="' + field + '"]');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('open');
    } else {
        $el.addClass('pmk-invalid');

        window.setTimeout(function () {
            $el.removeClass('pmk-invalid');
        }, 1800);

        pmkAddFocus($tr, field);
    }

    Swal.fire({
        icon: 'warning',
        title: 'Data Belum Lengkap',
        text: message
    });
}

/**
 * Validasi satu baris editor / draft.
 * @return {object} { ok:true, data } atau { ok:false, field, title, message }
 */
function pmkAddValidateRow($tr) {
    var d = pmkAddCollect($tr);

    if (!pmkAddDocno()) {
        return { ok: false, title: 'Nomor Jurnal Belum Terbentuk', message: 'Lengkapi Cabang / Job dan No. Jurnal terlebih dahulu.' };
    }

    if ($.trim($('#cabang').val() || '') === '') {
        return { ok: false, title: 'Cabang Belum Dipilih', message: 'Pilih Cabang / Job pada bagian Master terlebih dahulu.' };
    }

    if ($.trim($('#docdate').val() || '') === '') {
        return { ok: false, title: 'Tanggal Belum Terisi', message: 'Lengkapi Tanggal dokumen pada bagian Master terlebih dahulu.' };
    }

    if ($.trim($('#idcostcenter').val() || '') === '') {
        return { ok: false, title: 'Cost Center Belum Dipilih', message: 'Pilih Bagian / Cost Center pada bagian Master terlebih dahulu.' };
    }

    if (!d.idlocation) {
        return { ok: false, field: 'idlocation', message: 'Pilih gudang terlebih dahulu.' };
    }

    if (!d.idbarang) {
        return { ok: false, field: 'idbarang', message: 'Pilih ID barang terlebih dahulu.' };
    }

    if (!d.unit) {
        return { ok: false, field: 'unit', message: 'Pilih satuan barang terlebih dahulu.' };
    }

    if (!(d.qty > 0)) {
        return { ok: false, field: 'qty', message: 'Qty harus lebih besar dari 0.' };
    }

    /* =========================
       VALIDASI STOK (PMK = pengeluaran)
       Qty tidak boleh melebihi stok yang masih boleh dipakai baris ini
       (= stok fisik - qty draft baris lain pada grup yang sama).
       ========================= */
    var stokTersedia = pmkAddStockMaxOf($tr);

    if (d.qty > stokTersedia) {
        return {
            ok: false,
            field: 'qty',
            message: 'Qty melebihi stok tersedia. Maksimal untuk baris ini: '
                + pmkAddDec(stokTersedia, 2) + '.'
        };
    }

    if (d.val < 0) {
        return { ok: false, field: 'val', message: 'Harga tidak boleh negatif.' };
    }

    return { ok: true, data: d };
}

/**
 * Simpan satu baris ke temporary detail (versi promise).
 * options:
 *   silent                 : tidak menampilkan toast sukses
 *   reload                 : reload grid setelah sukses (default true)
 *   treatReloadAsSuccess   : bila server minta reload (header baru dibuat),
 *                            anggap sukses tanpa reload halaman (alur final)
 */
function pmkAddSaveRow($tr, options) {
    options = options || {};

    var dfd = $.Deferred();

    if (pmkAddState.saving) {
        return dfd.reject({ status: 'busy' }).promise();
    }

    if (!$tr || !$tr.length) {
        return dfd.reject({ status: 'norow' }).promise();
    }

    var check = pmkAddValidateRow($tr);

    if (!check.ok) {
        if (check.field) {
            pmkAddInvalid($tr, check.field, check.message);
        } else {
            Swal.fire({
                icon: 'warning',
                title: check.title || 'Data Belum Lengkap',
                text: check.message
            });
        }

        return dfd.reject({ status: 'invalid', check: check }).promise();
    }

    var d = check.data;

    var payload = {
        docno: pmkAddDocno(),
        idurut: d.idurut,
        iduniq: d.iduniq,
        cabang: $.trim($('#cabang').val() || ''),
        pemohon: $.trim($('#pemohon').val() || ''),
        docdate: $.trim($('#docdate').val() || ''),
        docref: $.trim($('#docref').val() || ''),
        idcostcenter: $.trim($('#idcostcenter').val() || ''),
        header_description: $.trim($('#keterangan').val() || ''),
        description: d.description,
        idbarang: d.idbarang,
        nmbarang: d.nmbarang,
        unit: d.unit,
        idlocation_dtl: d.idlocation,
        idlocation: d.idlocation,
        qtystock: pmkAddStockFisikOf($tr),
        batch: d.batch,
        qty: d.qty,
        val: d.val,
        valsum: Number((d.qty * d.val).toFixed(4))
    };

    pmkAddState.saving = true;
    $tr.addClass('pmk-row-busy');

    $.ajax({
        url: HOST_URL + 'persediaan/trans/save_pmk_brng_detail',
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function (res) {
            pmkAddState.saving = false;
            $tr.removeClass('pmk-row-busy');

            if (!res || !res.success) {
                Swal.fire({
                    icon: 'warning',
                    title: (res && res.code === 'COA') ? 'COA Belum Lengkap' : 'Gagal',
                    text: (res && res.message) ? res.message : 'Data detail gagal disimpan.'
                });

                dfd.reject({ status: 'server', res: res });
                return;
            }

            /* Simpan docno hasil pembuatan header agar Simpan Final berikutnya
               tetap mengirim nomor dokumen yang benar (tanpa reload halaman). */
            if (res.docno && $.trim($('#docno').val() || '') === '') {
                $('#docno').val($.trim(res.docno));
            }

            /* header baru dibuat server -> biasanya reload agar nomor dokumen tampil.
               Pada alur Simpan Final, reload tidak diperlukan (proses final memakai
               inputby/session), jadi cukup lanjut. */
            if (res.reload === true && options.treatReloadAsSuccess !== true) {
                window.location.reload();
                dfd.resolve({ res: res, data: d, reloaded: true });
                return;
            }

            if (options.reload !== false) {
                /* Baris BARU  -> lanjut sesi input (baris kosong berikutnya dibuat).
                   Baris EDIT  -> kembali ke daftar readonly TANPA baris baru. */
                var isNewRow = $.trim(d.idurut) === '';

                pmkAddLoadRows(isNewRow, isNewRow);
            } else {
                /* tanpa reload: qty tersimpan berubah -> sinkronkan stok grup */
                if (res.idurut) {
                    $tr.attr('data-idurut', res.idurut);
                }

                $tr.attr('data-qty-saved', d.qty);
                pmkAddScheduleStockSync();
            }

            if (!options.silent) {
                pmkAddToast('Baris ' + d.idbarang + ' tersimpan');
            }

            dfd.resolve({ res: res, data: d });
        },
        error: function (xhr) {
            pmkAddState.saving = false;
            $tr.removeClass('pmk-row-busy');

            console.error(xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Terjadi kesalahan server saat menyimpan baris.'
            });

            dfd.reject({ status: 'error' });
        }
    });

    return dfd.promise();
}

/**
 * Wrapper simpan baris (kompatibel dengan pemanggil lama).
 */
function pmkAddSave($tr, silent) {
    return pmkAddSaveRow($tr, { silent: !!silent });
}


/* ------------------------------------------------------------------ */
/* EDIT INLINE, HAPUS (SATU / BULK), STATE CHECKBOX                    */
/* ------------------------------------------------------------------ */
function pmkAddCheckedIds() {
    return $('#pmkGridBody tr.pmk-row-saved .row-check:checked').map(function () {
        return $(this).val();
    }).get();
}

function pmkAddSelectionState() {
    var total = $('#pmkGridBody tr.pmk-row-saved').length;
    var checked = $('#pmkGridBody tr.pmk-row-saved .row-check:checked').length;
    var editing = $('#pmkGridBody tr[data-mode="edit"]').length;

    $('#checkAll').prop('checked', total > 0 && checked === total);
    $('#btnUpdateDetail').prop('disabled', !(checked === 1 || editing > 0));

    /* Tombol hapus tetap bisa diklik saat ada baris tapi belum dicentang,
       supaya muncul pesan "Pilih minimal 1 baris untuk dihapus." */
    $('#btnDeleteDetail').prop('disabled', total === 0);

    $('#pmkSelectedRows').val(pmkAddCheckedIds().join(','));

    $('#pmkSelectedInfo').text(checked + ' dari ' + total + ' baris dipilih');
}

function pmkAddEditSelected() {
    var ids = pmkAddCheckedIds();

    if (ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Centang satu baris yang akan diupdate.'
        });
        return;
    }

    if (ids.length > 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Update hanya boleh satu baris.'
        });
        return;
    }

    pmkAddEditRow(ids[0]);
}

/** Tombol #btnUpdateDetail: simpan baris yang sedang diedit,
 *  atau masuk mode edit untuk baris yang dicentang. */
function pmkAddUpdateSelected() {
    var $editing = $('#pmkGridBody tr[data-mode="edit"]').first();

    if ($editing.length) {
        pmkAddSave($editing, false);
        return;
    }

    pmkAddEditSelected();
}

/** Cek apakah baris draft berisi data (untuk mencegah data hilang) */
function pmkAddDraftHasData($draft) {
    if (!$draft || !$draft.length) {
        return false;
    }

    var d = pmkAddCollect($draft);

    return (d.idlocation !== '' || d.idbarang !== '' || d.batch !== '' ||
        d.qty > 0 || d.val > 0 || d.description !== '');
}

function pmkAddEditRow(idurut) {
    var $draft = $('#pmkGridBody tr.pmk-row-draft[data-mode="new"]').first();

    if ($draft.length && pmkAddDraftHasData($draft)) {
        Swal.fire({
            icon: 'question',
            title: 'Baris Baru Belum Disimpan',
            text: 'Isi baris baru yang sedang diketik akan dibuang. Lanjutkan edit baris tersimpan?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then(function (res) {
            if (res.isConfirmed) {
                pmkAddEditRowNow(idurut);
            }
        });

        return;
    }

    pmkAddEditRowNow(idurut);
}

function pmkAddEditRowNow(idurut) {
    var row = null;

    $.each(pmkAddState.rows, function (i, r) {
        if (String(r.idurut) === String(idurut)) {
            row = r;
            return false;
        }
    });

    if (!row) {
        return;
    }

    var $target = $('#pmkGridBody tr.pmk-row-saved[data-idurut="' + idurut + '"]');

    pmkAddRemoveEditors();

    var $editor = pmkAddEditorRow(row).addClass('pmk-row-edit').attr('data-mode', 'edit');

    if ($target.length) {
        $target.replaceWith($editor);
    } else {
        $('#pmkGridBody').append($editor);
    }

    pmkAddSetupSelects($editor, row);
    pmkAddBindRow($editor);
    pmkAddRecalcRow($editor);
    pmkAddLoadStock($editor, null, true);   /* refresh stok, harga tersimpan dipertahankan */
    pmkAddSelectionState();
    pmkAddFocus($editor, 'batch');
}

function pmkAddDeleteRow(idurut) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Hapus baris detail ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545'
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        pmkAddDeleteIds([idurut]);
    });
}

function pmkAddDeleteChecked() {
    var ids = pmkAddCheckedIds();

    if (ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Baris Dipilih',
            text: 'Pilih minimal 1 baris untuk dihapus.'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: 'Hapus <b>' + ids.length + '</b> baris yang dipilih?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545'
    }).then(function (result) {
        if (result.isConfirmed) {
            pmkAddDeleteIds(ids);
        }
    });
}

function pmkAddDeleteIds(ids) {
    /* jaga baris input yang sedang diisi (jika ada) agar tidak hilang saat reload */
    var $draftNow = $('#pmkGridBody tr.pmk-row-draft[data-mode="new"]').first();
    var keepDraft = $draftNow.length > 0 && pmkAddDraftHasData($draftNow);

    $.ajax({
        url: HOST_URL + 'persediaan/trans/delete_pmk_brng',
        type: 'POST',
        data: { ids: ids },
        dataType: 'json',
        success: function (res) {
            if (res && res.success) {
                /* 1) buang dari state lokal */
                var idSet = {};

                $.each(ids, function (i, id) {
                    idSet[String(id).trim()] = true;
                });

                pmkAddState.rows = $.grep(pmkAddState.rows || [], function (row) {
                    return !idSet[String(row.idurut).trim()];
                });

                /* 2) hapus elemen <tr> dari DOM */
                $.each(ids, function (i, id) {
                    $('#pmkGridBody tr.pmk-row-saved[data-idurut="' + String(id).trim() + '"]').remove();
                });

                /* 3) reset Select All + hitung ulang total & empty state */
                $('#checkAll').prop('checked', false);
                pmkAddRecalcTotal();
                pmkAddSelectionState();
                pmkAddRenderEmptyState();

                pmkAddToast('Data detail dihapus');

                /* 4) sinkronkan ulang dengan server */
                pmkAddLoadRows(false, keepDraft);

                /* 5) stok tersedia baris lain bertambah -> hitung ulang */
                pmkAddScheduleStockSync();
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Gagal',
                text: (res && res.message) ? res.message : 'Gagal menghapus data'
            });
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Terjadi kesalahan server saat menghapus baris.'
            });
        }
    });
}


/* ------------------------------------------------------------------ */
/* AKSI GRID: SIMPAN (✓), BATAL (X), ESC                               */
/* ------------------------------------------------------------------ */
/**
 * Satu-satunya handler untuk tombol Simpan/Batal dan tombol ESC.
 * Memakai native addEventListener fase CAPTURE di document sehingga:
 *  - selalu terpanggil lebih dulu (tidak bisa dihentikan stopPropagation
 *    oleh library lain / Select2),
 *  - tetap bekerja untuk baris yang dibuat & di-render ulang (delegation).
 * Hanya dipasang sekali (guard window.pmkAddActionsBound).
 */
function pmkAddBindGridActions() {
    if (window.pmkAddActionsBound) {
        pmkAddLog('actions: sudah terpasang, skip');
        return;
    }

    window.pmkAddActionsBound = true;

    pmkAddLog('actions: handler terpasang');

    /* ---------------- KLIK: SIMPAN / BATAL ---------------- */
    document.addEventListener('click', function (ev) {
        var target = ev.target;

        if (!target || typeof target.closest !== 'function') {
            return;
        }

        var cancelBtn = target.closest('#pmkGrid [data-act="cancel"]');

        if (cancelBtn) {
            ev.preventDefault();
            ev.stopPropagation();

            var $row = $(cancelBtn).closest('tr');

            pmkAddLog('klik X -> mode =', $row.length ? $row.attr('data-mode') : '(row hilang)');

            if ($row.length && $.contains(document, $row[0])) {
                pmkAddCancelRow($row);
            }

            return;
        }

        var saveBtn = target.closest('#pmkGrid [data-act="save"]');

        if (saveBtn) {
            ev.preventDefault();
            ev.stopPropagation();

            var $saveRow = $(saveBtn).closest('tr');

            if ($saveRow.length && $.contains(document, $saveRow[0])) {
                pmkAddSave($saveRow, false);
            }
        }
    }, true);

    /* ---------------- MOUSEDOWN X: tutup dropdown grid ---------------- */
    document.addEventListener('mousedown', function (ev) {
        var target = ev.target;

        if (!target || typeof target.closest !== 'function' ||
            !target.closest('#pmkGrid [data-act="cancel"]')) {
            return;
        }

        $('#pmkGrid .select2-container--open').each(function () {
            var $sel = $(this).prev('select.select2-hidden-accessible');

            if ($sel.length) {
                try {
                    $sel.select2('close');
                } catch (err) {
                    /* abaikan */
                }
            }
        });
    }, true);

    /* ---------------- ESC: batalkan baris aktif ---------------- */
    document.addEventListener('keydown', function (ev) {
        if (ev.key !== 'Escape' && ev.keyCode !== 27) {
            return;
        }

        var active = document.activeElement;

        if (!active || typeof active.closest !== 'function') {
            return;
        }

        var row = active.closest('#pmkGridBody tr.pmk-row-draft');

        /* fokus di luar baris (mis. kotak search Select2) -> biarkan
           Select2 yang menutup dropdown-nya sendiri */
        if (!row || !document.body.contains(row)) {
            return;
        }

        ev.preventDefault();

        pmkAddLog('ESC -> batalkan baris mode =', row.getAttribute('data-mode'));

        pmkAddCancelRow($(row));
    }, true);
}

/* ------------------------------------------------------------------ */
/* EVENT STATIS GRID (delegation)                                      */
/* ------------------------------------------------------------------ */
function pmkAddBindStatic() {
    var $grid = $('#pmkGrid');

    $grid.off('.pmkstatic');

    $grid.on('click.pmkstatic', '.pmk-btn-edit', function (e) {
        e.preventDefault();
        pmkAddEditRow($(this).closest('tr').attr('data-idurut'));
    });

    $grid.on('click.pmkstatic', '.pmk-btn-delete', function (e) {
        e.preventDefault();
        pmkAddDeleteRow($(this).closest('tr').attr('data-idurut'));
    });

    /* Tombol Hapus baris di header card.
       Memakai event delegation (BUKAN inline onclick) karena elemen tombol
       memiliki id="btnDeleteDetail" sehingga inline onclick="btnDeleteDetail()"
       akan resolve ke elemen itu sendiri, bukan ke fungsi global. */
    $(document).off('click.pmkdelete', '#btnDeleteDetail')
        .on('click.pmkdelete', '#btnDeleteDetail', function (e) {
            e.preventDefault();
            e.stopPropagation();
            pmkAddDeleteChecked();
        });

    /* Tombol Update baris terpilih (delegation, bukan inline onclick) */
    $(document).off('click.pmkupdate', '#btnUpdateDetail')
        .on('click.pmkupdate', '#btnUpdateDetail', function (e) {
            e.preventDefault();
            e.stopPropagation();
            pmkAddUpdateSelected();
        });

    /* Tombol Simpan (✓), Batal (X) dan tombol ESC ditangani oleh
       pmkAddBindGridActions() memakai native event capture. */
    pmkAddBindGridActions();

    $grid.on('change.pmkstatic', '#checkAll', function () {
        var checked = this.checked;

        $('#pmkGridBody .row-check').prop('checked', checked);
        $('#pmkGridBody tr.pmk-row-saved').toggleClass('pmk-row-checked', checked);
        pmkAddSelectionState();
    });

    $grid.on('change.pmkstatic', '.row-check', function () {
        $(this).closest('tr').toggleClass('pmk-row-checked', this.checked);
        pmkAddSelectionState();
    });
}

/* ------------------------------------------------------------------ */
/* SIMPAN FINAL: HANYA BARIS TERPILIH / KONFIRMASI                     */
/* ------------------------------------------------------------------ */
/* ------------------------------------------------------------------ */
/* SIMPAN FINAL: SEMUA BARIS VALID (TANPA SYARAT CHECKBOX)             */
/* Checkbox hanya dipakai untuk hapus massal (btnDeleteDetail).        */
/* ------------------------------------------------------------------ */

/** Tunggu sampai proses simpan baris yang sedang berjalan selesai */
function pmkAddWaitForSaving() {
    var dfd = $.Deferred();
    var tries = 0;

    var check = function () {
        if (!pmkAddState.saving || tries >= 60) {   // maksimal ±6 detik
            dfd.resolve();
            return;
        }

        tries++;
        window.setTimeout(check, 100);
    };

    check();

    return dfd.promise();
}

/** Baris editor/draft yang masih berisi data & belum tersimpan */
function pmkAddGetPendingRows() {
    var rows = [];

    $('#pmkGridBody tr.pmk-row-draft').each(function () {
        var $tr = $(this);
        var mode = String($tr.attr('data-mode') || '');

        if (mode === 'edit' || pmkAddDraftHasData($tr)) {
            rows.push($tr);
        }
    });

    return rows;
}

/** Simpan semua baris editor yang tertunda secara berurutan */
function pmkAddCommitPendingRows() {
    var dfd = $.Deferred();
    var rows = pmkAddGetPendingRows();
    var i = 0;

    var next = function () {
        if (i >= rows.length) {
            dfd.resolve();
            return;
        }

        var $row = rows[i++];

        pmkAddSaveRow($row, {
            silent: true,
            reload: false,
            treatReloadAsSuccess: true
        }).done(function () {
            next();
        }).fail(function (arg) {
            dfd.reject(arg);
        });
    };

    next();

    return dfd.promise();
}

/** Baris valid: ID Barang & Gudang & Satuan terisi, Qty > 0 */
function pmkAddGetValidRows() {
    return $.grep(pmkAddState.rows || [], function (row) {
        return $.trim(row.idbarang || '') !== ''
            && $.trim(row.idlocation || '') !== ''
            && $.trim(row.unit || '') !== ''
            && pmkAddNum(row.qty) > 0;
    });
}

/** Payload JSON baris valid yang dikirim ke controller */
function pmkAddBuildDetailPayload(rows) {
    return $.map(rows, function (row) {
        return {
            idurut: row.idurut,
            idbarang: $.trim(row.idbarang || ''),
            nmbarang: $.trim(row.nmbarang || ''),
            idlocation: $.trim(row.idlocation || ''),
            batch: $.trim(row.batch || ''),
            unit: $.trim(row.unit || ''),
            qty: pmkAddNum(row.qty),
            val: pmkAddNum(row.val),
            valsum: pmkAddNum(row.valsum),
            stok: pmkAddNum(row.stok),
            stok_max: pmkAddNum(row.stok_max !== undefined && row.stok_max !== null
                ? row.stok_max
                : row.stok),
            description: $.trim(row.description || '')
        };
    });
}

/** Cari baris yang Qty-nya melebihi batas stok (validasi ulang saat final) */
function pmkAddFirstOverStock(rows) {
    var found = null;

    $.each(rows || [], function (i, row) {
        var qty  = pmkAddNum(row.qty);

        /* batas baris = stok fisik - draft baris lain (fallback ke stok tampil) */
        var stok = pmkAddNum(row.stok_max !== undefined && row.stok_max !== null
            ? row.stok_max
            : row.stok);

        if (row.stok_max === null || row.stok_max === undefined) {
            if (row.stok === null || row.stok === undefined || ($.trim(String(row.stok)) === '')) {
                stok = 0;
            }
        }

        if (qty > stok) {
            found = {
                idbarang: $.trim(row.idbarang || ''),
                qty: qty,
                stok: stok
            };

            return false;
        }
    });

    return found;
}

/* ------------------------------------------------------------------ */
/* ANTI DOUBLE SUBMIT: kunci tombol Simpan Final                       */
/* ------------------------------------------------------------------ */

/** Kunci tombol Simpan Final + tampilkan indikator loading */
function pmkAddLockFinalButton() {
    var $btn = $('#btnFinalSimpanPmk');

    if (!$btn.length) {
        return;
    }

    /* simpan tampilan asli sekali saja agar bisa dikembalikan */
    if (typeof $btn.data('pmkOriginalHtml') === 'undefined') {
        $btn.data('pmkOriginalHtml', $btn.html());
    }

    $btn.prop('disabled', true)
        .addClass('disabled')
        .attr('aria-disabled', 'true')
        .attr('data-pmk-saving', '1')
        .html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
}

/** Buka kembali tombol Simpan Final (dipakai bila proses gagal/batal) */
function pmkAddUnlockFinalButton() {
    var $btn = $('#btnFinalSimpanPmk');

    if (!$btn.length) {
        return;
    }

    var originalHtml = $btn.data('pmkOriginalHtml');

    if (originalHtml) {
        $btn.html(originalHtml);
    }

    $btn.prop('disabled', false)
        .removeClass('disabled')
        .removeAttr('aria-disabled')
        .removeAttr('data-pmk-saving');
}

function pmkAddBindSubmit() {
    var $form = $('#formPemakaianBarang');

    /* Guard submit: hanya submit yang "di-arm" oleh handler Simpan Final
       yang diizinkan. ENTER berulang / submit kedua akan diblokir. */
    $form.off('submit.pmksubmitguard')
        .on('submit.pmksubmitguard', function (e) {
            if (!pmkAddState.submitArmed) {
                e.preventDefault();
                return false;
            }

            pmkAddState.submitArmed = false;   // sekali pakai

            return true;
        });

    $(document).off('click.pmksubmit', '#btnFinalSimpanPmk')
        .on('click.pmksubmit', '#btnFinalSimpanPmk', function (e) {
            e.preventDefault();

            /* cegah double submit: klik kedua diabaikan */
            if (pmkAddState.isSaving || pmkAddState.finalizing) {
                return false;
            }

            pmkAddState.isSaving = true;
            pmkAddState.finalizing = true;

            /* langsung nonaktifkan tombol + tampilkan loading */
            pmkAddLockFinalButton();

            var finish = function () {
                pmkAddState.finalizing = false;
                pmkAddState.isSaving = false;
                pmkAddUnlockFinalButton();
            };

            var submitFinal = function () {
                pmkAddLoadRowsAsync(false)
                    .done(function () {
                        var rows = pmkAddGetValidRows();

                        if (rows.length === 0) {
                            finish();

                            Swal.fire({
                                icon: 'warning',
                                title: 'Tidak Ada Data',
                                text: 'Tidak ada data untuk disimpan. Pastikan minimal 1 baris terisi ID Barang dan Qty.'
                            });

                            return;
                        }

                        /* VALIDASI ULANG STOK: Qty tidak boleh melebihi stok tersedia */
                        var over = pmkAddFirstOverStock(rows);

                        if (over) {
                            finish();

                            Swal.fire({
                                icon: 'error',
                                title: 'Stok Tidak Mencukupi',
                                text: 'Barang ' + over.idbarang + ': Qty ' + pmkAddDec(over.qty, 2)
                                    + ' melebihi stok tersedia ' + pmkAddDec(over.stok, 2) + '.'
                            });

                            return;
                        }

                        /* SEMUA baris valid dikirim (tanpa perlu centang) */
                        $('#pmkSelectedRows').val($.map(rows, function (r) {
                            return r.idurut;
                        }).join(','));

                        $('#pmkFinalRows').val(JSON.stringify(pmkAddBuildDetailPayload(rows)));

                        var doSubmit = function () {
                            pmkAddLog('Simpan Final: submit', rows.length, 'baris');

                            /* pastikan hidden docno terisi walau halaman belum reload */
                            if ($.trim($('#docno').val() || '') === '') {
                                var finalDocno = pmkAddDocno();

                                if (finalDocno !== '') {
                                    $('#docno').val(finalDocno);
                                }
                            }

                            /* izinkan submit satu kali, lalu kunci lagi */
                            pmkAddState.submitArmed = true;
                            pmkAddLockFinalButton();

                            $form.trigger('submit');

                            /* JANGAN panggil finish() di sini: tombol harus tetap
                               disabled + berisi "Menyimpan..." sampai halaman
                               berpindah, agar tidak ada submit kedua. */
                        };

                        if (pmk_ADD_CONFIRM_FINAL) {
                            Swal.fire({
                                icon: 'question',
                                title: 'Simpan Final Data',
                                text: 'Simpan ' + rows.length + ' baris data pemakaian?',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Simpan',
                                cancelButtonText: 'Batal'
                            }).then(function (res) {
                                if (res.isConfirmed) {
                                    doSubmit();
                                } else {
                                    finish();
                                }
                            });

                            return;
                        }

                        doSubmit();
                    })
                    .fail(function () {
                        finish();

                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Gagal memuat data detail.'
                        });
                    });
            };

            /* 1) tunggu simpan yang sedang berjalan, lalu commit baris editor */
            pmkAddWaitForSaving().done(function () {
                pmkAddCommitPendingRows()
                    .done(function () {
                        submitFinal();
                    })
                    .fail(function (arg) {
                        finish();

                        if (arg && arg.status === 'busy') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Mohon Tunggu',
                                text: 'Baris sedang disimpan. Silakan klik Simpan Final lagi.'
                            });
                        }
                    });
            });

            return false;
        });
}

/* ------------------------------------------------------------------ */
/* INISIALISASI                                                        */
/* ------------------------------------------------------------------ */
function pmkAddInit() {
    if (!pmkAddExists()) {
        return;
    }

    /* tombol Input Detail (menggantikan modal) */
    $('#btnAddDetail').off('click').on('click', function (e) {
        e.preventDefault();
        pmkAddNewDraft(true);
    });

    pmkAddBindStatic();
    pmkAddBindSubmit();

    /* Jika halaman di-restore dari bfcache (tombol Back), buka kembali kunci
       tombol Simpan Final agar user bisa menyimpan lagi. */
    $(window).off('pageshow.pmksubmit').on('pageshow.pmksubmit', function (e) {
        if (!e.originalEvent || !e.originalEvent.persisted) {
            return;
        }

        pmkAddState.isSaving = false;
        pmkAddState.finalizing = false;
        pmkAddState.submitArmed = false;
        pmkAddUnlockFinalButton();
    });

    /* Track tombol terakhir: Select2 hanya auto-open bila fokus masuk via TAB */
    $(document).off('keydown.pmkkeytrack').on('keydown.pmkkeytrack', function (e) {
        pmkAddLastKey = e.key || '';
    });

    $(document).off('mousedown.pmkkeytrack touchstart.pmkkeytrack')
        .on('mousedown.pmkkeytrack touchstart.pmkkeytrack', function () {
            pmkAddLastKey = '';
        });

    /* -----------------------------------------------------------------
       SELECT2: setiap dropdown grid terbuka => kolom pencarian LANGSUNG fokus
       (handler global, hanya bereaksi untuk Select2 di dalam #pmkGrid)
    ----------------------------------------------------------------- */
    $(document).off('select2:open.pmkgrid').on('select2:open.pmkgrid', function () {
        pmkAddFocusOpenSearch();
        window.setTimeout(pmkAddFocusOpenSearch, 0);
        window.setTimeout(pmkAddFocusOpenSearch, 80);
    });

    /* ESC / dropdown Select2 grid tertutup -> kembalikan fokus ke elemen Select2
       tersebut (mis. ESC pada Batch/Spec) bila fokus tidak dipindahkan ke kolom lain */
    $(document).off('select2:close.pmkgrid').on('select2:close.pmkgrid', function (e) {
        var $select = $(e.target);

        if (!$select.length || !$select.closest('#pmkGrid').length) {
            return;
        }

        window.setTimeout(function () {
            var active = document.activeElement;

            if (active && active !== document.body) {
                return;   // fokus sudah di kolom berikutnya
            }

            var $container = $select.next('.select2-container');

            if ($container.length) {
                $container.find('.select2-selection').trigger('focus');
            }
        }, 0);
    });

    /* TAB di kolom pencarian Select2 grid => tutup & lanjut kolom kanan */
    $(document).off('keydown.pmksearchtab', '.select2-search__field')
        .on('keydown.pmksearchtab', '.select2-search__field', function (e) {
            if ((e.key !== 'Tab' && e.keyCode !== 9) || e.shiftKey) {
                return;
            }

            var $open = $('#pmkGrid .select2-container--open');

            if (!$open.length) {
                return;   // bukan Select2 milik grid
            }

            var $select = $open.first().prev('select.select2-hidden-accessible');
            var $row = $select.closest('tr');

            if (!$select.length || !$row.length) {
                return;
            }

            e.preventDefault();

            var field = $select.attr('data-field') || '';

            try {
                $select.select2('close');
            } catch (err) {
                /* abaikan */
            }

            pmkAddNext($row, field, false);
        });

    pmkAddLoadLocations(function () {
        pmkAddLoadRows(false);
    });

    /* gudang mengikuti cabang / job */
    $('#cabang').off('change.pmkadd').on('change.pmkadd', function () {
        pmkAddState.locations = [];
        pmkAddState.locationsAll = [];

        pmkAddLoadLocations(function () {
            pmkAddRefreshLocationSelects();
        });
    });

    /* TAB dari Keterangan header langsung masuk ke grid */
    $('#keterangan').off('keydown.pmkadd').on('keydown.pmkadd', function (e) {
        if (e.key === 'Tab' && !e.shiftKey) {
            e.preventDefault();
            pmkAddNewDraft(true);
        }
    });

    /* Shortcut Alt+N = tambah baris baru (sama seperti klik tombol +) */
    $(document).off('keydown.pmknewrow').on('keydown.pmknewrow', function (e) {
        if (!e.altKey) {
            return;
        }

        if (!(e.key === 'n' || e.key === 'N' || e.keyCode === 78)) {
            return;
        }

        if (!$('#pmkGrid').length) {
            return;
        }

        e.preventDefault();
        pmkAddNewDraft(true);
    });

    /* Klik / Enter pada empty state = tambah baris baru (aksi eksplisit user) */
    $('#pmkGrid').off('click.pmkempty', '.pmk-empty-row')
        .on('click.pmkempty', '.pmk-empty-row', function () {
            pmkAddNewDraft(true);
        });

    $('#pmkGrid').off('keydown.pmkempty', '.pmk-empty-row')
        .on('keydown.pmkempty', '.pmk-empty-row', function (e) {
            if (e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32) {
                e.preventDefault();
                pmkAddNewDraft(true);
            }
        });
}

/* Pasang handler Simpan/Batal/ESC lebih awal (tidak menunggu DOM ready)
   supaya tombol X & ESC selalu berfungsi. Guard di dalam fungsi membuat
   pemasangan tetap hanya sekali. */
pmkAddBindGridActions();

jQuery(function () {
    pmkAddInit();
});

/* ------------------------------------------------------------------ */
/* OVERRIDE FUNGSI LAMA (khusus halaman ini)                           */
/* pmk_brng.js tetap dipakai untuk header & halaman list.              */
/* ------------------------------------------------------------------ */
window.updateGrandTotal = function () {
    if ($('#pmkGrid').length > 0) {
        pmkAddRecalcTotal();
    }
};

window.reload_pemakaian_dtl = function () {
    if ($('#pmkGrid').length > 0) {
        /* jangan otomatis membuat baris input baru; pertahankan draft
           yang sedang diisi (jika ada) */
        var $draftNow = $('#pmkGridBody tr.pmk-row-draft[data-mode="new"]').first();
        var keepDraft = $draftNow.length > 0 && pmkAddDraftHasData($draftNow);

        pmkAddLoadRows(false, keepDraft);
    }
};

window.updatepmkBrgDtl = function () {
    pmkAddUpdateSelected();
};

window.btnDeleteDetail = function () {
    pmkAddDeleteChecked();
};

/* Backward compatibility nama fungsi lama (dipakai tombol grid lama) */
window.getCheckedDetailIds = function () {
    return pmkAddCheckedIds();
};

