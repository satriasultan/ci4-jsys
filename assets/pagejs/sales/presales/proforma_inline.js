/* =========================================================================
 * PROFORMA INVOICE - DETAIL INLINE, TARIK DATA PENJUALAN, SIMPAN DRAFT
 * -------------------------------------------------------------------------
 * Hanya dipakai halaman sales/presales/addProforma.
 * Pola grid inline mengikuti sales/postsales/addDeliveryOrder.
 * ========================================================================= */

var PF_URL = {
    saveRow:   HOST_URL + 'sales/presales/proformaSaveRow',
    delRow:    HOST_URL + 'sales/presales/deleteProformaDtl',
    refresh:   HOST_URL + 'sales/presales/proformaRefreshTotals',
    listPjo:   HOST_URL + 'sales/presales/proformaListPjo',
    pjoDetail: HOST_URL + 'sales/presales/proformaListPjoDetail',
    pullPjo:   HOST_URL + 'sales/presales/proformaPullPjoSelection',
    draft:     HOST_URL + 'sales/presales/proformaSaveDraft',
    final:     HOST_URL + 'sales/presales/finalEntryProforma',
    listItem:  HOST_URL + 'api/globalmodule/list_item',
    listUnit:  HOST_URL + 'api/globalmodule/list_unit_item'
};

var PF_MODE = 'single';
var PF_PJO_CACHE = {};

/* ------------------------------- helpers ------------------------------- */

function pfDocno() {
    return $.trim(String($('#docno').val() || ''));
}

/* Format angka aplikasi (jaccformat): ribuan "," dan desimal "." -> 1,500.7500 */
function pfNum(value) {
    if (value === null || value === undefined || value === '') {
        return 0;
    }

    var s = String(value).replace(/\s/g, '');

    if (s === '') {
        return 0;
    }

    var lastComma = s.lastIndexOf(',');
    var lastDot   = s.lastIndexOf('.');

    if (lastComma > -1 && lastDot > -1 && lastComma > lastDot) {
        // Format Indonesia: 1.500,75
        s = s.replace(/\./g, '').replace(/,/g, '.');
    } else {
        // Format aplikasi: 1,500.75 (koma = pemisah ribuan)
        s = s.replace(/,/g, '');
    }

    var n = parseFloat(s.replace(/[^0-9\.\-]/g, ''));
    return isFinite(n) ? n : 0;
}

function pfFmt(value, dec) {
    var n = pfNum(value);
    var d = (dec === undefined ? 4 : dec);

    if (typeof jaccformat === 'function') {
        return jaccformat(n, { maxDecimals: d });
    }

    return n.toLocaleString('en-US', {
        minimumFractionDigits: d,
        maximumFractionDigits: d
    });
}

function pfSet(selector, value) {
    var el = $(selector)[0];
    if (!el) {
        return;
    }

    el.value = pfFmt(value, 4);

    if (typeof jaccseparator === 'function') {
        jaccseparator(el, { maxDecimals: 4 });
    }
}

function pfApplyTotals(totals) {
    if (!totals) {
        return;
    }

    pfSet('#grosssales', totals.grosssales);
    pfSet('#netsales', totals.netsales);
    pfSet('#taxbasis', totals.taxbasis);
    pfSet('#vat', totals.vat);
    pfSet('#pph22', totals.pph22);
    pfSet('#ttlprice', totals.ttlprice);
}

function pfFoot() {
    return $('#proformaInlineFoot');
}

function pfActiveRow() {
    return pfFoot().find('tr.pf-inline-row').first();
}

function pfIsEdit($tr) {
    return !!($tr && $tr.length && String($tr.data('mode') || '') === 'EDIT');
}

function pfSyncEmptyState() {
    var $table = $('#t_proforma_dtl');
    if (!$table.length) {
        return;
    }

    $table.toggleClass('pf-has-draft', pfActiveRow().length > 0);
}

