/* =========================================================================
 * GRID DETAIL PENERIMAAN BP — INLINE (gaya pmk_brng)
 * -------------------------------------------------------------------------
 * - Tanpa DataTables: baris dimuat dari endpoint JSON
 *   production/trans/list_penerimaanbp_dtl_grid.
 * - Tambah baris (+ / Alt+N / Ctrl+Q) → satu baris editor di dalam tabel.
 * - Simpan baris → production/trans/save_penerimaanbp_mst (payload SAMA dengan modal),
 *   sukses tanpa popup dan langsung buka baris baru.
 * - Edit in-place (data dari state, tanpa AJAX), Esc membatalkan/memulihkan.
 * - Hapus massal baris tercentang → production/trans/delete_penerimaanbp_detail.
 * - Khusus PENERIMAAN BP:
 *     · checkbox Sub (issub) → Enter men-toggle; T = Substitute Item
 *       (list_item_std_cost, min 2 karakter, harga = newcost),
 *       F = BOM Item (list_item_bom, qty ikut item BOM).
 *     · Nilai = Qty × Harga (read-only, dihitung ulang saat qty/harga berubah).
 *     · Kolom Spec hanya tampil (tidak dipakai backend PENERIMAAN BP).
 * ========================================================================= */

var pnpGridState = {
    rows: [],
    saving: false,
    editing: null,
    query: '',
    sortKey: '',
    sortDir: 1
};

function pnpGridBody() {
    return $('#pnpGridBody');
}

function pnpGridExists() {
    return pnpGridBody().length > 0;
}

function pnpGridEditor() {
    return pnpGridBody().find('tr.pnp-row-draft, tr.pnp-row-editing').first();
}

