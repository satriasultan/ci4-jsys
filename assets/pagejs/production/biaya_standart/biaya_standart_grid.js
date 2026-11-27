/* =========================================================================
 * BIAYA STANDART — GRID DETAIL INLINE (gaya pmk_brng)
 * - Tanpa modal: input detail dilakukan langsung di baris grid.
 * - Tab / Enter pindah kolom; Enter di kolom terakhir = simpan + baris baru.
 * - Esc atau tombol X = batalkan baris.
 * - Aksi per baris: Simpan/Batal (baris editor) atau Edit/Hapus (baris tersimpan).
 * Endpoint (tidak berubah): save_biaya_standart_mst, get_biaya_standart_dtl,
 * delete_biaya_standart_dtl + endpoint grid baru list_biaya_standart_dtl_grid.
 * ========================================================================= */
var bstGridState = {
    rows: [],
    mode: 'add',
    draft: null,
    saving: false,
    search: '',
    sort: { key: null, dir: 'asc' }
};

function bstGridTable()  { return $('#tmp_stdcost'); }
function bstGridBody()   { return $('#bstGridBody'); }
function bstGridExists() { return bstGridBody().length > 0; }
function bstGridEditor() { return bstGridBody().find('tr.std-row-draft'); }

function bstGridEsc(text) {
    return $('<div>').text(text == null ? '' : String(text)).html();
}

function bstGridNum(value) {
    var n = parseFloat(String(value == null ? '' : value).replace(/,/g, ''));
    return isFinite(n) ? n : 0;
}

/* Fallback aman: beberapa halaman menyediakan helper global convertToDbNumber.
   Bila tidak tersedia, sediakan versi lokal agar grid tetap berjalan. */
if (typeof window.convertToDbNumber !== 'function') {
    window.convertToDbNumber = function (value) {
        var n = bstGridNum(value);
        return isFinite(n) ? String(n) : '';
    };
}

function bstGridDec(value, dec) {
    dec = (dec == null) ? 4 : dec;
    return bstGridNum(value).toLocaleString('en-US', {
        minimumFractionDigits: dec,
        maximumFractionDigits: dec
    });
}

function bstGridToast(message, icon) {
    if (typeof Swal === 'undefined') {
        alert(message);
        return;
    }

    Swal.fire({
        icon: icon || 'info',
        title: (icon === 'success') ? 'Berhasil' : (icon === 'error' ? 'Gagal' : 'Informasi'),
        text: message,
        timer: (icon === 'success') ? 1200 : undefined,
        showConfirmButton: (icon !== 'success')
    });
}

/* Tandai field wajib yang belum terisi (pengganti popup Swal):
   border merah + tooltip native, otomatis hilang saat field diisi. */
function bstGridMarkInvalid($el, message) {
    if (!$el || !$el.length) { return; }

    $el.addClass('std-invalid').attr('title', message);

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.next('.select2-container')
            .find('.select2-selection--single')
            .addClass('std-invalid')
            .attr('title', message);
    }

    if (!$el.data('stdInvalidBound')) {
        $el.data('stdInvalidBound', true);

        $el.on('input.stdinvalid change.stdinvalid select2:select.stdinvalid select2:clear.stdinvalid', function () {
            bstGridClearInvalid($el);
        });
    }
}

function bstGridClearInvalid($el) {
    if (!$el || !$el.length) { return; }

    $el.removeClass('std-invalid').removeAttr('title');

    $el.next('.select2-container')
        .find('.select2-selection--single')
        .removeClass('std-invalid')
        .removeAttr('title');
}

/* ------------------------------------------------------------------ */
/* RENDER                                                             */
/* ------------------------------------------------------------------ */
function bstGridVisibleRows() {
    var rows = (bstGridState.rows || []).slice();
    var term = $.trim(String(bstGridState.search || '')).toLowerCase();

    if (term !== '') {
        rows = $.grep(rows, function (r) {
            var hay = [r.idbarang, r.nmbarang, r.unit, r.description].join(' ').toLowerCase();
            return hay.indexOf(term) !== -1;
        });
    }

    var key = bstGridState.sort.key;

    if (key) {
        var dir = (bstGridState.sort.dir === 'desc') ? -1 : 1;

        rows.sort(function (a, b) {
            if (key === 'actualcost' || key === 'lastcost' || key === 'newcost') {
                return (bstGridNum(a[key]) - bstGridNum(b[key])) * dir;
            }

            var av = $.trim(String(a[key] == null ? '' : a[key])).toUpperCase();
            var bv = $.trim(String(b[key] == null ? '' : b[key])).toUpperCase();

            if (av === bv) { return 0; }

            return (av < bv ? -1 : 1) * dir;
        });
    }

    return rows;
}