function pfDestroySelects($tr) {
    $tr.find('select').each(function () {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
}

function pfRemoveRow() {
    var $tr = pfActiveRow();
    if (!$tr.length) {
        return;
    }

    pfDestroySelects($tr);
    $tr.remove();
    pfSyncEmptyState();
}

function pfRecalc($tr) {
    if (!$tr || !$tr.length) {
        return;
    }

    var qty   = pfNum($tr.find('[data-field="qty"]').val());
    var price = pfNum($tr.find('[data-field="price"]').val());

    $tr.find('[data-field="amount"]').val(pfFmt(qty * price, 4));
}

/* ---------------------------- tabel detail ----------------------------- */

function pfInitTable() {
    var $table = $('#t_proforma_dtl');
    if (!$table.length) {
        return;
    }

    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().destroy();
    }

    $table.DataTable({
        "processing": true,
        "serverSide": true,
        "language": $.extend({}, (typeof languageDatatable === 'function' ? languageDatatable() : {}), {
            "emptyTable": '<i class="fa fa-inbox"></i> Belum ada detail. Klik tombol <b>+</b> (atau tekan <b>Alt+N</b>) untuk menambah item.',
            "zeroRecords": '<i class="fa fa-filter"></i> Tidak ada baris yang cocok.'
        }),
        "paging": false,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": false,
        "bFilter": false,
        "iDisplayLength": -1,
        "ajax": {
            "url": HOST_URL + 'sales/presales/list_t_proforma_dtl',
            "type": "POST",
            "data": function (data) {
                data.docno = pfDocno();
            },
            "dataFilter": function (data) {
                var json = jQuery.parseJSON(data);
                json.draw = json.dataTables.draw;
                json.recordsTotal = json.dataTables.recordsTotal;
                json.recordsFiltered = json.dataTables.recordsFiltered;
                json.data = json.dataTables.data;

                // Kolom Aksi tidak dikirim server: rapikan panjang baris (8 kolom)
                if ($.isArray(json.data)) {
                    $.each(json.data, function (i, row) {
                        if (!$.isArray(row)) return;
                        while (row.length < 8) row.push('');
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
                    return '<input type="checkbox" class="pf-row-check" value="' + row[0] + '">';
                }
            },
            {
                "targets": 3,
                "className": "pf-col-unit-cell"
            },
            {
                "targets": -1,
                "orderable": false,
                "searchable": false,
                "className": "text-center pf-col-aksi-cell",
                "render": function (data, type, row) {
                    if (type !== 'display') return '';

                    var id = (row && row[0] !== undefined && row[0] !== null) ? String(row[0]) : '';
                    if ($.trim(id) === '') return '';

                    return '<button type="button" class="pf-row-btn pf-row-btn-edit" data-id="' + id + '" title="Update baris">' +
                        '<i class="fa fa-pencil"></i></button> ' +
                        '<button type="button" class="pf-row-btn pf-row-btn-delete" data-id="' + id + '" title="Hapus baris">' +
                        '<i class="fa fa-trash"></i></button>';
                }
            }
        ]
    });

    pfSyncEmptyState();
}

function pfReloadTable() {
    var $table = $('#t_proforma_dtl');
    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().ajax.reload(null, false);
    }
}


/* --------------------- baris input inline (ADD/EDIT) -------------------- */

/* Daftar No. Penjualan (FINAL + jurnal SALESX POSTED) untuk customer terpilih.
   Dipakai baris detail inline: kode yang diketik boleh kode item ATAU No. Penjualan. */
var PF_DOC_LIST = [];

function pfLoadPenjualanDocs() {
    $.ajax({
        url: PF_URL.listPjo,
        type: 'POST',
        dataType: 'json',
        data: {
            cust: $.trim(String($('#cust').val() || '')),
            search: ''
        },
        success: function (res) {
            PF_DOC_LIST = (res && res.rows) ? res.rows : [];
        },
        error: function () {
            PF_DOC_LIST = [];
        }
    });
}

function pfMatchPenjualanDocs(term) {
    var q = $.trim(String(term || '')).toUpperCase();
    if (q === '') {
        return [];
    }

    return $.grep(PF_DOC_LIST, function (r) {
        return $.trim(String(r.docno || '')).toUpperCase().indexOf(q) > -1;
    }).slice(0, 10);
}

function pfInitItemSelect($tr) {
    var $item = $tr.find('select.pf-inline-item');
    if (!$item.length) {
        return;
    }

    $item.select2({
        placeholder: 'Cari barang / No. Penjualan',
        allowClear: true,
        minimumInputLength: 2,
        width: '100%',
        dropdownParent: $(document.body),
        ajax: {
            url: PF_URL.listItem,
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

                var term = $.trim(String(params.term || ''));
                var docs = pfMatchPenjualanDocs(term);
                var out  = [];

                // No. Penjualan ditampilkan lebih dulu: sekali pilih, seluruh detail ditarik
                $.each(docs, function (i, r) {
                    var no   = $.trim(String(r.docno || ''));
                    var cust = $.trim(String(r.nmcustomer || r.kdcustomer || ''));
                    if (no === '') return;

                    out.push({
                        id: 'PF-DOC:' + no,
                        text: no + (cust !== '' ? ' - ' + cust : '') + '  [Tarik dokumen]',
                        pf_doc: no
                    });
                });

                $.each((data && data.items) || [], function (i, it) {
                    out.push(it);
                });

                return {
                    results: out,
                    pagination: { more: (params.page * 30) < ((data && data.total_count) || 0) }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) { return markup; }
    }).on('select2:select', function (e) {
        var d = e.params.data || {};

        // Pilih No. Penjualan -> tarik seluruh detail dokumen tersebut
        if (d.pf_doc) {
            $(this).val(null).trigger('change');
            pfPullDocToRow(d.pf_doc);
            return;
        }

        $tr.find('[data-field="nmbarang"]').val($.trim(d.nmbarang || d.text || ''));
        $tr.data('idbarang', $.trim(d.idbarang || d.id || ''));

        pfLoadUnits($tr, $.trim(d.idbarang || d.id || ''), $.trim(d.unit || ''));
    }).on('select2:clear', function () {
        $tr.find('[data-field="nmbarang"]').val('');
        $tr.data('idbarang', '');
        pfLoadUnits($tr, '', '');
    });
}

/* Tarik seluruh detail satu No. Penjualan (dipanggil dari baris detail inline) */
function pfPullDocToRow(no) {
    no = $.trim(String(no || ''));
    if (no === '') {
        return;
    }

    var docno = pfDocno();
    if (docno === '') {
        Swal.fire('Perhatian', 'Klik Proses terlebih dahulu untuk membentuk No. Jurnal proforma.', 'warning');
        return;
    }

    if ($.trim(String($('#cust').val() || '')) === '') {
        Swal.fire('Perhatian', 'Pilih Customer terlebih dahulu sebelum menarik dokumen Penjualan.', 'warning');
        return;
    }

    var doPull = function () {
        Swal.fire({
            title: 'Menarik...',
            text: 'Detail Penjualan ' + no + ' sedang ditarik ke proforma.',
            allowOutsideClick: false,
            didOpen: function () {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: PF_URL.pullPjo,
            type: 'POST',
            dataType: 'json',
            data: { docno: docno, docnopjo: no, mode: 'single' },
            success: function (res) {
                if (!res || !res.status) {
                    Swal.fire('Gagal', (res && res.message) ? res.message : 'Gagal menarik dokumen Penjualan.', 'error');
                    return;
                }

                pfReloadTable();
                pfApplyTotals(res.totals);
                pfSetSumberDoc(res.docnopjo || no);

                if (typeof checkTableData === 'function') {
                    try { checkTableData(); } catch (e) {}
                }

                Swal.fire('Berhasil', res.message || 'Detail penjualan berhasil ditarik.', 'success');
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Gagal menghubungi server (' + xhr.status + ').', 'error');
            }
        });
    };

    // Detail yang sudah ada akan diganti seluruhnya oleh detail dokumen Penjualan
    if ($('#t_proforma_dtl tbody tr').length === 0) {
        doPull();
        return;
    }

    Swal.fire({
        icon: 'question',
        title: 'Ganti detail?',
        text: 'Detail proforma saat ini akan diganti dengan seluruh detail Penjualan ' + no + '.',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tarik',
        cancelButtonText: 'Batal'
    }).then(function (r) {
        if (r.isConfirmed) {
            doPull();
        }
    });
}

function pfResetUnits($tr) {
    var $unit = $tr.find('select[data-field="unit"]');
    if (!$unit.length) return;

    $unit.empty().append(new Option('-- Pilih --', ''));
}

function pfFillUnits($tr, units, selected) {
    var $unit = $tr.find('select[data-field="unit"]');
    if (!$unit.length) return;

    var def   = $.trim(selected || '');
    var found = false;

    pfResetUnits($tr);

    $.each(units || [], function (i, it) {
        var u = $.trim(it.idunit || it.unit || '');
        if (u === '') return;

        $unit.append(new Option(u, u));
        if (u === def) found = true;
    });

    if (def !== '' && !found) {
        $unit.append(new Option(def, def));
    }

    if (def === '' && units && units.length > 0) {
        def = $.trim(units[0].unit_default || units[0].idunit || units[0].unit || '');
    }

    $unit.val(def || '');
}

function pfLoadUnits($tr, idbarang, selected) {
    idbarang = $.trim(idbarang || '');
    var def  = $.trim(selected || '');

    pfResetUnits($tr);

    if (idbarang === '') return;

    if (def !== '') {
        $tr.find('select[data-field="unit"]').append(new Option(def, def)).val(def);
    }

    $.ajax({
        url: PF_URL.listUnit,
        type: 'POST',
        dataType: 'json',
        data: { idbarang: idbarang },
        success: function (res) {
            pfFillUnits($tr, (res && res.items) ? res.items : [], def);
        },
        error: function () {
            pfFillUnits($tr, [], def);
        }
    });
}


/* Bangun baris input ADD (8 kolom: checkbox + 6 data + Aksi) */
function pfInlineAddRow(focus) {
    var $foot = pfFoot();
    if (!$foot.length) {
        return $();
    }

    var $open = pfActiveRow();
    if ($open.length) {
        if (focus) {
            $open.find('select.pf-inline-item').select2('open');
        }
        return $open;
    }

    var $tr = $('<tr class="pf-inline-row table-warning"></tr>');

    /* 1. nomor / penanda */
    $tr.append($('<td class="text-center pf-col-no"></td>')
        .append('<i class="fa fa-plus-circle" style="color:#16a34a"></i>'));

    /* 2. item */
    var $itemCell = $('<td></td>');
    $itemCell.append('<select class="pf-inline-item" style="width:100%"></select>');
    $itemCell.append($('<input type="hidden" data-field="nmbarang">'));
    $tr.append($itemCell);

    /* 3. qty */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="qty" placeholder="0" inputmode="decimal">'));

    /* 4. unit */
    $tr.append($('<td class="pf-col-unit-cell"></td>')
        .append('<select class="form-control form-control-sm" data-field="unit" style="width:100%"><option value="">-- Pilih --</option></select>'));

    /* 5. price */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="price" placeholder="0" inputmode="decimal">'));

    /* 6. amount */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm text-end" data-field="amount" readonly tabindex="-1" placeholder="0">'));

    /* 7. keterangan */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm" data-field="description" maxlength="150" placeholder="Keterangan" style="text-transform:uppercase">'));

    /* 8. aksi */
    $tr.append($('<td class="text-center pf-col-aksi"></td>')
        .append('<button type="button" class="pf-inline-btn pf-inline-save" title="Simpan baris (Enter)"><i class="fa fa-check"></i></button> ')
        .append('<button type="button" class="pf-inline-btn pf-inline-cancel" title="Batal (Esc)"><i class="fa fa-times"></i></button>'));

    $tr.attr('data-mode', 'ADD');
    $tr.data('mode', 'ADD');

    $foot.append($tr);

    pfInitItemSelect($tr);
    pfSyncEmptyState();

    if ($tr[0] && typeof $tr[0].scrollIntoView === 'function') {
        $tr[0].scrollIntoView({ block: 'nearest' });
    }

    if (focus) {
        setTimeout(function () { $tr.find('select.pf-inline-item').select2('open'); }, 60);
    }

    return $tr;
}

/* Bangun baris input EDIT dari data baris grid */
function pfInlineAddEditRow(data) {
    var $foot = pfFoot();
    if (!$foot.length || !data) {
        return $();
    }

    var idbarang = $.trim(String(data.idbarang || ''));
    var nmbarang = $.trim(String(data.nmbarang || ''));
    var unit     = $.trim(String(data.unit || ''));

    var $tr = $('<tr class="pf-inline-row table-info"></tr>');

    /* 1. penanda edit */
    $tr.append($('<td class="text-center pf-col-no"></td>')
        .append('<i class="fa fa-pencil" style="color:#d97706"></i>'));

    /* 2. item (readonly) + nmbarang hidden */
    var $itemCell = $('<td></td>');
    $itemCell.append($('<input type="text" class="form-control form-control-sm" readonly tabindex="-1">')
        .val((idbarang !== '' ? idbarang + ' - ' : '') + nmbarang));
    $itemCell.append($('<input type="hidden" data-field="nmbarang">').val(nmbarang));
    $tr.append($itemCell);

    /* 3. qty */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="qty" placeholder="0" inputmode="decimal">'));

    /* 4. unit */
    var $unitSel = $('<select class="form-control form-control-sm" data-field="unit" style="width:100%"></select>');
    $unitSel.append(new Option(unit, unit, true, true));
    $tr.append($('<td class="pf-col-unit-cell"></td>').append($unitSel));

    /* 5. price */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="price" placeholder="0" inputmode="decimal">'));

    /* 6. amount */
    $tr.append($('<td></td>').append('<input type="text" class="form-control form-control-sm text-end" data-field="amount" readonly tabindex="-1" placeholder="0">'));

    /* 7. keterangan */
    $tr.append($('<td></td>').append($('<input type="text" class="form-control form-control-sm" data-field="description" maxlength="150" placeholder="Keterangan" style="text-transform:uppercase">').val(data.description || '')));

    /* 8. aksi */
    $tr.append($('<td class="text-center pf-col-aksi"></td>')
        .append('<button type="button" class="pf-inline-btn pf-inline-save" title="Simpan perubahan (Enter)"><i class="fa fa-check"></i></button> ')
        .append('<button type="button" class="pf-inline-btn pf-inline-cancel" title="Batal (Esc)"><i class="fa fa-times"></i></button>'));

    $tr.attr('data-mode', 'EDIT');
    $tr.data('mode', 'EDIT');
    $tr.data('idurut', data.idurut || '');
    $tr.data('idbarang', idbarang);

    $foot.append($tr);

    $tr.find('[data-field="qty"]').val(pfFmt(data.qty, 4));
    $tr.find('[data-field="price"]').val(pfFmt(data.price, 4));
    pfRecalc($tr);

    pfSyncEmptyState();

    if ($tr[0] && typeof $tr[0].scrollIntoView === 'function') {
        $tr[0].scrollIntoView({ block: 'nearest' });
    }

    setTimeout(function () {
        $tr.find('[data-field="qty"]').focus().select();
    }, 60);

    return $tr;
}


function pfRowPayload($tr) {
    var isEdit = pfIsEdit($tr);

    return {
        idurut:      isEdit ? $.trim(String($tr.data('idurut') || '')) : '',
        idbarang:    $.trim(String($tr.data('idbarang') || '')),
        nmbarang:    $.trim($tr.find('[data-field="nmbarang"]').val() || ''),
        unit:        $.trim($tr.find('select[data-field="unit"]').val() || ''),
        qty:         pfNum($tr.find('[data-field="qty"]').val()),
        price:       pfNum($tr.find('[data-field="price"]').val()),
        description: $.trim($tr.find('[data-field="description"]').val() || '')
    };
}

function pfIsRowEmpty($tr) {
    if (!$tr || !$tr.length) return true;
    if (pfIsEdit($tr)) return false;

    var p = pfRowPayload($tr);
    return p.idbarang === '' && p.nmbarang === '' && p.qty <= 0;
}

function pfSaveRow($tr) {
    if (!$tr || !$tr.length) {
        return;
    }

    var p      = pfRowPayload($tr);
    var isEdit = pfIsEdit($tr);

    if (p.idbarang === '' && p.nmbarang === '') {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Nama Barang wajib diisi.' });
        return;
    }

    if (p.unit === '') {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Unit/Satuan wajib dipilih.' });
        return;
    }

    if (!p.qty || p.qty <= 0) {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Qty (Weight) harus lebih besar dari 0.' });
        $tr.find('[data-field="qty"]').focus();
        return;
    }

    var docno = pfDocno();
    if (docno === '') {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Klik Proses terlebih dahulu untuk membentuk No. Jurnal.' });
        return;
    }

    var $btn = $tr.find('.pf-inline-save').prop('disabled', true);

    $.ajax({
        url: PF_URL.saveRow,
        type: 'POST',
        dataType: 'json',
        data: {
            docno:       docno,
            idurut:      p.idurut,
            idbarang:    p.idbarang,
            nmbarang:    p.nmbarang,
            unit:        p.unit,
            qty:         p.qty,
            price:       p.price,
            description: p.description,
            downpayment: $('#downpayment').val(),
            pph22:       $('#pph22').val()
        },
        success: function (res) {
            if (!res || !res.status) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal',
                    text: (res && res.message) ? res.message : 'Detail gagal disimpan.'
                });
                return;
            }

            pfRemoveRow();
            pfReloadTable();
            pfApplyTotals(res.totals);

            if (typeof checkTableData === 'function') {
                try { checkTableData(); } catch (e) {}
            }

            // siapkan baris kosong berikutnya (khusus mode ADD)
            if (!isEdit) {
                pfInlineAddRow(false);
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Server Error', text: 'Terjadi kesalahan pada server (500)' });
        },
        complete: function () {
            $btn.prop('disabled', false);
        }
    });
}


/* Ambil data baris grid berdasarkan idurut (dari input yang sudah dirender server) */
function pfGetRowData(id) {
    var $tr = $('#t_proforma_dtl tbody tr').filter(function () {
        return String($(this).find('.pf-row-check').val()) === String(id);
    }).first();

    if (!$tr.length) {
        return null;
    }

    var $tds     = $tr.find('td');
    var $item    = $tds.eq(1).find('select');
    var idbarang = $item.length ? $.trim(String($item.val() || '')) : '';
    var nmbarang = '';

    if ($item.length) {
        nmbarang = $.trim($item.find('option:selected').text() || '');
        if (idbarang !== '' && nmbarang.indexOf(idbarang) === 0) {
            nmbarang = $.trim(nmbarang.substring(idbarang.length));
        }
    }

    return {
        idurut:      id,
        idbarang:    idbarang,
        nmbarang:    nmbarang,
        unit:        $.trim(String($tds.eq(3).find('select option:selected').val() || '')),
        qty:         $tds.eq(2).find('input').val() || 0,
        price:       $tds.eq(4).find('input').val() || 0,
        description: $tds.eq(6).find('input').val() || ''
    };
}

/* Buka edit inline untuk baris idurut */
function pfOpenEdit(id) {
    var $open = pfActiveRow();

    if ($open.length) {
        if (!pfIsRowEmpty($open)) {
            Swal.fire({
                icon: 'info',
                title: 'Perhatian',
                text: 'Selesaikan atau batalkan baris input yang sedang aktif terlebih dahulu'
            });
            return;
        }

        pfRemoveRow();
    }

    var data = pfGetRowData(id);
    if (!data) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Baris detail tidak ditemukan. Muat ulang halaman.' });
        return;
    }

    pfInlineAddEditRow(data);
}

function pfCheckedIds() {
    return $('#t_proforma_dtl tbody .pf-row-check:checked').map(function () {
        return String($(this).val() || '');
    }).get();
}

function pfDeleteRows(ids) {
    if (!ids || !ids.length) {
        return;
    }

    var docno = pfDocno();
    if (docno === '') {
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Hapus ' + ids.length + ' baris detail terpilih?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(function (r) {
        if (!r.isConfirmed) return;

        $.ajax({
            url: PF_URL.delRow,
            type: 'POST',
            dataType: 'json',
            data: { docno: docno, id: ids },
            success: function (res) {
                if (!res || !res.status) {
                    Swal.fire('Gagal', (res && res.messages) ? res.messages : 'Gagal hapus data', 'error');
                    return;
                }

                pfReloadTable();
                pfRefreshTotals();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Gagal menghubungi server (' + xhr.status + ')', 'error');
            }
        });
    });
}

function pfRefreshTotals() {
    var docno = pfDocno();
    if (docno === '') {
        return;
    }

    $.post(PF_URL.refresh, {
        docno: docno,
        downpayment: $('#downpayment').val(),
        pph22: $('#pph22').val()
    }, function (res) {
        if (res && res.status) {
            pfApplyTotals(res.totals);
            if (typeof checkTableData === 'function') {
                try { checkTableData(); } catch (e) {}
            }
        }
    }, 'json');
}


/* ------------------------------- events -------------------------------- */

$(document).on('click', '#pfBtnAdd', function (e) {
    e.preventDefault();
    pfInlineAddRow(true);
});

$(document).on('click', '#pfBtnUpdate', function (e) {
    e.preventDefault();

    var ids = pfCheckedIds();

    if (ids.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih satu baris yang akan diupdate.' });
        return;
    }

    if (ids.length > 1) {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Update hanya boleh satu baris.' });
        return;
    }

    pfOpenEdit(ids[0]);
});

$(document).on('click', '#pfBtnDelete', function (e) {
    e.preventDefault();

    var ids = pfCheckedIds();

    if (ids.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih baris yang akan dihapus.' });
        return;
    }

    pfDeleteRows(ids);
});

/* Tombol aksi per baris (pensil / tong sampah) */
$(document).on('click', '#t_proforma_dtl tbody .pf-row-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    var $btn = $(this);
    var id   = String($btn.attr('data-id') || '');
    var $cb  = $btn.closest('tr').find('.pf-row-check');

    $('#t_proforma_dtl tbody .pf-row-check').not($cb).prop('checked', false);
    $cb.prop('checked', true);

    if ($btn.hasClass('pf-row-btn-edit')) {
        pfOpenEdit(id);
    } else {
        pfDeleteRows([id]);
    }
});

/* Klik pesan empty state = tambah baris */
$(document).on('click', '#t_proforma_dtl td.dataTables_empty', function () {
    pfInlineAddRow(true);
});

/* Recalculate amount */
$(document).on('input change', '#proformaInlineFoot [data-field="qty"], #proformaInlineFoot [data-field="price"]', function () {
    pfRecalc($(this).closest('tr'));
});

$(document).on('click', '.pf-inline-save', function (e) {
    e.preventDefault();
    pfSaveRow($(this).closest('tr'));
});

$(document).on('click', '.pf-inline-cancel', function (e) {
    e.preventDefault();
    pfRemoveRow();
});

/* Enter simpan, Esc batal */
$(document).on('keydown', '#proformaInlineFoot input, #proformaInlineFoot select', function (e) {
    if (e.key === 'Enter') {
        if ($(this).is('select')) {
            return;
        }

        e.preventDefault();
        pfSaveRow($(this).closest('tr'));
    } else if (e.key === 'Escape') {
        e.preventDefault();
        pfRemoveRow();
    }
});

/* DP berubah → hitung ulang total (server) */
$(document).on('change blur', '#downpayment', function () {
    pfRefreshTotals();
});

/* Check all */
$(document).on('change', '#pfCheckAll', function () {
    $('#t_proforma_dtl tbody .pf-row-check').prop('checked', $(this).is(':checked'));
    $('#t_proforma_dtl tbody .pf-row-check').trigger('change');
});

$(document).on('change', '#t_proforma_dtl tbody .pf-row-check', function () {
    var total  = $('#t_proforma_dtl tbody .pf-row-check').length;
    var picked = $('#t_proforma_dtl tbody .pf-row-check:checked').length;

    $('#pfCheckAll').prop('checked', total > 0 && picked === total);
});

/* Shortcut keyboard (hanya halaman input proforma) */
$(document).on('keydown', function (e) {
    if (!document.getElementById('pfBtnAdd')) {
        return;
    }

    var key = (e.key || '').toLowerCase();

    if (e.ctrlKey && key === 'q') {
        e.preventDefault();
        pfInlineAddRow(true);
        return;
    }

    if (e.altKey && key === 'n') {
        e.preventDefault();
        pfInlineAddRow(true);
        return;
    }

    if (e.ctrlKey && key === 'e') {
        e.preventDefault();
        $('#pfBtnUpdate').trigger('click');
        return;
    }

    if (e.ctrlKey && key === 'd') {
        e.preventDefault();
        $('#pfBtnDelete').trigger('click');
        return;
    }
});

/* ===================== CABANG / JOB (referensi addDeliveryOrder) ===================== */

function pfInitCabangSelect() {
    var $cabang = $('#cabang');

    if (!$cabang.length || $cabang.data('select2')) {
        return;
    }

    $cabang.select2({
        placeholder: 'Pilih Cabang / Job',
        allowClear: false,
        width: '100%',
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_branchjob',
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
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items || [],
                    pagination: { more: (params.page * 30) < (data.total_count || 0) }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) { return markup; },
        templateResult: function (repo) {
            if (repo.loading) { return repo.text; }
            return (repo.idbranch || repo.id || '') + ' - ' + (repo.nmbranch || repo.text || '');
        },
        templateSelection: function (repo) {
            return repo.nmbranch || repo.text;
        }
    });
}