/* ------------------------------------------------------------------ */
/* HELPER                                                             */
/* ------------------------------------------------------------------ */
function pnpGridEsc(v) {
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

function pnpGridNum(v) {
    if (v === null || v === undefined) { return 0; }
    var s = String(v).replace(/,/g, '').trim();
    if (s === '') { return 0; }
    var n = parseFloat(s);
    return isFinite(n) ? n : 0;
}

function pnpGridDec(v, d) {
    var dec = (d == null ? 4 : d);

    /* pemisah ribuan seperti grid pilot (standart_cost) */
    return pnpGridNum(v).toLocaleString('en-US', {
        minimumFractionDigits: dec,
        maximumFractionDigits: dec
    });
}

/* Set nilai numerik ke input + jalankan formatter ribuan halaman (bila ada) */
function pnpGridSetNum($el, value) {
    if (!$el || !$el.length) { return; }

    $el.val(pnpGridDec(value, 4));

    if (typeof jaccseparator === 'function') {
        try { jaccseparator($el[0]); } catch (e) { /* abaikan */ }
    } else if (typeof _jtsseparator === 'function') {
        try { _jtsseparator($el[0]); } catch (e2) { /* abaikan */ }
    }
}

/* Fallback format saat field ditinggalkan (bila fungsi global tidak tersedia) */
function pnpGridFormatInput($el) {
    var raw = String($el.val() || '').replace(/,/g, '').trim();

    if (raw === '') { return; }

    var n = parseFloat(raw);

    if (!isFinite(n)) { return; }

    $el.val(n.toLocaleString('en-US', {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4
    }));
}

function pnpGridToast(msg, icon) {
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
function pnpGridLoadRows(focusDraft) {
    $.ajax({
        url: HOST_URL + 'production/trans/list_penerimaanbp_dtl_grid',
        type: 'GET',
        dataType: 'json',
        data: { docno: ($('#docno').val() || '').toString().trim() }
    }).done(function (res) {
        var payload = (res && res.dataTables) ? res.dataTables : res;

        pnpGridState.rows = (payload && payload.items) ? payload.items : [];
        pnpGridRender();

        if (focusDraft === true) {
            pnpGridNewDraft();
        }
    }).fail(function () {
        pnpGridToast('Gagal memuat data detail', 'error');
    });
}

function pnpGridReload() {
    pnpGridLoadRows(false);
}

/* ------------------------------------------------------------------ */
/* FILTER + SORT (client side)                                        */
/* ------------------------------------------------------------------ */
function pnpGridVisibleRows() {
    var q = $.trim(pnpGridState.query || '').toLowerCase();

    var rows = (pnpGridState.rows || []).filter(function (r) {
        if (q === '') { return true; }

        var hay = [r.idbarang, r.nmbarang, r.unit, r.description].join(' ').toLowerCase();

        return hay.indexOf(q) !== -1;
    });

    var key = pnpGridState.sortKey;

    if (key) {
        var dir = pnpGridState.sortDir;

        rows = rows.slice().sort(function (a, b) {
            if (key === 'qty' || key === 'harga' || key === 'nilai') {
                return (pnpGridNum(a[key]) - pnpGridNum(b[key])) * dir;
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

function pnpGridEmptyRow() {
    return '' +
        '<tr class="pnp-empty-row">' +
        '  <td colspan="11" tabindex="0">' +
        '      <i class="fa fa-plus-circle mr-2"></i>' +
        '      Klik di sini (atau tekan Alt+N / Ctrl+Q) untuk menambah baris biaya' +
        '  </td>' +
        '</tr>';
}

/* ------------------------------------------------------------------ */
/* RENDER                                                             */
/* ------------------------------------------------------------------ */
function pnpGridRender() {
    if (!pnpGridExists()) { return; }

    var $body   = pnpGridBody();
    var $editor = pnpGridEditor();

    /* baris editor dipindahkan sementara agar tidak hilang saat body diganti */
    if ($editor.length) { $editor.detach(); }

    var rows = pnpGridVisibleRows();
    var html = '';

    if (rows.length === 0) {
        html = pnpGridEmptyRow();
    } else {
        $.each(rows, function (i, row) {
            html += pnpGridSavedRow(row);
        });
    }

    $body.html(html);

    $('#checkAll').prop('checked', false);

    if ($editor.length) {
        $body.append($editor);
    }

    $('#tmp_penerimaanbpdtl').toggleClass('pnp-editing', pnpGridEditor().length > 0);

    pnpGridUpdateSelectedInfo();
}

function pnpGridSavedRow(row) {
    var id   = pnpGridEsc(row.uniqueid || '');
    var desc = $.trim(row.description || '');
    var nmb  = $.trim(row.nmbarang || '');
    var sub  = (String(row.issub || 'F').toUpperCase() === 'T');

    return '' +
        '<tr class="pnp-row-saved" data-uniqueid="' + id + '" data-idurut="' + pnpGridEsc(row.idurut || '') + '">' +
        '  <td class="pnp-col-no"><input type="checkbox" class="row-check" value="' + id + '" title="Pilih baris (hapus massal: Ctrl+D)"></td>' +
        '  <td class="text-center"><input type="checkbox" disabled ' + (sub ? 'checked' : '') + ' title="Substitute Item"></td>' +
        '  <td title="' + pnpGridEsc(row.idbarang) + '">' + pnpGridEsc(row.idbarang) + '</td>' +
        '  <td title="' + pnpGridEsc(nmb) + '"><span class="pnp-readonly-text">' + pnpGridEsc(nmb) + '</span></td>' +
        '  <td>' + pnpGridEsc(row.spec) + '</td>' +
        '  <td class="text-end"><span class="pnp-readonly-number" data-export="' + pnpGridNum(row.qty) + '">' + pnpGridDec(row.qty, 4) + '</span></td>' +
        '  <td class="text-center">' + pnpGridEsc(row.unit) + '</td>' +
        '  <td class="text-end"><span class="pnp-readonly-number" data-export="' + pnpGridNum(row.harga) + '">' + pnpGridDec(row.harga, 4) + '</span></td>' +
        '  <td class="text-end"><span class="pnp-readonly-number" data-export="' + pnpGridNum(row.nilai) + '">' + pnpGridDec(row.nilai, 4) + '</span></td>' +
        '  <td title="' + pnpGridEsc(desc) + '"><span class="pnp-readonly-text">' + pnpGridEsc(desc) + '</span></td>' +
        '  <td class="pnp-col-aksi">' +
        '      <button type="button" class="pnp-btn pnp-btn-edit" data-act="edit" title="Edit baris">' +
        '          <i class="fa fa-pencil"></i></button>' +
        '  </td>' +
        '</tr>';
}

/* ------------------------------------------------------------------ */
/* BARIS EDITOR                                                       */
/* ------------------------------------------------------------------ */
function pnpGridEditorRow() {
    var sel = 'style="width:100%"';

    return '' +
        '<tr class="pnp-row-draft">' +
        '  <td class="pnp-col-no"><input type="checkbox" disabled title="Baris belum tersimpan"></td>' +
        '  <td class="text-center">' +
        '      <input type="checkbox" class="pnp-issub" title="Enter: toggle. Centang = Substitute Item (T), kosong = BOM Item (F)">' +
        '  </td>' +
        '  <td class="pnp-col-item">' +
        '      <div class="pnp-item-bom-wrap"><select class="pnp-item-bom" ' + sel + '></select></div>' +
        '      <div class="pnp-item-sub-wrap" style="display:none"><select class="pnp-item-sub" ' + sel + '></select></div>' +
        '  </td>' +
        '  <td><input type="text" class="form-control form-control-sm pnp-nmbarang" readonly tabindex="-1"></td>' +
        '  <td class="text-center pnp-col-spec"><span class="pnp-readonly-text">—</span></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end jaccseparator pnp-qty"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-center pnp-unit" readonly tabindex="-1"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end jaccseparator pnp-harga"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end pnp-nilai" readonly tabindex="-1"></td>' +
        '  <td><input type="text" class="form-control form-control-sm pnp-description"></td>' +
        '  <td class="pnp-col-aksi">' +
        '      <button type="button" class="pnp-btn pnp-btn-save" data-act="save" title="Simpan baris (Enter)">' +
        '          <i class="fa fa-check"></i></button>' +
        '      <button type="button" class="pnp-btn pnp-btn-cancel" data-act="cancel" title="Batal (Esc)">' +
        '          <i class="fa fa-times"></i></button>' +
        '  </td>' +
        '</tr>';
}


/* ------------------------------------------------------------------ */
/* SELECT2: ITEM BOM / ITEM SUBSTITUTE                                */
/* ------------------------------------------------------------------ */
function pnpGridFormatItem(repo) {
    if (repo.loading) { return repo.text; }

    var idb = repo.idbarang || repo.id || '';
    var nmb = repo.nmbarang || repo.text || '';

    return "<div class='select2-result-repository__description'>" +
        pnpGridEsc(idb) + "   <i class='fa fa-circle-o'></i>   " + pnpGridEsc(nmb) +
        "</div>";
}

function pnpGridFormatItemSel(repo) {
    return repo.nmbarang || repo.text;
}

function pnpGridItemDefaults() {
    var grp = (typeof defaultInitialGroupBrng === 'undefined') ? '' : defaultInitialGroupBrng;
    return { grp: grp };
}

function pnpGridCloseSelect2($select) {
    if (!$select || !$select.length) { return; }
    if (!$select.hasClass('select2-hidden-accessible')) { return; }

    var inst = $select.data('select2');

    if (inst && typeof inst.isOpen === 'function' && inst.isOpen()) {
        try { $select.select2('close'); } catch (e) { /* abaikan */ }
    }
}

function pnpGridFixSelectWidth($el) {
    if (!$el || !$el.length) { return; }
    if (!$el.hasClass('select2-hidden-accessible')) { return; }

    var $container = $el.next('.select2-container');

    if ($container.length) {
        $container.css('width', '100%');
    }
}

function pnpGridSearchField($select) {
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
function pnpGridFocusOpenSearch($el) {
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
        var $search = pnpGridSearchField($el);

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

function pnpGridActiveSelect($tr) {
    $tr = ($tr && $tr.length) ? $tr : pnpGridEditor();

    return $tr.find('.pnp-issub').prop('checked')
        ? $tr.find('.pnp-item-sub')
        : $tr.find('.pnp-item-bom');
}

function pnpGridActiveItem($tr) {
    $tr = ($tr && $tr.length) ? $tr : pnpGridEditor();

    return $.trim(pnpGridActiveSelect($tr).val() || '').toString();
}

/* Nilai = Qty × Harga (mengikuti hitungTotalCost() versi modal) */
function pnpGridRecalcNilai($tr) {
    var qty   = pnpGridNum($tr.find('.pnp-qty').val());
    var harga = pnpGridNum($tr.find('.pnp-harga').val());

    pnpGridSetNum($tr.find('.pnp-nilai'), qty * harga);
}


function pnpGridInitSelects($tr) {
    var $bom = $tr.find('.pnp-item-bom');
    var $sub = $tr.find('.pnp-item-sub');
    var def  = pnpGridItemDefaults();

    /* ---- BOM Item ---- */
    if (!$bom.hasClass('select2-hidden-accessible')) {
        $bom.select2({
            placeholder: 'Choose BOM Item',
            allowClear: true,
            width: '100%',
            dropdownParent: document.body,
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_item_barangjadi',
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
            templateResult: pnpGridFormatItem,
            templateSelection: pnpGridFormatItemSel
        }).on('select2:select', function (e) {
            var data = e.params.data || {};
            var $row = $(this).closest('tr');

            $row.find('.pnp-nmbarang').val($.trim(data.nmbarang || ''));
            $row.find('.pnp-unit').val($.trim(data.unit || ''));
            pnpGridSetNum($row.find('.pnp-qty'), data.qty);

            var harga = (data.newcost != null) ? data.newcost : data.harga;

            if (harga != null && $.trim(String(harga)) !== '') {
                pnpGridSetNum($row.find('.pnp-harga'), harga);
            }

            pnpGridClearInvalid($row.find('.pnp-item-bom'));
            pnpGridRecalcNilai($row);
        }).on('select2:clear', function () {
            var $row = $(this).closest('tr');

            $row.find('.pnp-nmbarang').val('');
            $row.find('.pnp-unit').val('');
            $row.find('.pnp-qty').val('');
            $row.find('.pnp-harga').val('');

            pnpGridRecalcNilai($row);
        });
    }

    pnpGridInitSubSelect($tr, $sub, def);
}


/* ---- Substitute Item (modal: minimumInputLength 2) ---- */
function pnpGridInitSubSelect($tr, $sub, def) {
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
        templateResult: pnpGridFormatItem,
        templateSelection: pnpGridFormatItemSel
    }).on('select2:select', function (e) {
        var data = e.params.data || {};
        var $row = $(this).closest('tr');

        $row.find('.pnp-nmbarang').val($.trim(data.nmbarang || ''));
        $row.find('.pnp-unit').val($.trim(data.unit || ''));
        pnpGridSetNum($row.find('.pnp-harga'), data.newcost);

        pnpGridClearInvalid($row.find('.pnp-item-sub'));
        pnpGridRecalcNilai($row);
    }).on('select2:clear', function () {
        var $row = $(this).closest('tr');

        $row.find('.pnp-nmbarang').val('');
        $row.find('.pnp-unit').val('');
        $row.find('.pnp-harga').val('');

        pnpGridRecalcNilai($row);
    });
}

/* Toggle Substitute (T) / BOM (F) */
function pnpGridApplyIssub($tr, isSub, opts) {
    opts = opts || {};

    $tr.find('.pnp-issub').prop('checked', !!isSub);

    if (isSub === true) {
        pnpGridCloseSelect2($tr.find('.pnp-item-bom'));

        $tr.find('.pnp-item-bom-wrap').hide();
        $tr.find('.pnp-item-sub-wrap').show();

        $tr.find('.pnp-item-bom').val(null).trigger('change');
    } else {
        pnpGridCloseSelect2($tr.find('.pnp-item-sub'));

        $tr.find('.pnp-item-sub-wrap').hide();
        $tr.find('.pnp-item-bom-wrap').show();

        $tr.find('.pnp-item-sub').val(null).trigger('change');
    }

    if (opts.clearFields !== false) {
        $tr.find('.pnp-nmbarang').val('');
        $tr.find('.pnp-unit').val('');
        $tr.find('.pnp-qty').val('');
        $tr.find('.pnp-harga').val('');
        pnpGridRecalcNilai($tr);
    }

    if (opts.focus !== false) {
        var $active = pnpGridActiveSelect($tr);

        pnpGridFixSelectWidth($active);
        pnpGridFocusOpenSearch($active);
    }
}


/* ------------------------------------------------------------------ */
/* BUKA / ISI / BATAL BARIS EDITOR                                    */
/* ------------------------------------------------------------------ */
function pnpGridFocusEditor() {
    var $tr = pnpGridEditor();

    if (!$tr.length) { return; }

    pnpGridFocusOpenSearch(pnpGridActiveSelect($tr));
}

function pnpGridFillEditor($tr, data) {
    var isSub = (String(data.issub || 'F').toUpperCase() === 'T');

    pnpGridApplyIssub($tr, isSub, { clearFields: false, focus: false });

    /* PENERIMAAN BP menyimpan idbarang (bukan idbarang_bom), jadi untuk baris F
       nilai state `idbarang` dipakai sebagai item BOM terpilih. */
    if (isSub) {
        if (data.idbarang) {
            $tr.find('.pnp-item-sub')
                .append(new Option(data.nmbarang || data.idbarang, data.idbarang, true, true))
                .val(data.idbarang).trigger('change');
        }
    } else {
        var idb = data.idbarang_bom || data.idbarang;

        if (idb) {
            $tr.find('.pnp-item-bom')
                .append(new Option(data.nmbarang || idb, idb, true, true))
                .val(idb).trigger('change');
        }
    }

    $tr.find('.pnp-nmbarang').val(data.nmbarang || '');
    $tr.find('.pnp-unit').val(data.unit || '');
    pnpGridSetNum($tr.find('.pnp-qty'), data.qty);
    pnpGridSetNum($tr.find('.pnp-harga'), data.harga);
    $tr.find('.pnp-description').val(data.description || '');

    pnpGridRecalcNilai($tr);

    pnpGridFixSelectWidth($tr.find('.pnp-item-bom'));
    pnpGridFixSelectWidth($tr.find('.pnp-item-sub'));
}

function pnpGridNewDraft() {
    if (!pnpGridExists()) { return; }

    var $existing = pnpGridEditor();

    if ($existing.length) {
        pnpGridFocusOpenSearch(pnpGridActiveSelect($existing));
        return;
    }

    var $body = pnpGridBody();

    $body.find('tr.pnp-empty-row').remove();
    $body.append(pnpGridEditorRow());

    var $tr = pnpGridEditor();

    pnpGridInitSelects($tr);
    pnpGridApplyIssub($tr, false, { clearFields: false, focus: true });

    $('#tmp_penerimaanbpdtl').addClass('pnp-editing');
    pnpGridUpdateSelectedInfo();
}

function pnpGridRemoveEditor() {
    pnpGridEditor().remove();
    $('#tmp_penerimaanbpdtl').removeClass('pnp-editing');
}

function pnpGridCancel() {
    var $tr = pnpGridEditor();

    if (!$tr.length) { return; }

    pnpGridRemoveEditor();
    pnpGridRender();
}

/* Edit in-place: data diambil dari state (tanpa AJAX) */
function pnpGridEditRow(uniqueid) {
    var id = $.trim(uniqueid || '').toString();

    if (id === '') { return; }

    var row = null;

    $.each(pnpGridState.rows || [], function (i, r) {
        if (String(r.uniqueid) === id) { row = r; return false; }
    });

    if (!row) {
        pnpGridToast('Data baris tidak ditemukan, muat ulang halaman', 'warning');
        return;
    }

    var $target = pnpGridBody().find('tr.pnp-row-saved[data-uniqueid="' + id + '"]');

    if (!$target.length) { return; }

    var $current = pnpGridEditor();

    if ($current.length) {
        pnpGridRemoveEditor();
        pnpGridRender();

        $target = pnpGridBody().find('tr.pnp-row-saved[data-uniqueid="' + id + '"]');

        if (!$target.length) { return; }
    }

    $target.replaceWith(pnpGridEditorRow());

    var $tr = pnpGridEditor();

    $tr.removeClass('pnp-row-draft').addClass('pnp-row-editing')
        .attr('data-uniqueid', id)
        .attr('data-idurut', row.idurut || '');

    pnpGridInitSelects($tr);
    pnpGridFillEditor($tr, row);

    $('#tmp_penerimaanbpdtl').addClass('pnp-editing');
    pnpGridUpdateSelectedInfo();

    pnpGridFocusOpenSearch(pnpGridActiveSelect($tr));
}

function pnpGridEditSelected() {
    var ids = pnpGridCheckedIds();

    if (ids.length === 0) {
        pnpGridToast('Pilih satu baris yang akan diedit', 'warning');
        return;
    }

    if (ids.length > 1) {
        pnpGridToast('Edit hanya boleh satu baris', 'warning');
        return;
    }

    pnpGridEditRow(ids[0]);
}


/* ------------------------------------------------------------------ */
/* VALIDASI (tooltip, tanpa Swal)                                     */
/* ------------------------------------------------------------------ */
function pnpGridMarkInvalid($el, msg) {
    if (!$el || !$el.length) { return; }

    $el.addClass('pnp-invalid');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.next('.select2-container').find('.select2-selection--single').addClass('pnp-invalid');
    }

    $el.attr('title', msg || 'Wajib diisi');

    if (typeof $el.tooltip === 'function') {
        try { $el.tooltip('dispose').tooltip({ trigger: 'manual' }).tooltip('show'); } catch (e) { /* noop */ }
    }
}

function pnpGridClearInvalid($el) {
    if (!$el || !$el.length) { return; }

    $el.removeClass('pnp-invalid').removeAttr('title');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.next('.select2-container').find('.select2-selection--single').removeClass('pnp-invalid');
    }

    if (typeof $el.tooltip === 'function') {
        try { $el.tooltip('dispose'); } catch (e) { /* noop */ }
    }
}

function pnpGridUpdateSelectedInfo() {
    var $info = $('#pnpSelectedInfo');

    if (!$info.length) { return; }

    var total   = pnpGridBody().find('tr.pnp-row-saved').length;
    var checked = pnpGridBody().find('.row-check:checked').length;

    $info.text(checked + ' dari ' + total + ' baris dipilih');
}

/* ------------------------------------------------------------------ */
/* SIMPAN BARIS                                                       */
/* ------------------------------------------------------------------ */
function pnpGridSave(openNext) {
    var $tr = pnpGridEditor();

    if (!$tr.length || pnpGridState.saving) { return; }

    var isSub    = $tr.find('.pnp-issub').prop('checked');
    var $item    = pnpGridActiveSelect($tr);
    var idbarang = $.trim($item.val() || '').toString();

    if (idbarang === '') {
        pnpGridMarkInvalid($item, isSub ? 'Substitute Item wajib dipilih' : 'BOM Item wajib dipilih');
        pnpGridFocusOpenSearch($item);
        return;
    }

    var $qty   = $tr.find('.pnp-qty');
    var qtyRaw = $qty.val();

    if ($.trim(String(qtyRaw || '')) === '') {
        pnpGridMarkInvalid($qty, 'Qty wajib diisi');
        $qty.trigger('focus').select();
        return;
    }

    var qty = pnpGridNum(qtyRaw);

    if (qty <= 0) {
        pnpGridMarkInvalid($qty, 'Qty harus lebih besar dari 0');
        $qty.trigger('focus').select();
        return;
    }

    var harga = pnpGridNum($tr.find('.pnp-harga').val());
    var nilai = qty * harga;

    $tr.find('.pnp-nilai').val(pnpGridDec(nilai, 4));

    /* Payload: seluruh field header form ikut, field detail di-set ulang */
    var form     = document.getElementById('formStandarCostMst');
    var formData = form ? new FormData(form) : new FormData();

    formData.set('idurut', ($tr.attr('data-idurut') || '').toString());
    formData.set('uniqueid', ($tr.attr('data-uniqueid') || '').toString());

    formData.set('issub', isSub ? 'T' : 'F');
    formData.set('idbarang', isSub ? idbarang : '');
    formData.set('idbarang_bom', isSub ? '' : idbarang);

    formData.set('nmbarang', $.trim($tr.find('.pnp-nmbarang').val() || '').toString());
    formData.set('unit', $.trim($tr.find('.pnp-unit').val() || '').toString());
    formData.set('qty', String(qty));
    formData.set('harga', String(harga));
    formData.set('nilai', String(nilai));
    formData.set('description', $.trim($tr.find('.pnp-description').val() || '').toUpperCase());

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
            formData.set(name, String(pnpGridNum($f.val())));
        }
    });

    pnpGridState.saving = true;

    var $btn = $tr.find('[data-act="save"]');
    $btn.prop('disabled', true);

    $.ajax({
        url: HOST_URL + 'production/trans/save_penerimaanbp_mst',
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

                pnpGridRemoveEditor();
                pnpGridLoadRows(openNext === true);
            } else {
                pnpGridToast((res && res.message) || 'Gagal menyimpan data', 'warning');
            }
        },

        error: function () {
            pnpGridToast('Terjadi kesalahan server', 'error');
        },

        complete: function () {
            pnpGridState.saving = false;
            $btn.prop('disabled', false);
        }
    });
}


