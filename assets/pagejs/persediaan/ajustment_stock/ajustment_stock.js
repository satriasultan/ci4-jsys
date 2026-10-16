/*
 * ajustment_stock.js
 * Created by PhpStorm.
 * User: FIKY-PC
 * Date: 12/2/20, 2:32 PM
 * Last Modified: 12/2/20, 2:32 PM
 * Developed By: Fiky Ashariza Powered By PhpStorm
 * Copyright © 2020. All rights reserved.
 *
 * ---------------------------------------------------------------
 * REVISI:
 * - Fix handler #idbarang agar selalu trigger getSisaStock()
 * - Fix autoFillHargaPersediaan() agar tetap jalan walau stok 0
 * - Fix reset Select2 pakai .val(null).trigger('change.select2')
 * - Fix updateAjustmentStockDetail() agar trigger change dengan benar
 * ---------------------------------------------------------------
 */

var save_method; // for save method string
var table;
var initTable;

/* ============================================================
 * VIEW UTAMA
 * ============================================================ */
function table_trx_ajustment_stock_mst() {
    var initTable = function () {
        var table = $('#tTrxAjustmentStock');
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
                "url": HOST_URL + 'persediaan/trans/list_trx_ajustment_stock_mst',
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
    var table = $('#tTrxAjustmentStock');
    table.DataTable().ajax.reload();
}

$('#btn-filter-tx').click(function () {
    var table = $('#tTrxAjustmentStock');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});

$('#btn-reset-tx').click(function () {
    $('#form-filter')[0].reset();
    var table = $('#tTrxAjustmentStock');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});

let skipRoleChange = false;

/* ============================================================
 * EDIT / READ DATA MASTER
 * ============================================================ */
function documentReadable() {
    var docno = $('[name="docno"]').val();

    $.getJSON(HOST_URL + 'persediaan/trans/showing_ajustment_stock_mst_tmp', { docno: docno })
        .done(function (response) {

            if (!response.dataTables || !response.dataTables.items.length) {
                hideLoader();
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
            const branch2 = $.getJSON(HOST_URL + 'api/globalmodule/list_branchjob', { var: item.cabang_sent });
            const locFrom = $.getJSON(HOST_URL + 'api/globalmodule/list_mlocation', { var: item.idlocation_dtl });
            const locTo = $.getJSON(HOST_URL + 'api/globalmodule/list_mlocation', { var: item.idlocation_to });
            const locTransit = $.getJSON(HOST_URL + 'api/globalmodule/list_mlocation', { var: item.idlocation_transit });

            $.when(branch1, branch2, locFrom, locTo, locTransit)
                .done(function (b1, b2, l1, l2, l3) {
                    setSelect2('[name="cabang"]', b1[0].items[0], 'nmbranch', 'idbranch');
                    hideLoader();
                });

            hideLoader();
        })
        .fail(function () {
            console.log("Failed To Loading Data");
            hideLoader();
        })
        .always(function () {
            hideLoader();
        });

    hideLoader();
}

/* ============================================================
 * HELPER: set Select2 value (readonly)
 * ============================================================ */
function setSelect2(selector, data, textField, valueField) {
    if (!data) return;
    var option = new Option(data[textField], data[valueField], true, true);
    $(selector)
        .append(option)
        .trigger('change')
        .prop('disabled', true);
}

/* ============================================================
 * HELPER: reset Select2 dengan benar (bukan .empty())
 * ============================================================ */
function resetSelect2(selector) {
    $(selector).val(null).trigger('change.select2');
}

/* ============================================================
 * RANAH GROUP — #idbarang
 * ============================================================ */
var defaultInitialGroupBrng = '';

$("#idbarang").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item',
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
                _paramglobal_: defaultInitialGroupBrng,
                _parameterx_: defaultInitialGroupBrng,
                loccode: $('[name="idlocation_dtl"]').val(),
                term: params.term,
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: { more: (params.page * 30) < data.total_count }
            };
        },
        cache: false
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: formatItem,
    templateSelection: formatItemSelection
})
    .on("select2:select", function (e) {
        handleItemSelected(e.params.data);
    })
    .on("change", function () {
        // Fallback: kalau di-set via .val() programatik / select2:select kadang tidak fire
        let data = $('#idbarang').select2('data')[0];
        if (data && !data.loading) {
            handleItemSelected(data);
        }
    });

