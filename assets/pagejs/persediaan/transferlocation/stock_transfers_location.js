/*
stock_transfers_location.js
Versi Final - Fix Total
*/

var save_method;
var table;
var initTable;

/* ============================================================
   VIUW UTAMA — LIST TRANSFER LOCATION
   ============================================================ */
function tableTrxTransfersLocation() {
    var initTable = function () {
        var table = $('#tTrxTransferLocaton');
        table.DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "language": languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter": true,
            "lengthMenu": [
                [10, 25, 50, -1],
                ['10 rows', '25 rows', '50 rows', 'Show all']
            ],
            "dom": 'Bfrtip',
            "buttons": ['pageLength', 'excel'],
            "ajax": {
                "url": HOST_URL + 'persediaan/trans/list_trx_transfer_location',
                "type": "POST",
                "data": function (data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namasupplier = $('#namasupplier').val();
                    data.status = $('#status_filter').val();
                },
                "dataFilter": function (data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json);
                }
            },
            "columnDefs": [
                { "targets": [-1], "orderable": false }
            ]
        });
    };
    return initTable();
}

function reload_tablePPTrx() {
    var table = $('#tTrxTransferLocaton');
    table.DataTable().ajax.reload();
}

$('#btn-filter-tx').click(function () {
    var table = $('#tTrxTransferLocaton');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});

$('#btn-reset-tx').click(function () {
    $('#form-filter')[0].reset();
    var table = $('#tTrxTransferLocaton');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});

let skipRoleChange = false;

/* ============================================================
   DOCUMENT READABLE — HANYA JALAN SAAT EDIT
   ============================================================ */
function documentReadable() {

    var docno = $('[name="docno"]').val();

    if (!docno || docno.trim() === '') {
        console.log("Input baru — skip documentReadable");
        return;
    }

    $.getJSON(HOST_URL + 'persediaan/trans/showing_transfer_location_mst_tmp', { docno: docno })
        .done(function (response) {

            if (!response.dataTables || !response.dataTables.items.length) {
                console.log("Data kosong");
                return;
            }

            const item = response.dataTables.items[0];

            $('[name="docno"]').val(item.docno).prop('readonly', true);

            let prefixParts = item.docno.trim().split('/');
            $('[name="prefix"]').val(prefixParts[0]).prop('readonly', true);
            $('[name="infix"]').val(prefixParts[1]).prop('readonly', true);
            $('[name="suffix"]').val(prefixParts[2]).prop('readonly', true);

            $('[name="docdate"]').val(item.docdate).prop('disabled', true);
            $('[name="keterangan"]').val(item.description);

            skipRoleChange = true;

            const branch1 = $.getJSON(HOST_URL + 'api/globalmodule/list_branchjob', { var: item.cabang });
            const branch2 = $.getJSON(HOST_URL + 'api/globalmodule/list_branchjob', { var: item.cabang_sent });
            const locFrom = $.getJSON(HOST_URL + 'api/globalmodule/list_mlocation', { var: item.idlocation_from });
            const locTo = $.getJSON(HOST_URL + 'api/globalmodule/list_mlocation', { var: item.idlocation_to });
            const locTransit = $.getJSON(HOST_URL + 'api/globalmodule/list_mlocation', { var: item.idlocation_transit });

            $.when(branch1, branch2, locFrom, locTo, locTransit)
                .done(function (b1, b2, l1, l2, l3) {
                    setSelect2('[name="cabang"]',             b1[0].items[0], 'nmbranch', 'idbranch', true);
                    setSelect2('[name="cabang_sent"]',        b2[0].items[0], 'nmbranch', 'idbranch', true);
                    setSelect2('[name="idlocation_from"]',    l1[0].items[0], 'nmlocation', 'idlocation', true);
                    setSelect2('[name="idlocation_to"]',      l2[0].items[0], 'nmlocation', 'idlocation', true);
                    setSelect2('[name="idlocation_transit"]', l3[0].items[0], 'nmlocation', 'idlocation', true);
                });
        })
        .fail(function () {
            console.log("Failed To Loading Data");
        });
}

/* ============================================================
   HELPER — setSelect2
   ============================================================ */
function setSelect2(selector, data, textField, valueField, isDisabled = false) {
    if (!data) return;

    var option = new Option(data[textField], data[valueField], true, true);
    $(selector).append(option).trigger('change');

    if (isDisabled) {
        $(selector).prop('disabled', true);
    }
}

/* ============================================================
   SELECT2 BARANG — BERDASARKAN GUDANG ASAL
   ============================================================ */
var defaultInitialGroupBrng = '';

