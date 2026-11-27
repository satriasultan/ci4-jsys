/* =========================================================================
 * STANDART COST — GRID DETAIL INLINE (gaya pmk_brng)
 * - Tanpa modal: input detail dilakukan langsung di baris grid.
 * - Tab / Enter pindah kolom; Enter di kolom terakhir = simpan + baris baru.
 * - Esc atau tombol X = batalkan baris.
 * - Aksi per baris: Simpan/Batal (baris editor) atau Edit/Hapus (baris tersimpan).
 * Endpoint (tidak berubah): save_standart_cost_mst, get_standart_cost_dtl,
 * delete_standart_cost_dtl + endpoint grid baru list_standart_cost_dtl_grid.
 * ========================================================================= */
var stdGridState = {
    rows: [],
    mode: 'add',
    draft: null,
    saving: false,
    search: '',
    sort: { key: null, dir: 'asc' }
};

function stdGridTable()  { return $('#tmp_stdcost'); }
function stdGridBody()   { return $('#stdGridBody'); }
function stdGridExists() { return stdGridBody().length > 0; }
function stdGridEditor() { return stdGridBody().find('tr.std-row-draft'); }

function stdGridEsc(text) {
    return $('<div>').text(text == null ? '' : String(text)).html();
}

function stdGridNum(value) {
    var n = parseFloat(String(value == null ? '' : value).replace(/,/g, ''));
    return isFinite(n) ? n : 0;
}

/* Fallback aman: beberapa halaman menyediakan helper global convertToDbNumber.
   Bila tidak tersedia, sediakan versi lokal agar grid tetap berjalan. */
if (typeof window.convertToDbNumber !== 'function') {
    window.convertToDbNumber = function (value) {
        var n = stdGridNum(value);
        return isFinite(n) ? String(n) : '';
    };
}

function stdGridDec(value, dec) {
    dec = (dec == null) ? 4 : dec;
    return stdGridNum(value).toLocaleString('en-US', {
        minimumFractionDigits: dec,
        maximumFractionDigits: dec
    });
}

function stdGridToast(message, icon) {
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
function stdGridMarkInvalid($el, message) {
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
            stdGridClearInvalid($el);
        });
    }
}

