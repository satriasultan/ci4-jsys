/* =========================================================================
 * GRID DETAIL BPNM — INLINE (gaya pmk_brng)
 * -------------------------------------------------------------------------
 * - Tanpa DataTables: baris dimuat dari endpoint JSON
 *   production/trans/list_bpnm_dtl_grid.
 * - Tambah baris (+ / Alt+N / Ctrl+Q) → satu baris editor di dalam tabel.
 * - Simpan baris → production/trans/save_bpnm_mst (payload SAMA dengan modal),
 *   sukses tanpa popup dan langsung buka baris baru.
 * - Edit in-place (data dari state, tanpa AJAX), Esc membatalkan/memulihkan.
 * - Hapus massal baris tercentang → production/trans/delete_bpnm_detail.
 * - Khusus BPNM:
 *     · checkbox Sub (issub) → Enter men-toggle; T = Substitute Item
 *       (list_item_std_cost, min 2 karakter, harga = newcost),
 *       F = BOM Item (list_item_bom, qty ikut item BOM).
 *     · Nilai = Qty × Harga (read-only, dihitung ulang saat qty/harga berubah).
 *     · Kolom Spec hanya tampil (tidak dipakai backend BPNM).
 * ========================================================================= */

var bnmGridState = {
    rows: [],
    saving: false,
    editing: null,
    query: '',
    sortKey: '',
    sortDir: 1
};

function bnmGridBody() {
    return $('#bnmGridBody');
}

function bnmGridExists() {
    return bnmGridBody().length > 0;
}

function bnmGridEditor() {
    return bnmGridBody().find('tr.bnm-row-draft, tr.bnm-row-editing').first();
}

/* ------------------------------------------------------------------ */
/* HELPER                                                             */
/* ------------------------------------------------------------------ */
function bnmGridEsc(v) {
    return String(v == null ? '' : v)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

if (typeof window.convertToDbNumber !== 'function') {
    window.convertToDbNumber = function (value) {
        if (value === null || value === undefined) { return ''; }
        var s = String(value).replace(/,/g, '').trim();
        if (s === '') { return ''; }
        var n = parseFloat(s);
        return isFinite(n) ? String(n) : '';
    };
}

function bnmGridNum(v) {
    if (v === null || v === undefined) { return 0; }
    var s = String(v).replace(/,/g, '').trim();
    if (s === '') { return 0; }
    var n = parseFloat(s);
    return isFinite(n) ? n : 0;
}

function bnmGridDec(v, d) {
    var dec = (d == null ? 4 : d);

    /* pemisah ribuan seperti grid pilot (standart_cost) */
    return bnmGridNum(v).toLocaleString('en-US', {
        minimumFractionDigits: dec,
        maximumFractionDigits: dec
    });
}

/* Set nilai numerik ke input + jalankan formatter ribuan halaman (bila ada) */
function bnmGridSetNum($el, value) {
    if (!$el || !$el.length) { return; }

    $el.val(bnmGridDec(value, 4));

    if (typeof jaccseparator === 'function') {
        try { jaccseparator($el[0]); } catch (e) { /* abaikan */ }
    } else if (typeof _jtsseparator === 'function') {
        try { _jtsseparator($el[0]); } catch (e2) { /* abaikan */ }
    }
}

/* Fallback format saat field ditinggalkan (bila fungsi global tidak tersedia) */
function bnmGridFormatInput($el) {
    var raw = String($el.val() || '').replace(/,/g, '').trim();

    if (raw === '') { return; }

    var n = parseFloat(raw);

    if (!isFinite(n)) { return; }

    $el.val(n.toLocaleString('en-US', {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4
    }));
}

function bnmGridToast(msg, icon) {
    if (typeof Swal === 'undefined') { return; }

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon || 'warning',
        title: msg,
        showConfirmButton: false,
        timer: 2200,
        timerProgressBar: true
    });
}

/* ------------------------------------------------------------------ */
/* LOAD                                                               */
/* ------------------------------------------------------------------ */
function bnmGridLoadRows(focusDraft) {
    $.ajax({
        url: HOST_URL + 'production/trans/list_bpnm_dtl_grid',
        type: 'GET',
        dataType: 'json',
        data: { docno: ($('#docno').val() || '').toString().trim() }
    }).done(function (res) {
        var payload = (res && res.dataTables) ? res.dataTables : res;

        bnmGridState.rows = (payload && payload.items) ? payload.items : [];
        bnmGridRender();

        if (focusDraft === true) {
            bnmGridNewDraft();
        }
    }).fail(function () {
        bnmGridToast('Gagal memuat data detail', 'error');
    });
}

function bnmGridReload() {
    bnmGridLoadRows(false);
}

