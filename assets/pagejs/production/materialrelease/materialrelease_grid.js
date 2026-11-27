/* =========================================================================
 * GRID DETAIL MATERIAL RELEASE — INLINE (gaya pmk_brng)
 * -------------------------------------------------------------------------
 * - Tanpa DataTables: baris dimuat dari endpoint JSON
 *   production/trans/list_materialrelease_dtl_grid.
 * - Tambah baris (+ / Alt+N / Ctrl+Q) → satu baris editor di dalam tabel.
 * - Simpan baris → production/trans/save_materialrelease_mst (payload SAMA
 *   dengan versi modal), sukses tanpa popup dan langsung buka baris baru.
 * - Edit in-place (data dari state, tanpa AJAX), Esc membatalkan/memulihkan.
 * - Hapus massal baris tercentang → production/trans/delete_materialrelease_detail.
 * - Khusus modul ini:
 *     · checkbox Sub (issub) → Enter men-toggle; T = Substitute Item,
 *       F = BOM Item; setelah toggle fokus + buka select2 item yang aktif.
 *     · Spec/Batch dari api/globalmodule/list_batch_item.
 *     · Stok read-only dari api/globalmodule/stock_stkgdw_item
 *       (fallback: nilai stock hasil endpoint grid) + validasi qty ≤ stok
 *       memakai tooltip (tanpa Swal).
 * ========================================================================= */

var mrlGridState = {
    rows: [],
    saving: false,
    editing: null,
    query: '',
    sortKey: '',
    sortDir: 1
};

function mrlGridBody() {
    return $('#mrlGridBody');
}

function mrlGridExists() {
    return mrlGridBody().length > 0;
}

function mrlGridEditor() {
    return mrlGridBody().find('tr.mrl-row-draft, tr.mrl-row-editing').first();
}