function bstGridUpdateSortIndicator() {
    bstGridTable().find('thead th.std-sortable').each(function () {
        var $th = $(this);

        $th.removeClass('std-sort-asc std-sort-desc');

        if ($th.data('key') === bstGridState.sort.key) {
            $th.addClass(bstGridState.sort.dir === 'asc' ? 'std-sort-asc' : 'std-sort-desc');
        }
    });
}

function bstGridUpdateSelectedInfo() {
    var total = bstGridBody().find('tr.std-row-saved').length;
    var checked = bstGridBody().find('tr.std-row-saved .row-check:checked').length;

    $('#bstSelectedInfo').text(checked + ' dari ' + total + ' baris dipilih');
}

function bstGridEmptyState() {
    bstGridBody().find('tr.std-empty-row').remove();

    if (bstGridEditor().length > 0) {
        return;
    }

    if (bstGridBody().find('tr').length > 0) {
        return;
    }

    var adaData = (bstGridState.rows || []).length > 0;

    var pesan = adaData
        ? '<i class="fa fa-search"></i> Tidak ada data yang cocok dengan pencarian "' +
          bstGridEsc($.trim(bstGridState.search || '')) + '".'
        : '<i class="fa fa-inbox"></i> Belum ada data detail. ' +
          'Klik tombol <b>+</b> (atau tekan <b>Enter</b> / <b>Ctrl+Q</b>) untuk menambah barang.';

    bstGridBody().append(
        '<tr class="std-empty-row">' +
        '  <td colspan="9" tabindex="0">' + pesan + '</td>' +
        '</tr>'
    );
}

function bstGridRender() {
    var $body = bstGridBody();

    if (!$body.length) { return; }

    /* Baris editor dipindahkan sementara agar tidak ikut terhapus saat re-render
       (instance select2 di dalamnya tetap hidup). */
    var $editor = bstGridEditor().first().detach();

    $body.empty();

    $.each(bstGridVisibleRows(), function (i, row) {
        $body.append(bstGridSavedRow(row));
    });

    if ($editor && $editor.length) {
        $body.prepend($editor);
    }

    bstGridUpdateSortIndicator();
    bstGridEmptyState();
    bstGridUpdateSelectedInfo();
    bstGridTable().toggleClass('std-editing', bstGridEditor().length > 0);
}

function bstGridSavedRow(row) {
    var id    = bstGridEsc(row.uniqueid || '');
    var desc  = $.trim(row.description || '');
    var nmb   = $.trim(row.nmbarang || '');

    return '' +
        '<tr class="std-row-saved" data-uniqueid="' + id + '" data-idurut="' + bstGridEsc(row.idurut || '') + '">' +
        '  <td class="std-col-no"><input type="checkbox" class="row-check" value="' + id + '" title="Pilih baris (hapus massal: Ctrl+D)"></td>' +
        '  <td title="' + bstGridEsc(row.idbarang) + '">' + bstGridEsc(row.idbarang) + '</td>' +
        '  <td title="' + bstGridEsc(nmb) + '"><span class="std-readonly-text">' + bstGridEsc(nmb) + '</span></td>' +
        '  <td class="text-center">' + bstGridEsc(row.unit) + '</td>' +
        '  <td class="text-end"><span class="std-readonly-number">' + bstGridDec(row.actualcost, 4) + '</span></td>' +
        '  <td class="text-end"><span class="std-readonly-number">' + bstGridDec(row.lastcost, 4) + '</span></td>' +
        '  <td class="text-end"><span class="std-readonly-number">' + bstGridDec(row.newcost, 4) + '</span></td>' +
        '  <td title="' + bstGridEsc(desc) + '"><span class="std-readonly-text">' + bstGridEsc(desc) + '</span></td>' +
        '  <td class="std-col-aksi">' +
        '      <button type="button" class="std-btn std-btn-edit" data-act="edit" title="Edit baris">' +
        '          <i class="fa fa-pencil"></i></button>' +
        '  </td>' +
        '</tr>';
}