/* ------------------------------------------------------------------ */
/* FILTER + SORT (client side)                                        */
/* ------------------------------------------------------------------ */
function bnmGridVisibleRows() {
    var q = $.trim(bnmGridState.query || '').toLowerCase();

    var rows = (bnmGridState.rows || []).filter(function (r) {
        if (q === '') { return true; }

        var hay = [r.idbarang, r.nmbarang, r.unit, r.description].join(' ').toLowerCase();

        return hay.indexOf(q) !== -1;
    });

    var key = bnmGridState.sortKey;

    if (key) {
        var dir = bnmGridState.sortDir;

        rows = rows.slice().sort(function (a, b) {
            if (key === 'qty' || key === 'harga' || key === 'nilai') {
                return (bnmGridNum(a[key]) - bnmGridNum(b[key])) * dir;
            }

            var va = String(a[key] == null ? '' : a[key]).toLowerCase();
            var vb = String(b[key] == null ? '' : b[key]).toLowerCase();

            if (va < vb) { return -1 * dir; }
            if (va > vb) { return 1 * dir; }
            return 0;
        });
    }

    return rows;
}

function bnmGridEmptyRow() {
    return '' +
        '<tr class="bnm-empty-row">' +
        '  <td colspan="11" tabindex="0">' +
        '      <i class="fa fa-plus-circle mr-2"></i>' +
        '      Klik di sini (atau tekan Alt+N / Ctrl+Q) untuk menambah baris biaya' +
        '  </td>' +
        '</tr>';
}

/* ------------------------------------------------------------------ */
/* RENDER                                                             */
/* ------------------------------------------------------------------ */
function bnmGridRender() {
    if (!bnmGridExists()) { return; }

    var $body   = bnmGridBody();
    var $editor = bnmGridEditor();

    /* baris editor dipindahkan sementara agar tidak hilang saat body diganti */
    if ($editor.length) { $editor.detach(); }

    var rows = bnmGridVisibleRows();
    var html = '';

    if (rows.length === 0) {
        html = bnmGridEmptyRow();
    } else {
        $.each(rows, function (i, row) {
            html += bnmGridSavedRow(row);
        });
    }

    $body.html(html);

    $('#checkAll').prop('checked', false);

    if ($editor.length) {
        $body.append($editor);
    }

    $('#tmp_bpnmdtl').toggleClass('bnm-editing', bnmGridEditor().length > 0);

    bnmGridUpdateSelectedInfo();
}

function bnmGridSavedRow(row) {
    var id   = bnmGridEsc(row.uniqueid || '');
    var desc = $.trim(row.description || '');
    var nmb  = $.trim(row.nmbarang || '');
    var sub  = (String(row.issub || 'F').toUpperCase() === 'T');

    return '' +
        '<tr class="bnm-row-saved" data-uniqueid="' + id + '" data-idurut="' + bnmGridEsc(row.idurut || '') + '">' +
        '  <td class="bnm-col-no"><input type="checkbox" class="row-check" value="' + id + '" title="Pilih baris (hapus massal: Ctrl+D)"></td>' +
        '  <td class="text-center"><input type="checkbox" disabled ' + (sub ? 'checked' : '') + ' title="Substitute Item"></td>' +
        '  <td title="' + bnmGridEsc(row.idbarang) + '">' + bnmGridEsc(row.idbarang) + '</td>' +
        '  <td title="' + bnmGridEsc(nmb) + '"><span class="bnm-readonly-text">' + bnmGridEsc(nmb) + '</span></td>' +
        '  <td>' + bnmGridEsc(row.spec) + '</td>' +
        '  <td class="text-end"><span class="bnm-readonly-number" data-export="' + bnmGridNum(row.qty) + '">' + bnmGridDec(row.qty, 4) + '</span></td>' +
        '  <td class="text-center">' + bnmGridEsc(row.unit) + '</td>' +
        '  <td class="text-end"><span class="bnm-readonly-number" data-export="' + bnmGridNum(row.harga) + '">' + bnmGridDec(row.harga, 4) + '</span></td>' +
        '  <td class="text-end"><span class="bnm-readonly-number" data-export="' + bnmGridNum(row.nilai) + '">' + bnmGridDec(row.nilai, 4) + '</span></td>' +
        '  <td title="' + bnmGridEsc(desc) + '"><span class="bnm-readonly-text">' + bnmGridEsc(desc) + '</span></td>' +
        '  <td class="bnm-col-aksi">' +
        '      <button type="button" class="bnm-btn bnm-btn-edit" data-act="edit" title="Edit baris">' +
        '          <i class="fa fa-pencil"></i></button>' +
        '  </td>' +
        '</tr>';
}

