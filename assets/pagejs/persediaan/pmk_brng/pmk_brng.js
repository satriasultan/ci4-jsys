/*
pmk_brg.js
Versi Final - Field mengikuti Stock Transfer Location
Header : cabang, prefix/infix/suffix, docdate, idcostcenter, keterangan
Detail : idlocation_dtl + barang + spec + qtystock + unit + konversi + qty
*/

var save_method;
var table;
var initTable;

/* ============================================================
   LIST PMK BRG — HEADER
   ============================================================ */
function table_trx_pmk_brng_mst() {
    var initTable = function () {
        var table = $('#tPmkBrg');
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
                "url": HOST_URL + 'persediaan/trans/list_trx_pmk_brng_mst',
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

function reloadPmkBrg() {
    var table = $('#tPmkBrg');
    table.DataTable().ajax.reload();
}

$('#btn-filter-tx').click(function () {
    var table = $('#tPmkBrg');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});

$('#btn-reset-tx').click(function () {
    $('#form-filter')[0].reset();
    var table = $('#tPmkBrg');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});

let skipRoleChange = false;

/* ============================================================
   DOCUMENT READABLE — EDIT HEADER
   ============================================================ */
function documentReadable() {

    var docno = $('[name="docno"]').val();

    if (!docno || docno.trim() === '') {
        console.log("Input baru — skip documentReadable");
        return;
    }

    $.getJSON(HOST_URL + 'persediaan/trans/showing_pmk_brng_mst_tmp', { docno: docno })
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
            $('[name="docref"]').val(item.docref).prop('disabled', false);
            $('[name="keterangan"]').val(item.description);

            skipRoleChange = true;

            const branch1 = $.getJSON(HOST_URL + 'api/globalmodule/list_branchjob', { var: item.cabang });
            const cc      = $.getJSON(HOST_URL + 'api/globalmodule/list_costcenter', { var: item.idcostcenter });

            $.when(branch1, cc).done(function (b1, c1) {

                setSelect2('[name="cabang"]',       b1[0].items[0], 'nmbranch',     'idbranch',     true);
                setSelect2('[name="idcostcenter"]', c1[0].items[0], 'nmcostcenter', 'idcostcenter', true);
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
   SELECT2 BARANG — BERDASARKAN GUDANG (idlocation_dtl)
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
        dropdownParent: $('#modalDetailPMK'),
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
                    loccode:      $('#idlocation_dtl').val() || '',
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

        $('[name="nmbarang"]').val((data.nmbarang || '').trim());
        $('[name="spec"]').val((data.batch || '').trim());

        $('#qtystock_raw').val(data.sisaonhand || 0);

        setReadableNumber('#qtystock', data.sisaonhand || 0);
        setReadableNumber('#qty', data.sisaonhand || 0);

        initSelect2Unit(data.idbarang);

        $('#conversion_factor').val(1);
        $('#unit_default').val('');
        $('#qty_base').val('');
    });

    $('#idbarang').on('select2:opening', function (e) {
        var loc = $('#idlocation_dtl').val();
        if (!loc) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih Gudang terlebih dahulu!'
            });
            return false;
        }
    });
}

/* ============================================================
   SELECT2 UNIT
   ============================================================ */
