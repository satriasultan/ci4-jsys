/*
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


var save_method; //for save method string
var table;
var initTable;

//"use strict";

/* VIUW UTAMA*/
function table_trx_pmk_brng_mst() {
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tPmkBrg');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
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
            "buttons": [
                'pageLength', 'excel'
            ],
            "ajax": {
                "url": HOST_URL + 'persediaan/trans/list_trx_pmk_brng_mst',
                "type": "POST",
                "data": function (data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namasupplier = $('#namasupplier').val();
                    data.status = $('#status_filter').val(); //A,P,S,ALL
                },
                "dataFilter": function (data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json); // return JSON string
                }
            },

            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [-1], //last column
                    "orderable": false, //set not orderable
                },
            ],

        });

    };


    return initTable();
}

function reloadPmkBrg() {
    var table = $('#tPmkBrg');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function () { //button filter event click
    var table = $('#tPmkBrg');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function () { //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tPmkBrg');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

let skipRoleChange = false;


//EDIT ITEM
function documentReadable() {

    //showLoader();

    var docno = $('[name="docno"]').val();

    $.getJSON(HOST_URL + 'persediaan/trans/showing_pmk_brng_mst_tmp', {docno: docno})
        .done(function (response) {

            if (!response.dataTables || !response.dataTables.items.length) {
                hideLoader();
                console.log("Data kosong");
                return;
            }

            const item = response.dataTables.items[0];

            // ===============================
            // SET BASIC FIELD (langsung saja)
            // ===============================
            $('[name="docno"]').val(item.docno).prop('readonly', true);

            let prefixParts = item.docno.trim().split('/');
            $('[name="prefix"]').val(prefixParts[0]).prop('readonly', true);
            $('[name="infix"]').val(prefixParts[1]).prop('readonly', true);
            $('[name="sufix"]').val(prefixParts[2]).prop('readonly', true);

            $('[name="docdate"]').val(item.docdate).prop('disabled', true);
            $('[name="docref"]').val(item.docref).prop('disabled', false);
            $('[name="keterangan"]').val(item.description);

            skipRoleChange = true;

            // ===============================
            // LOAD SEMUA MASTER DATA PARALEL
            // ===============================

            const branch1 = $.getJSON(HOST_URL + 'api/globalmodule/list_branchjob', {var: item.cabang});
            const branch2 = $.getJSON(HOST_URL + 'api/globalmodule/list_branchjob', {var: item.cabang_sent});
            // const locFrom = $.getJSON(HOST_URL + 'api/globalmodule/list_mlocation', {var: item.idlocation_dtl});
            // const locTo = $.getJSON(HOST_URL + 'api/globalmodule/list_mlocation', {var: item.idlocation_to});
            // const locTransit = $.getJSON(HOST_URL + 'api/globalmodule/list_mlocation', {var: item.idlocation_transit});
            const locCostCenter = $.getJSON(HOST_URL + 'api/globalmodule/list_costcenter', {var: item.idcostcenter});

            $.when(branch1, branch2, locCostCenter)
                .done(function (b1, b2, l1) {

                    setSelect2('[name="cabang"]', b1[0].items[0], 'nmbranch', 'idbranch');
                    setSelect2('[name="idcostcenter"]', l1[0].items[0], 'nmcostcenter', 'idcostcenter');
                    // setSelect2('[name="idlocation_dtl"]', l1[0].items[0], 'nmlocation', 'idlocation');
                    // setSelect2('[name="idlocation_to"]', l2[0].items[0], 'nmlocation', 'idlocation');
                    // setSelect2('[name="idlocation_transit"]', l3[0].items[0], 'nmlocation', 'idlocation');


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


// ===============================
// Helper Function (Lebih Bersih)
// ===============================
function setSelect2(selector, data, textField, valueField) {
    if (!data) return;

    var option = new Option(data[textField], data[valueField], true, true);
    $(selector)
        .append(option)
        .trigger('change')
        .prop('disabled', true);
}

/* FOR INPUT FUNCTION */


// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH GROUP ++++++++++++++++++++++++++++++++++++++++//

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
                _search_: params.term, // search term
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
            // var searchTerm = $("#idbarang").data("select2").$dropdown.find("input").val();
            // if (data.items.length === 1 && data.items[0].text === searchTerm) {
            //     var option = new Option(data.items[0].nmbarang, data.items[0].idbarang, true, true);
            //     $('#idbarang').append(option).trigger('change').select2("close");
            //     // manually trigger the `select2:select` event
            //     $('#idbarang').trigger({
            //         type: 'select2:select',
            //         params: {
            //             data: data
            //         }
            //     });
            // }
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
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatItem, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var idlocation = $('[name="idlocation_dtl"]').val();

    if (!idlocation || idlocation.trim() === '') {

        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih gudang terlebih dahulu.'
        });

        $('#saveAjusment').prop('disabled', true);

        return; // hentikan proses

    } else {
        $('#saveAjusment').prop('disabled', false);
    }


    var data = e.params.data;

    $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    //$('[name="batch"]').val(data.batch.trim()).prop("readonly", true);

    let qty = (data.sisaonhand && data.sisaonhand !== '') ? data.sisaonhand : 0;
    $('[name="qtystock"]').val(qty).prop("readonly", true);
});


let isLoadingUpdate = false;
$(document).on("change blur", ".getLastStock", function () {
    if (isLoadingUpdate) return;
    getSisaStock();
});
//class getLastStock setiap ada pergerakan dari getLastStock class , input on blur on select selalu menjalankan fungsi getSisaStock(), dan fix ajax saya
function getSisaStock() {

    let idbarang       = $('[name="idbarang"]').val();
    let batch          = $('[name="batch"]').val();
    let idlocation_dtl = $('[name="idlocation_dtl"]').val();

    if (!idbarang || !idlocation_dtl) {
        return;
    }

    $.ajax({
        url: HOST_URL + 'api/globalmodule/list_avg_stock',
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
                return;
            }

            let data = response.items[0];

            $('[name="nmbarang"]').val((data.nmbarang || '').trim()).prop("readonly", true);

            $('[name="unit"]').val((data.unit || '').trim()).prop("readonly", true);
            $('[name="currency"]').val((data.currcode || '').trim()).prop("readonly", true);

     /*       if (data.batch) {
                $('#batch').val(data.batch).trigger('change');
            }*/

            let qty = (data.qty && data.qty !== '') ? data.qty : 0;

            $('[name="qtystock"]').val(qty).prop("readonly", true);

        },

        error: function () {

            console.log("Failed get stock data");

            $('[name="qtystock"]').val(0).prop("readonly", true);

        }

    });

}


/* Format Group */
function formatItem(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.idbarang + "   <i class='fa fa-circle-o'></i>   " + repo.nmbarang + "</div>";
    return markup;
}

function formatItemSelection(repo) {
    return repo.nmbarang || repo.text;
}


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
                return {
                    id: item.batch,
                    text: item.batch
                };
            });

            return {
                results: results
            };
        },

        cache: true
    },

    templateResult: formatBatch,
    templateSelection: formatBatchSelection,

    escapeMarkup: function (markup) {
        return markup;
    }

});