/* ------------------------------------------------------------------ */
/* BARIS EDITOR                                                       */
/* ------------------------------------------------------------------ */
function bnmGridEditorRow() {
    var sel = 'style="width:100%"';

    return '' +
        '<tr class="bnm-row-draft">' +
        '  <td class="bnm-col-no"><input type="checkbox" disabled title="Baris belum tersimpan"></td>' +
        '  <td class="text-center">' +
        '      <input type="checkbox" class="bnm-issub" title="Enter: toggle. Centang = Substitute Item (T), kosong = BOM Item (F)">' +
        '  </td>' +
        '  <td class="bnm-col-item">' +
        '      <div class="bnm-item-bom-wrap"><select class="bnm-item-bom" ' + sel + '></select></div>' +
        '      <div class="bnm-item-sub-wrap" style="display:none"><select class="bnm-item-sub" ' + sel + '></select></div>' +
        '  </td>' +
        '  <td><input type="text" class="form-control form-control-sm bnm-nmbarang" readonly tabindex="-1"></td>' +
        '  <td class="text-center bnm-col-spec"><span class="bnm-readonly-text">—</span></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end jaccseparator bnm-qty"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-center bnm-unit" readonly tabindex="-1"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end jaccseparator bnm-harga"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end bnm-nilai" readonly tabindex="-1"></td>' +
        '  <td><input type="text" class="form-control form-control-sm bnm-description"></td>' +
        '  <td class="bnm-col-aksi">' +
        '      <button type="button" class="bnm-btn bnm-btn-save" data-act="save" title="Simpan baris (Enter)">' +
        '          <i class="fa fa-check"></i></button>' +
        '      <button type="button" class="bnm-btn bnm-btn-cancel" data-act="cancel" title="Batal (Esc)">' +
        '          <i class="fa fa-times"></i></button>' +
        '  </td>' +
        '</tr>';
}


/* ------------------------------------------------------------------ */
/* SELECT2: ITEM BOM / ITEM SUBSTITUTE                                */
/* ------------------------------------------------------------------ */
function bnmGridFormatItem(repo) {
    if (repo.loading) { return repo.text; }

    var idb = repo.idbarang || repo.id || '';
    var nmb = repo.nmbarang || repo.text || '';

    return "<div class='select2-result-repository__description'>" +
        bnmGridEsc(idb) + "   <i class='fa fa-circle-o'></i>   " + bnmGridEsc(nmb) +
        "</div>";
}

function bnmGridFormatItemSel(repo) {
    return repo.nmbarang || repo.text;
}

function bnmGridItemDefaults() {
    var grp = (typeof defaultInitialGroupBrng === 'undefined') ? '' : defaultInitialGroupBrng;
    return { grp: grp };
}

function bnmGridCloseSelect2($select) {
    if (!$select || !$select.length) { return; }
    if (!$select.hasClass('select2-hidden-accessible')) { return; }

    var inst = $select.data('select2');

    if (inst && typeof inst.isOpen === 'function' && inst.isOpen()) {
        try { $select.select2('close'); } catch (e) { /* abaikan */ }
    }
}

function bnmGridFixSelectWidth($el) {
    if (!$el || !$el.length) { return; }
    if (!$el.hasClass('select2-hidden-accessible')) { return; }

    var $container = $el.next('.select2-container');

    if ($container.length) {
        $container.css('width', '100%');
    }
}

function bnmGridSearchField($select) {
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

/* Buka select2 + fokuskan kotak pencarian milik instance tersebut */
function bnmGridFocusOpenSearch($el) {
    if (!$el || !$el.length) { return; }

    if (!$el.hasClass('select2-hidden-accessible')) {
        $el.trigger('focus');
        return;
    }

    if (!$.contains(document, $el[0])) { return; }

    var inst = $el.data('select2');

    if (!(inst && typeof inst.isOpen === 'function' && inst.isOpen())) {
        try { $el.select2('open'); } catch (e) { /* abaikan */ }
    }

    var focusBox = function () {
        var $search = bnmGridSearchField($el);

        if (!$search.length || !$.contains(document, $search[0])) { return; }

        try { $search.trigger('focus'); } catch (e) { /* abaikan */ }

        if (document.activeElement !== $search[0] && typeof $search[0].focus === 'function') {
            try { $search[0].focus(); } catch (e2) { /* abaikan */ }
        }
    };

    focusBox();
    window.setTimeout(focusBox, 0);
    window.setTimeout(focusBox, 80);
    window.setTimeout(focusBox, 160);
}

function bnmGridActiveSelect($tr) {
    $tr = ($tr && $tr.length) ? $tr : bnmGridEditor();

    return $tr.find('.bnm-issub').prop('checked')
        ? $tr.find('.bnm-item-sub')
        : $tr.find('.bnm-item-bom');
}

function bnmGridActiveItem($tr) {
    $tr = ($tr && $tr.length) ? $tr : bnmGridEditor();

    return $.trim(bnmGridActiveSelect($tr).val() || '').toString();
}

/* Nilai = Qty × Harga (mengikuti hitungTotalCost() versi modal) */
function bnmGridRecalcNilai($tr) {
    var qty   = bnmGridNum($tr.find('.bnm-qty').val());
    var harga = bnmGridNum($tr.find('.bnm-harga').val());

    bnmGridSetNum($tr.find('.bnm-nilai'), qty * harga);
}


function bnmGridInitSelects($tr) {
    var $bom = $tr.find('.bnm-item-bom');
    var $sub = $tr.find('.bnm-item-sub');
    var def  = bnmGridItemDefaults();

    /* ---- BOM Item ---- */
    if (!$bom.hasClass('select2-hidden-accessible')) {
        $bom.select2({
            placeholder: 'Choose BOM Item',
            allowClear: true,
            width: '100%',
            dropdownParent: document.body,
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_item_bom',
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
                        wono: ($('#wono').val() || ''),
                        term: params.term
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    return {
                        results: (data && data.items) ? data.items : [],
                        pagination: {
                            more: (params.page * 20) < ((data && data.total_count) || 0)
                        }
                    };
                },
                cache: false
            },
            escapeMarkup: function (markup) { return markup; },
            templateResult: bnmGridFormatItem,
            templateSelection: bnmGridFormatItemSel
        }).on('select2:select', function (e) {
            var data = e.params.data || {};
            var $row = $(this).closest('tr');

            $row.find('.bnm-nmbarang').val($.trim(data.nmbarang || ''));
            $row.find('.bnm-unit').val($.trim(data.unit || ''));
            bnmGridSetNum($row.find('.bnm-qty'), data.qty);

            var harga = (data.newcost != null) ? data.newcost : data.harga;

            if (harga != null && $.trim(String(harga)) !== '') {
                bnmGridSetNum($row.find('.bnm-harga'), harga);
            }

            bnmGridClearInvalid($row.find('.bnm-item-bom'));
            bnmGridRecalcNilai($row);
        }).on('select2:clear', function () {
            var $row = $(this).closest('tr');

            $row.find('.bnm-nmbarang').val('');
            $row.find('.bnm-unit').val('');
            $row.find('.bnm-qty').val('');
            $row.find('.bnm-harga').val('');

            bnmGridRecalcNilai($row);
        });
    }

    bnmGridInitSubSelect($tr, $sub, def);
}