function initSelect2Barang() {

    $('#idbarang')
        .prop('readonly', false)
        .prop('disabled', false)
        .css('pointer-events', 'auto');

    if ($('#idbarang').data('select2')) {
        $('#idbarang').select2('destroy');
    }
    $('#idbarang').empty();

    $("#idbarang").select2({
        placeholder: "Ketik kode / nama barang...",
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        dropdownAutoWidth: true,
        closeOnSelect: true,
        multiple: false,
        dropdownParent: $('#modalDetailSPK'),
        escapeMarkup: function (markup) { return markup; },
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_stock_by_location',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_:     params.term || '',
                    _page_:       params.page || 1,
                    _perpage_:    30,
                    _parameterx_: defaultInitialGroupBrng,
                    loccode:      $('#idlocation_from').val() || '',
                    term:         params.term || ''
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                var results = (data.items || []).map(function (item) {
                    return {
                        id:         item.id,
                        text:       item.idbarang + ' — ' + (item.nmbarang || ''),
                        idbarang:   item.idbarang,
                        nmbarang:   item.nmbarang,
                        batch:      item.batch,
                        lotno:      item.lotno,
                        unit:       item.unit,
                        sisaonhand: item.sisaonhand,
                        idlocation: item.idlocation
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
        templateResult: formatBarang,
        templateSelection: formatBarangSelection
    }).on("select2:select", function (e) {

        var data = e.params.data;
        console.log("Selected:", data);

        $('[name="nmbarang"]').val((data.nmbarang || '').trim());
        $('[name="spec"]').val((data.batch || '').trim());

        $('#qtystock_raw').val(data.sisaonhand || 0);

        setReadableNumber('#qtystock', data.sisaonhand || 0);
        setReadableNumber('#qty', data.sisaonhand || 0);

        // Init unit
        initSelect2Unit(data.idbarang);      // ← pakai ini OK (untuk input baru)

        $('#conversion_factor').val(1);
        $('#unit_default').val('');
        $('#qty_base').val('');
    });

    $('#idbarang').on('select2:opening', function (e) {
        var loc = $('#idlocation_from').val();
        if (!loc) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih Gudang Asal terlebih dahulu!'
            });
            return false;
        }
    });
}

/* ============================================================
   SELECT2 UNIT — SESUAI RESPONSE list_unit_item
   Field: idunit, conv_value, cdefault (YES/NO)
   ============================================================ */
function initSelect2Unit(idbarang, callback) {

    if (!idbarang) return;

    /* Destroy Select2 lama */
    if ($('#unit').data('select2')) {
        $('#unit').select2('destroy');
    }
    $('#unit').empty();

    /* Guard: callback hanya dipanggil 1x */
    var callbackFired = false;

    $('#unit').select2({
        placeholder: "Pilih Satuan...",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalDetailSPK'),
        minimumInputLength: 0,
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_unit_item',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term || '',
                    idbarang: idbarang
                };
            },
            processResults: function (data) {

                var items = data.items || [];

                var results = items.map(function (it) {
                    var conv      = parseFloat(it.conv_value || 1) || 1;
                    var isDefault = (it.cdefault || '').toUpperCase() === 'YES';

                    var lbl = it.idunit;
                    if (conv > 1) {
                        lbl += ' — 1 ' + it.idunit + ' = ' + conv + ' ' + (it.unit_default || '');
                    }
                    if (isDefault) {
                        lbl += ' ★';
                    }

                    return {
                        id:           it.idunit,
                        text:         lbl,
                        idunit:       it.idunit,
                        unit_default: it.unit_default || it.idunit,
                        conv_value:   conv,
                        cdefault:     isDefault ? 'YES' : 'NO'
                    };
                });

                /* ------------------------------------------------
                   Callback — HANYA 1X (saat pertama load)
                   ------------------------------------------------ */
                if (typeof callback === 'function' && !callbackFired) {
                    callbackFired = true;
                    callback(results);
                }

                /* Auto-select unit default (hanya kalau belum ada option) */
                if ($('#unit').find('option').length === 0) {

                    var defaultUnit = null;

                    if (results.length === 1) {
                        defaultUnit = results[0];
                    } else if (results.length > 1) {
                        defaultUnit = results.find(function (r) {
                            return r.cdefault === 'YES';
                        });
                    }

                    if (defaultUnit) {
                        setTimeout(function () {
                            if (!$('#unit').data('select2')) return;
                            if ($('#unit').find('option').length > 0) return;

                            var opt = new Option(
                                defaultUnit.idunit,
                                defaultUnit.idunit,
                                true,
                                true
                            );
                            $('#unit').append(opt).trigger('change');
                            setUnitConversion(defaultUnit);
                        }, 50);
                    }
                }

                return { results: results };
            },
            cache: false
        },
        templateResult: function (repo) {
            if (repo.loading) return repo.text;
            return repo.text;
        },
        templateSelection: function (repo) {
            return repo.idunit || repo.text;
        }
    }).on("select2:select", function (e) {
        setUnitConversion(e.params.data);
    });
}

/* ============================================================
   SELECT2 UNIT — PRELOAD (untuk mode EDIT)
   Ambil data unit dari API dulu, baru init Select2.
   Tidak ada race condition.
   ============================================================ */