/* ============================================================
 * HANDLER: setelah item dipilih
 * ============================================================ */
function handleItemSelected(data) {
    if (!data) return;

    var idlocation = $('[name="idlocation_dtl"]').val();

    if (!idlocation || idlocation.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih gudang terlebih dahulu.'
        });
        $('#saveAjusment').prop('disabled', true);
        return;
    }

    $('#saveAjusment').prop('disabled', false);

    // Guard || '' supaya tidak error saat field undefined
    $('[name="nmbarang"]').val((data.nmbarang || '').trim()).prop("readonly", true);
    $('[name="unit"]').val((data.unit || '').trim()).prop("readonly", true);

    let qty = (data.sisaonhand && data.sisaonhand !== '') ? data.sisaonhand : 0;
    $('[name="qtystock"]').val(qty).prop("readonly", true);

    // ✅ Panggil getSisaStock() juga — karena select2:select kadang tidak trigger blur
    getSisaStock();
}

/* ============================================================
 * DETEKSI PERUBAHAN: getLastStock
 * ============================================================ */
let isLoadingUpdate = false;

$(document).on("change blur", ".getLastStock", function () {
    if (isLoadingUpdate) return;
    getSisaStock();
});

// Fallback tambahan untuk Select2 (#idbarang, #batch)
$(document).on("change", "#idbarang, #batch, #idlocation_dtl", function () {
    if (isLoadingUpdate) return;
    getSisaStock();
});

/* ============================================================
 * AMBIL SISA STOCK + AUTO-FILL HARGA
 * ============================================================ */
function getSisaStock() {
    let idbarang       = $('[name="idbarang"]').val();
    let batch          = $('[name="batch"]').val();
    let idlocation_dtl = $('[name="idlocation_dtl"]').val();

    if (!idbarang || !idlocation_dtl) return;

    $.ajax({
        url: HOST_URL + 'api/globalmodule/list_batch_item',
        type: 'POST',
        dataType: 'json',
        data: {
            _parameterx_: idbarang,
            _var_: batch,
            loccode: idlocation_dtl
        },
        success: function (response) {

            if (!response || !response.items || response.items.length === 0) {
                $('[name="qtystock"]').val(0).prop("readonly", true);
                // ✅ Tetap coba ambil harga walau stok 0
                autoFillHargaPersediaan(idbarang, idlocation_dtl, batch);
                return;
            }

            let data = response.items[0];

            $('[name="nmbarang"]').val((data.nmbarang || '').trim()).prop("readonly", true);
            $('[name="unit"]').val((data.unit || '').trim()).prop("readonly", true);
            $('[name="currency"]').val((data.defaultcurrency || '').trim()).prop("readonly", true);

            let qty = (data.sisaonhand && data.sisaonhand !== '') ? data.sisaonhand : 0;
            $('[name="qtystock"]').val(qty).prop("readonly", true);

            // ✅ AUTO-FILL HARGA PERSEDIAAN
            autoFillHargaPersediaan(idbarang, idlocation_dtl, batch);
        },
        error: function () {
            $('[name="qtystock"]').val(0).prop("readonly", true);
            autoFillHargaPersediaan(idbarang, idlocation_dtl, batch);
        }
    });
}

/* ============================================================
 * AUTO-FILL HARGA PERSEDIAAN (dari list_avg_stock)
 * Hanya isi kalau valqty masih kosong / 0
 * ============================================================ */