/* ------------------------------------------------------------------ */
/* HAPUS                                                              */
/* ------------------------------------------------------------------ */
function pnpGridCheckedIds() {
    var ids = [];

    pnpGridBody().find('.row-check:checked').each(function () {
        ids.push($(this).val());
    });

    return ids;
}

function pnpGridDelete(ids) {
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
            url: HOST_URL + 'production/trans/delete_penerimaanbp_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json'
        }).done(function (res) {
            if (res && res.status) {
                /* tanpa popup sukses */
                pnpGridRemoveEditor();
                $('#checkAll').prop('checked', false);
                pnpGridLoadRows(false);

                if (typeof documentReadable === 'function') {
                    documentReadable();
                }
            } else {
                pnpGridToast((res && res.message) || 'Gagal hapus data', 'error');
            }
        }).fail(function () {
            pnpGridToast('Gagal hapus data', 'error');
        });
    });
}

function pnpGridDeleteRow(uniqueid) {
    if (!uniqueid) { return; }

    pnpGridDelete([uniqueid]);
}

function pnpGridDeleteSelected() {
    var ids = pnpGridCheckedIds();

    if (ids.length === 0) {
        pnpGridToast('Pilih baris yang akan dihapus', 'warning');
        return;
    }

    pnpGridDelete(ids);
}