function initSelect2UnitWithPreload(idbarang, selectedUnit, callback) {

    if (!idbarang) return;

    /* --------------------------------------------------------
       1. Fetch unit dari API
       -------------------------------------------------------- */
    $.ajax({
        url: HOST_URL + 'api/globalmodule/list_unit_item',
        type: 'POST',
        dataType: 'json',
        data: {
            _search_: '',
            idbarang: idbarang
        }
    })
        .done(function (data) {

            var items = data.items || [];

            /* ----------------------------------------------------
               2. Map hasil
               ---------------------------------------------------- */
            var results = items.map(function (it) {
                var conv      = parseFloat(it.conv_value || 1) || 1;
                var isDefault = (it.cdefault || '').toUpperCase() === 'YES';

                var lbl = it.idunit;
                if (conv > 1) {
                    lbl += ' — 1 ' + it.idunit + ' = ' + conv + ' ' + (it.unit_default || '');
                }
                if (isDefault) lbl += ' ★';

                return {
                    id:           it.idunit,
                    text:         lbl,
                    idunit:       it.idunit,
                    unit_default: it.unit_default || it.idunit,
                    conv_value:   conv,
                    cdefault:     isDefault ? 'YES' : 'NO'
                };
            });

            /* ----------------------------------------------------
               3. Destroy Select2 lama
               ---------------------------------------------------- */
            if ($('#unit').data('select2')) {
                $('#unit').select2('destroy');
            }
            $('#unit').empty();

            /* ----------------------------------------------------
               4. Init Select2 — pakai data lokal (bukan AJAX)
               ---------------------------------------------------- */
            $('#unit').select2({
                placeholder: "Pilih Satuan...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalDetailSPK'),
                data: results          /* ← preload data */
            })
                .on("select2:select", function (e) {
                    setUnitConversion(e.params.data);
                });

            /* ----------------------------------------------------
               5. Set selected unit
               ---------------------------------------------------- */
            var preSelected = null;

            /* 5a. Prioritas: selectedUnit dari TMP */
            if (selectedUnit) {
                preSelected = results.find(function (r) {
                    return r.idunit === selectedUnit;
                });
            }

            /* 5b. Fallback: unit default (cdefault=YES) */
            if (!preSelected) {
                preSelected = results.find(function (r) {
                    return r.cdefault === 'YES';
                });
            }

            /* 5c. Fallback: 1 unit saja */
            if (!preSelected && results.length === 1) {
                preSelected = results[0];
            }

            /* 5d. Set option & trigger */
            if (preSelected) {
                var opt = new Option(preSelected.idunit, preSelected.idunit, true, true);
                $('#unit').append(opt).trigger('change');

                /* Update data internal Select2 */
                $('#unit').val(preSelected.idunit).trigger('change');
            }

            /* ----------------------------------------------------
               6. Callback
               ---------------------------------------------------- */
            if (typeof callback === 'function') {
                callback(results, preSelected);
            }

        })
        .fail(function (xhr, status, error) {
            console.error('[initSelect2UnitWithPreload] Failed:', error);
            if (typeof callback === 'function') {
                callback([], null);
            }
        });
}

/* ============================================================
   HELPER — Set Konversi Setelah Unit Dipilih
   ============================================================ */
function setUnitConversion(dataUnit) {

    var conv        = parseFloat(dataUnit.conv_value || 1) || 1;
    var unitDefault = dataUnit.unit_default || dataUnit.idunit || '';

    /* ✅ ROUND conversion_factor */
    var convRounded = Math.round(conv);

    $('#conversion_factor').val(convRounded);
    $('#unit_default').val(unitDefault);

    /* Auto-fill qty */
    autoFillQtyFromStock();

    /* Hitung qty_base */
    hitungQtyBase();
}


/* ============================================================
   AUTO QTY — qty = qtystock / conversion_factor
   Dipanggil saat:
   - unit dipilih
   - satuan berubah
   - qtystock berubah
   ============================================================ */
function autoFillQtyFromStock() {

    // Ambil qtystock dalam satuan default (nilai mentah)
    var qtystock = parseFloat($('#qtystock_raw').val()) || 0;

    // Kalau #qtystock_raw kosong, fallback dari #qtystock (tampilan)
    if (qtystock === 0) {
        qtystock = parseFloat(convertToDbNumber($('#qtystock').val())) || 0;
    }

    // Ambil conversion factor
    var conv = parseFloat($('#conversion_factor').val()) || 1;
    if (conv <= 0) conv = 1;

    // Hitung qty otomatis
    var qty = qtystock / conv;

    // Set ke #qty dengan format readable
    setReadableNumber('#qty', qty);

    // Update qty_base juga
    hitungQtyBase();
}
/* ============================================================
   HELPER — Hitung qty_base = qty × conversion_factor
   ============================================================ */
function hitungQtyBase() {
    var qty  = parseFloat(convertToDbNumber($('#qty').val())) || 0;
    var conv = parseFloat($('#conversion_factor').val()) || 1;

    /* Round conv biar konsisten */
    conv = Math.round(conv);

    $('#qty_base').val(qty * conv);
}

$(document).on('input', '#qty', function () {
    hitungQtyBase();
});

/* ============================================================
   FORMAT REPO — Tampilan Item di Dropdown
   ============================================================ */