/* ---- Substitute Item (modal: minimumInputLength 2) ---- */
function bnmGridInitSubSelect($tr, $sub, def) {
    if ($sub.hasClass('select2-hidden-accessible')) { return; }

    $sub.select2({
        placeholder: 'Choose Substitute Item',
        allowClear: true,
        width: '100%',
        minimumInputLength: 2,
        dropdownParent: document.body,
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
                    _paramglobal_: def.grp,
                    _parameterx_: def.grp,
                    loccode: '',
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: (data && data.items) ? data.items : [],
                    pagination: {
                        more: (params.page * 20) < ((data && data.total_count) || 0)
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) { return markup; },
        templateResult: bnmGridFormatItem,
        templateSelection: bnmGridFormatItemSel
    }).on('select2:select', function (e) {
        var data = e.params.data || {};
        var $row = $(this).closest('tr');

        $row.find('.bnm-nmbarang').val($.trim(data.nmbarang || ''));
        $row.find('.bnm-unit').val($.trim(data.unit || ''));
        bnmGridSetNum($row.find('.bnm-harga'), data.newcost);

        bnmGridClearInvalid($row.find('.bnm-item-sub'));
        bnmGridRecalcNilai($row);
    }).on('select2:clear', function () {
        var $row = $(this).closest('tr');

        $row.find('.bnm-nmbarang').val('');
        $row.find('.bnm-unit').val('');
        $row.find('.bnm-harga').val('');

        bnmGridRecalcNilai($row);
    });
}

/* Toggle Substitute (T) / BOM (F) */
function bnmGridApplyIssub($tr, isSub, opts) {
    opts = opts || {};

    $tr.find('.bnm-issub').prop('checked', !!isSub);

    if (isSub === true) {
        bnmGridCloseSelect2($tr.find('.bnm-item-bom'));

        $tr.find('.bnm-item-bom-wrap').hide();
        $tr.find('.bnm-item-sub-wrap').show();

        $tr.find('.bnm-item-bom').val(null).trigger('change');
    } else {
        bnmGridCloseSelect2($tr.find('.bnm-item-sub'));

        $tr.find('.bnm-item-sub-wrap').hide();
        $tr.find('.bnm-item-bom-wrap').show();

        $tr.find('.bnm-item-sub').val(null).trigger('change');
    }

    if (opts.clearFields !== false) {
        $tr.find('.bnm-nmbarang').val('');
        $tr.find('.bnm-unit').val('');
        $tr.find('.bnm-qty').val('');
        $tr.find('.bnm-harga').val('');
        bnmGridRecalcNilai($tr);
    }

    if (opts.focus !== false) {
        var $active = bnmGridActiveSelect($tr);

        bnmGridFixSelectWidth($active);
        bnmGridFocusOpenSearch($active);
    }
}


/* ------------------------------------------------------------------ */
/* BUKA / ISI / BATAL BARIS EDITOR                                    */
/* ------------------------------------------------------------------ */
function bnmGridFocusEditor() {
    var $tr = bnmGridEditor();

    if (!$tr.length) { return; }

    bnmGridFocusOpenSearch(bnmGridActiveSelect($tr));
}

