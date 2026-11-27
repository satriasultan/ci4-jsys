/* =========================================================================
 * INLINE DETAIL ENTRY - PENJUALAN
 * -------------------------------------------------------------------------
 * Grid detail diinput langsung pada baris di dalam tfoot #tabpenjualandtl,
 * mengikuti pola grid inline Sales Order (v_add_salesorder / salesorder.js):
 *   - tombol toolbar   : #btnAddDetail / #btnUpdateDetail / #btnDeleteDetail
 *   - tombol baris     : .pj-row-btn-edit / .pj-row-btn-delete
 *   - tombol input baris: .pj-inline-save / .pj-inline-cancel
 *
 * Endpoint yang dipakai (sudah tersedia di PostSales):
 *   sales/postsales/savePenjualanDetail   (ADD dari SO/SJ, EDIT per baris)
 *   sales/postsales/get_penjualan_detail  (data baris untuk mode edit)
 *   sales/postsales/delete_penjualan_detail
 *   sales/postsales/stock_penjualan_item
 *   sales/postsales/list_so_penjualan
 *   api/globalmodule/list_sj
 * ========================================================================= */

var PJ_NUM_HINT = 'Gunakan titik (.) untuk desimal, maksimal 4 angka di belakang koma';

/* Baris input berada di tfoot agar isi tbody (DataTables) tidak terganggu */
function pjInlineFoot() {
    return $('#pjInlineFoot');
}

function pjInlineActiveRow() {
    return pjInlineFoot().find('tr.pj-inline-row').first();
}

function pjInlineFormatNum(value) {
    var num = convertToDbNumber((value === undefined || value === null) ? '' : value);

    // Nilai kosong / nol ditampilkan kosong (bukan "0.0000")
    if (num === 0 || !isFinite(num)) return '';

    return salesFormatNumber(num, SALES_DECIMAL_DIGITS);
}

function pjInlineSetNum($input, value) {
    if (!$input || !$input.length) return;
    $input.val(pjInlineFormatNum(value));
}

/* No. Jurnal dari prefix / infix / suffix (pola salesorder) */
function pjInlineDocno() {
    var prefix = $.trim($('#prefix').val() || '');
    var infix  = $.trim($('#infix').val() || '');
    var suffix = $.trim($('#suffix').val() || '');

    if (prefix === '' && infix === '' && suffix === '') return '';

    return prefix + '/' + infix + '/' + suffix;
}

/* Kode customer terpilih (untuk filter SO / SJ) */
function pjInlineCustomerCode() {
    var data = $('#kdcustomer').select2('data');

    if (data && data.length > 0) {
        return $.trim(data[0].kdcustomer || data[0].id || '');
    }

    return $.trim($('#kdcustomer').val() || '');
}

/* Sembunyikan pesan empty state selama baris input inline terbuka */
function pjInlineSyncEmptyState() {
    var $table = $('#tabpenjualandtl');
    if (!$table.length) return;

    $table.toggleClass('pj-has-draft', pjInlineActiveRow().length > 0);
}