function formatBarang(repo) {
    if (repo.loading) {
        return '<div class="p-2 text-muted"><i class="fa fa-spinner fa-spin"></i> Memuat data...</div>';
    }

    if (!repo.idbarang) return repo.text;

    var stock = parseFloat(repo.sisaonhand || 0);
    var unit  = repo.unit || '';
    var batch = (repo.batch || '').trim();

    var stockText = stock.toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });

    var $line1 = $('<div>', {
        style: 'font-weight:600; color:#1f2937; margin-bottom:2px;'
    });
    $line1.append(document.createTextNode(repo.idbarang));
    $line1.append(
        '<i class="fa fa-circle-o" style="color:#000000; font-size:9px; margin:0 6px;"></i>'
    );
    $line1.append(document.createTextNode(repo.nmbarang || ''));

    var $line2 = $('<div>', {
        style: 'font-size:12px; color:#6b7280;'
    });

    var parts = [];

    if (batch && batch !== '-') {
        parts.push('Batch: <span style="color:#000000;">' + batch + '</span>');
    }

    parts.push(
        'Stock: <span style="color:#000000; font-weight:600;">' +
        stockText + ' ' + unit +
        '</span>'
    );

    $line2.html(parts.join(' <span style="color:#9c9c9c;">|</span> '));

    var $wrap = $('<div>', {
        class: 'select2-result-item',
        style: 'padding:4px 0; line-height:1.4;'
    });

    $wrap.append($line1).append($line2);
    return $wrap;
}

function formatBarangSelection(repo) {
    if (!repo.idbarang) return repo.text;
    return repo.idbarang + ' — ' + (repo.nmbarang || '');
}

/* ============================================================
   UTIL
   ============================================================ */
function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}

$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});

function editsearchitem(e) {
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Would be change? ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'master/item/edit' + '/?var=' + e);
        } else if (result.isDenied) {
            return false;
        }
    });
}

/* ============================================================
   TABLE DETAIL SPK TRANSFERS
   ============================================================ */
function tabletmpSPKDetail() {
    var initTable = function () {
        var table = $('#tmptabspktransfersdtl');
        table.DataTable({
            "processing": true,
            "serverSide": true,
            "language": languageDatatable(),
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
                "url": HOST_URL + 'persediaan/trans/list_tmp_transfer_location_dtl',
                "type": "POST",
                "data": function (data) {},
                "dataFilter": function (data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
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
                    "targets": [-1],
                    "orderable": false
                }
            ]
        });

        $('#tmptabspktransfersdtl tbody').on('click', 'tr', function (e) {
            if ($(e.target).is('input[type="checkbox"]')) return;
            var checkbox = $(this).find('input[type="checkbox"].row-check');
            checkbox.prop('checked', !checkbox.prop('checked'));
            checkbox.trigger('change');
        });
    };
    return initTable();
}

function reload_table_transfer_dtl() {
    var table = $('#tmptabspktransfersdtl');
    table.DataTable().ajax.reload();
}

$('#tmptabspktransfersdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;
    $('#tmptabspktransfersdtl tbody .row-check').prop('checked', checked);
});

$('#tmptabspktransfersdtl tbody').on('change', '.row-check', function () {
    const total = $('#tmptabspktransfersdtl tbody .row-check').length;
    const checked = $('#tmptabspktransfersdtl tbody .row-check:checked').length;
    $('#checkAll').prop('checked', total === checked);
});