function autoFillHargaPersediaan(idbarang, idlocation, batch) {
    if (!idbarang || !idlocation) return;

    $.ajax({
        url: HOST_URL + 'api/globalmodule/list_avg_stock',
        type: 'POST',
        dataType: 'json',
        data: {
            _parameterx_: idbarang,
            loccode:      idlocation,
            _var_:        batch || ''
        },
        success: function (res) {

            console.log('[autoFill] RAW response:', res);

            if (!res) return;

            // Ambil item pertama dari berbagai struktur response
            let item = null;
            if (Array.isArray(res.items) && res.items.length > 0)      item = res.items[0];
            else if (Array.isArray(res.data) && res.data.length > 0)   item = res.data[0];
            else if (res.data && typeof res.data === 'object')         item = res.data;
            else if (typeof res === 'object')                          item = res;

            if (!item) {
                console.log('[autoFill] item kosong');
                return;
            }

            let raw = item.avg_cost ?? item.avgcost ?? item.avgCost
                ?? item.harga ?? item.val ?? item.cost ?? 0;

            let avg_cost = parseFloat(
                String(raw).replace(/[^\d.-]/g, '')
            ) || 0;

            console.log('[autoFill] raw =', raw, ' parsed =', avg_cost);

            if (avg_cost <= 0) {
                console.log('[autoFill] avg_cost 0, skip');
                return;
            }

            let $valqty = $('[name="valqty"]');
            let currentVal = parseFloat(convertToDbNumber($valqty.val())) || 0;

            if (currentVal === 0) {
                $valqty.val(avg_cost);
                if (typeof _jtsseparator === 'function') {
                    _jtsseparator($valqty[0]);
                }
                console.log('[autoFill] Filled valqty =', avg_cost);
            } else {
                console.log('[autoFill] valqty sudah ada, skip. current =', currentVal);
            }
        },
        error: function (xhr) {
            console.log('[autoFill] Error:', xhr.status, xhr.responseText);
        }
    });
}

/* ============================================================
 * FORMAT ITEM
 * ============================================================ */
function formatItem(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
        repo.idbarang + "   <i class='fa fa-circle-o'></i>   " + repo.nmbarang +
        "</div>";
}

function formatItemSelection(repo) {
    return repo.nmbarang || repo.text;
}

/* ============================================================
 * RANAH BATCH
 * ============================================================ */
$("#batch").select2({
    placeholder: "Choose Your Specification / Batch",
    allowClear: true,
    width: "100%",
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_batch_item',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            var idbarang = $("#idbarang").val();
            var loccode = $("#idlocation_dtl").val();
            return {
                term: params.term,
                _parameterx_: (idbarang && idbarang.trim() !== '') ? idbarang : '-',
                loccode: loccode,
            };
        },
        processResults: function (data) {
            var results = $.map(data.items, function (item) {
                return { id: item.batch, text: item.batch };
            });
            return { results: results };
        },
        cache: true
    },
    templateResult: formatBatch,
    templateSelection: formatBatchSelection,
    escapeMarkup: function (markup) { return markup; }
});

function formatBatch(repo) {
    if (repo.loading) return repo.text;
    return '<div class="select2-result-repository__description">' + repo.text + '</div>';
}

function formatBatchSelection(repo) {
    return repo.text || repo.id || '';
}

/* ============================================================
 * UTIL: JTS separator
 * ============================================================ */
function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}

$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});

/* ============================================================
 * NEW SPEC / BATCH
 * ============================================================ */