/* ------------------------------------------------------------------ */
/* HELPER                                                             */
/* ------------------------------------------------------------------ */
function mrlGridEsc(v) {
    return String(v == null ? '' : v)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/* Fallback aman bila helper global convertToDbNumber tidak tersedia */
if (typeof window.convertToDbNumber !== 'function') {
    window.convertToDbNumber = function (value) {
        if (value === null || value === undefined) { return ''; }
        var s = String(value).replace(/,/g, '').trim();
        if (s === '') { return ''; }
        var n = parseFloat(s);
        return isFinite(n) ? String(n) : '';
    };
}

function mrlGridNum(v) {
    if (v === null || v === undefined) { return 0; }
    var s = String(v).replace(/,/g, '').trim();
    if (s === '') { return 0; }
    var n = parseFloat(s);
    return isFinite(n) ? n : 0;
}

function mrlGridDec(v, d) {
    var n = mrlGridNum(v);
    return n.toLocaleString('en-US', {
        minimumFractionDigits: (d == null ? 4 : d),
        maximumFractionDigits: (d == null ? 4 : d)
    });
}

/* Fallback format ribuan saat field ditinggalkan */
function mrlGridFormatInput($el) {
    var raw = String($el.val() || '').replace(/,/g, '').trim();

    if (raw === '') { return; }

    var n = parseFloat(raw);

    if (!isFinite(n)) { return; }

    $el.val(n.toLocaleString('en-US', {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4
    }));
}

function mrlGridToast(msg, icon) {
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
function mrlGridLoadRows(focusDraft) {
    $.ajax({
        url: HOST_URL + 'production/trans/list_materialrelease_dtl_grid',
        type: 'GET',
        dataType: 'json',
        data: { docno: ($('#docno').val() || '').toString().trim() }
    }).done(function (res) {
        var payload = (res && res.dataTables) ? res.dataTables : res;

        mrlGridState.rows = (payload && payload.items) ? payload.items : [];
        mrlGridRender();

        if (focusDraft === true) {
            mrlGridNewDraft();
        }
    }).fail(function () {
        mrlGridToast('Gagal memuat data detail', 'error');
    });
}

function mrlGridReload() {
    mrlGridLoadRows(false);
}

/* ------------------------------------------------------------------ */
/* FILTER + SORT (client side)                                        */
/* ------------------------------------------------------------------ */
function mrlGridVisibleRows() {
    var q = $.trim(mrlGridState.query || '').toLowerCase();

    var rows = (mrlGridState.rows || []).filter(function (r) {
        if (q === '') { return true; }

        var hay = [
            r.idbarang, r.nmbarang, r.spec, r.unit, r.description
        ].join(' ').toLowerCase();

        return hay.indexOf(q) !== -1;
    });

    var key = mrlGridState.sortKey;

    if (key) {
        var dir = mrlGridState.sortDir;

        rows = rows.slice().sort(function (a, b) {
            if (key === 'qty' || key === 'stock') {
                return (mrlGridNum(a[key]) - mrlGridNum(b[key])) * dir;
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

function mrlGridEmptyRow() {
    return '' +
        '<tr class="mrl-empty-row">' +
        '  <td colspan="10" tabindex="0">' +
        '      <i class="fa fa-plus-circle mr-2"></i>' +
        '      Klik di sini (atau tekan Alt+N / Ctrl+Q) untuk menambah baris material' +
        '  </td>' +
        '</tr>';
}

/* ------------------------------------------------------------------ */
/* RENDER                                                             */
/* ------------------------------------------------------------------ */
function mrlGridRender() {
    if (!mrlGridExists()) { return; }

    var $body   = mrlGridBody();
    var $editor = mrlGridEditor();

    /* baris editor dipindahkan sementara agar tidak hilang saat body diganti */
    if ($editor.length) { $editor.detach(); }

    var rows = mrlGridVisibleRows();
    var html = '';

    if (rows.length === 0) {
        html = mrlGridEmptyRow();
    } else {
        $.each(rows, function (i, row) {
            html += mrlGridSavedRow(row);
        });
    }

    $body.html(html);

    $('#checkAll').prop('checked', false);

    if ($editor.length) {
        $body.append($editor);
    }

    $('#tmp_materialreleasedtl')
        .toggleClass('mrl-editing', mrlGridEditor().length > 0);

    mrlGridUpdateSelectedInfo();
}

function mrlGridSavedRow(row) {
    var id   = mrlGridEsc(row.uniqueid || '');
    var desc = $.trim(row.description || '');
    var nmb  = $.trim(row.nmbarang || '');
    var qty  = mrlGridNum(row.qty);
    var stok = mrlGridNum(row.stock);
    var over = (qty > stok && stok >= 0 && row.idbarang);
    var sub  = (String(row.issub || 'F').toUpperCase() === 'T');

    return '' +
        '<tr class="mrl-row-saved" data-uniqueid="' + id + '" data-idurut="' + mrlGridEsc(row.idurut || '') + '">' +
        '  <td class="mrl-col-no"><input type="checkbox" class="row-check" value="' + id + '" title="Pilih baris (hapus massal: Ctrl+D)"></td>' +
        '  <td class="text-center"><input type="checkbox" disabled ' + (sub ? 'checked' : '') + ' title="Substitute Item"></td>' +
        '  <td title="' + mrlGridEsc(row.idbarang) + '">' + mrlGridEsc(row.idbarang) + '</td>' +
        '  <td title="' + mrlGridEsc(nmb) + '"><span class="mrl-readonly-text">' + mrlGridEsc(nmb) + '</span></td>' +
        '  <td>' + mrlGridEsc(row.spec) + '</td>' +
        '  <td class="text-end"><span class="mrl-readonly-number">' + mrlGridDec(qty, 4) + '</span></td>' +
        '  <td class="text-end">' +
        '      <span class="mrl-readonly-number mrl-stok-cell" data-stock="' + stok + '" data-qty="' + qty + '" data-over="' + (over ? '1' : '0') + '">' +
        '          ' + mrlGridDec(stok, 4) +
        (over ? ' <i class="fa fa-exclamation-triangle text-warning" title="Qty melebihi stok gudang"></i>' : '') +
        '      </span>' +
        '  </td>' +
        '  <td class="text-center">' + mrlGridEsc(row.unit) + '</td>' +
        '  <td title="' + mrlGridEsc(desc) + '"><span class="mrl-readonly-text">' + mrlGridEsc(desc) + '</span></td>' +
        '  <td class="mrl-col-aksi">' +
        '      <button type="button" class="mrl-btn mrl-btn-edit" data-act="edit" title="Edit baris">' +
        '          <i class="fa fa-pencil"></i></button>' +
        '  </td>' +
        '</tr>';
}

/* ------------------------------------------------------------------ */
/* BARIS EDITOR                                                       */
/* ------------------------------------------------------------------ */
function mrlGridEditorRow() {
    var sel = 'style="width:100%"';

    return '' +
        '<tr class="mrl-row-draft">' +
        '  <td class="mrl-col-no"><input type="checkbox" disabled title="Baris belum tersimpan"></td>' +
        '  <td class="text-center">' +
        '      <input type="checkbox" class="mrl-issub" title="Enter: toggle. Centang = Substitute Item (T), kosong = BOM Item (F)">' +
        '  </td>' +
        '  <td class="mrl-col-item">' +
        '      <div class="mrl-item-bom-wrap"><select class="mrl-item-bom" ' + sel + '></select></div>' +
        '      <div class="mrl-item-sub-wrap" style="display:none"><select class="mrl-item-sub" ' + sel + '></select></div>' +
        '  </td>' +
        '  <td><input type="text" class="form-control form-control-sm mrl-nmbarang" readonly tabindex="-1"></td>' +
        '  <td><select class="mrl-spec" ' + sel + '></select></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end jaccseparator mrl-qty"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end mrl-stok" readonly tabindex="-1" title="Stok gudang (read only)"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-center mrl-unit" readonly tabindex="-1"></td>' +
        '  <td><input type="text" class="form-control form-control-sm mrl-description"></td>' +
        '  <td class="mrl-col-aksi">' +
        '      <button type="button" class="mrl-btn mrl-btn-save" data-act="save" title="Simpan baris (Enter)">' +
        '          <i class="fa fa-check"></i></button>' +
        '      <button type="button" class="mrl-btn mrl-btn-cancel" data-act="cancel" title="Batal (Esc)">' +
        '          <i class="fa fa-times"></i></button>' +
        '  </td>' +
        '</tr>';
}

/* ------------------------------------------------------------------ */
/* SELECT2: ITEM BOM / ITEM SUBSTITUTE / SPEC (BATCH)                 */
/* ------------------------------------------------------------------ */
function mrlGridFormatItem(repo) {
    if (repo.loading) { return repo.text; }

    var idb = repo.idbarang || repo.id || '';
    var nmb = repo.nmbarang || repo.text || '';

    return "<div class='select2-result-repository__description'>" +
        mrlGridEsc(idb) + "   <i class='fa fa-circle-o'></i>   " + mrlGridEsc(nmb) +
        "</div>";
}

function mrlGridFormatItemSel(repo) {
    return repo.nmbarang || repo.text;
}

function mrlGridItemDefaults() {
    var grp = (typeof defaultInitialGroupBrng === 'undefined') ? '' : defaultInitialGroupBrng;
    return { grp: grp };
}

function mrlGridInitSelects($tr) {
    var $bom  = $tr.find('.mrl-item-bom');
    var $sub  = $tr.find('.mrl-item-sub');
    var def   = mrlGridItemDefaults();

    /* ---- BOM Item (komponen BOM, default) ---- */
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
            templateResult: mrlGridFormatItem,
            templateSelection: mrlGridFormatItemSel
        }).on('select2:select', function (e) {
            var data = e.params.data || {};
            var $row = $(this).closest('tr');

            $row.find('.mrl-nmbarang').val($.trim(data.nmbarang || ''));
            $row.find('.mrl-unit').val($.trim(data.unit || ''));
            $row.find('.mrl-qty').val(data.qty != null ? mrlGridDec(data.qty, 4) : '');

            mrlGridClearInvalid($row.find('.mrl-item-bom'));

            mrlGridResetSpec($row, null);
            mrlGridRefreshStock($row);
        }).on('select2:clear', function () {
            var $row = $(this).closest('tr');

            $row.find('.mrl-nmbarang').val('');
            $row.find('.mrl-unit').val('');
            $row.find('.mrl-qty').val('');

            mrlGridResetSpec($row, null);
            mrlGridRefreshStock($row);
        });
    }

    /* ---- Substitute Item ---- */
    if (!$sub.hasClass('select2-hidden-accessible')) {
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
            templateResult: mrlGridFormatItem,
            templateSelection: mrlGridFormatItemSel
        }).on('select2:select', function (e) {
            var data = e.params.data || {};
            var $row = $(this).closest('tr');

            $row.find('.mrl-nmbarang').val($.trim(data.nmbarang || ''));
            $row.find('.mrl-unit').val($.trim(data.unit || ''));
            $row.find('.mrl-qty').val('');

            mrlGridClearInvalid($row.find('.mrl-item-sub'));

            mrlGridResetSpec($row, null);
            mrlGridRefreshStock($row);
        }).on('select2:clear', function () {
            var $row = $(this).closest('tr');

            $row.find('.mrl-nmbarang').val('');
            $row.find('.mrl-unit').val('');
            $row.find('.mrl-qty').val('');

            mrlGridResetSpec($row, null);
            mrlGridRefreshStock($row);
        });
    }

    mrlGridInitSpecSelect($tr);
}


/* ---- Spec / Batch (list_batch_item) ---- */
function mrlGridInitSpecSelect($tr) {
    var $spec = $tr.find('.mrl-spec');

    if ($spec.hasClass('select2-hidden-accessible')) { return; }

    $spec.select2({
        placeholder: 'Spec / Batch',
        allowClear: true,
        width: '100%',
        dropdownParent: document.body,
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_batch_item',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var item = mrlGridActiveItem($tr);

                return {
                    _search_: params.term,
                    _page_: params.page,
                    _draw_: true,
                    _start_: 1,
                    _perpage_: 30,
                    _paramglobal_: '',
                    _parameterx_: item,
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: (data && data.items) ? data.items : [],
                    pagination: {
                        more: (params.page * 30) < ((data && data.total_count) || 0)
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) { return markup; }
    }).on('select2:select', function () {
        mrlGridRefreshStock($(this).closest('tr'));
    }).on('select2:clear', function () {
        mrlGridRefreshStock($(this).closest('tr'));
    });
}

function mrlGridActiveSelect($tr) {
    $tr = ($tr && $tr.length) ? $tr : mrlGridEditor();

    return $tr.find('.mrl-issub').prop('checked')
        ? $tr.find('.mrl-item-sub')
        : $tr.find('.mrl-item-bom');
}

function mrlGridActiveItem($tr) {
    $tr = ($tr && $tr.length) ? $tr : mrlGridEditor();

    return $.trim(mrlGridActiveSelect($tr).val() || '').toString();
}

function mrlGridCloseSelect2($select) {
    if (!$select || !$select.length) { return; }

    if (!$select.hasClass('select2-hidden-accessible')) { return; }

    var inst = $select.data('select2');

    if (inst && typeof inst.isOpen === 'function' && inst.isOpen()) {
        try { $select.select2('close'); } catch (e) { /* abaikan */ }
    }
}

function mrlGridFixSelectWidth($el) {
    if (!$el || !$el.length) { return; }
    if (!$el.hasClass('select2-hidden-accessible')) { return; }

    /* container select2 yang dibuat saat wrapper-nya display:none bisa
       berukuran 0 → paksa 100% setelah wrapper ditampilkan */
    var $container = $el.next('.select2-container');

    if ($container.length) {
        $container.css('width', '100%');
    }
}

function mrlGridSearchField($select) {
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

/* Buka select2 + fokuskan kotak pencarian milik instance tersebut (bukan global),
   supaya ketikan langsung memfilter item/batch tanpa klik. */
function mrlGridFocusOpenSearch($el) {
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
        var $search = mrlGridSearchField($el);

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

/* Toggle Substitute (T) / BOM (F) */
function mrlGridApplyIssub($tr, isSub, opts) {
    opts = opts || {};

    $tr.find('.mrl-issub').prop('checked', !!isSub);

    if (isSub === true) {
        mrlGridCloseSelect2($tr.find('.mrl-item-bom'));

        $tr.find('.mrl-item-bom-wrap').hide();
        $tr.find('.mrl-item-sub-wrap').show();

        /* kosongkan BOM item (mengikuti perilaku modal) */
        $tr.find('.mrl-item-bom').val(null).trigger('change');
    } else {
        mrlGridCloseSelect2($tr.find('.mrl-item-sub'));

        $tr.find('.mrl-item-sub-wrap').hide();
        $tr.find('.mrl-item-bom-wrap').show();

        $tr.find('.mrl-item-sub').val(null).trigger('change');
    }

    if (opts.clearFields !== false) {
        $tr.find('.mrl-nmbarang').val('');
        $tr.find('.mrl-unit').val('');
        $tr.find('.mrl-qty').val('');
        mrlGridResetSpec($tr, null);
        $tr.find('.mrl-stok').val('').data('stock', 0);
    }

    if (opts.focus !== false) {
        var $active = mrlGridActiveSelect($tr);

        mrlGridFixSelectWidth($active);
        mrlGridFocusOpenSearch($active);
    }
}

function mrlGridResetSpec($tr, preselect) {
    var $spec = $tr.find('.mrl-spec');

    $spec.empty().val(null).trigger('change');

    if (preselect && $.trim(preselect) !== '') {
        $spec.append(new Option(preselect, preselect, true, true))
            .val(preselect).trigger('change');
    }
}

/* ------------------------------------------------------------------ */
/* STOK GUDANG                                                        */
/* ------------------------------------------------------------------ */
function mrlGridRefreshStock($tr) {
    var item  = mrlGridActiveItem($tr);
    var loc   = $.trim($('#idlocation').val() || '').toString();
    var spec  = $.trim($tr.find('.mrl-spec').val() || '').toString();
    var $stok = $tr.find('.mrl-stok');

    if (item === '' || loc === '') {
        $stok.val('').data('stock', 0);
        return;
    }

    $.ajax({
        type: 'POST',
        url: HOST_URL + 'api/globalmodule/stock_stkgdw_item',
        dataType: 'json',
        data: { idbarang: item, loccode: loc, batch: spec }
    }).done(function (res) {
        var total = parseFloat((res && res.total != null) ? res.total : 0) || 0;

        $stok.data('stock', total).val(mrlGridDec(total, 4));
    }).fail(function () {
        $stok.data('stock', 0).val(mrlGridDec(0, 4));
    });
}


/* ------------------------------------------------------------------ */
/* BUKA / ISI / BATAL BARIS EDITOR                                    */
/* ------------------------------------------------------------------ */
function mrlGridFocusEditor() {
    var $tr = mrlGridEditor();

    if (!$tr.length) { return; }

    mrlGridFocusOpenSearch(mrlGridActiveSelect($tr));
}

function mrlGridFillEditor($tr, data) {
    var isSub = (String(data.issub || 'F').toUpperCase() === 'T');

    mrlGridApplyIssub($tr, isSub, { clearFields: false, focus: false });

    if (isSub) {
        if (data.idbarang) {
            $tr.find('.mrl-item-sub')
                .append(new Option(data.nmbarang || data.idbarang, data.idbarang, true, true))
                .val(data.idbarang).trigger('change');
        }
    } else {
        var idb = data.idbarang_bom || data.idbarang;

        if (idb) {
            $tr.find('.mrl-item-bom')
                .append(new Option(data.nmbarang || idb, idb, true, true))
                .val(idb).trigger('change');
        }
    }

    $tr.find('.mrl-nmbarang').val(data.nmbarang || '');
    $tr.find('.mrl-unit').val(data.unit || '');
    $tr.find('.mrl-qty').val(data.qty != null ? mrlGridDec(data.qty, 4) : '');
    $tr.find('.mrl-description').val(data.description || '');

    mrlGridResetSpec($tr, data.spec);

    $tr.find('.mrl-stok')
        .data('stock', mrlGridNum(data.stock))
        .val(mrlGridDec(data.stock, 4));

    mrlGridFixSelectWidth($tr.find('.mrl-item-bom'));
    mrlGridFixSelectWidth($tr.find('.mrl-item-sub'));
    mrlGridFixSelectWidth($tr.find('.mrl-spec'));
}

function mrlGridNewDraft() {
    if (!mrlGridExists()) { return; }

    var $existing = mrlGridEditor();

    if ($existing.length) {
        mrlGridFocusOpenSearch(mrlGridActiveSelect($existing));
        return;
    }

    var $body = mrlGridBody();

    $body.find('tr.mrl-empty-row').remove();
    $body.append(mrlGridEditorRow());

    var $tr = mrlGridEditor();

    mrlGridInitSelects($tr);
    mrlGridApplyIssub($tr, false, { clearFields: false, focus: true });

    $('#tmp_materialreleasedtl').addClass('mrl-editing');
    mrlGridUpdateSelectedInfo();
}

function mrlGridRemoveEditor() {
    mrlGridEditor().remove();
    $('#tmp_materialreleasedtl').removeClass('mrl-editing');
}

function mrlGridCancel() {
    var $tr = mrlGridEditor();

    if (!$tr.length) { return; }

    mrlGridRemoveEditor();
    mrlGridRender();
}

/* Edit in-place: data diambil dari state (tanpa AJAX) */
function mrlGridEditRow(uniqueid) {
    var id = ($.trim(uniqueid || '')).toString();

    if (id === '') { return; }

    var row = null;

    $.each(mrlGridState.rows || [], function (i, r) {
        if (String(r.uniqueid) === id) { row = r; return false; }
    });

    if (!row) {
        mrlGridToast('Data baris tidak ditemukan, muat ulang halaman', 'warning');
        return;
    }

    var $target = mrlGridBody().find('tr.mrl-row-saved[data-uniqueid="' + id + '"]');

    if (!$target.length) { return; }

    /* hanya satu editor aktif: baris yang sedang diedit dipulihkan dulu */
    var $current = mrlGridEditor();

    if ($current.length) {
        mrlGridRemoveEditor();
        mrlGridRender();

        $target = mrlGridBody().find('tr.mrl-row-saved[data-uniqueid="' + id + '"]');

        if (!$target.length) { return; }
    }

    $target.replaceWith(mrlGridEditorRow());

    var $tr = mrlGridEditor();

    $tr.removeClass('mrl-row-draft').addClass('mrl-row-editing')
        .attr('data-uniqueid', id)
        .attr('data-idurut', row.idurut || '');

    mrlGridInitSelects($tr);
    mrlGridFillEditor($tr, row);

    $('#tmp_materialreleasedtl').addClass('mrl-editing');
    mrlGridUpdateSelectedInfo();

    mrlGridFocusOpenSearch(mrlGridActiveSelect($tr));
}

function mrlGridEditSelected() {
    var ids = mrlGridCheckedIds();

    if (ids.length === 0) {
        mrlGridToast('Pilih satu baris yang akan diedit', 'warning');
        return;
    }

    if (ids.length > 1) {
        mrlGridToast('Edit hanya boleh satu baris', 'warning');
        return;
    }

    mrlGridEditRow(ids[0]);
}

/* ------------------------------------------------------------------ */
/* VALIDASI (tooltip, tanpa Swal)                                     */
/* ------------------------------------------------------------------ */
function mrlGridMarkInvalid($el, msg) {
    if (!$el || !$el.length) { return; }

    $el.addClass('mrl-invalid');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.next('.select2-container').find('.select2-selection--single').addClass('mrl-invalid');
    }

    $el.attr('title', msg || 'Wajib diisi');

    if (typeof $el.tooltip === 'function') {
        try { $el.tooltip('dispose').tooltip({ trigger: 'manual' }).tooltip('show'); } catch (e) { /* noop */ }
    }
}

function mrlGridClearInvalid($el) {
    if (!$el || !$el.length) { return; }

    $el.removeClass('mrl-invalid').removeAttr('title');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.next('.select2-container').find('.select2-selection--single').removeClass('mrl-invalid');
    }

    if (typeof $el.tooltip === 'function') {
        try { $el.tooltip('dispose'); } catch (e) { /* noop */ }
    }
}

function mrlGridUpdateSelectedInfo() {
    var $info = $('#mrlSelectedInfo');

    if (!$info.length) { return; }

    var total   = mrlGridBody().find('tr.mrl-row-saved').length;
    var checked = mrlGridBody().find('.row-check:checked').length;

    $info.text(checked + ' dari ' + total + ' baris dipilih');
}


/* ------------------------------------------------------------------ */
/* SIMPAN BARIS                                                       */
/* ------------------------------------------------------------------ */
function mrlGridSave(openNext) {
    var $tr = mrlGridEditor();

    if (!$tr.length || mrlGridState.saving) { return; }

    var isSub    = $tr.find('.mrl-issub').prop('checked');
    var $item    = mrlGridActiveSelect($tr);
    var idbarang = $.trim($item.val() || '').toString();

    if (idbarang === '') {
        mrlGridMarkInvalid($item, isSub ? 'Substitute Item wajib dipilih' : 'BOM Item wajib dipilih');
        mrlGridFocusOpenSearch($item);
        return;
    }

    var $qty   = $tr.find('.mrl-qty');
    var qtyRaw = $qty.val();

    if ($.trim(String(qtyRaw || '')) === '') {
        mrlGridMarkInvalid($qty, 'Qty wajib diisi');
        $qty.trigger('focus').select();
        return;
    }

    var qty = mrlGridNum(qtyRaw);

    if (qty <= 0) {
        mrlGridMarkInvalid($qty, 'Qty harus lebih besar dari 0');
        $qty.trigger('focus').select();
        return;
    }

    var stok = parseFloat($tr.find('.mrl-stok').data('stock'));
    var loc  = $.trim($('#idlocation').val() || '');

    if (!isFinite(stok)) { stok = 0; }

    /* validasi stok hanya bila gudang (idlocation) sudah dipilih */
    if (loc !== '' && qty > stok) {
        mrlGridMarkInvalid($qty, 'Qty melebihi stok gudang (' + mrlGridDec(stok, 4) + ')');
        $qty.trigger('focus').select();
        return;
    }

    /* Payload dibangun dari form header (semua field header ikut), lalu
       field detail di-override dengan nilai baris editor. */
    var form     = document.getElementById('formStandarCostMst');
    var formData = form ? new FormData(form) : new FormData();

    formData.set('idurut', ($tr.attr('data-idurut') || '').toString());
    formData.set('uniqueid', ($tr.attr('data-uniqueid') || '').toString());

    formData.set('issub', isSub ? 'T' : 'F');
    formData.set('idbarang', isSub ? idbarang : '');
    formData.set('idbarang_bom', isSub ? '' : idbarang);

    formData.set('nmbarang', $.trim($tr.find('.mrl-nmbarang').val() || '').toString());
    formData.set('unit', $.trim($tr.find('.mrl-unit').val() || '').toString());
    formData.set('qty', String(qty));
    formData.set('spec', $.trim($tr.find('.mrl-spec').val() || '').toString());
    formData.set('description', $.trim($tr.find('.mrl-description').val() || '').toUpperCase());

    formData.set(
        'docno',
        ($('#prefix').val() || '') + '/' +
        ($('#infix').val() || '') + '/' +
        ($('#suffix').val() || '')
    );

    /* field header yang tidak ada di form (bila elemennya ada di halaman) */
    $.each(['docref', 'nmbarang_jadi', 'keterangan', 'idlocation', 'nmlocation',
            'buildfor', 'buildunit', 'batchno', 'bagian', 'tabno', 'woeno', 'wono', 'bomno'],
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
            formData.set(name, String(mrlGridNum($f.val())));
        }
    });

    mrlGridState.saving = true;

    var $btn = $tr.find('[data-act="save"]');
    $btn.prop('disabled', true);

    $.ajax({
        url: HOST_URL + 'production/trans/save_materialrelease_mst',
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

                mrlGridRemoveEditor();
                mrlGridLoadRows(openNext === true);
            } else {
                mrlGridToast((res && res.message) || 'Gagal menyimpan data', 'warning');
            }
        },

        error: function () {
            mrlGridToast('Terjadi kesalahan server', 'error');
        },

        complete: function () {
            mrlGridState.saving = false;
            $btn.prop('disabled', false);
        }
    });
}