$('#tmptabspktransfersdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});

function getSelectedPPDetail() {
    return $('#tmptabspktransfersdtl tbody .row-check:checked')
        .map(function () {
            return $(this).val();
        }).get();
}

function getCheckedDetailIds() {
    let ids = [];
    $('.row-check:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}

function setSelect2Ajax(selector, value, text) {
    if (!value) return;
    let option = new Option(text || value, value, true, true);
    $(selector).append(option).trigger('change');
}

/* ============================================================
   UPDATE DETAIL — MODAL EDIT
   ============================================================ */
/* ============================================================
   UPDATE DETAIL — MODAL EDIT
   ============================================================ */
function updateTransferLocationDetail() {

    const ids = getCheckedDetailIds();

    if (!ids || ids.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Peringatan',
            text: 'Pilih satu data yang akan diupdate' });
        return;
    }

    if (ids.length > 1) {
        Swal.fire({ icon: 'warning', title: 'Peringatan',
            text: 'Update hanya boleh satu data' });
        return;
    }

    const iduniq = ids[0];

    $.ajax({
        url: HOST_URL + 'persediaan/trans/get_tmp_transfer_location_dtl',
        type: 'GET',
        data: { id: iduniq },
        dataType: 'json',

        dataFilter: function (data) {

            var json;
            try {
                json = jQuery.parseJSON(data);
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error',
                    text: 'Response bukan JSON valid' });
                return null;
            }

            var payload = json.dataTables || json;

            if (!payload.status || !payload.items || payload.items.length === 0) {
                Swal.fire({ icon: 'error', title: 'Error',
                    text: 'Data tidak ditemukan' });
                return null;
            }

            var item = payload.items[0];

            /* STEP 1 — Field dasar */
            $('#iduniq').val(item.iduniq);
            $('#idurut').val(item.idurut);
            $('#description').val(item.description ?? '');
            $('#docno').val(item.docno ?? '');

            /* STEP 2 — Load barang */
            $.ajax({
                type: 'POST',
                url: HOST_URL + 'api/globalmodule/list_stock_by_location',
                data: {
                    _parameterx_ : item.idbarang,
                    loccode      : $('[name="idlocation_from"]').val(),
                    include_zero : 1,
                    _perpage_    : 100
                },
                dataType: 'json'
            })
                .done(function (datax) {

                    if (!datax.items || datax.items.length === 0) {
                        Swal.fire({ icon: 'warning', title: 'Peringatan',
                            text: 'Barang tidak ditemukan' });
                        return;
                    }

                    var itemBarang = datax.items.find(function (x) {
                        return x.idbarang === item.idbarang;
                    }) || datax.items[0];

                    /* STEP 3 — Set #idbarang */
                    if ($('#idbarang').hasClass('select2-hidden-accessible')) {
                        $('#idbarang').select2('destroy');
                    }
                    $('#idbarang').empty();

                    var optionBarang = new Option(
                        itemBarang.nmbarang,
                        itemBarang.idbarang,
                        true, true
                    );
                    $('#idbarang').append(optionBarang);

                    $('#idbarang').select2({
                        placeholder: 'Pilih Barang',
                        width: '100%',
                        allowClear: false,
                        dropdownParent: $('#modalDetailSPK')
                    });

                    $('#idbarang').trigger('change');
                    $('#nmbarang').val(itemBarang.nmbarang ?? '');

                    /* STEP 4 — Set qty & stock */
                    setReadableNumber('#qty',      item.qty      || 0);
                    setReadableNumber('#qtystock', item.qtystock || itemBarang.sisaonhand || 0);

                    /* STEP 5 — Show modal DULU */
                    $('#modalDetailPPLabel').text('Update Detail');
                    $('#modalDetailSPK').modal('show');

                    /* STEP 6 — Preload unit SETELAH modal show */
                    setTimeout(function () {

                        initSelect2UnitWithPreload(
                            itemBarang.idbarang,
                            item.unit,                    /* ← unit dari TMP */
                            function (results, preSelected) {

                                /* ------------------------------------------------
                                   Set field pendukung dari TMP
                                   ------------------------------------------------ */
                                $('#conversion_factor').val(
                                    Math.round(parseFloat(item.conversion_factor) || 1)
                                );
                                $('#unit_default').val(item.unit_default || '');
                                $('#qty_base').val(item.qty_base || '');

                                /* ------------------------------------------------
                                   Recalculate qty_base
                                   ------------------------------------------------ */
                                if (typeof hitungQtyBase === 'function') {
                                    hitungQtyBase();
                                }

                                /* ------------------------------------------------
                                   Trigger input untuk field numeric
                                   ------------------------------------------------ */
                                $('#conversion_factor').trigger('input');
                                $('#qty').trigger('input');

                                /* ------------------------------------------------
                                   Set #qtystock dari TMP
                                   ------------------------------------------------ */
                                setReadableNumber('#qtystock', item.qtystock || itemBarang.sisaonhand || 0);
                            }
                        );

                    }, 300);   /* tunggu modal render dulu */

                })
                .fail(function (xhr, status, error) {
                    console.error('[updateTransferLocationDetail] Failed:', error);
                    Swal.fire({ icon: 'error', title: 'Error',
                        text: 'Gagal memuat data barang' });
                });

            return data;
        }
    });
}

$('#checkAllDetail').on('change', function () {
    $('.row-check').prop('checked', this.checked);
});

$(document).on('change', '.row-check', function () {
    if (!this.checked) {
        $('#checkAllDetail').prop('checked', false);
    }
});

/* ============================================================
   FILTER (LIST) — IDBARANG_FILTER
   ============================================================ */
$('#btn-filter').click(function () {
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});

$('#btn-reset').click(function () {
    $('#form-filter')[0].reset();
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});

var defaultInitialItem = '';
$("#idbarang_filter").select2({
    placeholder: "Type/ Choose your item",
    allowClear: true,
    dropdownParent: $("#filter"),
    multiple: false,
    ajax: {
        url: HOST_URL + 'stock/balance/list_item',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _search_: params.term,
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialItem,
                _parameterx_: defaultInitialItem,
                term: params.term
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#idbarang_filter").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmbarang, data.items[0].idbarang, true, true);
                $('#idbarang_filter').append(option).trigger('change').select2("close");
                $('#idbarang_filter').trigger({
                    type: 'select2:select',
                    params: { data: data }
                });
            }
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
    templateResult: formatItemFilter,
    templateSelection: formatItemSelectionFilter
});

function formatItemFilter(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" + repo.idbarang + "   <i class='fa fa-circle-o'></i>   " + repo.nmbarang + "</div>";
}

function formatItemSelectionFilter(repo) {
    return repo.nmbarang || repo.text;
}

/* ============================================================
   SAVE DETAIL — TRANSFER STOCK
   ============================================================ */