function initSelect2Unit(idbarang, callback) {

    if (!idbarang) return;

    if ($('#unit').data('select2')) {
        $('#unit').select2('destroy');
    }
    $('#unit').empty();

    var callbackFired = false;

    $('#unit').select2({
        placeholder: "Pilih Satuan...",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalDetailPMK'),
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

                if (typeof callback === 'function' && !callbackFired) {
                    callbackFired = true;
                    callback(results);
                }

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
   SELECT2 UNIT — PRELOAD (MODE EDIT)
   ============================================================ */
function initSelect2UnitWithPreload(idbarang, selectedUnit, callback) {

    if (!idbarang) return;

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

            if ($('#unit').data('select2')) {
                $('#unit').select2('destroy');
            }
            $('#unit').empty();

            $('#unit').select2({
                placeholder: "Pilih Satuan...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalDetailPMK'),
                data: results
            })
                .on("select2:select", function (e) {
                    setUnitConversion(e.params.data);
                });

            var preSelected = null;

            if (selectedUnit) {
                preSelected = results.find(function (r) {
                    return r.idunit === selectedUnit;
                });
            }

            if (!preSelected) {
                preSelected = results.find(function (r) {
                    return r.cdefault === 'YES';
                });
            }

            if (!preSelected && results.length === 1) {
                preSelected = results[0];
            }

            if (preSelected) {
                var opt = new Option(preSelected.idunit, preSelected.idunit, true, true);
                $('#unit').append(opt).trigger('change');
                $('#unit').val(preSelected.idunit).trigger('change');
            }

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
   HELPER — SET KONVERSI
   ============================================================ */
function setUnitConversion(dataUnit) {

    var conv        = parseFloat(dataUnit.conv_value || 1) || 1;
    var unitDefault = dataUnit.unit_default || dataUnit.idunit || '';

    var convRounded = Math.round(conv);

    $('#conversion_factor').val(convRounded);
    $('#unit_default').val(unitDefault);

    autoFillQtyFromStock();
    hitungQtyBase();
}

/* ============================================================
   AUTO QTY — qty = qtystock / conversion_factor
   ============================================================ */
function autoFillQtyFromStock() {

    var qtystock = parseFloat($('#qtystock_raw').val()) || 0;

    if (qtystock === 0) {
        qtystock = parseFloat(convertToDbNumber($('#qtystock').val())) || 0;
    }

    var conv = parseFloat($('#conversion_factor').val()) || 1;
    if (conv <= 0) conv = 1;

    var qty = qtystock / conv;

    setReadableNumber('#qty', qty);

    hitungQtyBase();
}

/* ============================================================
   HITUNG qty_base = qty × conversion_factor
   ============================================================ */
function hitungQtyBase() {
    var qty  = parseFloat(convertToDbNumber($('#qty').val())) || 0;
    var conv = parseFloat($('#conversion_factor').val()) || 1;

    conv = Math.round(conv);

    $('#qty_base').val(qty * conv);
}

$(document).on('input', '#qty', function () {
    hitungQtyBase();
});

/* ============================================================
   FORMAT DROPDOWN BARANG
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
   TABLE DETAIL PMK
   ============================================================ */
function tabletmpSPKDetail() {
    var initTable = function () {
        var table = $('#tmptabpmksdtl');
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
                "url": HOST_URL + 'persediaan/trans/list_tmp_pmk_brng_dtl',
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

        $('#tmptabpmksdtl tbody').on('click', 'tr', function (e) {
            if ($(e.target).is('input[type="checkbox"]')) return;
            var checkbox = $(this).find('input[type="checkbox"].row-check');
            checkbox.prop('checked', !checkbox.prop('checked'));
            checkbox.trigger('change');
        });
    };
    return initTable();
}

function reload_pemakaian_dtl() {
    var table = $('#tmptabpmksdtl');
    table.DataTable().ajax.reload();
}

$('#tmptabpmksdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;
    $('#tmptabpmksdtl tbody .row-check').prop('checked', checked);
});

$('#tmptabpmksdtl tbody').on('change', '.row-check', function () {
    const total = $('#tmptabpmksdtl tbody .row-check').length;
    const checked = $('#tmptabpmksdtl tbody .row-check:checked').length;
    $('#checkAll').prop('checked', total === checked);
});

$('#tmptabpmksdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});

function getSelectedPPDetail() {
    return $('#tmptabpmksdtl tbody .row-check:checked')
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
function updatePmkBrgDtl() {
    isLoadingUpdate = true;
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
        url: HOST_URL + 'persediaan/trans/get_tmp_pmk_brng_dtl',
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

            /* STEP 2 — Load lokasi (gudang) */
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mlocation',
                data: { var: item.idlocation },
                dataType: 'json'
            })
                .then(function (resLocation) {

                    if (!resLocation.items || resLocation.items.length === 0) return;

                    let loc = resLocation.items[0];
                    loc.idlocation = (loc.idlocation || '').trim();

                    if ($('#idlocation_dtl').hasClass('select2-hidden-accessible')) {
                        $('#idlocation_dtl').select2('destroy');
                    }
                    $('#idlocation_dtl').empty();

                    var optionLoc = new Option(loc.nmlocation, loc.idlocation, true, true);
                    $('#idlocation_dtl').append(optionLoc).trigger('change');

                    $('#idlocation_dtl').select2({
                        placeholder: 'Pilih Gudang',
                        width: '100%',
                        allowClear: false,
                        dropdownParent: $('#modalDetailPMK')
                    });

                    /* STEP 3 — Load barang */
                    return $.ajax({
                        type: 'POST',
                        url: HOST_URL + 'api/globalmodule/list_stock_by_location',
                        data: {
                            _parameterx_ : item.idbarang,
                            loccode      : loc.idlocation,
                            include_zero : 1,
                            _perpage_    : 100
                        },
                        dataType: 'json'
                    });

                })
                .then(function (datax) {

                    if (!datax.items || datax.items.length === 0) {
                        Swal.fire({ icon: 'warning', title: 'Peringatan',
                            text: 'Barang tidak ditemukan' });
                        return;
                    }

                    var itemBarang = datax.items.find(function (x) {
                        return x.idbarang === item.idbarang;
                    }) || datax.items[0];

                    if ($('#idbarang').hasClass('select2-hidden-accessible')) {
                        $('#idbarang').select2('destroy');
                    }
                    $('#idbarang').empty();

                    var optionBarang = new Option(itemBarang.nmbarang, itemBarang.idbarang, true, true);
                    $('#idbarang').append(optionBarang);

                    $('#idbarang').select2({
                        placeholder: 'Pilih Barang',
                        width: '100%',
                        allowClear: false,
                        dropdownParent: $('#modalDetailPMK')
                    });

                    $('#idbarang').trigger('change');
                    $('#nmbarang').val(itemBarang.nmbarang ?? '');
                    $('#spec').val(itemBarang.batch ?? '');

                    setReadableNumber('#qty',      item.qty      || 0);
                    setReadableNumber('#qtystock', item.qtystock || itemBarang.sisaonhand || 0);

                    $('#modalDetailPemakaianBarang').text('Update Detail');
                    $('#modalDetailPMK').modal('show');

                    setTimeout(function () {

                        initSelect2UnitWithPreload(
                            itemBarang.idbarang,
                            item.unit,
                            function (results, preSelected) {

                                $('#conversion_factor').val(
                                    Math.round(parseFloat(item.conversion_factor) || 1)
                                );
                                $('#unit_default').val(item.unit_default || '');
                                $('#qty_base').val(item.qty_base || '');

                                if (typeof hitungQtyBase === 'function') {
                                    hitungQtyBase();
                                }

                                $('#conversion_factor').trigger('input');
                                $('#qty').trigger('input');

                                setReadableNumber('#qtystock', item.qtystock || itemBarang.sisaonhand || 0);
                            }
                        );

                    }, 300);

                })
                .fail(function (xhr, status, error) {
                    console.error('[updatePmkBrgDtl] Failed:', error);
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
   FILTER LIST
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

/* ============================================================
   SAVE DETAIL — PMK BRG
   ============================================================ */
function savePemakaianBrng() {

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
           VALIDASI GUDANG
           ================================================ */
        var idlocation_dtl = ($('#idlocation_dtl').val() || '').trim();
        if (!idlocation_dtl) {
            Swal.fire({ icon: 'warning', title: 'Validasi',
                text: 'Pilih Gudang terlebih dahulu!' });
            return;
        }

        /* ================================================
           AMBIL DATA BARANG
           ================================================ */
        var dataBarang = null;

        if ($('#idbarang').data('select2')) {
            var selData = $('#idbarang').select2('data');
            if (selData && selData.length > 0) {
                dataBarang = selData[0];
            }
        }

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

        idbarang = idbarang.substring(0, 20);

        var nmbarang = ($('#nmbarang').val() || '').trim();
        if (!nmbarang && dataBarang) {
            nmbarang = (dataBarang.nmbarang || dataBarang.text || '').trim();
        }
        nmbarang = nmbarang.substring(0, 150);

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

        var unit = ($('#unit').val() || unitData.id || unitData.idunit || '').trim();

        if (!unit) {
            Swal.fire({ icon: 'warning', title: 'Validasi',
                text: 'Satuan tidak valid!' });
            return;
        }

        unit = unit.substring(0, 20);

        var cvRaw = unitData.conv_value;
        var cv    = parseFloat(cvRaw);
        var conversion_factor = (!isNaN(cv) && cv > 0) ? Math.round(cv) : 1;

        var unit_default = (
            (unitData && unitData.unit_default)
            || (dataBarang && dataBarang.unit)
            || unit
        ).trim();

        if (unit_default.indexOf('|') >= 0) {
            unit_default = unit_default.split('|')[0].trim();
        }

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
        let formData = new FormData(document.getElementById('formPemakaianStockDtl'));

        formData.set('idlocation_dtl', idlocation_dtl);

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

        formData.set('cabang',       $('#cabang').val());
        formData.set('idcostcenter', $('#idcostcenter').val());
        formData.set('description',  $('#description').val());
        formData.set('docdate',      $('#docdate').val());
        formData.set('docref',       $('#docref').val());
        formData.set('iduniq',       $('#iduniq').val());

        formData.set(
            'docno',
            $('#prefix').val() + '/' +
            $('#infix').val() + '/' +
            $('#suffix').val()
        );

        console.log('=== SAVE PMK BRG ===');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        /* ================================================
           AJAX
           ================================================ */
        $.ajax({
            url: HOST_URL + 'persediaan/trans/save_pmk_brng_detail',
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

                    $('#modalDetailPMK').modal('hide');
                    reload_pemakaian_dtl();
                    $('#formPemakaianStockDtl')[0].reset();

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

    var cabang       = $.trim($('[name="cabang"]').val());
    var docdate      = $.trim($('[name="docdate"]').val());
    var prefix       = $.trim($('[name="prefix"]').val());
    var infix        = $.trim($('[name="infix"]').val());
    var suffix       = $.trim($('[name="suffix"]').val());
    var idcostcenter = $.trim($('[name="idcostcenter"]').val());

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
        showWarning('Tanggal belum diisi!');
        $('[name="docdate"]').focus();
        return false;
    }

    if (!idcostcenter) {
        e.preventDefault();
        showWarning('Untuk Bagian / Cost Center belum dipilih!');
        $('[name="idcostcenter"]').focus();
        return false;
    }

    btnInputDetail();
});

/* ============================================================
   BUKA MODAL INPUT DETAIL
   ============================================================ */
function btnInputDetail() {
    $('#formPemakaianStockDtl')[0].reset();

    /* Reset gudang */
    if ($('#idlocation_dtl').data('select2')) {
        $('#idlocation_dtl').select2('destroy');
    }
    $('#idlocation_dtl').empty();
    initSelect2LocationDtl();

    /* Reset barang */
    $('#idbarang').val(null).trigger('change');

    /* Reset unit */
    if ($('#unit').data('select2')) {
        $('#unit').select2('destroy');
    }
    $('#unit').empty();

    $('#idurut').val('');
    $('#conversion_factor').val(1);
    $('#unit_default').val('');
    $('#qty_base').val('');

    $('#modalDetailPemakaianBarang').text('Tambah Item Detail');
    $('#modalDetailPMK').modal('show');
}

/* ============================================================
   SELECT2 LOCATION — GUDANG DETAIL
   ============================================================ */
var defaultInitialLocationDtl = '';

function initSelect2LocationDtl() {

    if ($('#idlocation_dtl').data('select2')) {
        $('#idlocation_dtl').select2('destroy');
    }

    $('#idlocation_dtl').select2({
        placeholder: " -- Pilih Gudang -- ",
        allowClear: true,
        width: '100%',
        multiple: false,
        dropdownParent: $('#modalDetailPMK'),
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
                    _paramglobal_: defaultInitialLocationDtl,
                    _parameterx_: defaultInitialLocationDtl,
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
    })
        .on("change", function () {

            var val = $(this).val();

            /* Reset barang */
            $('#idbarang').val(null).trigger('change');

            $('[name="nmbarang"]').val('');
            $('[name="spec"]').val('');
            $('[name="qtystock"]').val(0);

            $('#qtystock_raw').val(0);

            if ($('#unit').data('select2')) {
                $('#unit').select2('destroy');
            }
            $('#unit').empty();
        });
}

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
        Swal.fire({ icon: 'warning', title: 'Peringatan',
            text: 'Pilih minimal satu data yang akan dihapus' });
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
            url: HOST_URL + 'persediaan/trans/delete_pmk_brng',
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
                    reload_pemakaian_dtl();
                } else {
                    Swal.fire({ icon: 'warning', title: 'Gagal',
                        text: res.message || 'Gagal menghapus data' });
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
        url: HOST_URL + '/persediaan/trans/getBranch_ajustment_stock',
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
            loadNextSuffixPMK();

            var infix = (res.infix || '').toString();

            if (infix.length === 4) {
                $('#docdate').prop('disabled', false);

                var yy = infix.substring(0, 2);
                var mm = infix.substring(2, 4);
                var year  = 2000 + parseInt(yy, 10);
                var month = parseInt(mm, 10) - 1;

                var logindate = res.logindate ? moment(res.logindate, 'DD-MM-YYYY') : moment();
                var startDate = moment([year, month, 1]);
                var endDate   = moment(startDate).endOf('month');

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

function loadNextSuffixPMK() {

    let prefix = $.trim($('#prefix').val()).toUpperCase();
    let infix  = $.trim($('#infix').val());

    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + '/persediaan/trans/getNextSuffix_ajustment_stock',
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

$('#prefix').on('blur', function () {
    loadNextSuffixPMK();
});

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
   SELECT2 COST CENTER — UNTUK BAGIAN
   ============================================================ */
var defaultInitialCostcenter = '';

$("#idcostcenter").select2({
    placeholder: " -- Pilih Bagian/Costcenter -- ",
    allowClear: true,
    width: '100%',
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_costcenter',
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
                _paramglobal_: defaultInitialCostcenter,
                _parameterx_: defaultInitialCostcenter,
                term: params.term
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#idcostcenter").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmcostcenter, data.items[0].idcostcenter, true, true);
                $('#idcostcenter').append(option).trigger('change').select2("close");
                $('#idcostcenter').trigger({
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
        cache: true
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: formatCostcenter,
    templateSelection: formatCostcenterSelection
});

function formatCostcenter(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" + repo.idcostcenter + "   <i class='fa fa-circle-o'></i>   " + repo.nmcostcenter + "</div>";
}

function formatCostcenterSelection(repo) {
    return repo.nmcostcenter || repo.text;
}

/* ============================================================
   OPEN SCANNER
   ============================================================ */
function open_scan() {
    read_qrcode();
    $('#open_scan').modal('show');
    $('.modal-title').text('Open Scanner');
}

function read_qrcode() {
    function onScanSuccess(decodedText, decodedResult) {
        play();
        $('#searchitem').val(decodedText);
        $('#open_scan').modal('hide');
        var table = $('#tsearchitem');
        table.DataTable().ajax.reload();
        html5QrcodeScanner.clear();
    }

    var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
}

var audio = document.getElementById('chatAudio');

function play() {
    audio.play();
}

/* ============================================================
   INIT ON READY
   ============================================================ */
$(document).ready(function () {

    /* Fix SweetAlert focus */
    if ($.fn.modal) {
        $.fn.modal.Constructor.prototype._enforceFocus = function () {};
    }

    table_trx_pmk_brng_mst();
    tabletmpSPKDetail();

    initSelect2Barang();
    initSelect2LocationDtl();

    var docno = $('[name="docno"]').val();
    if (docno && docno.trim() !== '') {
        documentReadable();
    }

    $("#loadMe").modal("hide");
});