function pfApplySessionBranch() {
    var $cabang = $('#cabang');
    if (!$cabang.length) {
        return;
    }

    // Sudah ada nilai (draft / revisi dari documentReadable) → jangan ditimpa
    if ($.trim(String($cabang.val() || '')) !== '') {
        return;
    }

    var sessionBranchId   = $.trim(String($cabang.attr('data-session-branch-id') || ''));
    var sessionBranchName = $.trim(String($cabang.attr('data-session-branch-name') || ''));
    var canChoose         = String($cabang.attr('data-can-choose-branch') || '0') === '1';

    if (typeof salesFindBranchFromSession !== 'function' || typeof salesApplyInitialBranch !== 'function') {
        return;
    }

    salesFindBranchFromSession(sessionBranchId, sessionBranchName, function (item) {
        if ($.trim(String($('#cabang').val() || '')) !== '') {
            return;
        }

        if (!item) {
            if (sessionBranchId === '') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Cabang / Job user login tidak ditemukan. Silakan hubungi IT.',
                        icon: 'warning'
                    });
                }
                return;
            }

            item = { id: sessionBranchId, nmbranch: sessionBranchName || sessionBranchId };
        }

        salesApplyInitialBranch(item, canChoose, true);
    });
}

$(function () {
    if (!$('#t_proforma_dtl').length) {
        return;
    }

    pfInitCabangSelect();
    pfApplySessionBranch();

    pfInitTable();
    pfRefreshTotals();

    // Button Tarik Data Penjualan hanya aktif setelah Customer dipilih
    pfSyncTarikBtn();
    pfLoadPenjualanDocs();

    $(document).on('change', '#cust', function () {
        pfSyncTarikBtn();
        pfLoadPenjualanDocs();
    });

    var sumber = $.trim(String($('#docnopjo').val() || ''));
    if (sumber !== '') {
        $('#pfSumberInfo').text(sumber);
    }
});