function saveTransferStock() {

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan data Detail?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (!result.isConfirmed) return;

        /* ================================================
           AMBIL DATA BARANG
           ================================================ */

        /* 1. Ambil metadata dari Select2 */
        var dataBarang = null;

        if ($('#idbarang').data('select2')) {
            var selData = $('#idbarang').select2('data');
            if (selData && selData.length > 0) {
                dataBarang = selData[0];
            }
        }

        /* 2. idbarang — prioritas dari metadata Select2,
              fallback split by '|' ambil bagian pertama
              (karena .val() = concat idbarang||||idlocation) */
        var idbarang = '';

        if (dataBarang && dataBarang.idbarang) {
            idbarang = (dataBarang.idbarang || '').trim();
        } else {
            idbarang = ($('#idbarang').val() || '').trim().split('|')[0];
        }

        if (!idbarang) {
            Swal.fire({ icon: 'warning', title: 'Validasi',
                text: 'Pilih ID Barang terlebih dahulu!' });
            return;
        }

        /* Potong max 20 karakter */
        idbarang = idbarang.substring(0, 20);

        /* 3. nmbarang dari hidden input */
        var nmbarang = ($('#nmbarang').val() || '').trim();

        /* Fallback ke Select2 data */
        if (!nmbarang && dataBarang) {
            nmbarang = (dataBarang.nmbarang || dataBarang.text || '').trim();
        }

        /* Potong max 150 karakter */
        nmbarang = nmbarang.substring(0, 150);

        /* 4. Batch & Lotno */
        var batch = (dataBarang && dataBarang.batch) ? (dataBarang.batch || '').trim() : '';
        var lotno = (dataBarang && dataBarang.lotno) ? (dataBarang.lotno || '').trim() : '';

        /* ================================================
           AMBIL DATA UNIT
           ================================================ */

        if (!$('#unit').data('select2')) {
            Swal.fire({ icon: 'warning', title: 'Validasi',
                text: 'Unit belum dimuat. Pilih barang terlebih dahulu.' });
            return;
        }

        var selectedUnit = $('#unit').select2('data');

        if (!selectedUnit || selectedUnit.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Validasi',
                text: 'Pilih Satuan terlebih dahulu!' });
            return;
        }

        var unitData = selectedUnit[0];

        /* idunit dari .val() lebih reliable */
        var unit = ($('#unit').val() || unitData.id || unitData.idunit || '').trim();

        if (!unit) {
            Swal.fire({ icon: 'warning', title: 'Validasi',
                text: 'Satuan tidak valid!' });
            return;
        }

        /* Potong max 20 karakter */
        unit = unit.substring(0, 20);

        /* Conv factor */
        var cvRaw = unitData.conv_value;
        var cv    = parseFloat(cvRaw);
        var conversion_factor = (!isNaN(cv) && cv > 0) ? Math.round(cv) : 1;

        /* Unit default */
        var unit_default = (
            (unitData && unitData.unit_default)
            || (dataBarang && dataBarang.unit)
            || unit
        ).trim();

        /* Kalau ada karakter '|', ambil bagian pertama */
        if (unit_default.indexOf('|') >= 0) {
            unit_default = unit_default.split('|')[0].trim();
        }

        /* Potong max 20 karakter */
        unit_default = unit_default.substring(0, 20);

        /* ================================================
           QTY & STOCK
           ================================================ */
        let qtyInput   = $('#qty').val();
        let stockInput = $('#qtystock').val();

        let qty      = parseFloat(convertToDbNumber(qtyInput))   || 0;
        let qtystock = parseFloat(convertToDbNumber(stockInput)) || 0;

        if (qty <= 0) {
            Swal.fire({ icon: 'warning', title: 'Validasi',
                text: 'Qty harus lebih dari 0' });
            return;
        }

        if (qty > qtystock) {
            Swal.fire({ icon: 'warning', title: 'Stock Tidak Cukup',
                text: 'Qty tidak cukup (' + qtystock + ')' });
            return;
        }

        let qty_base = qty * conversion_factor;

        /* ================================================
           FORM DATA
           ================================================ */
        let formData = new FormData(document.getElementById('formSPKtransfersDtl'));

        formData.set('idbarang', idbarang);
        formData.set('nmbarang', nmbarang);
        formData.set('batch',    batch);
        formData.set('lotno',    lotno);

        formData.set('unit',              unit);
        formData.set('unit_default',      unit_default);
        formData.set('conversion_factor', conversion_factor);
        formData.set('qty',               qty);
        formData.set('qty_base',          qty_base);
        formData.set('qtystock',          qtystock);

        formData.set('cabang',             $('#cabang').val());
        formData.set('pemohon',            $('#pemohon').val());
        formData.set('idlocation_from',    $('#idlocation_from').val());
        formData.set('cabang_sent',        $('#cabang_sent').val());
        formData.set('idlocation_to',      $('#idlocation_to').val());
        formData.set('idlocation_transit', $('#idlocation_transit').val());
        formData.set('description',        $('#description').val());
        formData.set('docdate',            $('#docdate').val());
        formData.set('docref',             $('#docref').val());
        formData.set('iduniq',             $('#iduniq').val());

        formData.set(
            'docno',
            $('#prefix').val() + '/' +
            $('#infix').val() + '/' +
            $('#suffix').val()
        );

        /* Debug */
        console.log('=== SAVE TRANSFER STOCK ===');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        /* ================================================
           AJAX
           ================================================ */
        $.ajax({
            url: HOST_URL + 'persediaan/trans/saveTransferLocationDetail',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (res) {

                if (res.success) {

                    if (res.reload === true) {
                        window.location.reload();
                        return;
                    }

                    $('#modalDetailSPK').modal('hide');
                    reload_table_transfer_dtl();
                    $('#formSPKtransfersDtl')[0].reset();

                    $('#idbarang').val(null).trigger('change');

                    if ($('#unit').data('select2')) {
                        $('#unit').select2('destroy');
                    }
                    $('#unit').empty();

                } else {
                    Swal.fire({ icon: 'warning', title: 'Gagal',
                        text: res.message || 'Gagal menyimpan data' });
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({ icon: 'error', title: 'Server Error',
                    text: 'Terjadi kesalahan server' });
            }
        });

    });
}
/* ============================================================
   VALIDASI BUTTON TAMBAH DETAIL
   ============================================================ */
