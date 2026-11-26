/*
 * =========================================================================
 * pnm_brng_add.js  —  KHUSUS HALAMAN INPUT PENERIMAAN BARANG (I.Q.A.6)
 * URL : persediaan/trans/add_pnm_brng_mst
 * -------------------------------------------------------------------------
 * Modul ini menggantikan modal #modalDetailpnm dengan grid inline editing.
 * Hanya di-load oleh app/Views/persediaan/pnm_brng/v_add_pnm_brg.php.
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

var PNM_ADD_FIELDS = ['idlocation', 'idbarang', 'batch', 'unit', 'qty', 'val', 'description'];

var pnmAddState = {
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
var PNM_ADD_CONFIRM_FINAL = false;

/* Menyimpan tombol terakhir yang ditekan: dipakai agar dropdown Select2
   hanya auto-open saat fokus masuk lewat TAB (bukan lewat klik mouse,
   supaya tidak bentrok dengan toggle bawaan Select2). */
var pnmAddLastKey = '';

/* Debug: set true sementara untuk melihat log handler cancel di console. */
var PNM_ADD_DEBUG = false;

function pnmAddLog() {
    if (!PNM_ADD_DEBUG || !window.console || !console.log) {
        return;
    }

    console.log.apply(console, ['[PNM-GRID]'].concat(Array.prototype.slice.call(arguments)));
}

/* ------------------------------------------------------------------ */
/* HELPER                                                              */
/* ------------------------------------------------------------------ */
function pnmAddExists() {
    return $('#pnmGrid').length > 0;
}

function pnmAddEsc(text) {
    return $('<div>').text(text === null || text === undefined ? '' : String(text)).html();
}

function pnmAddNum(value) {
    if (typeof convertToDbNumber === 'function') {
        return Number(convertToDbNumber(value)) || 0;
    }

    return Number(String(value === null || value === undefined ? '' : value).replace(/,/g, '')) || 0;
}

function pnmAddDec(value, dec) {
    var n = pnmAddNum(value);

    return n.toLocaleString('en-US', {
        minimumFractionDigits: dec,
        maximumFractionDigits: dec
    });
}