/* ------------------------------------------------------------------ */
/* HAPUS                                                              */
/* ------------------------------------------------------------------ */
function mrlGridCheckedIds() {
    var ids = [];

    mrlGridBody().find('.row-check:checked').each(function () {
        ids.push($(this).val());
    });

    return ids;
}

function mrlGridDelete(ids) {
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
            url: HOST_URL + 'production/trans/delete_materialrelease_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json'
        }).done(function (res) {
            if (res && res.status) {
                /* tanpa popup sukses */
                mrlGridRemoveEditor();
                $('#checkAll').prop('checked', false);
                mrlGridLoadRows(false);

                if (typeof documentReadable === 'function') {
                    documentReadable();
                }
            } else {
                mrlGridToast((res && res.message) || 'Gagal hapus data', 'error');
            }
        }).fail(function () {
            mrlGridToast('Gagal hapus data', 'error');
        });
    });
}

function mrlGridDeleteRow(uniqueid) {
    if (!uniqueid) { return; }

    mrlGridDelete([uniqueid]);
}

function mrlGridDeleteSelected() {
    var ids = mrlGridCheckedIds();

    if (ids.length === 0) {
        mrlGridToast('Pilih baris yang akan dihapus', 'warning');
        return;
    }

    mrlGridDelete(ids);
}