/* ------------------------------------------------------------------ */
/* NAVIGASI KEYBOARD                                                  */
/* ------------------------------------------------------------------ */
function pnpGridFields($tr) {
    return [
        $tr.find('.pnp-issub')[0],
        pnpGridActiveSelect($tr)[0],
        $tr.find('.pnp-qty')[0],
        $tr.find('.pnp-harga')[0],
        $tr.find('.pnp-description')[0]
    ];
}

function pnpGridMoveNext($el) {
    var $tr = pnpGridEditor();

    if (!$tr.length) { return; }

    var fields = pnpGridFields($tr);
    var idx    = -1;

    $.each(fields, function (i, el) {
        if (el === $el[0]) { idx = i; return false; }
    });

    if (idx === -1 || idx >= fields.length - 1) { return; }

    var next = fields[idx + 1];

    if (!next) { return; }

    var $next = $(next);

    if ($next.hasClass('select2-hidden-accessible')) {
        pnpGridFocusOpenSearch($next);
    } else {
        $next.trigger('focus');
    }
}

function pnpGridIsLastField($el) {
    return $el.hasClass('pnp-description');
}


/* ------------------------------------------------------------------ */
/* KEYBOARD TAMBAHAN (Enter pada checkbox Sub)                        */
/* ------------------------------------------------------------------ */
function pnpGridBindIssubEnter() {
    $(document)
        .off('keydown.pnpgridissub')
        .on('keydown.pnpgridissub', '#tmp_penerimaanbpdtl tbody .pnp-issub', function (e) {
            var isEnter = (e.key === 'Enter' || e.keyCode === 13);
            var isSpace = (e.key === ' ' || e.keyCode === 32);

            if (isSpace) { return; } /* biarkan default: toggle */
            if (!isEnter) { return; }

            e.preventDefault();
            e.stopPropagation();

            var $tr  = $(this).closest('tr');
            var next = !$(this).prop('checked');

            pnpGridApplyIssub($tr, next, { focus: true });
        });

    $(document)
        .off('change.pnpgridissub')
        .on('change.pnpgridissub', '#tmp_penerimaanbpdtl tbody .pnp-issub', function () {
            /* klik mouse → perilaku sama: T tampilkan Substitute, F tampilkan BOM */
            pnpGridApplyIssub($(this).closest('tr'), $(this).prop('checked'), { focus: true });
        });
}

