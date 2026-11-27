/* =========================================================================
 * GRID DETAIL BOM — INLINE (gaya pmk_brng), 3 TAB: MATERIAL / COST / WIP
 * -------------------------------------------------------------------------
 * - Satu tabel database (sc_tmp.bom_dtl) dibedakan oleh doctype_detail.
 * - Data baris: production/trans/list_bom_dtl_grid?doctype=MATERIAL|COST|WIP
 * - Simpan baris : production/trans/save_bom_mst
 *   (field detail berprefix: *Material / *Cost / *Wip — payload SAMA modal)
 * - Hapus massal  : production/trans/delete_bom_detail (ids = IDURUT)
 * - Edit in-place dari state (tanpa AJAX), Esc batal/pulihkan.
 * - Total Cost = Qty × Standart Cost (read-only, dihitung ulang saat input).
 * - Select2 item per tab: api/globalmodule/list_item_std_cost
 *   (group: Material '' , Cost 'JSA', Wip 'WIP'; WIP minimumInputLength 2).
 * ========================================================================= */

var bomGridTabs = {
    material: {
        key: 'material',
        doctype: 'MATERIAL',
        label: 'Material',
        table: '#tmp_bommaterialdtl',
        tbody: '#bomMatGridBody',
        search: '#bomMatGridSearch',
        chip: '#bomMatSelectedInfo',
        del: '#bomMatDeleteSelected',
        checkAll: '#checkAll',
        cls: 'bom-mat',
        grp: '',
        minLen: 0,
        f: {
            idbarang: 'idbarangMaterial',
            nmbarang: 'nmbarangmaterial',
            unit: 'unitMaterial',
            qty: 'qtymaterial',
            cost: 'standartcostmaterial',
            total: 'totalcostmaterial',
            desc: 'description_detail_material',
            uid: 'uniqueidMaterial'
        }
    },
    cost: {
        key: 'cost',
        doctype: 'COST',
        label: 'Cost',
        table: '#tmp_bomcostdtl',
        tbody: '#bomCostGridBody',
        search: '#bomCostGridSearch',
        chip: '#bomCostSelectedInfo',
        del: '#bomCostDeleteSelected',
        checkAll: '#checkAllCost',
        cls: 'bom-cost',
        grp: 'JSA',
        minLen: 0,
        f: {
            idbarang: 'idbarangCost',
            nmbarang: 'nmbarangcost',
            unit: 'unitCost',
            qty: 'qtycost',
            cost: 'standartcostcost',
            total: 'totalcostcost',
            desc: 'description_detail_cost',
            uid: 'uniqueidCost'
        }
    },
    wip: {
        key: 'wip',
        doctype: 'WIP',
        label: 'WIP',
        table: '#tmp_bomwipdtl',
        tbody: '#bomWipGridBody',
        search: '#bomWipGridSearch',
        chip: '#bomWipSelectedInfo',
        del: '#bomWipDeleteSelected',
        checkAll: '#checkAllWip',
        cls: 'bom-wip',
        grp: 'WIP',
        minLen: 2,
        f: {
            idbarang: 'idbarangWip',
            nmbarang: 'nmbarangwip',
            unit: 'unitWip',
            qty: 'qtywip',
            cost: 'standartcostwip',
            total: 'totalcostwip',
            desc: 'description_detail_wip',
            uid: 'uniqueidWip'
        }
    }
};

var bomGridStates = {
    material: { rows: [], saving: false, query: '', sortKey: '', sortDir: 1 },
    cost:     { rows: [], saving: false, query: '', sortKey: '', sortDir: 1 },
    wip:      { rows: [], saving: false, query: '', sortKey: '', sortDir: 1 }
};

function bomGridState(def) {
    return bomGridStates[def.key];
}

function bomGridBody(def) {
    return $(def.tbody);
}

function bomGridExists(def) {
    return bomGridBody(def).length > 0;
}

function bomGridEditor(def) {
    return bomGridBody(def).find('tr.bom-row-draft, tr.bom-row-editing').first();
}