function formatBatch(repo) {

    if (repo.loading) return repo.text;

    return '<div class="select2-result-repository__description">' +
        repo.text +
        '</div>';
}


function formatBatchSelection(repo) {

    return repo.text || repo.id || '';

}


function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}


$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});

function new_spec() {
    Swal.fire({
        title: 'New Batch/Specification',
        html: '<input type="text" id="newbatch" class="swal2-input" style="text-transform: uppercase;" placeholder="New Batch/Spec">',
        confirmButtonText: 'Process',
        focusConfirm: false,
        preConfirm: () => {
            const newbatch = Swal.getPopup().querySelector('#newbatch').value
            //const password = Swal.getPopup().querySelector('#password').value
            if (!newbatch) {
                Swal.showValidationMessage(`Fill The New Batch`)
            }
            return {newbatch: newbatch}
        }
    }).then((result) => {
        $.ajax({
            type: "POST",
            url: HOST_URL + 'api/globalmodule/add_newbatch' + '',
            dataType: 'json',
            data: {
                'loccode': $('[name="idlocation_dtl"]').val(),
                'idbarang': $('[name="idbarang"]').val(),
                'batch': result.value.newbatch,
            },
            success: function (datax) {
                if (datax.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: datax.messages,
                        backdrop: true,
                        allowOutsideClick: false,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: datax.messages,
                        backdrop: true,
                        allowOutsideClick: false,
                    })
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Unable To Response Data',
                    backdrop: true,
                    allowOutsideClick: false,
                })

            }
        });

    })
}


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
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'master/item/edit' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}