$('#btnAddDetail').on('click', function (e) {

    var cabang             = $.trim($('[name="cabang"]').val());
    var docdate            = $.trim($('[name="docdate"]').val());
    var prefix             = $.trim($('[name="prefix"]').val());
    var infix              = $.trim($('[name="infix"]').val());
    var suffix             = $.trim($('[name="suffix"]').val());
    var idlocation_from    = $.trim($('[name="idlocation_from"]').val());
    var idlocation_to      = $.trim($('[name="idlocation_to"]').val());

    function showWarning(msg) {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            text: msg,
            confirmButtonColor: '#3085d6'
        });
    }

    if (!cabang) {
        e.preventDefault();
        showWarning('Cabang / Job belum dipilih!');
        $('[name="cabang"]').focus();
        return false;
    }

    if (!prefix || !infix || !suffix) {
        e.preventDefault();
        showWarning('Nomor Jurnal (prefix/infix/suffix) belum lengkap!');
        return false;
    }

    if (!docdate) {
        e.preventDefault();
        showWarning('Tanggal Transfer belum diisi!');
        $('[name="docdate"]').focus();
        return false;
    }

    if (!idlocation_from) {
        e.preventDefault();
        showWarning('Gudang Asal belum dipilih!');
        $('[name="idlocation_from"]').focus();
        return false;
    }

    if (!idlocation_to) {
        e.preventDefault();
        showWarning('Gudang Tujuan belum dipilih!');
        $('[name="idlocation_to"]').focus();
        return false;
    }

    if (idlocation_from === idlocation_to) {
        e.preventDefault();
        showWarning('Gudang Asal dan Gudang Tujuan tidak boleh sama!');
        $('[name="idlocation_to"]').focus();
        return false;
    }

    btnInputDetail();
});

/* ============================================================
   BUKA MODAL INPUT DETAIL
   ============================================================ */
function btnInputDetail() {
    $('#formSPKtransfersDtl')[0].reset();
    $('#idbarang').val(null).trigger('change');

    if ($('#unit').data('select2')) {
        $('#unit').select2('destroy');
    }
    $('#unit').empty();

    $('#idurut').val('');
    $('#conversion_factor').val(1);
    $('#unit_default').val('');
    $('#qty_base').val('');

    $('#modalDetailPPLabel').text('Tambah Item Detail');
    $('#modalDetailSPK').modal('show');
}

/* ============================================================
   DOCNO GENERATOR
   ============================================================ */
let currentKodeSuffix = '';

function generateDocNumber(prefix, infix, suffix) {
    if (!prefix || !infix || !suffix) return;
    $('#docno').val(prefix + '/' + infix + '/' + suffix);
}

$('#cabang').on('change', function () {

    if (skipRoleChange) return;

    let idbranch = $(this).val();
    if (!idbranch) return;

    $.ajax({
        url: HOST_URL + '/persediaan/trans/getBranchInfoStockTransfers',
        method: 'GET',
        data: { idbranch: idbranch },
        dataType: 'json',
        success: function (res) {

            if (!res.success) {
                Swal.fire('Error', res.message, 'warning');
                return;
            }

            currentKodeSuffix = res.kode_suffix;

            $('#infix').val(res.infix);
            var prefix = res.prefix;
            $('#prefix').val(prefix);
            loadNextSuffixST();

            var infix = (res.infix || '').toString();

            if (infix.length === 4) {
                $('#docdate').prop('disabled', false);
                var yy = infix.substring(0, 2);
                var mm = infix.substring(2, 4);
                var year = 2000 + parseInt(yy, 10);
                var month = parseInt(mm, 10) - 1;

                var logindate = res.logindate ? moment(res.logindate, 'DD-MM-YYYY') : moment();
                var startDate = moment([year, month, 1]);
                var endDate = moment(startDate).endOf('month');

                var selectedDate = logindate.isBetween(startDate, endDate, 'day', '[]')
                    ? logindate
                    : startDate;

                var $el = $('#docdate');
                var drp = $el.data('daterangepicker');

                if (drp) {
                    drp.minDate = startDate;
                    drp.maxDate = endDate;
                    drp.setStartDate(selectedDate);
                    drp.setEndDate(selectedDate);
                } else {
                    $el.daterangepicker({
                        autoUpdateInput: false,
                        singleDatePicker: true,
                        showDropdowns: true,
                        startDate: selectedDate,
                        minDate: startDate,
                        maxDate: endDate,
                        locale: { format: 'DD-MM-YYYY' },
                        cancelLabel: 'Clear'
                    });
                    $el.on('apply.daterangepicker', function (ev, picker) {
                        $(this).val(picker.startDate.format('DD-MM-YYYY'));
                        $(this).trigger('change');
                    });
                    $el.on('cancel.daterangepicker', function (ev, picker) {
                        $(this).val('');
                        $(this).trigger('change');
                    });
                }

                $el.val(selectedDate.format('DD-MM-YYYY'));
            }

            $('#docno').val(
                prefix + '/' + res.infix + '/' + currentKodeSuffix + '0001'
            );
        }
    });
});