function pjInlineDestroySelects($tr) {
    $tr.find('select').each(function () {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
}

function pjInlineRemoveRow() {
    var $tr = pjInlineActiveRow();
    if (!$tr.length) return;

    pjInlineDestroySelects($tr);
    $tr.remove();

    pjInlineSyncEmptyState();
}

/* Pasang nilai awal pada select2 tanpa harus menunggu AJAX */
function pjInlineSetSelectValue($sel, value, text) {
    if (!$sel || !$sel.length) return;

    value = $.trim(value === undefined || value === null ? '' : String(value));

    if (value === '' || value.toUpperCase() === 'NULL') {
        $sel.val(null).trigger('change');
        return;
    }

    if ($sel.find('option').filter(function () { return $(this).val() === value; }).length === 0) {
        $sel.append(new Option(text || value, value, true, true));
    }

    $sel.val(value).trigger('change');
}

function pjInlineFocusSj($tr) {
    if (!$tr || !$tr.length) return;

    setTimeout(function () {
        var $sj = $tr.find('select.pj-inline-sj');
        var $so = $tr.find('select.pj-inline-so');

        if ($sj.length && $sj.data('select2')) {
            $sj.select2('open');
        } else if ($so.length && $so.data('select2')) {
            $so.select2('open');
        }
    }, 50);
}

function pjInlineFocusGudang($tr) {
    if (!$tr || !$tr.length) return;

    setTimeout(function () {
        var $gudang = $tr.find('select.pj-inline-gudang');
        if (!$gudang.length) return;

        if ($gudang.data('select2')) {
            $gudang.select2('open');
        } else {
            $gudang.focus();
        }
    }, 50);
}

/* -------------------------------------------------------------------------
 * SELECT2 PADA BARIS INLINE
 * ---------------------------------------------------------------------- */

/* Dropdown No. SJ (sumber detail dari surat jalan) */
function pjInlineInitSjSelect($tr) {
    var $sj = $tr.find('select.pj-inline-sj');
    if (!$sj.length) return;

    $sj.select2({
        placeholder: 'Pilih No. SJ',
        allowClear: true,
        width: '100%',
        dropdownParent: $(document.body),
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_sj',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term,
                    _page_: params.page,
                    _draw_: true,
                    _start_: 1,
                    _perpage_: 30,
                    _paramglobal_: '',
                    _paramglobalcust_: pjInlineCustomerCode(),
                    _parameterx_: '',
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items || [],
                    pagination: {
                        more: (params.page * 30) < (data.total_count || 0)
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: (typeof formatSJ === 'function') ? formatSJ : undefined,
        templateSelection: (typeof formatSJSelection === 'function') ? formatSJSelection : undefined
    }).on('select2:select', function () {
        // SO dan SJ tidak boleh dipilih bersamaan (validasi server: XOR)
        $tr.find('select.pj-inline-so').val(null).trigger('change');
    });
}

/* Dropdown No. SO (sumber detail dari sales order) */
function pjInlineInitSoSelect($tr) {
    var $so = $tr.find('select.pj-inline-so');
    if (!$so.length) return;

    $so.select2({
        placeholder: 'Pilih No. SO',
        allowClear: true,
        width: '100%',
        dropdownParent: $(document.body),
        ajax: {
            url: HOST_URL + 'sales/postsales/list_so_penjualan',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term,
                    _page_: params.page,
                    _draw_: true,
                    _start_: 1,
                    _perpage_: 30,
                    _branchid_: $.trim($('#cabang').val() || ''),
                    _paramglobal_: '',
                    _paramglobalcust_: pjInlineCustomerCode(),
                    _parameterx_: '',
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items || [],
                    pagination: {
                        more: (params.page * 30) < (data.total_count || 0)
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: (typeof formatSO === 'function') ? formatSO : undefined,
        templateSelection: (typeof formatSOSelection === 'function') ? formatSOSelection : undefined
    }).on('select2:select', function () {
        // SO dan SJ tidak boleh dipilih bersamaan (validasi server: XOR)
        $tr.find('select.pj-inline-sj').val(null).trigger('change');
    });
}


/* Dropdown Principal (mode edit) */
function pjInlineInitPrincipalSelect($tr) {
    var $principal = $tr.find('select.pj-inline-principal');
    if (!$principal.length) return;

    $principal.select2({
        placeholder: 'Principal',
        allowClear: true,
        width: '100%',
        dropdownParent: $(document.body),
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_principal',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term,
                    _page_: params.page,
                    _draw_: true,
                    _start_: 1,
                    _perpage_: 30,
                    _paramglobal_: '',
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items || [],
                    pagination: {
                        more: (params.page * 30) < (data.total_count || 0)
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: (typeof formatPrincipal === 'function') ? formatPrincipal : undefined,
        templateSelection: (typeof formatPrincipalSelection === 'function') ? formatPrincipalSelection : undefined
    });
}

/* Dropdown Gudang / lokasi asal (mode edit) */
function pjInlineInitGudangSelect($tr) {
    var $gudang = $tr.find('select.pj-inline-gudang');
    if (!$gudang.length) return;

    $gudang.select2({
        placeholder: 'Gudang',
        allowClear: true,
        width: '100%',
        dropdownParent: $(document.body),
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_mlocation',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term,
                    _page_: params.page,
                    _draw_: true,
                    _start_: 1,
                    _perpage_: 30,
                    _paramglobal_: '',
                    _parameterx_: '',
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items || [],
                    pagination: {
                        more: (params.page * 30) < (data.total_count || 0)
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: (typeof formatLocation === 'function') ? formatLocation : undefined,
        templateSelection: (typeof formatLocationSelection === 'function') ? formatLocationSelection : undefined
    }).on('change', function () {
        pjInlineLoadStock($tr);
    });
}


/* Dropdown Batch / Specification (mode edit) */
function pjInlineInitSpecSelect($tr) {
    var $spec = $tr.find('select.pj-inline-spec');
    if (!$spec.length) return;

    $spec.select2({
        placeholder: 'Batch / Spec',
        allowClear: true,
        width: '100%',
        dropdownParent: $(document.body),
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_batch_item',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term,
                    _page_: params.page,
                    _draw_: true,
                    _start_: 1,
                    _perpage_: 30,
                    _paramglobal_: '',
                    _parameterx_: $.trim(String($tr.data('idbarang') || '')),
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                var results = $.map(data.items || [], function (item) {
                    return {
                        id: item.batch,
                        text: item.batch,
                        batch: item.batch
                    };
                });

                return {
                    results: results,
                    pagination: {
                        more: (params.page * 30) < (data.total_count || 0)
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    }).on('change', function () {
        pjInlineLoadStock($tr);
    });
}

/* Stok gudang pada baris (dipakai validasi qty saat mode edit) */
function pjInlineLoadStock($tr) {
    var $stok = $tr.find('[data-field="stok"]');
    if (!$stok.length) return;

    var idbarang = $.trim(String($tr.data('idbarang') || ''));
    var idgudang = $.trim($tr.find('select.pj-inline-gudang').val() || '');
    var idspec   = $.trim($tr.find('select.pj-inline-spec').val() || '');

    $tr.data('stock', null);
    $stok.val('');

    if (idbarang === '' || idgudang === '') return;

    $.ajax({
        url: HOST_URL + 'sales/postsales/stock_penjualan_item',
        type: 'POST',
        dataType: 'json',
        data: { idbarang: idbarang, idgudang: idgudang, idspec: idspec }
    }).done(function (res) {
        var total = parseFloat((res && res.total != null) ? res.total : 0) || 0;
        $tr.data('stock', total);
        $stok.val(salesFormatNumber(total, SALES_DECIMAL_DIGITS));
    }).fail(function () {
        $stok.val('');
    });
}

/* -------------------------------------------------------------------------
 * HITUNG NILAI BARIS: (Qty x Harga) - Multi Disc%
 * ---------------------------------------------------------------------- */
function pjInlineRecalc($tr) {
    if (!$tr || !$tr.length) return;

    var qty       = convertToDbNumber($tr.find('[data-field="qty"]').val() || '');
    var harga     = convertToDbNumber($tr.find('[data-field="harga"]').val() || '');
    var multidisc = convertToDbNumber($tr.find('[data-field="multidisc"]').val() || '');
    var nilai     = qty * harga * (1 - ((multidisc || 0) / 100));

    pjInlineSetNum($tr.find('[data-field="nilai"]'), nilai);
}


/* -------------------------------------------------------------------------
 * BARIS INPUT ADD: pilih No. SJ atau No. SO, lalu seluruh item ditarik server
 * ---------------------------------------------------------------------- */
function pjInlineAddRow(focus) {
    var $foot = pjInlineFoot();
    if (!$foot.length) return $();

    var $open = pjInlineActiveRow();
    if ($open.length) {
        if (focus) pjInlineFocusSj($open);
        return $open;
    }

    var autoText = '<input type="text" class="form-control form-control-sm" readonly tabindex="-1" placeholder="Otomatis dari SO/SJ">';
    var autoNum  = '<input type="text" class="form-control form-control-sm text-end" readonly tabindex="-1" placeholder="-">';

    var $tr = $('<tr class="pj-inline-row table-warning"></tr>')

        /* 1. kolom checkbox: baris draft belum bisa dicentang */
        .append($('<td class="text-center pj-col-no"></td>')
            .append('<i class="fa fa-plus-circle" style="color:#16a34a"></i>'))
        /* 2. No. SJ */
        .append($('<td></td>').append('<select class="pj-inline-sj" style="width:100%"></select>'))
        /* 3. No. SO */
        .append($('<td></td>').append('<select class="pj-inline-so" style="width:100%"></select>'))
        /* 4. ID Barang */
        .append($('<td></td>').append(autoText))
        /* 5. Nama Barang */
        .append($('<td></td>').append(autoText))
        /* 6. Principal */
        .append($('<td></td>').append(autoText))
        /* 7. Gudang */
        .append($('<td></td>').append(autoText))
        /* 8. Spec */
        .append($('<td></td>').append(autoText))
        /* 9. Satuan */
        .append($('<td class="pj-col-unit-cell"></td>').append(autoText))
        /* 10. Qty */
        .append($('<td></td>').append(autoNum))
        /* 11. Harga */
        .append($('<td></td>').append(autoNum))
        /* 12. Multi Disc */
        .append($('<td></td>').append(autoNum))
        /* 13. Nilai */
        .append($('<td></td>').append(autoNum))
        /* 14. Keterangan */
        .append($('<td></td>').append(autoText))
        /* 15. Stok */
        .append($('<td></td>').append(autoNum))

        /* 16. aksi: simpan + batal */
        .append($('<td class="text-center pj-col-aksi"></td>')
            .append('<button type="button" class="pj-inline-btn pj-inline-save" title="Simpan baris (Enter)"><i class="fa fa-check"></i></button> ')
            .append('<button type="button" class="pj-inline-btn pj-inline-cancel" title="Batal (Esc)"><i class="fa fa-times"></i></button>'));

    $tr.attr('data-mode', 'ADD');
    $tr.data('mode', 'ADD');

    $foot.append($tr);

    pjInlineInitSjSelect($tr);
    pjInlineInitSoSelect($tr);
    pjInlineSyncEmptyState();

    if ($tr[0] && typeof $tr[0].scrollIntoView === 'function') {
        $tr[0].scrollIntoView({ block: 'nearest' });
    }

    if (focus) pjInlineFocusSj($tr);

    return $tr;
}

function pjInlineIsEmpty($tr) {
    if (!$tr || !$tr.length) return true;

    if (($tr.data('mode') || 'ADD') !== 'ADD') return false;

    return $.trim($tr.find('select.pj-inline-sj').val() || '') === ''
        && $.trim($tr.find('select.pj-inline-so').val() || '') === '';
}

/* Pratinjau baris SO/SJ pada baris draft (membantu sebelum simpan) */
function pjInlinePreview($tr) {
    var $cells = $tr.find('td');
    var $sj = $tr.find('select.pj-inline-sj');
    var $so = $tr.find('select.pj-inline-so');

    var sjData = $sj.data('select2') ? $sj.select2('data') : null;
    var soData = $so.data('select2') ? $so.select2('data') : null;

    var label = '';

    if (sjData && sjData[0] && $.trim(sjData[0].id || '') !== '') {
        label = 'SJ: ' + $.trim(sjData[0].id || '');
    } else if (soData && soData[0] && $.trim(soData[0].id || '') !== '') {
        label = 'SO: ' + $.trim(soData[0].id || '');
    }

    // Kolom 4 (ID Barang) s.d. kolom 15 (Stok) menampilkan keterangan sumber
    $cells.slice(3, 15).find('input').each(function () {
        $(this).val(label);
    });
}


/* -------------------------------------------------------------------------
 * BARIS INPUT EDIT: Qty / Harga / Multi Disc / Keterangan / Principal /
 * Gudang / Spec dapat diubah langsung (item & satuan tetap readonly)
 * ---------------------------------------------------------------------- */
function pjInlineAddEditRow(data) {
    var $foot = pjInlineFoot();
    if (!$foot.length || !data) return $();

    var docnosj     = $.trim(data.docnosj || '');
    var docnoso     = $.trim(data.docnoso || '');
    var idbarang    = $.trim(data.idbarang || '');
    var nmbarang    = $.trim(data.nmbarang || '');
    var unit        = $.trim(data.unit || '');
    var idprincipal = $.trim(data.idprincipal || '');
    var idgudang    = $.trim(data.idgudang || '');
    var idspec      = $.trim(data.idspec || '');

    var $tr = $('<tr class="pj-inline-row table-info"></tr>');

    /* 1. penanda baris edit */
    $tr.append($('<td class="text-center pj-col-no"></td>')
        .append('<i class="fa fa-pencil" style="color:#d97706"></i>'));
    /* 2 - 5. sumber & identitas barang (readonly) */
    $tr.append($('<td class="pj-inline-ro"></td>').text(docnosj !== '' ? docnosj : '-'));
    $tr.append($('<td class="pj-inline-ro"></td>').text(docnoso !== '' ? docnoso : '-'));
    $tr.append($('<td class="pj-inline-ro"></td>').text(idbarang));
    $tr.append($('<td class="pj-inline-ro"></td>').attr('title', nmbarang).text(nmbarang));
    /* 6 - 8. principal / gudang / spec */
    $tr.append($('<td></td>').append('<select class="pj-inline-principal" style="width:100%"></select>'));
    $tr.append($('<td></td>').append('<select class="pj-inline-gudang" style="width:100%"></select>'));
    $tr.append($('<td></td>').append('<select class="pj-inline-spec" style="width:100%"></select>'));
    /* 9. satuan (readonly) */
    $tr.append($('<td class="pj-col-unit-cell pj-inline-ro"></td>').text(unit));
    /* 10 - 13. qty / harga / multi disc / nilai */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="qty" placeholder="0" inputmode="decimal" title="' + PJ_NUM_HINT + '">'));
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="harga" placeholder="0" inputmode="decimal" title="' + PJ_NUM_HINT + '">'));
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="multidisc" placeholder="0" inputmode="decimal" title="' + PJ_NUM_HINT + '">'));
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm text-end" data-field="nilai" readonly tabindex="-1" placeholder="0" title="Nilai dihitung otomatis">'));
    /* 14. keterangan */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm" data-field="description" maxlength="100" placeholder="Keterangan" style="text-transform:uppercase">'));
    /* 15. stok gudang (readonly) */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm text-end" data-field="stok" readonly tabindex="-1" placeholder="-">'));
    /* 16. aksi */
    $tr.append($('<td class="text-center pj-col-aksi"></td>')
        .append('<button type="button" class="pj-inline-btn pj-inline-save" title="Simpan baris (Enter)"><i class="fa fa-check"></i></button> ')
        .append('<button type="button" class="pj-inline-btn pj-inline-cancel" title="Batal (Esc)"><i class="fa fa-times"></i></button>'));

    $tr.attr('data-mode', 'EDIT');
    $tr.data('mode', 'EDIT');
    $tr.data('idurut', data.idurut || '');
    $tr.data('uniqueid', data.uniqueid || '');
    $tr.data('idbarang', idbarang);
    $tr.data('docnosj', docnosj);
    $tr.data('docnoso', docnoso);

    $foot.append($tr);

    pjInlineInitPrincipalSelect($tr);
    pjInlineInitGudangSelect($tr);
    pjInlineInitSpecSelect($tr);

    pjInlineSetSelectValue($tr.find('select.pj-inline-principal'), idprincipal, idprincipal);
    pjInlineSetSelectValue($tr.find('select.pj-inline-gudang'), idgudang, idgudang);
    pjInlineSetSelectValue($tr.find('select.pj-inline-spec'), idspec, idspec);

    pjInlineSetNum($tr.find('[data-field="qty"]'), data.qty);
    pjInlineSetNum($tr.find('[data-field="harga"]'), data.harga);
    pjInlineSetNum($tr.find('[data-field="multidisc"]'), data.multidisc);
    pjInlineSetNum($tr.find('[data-field="nilai"]'), data.nilai);
    $tr.find('[data-field="description"]').val($.trim(data.description || ''));

    pjInlineLoadStock($tr);
    pjInlineSyncEmptyState();

    setTimeout(function () {
        $tr.find('[data-field="qty"]').focus().select();
    }, 60);

    return $tr;
}


/* Ambil data baris lalu buka baris edit inline */
function pjInlineOpenEdit(uniqueid) {
    uniqueid = $.trim(String(uniqueid || ''));
    if (uniqueid === '') return;

    var $open = pjInlineActiveRow();

    if ($open.length) {
        if (!pjInlineIsEmpty($open)) {
            Swal.fire({
                icon: 'info',
                title: 'Perhatian',
                text: 'Selesaikan atau batalkan baris input yang sedang aktif terlebih dahulu'
            });
            return;
        }

        pjInlineRemoveRow();
    }

    $.ajax({
        url: HOST_URL + 'sales/postsales/get_penjualan_detail',
        type: 'GET',
        data: { id: uniqueid },
        dataType: 'json',
        success: function (res) {
            if (!res || !res.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: (res && res.message) ? res.message : 'Data tidak ditemukan'
                });
                return;
            }

            pjInlineAddEditRow(res.data);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Data detail tidak dapat dimuat'
            });
        }
    });
}

/* -------------------------------------------------------------------------
 * DATA YANG DIKIRIM SAAT SIMPAN
 * ---------------------------------------------------------------------- */
function pjInlinePayload($tr) {
    var mode      = $tr.data('mode') || 'ADD';
    var qty       = convertToDbNumber($tr.find('[data-field="qty"]').val() || '');
    var harga     = convertToDbNumber($tr.find('[data-field="harga"]').val() || '');
    var multidisc = convertToDbNumber($tr.find('[data-field="multidisc"]').val() || '');
    var nilai     = qty * harga * (1 - ((multidisc || 0) / 100));

    return {
        mode:        mode,
        idurut:      $.trim(String($tr.data('idurut') || '')),
        uniqueid:    $.trim(String($tr.data('uniqueid') || '')),
        idbarang:    $.trim(String($tr.data('idbarang') || '')),
        docnosj:     $.trim($tr.find('select.pj-inline-sj').val() || $tr.data('docnosj') || ''),
        docnoso:     $.trim($tr.find('select.pj-inline-so').val() || $tr.data('docnoso') || ''),
        qty:         qty,
        harga:       harga,
        multidisc:   multidisc,
        nilai:       Math.round(nilai * 10000) / 10000,
        description: $.trim($tr.find('[data-field="description"]').val() || ''),
        idprincipal: $.trim($tr.find('select.pj-inline-principal').val() || ''),
        idgudang:    $.trim($tr.find('select.pj-inline-gudang').val() || ''),
        idspec:      $.trim($tr.find('select.pj-inline-spec').val() || '')
    };
}

/* Data header yang ikut dikirim saat menyimpan detail */
function pjInlineHeaderFormData() {
    var fd = new FormData();

    var customerData = $('#kdcustomer').select2('data');
    var kdcustomer = '';
    var nmcustomer = '';

    if (customerData && customerData.length > 0) {
        kdcustomer = customerData[0].kdcustomer || customerData[0].id || '';
        nmcustomer = customerData[0].nmcustomer || customerData[0].text || '';
    }

    var customerDelivData = $('#kdcustomerdeliv').select2('data');
    var kdcustomerdeliv = '';
    var nmcustomerdeliv = '';

    if (customerDelivData && customerDelivData.length > 0) {
        kdcustomerdeliv = customerDelivData[0].kdcustomer || customerDelivData[0].id || '';
        nmcustomerdeliv = customerDelivData[0].nmcustomer || customerDelivData[0].text || '';
    }

    fd.set('docno', pjInlineDocno());
    fd.set('cabang', $('#cabang').val() || '');
    fd.set('docdate', $('#docdate').val() || '');
    fd.set('jthtempo', convertToDbNumber($('#jthtempo').val()));
    fd.set('kdcustomer', kdcustomer);
    fd.set('nmcustomer', nmcustomer);
    fd.set('kdcustomerdeliv', kdcustomerdeliv);
    fd.set('nmcustomerdeliv', nmcustomerdeliv);
    fd.set('kdsalesman', $('#kdsalesman').val() || '');
    fd.set('isinclusive', $('#isinclusive').is(':checked') ? 'YES' : 'NO');
    fd.set('isopenprice', $('#isopenprice').is(':checked') ? 'YES' : 'NO');
    fd.set('alamatcustomer', $('#alamatcustomer').val() || '');
    fd.set('alamatcustomerdeliv', $('#alamatcustomerdeliv').val() || '');
    fd.set('gradecustomer', $('#gradecustomer').val() || '');
    fd.set('idtax', $('#idtax').val() || '');
    fd.set('currcode', $('#currcode').val() || '');
    fd.set('kurs', convertToDbNumber($('#kurs').val()));
    fd.set('keterangan', $('#keterangan').val() || '');
    fd.set('carabayar', $('#carabayar').val() || '');

    return fd;
}


/* -------------------------------------------------------------------------
 * SIMPAN BARIS INLINE
 * ---------------------------------------------------------------------- */
function pjInlineSave($tr) {
    if (!$tr || !$tr.length) return;

    var p = pjInlinePayload($tr);
    var isEdit = (p.mode === 'EDIT');

    if (isEdit) {
        if (!p.qty || p.qty <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Qty wajib diisi dan harus lebih besar dari 0'
            });
            $tr.find('[data-field="qty"]').focus();
            return;
        }

        if (!p.idgudang) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Gudang wajib dipilih'
            });
            pjInlineFocusGudang($tr);
            return;
        }

        var stock = $tr.data('stock');
        if (stock !== null && stock !== undefined && p.qty > (parseFloat(stock) || 0)) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok tidak cukup',
                text: 'Qty ' + salesFormatNumber(p.qty, SALES_DECIMAL_DIGITS) + ' melebihi stok gudang (' + salesFormatNumber(parseFloat(stock) || 0, SALES_DECIMAL_DIGITS) + ').'
            });
            return;
        }
    } else {
        // Server memakai aturan XOR: pilih salah satu SO atau SJ
        if ((p.docnosj !== '' && p.docnoso !== '') || (p.docnosj === '' && p.docnoso === '')) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih salah satu: No. SO atau No. SJ (tidak boleh keduanya / kosong keduanya)'
            });
            pjInlineFocusSj($tr);
            return;
        }

        if (pjInlineCustomerCode() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Customer harus dipilih terlebih dahulu'
            });
            return;
        }
    }

    var docno = pjInlineDocno();
    if (docno === '' || $.trim($('#suffix').val() || '') === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'No. Jurnal belum terbentuk. Pilih Cabang / Job terlebih dahulu.'
        });
        return;
    }

    var formData = new FormData();
    pjInlineHeaderFormData().forEach(function (value, key) {
        formData.set(key, value);
    });

    formData.set('docno', docno);
    formData.set('idurut', p.idurut);
    formData.set('uniqueid', p.uniqueid);
    formData.set('docnoso', p.docnoso);
    formData.set('docnosj', p.docnosj);
    formData.set('idbarang', p.idbarang);
    formData.set('qty', p.qty);
    formData.set('harga', p.harga);
    formData.set('multidisc', p.multidisc);
    formData.set('nilai', p.nilai);
    formData.set('description', p.description);
    formData.set('idprincipal', p.idprincipal);
    formData.set('idgudang', p.idgudang);
    formData.set('idspec', p.idspec);

    var $btn = $tr.find('.pj-inline-save').prop('disabled', true);

    $.ajax({
        url: HOST_URL + 'sales/postsales/savePenjualanDetail',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,

        success: function (res) {
            if (!res || !res.success) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal',
                    text: (res && res.message) ? res.message : 'Data gagal disimpan'
                });
                return;
            }

            // Header baru dibuat → muat ulang halaman (pola grid Sales Order)
            if (res.reload === true) {
                window.location.reload();
                return;
            }

            var isInfo = res.message && res.message.toLowerCase().indexOf('sudah ada') !== -1;

            Swal.fire({
                icon: isInfo ? 'info' : 'success',
                title: isInfo ? 'Tidak Ada Perubahan' : 'Berhasil',
                text: res.message || 'Data berhasil disimpan',
                timer: 1800,
                showConfirmButton: false
            });

            pjInlineRemoveRow();

            if (typeof reload_table_penjualan_dtl === 'function') {
                reload_table_penjualan_dtl();
            }

            if (typeof documentReadable === 'function') {
                documentReadable();
            }

            // siapkan baris kosong berikutnya untuk input cepat (khusus mode ADD)
            if (!isEdit) {
                pjInlineAddRow(false);
            }
        },

        error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Terjadi kesalahan pada server (500)'
            });
        },

        complete: function () {
            $btn.prop('disabled', false);
        }
    });
}