/* ===================== TARIK DATA PENJUALAN ===================== */

function pfShowModal(id) {
    var el = document.getElementById(id);
    if (!el) return;

    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        window.bootstrap.Modal.getOrCreateInstance(el).show();
        return;
    }

    if ($.fn && typeof $.fn.modal === 'function') {
        $(el).modal('show');
    }
}

function pfHideModal(id) {
    var el = document.getElementById(id);
    if (!el) return;

    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        window.bootstrap.Modal.getOrCreateInstance(el).hide();
        return;
    }

    if ($.fn && typeof $.fn.modal === 'function') {
        $(el).modal('hide');
    }
}

function pfSetSumberDoc(value) {
    var $el = $('#docnopjo');
    if (!$el.length || !value) return;

    if ($el.find('option').filter(function () { return String($(this).val()) === String(value); }).length === 0) {
        $el.append(new Option(value, value, true, true));
    }

    $el.val(value).trigger('change');

    var info = $('#pfSumberInfo');
    if (info.length) {
        info.text(value);
    }
}

/* =========================================================================
 * TARIK DATA PENJUALAN (mode SINGLE / MULTI)
 * - SINGLE : hanya 1 dokumen penjualan, detail item dibuka lewat modal.
 * - MULTI  : beberapa dokumen, wajib dari customer yang sama.
 * ========================================================================= */