function bnmGridFillEditor($tr, data) {
    var isSub = (String(data.issub || 'F').toUpperCase() === 'T');

    bnmGridApplyIssub($tr, isSub, { clearFields: false, focus: false });

    /* BPNM menyimpan idbarang (bukan idbarang_bom), jadi untuk baris F
       nilai state `idbarang` dipakai sebagai item BOM terpilih. */
    if (isSub) {
        if (data.idbarang) {
            $tr.find('.bnm-item-sub')
                .append(new Option(data.nmbarang || data.idbarang, data.idbarang, true, true))
                .val(data.idbarang).trigger('change');
        }
    } else {
        var idb = data.idbarang_bom || data.idbarang;

        if (idb) {
            $tr.find('.bnm-item-bom')
                .append(new Option(data.nmbarang || idb, idb, true, true))
                .val(idb).trigger('change');
        }
    }

    $tr.find('.bnm-nmbarang').val(data.nmbarang || '');
    $tr.find('.bnm-unit').val(data.unit || '');
    bnmGridSetNum($tr.find('.bnm-qty'), data.qty);
    bnmGridSetNum($tr.find('.bnm-harga'), data.harga);
    $tr.find('.bnm-description').val(data.description || '');

    bnmGridRecalcNilai($tr);

    bnmGridFixSelectWidth($tr.find('.bnm-item-bom'));
    bnmGridFixSelectWidth($tr.find('.bnm-item-sub'));
}

function bnmGridNewDraft() {
    if (!bnmGridExists()) { return; }

    var $existing = bnmGridEditor();

    if ($existing.length) {
        bnmGridFocusOpenSearch(bnmGridActiveSelect($existing));
        return;
    }

    var $body = bnmGridBody();

    $body.find('tr.bnm-empty-row').remove();
    $body.append(bnmGridEditorRow());

    var $tr = bnmGridEditor();

    bnmGridInitSelects($tr);
    bnmGridApplyIssub($tr, false, { clearFields: false, focus: true });

    $('#tmp_bpnmdtl').addClass('bnm-editing');
    bnmGridUpdateSelectedInfo();
}

function bnmGridRemoveEditor() {
    bnmGridEditor().remove();
    $('#tmp_bpnmdtl').removeClass('bnm-editing');
}

function bnmGridCancel() {
    var $tr = bnmGridEditor();

    if (!$tr.length) { return; }

    bnmGridRemoveEditor();
    bnmGridRender();
}

/* Edit in-place: data diambil dari state (tanpa AJAX) */
function bnmGridEditRow(uniqueid) {
    var id = $.trim(uniqueid || '').toString();

    if (id === '') { return; }

    var row = null;

    $.each(bnmGridState.rows || [], function (i, r) {
        if (String(r.uniqueid) === id) { row = r; return false; }
    });

    if (!row) {
        bnmGridToast('Data baris tidak ditemukan, muat ulang halaman', 'warning');
        return;
    }

    var $target = bnmGridBody().find('tr.bnm-row-saved[data-uniqueid="' + id + '"]');

    if (!$target.length) { return; }

    var $current = bnmGridEditor();

    if ($current.length) {
        bnmGridRemoveEditor();
        bnmGridRender();

        $target = bnmGridBody().find('tr.bnm-row-saved[data-uniqueid="' + id + '"]');

        if (!$target.length) { return; }
    }

    $target.replaceWith(bnmGridEditorRow());

    var $tr = bnmGridEditor();

    $tr.removeClass('bnm-row-draft').addClass('bnm-row-editing')
        .attr('data-uniqueid', id)
        .attr('data-idurut', row.idurut || '');

    bnmGridInitSelects($tr);
    bnmGridFillEditor($tr, row);

    $('#tmp_bpnmdtl').addClass('bnm-editing');
    bnmGridUpdateSelectedInfo();

    bnmGridFocusOpenSearch(bnmGridActiveSelect($tr));
}

function bnmGridEditSelected() {
    var ids = bnmGridCheckedIds();

    if (ids.length === 0) {
        bnmGridToast('Pilih satu baris yang akan diedit', 'warning');
        return;
    }

    if (ids.length > 1) {
        bnmGridToast('Edit hanya boleh satu baris', 'warning');
        return;
    }

    bnmGridEditRow(ids[0]);
}


/* ------------------------------------------------------------------ */
/* VALIDASI (tooltip, tanpa Swal)                                     */
/* ------------------------------------------------------------------ */
function bnmGridMarkInvalid($el, msg) {
    if (!$el || !$el.length) { return; }

    $el.addClass('bnm-invalid');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.next('.select2-container').find('.select2-selection--single').addClass('bnm-invalid');
    }

    $el.attr('title', msg || 'Wajib diisi');

    if (typeof $el.tooltip === 'function') {
        try { $el.tooltip('dispose').tooltip({ trigger: 'manual' }).tooltip('show'); } catch (e) { /* noop */ }
    }
}