/* -------------------------------------------------------------------------
 * TOMBOL TOOLBAR DETAIL (menggantikan versi modal pada penjualan.js)
 * sales_shortcut.js memanggil btnInputDetail / btnUpdateDetail / btnDeleteDetail
 * ---------------------------------------------------------------------- */
function btnInputDetail() {
    var cabang = $.trim($('#cabang').val() || '');

    if (cabang === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Cabang harus dipilih terlebih dahulu'
        });

        if ($('#cabang').data('select2')) {
            $('#cabang').select2('open');
        }
        return;
    }

    if (pjInlineCustomerCode() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Customer harus dipilih terlebih dahulu'
        });

        if ($('#kdcustomer').data('select2')) {
            $('#kdcustomer').select2('open');
        }
        return;
    }

    if (pjInlineActiveRow().length) {
        pjInlineFocusSj(pjInlineActiveRow());
        return;
    }

    pjInlineAddRow(true);
}

function btnUpdateDetail() {
    var ids = (typeof getSelectedPenjualanDetail === 'function')
        ? getSelectedPenjualanDetail()
        : [];

    if (ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih satu data yang akan diupdate'
        });
        return;
    }

    if (ids.length > 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Update hanya boleh satu data'
        });
        return;
    }

    pjInlineOpenEdit(ids[0]);
}