var PF_PJO_ROWS  = [];
var PF_PJO_META  = {};
var PF_PJO_CACHE = {};
var PF_SEL       = {};
var PF_ACTIVE    = '';

/* ------------------------------ state helpers ------------------------------ */

function pfSelectedDocs() {
    var out = [];

    $.each(PF_SEL, function (no, ids) {
        if (ids && Object.keys(ids).length) {
            out.push(no);
        }
    });

    return out;
}

function pfItemCount(no) {
    return (PF_SEL[no] && PF_SEL[no]) ? Object.keys(PF_SEL[no]).length : 0;
}

function pfTotalItems() {
    var total = 0;

    $.each(pfSelectedDocs(), function (i, no) {
        total += pfItemCount(no);
    });

    return total;
}

function pfMetaOf(no) {
    return PF_PJO_META[no] || { docno: no, kdcustomer: '', nmcustomer: '', docdate: '', jurnalno: '' };
}

function pfUpdateCount() {
    $('#pjoPickCount').text(pfTotalItems());
    pfSyncPickedLabels();
}

function pfSyncPickedLabels() {
    $('#tblPJOList tr.pjo-doc-row').each(function () {
        var no      = String($(this).attr('data-docno') || '');
        var n       = pfItemCount(no);
        var $picked = $(this).find('span.pjo-doc-picked');

        $picked.text(n + ' item').toggleClass('pf-none', n === 0);
        $(this).toggleClass('pjo-doc-active', n > 0);
    });
}

function pfSyncDocChecks() {
    $('#tblPJOList tr.pjo-doc-row').each(function () {
        var no = String($(this).attr('data-docno') || '');
        $(this).find('input.pjo-doc-chk').prop('checked', pfItemCount(no) > 0);
    });
}

/* --------------------------- customer validation --------------------------- */

function pfLockedCustomer() {
    var docs = pfSelectedDocs();

    for (var i = 0; i < docs.length; i++) {
        var kd = $.trim(String(pfMetaOf(docs[i]).kdcustomer || '')).toUpperCase();

        if (kd !== '') {
            return kd;
        }
    }

    return '';
}

function pfCheckSameCustomer(no) {
    if (PF_MODE !== 'multi') {
        return true;
    }

    var lock = pfLockedCustomer();
    if (lock === '') {
        return true;
    }

    var kd = $.trim(String(pfMetaOf(no).kdcustomer || '')).toUpperCase();

    if (kd === '' || kd === lock) {
        return true;
    }

    Swal.fire('Customer Berbeda', 'Mode MULTI hanya boleh menarik dokumen dari customer yang sama (' + pfPjoTxt(lock) + ').', 'warning');

    return false;
}

/* ------------------------------- modal flow -------------------------------- */

function pfSyncTarikBtn() {
    var $btn = $('#btnTarikPJOData');
    if (!$btn.length) {
        return;
    }

    var hasCustomer = $.trim(String($('#cust').val() || '')) !== '';
    var hasDocno    = pfDocno() !== '';

    $btn.prop('disabled', !(hasCustomer && hasDocno));
    $btn.attr('title', !hasCustomer
        ? 'Pilih Customer terlebih dahulu'
        : (hasDocno
            ? 'Tarik data dari Penjualan FINAL/APPROVE (Single / Multiple)'
            : 'Klik Proses terlebih dahulu untuk membentuk No. Jurnal'));
}

function pfOpenTarik() {
    if (pfDocno() === '') {
        Swal.fire('Perhatian', 'Klik Proses terlebih dahulu untuk membentuk No. Jurnal proforma.', 'warning');
        return;
    }

    if ($.trim(String($('#cust').val() || '')) === '') {
        Swal.fire('Perhatian', 'Pilih Customer terlebih dahulu sebelum menarik data Penjualan.', 'warning');
        return;
    }

    pfShowModal('modalPilihModePJO');
}

function pfSetMode(mode) {
    PF_MODE = (mode === 'multi') ? 'multi' : 'single';

    $('#pfModeLabel').text(PF_MODE === 'multi' ? 'MULTI' : 'SINGLE');

    $('#pjoPickNote').html(PF_MODE === 'single'
        ? 'Klik baris Penjualan untuk membuka <b>detail item</b>, lalu centang item yang akan ditarik. <b>Mode SINGLE</b> hanya boleh satu dokumen penjualan.'
        : 'Klik baris Penjualan untuk membuka <b>detail item</b>. Mode <b>MULTI</b> boleh menarik item dari beberapa dokumen, tetapi semuanya harus dari <b>customer yang sama</b>.');

    pfCloseItemView();
    pfResetSel();
}

function pfShowItemView(no) {
    PF_ACTIVE = no;

    $('#pjoModalTitle').text('Detail Item Penjualan');
    $('#pjoItemDocno').text(no);

    $('#pjoListView').hide();
    $('#pjoItemView').show();

    pfLoadPjoItems(no, function () {
        pfRenderPjoItems(no);
    });
}

function pfCloseItemView() {
    PF_ACTIVE = '';

    $('#pjoItemView').hide();
    $('#pjoListView').show();
    $('#pjoModalTitle').text('Tarik Data Penjualan');
    $('#pjoItemDocno').text('-');
    $('#pjoItemCust').text('');
}