function stdGridClearInvalid($el) {
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
function stdGridVisibleRows() {
    var rows = (stdGridState.rows || []).slice();
    var term = $.trim(String(stdGridState.search || '')).toLowerCase();

    if (term !== '') {
        rows = $.grep(rows, function (r) {
            var hay = [r.idbarang, r.nmbarang, r.unit, r.description].join(' ').toLowerCase();
            return hay.indexOf(term) !== -1;
        });
    }

    var key = stdGridState.sort.key;

    if (key) {
        var dir = (stdGridState.sort.dir === 'desc') ? -1 : 1;

        rows.sort(function (a, b) {
            if (key === 'actualcost' || key === 'lastcost' || key === 'newcost') {
                return (stdGridNum(a[key]) - stdGridNum(b[key])) * dir;
            }

            var av = $.trim(String(a[key] == null ? '' : a[key])).toUpperCase();
            var bv = $.trim(String(b[key] == null ? '' : b[key])).toUpperCase();

            if (av === bv) { return 0; }

            return (av < bv ? -1 : 1) * dir;
        });
    }

    return rows;
}

function stdGridUpdateSortIndicator() {
    stdGridTable().find('thead th.std-sortable').each(function () {
        var $th = $(this);

        $th.removeClass('std-sort-asc std-sort-desc');

        if ($th.data('key') === stdGridState.sort.key) {
            $th.addClass(stdGridState.sort.dir === 'asc' ? 'std-sort-asc' : 'std-sort-desc');
        }
    });
}

function stdGridUpdateSelectedInfo() {
    var total = stdGridBody().find('tr.std-row-saved').length;
    var checked = stdGridBody().find('tr.std-row-saved .row-check:checked').length;

    $('#stdSelectedInfo').text(checked + ' dari ' + total + ' baris dipilih');
}

function stdGridEmptyState() {
    stdGridBody().find('tr.std-empty-row').remove();

    if (stdGridEditor().length > 0) {
        return;
    }

    if (stdGridBody().find('tr').length > 0) {
        return;
    }

    var adaData = (stdGridState.rows || []).length > 0;

    var pesan = adaData
        ? '<i class="fa fa-search"></i> Tidak ada data yang cocok dengan pencarian "' +
          stdGridEsc($.trim(stdGridState.search || '')) + '".'
        : '<i class="fa fa-inbox"></i> Belum ada data detail. ' +
          'Klik tombol <b>+</b> (atau tekan <b>Enter</b> / <b>Ctrl+Q</b>) untuk menambah barang.';

    stdGridBody().append(
        '<tr class="std-empty-row">' +
        '  <td colspan="9" tabindex="0">' + pesan + '</td>' +
        '</tr>'
    );
}

function stdGridRender() {
    var $body = stdGridBody();

    if (!$body.length) { return; }

    /* Baris editor dipindahkan sementara agar tidak ikut terhapus saat re-render
       (instance select2 di dalamnya tetap hidup). */
    var $editor = stdGridEditor().first().detach();

    $body.empty();

    $.each(stdGridVisibleRows(), function (i, row) {
        $body.append(stdGridSavedRow(row));
    });

    if ($editor && $editor.length) {
        $body.prepend($editor);
    }

    stdGridUpdateSortIndicator();
    stdGridEmptyState();
    stdGridUpdateSelectedInfo();
    stdGridTable().toggleClass('std-editing', stdGridEditor().length > 0);
}

function stdGridSavedRow(row) {
    var id    = stdGridEsc(row.uniqueid || '');
    var desc  = $.trim(row.description || '');
    var nmb   = $.trim(row.nmbarang || '');

    return '' +
        '<tr class="std-row-saved" data-uniqueid="' + id + '" data-idurut="' + stdGridEsc(row.idurut || '') + '">' +
        '  <td class="std-col-no"><input type="checkbox" class="row-check" value="' + id + '" title="Pilih baris (hapus massal: Ctrl+D)"></td>' +
        '  <td title="' + stdGridEsc(row.idbarang) + '">' + stdGridEsc(row.idbarang) + '</td>' +
        '  <td title="' + stdGridEsc(nmb) + '"><span class="std-readonly-text">' + stdGridEsc(nmb) + '</span></td>' +
        '  <td class="text-center">' + stdGridEsc(row.unit) + '</td>' +
        '  <td class="text-end"><span class="std-readonly-number">' + stdGridDec(row.actualcost, 4) + '</span></td>' +
        '  <td class="text-end"><span class="std-readonly-number">' + stdGridDec(row.lastcost, 4) + '</span></td>' +
        '  <td class="text-end"><span class="std-readonly-number">' + stdGridDec(row.newcost, 4) + '</span></td>' +
        '  <td title="' + stdGridEsc(desc) + '"><span class="std-readonly-text">' + stdGridEsc(desc) + '</span></td>' +
        '  <td class="std-col-aksi">' +
        '      <button type="button" class="std-btn std-btn-edit" data-act="edit" title="Edit baris">' +
        '          <i class="fa fa-pencil"></i></button>' +
        '  </td>' +
        '</tr>';
}

/* (versi lama stdGridRender dihapus: duplikat yang menimpa versi filter+sort) */

/* ------------------------------------------------------------------ */
/* LOAD                                                               */
/* ------------------------------------------------------------------ */
function stdGridLoadRows(focusDraft) {
    $.ajax({
        url: HOST_URL + 'production/trans/list_standart_cost_dtl_grid',
        type: 'GET',
        dataType: 'json'
    }).done(function (res) {
        stdGridState.rows = (res && res.items) ? res.items : [];
        stdGridRender();

        if (focusDraft === true) {
            stdGridNewDraft();
        }
    }).fail(function () {
        stdGridToast('Gagal memuat data detail', 'error');
    });
}

function stdGridReload() {
    stdGridLoadRows(false);
}

/* ------------------------------------------------------------------ */
/* BARIS EDITOR                                                       */
/* ------------------------------------------------------------------ */
function stdGridNewDraft() {
    if (!stdGridExists()) { return; }

    if (stdGridEditor().length > 0) {
        stdGridFocusEditor();
        return;
    }

    stdGridState.mode = 'add';
    stdGridState.draft = null;

    stdGridRemoveEditor();
    stdGridBody().find('tr.std-empty-row').remove();
    stdGridBody().append(stdGridEditorRow({}));
    stdGridBindEditor(stdGridEditor());
    stdGridTable().addClass('std-editing');
    stdGridFocusEditor();
}

function stdGridEditRow(uniqueid) {
    if (!uniqueid) { return; }

    /* cari data dari state (tanpa AJAX lookup), persis pola pmk_brng */
    var row = null;

    $.each(stdGridState.rows || [], function (i, r) {
        if (String(r.uniqueid) === String(uniqueid)) {
            row = r;
            return false;
        }
    });

    if (!row) { return; }

    /* bila ada baris draft baru yang sedang berisi → konfirmasi dulu */
    var $draft = stdGridBody().find('tr.std-row-draft[data-mode="new"]').first();

    if ($draft.length && stdGridDraftHasData($draft)) {
        Swal.fire({
            icon: 'question',
            title: 'Baris Baru Belum Disimpan',
            text: 'Isi baris baru yang sedang diketik akan dibuang. Lanjutkan edit baris tersimpan?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then(function (res) {
            if (res.isConfirmed) {
                stdGridEditRowNow(row);
            }
        });

        return;
    }

    stdGridEditRowNow(row);
}

function stdGridDraftHasData($tr) {
    var idbarang = ($tr.find('select.std-item').val() || '').toString().trim();
    var newcost = ($tr.find('.std-newcost').val() || '').toString().trim();
    var desc = ($tr.find('.std-description').val() || '').toString().trim();

    return (idbarang !== '' || newcost !== '' || desc !== '');
}

function stdGridEditRowNow(row) {
    /* bila baris lain sedang diedit → pulihkan dulu supaya tidak ada baris yang hilang */
    if (stdGridState.mode === 'edit' && stdGridEditor().length) {
        stdGridRemoveEditor();
        stdGridRender();
    }

    stdGridRemoveEditor();
    stdGridBody().find('tr.std-empty-row').remove();

    var id = String(row.uniqueid);
    var $target = stdGridBody().find('tr.std-row-saved[data-uniqueid="' + id + '"]');

    stdGridState.mode = 'edit';
    stdGridState.draft = {
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

    var $editor = stdGridEditorRow(stdGridState.draft)
        .addClass('std-row-editing')
        .attr('data-mode', 'edit');

    if ($target.length) {
        $target.replaceWith($editor);
    } else {
        stdGridBody().append($editor);
    }

    stdGridBindEditor($editor);
    stdGridTable().addClass('std-editing');
    stdGridFocusEditor();
}

function stdGridRemoveEditor() {
    var $tr = stdGridEditor();

    if ($tr.length) {
        $tr.find('select').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        $tr.remove();
    }

    stdGridTable().removeClass('std-editing');

    stdGridState.mode = 'add';
    stdGridState.draft = null;
}

function stdGridEditorRow(data) {
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
function stdGridInitItemSelect($tr) {
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
    var d = stdGridState.draft || {};

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
function stdGridFields($tr) {
    /* Hanya field yang bisa diisi; kolom readonly di-skip (sama seperti pmk_brng) */
    return $tr.find('select.std-item, input.std-newcost, input.std-description');
}

function stdGridFocusEditor() {
    var $tr = stdGridEditor();

    if (!$tr.length) { return; }

    var $item = $tr.find('select.std-item');

    if ($item.length) {
        stdGridOpenSelect2($item);
    }
}

/* ------------------------------------------------------------------ */
/* SELECT2: fokus otomatis ke kotak pencarian (pola pmk_brng)          */
/* ------------------------------------------------------------------ */
function stdGridSearchField($select) {
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
function stdGridFocusOpenSearch() {
    var $open = $('#tmp_stdcost .select2-container--open');

    if (!$open.length) {
        return false;
    }

    var $select = $open.first().prev('select.select2-hidden-accessible');
    var $search = $select.length ? stdGridSearchField($select) : $();

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
function stdGridOpenSelect2($select) {
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

    stdGridFocusOpenSearch();
    window.setTimeout(stdGridFocusOpenSearch, 0);
    window.setTimeout(stdGridFocusOpenSearch, 80);
}

function stdGridBindEditor($tr) {
    if (!$tr || !$tr.length) { return; }

    stdGridInitItemSelect($tr);

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
        var isLastField = $(this).is(stdGridFields($tr).last());

        if (e.key === 'Enter') {
            e.preventDefault();

            if (isLastField) {
                stdGridLastField();
            } else {
                stdGridMoveNext($(this));
            }

            return;
        }

        /* TAB di kolom terakhir (Keterangan) → simpan baris + buka baris baru */
        if (e.key === 'Tab' && !e.shiftKey && isLastField) {
            e.preventDefault();
            stdGridLastField();
            return;
        }

        /* ESC ditangani global (document keydown capture) agar juga berfungsi
           saat fokus masih di dalam baris draft/edit — lihat stdGridBindGlobalEsc(). */
    });

    /* tombol aksi di dalam baris editor */
    $tr.on('click.stdgrid', '.std-btn', function () {
        var act = $(this).data('act');

        if (act === 'save') {
            stdGridSave();
        } else if (act === 'cancel') {
            stdGridCancel();
        }
    });
}

/* Tab/Enter di kolom terakhir: simpan lalu buka baris baru.
   Bila baris masih kosong → fokus ke kolom pertama tanpa error
   (persis perilaku pmkAddLastField() di modul pmk_brng). */
function stdGridLastField() {
    var $tr = stdGridEditor();

    if (!$tr.length) { return; }

    var idbarang = ($tr.find('select.std-item').val() || '').toString().trim();
    var newcost = ($tr.find('.std-newcost').val() || '').toString().trim();
    var desc = ($tr.find('.std-description').val() || '').toString().trim();

    if (idbarang === '' && newcost === '' && desc === '') {
        stdGridFocusEditor();
        return;
    }

    stdGridSave(true);
}

function stdGridMoveNext($el) {
    var $tr = $el.closest('tr');
    var $fields = stdGridFields($tr);
    var idx = $fields.index($el);

    if (idx >= 0 && idx < $fields.length - 1) {
        var $next = $fields.eq(idx + 1);

        if ($next.hasClass('select2-hidden-accessible')) {
            stdGridOpenSelect2($next);
        } else {
            $next.focus().select();
        }

        return;
    }

    /* kolom terakhir → simpan lalu baris baru */
    stdGridSave(true);
}

function stdGridCancel() {
    stdGridRemoveEditor();
    // re-render: pulihkan baris tersimpan (untuk mode edit) & kembalikan empty state
    stdGridRender();
}


/* ------------------------------------------------------------------ */
/* SIMPAN BARIS                                                       */
/* ------------------------------------------------------------------ */
function stdGridSave(openNext) {
    var $tr = stdGridEditor();

    if (!$tr.length) { return; }
    if (stdGridState.saving) { return; }

    var $item = $tr.find('select.std-item');
    var idbarang = ($item.val() || '').toString().trim();

    if (idbarang === '') {
        stdGridMarkInvalid($item, 'ID Barang wajib dipilih');
        stdGridOpenSelect2($item);
        return;
    }

    var $newcost = $tr.find('.std-newcost');
    var newcostRaw = $newcost.val();

    if ($.trim(String(newcostRaw || '')) === '') {
        stdGridMarkInvalid($newcost, 'New Std Cost wajib diisi');
        $newcost.focus().select();
        return;
    }

    var activedate = $.trim($('#activedate').val() || '');

    if (activedate === '') {
        stdGridToast('Activate Date tidak boleh kosong!', 'warning');
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
    formData.append('actualcost', stdGridNum($tr.find('.std-actualcost').val()));
    formData.append('lastcost', stdGridNum($tr.find('.std-lastcost').val()));
    formData.append('newcost', stdGridNum(newcostRaw));

    /* HEADER — identik dengan save_standart_cost() versi modal */
    formData.append('docref', $('#docref').val());
    formData.append('cabang', $('#cabang').val());
    formData.append('pemohon', $('#pemohon').val());
    formData.append('docno', $('#docno').val());
    formData.append('docdate', $('#docdate').val());
    formData.append('activedate', $('#activedate').val());
    formData.append('penyesuaian_a', $('#penyesuaian_a').val());
    formData.append('penyesuaian_b', $('#penyesuaian_b').val());
    formData.append('idcostcenter', $('#idcostcenter').val());
    formData.append('keterangan', $('#keterangan').val());
    formData.append('idlocation', $('#idlocation').val());
    formData.append('description', $('#description').val());

    formData.set(
        'docno',
        $('#prefix').val() + '/' +
        $('#infix').val() + '/' +
        $('#suffix').val()
    );

    stdGridState.saving = true;

    var $btn = $tr.find('[data-act="save"]');
    $btn.prop('disabled', true);

    $.ajax({
        url: HOST_URL + 'production/trans/save_standart_cost_mst',
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

                stdGridRemoveEditor();
                stdGridLoadRows(openNext === true);
            } else {
                stdGridToast(res.message || 'Gagal menyimpan data', 'warning');
            }
        },

        error: function () {
            stdGridToast('Terjadi kesalahan server', 'error');
        },

        complete: function () {
            stdGridState.saving = false;
            $btn.prop('disabled', false);
        }
    });
}


/* ------------------------------------------------------------------ */
/* HAPUS & EDIT                                                       */
/* ------------------------------------------------------------------ */
function stdGridCheckedIds() {
    var ids = [];

    stdGridBody().find('.row-check:checked').each(function () {
        ids.push($(this).val());
    });

    return ids;
}

function stdGridDelete(ids) {
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
            url: HOST_URL + 'production/trans/delete_standart_cost_dtl',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json'
        }).done(function (res) {
            if (res && res.status) {
                // tanpa popup sukses (percepatan input administrasi)
                stdGridRemoveEditor();
                $('#checkAll').prop('checked', false);
                stdGridLoadRows(false);
            } else {
                stdGridToast((res && res.message) || 'Gagal hapus data', 'error');
            }
        }).fail(function () {
            stdGridToast('Gagal hapus data', 'error');
        });
    });
}