/* -------------------------------------------------------------------------
 * DATATABLE DETAIL (kolom Aksi + empty state + padding baris)
 * ---------------------------------------------------------------------- */
function pjInitDetailTable() {
    var table = $('#tabpenjualandtl');
    if (!table.length) return;

    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().destroy();
    }

    table.DataTable({
        "processing": true,
        "serverSide": true,
        "language": $.extend({}, (typeof languageDatatable === 'function' ? languageDatatable() : {}), {
            "emptyTable": '<i class="fa fa-inbox"></i> Belum ada data detail. ' +
                'Klik tombol <b>+</b> (atau tekan <b>Alt+N</b>) untuk menambah barang.',
            "zeroRecords": '<i class="fa fa-filter"></i> Tidak ada baris yang cocok dengan pencarian.'
        }),
        "paging": false,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": false,
        "bFilter": true,
        "iDisplayLength": -1,
        "ajax": {
            "url": HOST_URL + 'sales/postsales/list_tmp_penjualan_dtl',
            "type": "POST",
            "dataFilter": function (data) {
                var json = jQuery.parseJSON(data);
                json.draw = json.dataTables.draw;
                json.recordsTotal = json.dataTables.recordsTotal;
                json.recordsFiltered = json.dataTables.recordsFiltered;
                json.data = json.dataTables.data;

                // Kolom Aksi tidak dikirim server: rapikan panjang baris
                // agar DataTables tidak memunculkan warning.
                if ($.isArray(json.data)) {
                    $.each(json.data, function (i, row) {
                        if (!$.isArray(row)) return;
                        while (row.length < 16) row.push('');
                    });
                }

                return JSON.stringify(json);
            }
        },
        "columnDefs": [
            {
                "targets": 0,
                "orderable": false,
                "searchable": false,
                "className": "text-center",
                "render": function (data, type, row) {
                    return '<input type="checkbox" class="row-check" value="' + row[0] + '">';
                }
            },
            {
                // Kolom Satuan: lebar minimum dijaga
                "targets": 8,
                "className": "pj-col-unit-cell"
            },
            {
                // Kolom Aksi: tombol update / hapus baris
                "targets": -1,
                "orderable": false,
                "searchable": false,
                "className": "text-center pj-col-aksi-cell",
                "render": function (data, type, row) {
                    if (type !== 'display') return '';

                    var id = (row && row[0] !== undefined && row[0] !== null) ? String(row[0]) : '';

                    if ($.trim(id) === '') return '';

                    return '<button type="button" class="pj-row-btn pj-row-btn-edit" data-id="' + id + '" title="Update baris">' +
                        '<i class="fa fa-pencil"></i></button> ' +
                        '<button type="button" class="pj-row-btn pj-row-btn-delete" data-id="' + id + '" title="Hapus baris">' +
                        '<i class="fa fa-trash"></i></button>';
                }
            }
        ]
    });
}