function pfResetSel() {
    PF_SEL = {};

    $('#tblPJOList input.pjo-doc-chk').prop('checked', false);
    pfUpdateCount();

    if (PF_ACTIVE !== '') {
        pfSyncItemChecks(PF_ACTIVE);
        pfUpdateItemNote(PF_ACTIVE);
    }
}

/* ---------------------------- daftar dokumen ----------------------------- */

function pfPjoDate(v) {
    var s = String(v || '').substring(0, 10);
    var p = s.split('-');

    return (p.length === 3) ? (p[2] + '-' + p[1] + '-' + p[0]) : s;
}

function pfPjoTxt(v) {
    return $('<div>').text(v === undefined || v === null ? '' : String(v)).html();
}

function pfRenderFilterNote() {
    var cust  = $.trim(String($('#cust').val() || ''));
    var $note = $('#pjoFilterNote');

    if (cust !== '') {
        $note.removeClass('pf-warn').html('<i class="fa fa-filter"></i> Hanya menampilkan Penjualan customer <b>' + pfPjoTxt(cust) + '</b>.');
        return;
    }

    $note.addClass('pf-warn').html('<i class="fa fa-exclamation-triangle"></i> Customer pada header belum dipilih. Pada mode <b>MULTI</b> semua dokumen yang dipilih harus dari customer yang sama.');
}

function pfLoadPjoList() {
    var $tb = $('#tblPJOList tbody');

    PF_PJO_ROWS  = [];
    PF_PJO_META  = {};
    PF_PJO_CACHE = {};

    pfRenderFilterNote();
    $tb.html('<tr><td colspan="7" class="text-center text-muted py-2">Memuat data...</td></tr>');

    $.ajax({
        url: PF_URL.listPjo,
        type: 'POST',
        dataType: 'json',
        data: {
            cust: $.trim(String($('#cust').val() || '')),
            search: $.trim(String($('#pjoSearch').val() || ''))
        },
        success: function (res) {
            PF_PJO_ROWS = (res && res.rows) ? res.rows : [];
            pfRenderPjoList();
        },
        error: function () {
            $tb.html('<tr><td colspan="7" class="text-center text-danger py-2">Gagal memuat daftar Penjualan.</td></tr>');
        }
    });
}

function pfRenderPjoList() {
    var $tb = $('#tblPJOList tbody');

    if (!PF_PJO_ROWS.length) {
        $tb.html('<tr><td colspan="7" class="text-center text-muted py-2">Tidak ada Penjualan FINAL/APPROVE yang bisa ditarik.</td></tr>');
        pfUpdateCount();
        return;
    }

    var h = '';

    $.each(PF_PJO_ROWS, function (i, r) {
        var no = $.trim(String(r.docno || ''));

        if (no === '') {
            return;
        }

        PF_PJO_META[no] = {
            docno:      no,
            kdcustomer: $.trim(String(r.kdcustomer || '')),
            nmcustomer: $.trim(String(r.nmcustomer || '')),
            docdate:    pfPjoDate(r.docdate),
            jurnalno:   $.trim(String(r.jurnalno || ''))
        };

        h += '<tr class="pjo-doc-row" data-docno="' + pfPjoTxt(no) + '">'
            + '<td class="pjo-doc-check-cell">'
            + '<input type="' + (PF_MODE === 'single' ? 'radio' : 'checkbox') + '" class="pjo-doc-chk" name="pjoPickDoc" value="' + pfPjoTxt(no) + '" title="Pilih dokumen ini">'
            + '</td>'
            + '<td class="pjo-pick-col-no">' + pfPjoTxt(no) + '</td>'
            + '<td>' + pfPjoTxt(PF_PJO_META[no].docdate) + '</td>'
            + '<td>' + pfPjoTxt(PF_PJO_META[no].nmcustomer || PF_PJO_META[no].kdcustomer) + '</td>'
            + '<td class="text-end">' + pfFmt(r.total, 2) + '</td>'
            + '<td>' + pfPjoTxt(PF_PJO_META[no].jurnalno) + '</td>'
            + '<td class="text-center"><span class="pjo-doc-picked pf-none">0 item</span><br><span class="pjo-doc-open"><i class="fa fa-search"></i> Detail</span></td>'
            + '</tr>';
    });

    $tb.html(h);
    pfSyncDocChecks();
    pfUpdateCount();
}


/* ------------------------------ detail item ------------------------------ */

function pfLoadPjoItems(no, cb) {
    var cache = PF_PJO_CACHE[no];

    if (cache && cache.loaded) {
        if (cb) { cb(); }
        return;
    }

    PF_PJO_CACHE[no] = { loaded: false, rows: [] };
    $('#tblPJOItemList tbody').html('<tr><td colspan="7" class="text-center text-muted py-2">Memuat detail...</td></tr>');

    $.ajax({
        url: PF_URL.pjoDetail,
        type: 'POST',
        dataType: 'json',
        data: { docnopjo: no, docno: pfDocno() },
        success: function (res) {
            PF_PJO_CACHE[no] = { loaded: true, rows: (res && res.rows) ? res.rows : [] };

            if (res && res.header) {
                var meta = pfMetaOf(no);

                PF_PJO_META[no] = {
                    docno:      no,
                    kdcustomer: $.trim(String(res.header.kdcustomer || meta.kdcustomer || '')),
                    nmcustomer: $.trim(String(res.header.nmcustomer || meta.nmcustomer || '')),
                    docdate:    pfPjoDate(res.header.docdate || ''),
                    jurnalno:   $.trim(String(res.header.jurnalno || meta.jurnalno || ''))
                };
            }

            if (cb) { cb(); }
        },
        error: function () {
            PF_PJO_CACHE[no] = { loaded: true, rows: [] };
            if (cb) { cb(); }
        }
    });
}

function pfRenderPjoItems(no) {
    var cache = PF_PJO_CACHE[no] || { rows: [] };
    var $tb   = $('#tblPJOItemList tbody');
    var meta  = pfMetaOf(no);

    $('#pjoItemCust').text([meta.kdcustomer, meta.nmcustomer].filter(Boolean).join(' - '));

    if (!cache.rows.length) {
        $tb.html('<tr><td colspan="7" class="text-center text-muted py-2">Tidak ada detail item pada dokumen ini.</td></tr>');
        pfSyncItemChecks(no);
        pfUpdateItemNote(no);
        return;
    }

    var h = '';

    $.each(cache.rows, function (i, d) {
        var uid = String(d.idurutpjo);

        // MULTI menarik dokumen secara utuh (1 dokumen = 1 baris), jadi penanda
        // "sudah ada" tidak dipakai agar dokumen tetap bisa dipilih ulang.
        var sudah = (PF_MODE !== 'multi') && pfNum(d.sudah) === 1;

        h += '<tr class="' + (sudah ? 'pjo-pick-dtl-done' : '') + '">'
            + '<td class="text-center"><input type="checkbox" class="pjo-item-chk" data-docno="' + pfPjoTxt(no) + '" value="' + pfPjoTxt(uid) + '"'
            + (sudah ? ' disabled title="Sudah ada pada dokumen proforma ini"' : '') + '></td>'
            + '<td><span class="pjo-pick-item">' + pfPjoTxt($.trim(d.idbarang || '')) + '</span> ' + pfPjoTxt($.trim(d.nmbarang || '')) + '</td>'
            + '<td>' + pfPjoTxt($.trim(d.idspec || '')) + '</td>'
            + '<td>' + pfPjoTxt($.trim(d.unit || '')) + '</td>'
            + '<td class="text-end">' + pfFmt(d.qty, 2) + '</td>'
            + '<td class="text-end">' + pfFmt(d.harga, 2) + '</td>'
            + '<td class="text-end">' + pfFmt(d.nilai, 2) + '</td>'
            + '</tr>';
    });

    $tb.html(h);
    pfSyncItemChecks(no);
    pfUpdateItemNote(no);
}