/* (versi lama bstGridRender dihapus: duplikat yang menimpa versi filter+sort) */

/* ------------------------------------------------------------------ */
/* LOAD                                                               */
/* ------------------------------------------------------------------ */
function bstGridLoadRows(focusDraft) {
    $.ajax({
        url: HOST_URL + 'production/trans/list_biaya_standart_dtl_grid',
        type: 'GET',
        dataType: 'json'
    }).done(function (res) {
        bstGridState.rows = (res && res.items) ? res.items : [];
        bstGridRender();

        if (focusDraft === true) {
            bstGridNewDraft();
        }
    }).fail(function () {
        bstGridToast('Gagal memuat data detail', 'error');
    });
}

function bstGridReload() {
    bstGridLoadRows(false);
}

/* ------------------------------------------------------------------ */
/* BARIS EDITOR                                                       */
/* ------------------------------------------------------------------ */
function bstGridNewDraft() {
    if (!bstGridExists()) { return; }

    if (bstGridEditor().length > 0) {
        bstGridFocusEditor();
        return;
    }

    bstGridState.mode = 'add';
    bstGridState.draft = null;

    bstGridRemoveEditor();
    bstGridBody().find('tr.std-empty-row').remove();
    bstGridBody().append(bstGridEditorRow({}));
    bstGridBindEditor(bstGridEditor());
    bstGridTable().addClass('std-editing');
    bstGridFocusEditor();
}

function bstGridEditRow(uniqueid) {
    if (!uniqueid) { return; }

    /* cari data dari state (tanpa AJAX lookup), persis pola pmk_brng */
    var row = null;

    $.each(bstGridState.rows || [], function (i, r) {
        if (String(r.uniqueid) === String(uniqueid)) {
            row = r;
            return false;
        }
    });

    if (!row) { return; }

    /* bila ada baris draft baru yang sedang berisi → konfirmasi dulu */
    var $draft = bstGridBody().find('tr.std-row-draft[data-mode="new"]').first();

    if ($draft.length && bstGridDraftHasData($draft)) {
        Swal.fire({
            icon: 'question',
            title: 'Baris Baru Belum Disimpan',
            text: 'Isi baris baru yang sedang diketik akan dibuang. Lanjutkan edit baris tersimpan?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then(function (res) {
            if (res.isConfirmed) {
                bstGridEditRowNow(row);
            }
        });

        return;
    }

    bstGridEditRowNow(row);
}

function bstGridDraftHasData($tr) {
    var idbarang = ($tr.find('select.std-item').val() || '').toString().trim();
    var newcost = ($tr.find('.std-newcost').val() || '').toString().trim();
    var desc = ($tr.find('.std-description').val() || '').toString().trim();

    return (idbarang !== '' || newcost !== '' || desc !== '');
}

function bstGridEditRowNow(row) {
    /* bila baris lain sedang diedit → pulihkan dulu supaya tidak ada baris yang hilang */
    if (bstGridState.mode === 'edit' && bstGridEditor().length) {
        bstGridRemoveEditor();
        bstGridRender();
    }

    bstGridRemoveEditor();
    bstGridBody().find('tr.std-empty-row').remove();

    var id = String(row.uniqueid);
    var $target = bstGridBody().find('tr.std-row-saved[data-uniqueid="' + id + '"]');

    bstGridState.mode = 'edit';
    bstGridState.draft = {
        idurut: row.idurut,
        uniqueid: row.uniqueid,
        idbarang: row.idbarang,
        nmbarang: row.nmbarang,
        unit: row.unit,
        actualcost: row.actualcost,
        lastcost: row.lastcost,
        newcost: row.newcost,
        description: row.description
    };

    var $editor = bstGridEditorRow(bstGridState.draft)
        .addClass('std-row-editing')
        .attr('data-mode', 'edit');

    if ($target.length) {
        $target.replaceWith($editor);
    } else {
        bstGridBody().append($editor);
    }

    bstGridBindEditor($editor);
    bstGridTable().addClass('std-editing');
    bstGridFocusEditor();
}