/* ------------------------------------------------------------------ */
/* NAVIGASI KEYBOARD                                                  */
/* ------------------------------------------------------------------ */
function mrlGridFields($tr) {
    return [
        $tr.find('.mrl-issub')[0],
        mrlGridActiveSelect($tr)[0],
        $tr.find('.mrl-spec')[0],
        $tr.find('.mrl-qty')[0],
        $tr.find('.mrl-description')[0]
    ];
}

function mrlGridMoveNext($el) {
    var $tr = mrlGridEditor();

    if (!$tr.length) { return; }

    var fields = mrlGridFields($tr);
    var idx    = -1;

    $.each(fields, function (i, el) {
        if (el === $el[0]) { idx = i; return false; }
    });

    if (idx === -1 || idx >= fields.length - 1) { return; }

    var next = fields[idx + 1];

    if (!next) { return; }

    var $next = $(next);

    if ($next.hasClass('select2-hidden-accessible')) {
        mrlGridFocusOpenSearch($next);
    } else {
        $next.trigger('focus');
    }
}

function mrlGridIsLastField($el) {
    return $el.hasClass('mrl-description');
}


/* ------------------------------------------------------------------ */
/* KEYBOARD TAMBAHAN (Enter pada checkbox Sub)                        */
/* ------------------------------------------------------------------ */
function mrlGridBindIssubEnter() {
    $(document)
        .off('keydown.mrlgridissub')
        .on('keydown.mrlgridissub', '#tmp_materialreleasedtl tbody .mrl-issub', function (e) {
            var isEnter = (e.key === 'Enter' || e.keyCode === 13);
            var isSpace = (e.key === ' ' || e.keyCode === 32);

            if (isSpace) { return; } /* biarkan default: toggle */
            if (!isEnter) { return; }

            e.preventDefault();
            e.stopPropagation();

            var $tr  = $(this).closest('tr');
            var next = !$(this).prop('checked');

            mrlGridApplyIssub($tr, next, { focus: true });
        });

    $(document)
        .off('change.mrlgridissub')
        .on('change.mrlgridissub', '#tmp_materialreleasedtl tbody .mrl-issub', function () {
            /* klik mouse → perilaku sama: T tampilkan Substitute, F tampilkan BOM */
            mrlGridApplyIssub($(this).closest('tr'), $(this).prop('checked'), { focus: true });
        });
}