/* TABLE PP DETAIL */
function tabletmpSPKDetail() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#tmptabpmksdtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
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
                "data": function (data) {
                    //data.searchfilter = $('#searchitem').val()+'';
                    //data.idbarang = $('#idbarang').val()+'';
                    //data.idposition = $('#idposition').val()+'';
                },
                "dataFilter": function (data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json); // return JSON string
                }
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": 0,
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center",
                    "render": function (data, type, row) {
                        // row[1] = kolom ID (ID Tax)
                        return '<input type="checkbox" class="row-check" value="' + row[0] + '">';
                    }
                },
                {
                    "targets": [-1],
                    "orderable": false
                }
            ]
        });
    }

    return initTable();

}


function reload_pemakaian_dtl() {
    var table = $('#tmptabpmksdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#tmptabpmksdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tmptabpmksdtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
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


function updatePmkBrgDtl() {
    isLoadingUpdate = true;
    const ids = getCheckedDetailIds();

    if (!ids || ids.length === 0) {
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

    $.ajax({
        url: HOST_URL + 'persediaan/trans/get_tmp_pmk_brng_dtl',
        type: 'GET',
        data: { id: ids[0] },
        dataType: 'json',
        success: function (json) {

            $('[name="idlocation_dtl"]').empty().trigger('change');
            $('#idbarang').empty().trigger('change');
            $('#batch').empty().trigger('change');

            if (!json.dataTables.status) return;

            let data = json.dataTables.items[0];

            /*
            =========================
            TRIM DATA (CHAR FIX)
            =========================
            */

            data.idbarang   = (data.idbarang || '').trim();
            data.idlocation = (data.idlocation || '').trim();
            data.batch      = (data.batch || '').trim();
            data.dk         = (data.dk || '').trim();
            data.docno      = (data.docno || '').trim();
            data.nmbarang   = (data.nmbarang || '').trim();
            data.unit       = (data.unit || '').trim();

            /*
            =========================
            SET FORM
            =========================
            */

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
            =========================
            RESET SELECT2
            =========================
            */


            /*
            =========================
            1. LOAD LOCATION
            =========================
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

                $('[name="idlocation_dtl"]')
                    .append(optionLoc)
                    .trigger('change');

                /*
                =========================
                2. LOAD ITEM
                =========================
                */

                return $.ajax({
                    type: 'POST',
                    url: HOST_URL + 'api/globalmodule/list_item',
                    data: {
                        var: data.idbarang,
                        //loccode: loc.idlocation
                    },
                    dataType: 'json'
                });

            }).then(function (resItem) {

                if (!resItem || !resItem.items || resItem.items.length === 0) return;

                let item = resItem.items[0];

                item.idbarang = (item.idbarang || '').trim();

                let optionItem = new Option(item.nmbarang, item.idbarang, true, true);

                $('#idbarang')
                    .append(optionItem)
                    .trigger('change');

                $('#idbarang').trigger({
                    type: 'select2:select',
                    params: { data: item }
                });

                /*
                =========================
                3. LOAD BATCH
                =========================
                */

                // 🔥 VALIDASI AWAL (INI KUNCI)
                let batchParam = (data.batch || '').trim();

                if (!batchParam) {
                    // batch kosong → stop di sini, tidak lanjut ke AJAX batch
                    $('#batch').val(null);
                    return;
                }

                return $.ajax({
                    type: 'POST',
                    url: HOST_URL + 'api/globalmodule/list_batch_item',
                    data: {
                        _parameterx_: data.idbarang,
                        _var_: batchParam,
                        loccode: $('[name="idlocation_dtl"]').val()
                    },
                    dataType: 'json'
                });

            }).then(function (resBatch) {

                let $batch = $('#batch');
                $batch.empty();

                if (!resBatch || !resBatch.items || resBatch.items.length === 0) {
                    $batch.val(null);
                    return;
                }

                let batch = resBatch.items[0];
                let batchVal = (batch.batch || '').trim();

                // 🔥 double safety (opsional tapi bagus)
                if (!batchVal) {
                    $batch.val(null);
                    return;
                }

                let optionBatch = new Option(batchVal, batchVal, true, true);

                $batch.append(optionBatch);
                $batch.val(batchVal).trigger('change');

            });

            /*
            =========================
            SHOW MODAL
            =========================
            */
            isLoadingUpdate = false;
            $('#modalDetailPemakaianBarang').text('Update Detail');
            $('#modalDetailPMK').modal('show');



        }
    });

}


$('#checkAllDetail').on('change', function () {
    $('.row-check').prop('checked', this.checked);
});

// auto uncheck checkAll jika salah satu dilepas
$(document).on('change', '.row-check', function () {
    if (!this.checked) {
        $('#checkAllDetail').prop('checked', false);
    }
});


$('#btn-filter').click(function () { //button filter event click
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function () { //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});


var defaultInitialItem = '';
$("#idbarang_filter").select2({
    placeholder: "Type/ Choose your item",
    allowClear: true,
    dropdownParent: $("#filter"),
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    //maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'stock/balance/list_item',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _search_: params.term, // search term
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
                // manually trigger the `select2:select` event
                $('#idbarang_filter').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
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
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatItemFilter, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelectionFilter // omitted for brevity, see the source of this page
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
    //var table = $('#tsearchitem');
    //table.DataTable().ajax.reload(); //reload datatable ajax
    ///table.append().search( $(this).val() ).draw();
    //$('#filter').modal('hide');
});

/* Format Group */
function formatItemFilter(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.idbarang + "   <i class='fa fa-circle-o'></i>   " + repo.nmbarang + "</div>";
    return markup;
}

function formatItemSelectionFilter(repo) {
    return repo.nmbarang || repo.text;
}


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

        // ===============================
        // Ambil dan validasi qty
        // ===============================

        let qtyInput = $('#qty').val();
        let stockInput = $('#qtystock').val();
        let valueQty = $('#valqty').val();
        let vardk = $('#dk').val();

        let qty = parseFloat(convertToDbNumber(qtyInput)) || 0;
        let qtystock = parseFloat(convertToDbNumber(stockInput)) || 0;
        let valQty = parseFloat(convertToDbNumber(valueQty)) || 0;

        // ===============================
        // VALIDASI TAMBAHAN
        // ===============================

        // qty harus valid
        if (isNaN(qty) || qty <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Qty Tidak Valid',
                text: 'Qty harus lebih dari 0'
            });
            return;
        }

        // qtystock tidak boleh null / 0
        if (isNaN(qtystock) || qtystock <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Tidak Valid',
                text: 'Qty stock tidak boleh kosong atau 0'
            });
            return;
        }

        // qtystock harus >= qty
        if (qtystock < qty) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Tidak Cukup',
                text: 'Qty melebihi stock tersedia'
            });
            return;
        }

        // ===============================
        // Siapkan FormData
        // ===============================

        let formData = new FormData(document.getElementById('formPemakaianStockDtl'));

        formData.append('cabang', $('#cabang').val());
        formData.append('pemohon', $('#pemohon').val());
        formData.append('description', $('#description').val());
        formData.append('docdate', $('#docdate').val());
        formData.append('docref', $('#docref').val());
        formData.append('idcostcenter', $('#idcostcenter').val());

        // Gabungkan docno
        formData.set(
            'docno',
            $('#prefix').val() + '/' +
            $('#infix').val() + '/' +
            $('#sufix').val()
        );

        // Set numeric value yang sudah divalidasi
        formData.set('qty', qty);
        formData.set('qtystock', qtystock);
        formData.set('valqty', valQty);

        // ===============================
        // AJAX SAVE (TIDAK DIUBAH)
        // ===============================

        $.ajax({
            url: HOST_URL + 'persediaan/trans/save_pmk_brng_detail',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,

            success: function (res) {

                if (res.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data Detail berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    if (res.reload === true) {
                        window.location.reload();
                        return;
                    }

                    $('#modalDetailPMK').modal('hide');
                    reload_pemakaian_dtl();
                    $('#formPemakaianStockDtl')[0].reset();

                } else {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: res.message || 'Gagal menyimpan data'
                    });
                }
            },

            error: function (xhr) {
                console.error(xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan server'
                });
            }
        });

    });
}

