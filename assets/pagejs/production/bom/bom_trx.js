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
function table_trx_bom() {
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tbom');
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
                "url": HOST_URL + 'production/trans/list_bom_mst',
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

function reload_bom() {
    var table = $('#tbom');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function () { //button filter event click
    var table = $('#tbom');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function () { //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tbom');
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
        url: HOST_URL + '/production/trans/getNextSuffix_bom_mst',
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

    $.getJSON(HOST_URL + 'production/trans/showing_mst_bom_mst', {docno: docno})
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
            setJtsValue('[name="buildfor"]', convertToDbNumber(item.buildfor));
            setJtsValue('[name="minimumqty"]', convertToDbNumber(item.minimumqty));
            $('[name="keterangan"]').val(item.keterangan).prop('readonly', true);
            $('[name="buildfor"]').prop('readonly',true);
            $('[name="minimumqty"]').prop('readonly',true);
            skipRoleChange = true;
            $('#ttlprice').text(
                convertToDbNumber(item.ttlprice || 0)
                    .toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
            );
            $('#ttlmaterial').text(
                convertToDbNumber(item.ttlmaterial || 0)
                    .toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
            );
            $('#ttlcost').text(
                convertToDbNumber(item.ttlcost || 0)
                    .toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
            );
            $('#ttlwip').text(
                convertToDbNumber(item.ttlwip || 0)
                    .toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
            );
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_item' + '?var=' + item.idbarang_jadi,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                $('[name="idbarang_jadi"]').append(option).trigger('change').prop('disabled',true);

                // manually trigger the `select2:select` event
                $('[name="idbarang_jadi"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_unit' + '?var=' + item.buildunit,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
                $('[name="buildunit"]').append(option).trigger('change').prop('disabled',true);

                // manually trigger the `select2:select` event
                $('[name="buildunit"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_branchjob' + '?var=' + item.cabang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmbranch, datax.items[0].idbranch, true, true);
                $('[name="cabang"]').append(option).trigger('change').prop('disabled',true);

                // manually trigger the `select2:select` event
                $('[name="cabang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            // ===============================
            // LOAD SEMUA MASTER DATA PARALEL
            // ===============================


//             const branch1 = $.getJSON(
//                 HOST_URL + 'api/globalmodule/list_branchjob',
//                 { var: item.cabang }
//             );

//             branch1.done(function (res) {

//                 if (res.items && res.items.length > 0) {

//                     let data = res.items[0];

//                     // set option ke select2

//                     let el = $('[name="cabang"]');

// // set value
//                     let option = new Option(data.nmbranch, data.idbranch, true, true);
//                     el.empty().append(option).trigger('change');

// // 1. cegah dropdown kebuka
//                     el.on('select2:opening select2:unselecting select2:clearing', function (e) {
//                         e.preventDefault();
//                     });

// // 2. disable tombol clear (kalau ada)
//                     el.data('select2').$container.find('.select2-selection__clear').remove();

// // 3. matikan interaksi UI (super lock)
//                     el.next('.select2-container').css({
//                         'pointer-events': 'none',
//                         'background': '#e9ecef'
//                     });

//                 }

//             }).always(function () {
//                 hideLoader();
//             });
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

    console.log("INI KENAPA DI SISI");

    // reset form
    $('#formStandartCostDtl')[0].reset();

    // readonly
    $('[name="actualcost"]').prop("readonly", true);
    $('[name="lastcost"]').prop("readonly", true);

    // reset select2
    $('#idbarang').val(null).trigger('change');

    // reset idurut
    $('#idurut').val('');

    // title modal
    $('#modalDtlStandartCostTitle').text('Tambah Item Detail');

/*    // destroy jika sudah pernah init
    if ($('#idbarang').hasClass("select2-hidden-accessible")) {
        $('#idbarang').select2('destroy');
    }*/



    // show modal
    $('#modalDtlStandartCost').modal('show');
}

var defaultInitialGroupBrng = '';
$("#idbarang").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    dropdownParent: $('#modalDtlStandartCost'),
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

    let $el = $(selector);

    if ($el.is('input, select, textarea')) {

        $el.val(value);

        if ($el[0]) {
            _jtsseparator($el[0]);
        }

    } else {

        $el.text(value);

    }
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



function save_bom() {


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
            url: HOST_URL + 'production/trans/save_bom_mst',
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
                        timer: 800,
                        showConfirmButton: false
                    });

                    if (res.reload === true) {
                        window.location.reload();
                        return;
                    }

                    $('#modalDtlStandartCost').modal('hide');
                    reload_bom_dtl();
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


}




/* TABLE PP DETAIL */
function tabletrxBOMMaterial() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#trx_bommaterialdtl');
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
                "url": HOST_URL + 'production/trans/list_trx_bom_material_dtl',
                "type": "POST",
                "data": function (data) {
                    data.docno = $('#docno').val();
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


function reload_bom_material_dtl() {
    var table = $('#trx_bommaterialdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#trx_bommaterialdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#trx_bommaterialdtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#trx_bommaterialdtl tbody').on('change', '.row-check', function () {
    const total = $('#trx_bommaterialdtl tbody .row-check').length;
    const checked = $('#trx_bommaterialdtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#trx_bommaterialdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});


$('#checkAll').on('change', function () {
    $('.row-check').prop('checked', this.checked);
});

// auto uncheck checkAll jika salah satu dilepas
$(document).on('change', '.row-check', function () {
    if (!this.checked) {
        $('#checkAll').prop('checked', false);
    }
});


/* TABLE PP DETAIL */
function tabletrxBOMCost() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#trx_bomcostdtl');
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
                "url": HOST_URL + 'production/trans/list_trx_bom_cost_dtl',
                "type": "POST",
                "data": function (data) {
                    data.docno = $('#docno').val();
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
                        return '<input type="checkbox" class="row-check-cost" value="' + row[0] + '">';
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


function reload_bom_cost_dtl() {
    var table = $('#trx_bomcostdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#trx_bomcostdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#trx_bomcostdtl tbody .row-check-cost').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#trx_bomcostdtl tbody').on('change', '.row-check-cost', function () {
    const total = $('#trx_bomcostdtl tbody .row-check-cost').length;
    const checked = $('#trx_bomcostdtl tbody .row-check-cost:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#trx_bomcostdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});


$('#checkAllCost').on('change', function () {
    $('.row-check-cost').prop('checked', this.checked);
});

// auto uncheck checkAll jika salah satu dilepas
$(document).on('change', '.row-check-cost', function () {
    if (!this.checked) {
        $('#checkAllCost').prop('checked', false);
    }
});


/* TABLE PP DETAIL */
function tabletrxBOMWip() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#trx_bomwipdtl');
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
                "url": HOST_URL + 'production/trans/list_trx_bom_wip_dtl',
                "type": "POST",
                "data": function (data) {
                    data.docno = $('#docno').val();
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
                        return '<input type="checkbox" class="row-check-wip" value="' + row[0] + '">';
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


function reload_bom_wip_dtl() {
    var table = $('#trx_bomwipdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#trx_bomwipdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#trx_bomwipdtl tbody .row-check-wip').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#trx_bomwipdtl tbody').on('change', '.row-check-wip', function () {
    const total = $('#trx_bomwipdtl tbody .row-check-wip').length;
    const checked = $('#trx_bomwipdtl tbody .row-check-wip:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#trx_bomwipdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});



$('#checkAllWip').on('change', function () {
    $('.row-check-wip').prop('checked', this.checked);
});

// auto uncheck checkAll jika salah satu dilepas
$(document).on('change', '.row-check-wip', function () {
    if (!this.checked) {
        $('#checkAllWip').prop('checked', false);
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

    table_trx_bom();
    documentReadable();
    tabletrxBOMMaterial();
    tabletrxBOMCost();
    tabletrxBOMWip();

});