function new_spec() {
    Swal.fire({
        title: 'New Batch/Specification',
        html: '<input type="text" id="newbatch" class="swal2-input" style="text-transform: uppercase;" placeholder="New Batch/Spec">',
        confirmButtonText: 'Process',
        focusConfirm: false,
        preConfirm: () => {
            const newbatch = Swal.getPopup().querySelector('#newbatch').value;
            if (!newbatch) {
                Swal.showValidationMessage(`Fill The New Batch`);
            }
            return { newbatch: newbatch };
        }
    }).then((result) => {
        $.ajax({
            type: "POST",
            url: HOST_URL + 'api/globalmodule/add_newbatch',
            dataType: 'json',
            data: {
                'loccode': $('[name="idlocation_dtl"]').val(),
                'idbarang': $('[name="idbarang"]').val(),
                'batch': result.value.newbatch,
            },
            success: function (datax) {
                if (datax.status) {
                    Swal.fire({
                        icon: 'success', title: 'Berhasil', text: datax.messages,
                        backdrop: true, allowOutsideClick: false,
                    });
                } else {
                    Swal.fire({
                        icon: 'error', title: 'Oops...', text: datax.messages,
                        backdrop: true, allowOutsideClick: false,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Unable To Response Data',
                    backdrop: true, allowOutsideClick: false,
                });
            }
        });
    });
}

/* ============================================================
 * EDIT ITEM (external)
 * ============================================================ */
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
        icon: 'question',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'master/item/edit/?var=' + e);
        } else if (result.isDenied) {
            return false;
        }
    });
}

/* ============================================================
 * TABLE DETAIL
 * ============================================================ */
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
                "url": HOST_URL + 'persediaan/trans/list_tmp_ajustment_stock_dtl',
                "type": "POST",
                "data": function (data) { /* no-op */ },
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
                { "targets": [-1], "orderable": false }
            ]
        });
    };
    return initTable();
}

function reload_table_transfer_dtl() {
    var table = $('#tmptabspktransfersdtl');
    table.DataTable().ajax.reload();
}

/* ============================================================
 * CHECK ALL
 * ============================================================ */
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
/* ============================================================
   KLIK BODY ROW → TOGGLE CHECKBOX
   ============================================================ */
/* ============================================================
   ROW SELECT — Klik row untuk centang + highlight
   ============================================================ */
$('#tmptabspktransfersdtl tbody').on('click', 'tr', function (e) {

    // Skip kalau yang diklik checkbox atau tombol
    if ($(e.target).is('input[type="checkbox"], button, a, i.fa, .btn')) {
        return;
    }

    let $chk = $(this).find('.row-check');
    if ($chk.length === 0) return;

    let newState = !$chk.prop('checked');
    $chk.prop('checked', newState);

    // Toggle class highlight
    $(this).toggleClass('row-selected', newState);

    // Sinkronisasi checkAll
    syncCheckAll();
});

/* Sync status checkAll */
function syncCheckAll() {
    let total   = $('#tmptabspktransfersdtl tbody .row-check').length;
    let checked = $('#tmptabspktransfersdtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked && total > 0);
}

/* Pastikan checkbox manual juga update highlight */
$('#tmptabspktransfersdtl tbody').on('change', '.row-check', function () {
    $(this).closest('tr').toggleClass('row-selected', this.checked);
    syncCheckAll();
});

/* Setelah redraw DataTable, reset checkAll */
$('#tmptabspktransfersdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
    $('.row-selected').removeClass('row-selected');
});
function getSelectedPPDetail() {
    return $('#tmptabspktransfersdtl tbody .row-check:checked')
        .map(function () { return $(this).val(); }).get();
}