// ===============================
// VALIDASI BUTTON SAVE
// ===============================
$('#btnAddDetail').on('click', function (e) {
    btnInputDetail();
    // var idlocation_dtl = $('[name="idlocation_dtl"]').val();
    // console.log(idlocation_dtl + "STRING")
    // if (!idlocation_dtl) {
    //     e.preventDefault();
    //
    //     Swal.fire({
    //         icon: 'warning',
    //         title: 'Oops...',
    //         text: 'Semua Inputan Master Tidak Boleh Kosong!',
    //         confirmButtonColor: '#3085d6'
    //     });
    //     return false; // berhenti di sini
    //
    //
    // } else {
    //     // ✅ kalau valid jalankan function
    //
    //
    //
    //
    //
    //
    // }

});


// ===============================
// FUNCTION BUKA MODAL
// ===============================
function btnInputDetail() {
    console.log(" INI KENAPA DI SISI ");


    $('#formPemakaianStockDtl')[0].reset();
    // Clear select2
    $('#idlocation_dtl').val(null).trigger('change');
    $('#batch').val(null).trigger('change');
    $('#idbarang').val(null).trigger('change');
    $('#idurut').val('');
    $('#modalDetailPemakaianBarang').text('Tambah Item Detail');
    $('#modalDetailPMK').modal('show');
}


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
        data: {idbranch: idbranch},
        dataType: 'json',
        success: function (res) {

            if (!res.success) {
                Swal.fire('Error', res.message, 'warning');
                return;
            }

            currentKodeSuffix = res.kode_suffix;

            $('#infix').val(res.infix);

            // SET PREFIX + TRIGGER CHANGE
            $('#prefix').val('PBL').trigger('change');

            $('#sufix').val(currentKodeSuffix + '0001');

            var infix = (res.infix || '').toString();

            if (infix.length === 4) {

                $('#docdate').prop('disabled', false);

                var yy = infix.substring(0, 2);
                var mm = infix.substring(2, 4);

                var year = 2000 + parseInt(yy, 10);
                var month = parseInt(mm, 10) - 1;

                var today = moment();
                var startDate = moment([year, month, 1]);
                var endDate = moment(startDate).endOf('month');

                var $el = $('#docdate');
                var drp = $el.data('daterangepicker');

                if (drp) {

                    drp.minDate = startDate;
                    drp.maxDate = endDate;
                    drp.setStartDate(startDate);
                    drp.setEndDate(startDate);

                } else {

                    $el.daterangepicker({
                        autoUpdateInput: false,
                        singleDatePicker: true,
                        showDropdowns: true,
                        startDate: today,
                        minDate: startDate,
                        maxDate: endDate,
                        locale: {format: 'YYYY-MM-DD'},
                        cancelLabel: 'Clear'
                    });

                    $el.on('apply.daterangepicker', function (ev, picker) {
                        $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    });

                    $el.on('cancel.daterangepicker', function (ev, picker) {
                        $(this).val('');
                    });
                }

                $el.val(today.format('YYYY-MM-DD'));
            }

            generateDocNumber('JBR', res.infix, currentKodeSuffix + '0001');

        }
    });

});