/* ------------------------------------------------------------------ */
/* HELPER                                                             */
/* ------------------------------------------------------------------ */
function bomGridEsc(v) {
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

function bomGridNum(v) {
    if (v === null || v === undefined) { return 0; }
    var s = String(v).replace(/,/g, '').trim();
    if (s === '') { return 0; }
    var n = parseFloat(s);
    return isFinite(n) ? n : 0;
}

function bomGridDec(v, d) {
    var dec = (d == null ? 4 : d);

    return bomGridNum(v).toLocaleString('en-US', {
        minimumFractionDigits: dec,
        maximumFractionDigits: dec
    });
}

function bomGridSetNum($el, value) {
    if (!$el || !$el.length) { return; }

    $el.val(bomGridDec(value, 4));

    if (typeof jaccseparator === 'function') {
        try { jaccseparator($el[0]); } catch (e) { /* abaikan */ }
    } else if (typeof _jtsseparator === 'function') {
        try { _jtsseparator($el[0]); } catch (e2) { /* abaikan */ }
    }
}

function bomGridFormatInput($el) {
    var raw = String($el.val() || '').replace(/,/g, '').trim();

    if (raw === '') { return; }

    var n = parseFloat(raw);

    if (!isFinite(n)) { return; }

    $el.val(n.toLocaleString('en-US', {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4
    }));
}

function bomGridToast(msg, icon) {
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
function bomGridLoadRows(def, focusDraft) {
    $.ajax({
        url: HOST_URL + 'production/trans/list_bom_dtl_grid',
        type: 'GET',
        dataType: 'json',
        data: { doctype: def.doctype }
    }).done(function (res) {
        var payload = (res && res.dataTables) ? res.dataTables : res;
        var st = bomGridState(def);

        st.rows = (payload && payload.items) ? payload.items : [];
        bomGridRender(def);

        if (focusDraft === true) {
            bomGridNewDraft(def.key);
        }
    }).fail(function () {
        bomGridToast('Gagal memuat data detail ' + def.label, 'error');
    });
}

function bomGridReload(def) {
    bomGridLoadRows(def, false);
}

/* ------------------------------------------------------------------ */
/* FILTER + SORT (client side)                                        */
/* ------------------------------------------------------------------ */
function bomGridVisibleRows(def) {
    var st = bomGridState(def);
    var q  = $.trim(st.query || '').toLowerCase();

    var rows = (st.rows || []).filter(function (r) {
        if (q === '') { return true; }

        var hay = [r.idbarang, r.nmbarang, r.unit, r.description].join(' ').toLowerCase();

        return hay.indexOf(q) !== -1;
    });

    var key = st.sortKey;

    if (key) {
        var dir = st.sortDir;

        rows = rows.slice().sort(function (a, b) {
            if (key === 'qty' || key === 'standartcost' || key === 'totalcost') {
                return (bomGridNum(a[key]) - bomGridNum(b[key])) * dir;
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

function bomGridEmptyRow(def) {
    return '' +
        '<tr class="bom-empty-row">' +
        '  <td colspan="9" tabindex="0">' +
        '      <i class="fa fa-plus-circle mr-2"></i>' +
        '      Klik di sini (atau tekan Alt+N / Ctrl+Q) untuk menambah baris ' + def.label +
        '  </td>' +
        '</tr>';
}


/* ------------------------------------------------------------------ */
/* RENDER                                                             */
/* ------------------------------------------------------------------ */
function bomGridRender(def) {
    if (!bomGridExists(def)) { return; }

    var $body   = bomGridBody(def);
    var $editor = bomGridEditor(def);

    if ($editor.length) { $editor.detach(); }

    var rows = bomGridVisibleRows(def);
    var html = '';

    if (rows.length === 0) {
        html = bomGridEmptyRow(def);
    } else {
        $.each(rows, function (i, row) {
            html += bomGridSavedRow(def, row);
        });
    }

    $body.html(html);

    $(def.checkAll).prop('checked', false);

    if ($editor.length) {
        $body.append($editor);
    }

    $(def.table).toggleClass('bom-editing', bomGridEditor(def).length > 0);

    bomGridUpdateSelectedInfo(def);
}

function bomGridSavedRow(def, row) {
    var id   = bomGridEsc(row.idurut || '');
    var desc = $.trim(row.description || '');
    var nmb  = $.trim(row.nmbarang || '');

    return '' +
        '<tr class="bom-row-saved" data-idurut="' + id + '" data-uniqueid="' + bomGridEsc(row.uniqueid || '') + '">' +
        '  <td class="bom-col-no"><input type="checkbox" class="row-check" value="' + id + '" title="Pilih baris (hapus massal: Ctrl+D)"></td>' +
        '  <td title="' + bomGridEsc(row.idbarang) + '">' + bomGridEsc(row.idbarang) + '</td>' +
        '  <td title="' + bomGridEsc(nmb) + '"><span class="bom-readonly-text">' + bomGridEsc(nmb) + '</span></td>' +
        '  <td class="text-end"><span class="bom-readonly-number" data-export="' + bomGridNum(row.qty) + '">' + bomGridDec(row.qty, 4) + '</span></td>' +
        '  <td class="text-center">' + bomGridEsc(row.unit) + '</td>' +
        '  <td class="text-end"><span class="bom-readonly-number" data-export="' + bomGridNum(row.standartcost) + '">' + bomGridDec(row.standartcost, 4) + '</span></td>' +
        '  <td class="text-end"><span class="bom-readonly-number" data-export="' + bomGridNum(row.totalcost) + '">' + bomGridDec(row.totalcost, 4) + '</span></td>' +
        '  <td title="' + bomGridEsc(desc) + '"><span class="bom-readonly-text">' + bomGridEsc(desc) + '</span></td>' +
        '  <td class="bom-col-aksi">' +
        '      <button type="button" class="bom-btn bom-btn-edit" data-act="edit" title="Edit baris">' +
        '          <i class="fa fa-pencil"></i></button>' +
        '  </td>' +
        '</tr>';
}

function bomGridEditorRow(def) {
    return '' +
        '<tr class="bom-row-draft">' +
        '  <td class="bom-col-no"><input type="checkbox" disabled title="Baris belum tersimpan"></td>' +
        '  <td class="bom-col-item"><select class="bom-item" style="width:100%"></select></td>' +
        '  <td><input type="text" class="form-control form-control-sm bom-nmbarang" readonly tabindex="-1"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end jaccseparator bom-qty"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-center bom-unit" readonly tabindex="-1"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end jaccseparator bom-cost"></td>' +
        '  <td><input type="text" class="form-control form-control-sm text-end bom-total" readonly tabindex="-1"></td>' +
        '  <td><input type="text" class="form-control form-control-sm bom-description"></td>' +
        '  <td class="bom-col-aksi">' +
        '      <button type="button" class="bom-btn bom-btn-save" data-act="save" title="Simpan baris (Enter)">' +
        '          <i class="fa fa-check"></i></button>' +
        '      <button type="button" class="bom-btn bom-btn-cancel" data-act="cancel" title="Batal (Esc)">' +
        '          <i class="fa fa-times"></i></button>' +
        '  </td>' +
        '</tr>';
}


/* ------------------------------------------------------------------ */
/* SELECT2 ITEM                                                       */
/* ------------------------------------------------------------------ */
function bomGridFormatItem(repo) {
    if (repo.loading) { return repo.text; }

    var idb = repo.idbarang || repo.id || '';
    var nmb = repo.nmbarang || repo.text || '';

    return "<div class='select2-result-repository__description'>" +
        bomGridEsc(idb) + "   <i class='fa fa-circle-o'></i>   " + bomGridEsc(nmb) +
        "</div>";
}

function bomGridFormatItemSel(repo) {
    return repo.nmbarang || repo.text;
}

function bomGridCloseSelect2($select) {
    if (!$select || !$select.length) { return; }
    if (!$select.hasClass('select2-hidden-accessible')) { return; }

    var inst = $select.data('select2');

    if (inst && typeof inst.isOpen === 'function' && inst.isOpen()) {
        try { $select.select2('close'); } catch (e) { /* abaikan */ }
    }
}

function bomGridFixSelectWidth($el) {
    if (!$el || !$el.length) { return; }
    if (!$el.hasClass('select2-hidden-accessible')) { return; }

    var $container = $el.next('.select2-container');

    if ($container.length) {
        $container.css('width', '100%');
    }
}

function bomGridSearchField($select) {
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

function bomGridFocusOpenSearch($el) {
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
        var $search = bomGridSearchField($el);

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

/* Total Cost = Qty × Standart Cost (mengikuti hitungTotalCost*() versi modal) */
function bomGridRecalcTotal(def, $tr) {
    var qty  = bomGridNum($tr.find('.bom-qty').val());
    var cost = bomGridNum($tr.find('.bom-cost').val());

    bomGridSetNum($tr.find('.bom-total'), qty * cost);
}

function bomGridInitSelect(def, $tr) {
    var $item = $tr.find('.bom-item');

    if ($item.hasClass('select2-hidden-accessible')) { return; }

    $item.select2({
        placeholder: 'Choose Your Item List',
        allowClear: true,
        width: '100%',
        minimumInputLength: def.minLen,
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
                    docdate: ($('#docdate').val() || ''),
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
        templateResult: bomGridFormatItem,
        templateSelection: bomGridFormatItemSel
    }).on('select2:select', function (e) {
        var data = e.params.data || {};
        var $row = $(this).closest('tr');
        var d    = bomGridTabs[$row.attr('data-bomtab') || ''] || def;

        $row.find('.bom-nmbarang').val($.trim(data.nmbarang || ''));
        $row.find('.bom-unit').val($.trim(data.unit || ''));
        bomGridSetNum($row.find('.bom-cost'), data.newcost);

        bomGridClearInvalid($(this));
        bomGridRecalcTotal(d, $row);
    }).on('select2:clear', function () {
        var $row = $(this).closest('tr');
        var d    = bomGridTabs[$row.attr('data-bomtab') || ''] || def;

        $row.find('.bom-nmbarang').val('');
        $row.find('.bom-unit').val('');
        $row.find('.bom-cost').val('');

        bomGridRecalcTotal(d, $row);
    });
}


/* ------------------------------------------------------------------ */
/* BUKA / ISI / BATAL BARIS EDITOR                                    */
/* ------------------------------------------------------------------ */
function bomGridFillEditor(def, $tr, data) {
    $tr.attr('data-bomtab', def.key);

    if (data.idbarang) {
        $tr.find('.bom-item')
            .append(new Option(data.nmbarang || data.idbarang, data.idbarang, true, true))
            .val(data.idbarang).trigger('change');
    }

    $tr.find('.bom-nmbarang').val(data.nmbarang || '');
    $tr.find('.bom-unit').val(data.unit || '');
    bomGridSetNum($tr.find('.bom-qty'), data.qty);
    bomGridSetNum($tr.find('.bom-cost'), data.standartcost);
    $tr.find('.bom-description').val(data.description || '');

    bomGridRecalcTotal(def, $tr);

    bomGridFixSelectWidth($tr.find('.bom-item'));
}

function bomGridNewDraft(key) {
    var def = bomGridTabs[key];

    if (!def || !bomGridExists(def)) { return; }

    var $existing = bomGridEditor(def);

    if ($existing.length) {
        bomGridFocusOpenSearch($existing.find('.bom-item'));
        return;
    }

    var $body = bomGridBody(def);

    $body.find('tr.bom-empty-row').remove();
    $body.append(bomGridEditorRow(def));

    var $tr = bomGridEditor(def);

    $tr.attr('data-bomtab', def.key);

    bomGridInitSelect(def, $tr);

    $(def.table).addClass('bom-editing');
    bomGridUpdateSelectedInfo(def);

    bomGridFocusOpenSearch($tr.find('.bom-item'));
}

function bomGridRemoveEditor(def) {
    bomGridEditor(def).remove();
    $(def.table).removeClass('bom-editing');
}

function bomGridCancel(key) {
    var def = bomGridTabs[key];

    if (!def) { return; }

    if (!bomGridEditor(def).length) { return; }

    bomGridRemoveEditor(def);
    bomGridRender(def);
}

/* Edit in-place: data diambil dari state (tanpa AJAX) */
function bomGridEditRow(def, idurut) {
    var id = $.trim(String(idurut == null ? '' : idurut));

    if (id === '') { return; }

    var row = null;

    $.each(bomGridState(def).rows || [], function (i, r) {
        if (String(r.idurut) === id) { row = r; return false; }
    });

    if (!row) {
        bomGridToast('Data baris tidak ditemukan, muat ulang halaman', 'warning');
        return;
    }

    var $target = bomGridBody(def).find('tr.bom-row-saved[data-idurut="' + id + '"]');

    if (!$target.length) { return; }

    if (bomGridEditor(def).length) {
        bomGridRemoveEditor(def);
        bomGridRender(def);

        $target = bomGridBody(def).find('tr.bom-row-saved[data-idurut="' + id + '"]');

        if (!$target.length) { return; }
    }

    $target.replaceWith(bomGridEditorRow(def));

    var $tr = bomGridEditor(def);

    $tr.removeClass('bom-row-draft').addClass('bom-row-editing')
        .attr('data-idurut', id)
        .attr('data-uniqueid', row.uniqueid || '');

    bomGridInitSelect(def, $tr);
    bomGridFillEditor(def, $tr, row);

    $(def.table).addClass('bom-editing');
    bomGridUpdateSelectedInfo(def);

    bomGridFocusOpenSearch($tr.find('.bom-item'));
}

function bomGridEditSelected(key) {
    var def  = bomGridTabs[key];
    var ids  = bomGridCheckedIds(def);

    if (ids.length === 0) {
        bomGridToast('Pilih satu baris yang akan diedit', 'warning');
        return;
    }

    if (ids.length > 1) {
        bomGridToast('Edit hanya boleh satu baris', 'warning');
        return;
    }

    bomGridEditRow(def, ids[0]);
}


/* ------------------------------------------------------------------ */
/* VALIDASI (tooltip, tanpa Swal)                                     */
/* ------------------------------------------------------------------ */
function bomGridMarkInvalid($el, msg) {
    if (!$el || !$el.length) { return; }

    $el.addClass('bom-invalid');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.next('.select2-container').find('.select2-selection--single').addClass('bom-invalid');
    }

    $el.attr('title', msg || 'Wajib diisi');

    if (typeof $el.tooltip === 'function') {
        try { $el.tooltip('dispose').tooltip({ trigger: 'manual' }).tooltip('show'); } catch (e) { /* noop */ }
    }
}

function bomGridClearInvalid($el) {
    if (!$el || !$el.length) { return; }

    $el.removeClass('bom-invalid').removeAttr('title');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.next('.select2-container').find('.select2-selection--single').removeClass('bom-invalid');
    }

    if (typeof $el.tooltip === 'function') {
        try { $el.tooltip('dispose'); } catch (e) { /* noop */ }
    }
}

function bomGridUpdateSelectedInfo(def) {
    var $info = $(def.chip);

    if (!$info.length) { return; }

    var total   = bomGridBody(def).find('tr.bom-row-saved').length;
    var checked = bomGridBody(def).find('.row-check:checked').length;

    $info.text(checked + ' dari ' + total + ' baris dipilih');
}

/* ------------------------------------------------------------------ */
/* SIMPAN BARIS                                                       */
/* ------------------------------------------------------------------ */
function bomGridSave(key, openNext) {
    var def = bomGridTabs[key];

    if (!def || bomGridState(def).saving) { return; }

    var $tr = bomGridEditor(def);

    if (!$tr.length) { return; }

    var $item    = $tr.find('.bom-item');
    var idbarang = $.trim($item.val() || '').toString();

    if (idbarang === '') {
        bomGridMarkInvalid($item, 'Item wajib dipilih');
        bomGridFocusOpenSearch($item);
        return;
    }

    var $qty   = $tr.find('.bom-qty');
    var qtyRaw = $qty.val();

    if ($.trim(String(qtyRaw || '')) === '') {
        bomGridMarkInvalid($qty, 'Qty wajib diisi');
        $qty.trigger('focus').select();
        return;
    }

    var qty = bomGridNum(qtyRaw);

    if (qty <= 0) {
        bomGridMarkInvalid($qty, 'Qty harus lebih besar dari 0');
        $qty.trigger('focus').select();
        return;
    }

    var cost  = bomGridNum($tr.find('.bom-cost').val());
    var total = qty * cost;

    bomGridSetNum($tr.find('.bom-total'), total);

    /* Payload: seluruh field header form ikut, field detail di-set ulang */
    var form     = document.getElementById('formStandarCostMst');
    var formData = form ? new FormData(form) : new FormData();

    formData.set('idurut', ($tr.attr('data-idurut') || '').toString());
    formData.set('doctype_detail', def.doctype);

    formData.set(def.f.idbarang, idbarang);
    formData.set(def.f.nmbarang, $.trim($tr.find('.bom-nmbarang').val() || '').toString());
    formData.set(def.f.unit, $.trim($tr.find('.bom-unit').val() || '').toString());
    formData.set(def.f.qty, String(qty));
    formData.set(def.f.cost, String(cost));
    formData.set(def.f.total, String(total));
    formData.set(def.f.desc, $.trim($tr.find('.bom-description').val() || '').toUpperCase());
    formData.set(def.f.uid, ($tr.attr('data-uniqueid') || '').toString());

    formData.set(
        'docno',
        ($('#prefix').val() || '') + '/' +
        ($('#infix').val() || '') + '/' +
        ($('#suffix').val() || '')
    );

    $.each(['docref', 'cabang', 'pemohon', 'docdate', 'idbarang_jadi', 'keterangan',
            'minimumqty', 'buildunit', 'buildfor'],
        function (i, name) {
            if (formData.has(name)) { return; }

            var $f = $('#' + name);

            if ($f.length) {
                formData.set(name, $f.val() || '');
            }
        });

    /* field header numerik WAJIB angka murni (hindari "24,300.3345" → numeric) */
    $.each(['minimumqty', 'buildfor'], function (i, name) {
        var $f = $('#' + name);

        if ($f.length) {
            formData.set(name, String(bomGridNum($f.val())));
        }
    });

    bomGridState(def).saving = true;

    var $btn = $tr.find('[data-act="save"]');
    $btn.prop('disabled', true);

    $.ajax({
        url: HOST_URL + 'production/trans/save_bom_mst',
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

                bomGridRemoveEditor(def);
                bomGridLoadRows(def, openNext === true);
            } else {
                bomGridToast((res && res.message) || 'Gagal menyimpan data', 'warning');
            }
        },

        error: function () {
            bomGridToast('Terjadi kesalahan server', 'error');
        },

        complete: function () {
            bomGridState(def).saving = false;
            $btn.prop('disabled', false);
        }
    });
}


/* ------------------------------------------------------------------ */
/* HAPUS (ids = IDURUT, sesuai delete_bom_detail)                     */
/* ------------------------------------------------------------------ */
function bomGridCheckedIds(def) {
    var ids = [];

    bomGridBody(def).find('.row-check:checked').each(function () {
        ids.push($(this).val());
    });

    return ids;
}

function bomGridDelete(def, ids) {
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
            url: HOST_URL + 'production/trans/delete_bom_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json'
        }).done(function (res) {
            if (res && res.status) {
                /* tanpa popup sukses */
                bomGridRemoveEditor(def);
                $(def.checkAll).prop('checked', false);
                bomGridLoadRows(def, false);

                if (typeof documentReadable === 'function') {
                    documentReadable();
                }
            } else {
                bomGridToast((res && res.message) || 'Gagal hapus data', 'error');
            }
        }).fail(function () {
            bomGridToast('Gagal hapus data', 'error');
        });
    });
}

function bomGridDeleteRow(def, idurut) {
    if (!idurut) { return; }

    bomGridDelete(def, [idurut]);
}

function bomGridDeleteSelected(key) {
    var def = bomGridTabs[key];
    var ids = bomGridCheckedIds(def);

    if (ids.length === 0) {
        bomGridToast('Pilih baris yang akan dihapus', 'warning');
        return;
    }

    bomGridDelete(def, ids);
}

/* ------------------------------------------------------------------ */
/* NAVIGASI KEYBOARD                                                  */
/* ------------------------------------------------------------------ */
function bomGridFields($tr) {
    return [
        $tr.find('.bom-item')[0],
        $tr.find('.bom-qty')[0],
        $tr.find('.bom-cost')[0],
        $tr.find('.bom-description')[0]
    ];
}

function bomGridMoveNext($el) {
    var $def = bomGridTabs[$el.closest('tr').attr('data-bomtab') || ''] || null;
    var $tr  = $def ? bomGridEditor($def) : $();

    if (!$tr.length) { return; }

    var fields = bomGridFields($tr);
    var idx    = -1;

    $.each(fields, function (i, el) {
        if (el === $el[0]) { idx = i; return false; }
    });

    if (idx === -1 || idx >= fields.length - 1) { return; }

    var next = fields[idx + 1];

    if (!next) { return; }

    var $next = $(next);

    if ($next.hasClass('select2-hidden-accessible')) {
        bomGridFocusOpenSearch($next);
    } else {
        $next.trigger('focus');
    }
}


/* ------------------------------------------------------------------ */
/* RESOLVE TAB DARI ELEMEN                                            */
/* ------------------------------------------------------------------ */
function bomGridDefOf($el) {
    var id = ($el.closest('table').attr('id') || '').trim();
    var found = null;

    $.each(bomGridTabs, function (k, d) {
        if (d.table === '#' + id) { found = d; return false; }
    });

    return found;
}

function bomGridTables() {
    var list = [];

    $.each(bomGridTabs, function (k, d) { list.push(d.table + ' tbody'); });

    return list.join(', ');
}

/* selector gabungan untuk elemen di dalam tabel (mis. '.bom-qty') */
function bomGridSel(inner) {
    var list = [];

    $.each(bomGridTabs, function (k, d) { list.push(d.table + ' tbody ' + inner); });

    return list.join(', ');
}

/* selector gabungan untuk elemen header tabel */
function bomGridSelHead(inner) {
    var list = [];

    $.each(bomGridTabs, function (k, d) { list.push(d.table + ' thead ' + inner); });

    return list.join(', ');
}

/* def tab yang sedang aktif (dipakai shortcut Alt+N & Tab dari header) */
function bomGridActiveDef() {
    var $pane = $('.tab-pane.active').first();
    var def   = $pane.length ? bomGridDefOf($pane.find('table').first()) : null;

    return def || bomGridTabs.material;
}

/* ------------------------------------------------------------------ */
/* BINDING GRID (3 tab)                                               */
/* ------------------------------------------------------------------ */
$(function () {
    var selBody = bomGridTables();

    /* pencarian live */
    $(document)
        .off('input.bomgridsearch')
        .on('input.bomgridsearch', '#bomMatGridSearch, #bomCostGridSearch, #bomWipGridSearch', function () {
            var def = bomGridDefOf($(this).closest('.detail-tab-wrapper').find('table').first());

            if (!def) { return; }

            bomGridState(def).query = this.value || '';
            bomGridRender(def);
        });

    /* empty state → baris baru */
    $(document)
        .off('click.bomgridempty')
        .on('click.bomgridempty', bomGridSel('tr.bom-empty-row td'), function () {
            var def = bomGridDefOf($(this));
            if (def) { bomGridNewDraft(def.key); }
        });

    $(document)
        .off('keydown.bomgridempty')
        .on('keydown.bomgridempty', bomGridSel('tr.bom-empty-row td'), function (e) {
            if (e.key !== 'Enter' && e.keyCode !== 13) { return; }

            e.preventDefault();

            var def = bomGridDefOf($(this));
            if (def) { bomGridNewDraft(def.key); }
        });

    /* sort header */
    $(document)
        .off('click.bomgridsort')
        .on('click.bomgridsort', bomGridSelHead('th.bom-sortable'), function () {
            var def = bomGridDefOf($(this));
            var key = $(this).data('key');

            if (!def || !key) { return; }

            var st = bomGridState(def);

            if (st.sortKey === key) {
                st.sortDir = -st.sortDir;
            } else {
                st.sortKey = key;
                st.sortDir = 1;
            }

            $(def.table + ' thead th').removeClass('bom-sort-asc bom-sort-desc');
            $(this).addClass(st.sortDir === 1 ? 'bom-sort-asc' : 'bom-sort-desc');

            bomGridRender(def);
        });

    /* klik baris tersimpan → centang */
    $(document)
        .off('click.bomgridrow')
        .on('click.bomgridrow', bomGridSel('tr.bom-row-saved'), function (e) {
            if ($(e.target).is('input, button, i, a')) { return; }

            var $cb = $(this).find('.row-check');

            $cb.prop('checked', !$cb.prop('checked')).trigger('change');
        });

    /* chip info pilihan */
    $(document)
        .off('change.bomgridcheck')
        .on('change.bomgridcheck', bomGridSel('.row-check'), function () {
            var def = bomGridDefOf($(this));
            if (def) { bomGridUpdateSelectedInfo(def); }
        });

    /* tombol aksi per baris */
    $(document)
        .off('click.bomgridact')
        .on('click.bomgridact', bomGridSel('[data-act]'), function (e) {
            e.preventDefault();

            var $btn = $(this);
            var def  = bomGridDefOf($btn);
            var act  = String($btn.data('act') || '');
            var $tr  = $btn.closest('tr');

            if (!def) { return; }

            if (act === 'edit') {
                bomGridEditRow(def, $tr.attr('data-idurut'));
            } else if (act === 'save') {
                bomGridSave(def.key, true);
            } else if (act === 'cancel') {
                bomGridCancel(def.key);
            }
        });

    /* tombol Hapus di tiap tab */
    $(document)
        .off('click.bomgriddelete')
        .on('click.bomgriddelete', '#bomMatDeleteSelected, #bomCostDeleteSelected, #bomWipDeleteSelected', function (e) {
            e.preventDefault();

            if (this.id === 'bomMatDeleteSelected') { bomGridDeleteSelected('material'); }
            else if (this.id === 'bomCostDeleteSelected') { bomGridDeleteSelected('cost'); }
            else { bomGridDeleteSelected('wip'); }
        });

    /* total cost dihitung ulang saat qty/cost diketik */
    $(document)
        .off('input.bomgridtotal')
        .on('input.bomgridtotal', bomGridSel('.bom-qty') + ', ' + bomGridSel('.bom-cost'), function () {
            var def = bomGridDefOf($(this));
            if (def) { bomGridRecalcTotal(def, $(this).closest('tr')); }
        });

    /* format ribuan saat blur */
    $(document)
        .off('blur.bomgridnum')
        .on('blur.bomgridnum', bomGridSel('.bom-qty') + ', ' + bomGridSel('.bom-cost'), function () {
            var def = bomGridDefOf($(this));
            if (!def) { return; }

            bomGridFormatInput($(this));
            bomGridRecalcTotal(def, $(this).closest('tr'));
        });


    /* Tab / Enter di dalam baris editor */
    $(document)
        .off('keydown.bomgridnav')
        .on('keydown.bomgridnav', bomGridSel('tr'), function (e) {
            var $tr = $(this);

            if (!$tr.hasClass('bom-row-draft') && !$tr.hasClass('bom-row-editing')) { return; }

            var def = bomGridDefOf($tr);

            if (!def) { return; }

            var $t    = $(e.target);
            var $cont = $t.closest('.select2-container');
            var $sel  = $cont.length ? $cont.prev('select') : $();

            var isEnter = (e.key === 'Enter' || e.keyCode === 13);
            var isTab   = (e.key === 'Tab' || e.keyCode === 9);

            if (isEnter) {
                e.preventDefault();

                if ($t.hasClass('bom-description')) {
                    bomGridSave(def.key, true);
                    return;
                }

                bomGridMoveNext(($sel.length ? $sel : $t));
                return;
            }

            if (isTab && $t.hasClass('bom-description')) {
                e.preventDefault();
                bomGridSave(def.key, true);
                return;
            }

            if (isTab && $sel.length) {
                e.preventDefault();
                bomGridMoveNext($sel);
            }
        });

    /* Esc: capture phase (fokus bisa di select2) */
    document.addEventListener('keydown', function (ev) {
        if (ev.key !== 'Escape' && ev.keyCode !== 27) { return; }

        var active = document.activeElement;

        if (!active) { return; }

        var $tr = $(active).closest('tr.bom-row-draft, tr.bom-row-editing');

        if (!$tr.length && $(active).closest('.select2-container').length) {
            var $open = $('.select2-container--open').last().prev('select.bom-item');
            $tr = $open.closest('tr.bom-row-draft, tr.bom-row-editing');
        }

        if (!$tr.length) { return; }

        var def = bomGridDefOf($tr);

        if (!def) { return; }

        ev.preventDefault();
        bomGridCancel(def.key);
    }, true);

    /* shortcut tambahan: Alt+N pada tab aktif */
    $(document)
        .off('keydown.bomgridshortcut')
        .on('keydown.bomgridshortcut', function (e) {
            if (typeof isTypingTarget === 'function' && isTypingTarget(e.target)) { return; }

            if (!e.altKey || String(e.key || '').toLowerCase() !== 'n') { return; }

            var def = bomGridActiveDef();

            if (!def) { return; }

            e.preventDefault();
            bomGridNewDraft(def.key);
        });

    /* Tab dari field terakhir header (#keterangan) → langsung buka baris baru tab aktif */
    $(document)
        .off('keydown.bomgridheader')
        .on('keydown.bomgridheader', '#keterangan', function (e) {
            var isTab = (e.key === 'Tab' || e.keyCode === 9);

            if (!isTab || e.shiftKey) { return; }

            var def = bomGridActiveDef();

            if (!def || !bomGridExists(def)) { return; }

            e.preventDefault();
            bomGridNewDraft(def.key);
        });

    /* muat data 3 tab */
    $.each(bomGridTabs, function (k, d) {
        bomGridLoadRows(d, false);
    });
});


/* ------------------------------------------------------------------ */
/* ALIHKAN ENTRY POINT MODAL KE GRID (hanya bila grid aktif)          */
/* ------------------------------------------------------------------ */
/* bom.js mendefinisikan 15 fungsi entry point (3 tab x 5). Alih-alih  */
/* menambah guard di 15 tempat, seluruh nama globalnya diarahkan ke    */
/* engine grid saat grid body tersedia. Bila grid dimatikan (rollback),*/
/* blok ini otomatis tidak aktif dan modal/DataTables lama kembali.    */
(function () {
    if (typeof document === 'undefined') { return; }

    if (!document.getElementById('bomMatGridBody')) { return; }

    /* tombol + (Ctrl+Q) */
    window.btnInputDetailMaterial = function () { bomGridNewDraft('material'); };
    window.btnInputDetailCost     = function () { bomGridNewDraft('cost'); };
    window.btnInputDetailWip      = function () { bomGridNewDraft('wip'); };

    /* tombol Edit (Ctrl+E) */
    window.updateDetailBomMaterial = function () { bomGridEditSelected('material'); };
    window.updateDetailBomCost     = function () { bomGridEditSelected('cost'); };
    window.updateDetailBomWip      = function () { bomGridEditSelected('wip'); };

    /* tombol Hapus (Ctrl+D) */
    window.btnDeleteDetailMaterial = function () { bomGridDeleteSelected('material'); };
    window.btnDeleteDetailCost     = function () { bomGridDeleteSelected('cost'); };
    window.btnDeleteDetailWip      = function () { bomGridDeleteSelected('wip'); };

    /* init DataTables detail dimatikan (grid memakai render sendiri) */
    window.tabletmpBOMMaterial = function () { /* grid aktif */ };
    window.tabletmpBOMCost     = function () { /* grid aktif */ };
    window.tabletmpBOMWip      = function () { /* grid aktif */ };

    /* reload detail setelah simpan/hapus header */
    window.reload_bom_material_dtl = function () { bomGridReload(bomGridTabs.material); };
    window.reload_bom_cost_dtl     = function () { bomGridReload(bomGridTabs.cost); };
    window.reload_bom_wip_dtl      = function () { bomGridReload(bomGridTabs.wip); };
})();