/* ------------------------------------------------------------------ */
/* BINDING GRID                                                       */
/* ------------------------------------------------------------------ */
$(function () {
    if (!mrlGridExists()) { return; }

    /* pencarian live (client side) */
    $(document)
        .off('input.mrlgridsearch')
        .on('input.mrlgridsearch', '#mrlGridSearch', function () {
            mrlGridState.query = this.value || '';
            mrlGridRender();
        });

    /* empty state → buka baris baru */
    $(document)
        .off('click.mrlgridempty')
        .on('click.mrlgridempty', '#tmp_materialreleasedtl tbody tr.mrl-empty-row td', function () {
            mrlGridNewDraft();
        });

    $(document)
        .off('keydown.mrlgridempty')
        .on('keydown.mrlgridempty', '#tmp_materialreleasedtl tbody tr.mrl-empty-row td', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                mrlGridNewDraft();
            }
        });

    /* sort header (▲/▼) */
    $(document)
        .off('click.mrlgridsort')
        .on('click.mrlgridsort', '#tmp_materialreleasedtl thead th.mrl-sortable', function () {
            var key = $(this).data('key');

            if (!key) { return; }

            if (mrlGridState.sortKey === key) {
                mrlGridState.sortDir = -mrlGridState.sortDir;
            } else {
                mrlGridState.sortKey = key;
                mrlGridState.sortDir = 1;
            }

            $('#tmp_materialreleasedtl thead th').removeClass('mrl-sort-asc mrl-sort-desc');
            $(this).addClass(mrlGridState.sortDir === 1 ? 'mrl-sort-asc' : 'mrl-sort-desc');

            mrlGridRender();
        });

    /* klik baris tersimpan → centang */
    $(document)
        .off('click.mrlgridrow')
        .on('click.mrlgridrow', '#tmp_materialreleasedtl tbody tr.mrl-row-saved', function (e) {
            if ($(e.target).is('input, button, i, a')) { return; }

            var $cb = $(this).find('.row-check');

            $cb.prop('checked', !$cb.prop('checked')).trigger('change');
        });

    /* chip info pilihan */
    $(document)
        .off('change.mrlgridcheck')
        .on('change.mrlgridcheck', '#tmp_materialreleasedtl tbody .row-check', function () {
            mrlGridUpdateSelectedInfo();
        });

    /* format ribuan saat field ditinggalkan (fallback bila fungsi global halaman tidak ada) */
    $(document)
        .off('blur.mrlgridnum')
        .on('blur.mrlgridnum', '#tmp_materialreleasedtl tbody .mrl-qty', function () {
            mrlGridFormatInput($(this));
        });

    /* tombol aksi per baris */
    $(document)
        .off('click.mrlgridact')
        .on('click.mrlgridact', '#tmp_materialreleasedtl tbody [data-act]', function (e) {
            e.preventDefault();

            var act = String($(this).data('act') || '');
            var $tr = $(this).closest('tr');

            if (act === 'edit') {
                mrlGridEditRow($tr.attr('data-uniqueid'));
            } else if (act === 'save') {
                mrlGridSave(true);
            } else if (act === 'cancel') {
                mrlGridCancel();
            }
        });

    /* tombol Hapus di toolbar */
    $(document)
        .off('click.mrlgriddelete')
        .on('click.mrlgriddelete', '#mrlDeleteSelected', function (e) {
            e.preventDefault();
            mrlGridDeleteSelected();
        });

    mrlGridBindIssubEnter();


    /* Tab / Enter di dalam baris editor */
    $(document)
        .off('keydown.mrlgridnav')
        .on('keydown.mrlgridnav', '#tmp_materialreleasedtl tbody tr', function (e) {
            var $tr = $(this);

            if (!$tr.hasClass('mrl-row-draft') && !$tr.hasClass('mrl-row-editing')) { return; }

            var $t    = $(e.target);
            var $cont = $t.closest('.select2-container');
            var $sel  = $cont.length ? $cont.prev('select') : $();

            var isEnter = (e.key === 'Enter' || e.keyCode === 13);
            var isTab   = (e.key === 'Tab' || e.keyCode === 9);

            /* Tab dari checkbox Sub → langsung ke select item yang aktif */
            if ($t.hasClass('mrl-issub')) {
                if (isTab) {
                    e.preventDefault();
                    mrlGridFocusOpenSearch(mrlGridActiveSelect($tr));
                }
                return;
            }

            if (isEnter) {
                e.preventDefault();

                if ($t.hasClass('mrl-description')) {
                    mrlGridSave(true);
                    return;
                }

                mrlGridMoveNext(($sel.length ? $sel : $t));
                return;
            }

            if (isTab && $t.hasClass('mrl-description')) {
                e.preventDefault();
                mrlGridSave(true);
                return;
            }

            if (isTab && $sel.length) {
                e.preventDefault();
                mrlGridMoveNext($sel);
            }
        });

    /* Esc: capture phase (fokus bisa di select2) */
    document.addEventListener('keydown', function (ev) {
        if (ev.key !== 'Escape' && ev.keyCode !== 27) { return; }

        var $row = mrlGridEditor();

        if (!$row.length) { return; }

        var active = document.activeElement;

        if (!active) { return; }

        var inEditor = ($row[0] === active) ||
                       $row[0].contains(active) ||
                       $(active).closest('.select2-container').length > 0;

        if (!inEditor) { return; }

        ev.preventDefault();
        mrlGridCancel();
    }, true);

    /* shortcut tambahan: Alt+N (Ctrl+Q / Ctrl+D sudah ditangani page JS) */
    $(document)
        .off('keydown.mrlgridshortcut')
        .on('keydown.mrlgridshortcut', function (e) {
            if (typeof isTypingTarget === 'function' && isTypingTarget(e.target)) { return; }

            var key = String(e.key || '').toLowerCase();

            if (e.altKey && key === 'n') {
                e.preventDefault();
                mrlGridNewDraft();
            }
        });

    mrlGridLoadRows(false);
});