$('#prefix').on('change', function () {

    let prefix = $(this).val().toUpperCase();
    $(this).val(prefix);

    let infix = $('#infix').val();

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

            $('#sufix').val(res.suffix);

            generateDocNumber(prefix, infix, res.suffix);

        }
    });

});


var defaultInitialBranch = '';
$("#cabang").select2({
    placeholder: "Type/ Choose your Branch",
    allowClear: true,
    width: '100%',
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_branchjob',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _search_: params.term, // search term
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
                // manually trigger the `select2:select` event
                $('#cabang').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
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
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatBranch, // omitted for brevity, see the source of this page
    templateSelection: formatBranchSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
    //var table = $('#tsearchitem');
    //table.DataTable().ajax.reload(); //reload datatable ajax
    ///table.append().search( $(this).val() ).draw();
    //$('#filter').modal('hide');
});

/* Format Group */
function formatBranch(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.idbranch + "   <i class='fa fa-circle-o'></i>   " + repo.nmbranch + "</div>";
    return markup;
}

function formatBranchSelection(repo) {
    return repo.nmbranch || repo.text;
}


var defaultInitialCabangSent = '';
$("#cabang_sent").select2({
    placeholder: "Type/ Choose your Branch",
    allowClear: true,
    width: '100%',
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_branchjob',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _search_: params.term, // search term
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
                // manually trigger the `select2:select` event
                $('#cabang_sent').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
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
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatBranch, // omitted for brevity, see the source of this page
    templateSelection: formatBranchSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
    //var table = $('#tsearchitem');
    //table.DataTable().ajax.reload(); //reload datatable ajax
    ///table.append().search( $(this).val() ).draw();
    //$('#filter').modal('hide');
});