/* -------------------------------------------------------------------------
 * EVENT BINDING
 * ---------------------------------------------------------------------- */
$(document).on('click', '#btnAddDetail', function (e) {
    e.preventDefault();
    btnInputDetail();
});

$(document).on('click', '#btnUpdateDetail', function (e) {
    e.preventDefault();
    btnUpdateDetail();
});

$(document).on('click', '#btnDeleteDetail', function (e) {
    e.preventDefault();

    if (typeof window.btnDeleteDetail === 'function') {
        window.btnDeleteDetail();
    }
});

// Tombol aksi per baris (update / hapus)
$(document).on('click', '#tabpenjualandtl tbody .pj-row-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    var $btn = $(this);
    var $cb  = $btn.closest('tr').find('.row-check');

    if (!$cb.length) return;

    // Fokuskan pilihan pada baris ini saja
    $('#tabpenjualandtl tbody .row-check').not($cb).prop('checked', false);
    $cb.prop('checked', true).trigger('change');

    if ($btn.hasClass('pj-row-btn-edit')) {
        btnUpdateDetail();
    } else if (typeof window.btnDeleteDetail === 'function') {
        window.btnDeleteDetail();
    }
});

// Klik pada pesan empty state detail = buka baris input inline
$(document).on('click', '#tabpenjualandtl td.dataTables_empty', function () {
    btnInputDetail();
});