function bstGridRemoveEditor() {
    var $tr = bstGridEditor();

    if ($tr.length) {
        $tr.find('select').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        $tr.remove();
    }

    bstGridTable().removeClass('std-editing');

    bstGridState.mode = 'add';
    bstGridState.draft = null;
}

function bstGridEditorRow(data) {
    data = data || {};

    var isEdit = $.trim(String(data.uniqueid || '')) !== '' || $.trim(String(data.idurut || '')) !== '';

    var $tr = $('<tr class="std-row-draft"></tr>')
        .attr('data-idurut', data.idurut || '')
        .attr('data-uniqueid', data.uniqueid || '')
        .attr('data-mode', isEdit ? 'edit' : 'new');

    /* 1. kolom no */
    $tr.append(
        $('<td class="std-col-no"></td>').html(isEdit
            ? '<i class="fa fa-pencil-square-o" style="color:#d97706"></i>'
            : '<i class="fa fa-plus-circle" style="color:#16a34a"></i>')
    );

    /* 2. ID Barang (select2) */
    $tr.append($('<td class="std-cell"></td>').append('<select class="form-select std-item"></select>'));

    /* 3. Nama Barang */
    $tr.append($('<td></td>').append(
        $('<input type="text" class="form-control std-nmbarang" readonly tabindex="-1">').val(data.nmbarang || '')
    ));

    /* 4. Satuan */
    $tr.append($('<td></td>').append(
        $('<input type="text" class="form-control std-unit" readonly tabindex="-1">').val(data.unit || '')
    ));

    /* 5. Actual Cost */
    $tr.append($('<td></td>').append(
        $('<input type="text" class="form-control text-end jaccseparator std-actualcost" readonly tabindex="-1">')
            .val(data.actualcost != null ? convertToDbNumber(data.actualcost) : '')
    ));

    /* 6. Last Std Cost */
    $tr.append($('<td></td>').append(
        $('<input type="text" class="form-control text-end jaccseparator std-lastcost" readonly tabindex="-1">')
            .val(data.lastcost != null ? convertToDbNumber(data.lastcost) : '')
    ));

    /* 7. New Std Cost (editable) */
    $tr.append($('<td></td>').append(
        $('<input type="text" class="form-control text-end jaccseparator std-newcost">')
            .val(data.newcost != null ? convertToDbNumber(data.newcost) : '')
    ));

    /* 8. Keterangan */
    $tr.append($('<td></td>').append(
        $('<input type="text" class="form-control std-description" style="text-transform:uppercase">')
            .val(data.description || '')
    ));

    /* 9. Aksi */
    $tr.append(
        $('<td class="std-col-aksi"></td>').html(
            '<button type="button" class="std-btn std-btn-save" data-act="save" title="Simpan (Enter)"><i class="fa fa-save"></i></button>' +
            '<button type="button" class="std-btn std-btn-cancel" data-act="cancel" title="Batal (Esc)"><i class="fa fa-times"></i></button>'
        )
    );

    return $tr;
}