function stdGridDeleteRow(uniqueid) {
    if (!uniqueid) { return; }
    stdGridDelete([uniqueid]);
}

function stdGridDeleteSelected() {
    var ids = stdGridCheckedIds();

    if (ids.length === 0) {
        stdGridToast('Pilih data yang akan dihapus', 'warning');
        return;
    }

    stdGridDelete(ids);
}

function stdGridEditSelected() {
    var ids = stdGridCheckedIds();

    if (ids.length === 0) {
        stdGridToast('Pilih satu data yang akan diupdate', 'warning');
        return;
    }

    if (ids.length > 1) {
        stdGridToast('Update hanya boleh satu data', 'warning');
        return;
    }

    stdGridEditRow(ids[0]);
}

/* ------------------------------------------------------------------ */
/* INIT                                                               */
/* ------------------------------------------------------------------ */
$(function () {
    if (!stdGridExists()) { return; }

    /* aksi per baris + empty state */
    stdGridBody()
        .off('.stdgrid')
        .on('click.stdgrid', 'button[data-act="edit"]', function () {
            stdGridEditRow($(this).closest('tr').attr('data-uniqueid'));
        })
        .on('click.stdgrid', 'button[data-act="delete"]', function () {
            stdGridDeleteRow($(this).closest('tr').attr('data-uniqueid'));
        })
        .on('click.stdgrid', 'tr.std-empty-row', function () {
            stdGridNewDraft();
        })
        .on('keydown.stdgrid', 'tr.std-empty-row', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                stdGridNewDraft();
            }
        })
        .on('change.stdgrid', '.row-check', function () {
            var total = stdGridBody().find('.row-check').length;
            var checked = stdGridBody().find('.row-check:checked').length;

            $(this).closest('tr').toggleClass('std-row-checked', this.checked);
            $('#checkAll').prop('checked', total > 0 && total === checked);
            stdGridUpdateSelectedInfo();
        });

    /* select all */
    $(document).off('change.stdgridall').on('change.stdgridall', '#checkAll', function () {
        var on = this.checked;

        stdGridBody().find('.row-check').prop('checked', on);
        stdGridBody().find('tr.std-row-saved').toggleClass('std-row-checked', on);
        stdGridUpdateSelectedInfo();
    });

    /* shortcut tambahan: Alt+N (sama seperti pmk_brng) */
    $(document).off('keydown.stdgridnew').on('keydown.stdgridnew', function (e) {
        if (e.altKey && (e.key === 'n' || e.key === 'N')) {
            e.preventDefault();
            stdGridNewDraft();
        }
    });

    /* Setiap dropdown select2 grid dibuka (klik / Tab / programatik)
       → fokus otomatis ke kotak pencarian agar user bisa langsung mengetik. */
    $(document).off('select2:open.stdgrid').on('select2:open.stdgrid', function () {
        stdGridFocusOpenSearch();
        window.setTimeout(stdGridFocusOpenSearch, 0);
        window.setTimeout(stdGridFocusOpenSearch, 80);
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
            stdGridMoveNext($select);
        }
    });

    /* Search detail (pengganti kotak search DataTables) */
    $('#stdGridSearch')
        .off('input.stdgridsearch')
        .on('input.stdgridsearch', function () {
            stdGridState.search = $(this).val();
            stdGridRender();
        });

    /* Klik header kolom = sort asc/desc (pengganti ordering DataTables) */
    stdGridTable()
        .find('thead th.std-sortable')
        .off('click.stdgridsort')
        .on('click.stdgridsort', function () {
            var key = $(this).data('key');

            if (!key) { return; }

            if (stdGridState.sort.key === key) {
                stdGridState.sort.dir = (stdGridState.sort.dir === 'asc') ? 'desc' : 'asc';
            } else {
                stdGridState.sort.key = key;
                stdGridState.sort.dir = 'asc';
            }

            stdGridRender();
        });

    /* Klik di kolom mana saja pada baris tersimpan → centang (behaviour lama) */
    stdGridBody().on('click.stdgrid', 'tr.std-row-saved', function (e) {
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
        stdGridCancel();
    }, true);

    /* Tombol Hapus di toolbar (hapus massal baris yang dicentang) */
    $(document).off('click.stdgriddelete').on('click.stdgriddelete', '#stdDeleteSelected', function (e) {
        e.preventDefault();
        stdGridDeleteSelected();
    });

    stdGridLoadRows(false);
});