/* TABEL LIST PO FINAL */


//OPEN SCANNER
function open_scan() {
    //$('[name="dob"]').datepicker('update',data.dob);
    read_qrcode();
    $('#open_scan').modal('show'); // show bootstrap modal when complete loaded
    $('.modal-title').text('Open Scanner'); // Set title to Bootstrap modal title
}

function read_qrcode() {
    function onScanSuccess(decodedText, decodedResult) {
        play();
        //alert(`Code scanned = ${decodedText}`, decodedResult);
        //alert('Code scanned Kintil= ' + decodedText + '');
        //$('#searchitem').val(decodedText).trigger('keyup');

        $('#searchitem').val(decodedText);
        $('#open_scan').modal('hide');
        var table = $('#tsearchitem');
        table.DataTable().ajax.reload();

        //
        // var _this = $(decodedText); // copy of this object for further usage
        // clearTimeout(timer);
        // timer = setTimeout(function() {
        //     //$('#searchitem').val('');
        //     $('#searchitem').val(decodedText);
        //     $('#open_scan').modal('hide');
        //     var table = $('#tsearchitem');
        //     table.DataTable().ajax.reload();
        // }, 1000);
        html5QrcodeScanner.clear();
    }

    var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", {fps: 10, qrbox: 250});
    html5QrcodeScanner.render(onScanSuccess);

}

var audio = document.getElementById('chatAudio');

function play() {
    audio.play()
}

function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kddept + "   <i class='fa fa-circle-o'></i>   " + repo.nmdept + "  </div>";
    return markup;
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
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialNewDept,
            };
        },
        processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
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
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatNewdept, // omitted for brevity, see the source of this page
    templateSelection: formatNewdeptSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
});