/* ------------------------------------------------------------------ */
/* SELECT2 ITEM + AUTO-FILL                                           */
/* ------------------------------------------------------------------ */
function bstGridInitItemSelect($tr) {
    var $item = $tr.find('select.std-item');

    if (!$item.length) { return; }

    $item.select2({
        placeholder: "Ketik minimal 2 huruf untuk mencari barang...",
        allowClear: true,
        width: '100%',
        minimumInputLength: 2,
        dropdownParent: $(document.body),
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_item_std_cost',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term,
                    _page_: params.page,
                    _draw_: true,
                    _start_: 1,
                    _perpage_: 20,
                    _paramglobal_: defaultInitialGroupBrng,
                    _parameterx_: defaultInitialGroupBrng,
                    loccode: $('[name="idlocation_dtl"]').val(),
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) { return markup; },
        templateResult: (typeof formatItem === 'function') ? formatItem : undefined,
        templateSelection: (typeof formatItemSelection === 'function') ? formatItemSelection : undefined
    });

    /* prefill saat mode edit */
    var d = bstGridState.draft || {};

    if ($.trim(String(d.idbarang || '')) !== '') {
        var opt = new Option(d.nmbarang || d.idbarang, d.idbarang, true, true);
        $item.append(opt).trigger('change.select2');
    }

    /* auto-fill: logika identik dengan modal */
    $item.on('select2:select', function (e) {
        var it = e.params.data || {};

        $tr.find('.std-nmbarang').val((it.nmbarang || '').trim());
        $tr.find('.std-unit').val((it.unit || '').trim());
        $tr.find('.std-actualcost').val(it.actualcost != null ? convertToDbNumber(it.actualcost) : '');

        /* Last Std Cost: prioritas newcost -> lastcost -> actualcost (sama seperti modal) */
        var lastRef = [it.newcost, it.lastcost, it.actualcost]
            .map(function (v) { var n = parseFloat(v); return isFinite(n) ? n : 0; })
            .find(function (n) { return n !== 0; });

        $tr.find('.std-lastcost').val(convertToDbNumber(lastRef != null ? lastRef : 0));

        /* lanjut ke New Std Cost */
        $tr.find('.std-newcost').focus().select();
    });

    $item.on('select2:clear', function () {
        $tr.find('.std-nmbarang').val('');
        $tr.find('.std-unit').val('');
        $tr.find('.std-actualcost').val('');
        $tr.find('.std-lastcost').val('');
    });
}


/* ------------------------------------------------------------------ */
/* NAVIGASI KEYBOARD (Tab / Enter / Esc)                              */
/* ------------------------------------------------------------------ */
function bstGridFields($tr) {
    /* Hanya field yang bisa diisi; kolom readonly di-skip (sama seperti pmk_brng) */
    return $tr.find('select.std-item, input.std-newcost, input.std-description');
}

function bstGridFocusEditor() {
    var $tr = bstGridEditor();

    if (!$tr.length) { return; }

    var $item = $tr.find('select.std-item');

    if ($item.length) {
        bstGridOpenSelect2($item);
    }
}