/* ------------------------------------------------------------------ */
/* BINDING GRID                                                       */
/* ------------------------------------------------------------------ */
$(function () {
    if (!pnpGridExists()) { return; }

    /* pencarian live (client side) */
    $(document)
        .off('input.pnpgridsearch')
        .on('input.pnpgridsearch', '#pnpGridSearch', function () {
            pnpGridState.query = this.value || '';
            pnpGridRender();
        });

    /* empty state → buka baris baru */
    $(document)
        .off('click.pnpgridempty')
        .on('click.pnpgridempty', '#tmp_penerimaanbpdtl tbody tr.pnp-empty-row td', function () {
            pnpGridNewDraft();
        });

    $(document)
        .off('keydown.pnpgridempty')
        .on('keydown.pnpgridempty', '#tmp_penerimaanbpdtl tbody tr.pnp-empty-row td', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                pnpGridNewDraft();
            }
        });

    /* sort header (▲/▼) */
    $(document)
        .off('click.pnpgridsort')
        .on('click.pnpgridsort', '#tmp_penerimaanbpdtl thead th.pnp-sortable', function () {
            var key = $(this).data('key');

            if (!key) { return; }

            if (pnpGridState.sortKey === key) {
                pnpGridState.sortDir = -pnpGridState.sortDir;
            } else {
                pnpGridState.sortKey = key;
                pnpGridState.sortDir = 1;
            }

            $('#tmp_penerimaanbpdtl thead th').removeClass('pnp-sort-asc pnp-sort-desc');
            $(this).addClass(pnpGridState.sortDir === 1 ? 'pnp-sort-asc' : 'pnp-sort-desc');

            pnpGridRender();
        });

    /* klik baris tersimpan → centang */
    $(document)
        .off('click.pnpgridrow')
        .on('click.pnpgridrow', '#tmp_penerimaanbpdtl tbody tr.pnp-row-saved', function (e) {
            if ($(e.target).is('input, button, i, a')) { return; }

            var $cb = $(this).find('.row-check');

            $cb.prop('checked', !$cb.prop('checked')).trigger('change');
        });

    /* chip info pilihan */
    $(document)
        .off('change.pnpgridcheck')
        .on('change.pnpgridcheck', '#tmp_penerimaanbpdtl tbody .row-check', function () {
            pnpGridUpdateSelectedInfo();
        });

    /* tombol aksi per baris */
    $(document)
        .off('click.pnpgridact')
        .on('click.pnpgridact', '#tmp_penerimaanbpdtl tbody [data-act]', function (e) {
            e.preventDefault();

            var act = String($(this).data('act') || '');
            var $tr = $(this).closest('tr');

            if (act === 'edit') {
                pnpGridEditRow($tr.attr('data-uniqueid'));
            } else if (act === 'save') {
                pnpGridSave(true);
            } else if (act === 'cancel') {
                pnpGridCancel();
            }
        });

    /* tombol Hapus di toolbar */
    $(document)
        .off('click.pnpgriddelete')
        .on('click.pnpgriddelete', '#pnpDeleteSelected', function (e) {
            e.preventDefault();
            pnpGridDeleteSelected();
        });

    /* nilai dihitung ulang saat qty/harga diketik */
    $(document)
        .off('input.pnpgridnilai')
        .on('input.pnpgridnilai', '#tmp_penerimaanbpdtl tbody .pnp-qty, #tmp_penerimaanbpdtl tbody .pnp-harga', function () {
            pnpGridRecalcNilai($(this).closest('tr'));
        });

    /* format ribuan saat field ditinggalkan (fallback bila fungsi global halaman tidak ada) */
    $(document)
        .off('blur.pnpgridnum')
        .on('blur.pnpgridnum', '#tmp_penerimaanbpdtl tbody .pnp-qty, #tmp_penerimaanbpdtl tbody .pnp-harga', function () {
            pnpGridFormatInput($(this));
            pnpGridRecalcNilai($(this).closest('tr'));
        });

    pnpGridBindIssubEnter();


    /* Tab / Enter di dalam baris editor */
    $(document)
        .off('keydown.pnpgridnav')
        .on('keydown.pnpgridnav', '#tmp_penerimaanbpdtl tbody tr', function (e) {
            var $tr = $(this);

            if (!$tr.hasClass('pnp-row-draft') && !$tr.hasClass('pnp-row-editing')) { return; }

            var $t    = $(e.target);
            var $cont = $t.closest('.select2-container');
            var $sel  = $cont.length ? $cont.prev('select') : $();

            var isEnter = (e.key === 'Enter' || e.keyCode === 13);
            var isTab   = (e.key === 'Tab' || e.keyCode === 9);

            /* Tab dari checkbox Sub → langsung ke select item yang aktif */
            if ($t.hasClass('pnp-issub')) {
                if (isTab) {
                    e.preventDefault();
                    pnpGridFocusOpenSearch(pnpGridActiveSelect($tr));
                }
                return;
            }

            if (isEnter) {
                e.preventDefault();

                if ($t.hasClass('pnp-description')) {
                    pnpGridSave(true);
                    return;
                }

                pnpGridMoveNext(($sel.length ? $sel : $t));
                return;
            }

            if (isTab && $t.hasClass('pnp-description')) {
                e.preventDefault();
                pnpGridSave(true);
                return;
            }

            if (isTab && $sel.length) {
                e.preventDefault();
                pnpGridMoveNext($sel);
            }
        });

    /* Esc: capture phase (fokus bisa di select2) */
    document.addEventListener('keydown', function (ev) {
        if (ev.key !== 'Escape' && ev.keyCode !== 27) { return; }

        var $row = pnpGridEditor();

        if (!$row.length) { return; }

        var active = document.activeElement;

        if (!active) { return; }

        var inEditor = ($row[0] === active) ||
                       $row[0].contains(active) ||
                       $(active).closest('.select2-container').length > 0;

        if (!inEditor) { return; }

        ev.preventDefault();
        pnpGridCancel();
    }, true);

    /* shortcut tambahan: Alt+N (Ctrl+Q / Ctrl+D / Ctrl+E ditangani page JS) */
    $(document)
        .off('keydown.pnpgridshortcut')
        .on('keydown.pnpgridshortcut', function (e) {
            if (typeof isTypingTarget === 'function' && isTypingTarget(e.target)) { return; }

            var key = String(e.key || '').toLowerCase();

            if (e.altKey && key === 'n') {
                e.preventDefault();
                pnpGridNewDraft();
            }
        });

    pnpGridLoadRows(false);
});

