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
let skipRoleChange = false;
//"use strict";

/* VIUW UTAMA*/
function table_mst_standart_cost() {
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tstandart_cost');
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
                "url": HOST_URL + 'production/trans/list_standart_cost_mst',
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

function reload_standart_cost() {
    var table = $('#tstandart_cost');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function () { //button filter event click
    var table = $('#tstandart_cost');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function () { //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tstandart_cost');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

/*-------- PENTING DI SETIAP MENU ---------*/


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
            $('#prefix').val('HSC').trigger('change');

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
        url: HOST_URL + '/production/trans/getNextSuffix_standart_cost_mst',
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

/*-------- PENTING DI SETIAP MENU ---------*/

function documentReadable() {

    //showLoader();

    var docno = $('[name="docno"]').val();

    $.getJSON(HOST_URL + 'production/trans/showing_tmp_standart_cost_mst', {docno: docno})
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
            $('[name="activedate"]').val(item.activedate).prop('disabled', true);
            $('[name="docref"]').val(item.docref).prop('disabled', false);
            $('[name="keterangan"]').val(item.description);

            skipRoleChange = true;

            // ===============================
            // LOAD SEMUA MASTER DATA PARALEL
            // ===============================


            const branch1 = $.getJSON(
                HOST_URL + 'api/globalmodule/list_branchjob',
                { var: item.cabang }
            );

            branch1.done(function (res) {

                if (res.items && res.items.length > 0) {

                    let data = res.items[0];

                    // set option ke select2

                    let el = $('[name="cabang"]');

// set value
                    let option = new Option(data.nmbranch, data.idbranch, true, true);
                    el.empty().append(option).trigger('change');

// 1. cegah dropdown kebuka
                    el.on('select2:opening select2:unselecting select2:clearing', function (e) {
                        e.preventDefault();
                    });

// 2. disable tombol clear (kalau ada)
                    el.data('select2').$container.find('.select2-selection__clear').remove();

// 3. matikan interaksi UI (super lock)
                    el.next('.select2-container').css({
                        'pointer-events': 'none',
                        'background': '#e9ecef'
                    });

                }

            }).always(function () {
                hideLoader();
            });
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


$('#btnAddDetail').on('click', function (e) {
    btnInputDetail();
});
function btnInputDetail() {
    console.log(" INI KENAPA DI SISI ");

    $('#formStandartCostDtl')[0].reset();
    // Clear select2
    $('[name="actualcost"]').prop("readonly", true);
    $('[name="lastcost"]').prop("readonly", true);
    $('#idbarang').val(null).trigger('change');
    $('#idurut').val('');
    $('#modalDtlStandartCostTitle').text('Tambah Item Detail');
    $('#modalDtlStandartCost').modal('show');
}

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

    var data = e.params.data;

    $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    $('[name="actualcost"]').val().prop("readonly", true);
    $('[name="lastcost"]').val().prop("readonly", true);

});

function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}


$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});


function formatItem(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.idbarang + "   <i class='fa fa-circle-o'></i>   " + repo.nmbarang + "</div>";
    return markup;
}

function formatItemSelection(repo) {
    return repo.nmbarang || repo.text;
}



function save_standart_cost() {

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
        let activedate = $('#activedate').val();
        let qtyactualcost = $('#actualcost').val();
        let qtylastcost = $('#lastcost').val();
        let qtynewcost = $('#newcost').val();



        let actualCost = parseFloat(convertToDbNumber(qtyactualcost)) || 0;
        let lastCost = parseFloat(convertToDbNumber(qtylastcost)) || 0;
        let newCOst = parseFloat(convertToDbNumber(qtynewcost)) || 0;



        // ===============================
        // Siapkan FormData
        // ===============================

        let formData = new FormData(document.getElementById('formStandartCostDtl'));



        formData.append('docref', $('#docref').val());
        formData.append('cabang', $('#cabang').val());
        formData.append('pemohon', $('#pemohon').val());
        formData.append('docno', $('#docno').val());
        formData.append('docdate', $('#docdate').val());
        formData.append('activedate', $('#activedate').val());
        formData.append('penyesuaian_a', $('#penyesuaian_a').val());
        formData.append('penyesuaian_b', $('#penyesuaian_b').val());
        formData.append('idcostcenter', $('#idcostcenter').val());
        formData.append('keterangan', $('#keterangan').val());

        formData.append('idlocation', $('#idlocation').val());
        formData.append('description', $('#description').val());

        // Gabungkan docno
        formData.set(
            'docno',
            $('#prefix').val() + '/' +
            $('#infix').val() + '/' +
            $('#sufix').val()
        );

        // Set numeric value yang sudah divalidasi
        formData.set('actualcost', actualCost);
        formData.set('lastcost', lastCost);
        formData.set('newcost', newCOst);

        if (!activedate || activedate.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi',
                text: 'Activate Date tidak boleh kosong!'
            });
            $('#activedate').focus();
            return;
        }
        // ===============================
        // AJAX SAVE
        // ===============================

        $.ajax({
            url: HOST_URL + 'production/trans/save_standart_cost_mst',
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
                        text: res.message || 'Data Saved Success fully',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    if (res.reload === true) {
                        window.location.reload();
                        return;
                    }

                    $('#modalDtlStandartCost').modal('hide');
                    reload_standart_cost_dtl();
                    $('#formStandartCostDtl')[0].reset();

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




/* TABLE PP DETAIL */
function tabletmpStandartCost() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#tmp_stdcost');
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
                "url": HOST_URL + 'production/trans/list_tmp_standart_cost_dtl',
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
            ],
            // Di dalam konfigurasi DataTable Anda:
            "drawCallback": function(settings) {
                // Panggil fungsi manual tadi
                // updateGrandTotal();
            }
        });
    }

    return initTable();

}


function reload_standart_cost_dtl() {
    var table = $('#tmp_stdcost');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#tmp_stdcost thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tmp_stdcost tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tmp_stdcost tbody').on('change', '.row-check', function () {
    const total = $('#tmp_stdcost tbody .row-check').length;
    const checked = $('#tmp_stdcost tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tmp_stdcost').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});




function updateStandartCost() {
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
        url: HOST_URL + 'production/trans/get_standart_cost_dtl',
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
            data.actualcost       = (data.actualcost || '').trim();
            data.lastcost       = (data.lastcost || '').trim();
            data.newcost       = (data.newcost || '').trim();

            /*
            =========================
            SET FORM
            =========================
            */

            $('#idurut').val(data.idurut);
            $('#description_detail').val(data.description);
            $('#docno').val(data.docno);
            $('#dk').val(data.dk).trigger('change');


            $('#nmbarang').val(data.nmbarang);
            $('#unit').val(data.unit);
// Contoh implementasi saat data diterima dari AJAX

            // $('#qty').val(data.qty);
            // $('#val').val(data.val);
            // $('#valsum').val(data.valsum);

            // Mengisi field secara otomatis dengan separator dari separator
            setJtsValue('#actualcost', data.actualcost);
            setJtsValue('#lastcost', data.lastcost);
            setJtsValue('#newcost', data.newcost);


//             $('#qty').val(_jtsseparator(data.qty));
//             $('#val').val(_jtsseparator(data.val));
//             $('#valsum').val(_jtsseparator(data.valsum));
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
            $('#modalDtlStandartCostTitle').text('Update Detail');
            $('#modalDtlStandartCost').modal('show');



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


function getCheckedDetailIds() {
    let ids = [];
    $('.row-check:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}


$(document).ready(function () {

    table_mst_standart_cost();
    documentReadable();
    tabletmpStandartCost();

});