/* ------------------------------------------------------------------ */
/* SELECT2: fokus otomatis ke kotak pencarian (pola pmk_brng)          */
/* ------------------------------------------------------------------ */
function bstGridSearchField($select) {
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

/* Fokuskan search field milik dropdown select2 yang sedang terbuka (khusus grid) */
function bstGridFocusOpenSearch() {
    var $open = $('#tmp_stdcost .select2-container--open');

    if (!$open.length) {
        return false;
    }

    var $select = $open.first().prev('select.select2-hidden-accessible');
    var $search = $select.length ? bstGridSearchField($select) : $();

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

/* Buka dropdown select2 grid lalu langsung fokus ke kotak pencarian */
function bstGridOpenSelect2($select) {
    if (!$select || !$select.length) {
        return;
    }

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

    bstGridFocusOpenSearch();
    window.setTimeout(bstGridFocusOpenSearch, 0);
    window.setTimeout(bstGridFocusOpenSearch, 80);
}

function bstGridBindEditor($tr) {
    if (!$tr || !$tr.length) { return; }

    bstGridInitItemSelect($tr);

    $tr.off('.stdgrid');

    /* select2 auto-open saat sel mendapat fokus (perilaku pmk_brng) */
    $tr.on('focusin.stdgrid', '.select2-selection', function () {
        var $sel = $(this).closest('td').find('select.std-item');

        if ($sel.length) {
            var api = $sel.data('select2');

            if (api && !api.isOpen()) {
                $sel.select2('open');
            }
        }
    });

    /* Tab/Enter pindah kolom; Tab/Enter di kolom terakhir = simpan + baris baru */
    $tr.on('keydown.stdgrid', 'input, select', function (e) {
        var isLastField = $(this).is(bstGridFields($tr).last());

        if (e.key === 'Enter') {
            e.preventDefault();

            if (isLastField) {
                bstGridLastField();
            } else {
                bstGridMoveNext($(this));
            }

            return;
        }

        /* TAB di kolom terakhir (Keterangan) → simpan baris + buka baris baru */
        if (e.key === 'Tab' && !e.shiftKey && isLastField) {
            e.preventDefault();
            bstGridLastField();
            return;
        }

        /* ESC ditangani global (document keydown capture) agar juga berfungsi
           saat fokus masih di dalam baris draft/edit — lihat bstGridBindGlobalEsc(). */
    });

    /* tombol aksi di dalam baris editor */
    $tr.on('click.stdgrid', '.std-btn', function () {
        var act = $(this).data('act');

        if (act === 'save') {
            bstGridSave();
        } else if (act === 'cancel') {
            bstGridCancel();
        }
    });
}

/* Tab/Enter di kolom terakhir: simpan lalu buka baris baru.
   Bila baris masih kosong → fokus ke kolom pertama tanpa error
   (persis perilaku pmkAddLastField() di modul pmk_brng). */
function bstGridLastField() {
    var $tr = bstGridEditor();

    if (!$tr.length) { return; }

    var idbarang = ($tr.find('select.std-item').val() || '').toString().trim();
    var newcost = ($tr.find('.std-newcost').val() || '').toString().trim();
    var desc = ($tr.find('.std-description').val() || '').toString().trim();

    if (idbarang === '' && newcost === '' && desc === '') {
        bstGridFocusEditor();
        return;
    }

    bstGridSave(true);
}

function bstGridMoveNext($el) {
    var $tr = $el.closest('tr');
    var $fields = bstGridFields($tr);
    var idx = $fields.index($el);

    if (idx >= 0 && idx < $fields.length - 1) {
        var $next = $fields.eq(idx + 1);

        if ($next.hasClass('select2-hidden-accessible')) {
            bstGridOpenSelect2($next);
        } else {
            $next.focus().select();
        }

        return;
    }

    /* kolom terakhir → simpan lalu baris baru */
    bstGridSave(true);
}

function bstGridCancel() {
    bstGridRemoveEditor();
    // re-render: pulihkan baris tersimpan (untuk mode edit) & kembalikan empty state
    bstGridRender();
}


/* ------------------------------------------------------------------ */
/* SIMPAN BARIS                                                       */
/* ------------------------------------------------------------------ */
function bstGridSave(openNext) {
    var $tr = bstGridEditor();

    if (!$tr.length) { return; }
    if (bstGridState.saving) { return; }

    var $item = $tr.find('select.std-item');
    var idbarang = ($item.val() || '').toString().trim();

    if (idbarang === '') {
        bstGridMarkInvalid($item, 'ID Barang wajib dipilih');
        bstGridOpenSelect2($item);
        return;
    }

    var $newcost = $tr.find('.std-newcost');
    var newcostRaw = $newcost.val();

    if ($.trim(String(newcostRaw || '')) === '') {
        bstGridMarkInvalid($newcost, 'New Std Cost wajib diisi');
        $newcost.focus().select();
        return;
    }

    var activedate = $.trim($('#activedate').val() || '');

    if (activedate === '') {
        bstGridToast('Activate Date tidak boleh kosong!', 'warning');
        $('#activedate').focus();
        return;
    }

    var formData = new FormData();

    /* DETAIL — nama field identik dengan modal */
    formData.append('idurut', ($tr.attr('data-idurut') || '').toString());
    formData.append('uniqueid', ($tr.attr('data-uniqueid') || '').toString());
    formData.append('idbarang', idbarang);
    formData.append('nmbarang', ($tr.find('.std-nmbarang').val() || '').toString());
    formData.append('unit', ($tr.find('.std-unit').val() || '').toString());
    formData.append('description_detail', ($tr.find('.std-description').val() || '').toUpperCase());
    formData.append('actualcost', bstGridNum($tr.find('.std-actualcost').val()));
    formData.append('lastcost', bstGridNum($tr.find('.std-lastcost').val()));
    formData.append('newcost', bstGridNum(newcostRaw));

    /* HEADER — hanya field yang benar-benar ada di halaman ini.
       (biaya_standart tidak punya docref/idcostcenter/idlocation/description) */
    $.each(['docref', 'cabang', 'pemohon', 'docno', 'docdate', 'activedate',
            'penyesuaian_a', 'penyesuaian_b', 'dari_bagian',
            'idcostcenter', 'idlocation', 'keterangan', 'description'], function (i, name) {
        if ($('#' + name).length) {
            formData.append(name, $('#' + name).val());
        }
    });

    formData.set(
        'docno',
        $('#prefix').val() + '/' +
        $('#infix').val() + '/' +
        $('#suffix').val()
    );

    bstGridState.saving = true;

    var $btn = $tr.find('[data-act="save"]');
    $btn.prop('disabled', true);

    $.ajax({
        url: HOST_URL + 'production/trans/save_biaya_standart_mst',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,

        success: function (res) {
            if (res.success) {
                // tanpa popup sukses (percepatan input administrasi)

                if (res.reload === true) {
                    window.location.reload();
                    return;
                }

                bstGridRemoveEditor();
                bstGridLoadRows(openNext === true);
            } else {
                bstGridToast(res.message || 'Gagal menyimpan data', 'warning');
            }
        },

        error: function () {
            bstGridToast('Terjadi kesalahan server', 'error');
        },

        complete: function () {
            bstGridState.saving = false;
            $btn.prop('disabled', false);
        }
    });
}


/* ------------------------------------------------------------------ */
/* HAPUS & EDIT                                                       */
/* ------------------------------------------------------------------ */
function bstGridCheckedIds() {
    var ids = [];

    bstGridBody().find('.row-check:checked').each(function () {
        ids.push($(this).val());
    });

    return ids;
}

function bstGridDelete(ids) {
    if (!ids || ids.length === 0) { return; }

    var teks = (ids.length > 1)
        ? 'Yakin hapus ' + ids.length + ' data terpilih?'
        : 'Yakin hapus data ini?';

    Swal.fire({
        title: 'Konfirmasi',
        text: teks,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(function (result) {
        if (!result.isConfirmed) { return; }

        $.ajax({
            url: HOST_URL + 'production/trans/delete_biaya_standart_dtl',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json'
        }).done(function (res) {
            if (res && res.status) {
                // tanpa popup sukses (percepatan input administrasi)
                bstGridRemoveEditor();
                $('#checkAll').prop('checked', false);
                bstGridLoadRows(false);
            } else {
                bstGridToast((res && res.message) || 'Gagal hapus data', 'error');
            }
        }).fail(function () {
            bstGridToast('Gagal hapus data', 'error');
        });
    });
}

function bstGridDeleteRow(uniqueid) {
    if (!uniqueid) { return; }
    bstGridDelete([uniqueid]);
}

function bstGridDeleteSelected() {
    var ids = bstGridCheckedIds();

    if (ids.length === 0) {
        bstGridToast('Pilih data yang akan dihapus', 'warning');
        return;
    }

    bstGridDelete(ids);
}

function bstGridEditSelected() {
    var ids = bstGridCheckedIds();

    if (ids.length === 0) {
        bstGridToast('Pilih satu data yang akan diupdate', 'warning');
        return;
    }

    if (ids.length > 1) {
        bstGridToast('Update hanya boleh satu data', 'warning');
        return;
    }

    bstGridEditRow(ids[0]);
}

/* ------------------------------------------------------------------ */
/* INIT                                                               */
/* ------------------------------------------------------------------ */
$(function () {
    if (!bstGridExists()) { return; }

    /* aksi per baris + empty state */
    bstGridBody()
        .off('.stdgrid')
        .on('click.stdgrid', 'button[data-act="edit"]', function () {
            bstGridEditRow($(this).closest('tr').attr('data-uniqueid'));
        })
        .on('click.stdgrid', 'button[data-act="delete"]', function () {
            bstGridDeleteRow($(this).closest('tr').attr('data-uniqueid'));
        })
        .on('click.stdgrid', 'tr.std-empty-row', function () {
            bstGridNewDraft();
        })
        .on('keydown.stdgrid', 'tr.std-empty-row', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                bstGridNewDraft();
            }
        })
        .on('change.stdgrid', '.row-check', function () {
            var total = bstGridBody().find('.row-check').length;
            var checked = bstGridBody().find('.row-check:checked').length;

            $(this).closest('tr').toggleClass('std-row-checked', this.checked);
            $('#checkAll').prop('checked', total > 0 && total === checked);
            bstGridUpdateSelectedInfo();
        });

    /* select all */
    $(document).off('change.stdgridall').on('change.stdgridall', '#checkAll', function () {
        var on = this.checked;

        bstGridBody().find('.row-check').prop('checked', on);
        bstGridBody().find('tr.std-row-saved').toggleClass('std-row-checked', on);
        bstGridUpdateSelectedInfo();
    });

    /* shortcut tambahan: Alt+N (sama seperti pmk_brng) */
    $(document).off('keydown.stdgridnew').on('keydown.stdgridnew', function (e) {
        if (e.altKey && (e.key === 'n' || e.key === 'N')) {
            e.preventDefault();
            bstGridNewDraft();
        }
    });

    /* Setiap dropdown select2 grid dibuka (klik / Tab / programatik)
       → fokus otomatis ke kotak pencarian agar user bisa langsung mengetik. */
    $(document).off('select2:open.stdgrid').on('select2:open.stdgrid', function () {
        bstGridFocusOpenSearch();
        window.setTimeout(bstGridFocusOpenSearch, 0);
        window.setTimeout(bstGridFocusOpenSearch, 80);
    });

    /* Tab di kotak pencarian select2 grid → tutup dropdown & lanjut kolom berikutnya */
    $(document).off('keydown.stdgridsearch').on('keydown.stdgridsearch', '.select2-search__field', function (e) {
        if (e.key !== 'Tab' || e.shiftKey) {
            return;
        }

        var $open = $('#tmp_stdcost .select2-container--open');

        if (!$open.length) {
            return;
        }

        e.preventDefault();

        var $select = $open.first().prev('select.select2-hidden-accessible');

        try {
            $select.select2('close');
        } catch (err) {
            /* abaikan */
        }

        if ($select.length) {
            bstGridMoveNext($select);
        }
    });

    /* Search detail (pengganti kotak search DataTables) */
    $('#bstGridSearch')
        .off('input.stdgridsearch')
        .on('input.stdgridsearch', function () {
            bstGridState.search = $(this).val();
            bstGridRender();
        });

    /* Klik header kolom = sort asc/desc (pengganti ordering DataTables) */
    bstGridTable()
        .find('thead th.std-sortable')
        .off('click.stdgridsort')
        .on('click.stdgridsort', function () {
            var key = $(this).data('key');

            if (!key) { return; }

            if (bstGridState.sort.key === key) {
                bstGridState.sort.dir = (bstGridState.sort.dir === 'asc') ? 'desc' : 'asc';
            } else {
                bstGridState.sort.key = key;
                bstGridState.sort.dir = 'asc';
            }

            bstGridRender();
        });

    /* Klik di kolom mana saja pada baris tersimpan → centang (behaviour lama) */
    bstGridBody().on('click.stdgrid', 'tr.std-row-saved', function (e) {
        if ($(e.target).closest('input, button, a, label, select').length) {
            return;
        }

        var $chk = $(this).find('.row-check');

        if ($chk.length) {
            $chk.prop('checked', !$chk.prop('checked')).trigger('change');
        }
    });

    /* ESC global (capture): batalkan baris draft/edit bila fokus masih di dalam baris.
       Bila fokus di dropdown Select2 (di luar baris), biarkan Select2 menutup dropdown. */
    document.addEventListener('keydown', function (ev) {
        if (ev.key !== 'Escape' && ev.keyCode !== 27) {
            return;
        }

        var active = document.activeElement;

        if (!active || typeof active.closest !== 'function') {
            return;
        }

        var $row = $(active).closest('tr.std-row-draft');

        if (!$row.length || !document.body.contains($row[0])) {
            return;
        }

        ev.preventDefault();
        bstGridCancel();
    }, true);

    /* Tombol Hapus di toolbar (hapus massal baris yang dicentang) */
    $(document).off('click.stdgriddelete').on('click.stdgriddelete', '#bstDeleteSelected', function (e) {
        e.preventDefault();
        bstGridDeleteSelected();
    });

    bstGridLoadRows(false);
});