function getCheckedDetailIds() {
    let ids = [];
    $('.row-check:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}

/* ============================================================
 * UPDATE DETAIL
 * ============================================================ */
function updateAjustmentStockDetail() {
    isLoadingUpdate = true;
    const ids = getCheckedDetailIds();

    if (!ids || ids.length === 0) {
        Swal.fire({
            icon: 'warning', title: 'Peringatan',
            text: 'Pilih satu data yang akan diupdate'
        });
        return;
    }

    if (ids.length > 1) {
        Swal.fire({
            icon: 'warning', title: 'Peringatan',
            text: 'Update hanya boleh satu data'
        });
        return;
    }

    $.ajax({
        url: HOST_URL + 'persediaan/trans/get_tmp_ajustment_stock_dtl',
        type: 'GET',
        data: { id: ids[0] },
        dataType: 'json',
        success: function (json) {

            // ✅ Reset Select2 dengan cara yang benar
            resetSelect2('[name="idlocation_dtl"]');
            resetSelect2('#idbarang');
            resetSelect2('#batch');

            if (!json.dataTables.status) {
                isLoadingUpdate = false;
                return;
            }

            let data = json.dataTables.items[0];

            // Trim
            data.idbarang   = (data.idbarang || '').trim();
            data.idlocation = (data.idlocation || '').trim();
            data.batch      = (data.batch || '').trim();
            data.dk         = (data.dk || '').trim();
            data.docno      = (data.docno || '').trim();
            data.nmbarang   = (data.nmbarang || '').trim();
            data.unit       = (data.unit || '').trim();

            // SET FORM
            $('#iduniq').val(data.iduniq);
            $('#idurut').val(data.idurut);
            $('#description').val(data.description);
            $('#docno').val(data.docno);
            $('#dk').val(data.dk).trigger('change');

            $('#valqty').val(data.valqty);
            $('#qtystock').val(data.qtystock);
            $('#nmbarang').val(data.nmbarang);
            $('#unit').val(data.unit);
            $('#qty').val(data.qty);
            $('#currency').val(data.currency);

            /*
             * 1. LOAD LOCATION
             */
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mlocation',
                data: { var: data.idlocation },
                dataType: 'json'
            }).then(function (resLocation) {

                if (!resLocation.items || resLocation.items.length === 0) return;

                let loc = resLocation.items[0];
                loc.idlocation = (loc.idlocation || '').trim();

                let optionLoc = new Option(loc.nmlocation, loc.idlocation, true, true);
                $('[name="idlocation_dtl"]').append(optionLoc).trigger('change');

                /*
                 * 2. LOAD ITEM
                 */
                return $.ajax({
                    type: 'POST',
                    url: HOST_URL + 'api/globalmodule/list_item',
                    data: { var: data.idbarang },
                    dataType: 'json'
                });

            }).then(function (resItem) {

                if (!resItem || !resItem.items || resItem.items.length === 0) return;

                let item = resItem.items[0];
                item.idbarang = (item.idbarang || '').trim();

                let optionItem = new Option(item.nmbarang, item.idbarang, true, true);
                $('#idbarang').append(optionItem).trigger('change');

                /*
                 * 3. LOAD BATCH
                 */
                return $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_batch_item',
                    data: {
                        _parameterx_: data.idbarang,
                        _var_: data.batch,
                        loccode: $('[name="idlocation_dtl"]').val()
                    },
                    dataType: 'json'
                });

            }).then(function (resBatch) {

                if (!resBatch || !resBatch.items || resBatch.items.length === 0) return;

                let batch = resBatch.items[0];
                batch.batch = (batch.batch || '').trim();

                let optionBatch = new Option(batch.batch, batch.batch, true, true);
                $('#batch').append(optionBatch).trigger('change');
            });

            isLoadingUpdate = false;
            $('#modalDetailPPLabel').text('Update Detail');
            $('#modalDetailSPK').modal('show');
        },
        error: function () {
            isLoadingUpdate = false;
        }
    });
}

/* ============================================================
 * CHECK ALL DETAIL (alternatif)
 * ============================================================ */
$('#checkAllDetail').on('change', function () {
    $('.row-check').prop('checked', this.checked);
});

$(document).on('change', '.row-check', function () {
    if (!this.checked) {
        $('#checkAllDetail').prop('checked', false);
    }
});

/* ============================================================
 * FILTER (list lain)
 * ============================================================ */
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
 * FILTER ITEM (list)
 * ============================================================ */
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
                term: params.term,
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
                pagination: { more: (params.page * 30) < data.total_count }
            };
        },
        cache: false
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: formatItemFilter,
    templateSelection: formatItemSelectionFilter
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
});

function formatItemFilter(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
        repo.idbarang + "   <i class='fa fa-circle-o'></i>   " + repo.nmbarang +
        "</div>";
}

function formatItemSelectionFilter(repo) {
    return repo.nmbarang || repo.text;
}

/* ============================================================
 * SAVE AJUSTMENT DETAIL
 * ============================================================ */