function pfSyncItemChecks(no) {
    var sel = PF_SEL[no] || {};

    $('#tblPJOItemList input.pjo-item-chk').each(function () {
        $(this).prop('checked', sel[String($(this).val())] === true);
    });

    pfSyncItemAll();
    pfSyncDocChecks();
    pfUpdateCount();
}

function pfSyncItemAll() {
    var $chk   = $('#tblPJOItemList input.pjo-item-chk:enabled');
    var total  = $chk.length;
    var picked = $chk.filter(':checked').length;

    $('#pjoItemChkAll')
        .prop('checked', total > 0 && picked === total)
        .prop('indeterminate', picked > 0 && picked < total);
}

function pfUpdateItemNote(no) {
    var n = pfItemCount(no);

    $('#pjoItemNote').html(n > 0
        ? '<i class="fa fa-check-circle" style="color:#16a34a"></i> <b>' + n + '</b> item dipilih dari dokumen ini.'
        : 'Belum ada item yang dipilih dari dokumen ini.');
}

/* SINGLE: pilihan item hanya boleh dari satu dokumen */
function pfClearOtherDocs(no) {
    if (PF_MODE !== 'single') {
        return;
    }

    $.each(pfSelectedDocs(), function (i, d) {
        if (d !== no) {
            delete PF_SEL[d];
        }
    });
}

function pfToggleItem(no, id, on) {
    if (on && !pfCheckSameCustomer(no)) {
        return false;
    }

    if (on) {
        pfClearOtherDocs(no);
    }

    if (!PF_SEL[no]) {
        PF_SEL[no] = {};
    }

    if (on) {
        PF_SEL[no][id] = true;
    } else {
        delete PF_SEL[no][id];

        if (!Object.keys(PF_SEL[no]).length) {
            delete PF_SEL[no];
        }
    }

    pfSyncItemAll();
    pfSyncDocChecks();
    pfUpdateItemNote(no);
    pfUpdateCount();

    return true;
}

function pfPickDoc(no, on) {
    if (!on) {
        delete PF_SEL[no];
        pfSyncDocChecks();
        pfUpdateCount();

        if (PF_ACTIVE === no) {
            pfSyncItemChecks(no);
            pfUpdateItemNote(no);
        }

        return;
    }

    if (!pfCheckSameCustomer(no)) {
        pfSyncDocChecks();
        return;
    }

    pfClearOtherDocs(no);

    pfLoadPjoItems(no, function () {
        var cache = PF_PJO_CACHE[no] || { rows: [] };
        var ids   = {};

        $.each(cache.rows, function (i, d) {
            // MULTI: seluruh item dokumen ikut terpilih (dokumen ditarik utuh)
            if (PF_MODE !== 'multi' && pfNum(d.sudah) === 1) {
                return;
            }

            ids[String(d.idurutpjo)] = true;
        });

        if (Object.keys(ids).length) {
            PF_SEL[no] = ids;
        } else {
            delete PF_SEL[no];
        }

        pfSyncDocChecks();
        pfUpdateCount();

        if (PF_ACTIVE === no) {
            pfSyncItemChecks(no);
            pfUpdateItemNote(no);
        }
    });
}

function pfSetAllItems(no, on) {
    if (no === '') {
        return;
    }

    if (on && !pfCheckSameCustomer(no)) {
        pfSyncItemAll();
        return;
    }

    if (on) {
        pfClearOtherDocs(no);
    }

    var $chk = $('#tblPJOItemList input.pjo-item-chk:enabled');

    if (!PF_SEL[no]) {
        PF_SEL[no] = {};
    }

    $chk.each(function () {
        var id = String($(this).val() || '');

        if (on) {
            PF_SEL[no][id] = true;
        } else {
            delete PF_SEL[no][id];
        }

        $(this).prop('checked', on);
    });

    if (!Object.keys(PF_SEL[no]).length) {
        delete PF_SEL[no];
    }

    pfSyncItemAll();
    pfSyncDocChecks();
    pfUpdateItemNote(no);
    pfUpdateCount();
}


/* ---------------------------------- pull --------------------------------- */