function bnmGridClearInvalid($el) {
    if (!$el || !$el.length) { return; }

    $el.removeClass('bnm-invalid').removeAttr('title');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.next('.select2-container').find('.select2-selection--single').removeClass('bnm-invalid');
    }

    if (typeof $el.tooltip === 'function') {
        try { $el.tooltip('dispose'); } catch (e) { /* noop */ }
    }
}

function bnmGridUpdateSelectedInfo() {
    var $info = $('#bnmSelectedInfo');

    if (!$info.length) { return; }

    var total   = bnmGridBody().find('tr.bnm-row-saved').length;
    var checked = bnmGridBody().find('.row-check:checked').length;

    $info.text(checked + ' dari ' + total + ' baris dipilih');
}

/* ------------------------------------------------------------------ */
/* SIMPAN BARIS                                                       */
/* ------------------------------------------------------------------ */
function bnmGridSave(openNext) {
    var $tr = bnmGridEditor();

    if (!$tr.length || bnmGridState.saving) { return; }

    var isSub    = $tr.find('.bnm-issub').prop('checked');
    var $item    = bnmGridActiveSelect($tr);
    var idbarang = $.trim($item.val() || '').toString();

    if (idbarang === '') {
        bnmGridMarkInvalid($item, isSub ? 'Substitute Item wajib dipilih' : 'BOM Item wajib dipilih');
        bnmGridFocusOpenSearch($item);
        return;
    }

    var $qty   = $tr.find('.bnm-qty');
    var qtyRaw = $qty.val();

    if ($.trim(String(qtyRaw || '')) === '') {
        bnmGridMarkInvalid($qty, 'Qty wajib diisi');
        $qty.trigger('focus').select();
        return;
    }

    var qty = bnmGridNum(qtyRaw);

    if (qty <= 0) {
        bnmGridMarkInvalid($qty, 'Qty harus lebih besar dari 0');
        $qty.trigger('focus').select();
        return;
    }

    var harga = bnmGridNum($tr.find('.bnm-harga').val());
    var nilai = qty * harga;

    $tr.find('.bnm-nilai').val(bnmGridDec(nilai, 4));

    /* Payload: seluruh field header form ikut, field detail di-set ulang */
    var form     = document.getElementById('formStandarCostMst');
    var formData = form ? new FormData(form) : new FormData();

    formData.set('idurut', ($tr.attr('data-idurut') || '').toString());
    formData.set('uniqueid', ($tr.attr('data-uniqueid') || '').toString());

    formData.set('issub', isSub ? 'T' : 'F');
    formData.set('idbarang', isSub ? idbarang : '');
    formData.set('idbarang_bom', isSub ? '' : idbarang);

    formData.set('nmbarang', $.trim($tr.find('.bnm-nmbarang').val() || '').toString());
    formData.set('unit', $.trim($tr.find('.bnm-unit').val() || '').toString());
    formData.set('qty', String(qty));
    formData.set('harga', String(harga));
    formData.set('nilai', String(nilai));
    formData.set('description', $.trim($tr.find('.bnm-description').val() || '').toUpperCase());

    formData.set(
        'docno',
        ($('#prefix').val() || '') + '/' +
        ($('#infix').val() || '') + '/' +
        ($('#suffix').val() || '')
    );

    /* field header yang tidak ada di form (bila elemennya ada di halaman) */
    $.each(['docref', 'nmbarang_jadi', 'keterangan', 'buildfor', 'buildunit',
            'batchno', 'bagian', 'tabno', 'woeno', 'wono', 'bomno'],
        function (i, name) {
            if (formData.has(name)) { return; }

            var $f = $('#' + name);

            if ($f.length) {
                formData.set(name, $f.val() || '');
            }
        });

    /* field header numerik WAJIB angka murni (hindari "1,234.0000" → numeric) */
    $.each(['buildfor', 'minimumqty'], function (i, name) {
        var $f = $('#' + name);

        if ($f.length) {
            formData.set(name, String(bnmGridNum($f.val())));
        }
    });

    bnmGridState.saving = true;

    var $btn = $tr.find('[data-act="save"]');
    $btn.prop('disabled', true);

    $.ajax({
        url: HOST_URL + 'production/trans/save_bpnm_mst',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,

        success: function (res) {
            if (res && res.success) {
                /* tanpa popup sukses (percepatan input) */

                if (res.reload === true) {
                    window.location.reload();
                    return;
                }

                if (typeof documentReadable === 'function') {
                    documentReadable();
                }

                bnmGridRemoveEditor();
                bnmGridLoadRows(openNext === true);
            } else {
                bnmGridToast((res && res.message) || 'Gagal menyimpan data', 'warning');
            }
        },

        error: function () {
            bnmGridToast('Terjadi kesalahan server', 'error');
        },

        complete: function () {
            bnmGridState.saving = false;
            $btn.prop('disabled', false);
        }
    });
}


/* ------------------------------------------------------------------ */
/* HAPUS                                                              */
/* ------------------------------------------------------------------ */
function bnmGridCheckedIds() {
    var ids = [];

    bnmGridBody().find('.row-check:checked').each(function () {
        ids.push($(this).val());
    });

    return ids;
}