function loadNextSuffixST() {

    let prefix = $.trim($('#prefix').val()).toUpperCase();
    let infix = $.trim($('#infix').val());

    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + '/persediaan/trans/getNextSuffixStockTransfers',
        method: 'GET',
        data: {
            prefix: prefix,
            infix: infix,
            kode_suffix: currentKodeSuffix
        },
        dataType: 'json',
        success: function (res) {

            if (!res.success) {
                Swal.fire('Error', res.message, 'warning');
                return;
            }

            let suffix = $.trim(res.suffix || '');
            $('#suffix').val(suffix).trigger('change');

            $('#docno').val(prefix + '/' + infix + '/' + res.suffix);
        }
    });
}

/* ============================================================
   SELECT2 BRANCH — CABANG
   ============================================================ */
var defaultInitialBranch = '';

$("#cabang").select2({
    placeholder: "Type/ Choose your Branch",
    allowClear: true,
    width: '100%',
    multiple: false,
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
                _perpage_: 2,
                _paramglobal_: defaultInitialBranch,
                _parameterx_: defaultInitialBranch,
                term: params.term
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#cabang").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmbranch, data.items[0].idbranch, true, true);
                $('#cabang').append(option).trigger('change').select2("close");
                $('#cabang').trigger({
                    type: 'select2:select',
                    params: { data: data }
                });
            }
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: { more: (params.page * 30) < data.total_count }
            };
        },
        cache: false
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: formatBranch,
    templateSelection: formatBranchSelection
});

function formatBranch(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" + repo.idbranch + "   <i class='fa fa-circle-o'></i>   " + repo.nmbranch + "</div>";
}

function formatBranchSelection(repo) {
    return repo.nmbranch || repo.text;
}

/* ============================================================
   SELECT2 BRANCH — CABANG_SENT
   ============================================================ */
var defaultInitialCabangSent = '';

$("#cabang_sent").select2({
    placeholder: "Type/ Choose your Branch",
    allowClear: true,
    width: '100%',
    multiple: false,
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
                _perpage_: 2,
                _paramglobal_: defaultInitialBranch,
                _parameterx_: defaultInitialBranch,
                term: params.term
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#cabang_sent").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmbranch, data.items[0].idbranch, true, true);
                $('#cabang_sent').append(option).trigger('change').select2("close");
                $('#cabang_sent').trigger({
                    type: 'select2:select',
                    params: { data: data }
                });
            }
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: { more: (params.page * 30) < data.total_count }
            };
        },
        cache: false
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: formatBranch,
    templateSelection: formatBranchSelection
});

/* ============================================================
   SELECT2 LOCATION — FROM / TO / TRANSIT
   ============================================================ */
var defaultInitialLocationFrom = '';

$("#idlocation_from").select2({
    placeholder: " -- Pilih Gudang Asal -- ",
    allowClear: true,
    multiple: false,
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
                _perpage_: 2,
                _paramglobal_: defaultInitialLocationFrom,
                _parameterx_: defaultInitialLocationFrom,
                term: params.term
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: { more: (params.page * 30) < data.total_count }
            };
        },
        cache: true
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: formatLocation,
    templateSelection: formatLocationSelection
});

var defaultInitialLocationTo = '';

$("#idlocation_to").select2({
    placeholder: " -- Pilih Gudang Tujuan -- ",
    allowClear: true,
    multiple: false,
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
                _perpage_: 2,
                _paramglobal_: defaultInitialLocationTo,
                _parameterx_: defaultInitialLocationTo,
                term: params.term
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: { more: (params.page * 30) < data.total_count }
            };
        },
        cache: true
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: formatLocation,
    templateSelection: formatLocationSelection
});

var defaultInitialLocationTransit = '';

$("#idlocation_transit").select2({
    placeholder: " -- Pilih Gudang Transit -- ",
    allowClear: true,
    multiple: false,
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
                _perpage_: 2,
                _paramglobal_: defaultInitialLocationTransit,
                _parameterx_: defaultInitialLocationTransit,
                term: params.term
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: { more: (params.page * 30) < data.total_count }
            };
        },
        cache: true
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: formatLocation,
    templateSelection: formatLocationSelection
});

function formatLocation(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" + repo.idlocation + "   <i class='fa fa-circle-o'></i>   " + repo.nmlocation + "</div>";
}

function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}

/* ============================================================
   DELETE DETAIL
   ============================================================ */
function btnDeleteDetail() {

    const ids = getCheckedDetailIds();

    if (!ids || ids.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih minimal satu data yang akan dihapus' });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Data yang dihapus tidak dapat dikembalikan. Lanjutkan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'persediaan/trans/deleteSPKTransferDetail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data berhasil dihapus',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    reload_table_transfer_dtl();
                } else {
                    Swal.fire({ icon: 'warning', title: 'Gagal', text: res.message || 'Gagal menghapus data' });
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({ icon: 'error', title: 'Server Error', text: 'Terjadi kesalahan server' });
            }
        });
    });
}

/* ============================================================
   INIT ON READY
   ============================================================ */
$(document).ready(function () {

    tableTrxTransfersLocation();
    tabletmpSPKDetail();

    initSelect2Barang();

    var docno = $('[name="docno"]').val();
    if (docno && docno.trim() !== '') {
        documentReadable();
    }

    $("#loadMe").modal("hide");
});