$("#fjurnal").on("change", function () {
    //$("#njurnal").val('00011');
    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'purchase/purchaseorder/njurnal' + '/?var=' + $("#fjurnal").val(),
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $("#njurnal").val(data.njurnal);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
});


$("#docjns").on("change", function () {
    console.log($(this).val() + 'H');
    if ($(this).val() === 'PO') {

        $('.sref').remove();
        $('.sreference').append('<div class="sref"> <label for="description">Reference ID/PO Number</label><select name="docref" id="docref2" class="form-control" required></select></div>');

        //LOAD PO
        var defaultInitialPO = $("#docref2").val();
        $("#docref2").select2({
            placeholder: "Ketik PO Outstanding",
            allowClear: true,
            //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
            maximumSelectionLength: 1,
            multiple: false,
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_outstanding_po',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _search_: params.term, // search term
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
                        // manually trigger the `select2:select` event
                        $('#docref2').trigger({
                            type: 'select2:select',
                            params: {
                                data: data
                            }
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
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            // minimumInputLength: 1,
            templateResult: formatOutstandingPO, // omitted for brevity, see the source of this page
            templateSelection: formatOutstandingPOSelection // omitted for brevity, see the source of this page
        }).on("change", function () {

        });


        /* Format Group */
        function formatOutstandingPO(repo) {
            if (repo.loading) return repo.text;
            var markup = "<div class='select2-result-repository__description'>" + repo.docno + "</div>";
            return markup;
        }

        function formatOutstandingPOSelection(repo) {
            return repo.docno || repo.text;
        }
    } else {
        $('.sref').remove();
        $('.sreference').append('<div class="sref"> <label for="description">Reference ID/PO Number</label> <input type="text" name="docref" class="form-control" id="docref1"  style="text-transform: uppercase" placeholder="Reference ID" required></div>');
    }


});

// COST CENTER
function formatCostcenter(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.idcostcenter + "   <i class='fa fa-circle-o'></i>   " + repo.nmcostcenter + "</div>";
    return markup;
}

function formatCostcenterSelection(repo) {
    return repo.nmcostcenter || repo.text;
}

//var defaultInitialDivision = $("#newdept").val();
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
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCostcenter, // omitted for brevity, see the source of this page
    templateSelection: formatCostcenterSelection // omitted for brevity, see the source of this page
});


//ID LOCATION PEMILIHAN GUDANG ASAL
var defaultInitialLocationFrom = '';
// =========================
// GLOBAL FLAG (WAJIB ADA)
// =========================
let isAutoSet = false;


// =========================
// SELECT2 LOCATION
// =========================
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

            let searchTerm = $("#idlocation_dtl")
                .data("select2").$dropdown.find("input").val();

            if (data.items.length === 1 && data.items[0].text === searchTerm) {

                let item = data.items[0];
                let option = new Option(item.nmlocation, item.idlocation, true, true);

                // ⛔ tandai auto
                isAutoSet = true;

                $('#idlocation_dtl')
                    .append(option)
                    .val(item.idlocation)
                    .trigger('change')
                    .select2("close");

                isAutoSet = false;
            }

            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function (markup) {
        return markup;
    },
    templateResult: formatLocation,
    templateSelection: formatLocationSelection
})

    .on("change", function () {

        // 🔥 STOP kalau dari auto load
        if (isAutoSet) return;

        let val = $(this).val();

        $('#savePmkBrng').prop('disabled', !val);

        // =========================
        // RESET TANPA TRIGGER (ANTI DOMINO)
        // =========================
        $('#idbarang').val(null);
        $('#batch').val(null);

        $('[name="nmbarang"]').val('');
        $('[name="unit"]').val('');
        $('[name="qtystock"]').val(0);

    });

/* Format Group */
function formatLocation(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.idlocation + "   <i class='fa fa-circle-o'></i>   " + repo.nmlocation + "</div>";
    return markup;
}