// Nilai dihitung ulang saat qty / harga / multi disc berubah
$(document).on('input change', '#pjInlineFoot [data-field="qty"], #pjInlineFoot [data-field="harga"], #pjInlineFoot [data-field="multidisc"]', function () {
    pjInlineRecalc($(this).closest('tr'));
});

// Pratinjau sumber data (SO / SJ) pada baris draft
$(document).on('select2:select', '#pjInlineFoot select.pj-inline-sj, #pjInlineFoot select.pj-inline-so', function () {
    pjInlinePreview($(this).closest('tr'));
});

$(document).on('click', '.pj-inline-save', function (e) {
    e.preventDefault();
    pjInlineSave($(this).closest('tr'));
});

$(document).on('click', '.pj-inline-cancel', function (e) {
    e.preventDefault();
    pjInlineRemoveRow();
});

// Enter = simpan baris, Esc = batalkan baris
$(document).on('keydown', '#pjInlineFoot input, #pjInlineFoot select', function (e) {
    if (e.key === 'Enter') {
        // Dropdown (select2) menangani Enter sendiri untuk memilih opsi
        if ($(this).is('select')) return;

        e.preventDefault();
        pjInlineSave($(this).closest('tr'));
    } else if (e.key === 'Escape') {
        e.preventDefault();
        pjInlineRemoveRow();
    }
});

/* Inisialisasi tabel detail setelah penjualan.js selesai membuat DataTable */
$(function () {
    pjInitDetailTable();
});


/* =========================================================================
 * TARIK DO -> PENJUALAN (modal Tarik DO)
 * -------------------------------------------------------------------------
 * Tampilan & interaksi mengikuti halaman addDeliveryOrder
 * (v_add_deliveryorder_inline: modal "Tarik SO") — parent-child DO header
 * dan detail DO, pencarian, centang sebagian, lalu tarik ke inline detail.
 * Endpoint: sales/postsales/pjListDO | pjListDODetail | pjPullDO
 * ========================================================================= */

var PJ_DO_URL = {
    list:   HOST_URL + 'sales/postsales/pjListDO',
    detail: HOST_URL + 'sales/postsales/pjListDODetail',
    pull:   HOST_URL + 'sales/postsales/pjPullDO'
};

var PJ_DO_CACHE = {};

function pjDoTxt(v) {
    return $('<div>').text(v === undefined || v === null ? '' : String(v)).html();
}

function pjDoNum(v) {
    if (v === null || v === undefined || v === '') {
        return 0;
    }
    var n = parseFloat(String(v).replace(/,/g, ''));
    return isFinite(n) ? n : 0;
}

function pjDoFmt(v, dec) {
    return salesFormatNumber(pjDoNum(v), (dec === undefined ? 2 : dec));
}

function pjDoDate(v) {
    var s = String(v || '').substring(0, 10);
    var p = s.split('-');
    return (p.length === 3) ? (p[2] + '-' + p[1] + '-' + p[0]) : s;
}

function pjDoRow(no) {
    return {
        parent: $('#tblDOList tr.do-pick-parent[data-docnodo="' + no + '"]'),
        child:  $('#tblDOList tr.do-pick-child[data-docnodo="' + no + '"]')
    };
}

/* Buka / tutup modal — mendukung Bootstrap 5 native & jQuery plugin (BS4) */
function pjDoModalShow() {
    var el = document.getElementById('modalTarikDO');
    if (!el) { return; }

    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        window.bootstrap.Modal.getOrCreateInstance(el).show();
        return;
    }

    if ($.fn && typeof $.fn.modal === 'function') {
        $(el).modal('show');
    }
}

function pjDoModalHide() {
    var el = document.getElementById('modalTarikDO');
    if (!el) { return; }

    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        window.bootstrap.Modal.getOrCreateInstance(el).hide();
        return;
    }

    if ($.fn && typeof $.fn.modal === 'function') {
        $(el).modal('hide');
    }
}

/* Daftar DO (header) untuk customer terpilih */
function pjDoLoadList() {
    var $tb = $('#tblDOList tbody');

    PJ_DO_CACHE = {};
    $('#doChkAll').prop('checked', false).prop('indeterminate', false);
    $tb.html('<tr><td colspan="7" class="text-center text-muted py-2">Memuat data...</td></tr>');

    $.ajax({
        url: PJ_DO_URL.list,
        type: 'POST',
        dataType: 'json',
        data: {
            kdcustomer: pjInlineCustomerCode(),
            search: $.trim($('#doSearch').val() || '')
        },
        success: function (res) {
            var rows = (res && res.rows) ? res.rows : [];

            if (!rows.length) {
                $tb.html('<tr><td colspan="7" class="text-center text-muted py-2">Tidak ada DO yang bisa ditarik untuk customer ini.</td></tr>');
                pjDoUpdateCount();
                return;
            }

            var h = '';

            $.each(rows, function (i, r) {
                var no = $.trim(r.docno);

                h += '<tr class="do-pick-parent" data-docnodo="' + pjDoTxt(no) + '">'
                    + '<td class="do-pick-toggle"><i class="fa fa-caret-right"></i></td>'
                    + '<td class="text-center"><input type="checkbox" class="do-pick-head" value="' + pjDoTxt(no) + '" title="Centang semua detail DO ini"></td>'
                    + '<td class="do-pick-col-no">' + pjDoTxt(no) + '</td>'
                    + '<td>' + pjDoTxt(pjDoDate(r.docdate)) + '</td>'
                    + '<td class="do-pick-col-cust">' + pjDoTxt($.trim(r.nmcustomer || '')) + '</td>'
                    + '<td class="text-end">' + pjDoFmt(r.total, 2) + '</td>'
                    + '<td>' + pjDoTxt($.trim(r.status || '')) + '</td>'
                    + '</tr>'
                    + '<tr class="do-pick-child" data-docnodo="' + pjDoTxt(no) + '" style="display:none;">'
                    + '<td colspan="7" class="do-pick-child-cell"></td>'
                    + '</tr>';
            });

            $tb.html(h);
            pjDoUpdateCount();
        },
        error: function () {
            $tb.html('<tr><td colspan="7" class="text-center text-danger py-2">Gagal memuat daftar DO.</td></tr>');
        }
    });
}