function saveAjustment() {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan data Detail?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (!result.isConfirmed) return;

        let qtyInput = $('#qty').val();
        let stockInput = $('#qtystock').val();
        let valueQty = $('#valqty').val();

        let qty = parseFloat(convertToDbNumber(qtyInput)) || 0;
        let qtystock = parseFloat(convertToDbNumber(stockInput)) || 0;
        let valQty = parseFloat(convertToDbNumber(valueQty)) || 0;

        let formData = new FormData(document.getElementById('formAjustmentStockDtl'));

        formData.append('cabang', $('#cabang').val());
        formData.append('pemohon', $('#pemohon').val());
        formData.append('description', $('#description').val());
        formData.append('docdate', $('#docdate').val());
        formData.append('docref', $('#docref').val());

        formData.set(
            'docno',
            $('#prefix').val() + '/' + $('#infix').val() + '/' + $('#suffix').val()
        );

        formData.set('qty', qty);
        formData.set('qtystock', qtystock);
        formData.set('valqty', valQty);
        // ⚠️ iduniq diambil dari input hidden — jangan pakai variabel global iduniq
        formData.set('iduniq', $('#iduniq').val());

        $.ajax({
            url: HOST_URL + 'persediaan/trans/save_ajustment_stock_detail',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,

            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success', title: 'Berhasil',
                        text: res.message || 'Data Detail berhasil disimpan',
                        timer: 2000, showConfirmButton: false
                    });

                    if (res.reload === true) {
                        window.location.reload();
                        return;
                    }

                    $('#modalDetailSPK').modal('hide');
                    reload_table_transfer_dtl();
                    $('#formAjustmentStockDtl')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'warning', title: 'Gagal',
                        text: res.message || 'Gagal menyimpan data'
                    });
                }
            },

            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error', title: 'Server Error',
                    text: 'Terjadi kesalahan server'
                });
            }
        });
    });
}

/* ============================================================
 * BUTTON: ADD DETAIL
 * ============================================================ */
$('#btnAddDetail').on('click', function (e) {
    btnInputDetail();
});

function btnInputDetail() {
    $('#formAjustmentStockDtl')[0].reset();
    resetSelect2('#idlocation_dtl');
    resetSelect2('#batch');
    resetSelect2('#idbarang');
    $('#idurut').val('');
    $('#iduniq').val('');
    $('#modalDetailPPLabel').text('Tambah Item Detail');
    $('#modalDetailSPK').modal('show');
}

/* ============================================================
 * DOC NUMBER
 * ============================================================ */
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
            loadNextSuffixAdj();

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
                    ? logindate : startDate;

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
                    $el.on('cancel.daterangepicker', function () {
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

function loadNextSuffixAdj() {
    let prefix = $.trim($('#prefix').val()).toUpperCase();
    let infix = $.trim($('#infix').val());

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
            $('#suffix').val(res.suffix);
            generateDocNumber(prefix, infix, res.suffix);
        }
    });
}

$('#prefix').on('blur', function () {
    loadNextSuffixAdj();
});

/* ============================================================
 * BRANCH
 * ============================================================ */
var defaultInitialBranch = '';

$("#cabang").select2({
    placeholder: "Type/ Choose your Branch",
    allowClear: true,
    width: '100%',
    maximumSelectionLength: 1,
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
                term: params.term,
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
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
});

function formatBranch(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
        repo.idbranch + "   <i class='fa fa-circle-o'></i>   " + repo.nmbranch +
        "</div>";
}

function formatBranchSelection(repo) {
    return repo.nmbranch || repo.text;
}

/* ============================================================
 * CABANG SENT
 * ============================================================ */
var defaultInitialCabangSent = '';

$("#cabang_sent").select2({
    placeholder: "Type/ Choose your Branch",
    allowClear: true,
    width: '100%',
    maximumSelectionLength: 1,
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
                term: params.term,
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
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
});

/* ============================================================
 * QR SCANNER
 * ============================================================ */
function open_scan() {
    read_qrcode();
    $('#open_scan').modal('show');
    $('.modal-title').text('Open Scanner');
}