function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}

//ID LOCATION PEMILIHAN GUDANG TUJUAN
var defaultInitialLocationTo = '';
$("#idlocation_to").select2({
    placeholder: " -- Pilih Gudang Tujuan -- ",
    allowClear: true,
    // minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _search_: params.term, // search term
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
                // manually trigger the `select2:select` event
                $('#idlocation_to').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
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
        cache: true
    },
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    /*Sementara TUtup Location */
});


//ID LOCATION PEMILIHAN GUDANG TUJUAN
var defaultInitialLocationTransit = '';
$("#idlocation_transit").select2({
    placeholder: " -- Pilih Gudang Tujuan -- ",
    allowClear: true,
    // minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _search_: params.term, // search term
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
                // manually trigger the `select2:select` event
                $('#idlocation_transit').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
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
        cache: true
    },
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    /*Sementara TUtup Location */
});


function btnDeleteDetail() {

    const ids = getCheckedDetailIds();

    // ===============================
    // Validasi pilihan
    // ===============================
    if (!ids || ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih minimal satu data yang akan dihapus'
        });
        return;
    }

    // ===============================
    // Konfirmasi Hapus
    // ===============================
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

        // ===============================
        // AJAX DELETE
        // ===============================
        $.ajax({
            url: HOST_URL + 'persediaan/trans/delete_pmk_brng',
            type: 'POST',
            data: {
                ids: ids   // kirim array id
            },
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

                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: res.message || 'Gagal menghapus data'
                    });
                }
            },

            error: function (xhr) {
                console.error(xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan server'
                });
            }
        });

    });
}

/*        ---------------------              ID COSTCENTER ----------------------------------------------*/

//ID LOCATION PEMILIHAN GUDANG TUJUAN
var defaultInitialCostcenter = '';
$("#idcostcenter").select2({
    placeholder: " -- Pilih Bagian/Costcenter -- ",
    allowClear: true,
    // minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_costcenter',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialCostcenter,
                _parameterx_: defaultInitialCostcenter,
                term: params.term,
            };
        },
        processResults: function (data, params) {

            var searchTerm = $("#idcostcenter").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmcostcenter, data.items[0].idcostcenter, true, true);
                $('#idcostcenter').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#idcostcenter').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
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
        cache: true
    },
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCostcenter, // omitted for brevity, see the source of this page
    templateSelection: formatCostcenterSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    /*Sementara TUtup Location */
});

function formatCostcenter(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.idcostcenter + "   <i class='fa fa-circle-o'></i>   " + repo.nmcostcenter + "</div>";
    return markup;
}

function formatCostcenterSelection(repo) {
    return repo.nmcostcenter || repo.text;
}


$(document).ready(function () {
    // Handle form submission event
    // Fix SweetAlert input tidak bisa diketik di atas Bootstrap modal
    if ($.fn.modal) {
        $.fn.modal.Constructor.prototype._enforceFocus = function () {
        };
    }


    // Fix SweetAlert input tidak bisa diketik di atas Bootstrap modal


    table_trx_pmk_brng_mst();
    tabletmpSPKDetail();
    // tableItem();
    //read_qrcode();
    $('#checkboxnik').change(function () {
        // this will contain a reference to the checkbox
        if (this.checked) {
            var valnik = $('#nik').val();
            // the checkbox is now checked
            //alert('Checked');
            $('#username').prop('readonly', true);
            $('#username').val(valnik);
        } else {
            // the checkbox is now no longer checked
            //alert('Un Checked');
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
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);
    // if ($('[name="type"]').val() === 'EDIT') {
    //     documentReadable();
    // }
    //console.log($('[name="type"]').val());
    // if ($('[name="typeform"]').val() === 'INPUT' || $('[name="typeform"]').val() === 'UPDATE' || $('[name="typeform"]').val() === 'DELETE' ) {
    documentReadable();
    // }
    $("#loadMe").modal("hide");


});