/* Detail DO (child row) untuk modal Tarik DO */
function pjDoRenderChild(no, cb) {
    var cache = PJ_DO_CACHE[no] || (PJ_DO_CACHE[no] = { loaded: false, rows: [] });
    var $cell = $('#tblDOList tr.do-pick-child[data-docnodo="' + no + '"] td.do-pick-child-cell');

    if (!$cell.length) {
        if (cb) { cb(); }
        return;
    }

    if (!cache.loaded) {
        $cell.html('<div class="text-muted do-pick-loading">Memuat detail...</div>');

        $.ajax({
            url: PJ_DO_URL.detail,
            type: 'POST',
            dataType: 'json',
            data: {
                docnodo: no,
                docno: pjInlineDocno()
            },
            success: function (res) {
                cache.loaded = true;
                cache.rows = (res && res.rows) ? res.rows : [];
                pjDoRenderChild(no, cb);
            },
            error: function () {
                cache.loaded = true;
                cache.rows = [];
                pjDoRenderChild(no, cb);
            }
        });

        return;
    }

    if (!cache.rows.length) {
        $cell.html('<div class="text-muted do-pick-loading">Tidak ada detail pada DO ini.</div>');
        if (cb) { cb(); }
        return;
    }

    var h = '<table class="table table-sm do-pick-dtl mb-0"><thead><tr>'
        + '<th width="22"><input type="checkbox" class="do-pick-dtl-all" data-docnodo="' + pjDoTxt(no) + '" title="Centang semua detail DO ini"></th>'
        + '<th>Item</th>'
        + '<th width="96">Spec</th>'
        + '<th width="44">Sat</th>'
        + '<th width="66" class="text-end">Qty DO</th>'
        + '<th width="66" class="text-end">Qty SJ</th>'
        + '<th width="66" class="text-end">Sisa</th>'
        + '<th width="94" class="text-end">Harga</th>'
        + '</tr></thead><tbody>';

    $.each(cache.rows, function (i, d) {
        var uid   = $.trim(d.uniqueid || '');
        var sisa  = pjDoNum(d.qtysisa);
        var sudah = pjDoNum(d.sudah) === 1;
        var off   = (sudah || sisa <= 0);
        var ket   = sudah ? 'Sudah ditarik ke dokumen ini' : (sisa <= 0 ? 'Sudah full SJ / tidak ada sisa' : '');

        h += '<tr class="do-pick-dtl-row' + (off ? ' do-pick-dtl-done' : '') + '">'
            + '<td class="text-center">'
            + '<input type="checkbox" class="do-pick-dtl-chk" data-docnodo="' + pjDoTxt(no) + '" value="' + pjDoTxt(uid) + '"'
            + (off ? ' disabled title="' + pjDoTxt(ket) + '"' : '') + '>'
            + '</td>'
            + '<td><span class="do-pick-item">' + pjDoTxt($.trim(d.idbarang || '')) + '</span> ' + pjDoTxt($.trim(d.nmbarang || '')) + '</td>'
            + '<td>' + pjDoTxt($.trim(d.idspec || '')) + '</td>'
            + '<td>' + pjDoTxt($.trim(d.unit || '')) + '</td>'
            + '<td class="text-end">' + pjDoFmt(d.qty, 2) + '</td>'
            + '<td class="text-end">' + pjDoFmt(d.qtysj, 2) + '</td>'
            + '<td class="text-end' + (sisa <= 0 ? ' text-danger' : '') + '">' + pjDoFmt(sisa, 2) + '</td>'
            + '<td class="text-end">' + pjDoFmt(d.harga, 2) + '</td>'
            + '</tr>';
    });

    h += '</tbody></table>';

    $cell.html(h);
    pjDoSyncHead(no);

    if (cb) { cb(); }
}

function pjDoOpenChild(no, cb) {
    var r = pjDoRow(no);
    if (!r.child.length) {
        if (cb) { cb(); }
        return;
    }

    r.child.show();
    r.parent.addClass('do-open').find('.do-pick-toggle i').removeClass('fa-caret-right').addClass('fa-caret-down');
    pjDoRenderChild(no, cb);
}

function pjDoCloseChild(no) {
    var r = pjDoRow(no);
    if (!r.child.length) { return; }

    r.child.hide();
    r.parent.removeClass('do-open').find('.do-pick-toggle i').removeClass('fa-caret-down').addClass('fa-caret-right');
}

function pjDoToggleChild(no) {
    var r = pjDoRow(no);
    if (!r.child.length) { return; }

    if (r.child.is(':visible')) {
        pjDoCloseChild(no);
    } else {
        pjDoOpenChild(no);
    }
}


function pjDoSetAll(no, on) {
    $('#tblDOList tr.do-pick-child[data-docnodo="' + no + '"] input.do-pick-dtl-chk:enabled').prop('checked', on);
    pjDoSyncAllHeads();
}

function pjDoSyncAllHeads() {
    $('#tblDOList tr.do-pick-parent').each(function () {
        pjDoSyncHead(String($(this).attr('data-docnodo') || ''));
    });
}

function pjDoSyncHead(no) {
    var $chk   = $('#tblDOList tr.do-pick-child[data-docnodo="' + no + '"] input.do-pick-dtl-chk:enabled');
    var total  = $chk.length;
    var picked = $chk.filter(':checked').length;

    $('#tblDOList input.do-pick-head[value="' + no + '"]')
        .prop('checked', total > 0 && picked === total)
        .prop('indeterminate', picked > 0 && picked < total);

    $('#tblDOList input.do-pick-dtl-all[data-docnodo="' + no + '"]')
        .prop('checked', total > 0 && picked === total)
        .prop('indeterminate', picked > 0 && picked < total);

    pjDoSyncAll();
}