function bnmGridDelete(ids) {
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
            url: HOST_URL + 'production/trans/delete_bpnm_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json'
        }).done(function (res) {
            if (res && res.status) {
                /* tanpa popup sukses */
                bnmGridRemoveEditor();
                $('#checkAll').prop('checked', false);
                bnmGridLoadRows(false);

                if (typeof documentReadable === 'function') {
                    documentReadable();
                }
            } else {
                bnmGridToast((res && res.message) || 'Gagal hapus data', 'error');
            }
        }).fail(function () {
            bnmGridToast('Gagal hapus data', 'error');
        });
    });
}

function bnmGridDeleteRow(uniqueid) {
    if (!uniqueid) { return; }

    bnmGridDelete([uniqueid]);
}

function bnmGridDeleteSelected() {
    var ids = bnmGridCheckedIds();

    if (ids.length === 0) {
        bnmGridToast('Pilih baris yang akan dihapus', 'warning');
        return;
    }

    bnmGridDelete(ids);
}

/* ------------------------------------------------------------------ */
/* NAVIGASI KEYBOARD                                                  */
/* ------------------------------------------------------------------ */
function bnmGridFields($tr) {
    return [
        $tr.find('.bnm-issub')[0],
        bnmGridActiveSelect($tr)[0],
        $tr.find('.bnm-qty')[0],
        $tr.find('.bnm-harga')[0],
        $tr.find('.bnm-description')[0]
    ];
}

function bnmGridMoveNext($el) {
    var $tr = bnmGridEditor();

    if (!$tr.length) { return; }

    var fields = bnmGridFields($tr);
    var idx    = -1;

    $.each(fields, function (i, el) {
        if (el === $el[0]) { idx = i; return false; }
    });

    if (idx === -1 || idx >= fields.length - 1) { return; }

    var next = fields[idx + 1];

    if (!next) { return; }

    var $next = $(next);

    if ($next.hasClass('select2-hidden-accessible')) {
        bnmGridFocusOpenSearch($next);
    } else {
        $next.trigger('focus');
    }
}

function bnmGridIsLastField($el) {
    return $el.hasClass('bnm-description');
}


/* ------------------------------------------------------------------ */
/* KEYBOARD TAMBAHAN (Enter pada checkbox Sub)                        */
/* ------------------------------------------------------------------ */
function bnmGridBindIssubEnter() {
    $(document)
        .off('keydown.bnmgridissub')
        .on('keydown.bnmgridissub', '#tmp_bpnmdtl tbody .bnm-issub', function (e) {
            var isEnter = (e.key === 'Enter' || e.keyCode === 13);
            var isSpace = (e.key === ' ' || e.keyCode === 32);

            if (isSpace) { return; } /* biarkan default: toggle */
            if (!isEnter) { return; }

            e.preventDefault();
            e.stopPropagation();

            var $tr  = $(this).closest('tr');
            var next = !$(this).prop('checked');

            bnmGridApplyIssub($tr, next, { focus: true });
        });

    $(document)
        .off('change.bnmgridissub')
        .on('change.bnmgridissub', '#tmp_bpnmdtl tbody .bnm-issub', function () {
            /* klik mouse → perilaku sama: T tampilkan Substitute, F tampilkan BOM */
            bnmGridApplyIssub($(this).closest('tr'), $(this).prop('checked'), { focus: true });
        });
}