function read_qrcode() {
    function onScanSuccess(decodedText) {
        play();
        $('#searchitem').val(decodedText);
        $('#open_scan').modal('hide');
        var table = $('#tsearchitem');
        table.DataTable().ajax.reload();
        html5QrcodeScanner.clear();
    }
    var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
}

var audio = document.getElementById('chatAudio');
function play() {
    if (audio) audio.play();
}

/* ============================================================
 * DEPARTEMEN
 * ============================================================ */
function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
        repo.kddept + "   <i class='fa fa-circle-o'></i>   " + repo.nmdept +
        "</div>";
}

function formatNewdeptSelection(repo) {
    return repo.nmdept || repo.text;
}

var defaultInitialNewDept = '';

$("#iddept").select2({
    placeholder: "Pilih Bagian",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_departmen',
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
                _paramglobal_: defaultInitialNewDept,
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: { more: (params.page * 30) < data.total_count }
            };
        },
        cache: false
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: formatNewdept,
    templateSelection: formatNewdeptSelection
});

/* ============================================================
 * JURNAL / DOC REF
 * ============================================================ */
$("#fjurnal").on("change", function () {
    $.ajax({
        url: HOST_URL + 'purchase/purchaseorder/njurnal/?var=' + $("#fjurnal").val(),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $("#njurnal").val(data.njurnal);
        },
        error: function () {
            alert('Error get data from ajax');
        }
    });
});