function pjDoSyncAll() {
    var $chk   = $('#tblDOList input.do-pick-dtl-chk:enabled');
    var total  = $chk.length;
    var picked = $chk.filter(':checked').length;

    $('#doChkAll')
        .prop('checked', total > 0 && picked === total)
        .prop('indeterminate', picked > 0 && picked < total);

    pjDoUpdateCount();
}

function pjDoUpdateCount() {
    $('#doPickCount').text($('#tblDOList input.do-pick-dtl-chk:checked').length);
}

/* Tarik detail DO terpilih ke dokumen penjualan (per DO) */
function pjDoPull() {
    var byDo  = {};
    var total = 0;

    $('#tblDOList input.do-pick-dtl-chk:checked').each(function () {
        var no  = String($(this).attr('data-docnodo') || '');
        var uid = $.trim(String($(this).val() || ''));

        if (!no || !uid) { return; }

        if (!byDo[no]) { byDo[no] = []; }
        byDo[no].push(uid);
        total++;
    });

    if (!total) {
        Swal.fire('Perhatian', 'Centang minimal satu detail DO yang akan ditarik.', 'warning');
        return;
    }

    var docno = pjInlineDocno();

    if (docno === '' || $.trim($('#suffix').val() || '') === '') {
        Swal.fire('Perhatian', 'No. Jurnal belum terbentuk. Pilih Cabang / Job terlebih dahulu.', 'warning');
        return;
    }

    if (pjInlineCustomerCode() === '') {
        Swal.fire('Perhatian', 'Customer wajib dipilih.', 'warning');
        return;
    }

    var list = Object.keys(byDo);
    var $btn = $('#btnPullDO').prop('disabled', true);
    var idx  = 0;
    var inserted = 0;
    var needReload = false;

    function step() {
        if (idx >= list.length) {
            $btn.prop('disabled', false);
            pjDoModalHide();

            if (needReload) {
                window.location.reload();
                return;
            }

            reload_table_penjualan_dtl();

            if (typeof documentReadable === 'function') {
                documentReadable();
            }

            Swal.fire(
                'Berhasil',
                inserted > 0
                    ? inserted + ' detail DO berhasil ditarik ke Penjualan.'
                    : 'Tidak ada detail baru yang ditarik (detail sudah ada di dokumen ini).',
                'success'
            );

            return;
        }

        var no = list[idx++];

        var fd = new FormData();
        pjInlineHeaderFormData().forEach(function (value, key) {
            fd.set(key, value);
        });

        fd.set('docno', docno);
        fd.set('docnodo', no);

        $.each(byDo[no], function (i, uid) {
            fd.append('ids[]', uid);
        });

        $.ajax({
            url: PJ_DO_URL.pull,
            type: 'POST',
            data: fd,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (res) {
                if (!res || !res.success) {
                    $btn.prop('disabled', false);
                    if (inserted > 0) { reload_table_penjualan_dtl(); }
                    Swal.fire('Gagal', (res && res.message) || 'DO gagal ditarik', 'error');
                    return;
                }

                inserted += pjDoNum(res.inserted);

                if (res.reload === true) {
                    needReload = true;
                }

                step();
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                if (inserted > 0) { reload_table_penjualan_dtl(); }
                Swal.fire('Error', 'Gagal menghubungi server (' + xhr.status + ')', 'error');
            }
        });
    }

    step();
}


/* =========================================================================
 * EVENT MODAL TARIK DO
 * ========================================================================= */

$(document).on('click', '#btnTarikDO', function (e) {
    e.preventDefault();

    if ($.trim($('#cabang').val() || '') === '') {
        Swal.fire('Perhatian', 'Cabang harus dipilih terlebih dahulu', 'warning');
        return;
    }

    if (pjInlineCustomerCode() === '') {
        Swal.fire('Perhatian', 'Pilih customer terlebih dahulu.', 'warning');
        return;
    }

    if (pjInlineDocno() === '' || $.trim($('#suffix').val() || '') === '') {
        Swal.fire('Perhatian', 'Lengkapi No. Jurnal terlebih dahulu.', 'warning');
        return;
    }

    $('#doSearch').val('');
    pjDoModalShow();
    $('.do-pick-wrap').scrollTop(0);
    pjDoLoadList();
});

$(document).on('click', '#btnSearchDO', function (e) {
    e.preventDefault();
    pjDoLoadList();
});

$(document).on('keydown', '#doSearch', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        pjDoLoadList();
    }
});

/* Klik baris DO = expand / collapse detail */
$(document).on('click', '#tblDOList tr.do-pick-parent', function (e) {
    if ($(e.target).is('input[type="checkbox"]')) { return; }
    pjDoToggleChild(String($(this).attr('data-docnodo') || ''));
});

/* Checkbox di baris DO = centang semua detail DO tersebut */
$(document).on('change', '#tblDOList input.do-pick-head', function () {
    var no = String($(this).val() || '');

    if (!$(this).is(':checked')) {
        pjDoSetAll(no, false);
        return;
    }

    pjDoOpenChild(no, function () {
        pjDoSetAll(no, true);
    });
});

/* Checkbox "semua" di header tabel detail */
$(document).on('change', '#tblDOList input.do-pick-dtl-all', function () {
    pjDoSetAll(String($(this).attr('data-docnodo') || ''), $(this).is(':checked'));
});

/* Checkbox per detail */
$(document).on('change', '#tblDOList input.do-pick-dtl-chk', function () {
    var no = String($(this).attr('data-docnodo') || '');

    if ($(this).is(':checked')) {
        pjDoSyncAllHeads();
    } else {
        pjDoSyncHead(no);
    }
});

/* Centang semua detail yang sudah dibuka */
$(document).on('change', '#doChkAll', function () {
    var on = $(this).is(':checked');

    $('#tblDOList tr.do-pick-parent').each(function () {
        var no = String($(this).attr('data-docnodo') || '');

        if (PJ_DO_CACHE[no] && PJ_DO_CACHE[no].loaded) {
            pjDoSetAll(no, on);
        }
    });

    pjDoSyncAll();
});

$(document).on('click', '#btnCollapseDO', function (e) {
    e.preventDefault();

    $('#tblDOList tr.do-pick-parent').each(function () {
        pjDoCloseChild(String($(this).attr('data-docnodo') || ''));
    });
});

$(document).on('click', '#btnPullDO', function (e) {
    e.preventDefault();
    pjDoPull();
});

/* Tombol Batal / silang pada modal (BS5 native & fallback jQuery) */
$(document).on('click', '#modalTarikDO [data-bs-dismiss="modal"]', function () {
    pjDoModalHide();
});