/* ------------------------------------------------------------------ */
/* BINDING GRID                                                       */
/* ------------------------------------------------------------------ */
$(function () {
    if (!bnmGridExists()) { return; }

    /* pencarian live (client side) */
    $(document)
        .off('input.bnmgridsearch')
        .on('input.bnmgridsearch', '#bnmGridSearch', function () {
            bnmGridState.query = this.value || '';
            bnmGridRender();
        });

    /* empty state → buka baris baru */
    $(document)
        .off('click.bnmgridempty')
        .on('click.bnmgridempty', '#tmp_bpnmdtl tbody tr.bnm-empty-row td', function () {
            bnmGridNewDraft();
        });

    $(document)
        .off('keydown.bnmgridempty')
        .on('keydown.bnmgridempty', '#tmp_bpnmdtl tbody tr.bnm-empty-row td', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                bnmGridNewDraft();
            }
        });

    /* sort header (▲/▼) */
    $(document)
        .off('click.bnmgridsort')
        .on('click.bnmgridsort', '#tmp_bpnmdtl thead th.bnm-sortable', function () {
            var key = $(this).data('key');

            if (!key) { return; }

            if (bnmGridState.sortKey === key) {
                bnmGridState.sortDir = -bnmGridState.sortDir;
            } else {
                bnmGridState.sortKey = key;
                bnmGridState.sortDir = 1;
            }

            $('#tmp_bpnmdtl thead th').removeClass('bnm-sort-asc bnm-sort-desc');
            $(this).addClass(bnmGridState.sortDir === 1 ? 'bnm-sort-asc' : 'bnm-sort-desc');

            bnmGridRender();
        });

    /* klik baris tersimpan → centang */
    $(document)
        .off('click.bnmgridrow')
        .on('click.bnmgridrow', '#tmp_bpnmdtl tbody tr.bnm-row-saved', function (e) {
            if ($(e.target).is('input, button, i, a')) { return; }

            var $cb = $(this).find('.row-check');

            $cb.prop('checked', !$cb.prop('checked')).trigger('change');
        });

    /* chip info pilihan */
    $(document)
        .off('change.bnmgridcheck')
        .on('change.bnmgridcheck', '#tmp_bpnmdtl tbody .row-check', function () {
            bnmGridUpdateSelectedInfo();
        });

    /* tombol aksi per baris */
    $(document)
        .off('click.bnmgridact')
        .on('click.bnmgridact', '#tmp_bpnmdtl tbody [data-act]', function (e) {
            e.preventDefault();

            var act = String($(this).data('act') || '');
            var $tr = $(this).closest('tr');

            if (act === 'edit') {
                bnmGridEditRow($tr.attr('data-uniqueid'));
            } else if (act === 'save') {
                bnmGridSave(true);
            } else if (act === 'cancel') {
                bnmGridCancel();
            }
        });

    /* tombol Hapus di toolbar */
    $(document)
        .off('click.bnmgriddelete')
        .on('click.bnmgriddelete', '#bnmDeleteSelected', function (e) {
            e.preventDefault();
            bnmGridDeleteSelected();
        });

    /* nilai dihitung ulang saat qty/harga diketik */
    $(document)
        .off('input.bnmgridnilai')
        .on('input.bnmgridnilai', '#tmp_bpnmdtl tbody .bnm-qty, #tmp_bpnmdtl tbody .bnm-harga', function () {
            bnmGridRecalcNilai($(this).closest('tr'));
        });

    /* format ribuan saat field ditinggalkan (fallback bila fungsi global halaman tidak ada) */
    $(document)
        .off('blur.bnmgridnum')
        .on('blur.bnmgridnum', '#tmp_bpnmdtl tbody .bnm-qty, #tmp_bpnmdtl tbody .bnm-harga', function () {
            bnmGridFormatInput($(this));
            bnmGridRecalcNilai($(this).closest('tr'));
        });

    bnmGridBindIssubEnter();


    /* Tab / Enter di dalam baris editor */
    $(document)
        .off('keydown.bnmgridnav')
        .on('keydown.bnmgridnav', '#tmp_bpnmdtl tbody tr', function (e) {
            var $tr = $(this);

            if (!$tr.hasClass('bnm-row-draft') && !$tr.hasClass('bnm-row-editing')) { return; }

            var $t    = $(e.target);
            var $cont = $t.closest('.select2-container');
            var $sel  = $cont.length ? $cont.prev('select') : $();

            var isEnter = (e.key === 'Enter' || e.keyCode === 13);
            var isTab   = (e.key === 'Tab' || e.keyCode === 9);

            /* Tab dari checkbox Sub → langsung ke select item yang aktif */
            if ($t.hasClass('bnm-issub')) {
                if (isTab) {
                    e.preventDefault();
                    bnmGridFocusOpenSearch(bnmGridActiveSelect($tr));
                }
                return;
            }

            if (isEnter) {
                e.preventDefault();

                if ($t.hasClass('bnm-description')) {
                    bnmGridSave(true);
                    return;
                }

                bnmGridMoveNext(($sel.length ? $sel : $t));
                return;
            }

            if (isTab && $t.hasClass('bnm-description')) {
                e.preventDefault();
                bnmGridSave(true);
                return;
            }

            if (isTab && $sel.length) {
                e.preventDefault();
                bnmGridMoveNext($sel);
            }
        });

    /* Esc: capture phase (fokus bisa di select2) */
    document.addEventListener('keydown', function (ev) {
        if (ev.key !== 'Escape' && ev.keyCode !== 27) { return; }

        var $row = bnmGridEditor();

        if (!$row.length) { return; }

        var active = document.activeElement;

        if (!active) { return; }

        var inEditor = ($row[0] === active) ||
                       $row[0].contains(active) ||
                       $(active).closest('.select2-container').length > 0;

        if (!inEditor) { return; }

        ev.preventDefault();
        bnmGridCancel();
    }, true);

    /* shortcut tambahan: Alt+N (Ctrl+Q / Ctrl+D / Ctrl+E ditangani page JS) */
    $(document)
        .off('keydown.bnmgridshortcut')
        .on('keydown.bnmgridshortcut', function (e) {
            if (typeof isTypingTarget === 'function' && isTypingTarget(e.target)) { return; }

            var key = String(e.key || '').toLowerCase();

            if (e.altKey && key === 'n') {
                e.preventDefault();
                bnmGridNewDraft();
            }
        });

    bnmGridLoadRows(false);
});