function pnmAddFormatText(value, dec) {
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

function pnmAddToast(message, icon) {
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
function pnmAddDocno() {
    var docno = $.trim($('#docno').val() || '');

    if (docno !== '') {
        return docno;
    }

    var prefix = $.trim($('#prefix').val() || '').toUpperCase();
    var infix = $.trim($('#infix').val() || '');
    var suffix = $.trim($('#suffix').val() || '').toUpperCase();

    return (prefix && infix && suffix) ? (prefix + '/' + infix + '/' + suffix) : '';
}

function pnmAddLocName(idlocation) {
    var id = $.trim(idlocation || '');
    var name = '';
    var source = (pnmAddState.locationsAll && pnmAddState.locationsAll.length)
        ? pnmAddState.locationsAll
        : pnmAddState.locations;

    $.each(source || [], function (i, loc) {
        if ($.trim(loc.idlocation || '') === id) {
            name = $.trim(loc.nmlocation || id);
            return false;
        }
    });

    return name || id;
}

function pnmAddLocData(selected) {
    var data = [{ id: '', text: '-- Pilih Gudang --' }];
    var sel = $.trim(selected || '');

    $.each(pnmAddState.locations || [], function (i, loc) {
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
function pnmAddLoadLocations(callback) {
    if (pnmAddState.locations.length > 0) {
        if (callback) callback();
        return;
    }

    $.ajax({
        url: HOST_URL + 'persediaan/trans/list_location_pnm_brng',
        type: 'POST',
        dataType: 'json',
        data: { branch: $.trim($('#cabang').val() || '') },
        success: function (res) {
            var items = (res && res.items) ? res.items : [];

            pnmAddState.locationsAll = items.slice();
            pnmAddState.locations = items;

            if (callback) callback();
        },
        error: function () {
            pnmAddState.locationsAll = [];
            pnmAddState.locations = [];

            if (callback) callback();

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Daftar gudang tidak dapat dimuat.'
            });
        }
    });
}

function pnmAddRefreshLocationSelects() {
    $('#pnmGridBody tr.pnm-row-draft').each(function () {
        var $row = $(this);
        var $loc = $row.find('select.pnm-loc');

        if (!$loc.length) {
            return;
        }

        pnmAddInitLocationSelect($loc, $loc.val());
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
function pnmAddLoadRowsAsync(focusDraft, withDraft) {
    var dfd = $.Deferred();

    if (!pnmAddExists()) {
        return dfd.resolve().promise();
    }

    $.ajax({
        url: HOST_URL + 'persediaan/trans/list_pnm_brng_dtl_grid',
        type: 'POST',
        dataType: 'json',
        success: function (res) {
            pnmAddState.rows = (res && res.items) ? res.items : [];
            pnmAddState.loaded = true;

            pnmAddRender({ withDraft: !!withDraft });

            if (focusDraft) {
                pnmAddFocusDraft();
            }

            dfd.resolve();
        },
        error: function () {
            dfd.reject();
        }
    });

    return dfd.promise();
}

function pnmAddLoadRows(focusDraft, withDraft) {
    if (withDraft === undefined) {
        withDraft = !!focusDraft;
    }

    pnmAddLoadRowsAsync(focusDraft, withDraft).fail(function () {
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
function pnmAddRenderEmptyState() {
    var $body = $('#pnmGridBody');

    $body.find('tr.pnm-empty-row').remove();

    if ($body.find('tr').length > 0) {
        return;
    }

    $body.append(
        '<tr class="pnm-empty-row">' +
        '  <td colspan="11" tabindex="0">' +
        '      <i class="fa fa-inbox"></i> Belum ada data detail. ' +
        '      Klik tombol <b>+</b> (atau tekan <b>Alt+N</b>) untuk menambah barang.' +
        '  </td>' +
        '</tr>'
    );
}

/**
 * Render ulang isi grid dari pnmAddState.rows.
 * @param {object} options { withDraft: bool } -> default TANPA baris input baru
 */
function pnmAddRender(options) {
    options = options || {};

    pnmAddRemoveEditors();

    var $body = $('#pnmGridBody');
    $body.empty();

    $.each(pnmAddState.rows, function (i, row) {
        $body.append(pnmAddSavedRow(row));
    });

    if (options.withDraft) {
        pnmAddNewDraft(false);
    }

    pnmAddRenderEmptyState();
    pnmAddRecalcTotal();
    pnmAddSelectionState();
}

function pnmAddSavedRow(row) {
    var idurut = pnmAddEsc(row.idurut);
    var locName = pnmAddLocName(row.idlocation);
    var desc = $.trim(row.description || '');

    return ''
        + '<tr class="pnm-row-saved" data-idurut="' + idurut + '">'
        + '  <td class="pnm-col-no"><input type="checkbox" class="row-check" value="' + idurut + '"></td>'
        + '  <td title="' + pnmAddEsc(row.idlocation + ' - ' + locName) + '">'
        + '      <span class="pnm-readonly-text">' + pnmAddEsc(locName) + '</span></td>'
        + '  <td>' + pnmAddEsc(row.idbarang) + '</td>'
        + '  <td title="' + pnmAddEsc(row.nmbarang) + '">'
        + '      <span class="pnm-readonly-text">' + pnmAddEsc(row.nmbarang) + '</span></td>'
        + '  <td>' + pnmAddEsc(row.batch) + '</td>'
        + '  <td class="text-center">' + pnmAddEsc(row.unit) + '</td>'
        + '  <td class="text-end"><span class="pnm-readonly-number">' + pnmAddDec(row.qty, 2) + '</span></td>'
        + '  <td class="text-end"><span class="pnm-readonly-number">' + pnmAddDec(row.val, 4) + '</span></td>'
        + '  <td class="text-end"><span class="pnm-readonly-number">' + pnmAddDec(row.valsum, 2) + '</span></td>'
        + '  <td title="' + pnmAddEsc(desc) + '">'
        + '      <span class="pnm-readonly-text">' + pnmAddEsc(desc) + '</span></td>'
        + '  <td class="pnm-col-aksi">'
        + '      <button type="button" class="pnm-btn pnm-btn-edit" title="Edit baris">'
        + '          <i class="fa fa-pencil"></i></button>'
        + '      <button type="button" class="pnm-btn pnm-btn-delete" title="Hapus baris">'
        + '          <i class="fa fa-trash"></i></button>'
        + '  </td>'
        + '</tr>';
}


/* ------------------------------------------------------------------ */
/* BARIS EDITOR (draft baru / edit baris tersimpan)                    */
/* ------------------------------------------------------------------ */
function pnmAddEditorRow(data) {
    data = data || {};

    var isEdit = $.trim(String(data.idurut || '')) !== '';

    var $tr = $('<tr class="pnm-row-draft"></tr>')
        .attr('data-idurut', data.idurut || '')
        .attr('data-iduniq', data.iduniq || '')
        .attr('data-mode', isEdit ? 'edit' : 'new')
        .toggleClass('pnm-row-edit', isEdit);

    /* 1. checkbox (baris editor belum bisa dicentang) */
    $tr.append($('<td class="pnm-col-no"></td>').html(isEdit
        ? '<i class="fa fa-pencil-square-o" style="color:#d97706"></i>'
        : '<i class="fa fa-plus-circle" style="color:#16a34a"></i>'));

    /* 2. gudang (select2) */
    $tr.append($('<td class="pnm-cell"></td>').append(
        $('<select class="pnm-loc" data-field="idlocation" style="width:100%"></select>')
    ));

    /* 3. id barang (select2 + pencarian) */
    $tr.append($('<td class="pnm-cell"></td>').append(
        $('<select class="pnm-item" data-field="idbarang" style="width:100%"></select>')
    ));

    /* 4. nama barang (auto-fill, readonly) */
    $tr.append($('<td class="pnm-cell"></td>').append(
        $('<input type="text" class="pnm-cell-input" data-field="nmbarang" readonly tabindex="-1" placeholder="Otomatis">')
            .val(data.nmbarang || '')
    ));

    /* 5. batch / spec (Select2, tergantung ID Barang) */
    $tr.append($('<td class="pnm-cell pnm-cell-batch"></td>').append(
        $('<select class="pnm-batch" data-field="batch" style="width:100%"></select>')
    ));

    /* 6. satuan (dropdown native, diisi dari master satuan barang) */
    $tr.append($('<td class="pnm-cell"></td>').append(
        $('<select class="pnm-cell-select pnm-input" data-field="unit"></select>')
            .append(new Option('-- Pilih --', ''))
    ));

    /* 7. qty */
    $tr.append($('<td class="pnm-cell"></td>').append(
        $('<input type="text" class="pnm-cell-input text-end pnm-num pnm-input" data-field="qty" data-dec="2" placeholder="0.00">')
            .val(data.qty !== undefined && data.qty !== null ? pnmAddDec(data.qty, 2) : '')
    ));

    /* 8. harga */
    $tr.append($('<td class="pnm-cell"></td>').append(
        $('<input type="text" class="pnm-cell-input text-end pnm-num pnm-input" data-field="val" data-dec="4" placeholder="0.0000">')
            .val(data.val !== undefined && data.val !== null ? pnmAddDec(data.val, 4) : '')
    ));

    /* 9. nilai = qty x harga (readonly) */
    $tr.append($('<td class="pnm-cell"></td>').append(
        $('<input type="text" class="pnm-cell-input text-end" data-field="valsum" readonly tabindex="-1" placeholder="0.00">')
            .val(data.valsum !== undefined && data.valsum !== null ? pnmAddDec(data.valsum, 2) : '')
    ));

    /* 10. keterangan */
    $tr.append($('<td class="pnm-cell"></td>').append(
        $('<input type="text" class="pnm-cell-input pnm-input" data-field="description" maxlength="200" placeholder="Keterangan" style="text-transform:uppercase">')
            .val(data.description || '')
    ));

    /* 11. aksi: simpan + batal (X) */
    var $act = $('<td class="pnm-col-aksi"></td>');

    $act.append('<button type="button" class="pnm-btn pnm-btn-save" data-act="save" title="Simpan baris (Enter)">' +
        '<i class="fa fa-check"></i></button>');

    $act.append('<button type="button" class="pnm-btn pnm-btn-cancel" data-act="cancel" title="Batal / tutup baris (X / Esc)">' +
        '<i class="fa fa-times"></i></button>');

    $tr.append($act);

    return $tr;
}

function pnmAddNewDraft(focus) {
    if (!pnmAddExists()) {
        return null;
    }

    /* hapus empty state bila ada */
    $('#pnmGridBody tr.pnm-empty-row').remove();

    var $draft = $('#pnmGridBody tr.pnm-row-draft[data-mode="new"]');

    if ($draft.length) {
        if (focus) {
            pnmAddFocus($draft.last(), 'idlocation');
        }
        return $draft.last();
    }

    var $tr = pnmAddEditorRow({});
    $('#pnmGridBody').append($tr);

    pnmAddSetupSelects($tr, null);
    pnmAddBindRow($tr);
    pnmAddRecalcTotal();
    pnmAddSelectionState();

    if (focus) {
        pnmAddFocus($tr, 'idlocation');
    }

    return $tr;
}

function pnmAddFocusDraft() {
    var $draft = $('#pnmGridBody tr.pnm-row-draft[data-mode="new"]').last();

    if ($draft.length) {
        pnmAddFocus($draft, 'idlocation');
    }
}

function pnmAddRemoveEditors() {
    $('#pnmGridBody tr.pnm-row-draft').each(function () {
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
function pnmAddDropdownParent() {
    var $parent = $('.corp-form-container').first();

    return $parent.length ? $parent : $(document.body);
}

/* ------------------------------------------------------------------ */
/* KEYBOARD SUPPORT SELECT2 (TAB => buka pencarian)                    */
/* ------------------------------------------------------------------ */
/** Ambil search field milik dropdown Select2 yang sedang terbuka */
function pnmAddSelect2SearchField($select) {
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
 * Hanya bekerja untuk Select2 yang berada di dalam #pnmGrid,
 * sehingga tidak mengganggu dropdown modul lain.
 * Dipanggil berulang (0 ms & 80 ms) karena Select2 kadang
 * memindahkan fokus setelah animasi/penutupan dropdown lain.
 */
function pnmAddFocusOpenSearch() {
    var $open = $('#pnmGrid .select2-container--open');

    if (!$open.length) {
        return false;   // dropdown grid tidak sedang terbuka
    }

    var $select = $open.first().prev('select.select2-hidden-accessible');
    var $search = $select.length ? pnmAddSelect2SearchField($select) : $();

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
function pnmAddOpenSelect2($select) {
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
    pnmAddFocusOpenSearch();
    window.setTimeout(pnmAddFocusOpenSearch, 0);
    window.setTimeout(pnmAddFocusOpenSearch, 80);
}

/**
 * Fokus masuk ke Select2 lewat TAB => buka dropdown (search siap diketik).
 * Tidak dijalankan untuk klik mouse agar tidak bentrok dengan toggle Select2.
 */
function pnmAddBindSelect2AutoOpen($tr) {
    $tr.off('focusin.pnmopen', '.select2-selection')
        .on('focusin.pnmopen', '.select2-selection', function () {
            if (pnmAddLastKey !== 'Tab') {
                return;
            }

            var $select = $(this).closest('td').find('select.select2-hidden-accessible').first();

            if ($select.length) {
                pnmAddOpenSelect2($select);
            }
        });
}

/** (re)inisialisasi select2 gudang pada satu baris */
function pnmAddInitLocationSelect($loc, selected) {
    if ($loc.hasClass('select2-hidden-accessible')) {
        $loc.select2('destroy');
    }

    $loc.empty();
    $loc.select2({
        placeholder: 'Pilih Gudang',
        allowClear: true,
        width: '100%',
        dropdownParent: pnmAddDropdownParent(),
        data: pnmAddLocData(selected)
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
function pnmAddInitBatchSelect($tr, opts) {
    opts = opts || {};

    var $batch = $tr.find('select.pnm-batch');

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
        dropdownParent: pnmAddDropdownParent(),
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
            url: HOST_URL + 'persediaan/trans/list_batch_pnm_brng',
            type: 'POST',
            dataType: 'json',
            delay: 200,
            data: function (params) {
                return {
                    idbarang: $.trim($tr.find('select[data-field="idbarang"]').val() || ''),
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

    /* ENTER / pilih batch -> lanjut ke kolom Satuan */
    $batch.off('.pnmbatch').on('select2:select.pnmbatch', function () {
        pnmAddFocus($tr, 'unit');
    });
}

function pnmAddSetupSelects($tr, data) {
    var $loc = $tr.find('select.pnm-loc');
    var $item = $tr.find('select.pnm-item');
    var $unit = $tr.find('select.pnm-unit, select[data-field="unit"]');

    /* ---------------- GUDANG (select2) ---------------- */
    pnmAddInitLocationSelect($loc, data && data.idlocation ? $.trim(data.idlocation) : '');

    /* TAB masuk -> dropdown + search langsung aktif */
    pnmAddBindSelect2AutoOpen($tr);

    $loc.off('.pnmloc').on('select2:select.pnmloc', function () {
        if ($item.val()) {
            /* item mengikuti gudang: reset bila gudang diganti */
            $item.val(null).trigger('change');

            $tr.find('[data-field="nmbarang"]').val('');
            pnmAddFillUnits($tr, [], '');
            pnmAddInitBatchSelect($tr, { enabled: false });   // batch ikut reset
        }

        pnmAddFocus($tr, 'idbarang');
    });

    /* ---------------- ID BARANG (select2 + ajax) ---------------- */
    $item.select2({
        placeholder: 'Cari ID / Nama Barang',
        allowClear: true,
        width: '100%',
        dropdownParent: pnmAddDropdownParent(),
        minimumInputLength: 0,
        ajax: {
            url: HOST_URL + 'persediaan/trans/search_item_pnm_brng',
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

            return '<div><b>' + pnmAddEsc(repo.idbarang || repo.id) + '</b>' +
                ' <i class="fa fa-circle-o"></i> ' +
                pnmAddEsc(repo.nmbarang || repo.text) + '</div>';
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

    $item.off('.pnmitem').on('select2:select.pnmitem', function (e) {
        var item = e.params.data || {};

        $tr.find('[data-field="nmbarang"]').val(item.nmbarang || '');

        /* ID Barang berubah -> Batch/Spec direset & diaktifkan kembali */
        pnmAddInitBatchSelect($tr, { enabled: true });

        pnmAddLoadItemInfo($tr, item.idbarang);
        pnmAddFocus($tr, 'batch');
    });

    $item.on('select2:clear.pnmitem', function () {
        $tr.find('[data-field="nmbarang"]').val('');
        pnmAddFillUnits($tr, [], '');
        pnmAddInitBatchSelect($tr, { enabled: false });   // tanpa barang -> batch disabled
    });

    /* ---------------- BATCH / SPEC (select2, tergantung ID Barang) ---------------- */
    pnmAddInitBatchSelect($tr, {
        enabled: !!(data && data.idbarang),
        selected: (data && data.batch) ? $.trim(data.batch) : ''
    });

    /* ---------------- SATUAN (dropdown native) ---------------- */
    if (data && data.idbarang) {
        pnmAddLoadItemInfo($tr, data.idbarang, true);
    }

    $unit.off('.pnmunit').on('change.pnmunit', function () {
        pnmAddFocus($tr, 'qty');
    });
}


/* ------------------------------------------------------------------ */
/* AUTO-FILL: NAMA BARANG, SATUAN, HARGA (endpoint modul ini)          */
/* ------------------------------------------------------------------ */
function pnmAddFillUnits($tr, units, selected) {
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

function pnmAddLoadItemInfo($tr, idbarang, preservePrice) {
    idbarang = $.trim(idbarang || '');

    if (idbarang === '') {
        return;
    }

    $.ajax({
        url: HOST_URL + 'persediaan/trans/get_item_pnm_brng',
        type: 'POST',
        dataType: 'json',
        data: { idbarang: idbarang },
        success: function (res) {
            if (!res || !res.success || !res.data) {
                return;
            }

            var item = res.data;

            $tr.find('[data-field="nmbarang"]').val(item.nmbarang || '');
            pnmAddFillUnits($tr, res.units || [], item.unit || '');

            if (!preservePrice) {
                var $val = $tr.find('[data-field="val"]');

                if ($.trim($val.val() || '') === '' || pnmAddNum($val.val()) === 0) {
                    $val.val(pnmAddDec(item.lastprice || 0, 4));
                }
            }

            pnmAddRecalcRow($tr);
            pnmAddRecalcTotal();
        }
    });
}

/* ------------------------------------------------------------------ */
/* NILAI BARIS & TOTAL                                                 */
/* ------------------------------------------------------------------ */
function pnmAddCollect($tr) {
    return {
        idurut: $.trim($tr.attr('data-idurut') || ''),
        iduniq: $.trim($tr.attr('data-iduniq') || ''),
        idlocation: $.trim($tr.find('select[data-field="idlocation"]').val() || ''),
        idbarang: $.trim($tr.find('select[data-field="idbarang"]').val() || ''),
        nmbarang: $.trim($tr.find('[data-field="nmbarang"]').val() || ''),
        batch: $.trim($tr.find('[data-field="batch"]').val() || '').toUpperCase(),
        unit: $.trim($tr.find('[data-field="unit"]').val() || ''),
        qty: pnmAddNum($tr.find('[data-field="qty"]').val()),
        val: pnmAddNum($tr.find('[data-field="val"]').val()),
        description: $.trim($tr.find('[data-field="description"]').val() || '').toUpperCase()
    };
}

function pnmAddRecalcRow($tr) {
    if (!$tr || !$tr.length) {
        return { qty: 0, val: 0, valsum: 0 };
    }

    var d = pnmAddCollect($tr);
    var valsum = Number((d.qty * d.val).toFixed(4));

    $tr.find('[data-field="valsum"]').val(valsum > 0 ? pnmAddDec(valsum, 2) : '');

    return { qty: d.qty, val: d.val, valsum: valsum };
}

function pnmAddRecalcTotal() {
    var totalQty = 0;
    var totalNilai = 0;

    $.each(pnmAddState.rows, function (i, row) {
        totalQty += pnmAddNum(row.qty);
        totalNilai += pnmAddNum(row.valsum);
    });

    $('#pnmGridBody tr.pnm-row-draft[data-mode="new"]').each(function () {
        var hitung = pnmAddRecalcRow($(this));

        totalQty += hitung.qty;
        totalNilai += hitung.valsum;
    });

    $('#totalQty').text(pnmAddDec(totalQty, 2));
    $('#totalNilai').text(pnmAddDec(totalNilai, 2));
}

/* ------------------------------------------------------------------ */
/* FOKUS & NAVIGASI KEYBOARD                                           */
/* ------------------------------------------------------------------ */
function pnmAddFocus($tr, field) {
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
        pnmAddOpenSelect2($el);
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

function pnmAddNext($tr, field, backward) {
    var idx = PNM_ADD_FIELDS.indexOf(field);
    var next;

    if (idx < 0) {
        return;
    }

    next = backward ? PNM_ADD_FIELDS[idx - 1] : PNM_ADD_FIELDS[idx + 1];

    if (next) {
        pnmAddFocus($tr, next);
        return;
    }

    pnmAddLastField($tr);
}

function pnmAddLastField($tr) {
    var d = pnmAddCollect($tr);
    var hasData = (d.idlocation !== '' || d.idbarang !== '' || d.batch !== '' ||
        d.qty > 0 || d.val > 0 || d.description !== '');

    if (!hasData) {
        pnmAddFocus($tr, 'idlocation');
        return;
    }

    pnmAddSave($tr, true);
}


/* ------------------------------------------------------------------ */
/* EVENT PER BARIS (TAB / ENTER / FORMAT ANGKA)                        */
/* ------------------------------------------------------------------ */
function pnmAddBindRow($tr) {
    /* format angka saat mengetik qty & harga */
    $tr.find('.pnm-num').off('.pnmnum').on('input.pnmnum', function () {
        var dec = parseInt($(this).attr('data-dec'), 10);
        var el = this;

        if (isNaN(dec)) {
            dec = 2;
        }

        if (el.selectionStart === null || el.selectionStart === (el.value || '').length) {
            el.value = pnmAddFormatText(el.value, dec);
        }

        pnmAddRecalcRow($tr);
        pnmAddRecalcTotal();
    }).on('blur.pnmnum', function () {
        var dec = parseInt($(this).attr('data-dec'), 10);

        if (isNaN(dec)) {
            dec = 2;
        }

        this.value = pnmAddFormatText(this.value, dec);
        pnmAddRecalcRow($tr);
        pnmAddRecalcTotal();
    });

    /* ENTER = kolom berikutnya; ENTER di kolom terakhir = simpan baris
       (ESC ditangani global lewat delegation di pnmAddBindStatic) */
    $tr.find('.pnm-input').off('.pnmkey').on('keydown.pnmkey', function (e) {
        if (e.key !== 'Enter' && e.keyCode !== 13) {
            return;
        }

        e.preventDefault();

        var field = $(this).attr('data-field') || '';

        if (field === 'description') {
            pnmAddLastField($tr);
            return;
        }

        pnmAddNext($tr, field, e.shiftKey);
    });

    /* TAB di kolom terakhir = simpan baris + tambah baris baru */
    $tr.find('[data-field="description"]').off('.pnmtab').on('keydown.pnmtab', function (e) {
        if ((e.key === 'Tab' || e.keyCode === 9) && !e.shiftKey) {
            e.preventDefault();
            pnmAddLastField($tr);
        }
    });

    /* Tandai navigasi TAB dari dalam baris (untuk auto-open Select2) */
    $tr.off('keydown.pnmnavtrack').on('keydown.pnmnavtrack', function (e) {
        if (e.key === 'Tab' || e.keyCode === 9) {
            pnmAddLastKey = 'Tab';
        }
    });

    /* Tombol Simpan & Batal (X) TIDAK di-bind di sini.
       Keduanya memakai event delegation global di pnmAddBindStatic()
       agar tetap bekerja untuk baris yang dibuat/di-render ulang. */
}

/* ------------------------------------------------------------------ */
/* BATAL BARIS (tombol X / Esc)                                        */
/* ------------------------------------------------------------------ */
/** Fokuskan baris tersimpan (checkbox) agar navigasi keyboard tetap jalan */
function pnmAddFocusRow($row) {
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

    $row.find('.pnm-btn-edit').first().trigger('focus');
}

/**
 * Lepas Select2 dari semua <select> dalam satu baris (sebelum baris dihapus).
 */
function pnmAddDestroyRowSelects($tr) {
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
function pnmAddRevertEditRow($tr) {
    var idurut = $.trim($tr.attr('data-idurut') || '');
    var row = null;

    $.each(pnmAddState.rows, function (i, r) {
        if (String(r.idurut) === String(idurut)) {
            row = r;
            return false;
        }
    });

    if (!row) {
        return false;
    }

    pnmAddDestroyRowSelects($tr);

    var $saved = $(pnmAddSavedRow(row));
    $tr.replaceWith($saved);

    pnmAddRecalcTotal();
    pnmAddSelectionState();
    pnmAddFocusRow($saved);

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
function pnmAddCancelRow($tr) {
    if (!$tr || !$tr.length) {
        pnmAddLog('cancel: baris tidak ditemukan');
        return false;
    }

    try {
        var mode = String($tr.attr('data-mode') || '');
        var $prevSaved = $tr.prevAll('tr.pnm-row-saved').first();

        /* hanya baris editor (new/edit) yang bisa dibatalkan */
        if (mode !== 'edit' && mode !== 'new') {
            return false;
        }

        pnmAddLog('cancel: mode =', mode, 'idurut =', $tr.attr('data-idurut'));

        /* ---------- BARIS EDIT: revert nilai, baris tetap ada ---------- */
        if (mode === 'edit') {
            if (pnmAddRevertEditRow($tr)) {
                pnmAddToast('Perubahan baris dibatalkan', 'info');
                pnmAddLog('cancel: baris edit di-revert');
                return true;
            }

            /* data asli tidak ketemu -> fallback render ulang */
            pnmAddRender();
            pnmAddLog('cancel: fallback render untuk baris edit');
            return true;
        }

        /* ---------- BARIS BARU: hapus elemen <tr> dari DOM ---------- */
        /* tutup dropdown Select2 grid yang mungkin sedang terbuka */
        $('#pnmGrid .select2-container--open').each(function () {
            var $select = $(this).prev('select.select2-hidden-accessible');

            if ($select.length) {
                try {
                    $select.select2('close');
                } catch (err) {
                    /* abaikan */
                }
            }
        });

        pnmAddDestroyRowSelects($tr);

        $tr.remove();   // <-- baris baru benar-benar dihapus dari DOM

        pnmAddLog('cancel: baris baru dihapus, sisa baris =', $('#pnmGridBody tr').length);

        pnmAddToast('Baris baru dibatalkan', 'info');

        /* Baris kosong TIDAK dibuat otomatis.
           Bila tabel menjadi kosong, tampilkan empty state. */
        pnmAddRenderEmptyState();

        pnmAddRecalcTotal();
        pnmAddSelectionState();

        /* fokus ke baris sebelumnya (valid), atau ke empty state */
        if ($prevSaved.length) {
            pnmAddFocusRow($prevSaved);
        } else {
            var $lastRow = $('#pnmGridBody tr.pnm-row-saved').last();

            if ($lastRow.length) {
                pnmAddFocusRow($lastRow);
            } else {
                $('#pnmGridBody tr.pnm-empty-row td').first().trigger('focus');
            }
        }

        return true;
    } catch (err) {
        console.error('pnmAddCancelRow gagal:', err);
        return false;
    }
}

/* ------------------------------------------------------------------ */
/* VALIDASI & SIMPAN BARIS                                             */
/* ------------------------------------------------------------------ */
function pnmAddInvalid($tr, field, message) {
    var $el = $tr.find('[data-field="' + field + '"]');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('open');
    } else {
        $el.addClass('pnm-invalid');

        window.setTimeout(function () {
            $el.removeClass('pnm-invalid');
        }, 1800);

        pnmAddFocus($tr, field);
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
function pnmAddValidateRow($tr) {
    var d = pnmAddCollect($tr);

    if (!pnmAddDocno()) {
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
function pnmAddSaveRow($tr, options) {
    options = options || {};

    var dfd = $.Deferred();

    if (pnmAddState.saving) {
        return dfd.reject({ status: 'busy' }).promise();
    }

    if (!$tr || !$tr.length) {
        return dfd.reject({ status: 'norow' }).promise();
    }

    var check = pnmAddValidateRow($tr);

    if (!check.ok) {
        if (check.field) {
            pnmAddInvalid($tr, check.field, check.message);
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
        docno: pnmAddDocno(),
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
        qtystock: 0,
        batch: d.batch,
        qty: d.qty,
        val: d.val,
        valsum: Number((d.qty * d.val).toFixed(4))
    };

    pnmAddState.saving = true;
    $tr.addClass('pnm-row-busy');

    $.ajax({
        url: HOST_URL + 'persediaan/trans/save_pnm_brng_detail',
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function (res) {
            pnmAddState.saving = false;
            $tr.removeClass('pnm-row-busy');

            if (!res || !res.success) {
                Swal.fire({
                    icon: 'warning',
                    title: (res && res.code === 'COA') ? 'COA Belum Lengkap' : 'Gagal',
                    text: (res && res.message) ? res.message : 'Data detail gagal disimpan.'
                });

                dfd.reject({ status: 'server', res: res });
                return;
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

                pnmAddLoadRows(isNewRow, isNewRow);
            }

            if (!options.silent) {
                pnmAddToast('Baris ' + d.idbarang + ' tersimpan');
            }

            dfd.resolve({ res: res, data: d });
        },
        error: function (xhr) {
            pnmAddState.saving = false;
            $tr.removeClass('pnm-row-busy');

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
function pnmAddSave($tr, silent) {
    return pnmAddSaveRow($tr, { silent: !!silent });
}


/* ------------------------------------------------------------------ */
/* EDIT INLINE, HAPUS (SATU / BULK), STATE CHECKBOX                    */
/* ------------------------------------------------------------------ */
function pnmAddCheckedIds() {
    return $('#pnmGridBody tr.pnm-row-saved .row-check:checked').map(function () {
        return $(this).val();
    }).get();
}

function pnmAddSelectionState() {
    var total = $('#pnmGridBody tr.pnm-row-saved').length;
    var checked = $('#pnmGridBody tr.pnm-row-saved .row-check:checked').length;
    var editing = $('#pnmGridBody tr[data-mode="edit"]').length;

    $('#checkAll').prop('checked', total > 0 && checked === total);
    $('#btnUpdateDetail').prop('disabled', !(checked === 1 || editing > 0));

    /* Tombol hapus tetap bisa diklik saat ada baris tapi belum dicentang,
       supaya muncul pesan "Pilih minimal 1 baris untuk dihapus." */
    $('#btnDeleteDetail').prop('disabled', total === 0);

    $('#pnmSelectedRows').val(pnmAddCheckedIds().join(','));

    $('#pnmSelectedInfo').text(checked + ' dari ' + total + ' baris dipilih');
}

function pnmAddEditSelected() {
    var ids = pnmAddCheckedIds();

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

    pnmAddEditRow(ids[0]);
}

/** Tombol #btnUpdateDetail: simpan baris yang sedang diedit,
 *  atau masuk mode edit untuk baris yang dicentang. */
function pnmAddUpdateSelected() {
    var $editing = $('#pnmGridBody tr[data-mode="edit"]').first();

    if ($editing.length) {
        pnmAddSave($editing, false);
        return;
    }

    pnmAddEditSelected();
}

/** Cek apakah baris draft berisi data (untuk mencegah data hilang) */
function pnmAddDraftHasData($draft) {
    if (!$draft || !$draft.length) {
        return false;
    }

    var d = pnmAddCollect($draft);

    return (d.idlocation !== '' || d.idbarang !== '' || d.batch !== '' ||
        d.qty > 0 || d.val > 0 || d.description !== '');
}

function pnmAddEditRow(idurut) {
    var $draft = $('#pnmGridBody tr.pnm-row-draft[data-mode="new"]').first();

    if ($draft.length && pnmAddDraftHasData($draft)) {
        Swal.fire({
            icon: 'question',
            title: 'Baris Baru Belum Disimpan',
            text: 'Isi baris baru yang sedang diketik akan dibuang. Lanjutkan edit baris tersimpan?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then(function (res) {
            if (res.isConfirmed) {
                pnmAddEditRowNow(idurut);
            }
        });

        return;
    }

    pnmAddEditRowNow(idurut);
}

function pnmAddEditRowNow(idurut) {
    var row = null;

    $.each(pnmAddState.rows, function (i, r) {
        if (String(r.idurut) === String(idurut)) {
            row = r;
            return false;
        }
    });

    if (!row) {
        return;
    }

    var $target = $('#pnmGridBody tr.pnm-row-saved[data-idurut="' + idurut + '"]');

    pnmAddRemoveEditors();

    var $editor = pnmAddEditorRow(row).addClass('pnm-row-edit').attr('data-mode', 'edit');

    if ($target.length) {
        $target.replaceWith($editor);
    } else {
        $('#pnmGridBody').append($editor);
    }

    pnmAddSetupSelects($editor, row);
    pnmAddBindRow($editor);
    pnmAddRecalcRow($editor);
    pnmAddSelectionState();
    pnmAddFocus($editor, 'batch');
}

function pnmAddDeleteRow(idurut) {
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

        pnmAddDeleteIds([idurut]);
    });
}

function pnmAddDeleteChecked() {
    var ids = pnmAddCheckedIds();

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
            pnmAddDeleteIds(ids);
        }
    });
}

function pnmAddDeleteIds(ids) {
    /* jaga baris input yang sedang diisi (jika ada) agar tidak hilang saat reload */
    var $draftNow = $('#pnmGridBody tr.pnm-row-draft[data-mode="new"]').first();
    var keepDraft = $draftNow.length > 0 && pnmAddDraftHasData($draftNow);

    $.ajax({
        url: HOST_URL + 'persediaan/trans/delete_pnm_brng',
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

                pnmAddState.rows = $.grep(pnmAddState.rows || [], function (row) {
                    return !idSet[String(row.idurut).trim()];
                });

                /* 2) hapus elemen <tr> dari DOM */
                $.each(ids, function (i, id) {
                    $('#pnmGridBody tr.pnm-row-saved[data-idurut="' + String(id).trim() + '"]').remove();
                });

                /* 3) reset Select All + hitung ulang total & empty state */
                $('#checkAll').prop('checked', false);
                pnmAddRecalcTotal();
                pnmAddSelectionState();
                pnmAddRenderEmptyState();

                pnmAddToast('Data detail dihapus');

                /* 4) sinkronkan ulang dengan server */
                pnmAddLoadRows(false, keepDraft);
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
 * Hanya dipasang sekali (guard window.pnmAddActionsBound).
 */
function pnmAddBindGridActions() {
    if (window.pnmAddActionsBound) {
        pnmAddLog('actions: sudah terpasang, skip');
        return;
    }

    window.pnmAddActionsBound = true;

    pnmAddLog('actions: handler terpasang');

    /* ---------------- KLIK: SIMPAN / BATAL ---------------- */
    document.addEventListener('click', function (ev) {
        var target = ev.target;

        if (!target || typeof target.closest !== 'function') {
            return;
        }

        var cancelBtn = target.closest('#pnmGrid [data-act="cancel"]');

        if (cancelBtn) {
            ev.preventDefault();
            ev.stopPropagation();

            var $row = $(cancelBtn).closest('tr');

            pnmAddLog('klik X -> mode =', $row.length ? $row.attr('data-mode') : '(row hilang)');

            if ($row.length && $.contains(document, $row[0])) {
                pnmAddCancelRow($row);
            }

            return;
        }

        var saveBtn = target.closest('#pnmGrid [data-act="save"]');

        if (saveBtn) {
            ev.preventDefault();
            ev.stopPropagation();

            var $saveRow = $(saveBtn).closest('tr');

            if ($saveRow.length && $.contains(document, $saveRow[0])) {
                pnmAddSave($saveRow, false);
            }
        }
    }, true);

    /* ---------------- MOUSEDOWN X: tutup dropdown grid ---------------- */
    document.addEventListener('mousedown', function (ev) {
        var target = ev.target;

        if (!target || typeof target.closest !== 'function' ||
            !target.closest('#pnmGrid [data-act="cancel"]')) {
            return;
        }

        $('#pnmGrid .select2-container--open').each(function () {
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

        var row = active.closest('#pnmGridBody tr.pnm-row-draft');

        /* fokus di luar baris (mis. kotak search Select2) -> biarkan
           Select2 yang menutup dropdown-nya sendiri */
        if (!row || !document.body.contains(row)) {
            return;
        }

        ev.preventDefault();

        pnmAddLog('ESC -> batalkan baris mode =', row.getAttribute('data-mode'));

        pnmAddCancelRow($(row));
    }, true);
}

/* ------------------------------------------------------------------ */
/* EVENT STATIS GRID (delegation)                                      */
/* ------------------------------------------------------------------ */
function pnmAddBindStatic() {
    var $grid = $('#pnmGrid');

    $grid.off('.pnmstatic');

    $grid.on('click.pnmstatic', '.pnm-btn-edit', function (e) {
        e.preventDefault();
        pnmAddEditRow($(this).closest('tr').attr('data-idurut'));
    });

    $grid.on('click.pnmstatic', '.pnm-btn-delete', function (e) {
        e.preventDefault();
        pnmAddDeleteRow($(this).closest('tr').attr('data-idurut'));
    });

    /* Tombol Hapus baris di header card.
       Memakai event delegation (BUKAN inline onclick) karena elemen tombol
       memiliki id="btnDeleteDetail" sehingga inline onclick="btnDeleteDetail()"
       akan resolve ke elemen itu sendiri, bukan ke fungsi global. */
    $(document).off('click.pnmdelete', '#btnDeleteDetail')
        .on('click.pnmdelete', '#btnDeleteDetail', function (e) {
            e.preventDefault();
            e.stopPropagation();
            pnmAddDeleteChecked();
        });

    /* Tombol Simpan (✓), Batal (X) dan tombol ESC ditangani oleh
       pnmAddBindGridActions() memakai native event capture. */
    pnmAddBindGridActions();

    $grid.on('change.pnmstatic', '#checkAll', function () {
        var checked = this.checked;

        $('#pnmGridBody .row-check').prop('checked', checked);
        $('#pnmGridBody tr.pnm-row-saved').toggleClass('pnm-row-checked', checked);
        pnmAddSelectionState();
    });

    $grid.on('change.pnmstatic', '.row-check', function () {
        $(this).closest('tr').toggleClass('pnm-row-checked', this.checked);
        pnmAddSelectionState();
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
function pnmAddWaitForSaving() {
    var dfd = $.Deferred();
    var tries = 0;

    var check = function () {
        if (!pnmAddState.saving || tries >= 60) {   // maksimal ±6 detik
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
function pnmAddGetPendingRows() {
    var rows = [];

    $('#pnmGridBody tr.pnm-row-draft').each(function () {
        var $tr = $(this);
        var mode = String($tr.attr('data-mode') || '');

        if (mode === 'edit' || pnmAddDraftHasData($tr)) {
            rows.push($tr);
        }
    });

    return rows;
}

/** Simpan semua baris editor yang tertunda secara berurutan */
function pnmAddCommitPendingRows() {
    var dfd = $.Deferred();
    var rows = pnmAddGetPendingRows();
    var i = 0;

    var next = function () {
        if (i >= rows.length) {
            dfd.resolve();
            return;
        }

        var $row = rows[i++];

        pnmAddSaveRow($row, {
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
function pnmAddGetValidRows() {
    return $.grep(pnmAddState.rows || [], function (row) {
        return $.trim(row.idbarang || '') !== ''
            && $.trim(row.idlocation || '') !== ''
            && $.trim(row.unit || '') !== ''
            && pnmAddNum(row.qty) > 0;
    });
}

/** Payload JSON baris valid yang dikirim ke controller */
function pnmAddBuildDetailPayload(rows) {
    return $.map(rows, function (row) {
        return {
            idurut: row.idurut,
            idbarang: $.trim(row.idbarang || ''),
            nmbarang: $.trim(row.nmbarang || ''),
            idlocation: $.trim(row.idlocation || ''),
            batch: $.trim(row.batch || ''),
            unit: $.trim(row.unit || ''),
            qty: pnmAddNum(row.qty),
            val: pnmAddNum(row.val),
            valsum: pnmAddNum(row.valsum),
            description: $.trim(row.description || '')
        };
    });
}

/* ------------------------------------------------------------------ */
/* ANTI DOUBLE SUBMIT: kunci tombol Simpan Final                       */
/* ------------------------------------------------------------------ */

/** Kunci tombol Simpan Final + tampilkan indikator loading */
function pnmAddLockFinalButton() {
    var $btn = $('#btnFinalSimpanPnm');

    if (!$btn.length) {
        return;
    }

    /* simpan tampilan asli sekali saja agar bisa dikembalikan */
    if (typeof $btn.data('pnmOriginalHtml') === 'undefined') {
        $btn.data('pnmOriginalHtml', $btn.html());
    }

    $btn.prop('disabled', true)
        .addClass('disabled')
        .attr('aria-disabled', 'true')
        .attr('data-pnm-saving', '1')
        .html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
}

/** Buka kembali tombol Simpan Final (dipakai bila proses gagal/batal) */
function pnmAddUnlockFinalButton() {
    var $btn = $('#btnFinalSimpanPnm');

    if (!$btn.length) {
        return;
    }

    var originalHtml = $btn.data('pnmOriginalHtml');

    if (originalHtml) {
        $btn.html(originalHtml);
    }

    $btn.prop('disabled', false)
        .removeClass('disabled')
        .removeAttr('aria-disabled')
        .removeAttr('data-pnm-saving');
}

function pnmAddBindSubmit() {
    var $form = $('#formPenerimaanBarang');

    /* Guard submit: hanya submit yang "di-arm" oleh handler Simpan Final
       yang diizinkan. ENTER berulang / submit kedua akan diblokir. */
    $form.off('submit.pnmsubmitguard')
        .on('submit.pnmsubmitguard', function (e) {
            if (!pnmAddState.submitArmed) {
                e.preventDefault();
                return false;
            }

            pnmAddState.submitArmed = false;   // sekali pakai

            return true;
        });

    $(document).off('click.pnmsubmit', '#btnFinalSimpanPnm')
        .on('click.pnmsubmit', '#btnFinalSimpanPnm', function (e) {
            e.preventDefault();

            /* cegah double submit: klik kedua diabaikan */
            if (pnmAddState.isSaving || pnmAddState.finalizing) {
                return false;
            }

            pnmAddState.isSaving = true;
            pnmAddState.finalizing = true;

            /* langsung nonaktifkan tombol + tampilkan loading */
            pnmAddLockFinalButton();

            var finish = function () {
                pnmAddState.finalizing = false;
                pnmAddState.isSaving = false;
                pnmAddUnlockFinalButton();
            };

            var submitFinal = function () {
                pnmAddLoadRowsAsync(false)
                    .done(function () {
                        var rows = pnmAddGetValidRows();

                        if (rows.length === 0) {
                            finish();

                            Swal.fire({
                                icon: 'warning',
                                title: 'Tidak Ada Data',
                                text: 'Tidak ada data untuk disimpan. Pastikan minimal 1 baris terisi ID Barang dan Qty.'
                            });

                            return;
                        }

                        /* SEMUA baris valid dikirim (tanpa perlu centang) */
                        $('#pnmSelectedRows').val($.map(rows, function (r) {
                            return r.idurut;
                        }).join(','));

                        $('#pnmFinalRows').val(JSON.stringify(pnmAddBuildDetailPayload(rows)));

                        var doSubmit = function () {
                            pnmAddLog('Simpan Final: submit', rows.length, 'baris');

                            /* izinkan submit satu kali, lalu kunci lagi */
                            pnmAddState.submitArmed = true;
                            pnmAddLockFinalButton();

                            $form.trigger('submit');

                            /* JANGAN panggil finish() di sini: tombol harus tetap
                               disabled + berisi "Menyimpan..." sampai halaman
                               berpindah, agar tidak ada submit kedua. */
                        };

                        if (PNM_ADD_CONFIRM_FINAL) {
                            Swal.fire({
                                icon: 'question',
                                title: 'Simpan Final Data',
                                text: 'Simpan ' + rows.length + ' baris data penerimaan?',
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
            pnmAddWaitForSaving().done(function () {
                pnmAddCommitPendingRows()
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
function pnmAddInit() {
    if (!pnmAddExists()) {
        return;
    }

    /* tombol Input Detail (menggantikan modal) */
    $('#btnAddDetail').off('click').on('click', function (e) {
        e.preventDefault();
        pnmAddNewDraft(true);
    });

    pnmAddBindStatic();
    pnmAddBindSubmit();

    /* Jika halaman di-restore dari bfcache (tombol Back), buka kembali kunci
       tombol Simpan Final agar user bisa menyimpan lagi. */
    $(window).off('pageshow.pnmsubmit').on('pageshow.pnmsubmit', function (e) {
        if (!e.originalEvent || !e.originalEvent.persisted) {
            return;
        }

        pnmAddState.isSaving = false;
        pnmAddState.finalizing = false;
        pnmAddState.submitArmed = false;
        pnmAddUnlockFinalButton();
    });

    /* Track tombol terakhir: Select2 hanya auto-open bila fokus masuk via TAB */
    $(document).off('keydown.pnmkeytrack').on('keydown.pnmkeytrack', function (e) {
        pnmAddLastKey = e.key || '';
    });

    $(document).off('mousedown.pnmkeytrack touchstart.pnmkeytrack')
        .on('mousedown.pnmkeytrack touchstart.pnmkeytrack', function () {
            pnmAddLastKey = '';
        });

    /* -----------------------------------------------------------------
       SELECT2: setiap dropdown grid terbuka => kolom pencarian LANGSUNG fokus
       (handler global, hanya bereaksi untuk Select2 di dalam #pnmGrid)
    ----------------------------------------------------------------- */
    $(document).off('select2:open.pnmgrid').on('select2:open.pnmgrid', function () {
        pnmAddFocusOpenSearch();
        window.setTimeout(pnmAddFocusOpenSearch, 0);
        window.setTimeout(pnmAddFocusOpenSearch, 80);
    });

    /* ESC / dropdown Select2 grid tertutup -> kembalikan fokus ke elemen Select2
       tersebut (mis. ESC pada Batch/Spec) bila fokus tidak dipindahkan ke kolom lain */
    $(document).off('select2:close.pnmgrid').on('select2:close.pnmgrid', function (e) {
        var $select = $(e.target);

        if (!$select.length || !$select.closest('#pnmGrid').length) {
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
    $(document).off('keydown.pnmsearchtab', '.select2-search__field')
        .on('keydown.pnmsearchtab', '.select2-search__field', function (e) {
            if ((e.key !== 'Tab' && e.keyCode !== 9) || e.shiftKey) {
                return;
            }

            var $open = $('#pnmGrid .select2-container--open');

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

            pnmAddNext($row, field, false);
        });

    pnmAddLoadLocations(function () {
        pnmAddLoadRows(false);
    });

    /* gudang mengikuti cabang / job */
    $('#cabang').off('change.pnmadd').on('change.pnmadd', function () {
        pnmAddState.locations = [];
        pnmAddState.locationsAll = [];

        pnmAddLoadLocations(function () {
            pnmAddRefreshLocationSelects();
        });
    });

    /* TAB dari Keterangan header langsung masuk ke grid */
    $('#keterangan').off('keydown.pnmadd').on('keydown.pnmadd', function (e) {
        if (e.key === 'Tab' && !e.shiftKey) {
            e.preventDefault();
            pnmAddNewDraft(true);
        }
    });

    /* Shortcut Alt+N = tambah baris baru (sama seperti klik tombol +) */
    $(document).off('keydown.pnmnewrow').on('keydown.pnmnewrow', function (e) {
        if (!e.altKey) {
            return;
        }

        if (!(e.key === 'n' || e.key === 'N' || e.keyCode === 78)) {
            return;
        }

        if (!$('#pnmGrid').length) {
            return;
        }

        e.preventDefault();
        pnmAddNewDraft(true);
    });

    /* Klik / Enter pada empty state = tambah baris baru (aksi eksplisit user) */
    $('#pnmGrid').off('click.pnmempty', '.pnm-empty-row')
        .on('click.pnmempty', '.pnm-empty-row', function () {
            pnmAddNewDraft(true);
        });

    $('#pnmGrid').off('keydown.pnmempty', '.pnm-empty-row')
        .on('keydown.pnmempty', '.pnm-empty-row', function (e) {
            if (e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32) {
                e.preventDefault();
                pnmAddNewDraft(true);
            }
        });
}

/* Pasang handler Simpan/Batal/ESC lebih awal (tidak menunggu DOM ready)
   supaya tombol X & ESC selalu berfungsi. Guard di dalam fungsi membuat
   pemasangan tetap hanya sekali. */
pnmAddBindGridActions();

jQuery(function () {
    pnmAddInit();
});

/* ------------------------------------------------------------------ */
/* OVERRIDE FUNGSI LAMA (khusus halaman ini)                           */
/* pnm_brng.js tetap dipakai untuk header & halaman list.              */
/* ------------------------------------------------------------------ */
window.updateGrandTotal = function () {
    if ($('#pnmGrid').length > 0) {
        pnmAddRecalcTotal();
    }
};

window.reload_pemakaian_dtl = function () {
    if ($('#pnmGrid').length > 0) {
        /* jangan otomatis membuat baris input baru; pertahankan draft
           yang sedang diisi (jika ada) */
        var $draftNow = $('#pnmGridBody tr.pnm-row-draft[data-mode="new"]').first();
        var keepDraft = $draftNow.length > 0 && pnmAddDraftHasData($draftNow);

        pnmAddLoadRows(false, keepDraft);
    }
};

window.updatepnmBrgDtl = function () {
    pnmAddUpdateSelected();
};

window.btnDeleteDetail = function () {
    pnmAddDeleteChecked();
};

/* Backward compatibility nama fungsi lama (dipakai tombol grid lama) */
window.getCheckedDetailIds = function () {
    return pnmAddCheckedIds();
};