$("#docjns").on("change", function () {
    if ($(this).val() === 'PO') {
        $('.sref').remove();
        $('.sreference').append(
            '<div class="sref"> <label for="description">Reference ID/PO Number</label>' +
            '<select name="docref" id="docref2" class="form-control" required></select></div>'
        );

        var defaultInitialPO = $("#docref2").val();
        $("#docref2").select2({
            placeholder: "Ketik PO Outstanding",
            allowClear: true,
            maximumSelectionLength: 1,
            multiple: false,
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_outstanding_po',
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
                        _paramglobal_: defaultInitialPO,
                        _parameterx_: defaultInitialPO,
                        term: params.term,
                    };
                },
                processResults: function (data, params) {
                    var searchTerm = $("#docref2").data("select2").$dropdown.find("input").val();
                    if (data.items.length === 1 && data.items[0].text === searchTerm) {
                        var option = new Option(data.items[0].docno, data.items[0].docno, true, true);
                        $('#docref2').append(option).trigger('change').select2("close");
                        $('#docref2').trigger({
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
            templateResult: formatOutstandingPO,
            templateSelection: formatOutstandingPOSelection
        });

        function formatOutstandingPO(repo) {
            if (repo.loading) return repo.text;
            return "<div class='select2-result-repository__description'>" + repo.docno + "</div>";
        }

        function formatOutstandingPOSelection(repo) {
            return repo.docno || repo.text;
        }
    } else {
        $('.sref').remove();
        $('.sreference').append(
            '<div class="sref"> <label for="description">Reference ID/PO Number</label>' +
            '<input type="text" name="docref" class="form-control" id="docref1" ' +
            'style="text-transform: uppercase" placeholder="Reference ID" required></div>'
        );
    }
});

/* ============================================================
 * COST CENTER
 * ============================================================ */
function formatCostcenter(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
        repo.idcostcenter + "   <i class='fa fa-circle-o'></i>   " + repo.nmcostcenter +
        "</div>";
}

function formatCostcenterSelection(repo) {
    return repo.nmcostcenter || repo.text;
}

$("#idcostcenter").select2({
    placeholder: "Ketik/Pilih Cost Center",
    allowClear: true,
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
                _paramglobal_: '',
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
    templateResult: formatCostcenter,
    templateSelection: formatCostcenterSelection
});

/* ============================================================
 * LOCATION (Gudang Asal)
 * ============================================================ */
var defaultInitialLocationFrom = '';

$("#idlocation_dtl").select2({
    placeholder: " -- Pilih Gudang Asal -- ",
    allowClear: true,
    maximumSelectionLength: 1,
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
                term: params.term,
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#idlocation_dtl").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmlocation, data.items[0].idlocation, true, true);
                $('#idlocation_dtl').append(option).trigger('change').select2("close");
                $('#idlocation_dtl').trigger({
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
    templateResult: formatLocation,
    templateSelection: formatLocationSelection
}).on("change", function () {
    if ($(this).val()) {
        $('#saveAjusment').prop('disabled', false);
    } else {
        $('#saveAjusment').prop('disabled', true);
    }

    resetSelect2('#idbarang');
    resetSelect2('#batch');

    $('[name="nmbarang"]').val('');
    $('[name="unit"]').val('');
    $('[name="qtystock"]').val(0);
    $('[name="valqty"]').val('');
});

function formatLocation(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
        repo.idlocation + "   <i class='fa fa-circle-o'></i>   " + repo.nmlocation +
        "</div>";
}

function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}

/* ============================================================
 * LOCATION (Gudang Tujuan)
 * ============================================================ */
var defaultInitialLocationTo = '';

$("#idlocation_to").select2({
    placeholder: " -- Pilih Gudang Tujuan -- ",
    allowClear: true,
    maximumSelectionLength: 1,
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
                term: params.term,
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#idlocation_to").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmlocation, data.items[0].idlocation, true, true);
                $('#idlocation_to').append(option).trigger('change').select2("close");
                $('#idlocation_to').trigger({
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
    templateResult: formatLocation,
    templateSelection: formatLocationSelection
}).on("change", function () {
    /* Sementara ditutup */
});

/* ============================================================
 * LOCATION (Gudang Transit)
 * ============================================================ */
var defaultInitialLocationTransit = '';

$("#idlocation_transit").select2({
    placeholder: " -- Pilih Gudang Tujuan -- ",
    allowClear: true,
    maximumSelectionLength: 1,
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
                term: params.term,
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#idlocation_transit").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmlocation, data.items[0].idlocation, true, true);
                $('#idlocation_transit').append(option).trigger('change').select2("close");
                $('#idlocation_transit').trigger({
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
    templateResult: formatLocation,
    templateSelection: formatLocationSelection
}).on("change", function () {
    /* Sementara ditutup */
});

/* ============================================================
 * DELETE DETAIL
 * ============================================================ */
function btnDeleteDetail() {
    const ids = getCheckedDetailIds();

    if (!ids || ids.length === 0) {
        Swal.fire({
            icon: 'warning', title: 'Peringatan',
            text: 'Pilih minimal satu data yang akan dihapus'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Data yang dihapus. Lanjutkan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'persediaan/trans/deleteAjustmentStockDetail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json',

            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success', title: 'Berhasil',
                        text: res.message || 'Data berhasil dihapus',
                        timer: 2000, showConfirmButton: false
                    });
                    reload_table_transfer_dtl();
                } else {
                    Swal.fire({
                        icon: 'warning', title: 'Gagal',
                        text: res.message || 'Gagal menghapus data'
                    });
                }
            },

            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error', title: 'Server Error',
                    text: 'Terjadi kesalahan server'
                });
            }
        });
    });
}

/* ============================================================
 * DOCUMENT READY
 * ============================================================ */
$(document).ready(function () {

    // Fix SweetAlert di atas Bootstrap modal
    if ($.fn.modal) {
        $.fn.modal.Constructor.prototype._enforceFocus = function () { };
    }

    table_trx_ajustment_stock_mst();
    tabletmpSPKDetail();

    $('#checkboxnik').change(function () {
        if (this.checked) {
            var valnik = $('#nik').val();
            $('#username').prop('readonly', true);
            $('#username').val(valnik);
        } else {
            $('#username').prop('readonly', false);
            $('#username').val('');
        }
    });

    $('#nik').change(function () {
        if ($('#checkboxnik').is(':checked')) {
            var valnik = $(this).val();
            $('#username').val(valnik);
        }
    });

    documentReadable();
    $("#loadMe").modal("hide");
});