function pfPullPjo() {
    var items = [];
    var docs  = {};

    $.each(pfSelectedDocs(), function (i, no) {
        $.each(PF_SEL[no], function (id) {
            if (id === '') {
                return;
            }

            items.push(no + '|' + id);
            docs[no] = true;
        });
    });

    if (!items.length) {
        Swal.fire('Perhatian', 'Centang minimal satu detail Penjualan yang akan ditarik.', 'warning');
        return;
    }

    var docKeys = Object.keys(docs);

    if (PF_MODE === 'single' && docKeys.length > 1) {
        Swal.fire('Perhatian', 'Mode SINGLE hanya boleh satu dokumen penjualan.', 'warning');
        return;
    }

    if (PF_MODE === 'multi' && docKeys.length > 1) {
        var custs = {};

        $.each(docKeys, function (i, no) {
            custs[$.trim(String(pfMetaOf(no).kdcustomer || '')).toUpperCase()] = true;
        });

        if (Object.keys(custs).length > 1) {
            Swal.fire('Customer Berbeda', 'Mode MULTI hanya boleh menarik dokumen dari customer yang sama.', 'warning');
            return;
        }
    }

    var docno = pfDocno();
    if (docno === '') {
        Swal.fire('Perhatian', 'No. Jurnal/Dokumen proforma belum terbentuk.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Tarik Data Penjualan?',
        html: (PF_MODE === 'multi')
            ? 'Detail proforma saat ini akan <b>diganti</b> dengan <b>' + docKeys.length +
              ' baris</b> (1 baris per dokumen: No. Penjualan + total nilainya).'
            : 'Detail proforma saat ini akan <b>diganti</b> dengan ' + items.length + ' detail terpilih' +
              (docKeys.length > 1 ? ' dari <b>' + docKeys.length + ' dokumen</b>' : '') + '.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tarik',
        cancelButtonText: 'Batal'
    }).then(function (r) {
        if (!r.isConfirmed) {
            return;
        }

        var $btn = $('#btnPullPJOData').prop('disabled', true);

        $.ajax({
            url: PF_URL.pullPjo,
            type: 'POST',
            dataType: 'json',
            data: { docno: docno, mode: PF_MODE, items: items },
            success: function (res) {
                if (!res || !res.status) {
                    Swal.fire('Gagal', (res && res.message) ? res.message : 'Gagal menarik data penjualan.', 'error');
                    return;
                }

                pfHideModal('modalTarikPJOData');
                pfCloseItemView();
                pfResetSel();

                pfSetSumberDoc(res.docnopjo || '');

                // Mode MULTI: satu dokumen = satu baris detail, tampilkan semua No. Penjualan
                if (res.mode === 'multi' && res.docs && res.docs.length) {
                    var label = res.docs.join(', ');

                    $('#docnopjo option').filter(function () {
                        return String($(this).val()) === String(res.docnopjo || '');
                    }).text(label);

                    var $info = $('#pfSumberInfo');
                    if ($info.length) {
                        $info.text(label);
                    }
                }

                pfReloadTable();
                pfApplyTotals(res.totals);

                if (typeof checkTableData === 'function') {
                    try { checkTableData(); } catch (e) {}
                }

                Swal.fire('Berhasil', res.message || 'Data penjualan berhasil ditarik.', 'success');
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Gagal menghubungi server (' + xhr.status + ')', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });
}

/* -------------------------------- events --------------------------------- */

$(document).on('click', '#btnTarikPJOData', function (e) {
    e.preventDefault();
    pfOpenTarik();
});

$(document).on('click', '#modalPilihModePJO .pf-mode-btn', function (e) {
    e.preventDefault();

    pfSetMode(String($(this).attr('data-mode') || 'single'));
    pfHideModal('modalPilihModePJO');

    $('#pjoSearch').val('');
    pfShowModal('modalTarikPJOData');
    $('.pjo-pick-wrap').scrollTop(0);
    pfLoadPjoList();
});

$(document).on('click', '#btnSearchPJO', function (e) {
    e.preventDefault();
    pfLoadPjoList();
});

$(document).on('keydown', '#pjoSearch', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        pfLoadPjoList();
    }
});

$(document).on('click', '#tblPJOList tr.pjo-doc-row', function (e) {
    if ($(e.target).is('input[type="checkbox"], input[type="radio"]')) {
        return;
    }

    pfShowItemView(String($(this).attr('data-docno') || ''));
});

$(document).on('change', '#tblPJOList input.pjo-doc-chk', function () {
    var no = String($(this).val() || '');

    pfPickDoc(no, $(this).is(':checked'));
});

$(document).on('change', '#tblPJOItemList input.pjo-item-chk', function () {
    var no = String($(this).attr('data-docno') || '');
    var id = $.trim(String($(this).val() || ''));
    var on = $(this).is(':checked');

    if (!pfToggleItem(no, id, on)) {
        $(this).prop('checked', !on);
    }
});

$(document).on('change', '#pjoItemChkAll', function () {
    pfSetAllItems(PF_ACTIVE, $(this).is(':checked'));
});

$(document).on('click', '#btnBackPJO', function (e) {
    e.preventDefault();
    pfCloseItemView();
});

$(document).on('click', '#btnResetPJO', function (e) {
    e.preventDefault();
    pfResetSel();
});

$(document).on('click', '#btnPullPJOData', function (e) {
    e.preventDefault();
    pfPullPjo();
});

/* --------------------- SIMPAN DRAFT / FINAL ENTRY ------------------------ */

/* Nilai header yang dipakai bersama oleh Simpan Draft & Submit Final Entry */
function pfHeaderPayload() {
    return {
        docno:       pfDocno(),
        downpayment: pfNum($('#downpayment').val()),
        pph22:       pfNum($('#pph22').val()),
        desc:        $.trim(String($('#desc').val() || '')),
        bank:        $.trim(String($('#bank').val() || '')),
        nmbank:      $.trim(String($('#nmbank').val() || '')),
        alamatbank:  $.trim(String($('#alamatbank').val() || '')),
        accname:     $.trim(String($('#accname').val() || '')),
        accno:       $.trim(String($('#accno').val() || '')),
        swiftcode:   $.trim(String($('#swiftcode').val() || '')),

        // Tujuan / Rekanan (hanya diproses server bila tujuan_sync = 1)
        tujuan_sync:              1,
        customerdestination:      $.trim(String($('#customerdestination').val() || '')),
        customerdestination_addr: $.trim(String($('#customerdestination_addr').val() || '')),
        trader:                   $.trim(String($('#trader').val() || '')),
        trader_addr:              $.trim(String($('#trader_addr').val() || '')),
        enduser:                  $.trim(String($('#enduser').val() || '')),
        enduser_addr:             $.trim(String($('#enduser_addr').val() || ''))
    };
}

/* Masih ada baris inline yang belum disimpan (sedang edit atau sudah diisi)? */
function pfHasUnsavedRow() {
    var $tr = pfActiveRow();

    if (!$tr.length) {
        return false;
    }

    if (!pfIsEdit($tr) && pfIsRowEmpty($tr)) {
        return false;
    }

    return true;
}

/* Jumlah baris detail yang sudah tersimpan di grid */
function pfSavedRowCount() {
    var n = 0;

    $('#t_proforma_dtl tbody tr').each(function () {
        if ($(this).find('.pf-row-check').length) {
            n++;
        }
    });

    return n;
}

/* Validasi umum sebelum draft / final entry */
function pfValidateBeforeSave(data) {
    if (data.docno === '') {
        Swal.fire('Perhatian', 'Klik Proses terlebih dahulu untuk membentuk No. Jurnal proforma.', 'warning');
        return false;
    }

    if (pfHasUnsavedRow()) {
        Swal.fire('Perhatian', 'Masih ada baris detail yang belum disimpan. Tekan Enter untuk menyimpan baris (Esc untuk membatalkan), lalu ulangi.', 'warning');
        return false;
    }

    if (pfSavedRowCount() <= 0) {
        Swal.fire('Perhatian', 'Detail masih kosong. Tambahkan minimal satu item sebelum menyimpan.', 'warning');
        return false;
    }

    return true;
}

/* ------------------------------ Simpan Draft ----------------------------- */

function pfSaveDraftProforma() {
    var data = pfHeaderPayload();

    if (!pfValidateBeforeSave(data)) {
        return false;
    }

    Swal.fire({
        title: 'Simpan Draft?',
        text: 'Dokumen ' + data.docno + ' akan disimpan sebagai DRAFT dan dapat dibuka kembali dari daftar untuk revisi.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then(function (r) {
        if (!r.isConfirmed) {
            return;
        }

        var $btn = $('#btnSimpanDraftProforma').prop('disabled', true);

        $.ajax({
            url: PF_URL.draft,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (res) {
                if (!res || !res.status) {
                    Swal.fire('Gagal', (res && res.message) ? res.message : 'Draft gagal disimpan.', 'error');
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message || ('Draft proforma ' + data.docno + ' berhasil disimpan.'),
                    allowOutsideClick: false,
                    confirmButtonText: 'Ke Daftar Proforma'
                }).then(function () {
                    window.location.href = res.redirect || (HOST_URL + 'sales/presales/performainvoice');
                });
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Gagal menghubungi server (' + xhr.status + ').', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    return false;
}

/* --------------------------- Submit Final Entry -------------------------- */

function pfSaveFinalProforma() {
    var data = pfHeaderPayload();

    if (!pfValidateBeforeSave(data)) {
        return false;
    }

    if (String($('#jnsinvoice').val() || '').toUpperCase() === 'INVOICE'
        && $.trim(String($('#docnopjo').val() || '')) === '') {
        Swal.fire('Perhatian', 'INVOICE wajib menarik PJO yang sudah memiliki jurnal POSTED.', 'warning');
        return false;
    }

    if (data.bank === '') {
        Swal.fire('Perhatian', 'Bank harus dipilih terlebih dahulu!', 'warning');
        return false;
    }

    Swal.fire({
        title: 'Yakin?',
        text: 'Finish Entry Proforma ' + data.docno + '? Setelah FINAL, dokumen tidak dapat diubah lagi.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Final Entry',
        cancelButtonText: 'Batal'
    }).then(function (r) {
        if (!r.isConfirmed) {
            return;
        }

        var $btn = $('.btn-final-entry').prop('disabled', true);

        $.ajax({
            url: PF_URL.final,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (res) {
                if (!res || res.status !== 'success') {
                    Swal.fire('Gagal', (res && res.message) ? res.message : 'Final entry gagal disimpan. Periksa kembali detail proforma.', 'error');
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message || 'Final entry berhasil disimpan.',
                    showCancelButton: !!res.preview_url,
                    confirmButtonText: res.preview_url ? 'Preview Proforma' : 'OK',
                    cancelButtonText: 'Kembali ke List',
                    allowOutsideClick: false
                }).then(function (out) {
                    if (res.preview_url && out.isConfirmed) {
                        window.location.href = res.preview_url;
                        return;
                    }

                    window.location.href = res.redirect || (HOST_URL + 'sales/presales/performainvoice');
                });
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Gagal menghubungi server (' + xhr.status + ').', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    return false;
}

