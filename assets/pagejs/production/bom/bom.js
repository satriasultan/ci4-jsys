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
function table_bom() {
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
        url: HOST_URL + '/production/trans/getBranchInfo_bom',
        method: 'GET',
        data: {idbranch: idbranch},
        dataType: 'json',
        success: function (res) {

            if (!res.success) {
                Swal.fire('Error', res.message, 'warning');
                return;
            }

            currentKodeSuffix = res.kode_suffix;
            $('#infix').val(res.infix);          // YYMM
            var prefix = res.prefix;
            $('#prefix').val(prefix);
            loadNextSuffixBOM()

            var infix = (res.infix || '').toString();

            if (infix.length === 4) {
                $('#docdate').prop('disabled', false);
                var yy = infix.substring(0, 2);
                var mm = infix.substring(2, 4);
                var year = 2000 + parseInt(yy, 10);
                var month = parseInt(mm, 10) - 1;

                // Default: logindate dari server, fallback ke hari ini
                var logindate = res.logindate ? moment(res.logindate, 'DD-MM-YYYY') : moment();

                var startDate = moment([year, month, 1]);
                var endDate = moment(startDate).endOf('month');

                // Kalau logindate di luar range, pakai startDate
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


function loadNextSuffixBOM() {
    
    let prefix = $.trim($('#prefix').val()).toUpperCase();
    let infix = $.trim($('#infix').val());

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

            
            let suffix = $.trim(
                res.suffix || ''
            );

            $('#suffix')
                .val(suffix)
                .trigger('change');

            $('#docno').val(
                prefix + '/' + infix + '/' + res.suffix
            );

        }
    });

};



$('#prefix').on('blur', function () {
    loadNextSuffixBOM();
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

    $.getJSON(HOST_URL + 'production/trans/showing_tmp_bom_mst', {docno: docno})
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
            $('[name="suffix"]').val(prefixParts[2]).prop('readonly', true);
            loadNextSuffixBOM()
            setJtsValue('[name="buildfor"]', convertToDbNumber(item.buildfor));
            setJtsValue('[name="minimumqty"]', convertToDbNumber(item.minimumqty));
            $('[name="docdate"]')
                .val(moment(item.docdate).format('DD-MM-YYYY'))
                .prop('disabled', true);

            // $('[name="activedate"]').val(item.activedate).prop('disabled', true);
            // $('[name="docref"]').val(item.docref).prop('disabled', false);
            $('[name="keterangan"]').val(item.keterangan);
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
                $('[name="idbarang_jadi"]').append(option).trigger('change');

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
                $('[name="buildunit"]').append(option).trigger('change');

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

            skipRoleChange = true;

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


$('#btnAddDetailMaterial').on('click', function (e) {
    btnInputDetailMaterial();
});
function btnInputDetailMaterial() {

    let cabang = $('#cabang').val();

    if (!cabang || cabang.trim() === '') {
        Swal.fire({
            title: 'Peringatan',
            text: 'Cabang harus dipilih terlebih dahulu.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });

        $('#cabang').focus();
        return;
    }
    // reset form
    $('#formBOMMaterialDtl')[0].reset();

    // readonly
    // $('[name="actualcost"]').prop("readonly", true);
    // $('[name="lastcost"]').prop("readonly", true);

    // reset select2
    $('#idbarangMaterial').val(null).trigger('change');

    // reset idurut
    $('#idurut').val('');
    $('#uniqueidMaterial').val('');
    // title modal
    $('#modalDtlBomMaterialTitle').text('Tambah Detail Material');

/*    // destroy jika sudah pernah init
    if ($('#idbarang').hasClass("select2-hidden-accessible")) {
        $('#idbarang').select2('destroy');
    }*/



    // show modal
    $('#modalDtlBomMaterial').modal('show');
}




$('#btnAddDetailCost').on('click', function (e) {
    btnInputDetailCost();
});

function btnInputDetailCost() {

    let cabang = $('#cabang').val();

    if (!cabang || cabang.trim() === '') {
        Swal.fire({
            title: 'Peringatan',
            text: 'Cabang harus dipilih terlebih dahulu.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });

        $('#cabang').focus();
        return;
    }
    // reset form
    $('#formBOMCostDtl')[0].reset();

    // readonly
    // $('[name="actualcost"]').prop("readonly", true);
    // $('[name="lastcost"]').prop("readonly", true);

    // reset select2
    $('#idbarangCost').val(null).trigger('change');

    // reset idurut
    $('#idurut').val('');

    $('#uniqueidCost').val('');
    // title modal
    $('#modalDtlBomCostTitle').text('Tambah Detail Cost');

/*    // destroy jika sudah pernah init
    if ($('#idbarang').hasClass("select2-hidden-accessible")) {
        $('#idbarang').select2('destroy');
    }*/



    // show modal
    $('#modalDtlBomCost').modal('show');
}




$('#btnAddDetailWip').on('click', function (e) {
    btnInputDetailWip();
});

function btnInputDetailWip() {

    let cabang = $('#cabang').val();

    if (!cabang || cabang.trim() === '') {
        Swal.fire({
            title: 'Peringatan',
            text: 'Cabang harus dipilih terlebih dahulu.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });

        $('#cabang').focus();
        return;
    }
    // reset form
    $('#formBOMWipDtl')[0].reset();

    // readonly
    // $('[name="actualcost"]').prop("readonly", true);
    // $('[name="lastcost"]').prop("readonly", true);

    // reset select2
    $('#idbarangWip').val(null).trigger('change');

    // reset idurut
    $('#idurut').val('');
    $('#uniqueidWip').val('');
    // title modal
    $('#modalDtlBomWipTitle').text('Tambah Detail Wip');

/*    // destroy jika sudah pernah init
    if ($('#idbarang').hasClass("select2-hidden-accessible")) {
        $('#idbarang').select2('destroy');
    }*/



    // show modal
    $('#modalDtlBomWip').modal('show');
}


function setSelect2Ajax(selector, value, text) {
    if (!value) return;

    let option = new Option(text || value, value, true, true);
    $(selector).append(option).trigger('change');
}


function updateDetailBomMaterial(){
    const ids = getCheckedDetailIds();
    console.log('IDS:', ids);

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih satu data yang akan diupdate'
        });
        return;
    }

    if(ids.length > 1){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Update hanya boleh satu data'
        });
        return;
    }

    const id = ids[0];

    $.ajax({
        url: HOST_URL + 'production/trans/get_bom_dtl',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){
                $('#uniqueidMaterial').val(res.items[0].uniqueid);
                $('#idurutMaterial').val(res.items[0].idurut);
                $('#description_detail_material').val(res.items[0].description);
                $('#docno').val(res.items[0].docno);
                $('#idbarangMaterial').val(res.items[0].idbarang);
                $('#nmbarangmaterial').val(res.items[0].nmbarang);
                $('#unitMaterial').val(res.items[0].unit);
                // $('#qtymaterial').val(res.items[0].qty);
                setJtsValue('[name="qtymaterial"]', convertToDbNumber(res.items[0].qty));
                setJtsValue('[name="standartcostmaterial"]', convertToDbNumber(res.items[0].standartcost));
                setJtsValue('[name="totalcostmaterial"]', convertToDbNumber(res.items[0].totalcost));
                // $('#standartcostmaterial').val(res.items[0].standartcost);
                // $('#totalcostmaterial').val(res.items[0].totalcost);
                // setSelect2Ajax('#idbarangMaterial', res.items[0].idbarang, res.items[0].idbarang);

                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_item_std_cost' + '?var=' + res.items[0].idbarang.trim(),
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                    $('[name="idbarangMaterial"]').append(option).trigger('change')

                    // manually trigger the `select2:select` event
                    $('[name="idbarangMaterial"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });
                $('#modalDtlBomMaterialTitle').text('Update Detail Material');
                $('#modalDtlBomMaterial').modal('show');

            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Data tidak ditemukan'
                });
            }
        }
    });
}




function updateDetailBomCost(){
    const ids = getCheckedDetailIdsCost();
    console.log('IDS:', ids);

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih satu data yang akan diupdate'
        });
        return;
    }

    if(ids.length > 1){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Update hanya boleh satu data'
        });
        return;
    }

    const id = ids[0];

    $.ajax({
        url: HOST_URL + 'production/trans/get_bom_dtl',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){
                $('#uniqueidCost').val(res.items[0].uniqueid);
                $('#idurutCost').val(res.items[0].idurut);
                $('#description_detail_cost').val(res.items[0].description);
                $('#docno').val(res.items[0].docno);
                $('#idbarangCost').val(res.items[0].idbarang);
                $('#nmbarangcost').val(res.items[0].nmbarang);
                $('#unitCost').val(res.items[0].unit);
                // $('#qtycost').val(res.items[0].qty);
                setJtsValue('[name="qtycost"]', convertToDbNumber(res.items[0].qty));
                setJtsValue('[name="standartcostcost"]', convertToDbNumber(res.items[0].standartcost));
                setJtsValue('[name="totalcostcost"]', convertToDbNumber(res.items[0].totalcost));
                // $('#standartcostcost').val(res.items[0].standartcost);
                // $('#totalcostcost').val(res.items[0].totalcost);
                // setSelect2Ajax('#idbarangCost', res.items[0].idbarang, res.items[0].idbarang);

                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_item_std_cost' + '?var=' + res.items[0].idbarang.trim(),
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                    $('[name="idbarangCost"]').append(option).trigger('change')

                    // manually trigger the `select2:select` event
                    $('[name="idbarangCost"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });
                $('#modalDtlBomCostTitle').text('Update Detail Cost');
                $('#modalDtlBomCost').modal('show');

            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Data tidak ditemukan'
                });
            }
        }
    });
}




function updateDetailBomWip(){
    const ids = getCheckedDetailIdsWip();
    console.log('IDS:', ids);

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih satu data yang akan diupdate'
        });
        return;
    }

    if(ids.length > 1){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Update hanya boleh satu data'
        });
        return;
    }

    const id = ids[0];

    $.ajax({
        url: HOST_URL + 'production/trans/get_bom_dtl',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                $('#uniqueidWip').val(res.items[0].uniqueid);
                $('#idurutWip').val(res.items[0].idurut);
                $('#description_detail_wip').val(res.items[0].description);
                $('#docno').val(res.items[0].docno);
                $('#idbarangWip').val(res.items[0].idbarang);
                $('#nmbarangwip').val(res.items[0].nmbarang);
                $('#unitWip').val(res.items[0].unit);
                // $('#qtywip').val(res.items[0].qty);
                setJtsValue('[name="qtywip"]', convertToDbNumber(res.items[0].qty));
                setJtsValue('[name="standartcostwip"]', convertToDbNumber(res.items[0].standartcost));
                setJtsValue('[name="totalcostwip"]', convertToDbNumber(res.items[0].totalcost));
                // $('#standartcostwip').val(res.items[0].standartcost);
                // $('#totalcostwip').val(res.items[0].totalcost);
                // setSelect2Ajax('#idbarangWip', res.items[0].idbarang, res.items[0].idbarang);

                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_item_std_cost' + '?var=' + res.items[0].idbarang.trim(),
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                    $('[name="idbarangWip"]').append(option).trigger('change')

                    // manually trigger the `select2:select` event
                    $('[name="idbarangWip"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });
                $('#modalDtlBomWipTitle').text('Update Detail Wip');
                $('#modalDtlBomWip').modal('show');

            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Data tidak ditemukan'
                });
            }
        }
    });
}


function btnDeleteDetailMaterial(){
    const ids = getCheckedDetailIds();

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih data yang akan dihapus'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin hapus ' + ids.length + ' data terpilih?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if(!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'production/trans/delete_bom_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json',
            success: function(res){
                if(res.status){

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    documentReadable()
                    $('#tmp_bommaterialdtl').DataTable().ajax.reload(null,false);

                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Gagal hapus data'
                    });
                }
            }
        });

    });
}



function btnDeleteDetailCost(){
    const ids = getCheckedDetailIdsCost();

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih data yang akan dihapus'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin hapus ' + ids.length + ' data terpilih?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if(!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'production/trans/delete_bom_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json',
            success: function(res){
                if(res.status){

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    documentReadable()
                    $('#tmp_bomcostdtl').DataTable().ajax.reload(null,false);

                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Gagal hapus data'
                    });
                }
            }
        });

    });
}





function btnDeleteDetailWip(){
    const ids = getCheckedDetailIdsWip();

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih data yang akan dihapus'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin hapus ' + ids.length + ' data terpilih?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if(!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'production/trans/delete_bom_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json',
            success: function(res){
                if(res.status){

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    documentReadable()
                    $('#tmp_bomwipdtl').DataTable().ajax.reload(null,false);

                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Gagal hapus data'
                    });
                }
            }
        });

    });
}

var defaultInitialGroupBrng = 'BRG';
$("#idbarangMaterial").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    // minimumInputLength: 2,
    dropdownParent: $('#modalDtlBomMaterial .modal-body'),
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item_std_cost',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            var docdate = $('#docdate').val();
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialGroupBrng,
                _parameterx_: defaultInitialGroupBrng,
                loccode: '',
                docdate:  docdate,
                //loccode: $('[name="idlocation_dtl"]').val(),
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

    $('[name="nmbarangmaterial"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unitMaterial"]').val(data.unit.trim()).prop("readonly", true);
    setJtsValue('[name="standartcostmaterial"]', convertToDbNumber(data.newcost));
    $('[name="totalcostmaterial"]').val().prop("readonly", true);

});



var defaultInitialGroupJSA = 'JSA';
$("#idbarangCost").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    // minimumInputLength: 2,
    dropdownParent: $('#modalDtlBomCost .modal-body'),
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item_std_cost',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            var docdate = $('#docdate').val();
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialGroupJSA,
                _parameterx_: defaultInitialGroupJSA,
                loccode: '',
                docdate:  docdate,
                //loccode: $('[name="idlocation_dtl"]').val(),
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

    $('[name="nmbarangcost"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unitCost"]').val(data.unit.trim()).prop("readonly", true);
    setJtsValue('[name="standartcostcost"]', convertToDbNumber(data.newcost));
    $('[name="totalcostcost"]').val().prop("readonly", true);

});





var defaultInitialGroupWIP = 'WIP';
$("#idbarangWip").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    minimumInputLength: 2,
    dropdownParent: $('#modalDtlBomWip .modal-body'),
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item_std_cost',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            var docdate = $('#docdate').val();
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialGroupWIP,
                _parameterx_: defaultInitialGroupWIP,
                loccode: '',
                docdate:  docdate,
                //loccode: $('[name="idlocation_dtl"]').val(),
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

    $('[name="nmbarangwip"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unitWip"]').val(data.unit.trim()).prop("readonly", true);
    setJtsValue('[name="standartcostwip"]', convertToDbNumber(data.newcost));
    $('[name="totalcostwip"]').val().prop("readonly", true);

});




var defaultIdbarangJadi = '';
$("#idbarang_jadi").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    minimumInputLength: 2,
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
                _paramglobal_: defaultIdbarangJadi,
                _parameterx_: defaultIdbarangJadi,
                loccode: '',
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
    // $('[name="nmbarangmaterial"]').val(data.nmbarang.trim()).prop("readonly", true);

});




var defaultInitialUnit = 'UNIT';
$("#buildunit").select2({
    placeholder: "Type/Chose Your Unit",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_unit',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialUnit,
                _parameterx_: defaultInitialUnit,
            };
        },
        processResults: function(data, params) {
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
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatUnit, // omitted for brevity, see the source of this page
    templateSelection: formatUnitSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {

});


function hitungTotalCostMaterial() {

    let qty = $('#qtymaterial').val().replace(/,/g, '');
    let cost = $('#standartcostmaterial').val().replace(/,/g, '');

    qty = parseFloat(qty) || 0;
    cost = parseFloat(cost) || 0;

    let total = qty * cost;

    $('#totalcostmaterial').val(
        total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
    );
}


function hitungTotalCostCost() {

    let qty = $('#qtycost').val().replace(/,/g, '');
    let cost = $('#standartcostcost').val().replace(/,/g, '');

    qty = parseFloat(qty) || 0;
    cost = parseFloat(cost) || 0;

    let total = qty * cost;

    $('#totalcostcost').val(
        total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
    );
}




function hitungTotalCostWip() {

    let qty = $('#qtywip').val().replace(/,/g, '');
    let cost = $('#standartcostwip').val().replace(/,/g, '');

    qty = parseFloat(qty) || 0;
    cost = parseFloat(cost) || 0;

    let total = qty * cost;

    $('#totalcostwip').val(
        total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
    );
}


/* Format formatUnit */
function formatUnit(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idunit +"</div>";
    return markup;
}

function formatUnitSelection(repo) {
    return repo.idunit || repo.text;
}


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







function setToReleased(docno) {
    if (!guardPeriodeTutup()) return;

    Swal.fire({
        title: 'Set BOM menjadi Release?',
        text: "Status dokumen akan diubah menjadi Release",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/production/trans/updateStatusBOM',
                type: 'POST',
                data: { docno: docno, status: 'A' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi Release'
                        }).then(() => {
                            reload_bom()
                            // reload_tableBOMApprvTrx()
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
                }
            });
        }
    });
}

function setToUnreleased(docno) {
    if (!guardPeriodeTutup()) return;

    Swal.fire({
        title: 'Set BOM menjadi Unreleased?',
        text: "Status dokumen akan diubah menjadi Unreleased",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/production/trans/updateStatusBOM',
                type: 'POST',
                data: { docno: docno, status: 'F' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi Unreleased'
                        }).then(() => {
                            reload_bom()
                            // reload_tableBOMApprvTrx()
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
                }
            });
        }
    });
}


function saveBomDetail(formId, detailType) {


        // ===============================
        // Ambil dan validasi qty
        // ===============================
        let buildfor = $('#buildfor').val();
        let minimumqty = $('#minimumqty').val();

        // ===============================
        // Siapkan FormData
        // ===============================

        let formData = new FormData(document.getElementById(formId));

        //HEADER
        formData.append('docref', $('#docref').val());
        formData.append('cabang', $('#cabang').val());
        formData.append('pemohon', $('#pemohon').val());
        formData.append('docno', $('#docno').val());
        formData.append('docdate', $('#docdate').val());
        formData.append('idbarang_jadi', $('#idbarang_jadi').val());
        formData.append('docdate', $('#docdate').val());
        formData.append('keterangan', $('#keterangan').val());
        formData.append('minimumqty', convertToDbNumber(minimumqty));
        formData.append('buildunit', $('#buildunit').val());
        formData.append('buildfor', convertToDbNumber(buildfor));
        // formData.append('minimumqty', $('#minimumqty').val());
        formData.append('keterangan', $('#keterangan').val());
        //DETAIL
        // formData.append('idbarangMaterial', $('#idbarangMaterial').val());
        // formData.append('nmbarangmaterial', $('#nmbarangmaterial').val());
        // formData.append('unitMaterial', $('#unitMaterial').val());
        // formData.set('qtymaterial', convertToDbNumber(qtymaterial));
        // formData.set('standartcostmaterial', convertToDbNumber(standartcostmaterial));
        // formData.set('totalcostmaterial', convertToDbNumber(totalcostmaterial));
        // formData.append('description_detail_material', $('#description_detail_material').val());

        if(detailType === 'MATERIAL'){
            let qtymaterial = $('#qtymaterial').val();
            let standartcostmaterial = $('#standartcostmaterial').val();
            let totalcostmaterial = $('#totalcostmaterial').val();
            formData.set('qtymaterial',convertToDbNumber($('#qtymaterial').val()));
            formData.set('standartcostmaterial',convertToDbNumber($('#standartcostmaterial').val()));
            formData.set('uniqueidMaterial', $('#uniqueidMaterial').val());
            formData.set('totalcostmaterial',convertToDbNumber($('#totalcostmaterial').val()));

        }else if(detailType === 'COST'){
            let qtycost = $('#qtycost').val();
            let standartcostcost = $('#standartcostcost').val();
            let totalcostcost = $('#totalcostcost').val();
            formData.set('qtycost',convertToDbNumber($('#qtycost').val()));
            formData.set('standartcostcost',convertToDbNumber($('#standartcostcost').val()));
            formData.set('uniqueidCost', $('#uniqueidCost').val());
            formData.set('totalcostcost',convertToDbNumber($('#totalcostcost').val()));
        }else if(detailType === 'WIP'){
            let qtywip = $('#qtywip').val();
            let standartcostwip = $('#standartcostwip').val();
            let totalcostwip = $('#totalcostwip').val();
            formData.set('qtywip',convertToDbNumber($('#qtywip').val()));
            formData.set('standartcostwip',convertToDbNumber($('#standartcostwip').val()));
            formData.set('uniqueidWip', $('#uniqueidWip').val());
            formData.set('totalcostwip',convertToDbNumber($('#totalcostwip').val()));
        }
        formData.append('doctype_detail', detailType);
        // Gabungkan docno
        formData.set(
            'docno',
            $('#prefix').val() + '/' +
            $('#infix').val() + '/' +
            $('#suffix').val()
        );

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

                    if(detailType === 'MATERIAL'){
                        $('#modalDtlBomMaterial').modal('hide');
                        reload_bom_material_dtl();
                        $('#formBOMMaterialDtl')[0].reset();
                    } else if(detailType === 'COST'){
                        $('#modalDtlBomCost').modal('hide');
                        reload_bom_cost_dtl();
                        $('#formBOMCostDtl')[0].reset();
                    } else if(detailType === 'WIP'){
                        $('#modalDtlBomWip').modal('hide');
                        reload_bom_wip_dtl();
                        $('#formBOMWipDtl')[0].reset();
                    }
                    documentReadable()

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
function tabletmpBOMMaterial() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#tmp_bommaterialdtl');
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
                "url": HOST_URL + 'production/trans/list_tmp_bom_material_dtl',
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

        $('#tmp_bommaterialdtl tbody').on('click', 'tr', function(e) {
            // Cegah jika yang diklik adalah checkbox itu sendiri (untuk menghindari double trigger)
            if ($(e.target).is('input[type="checkbox"]')) {
                return;
            }

            // Cari checkbox di dalam baris ini
            var checkbox = $(this).find('input[type="checkbox"].row-check');

            // Toggle status checkbox
            checkbox.prop('checked', !checkbox.prop('checked'));

            // Trigger event change jika diperlukan
            checkbox.trigger('change');
        });
    }

    return initTable();

}


function reload_bom_material_dtl() {
    var table = $('#tmp_bommaterialdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#tmp_bommaterialdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tmp_bommaterialdtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tmp_bommaterialdtl tbody').on('change', '.row-check', function () {
    const total = $('#tmp_bommaterialdtl tbody .row-check').length;
    const checked = $('#tmp_bommaterialdtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tmp_bommaterialdtl').on('draw.dt', function () {
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
function tabletmpBOMCost() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#tmp_bomcostdtl');
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
                "url": HOST_URL + 'production/trans/list_tmp_bom_cost_dtl',
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

         $('#tmp_bomcostdtl tbody').on('click', 'tr', function(e) {
            // Cegah jika yang diklik adalah checkbox itu sendiri (untuk menghindari double trigger)
            if ($(e.target).is('input[type="checkbox"]')) {
                return;
            }

            // Cari checkbox di dalam baris ini
            var checkbox = $(this).find('input[type="checkbox"].row-check');

            // Toggle status checkbox
            checkbox.prop('checked', !checkbox.prop('checked'));

            // Trigger event change jika diperlukan
            checkbox.trigger('change');
        });
    }

    return initTable();

}


function reload_bom_cost_dtl() {
    var table = $('#tmp_bomcostdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#tmp_bomcostdtl thead').on('change', '#checkAllCost', function () {
    const checked = this.checked;

    $('#tmp_bomcostdtl tbody .row-check-cost').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tmp_bomcostdtl tbody').on('change', '.row-check-cost', function () {
    const total = $('#tmp_bomcostdtl tbody .row-check-cost').length;
    const checked = $('#tmp_bomcostdtl tbody .row-check-cost:checked').length;

    $('#checkAllCost').prop('checked', total === checked);
});

$('#tmp_bomcostdtl').on('draw.dt', function () {
    $('#checkAllCost').prop('checked', false);
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
function tabletmpBOMWip() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#tmp_bomwipdtl');
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
                "url": HOST_URL + 'production/trans/list_tmp_bom_wip_dtl',
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


         $('#tmp_bomwipdtl tbody').on('click', 'tr', function(e) {
            // Cegah jika yang diklik adalah checkbox itu sendiri (untuk menghindari double trigger)
            if ($(e.target).is('input[type="checkbox"]')) {
                return;
            }

            // Cari checkbox di dalam baris ini
            var checkbox = $(this).find('input[type="checkbox"].row-check');

            // Toggle status checkbox
            checkbox.prop('checked', !checkbox.prop('checked'));

            // Trigger event change jika diperlukan
            checkbox.trigger('change');
        });
    }

    return initTable();

}


function reload_bom_wip_dtl() {
    var table = $('#tmp_bomwipdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#tmp_bomwipdtl thead').on('change', '#checkAllWip', function () {
    const checked = this.checked;

    $('#tmp_bomwipdtl tbody .row-check-wip').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tmp_bomwipdtl tbody').on('change', '.row-check-wip', function () {
    const total = $('#tmp_bomwipdtl tbody .row-check-wip').length;
    const checked = $('#tmp_bomwipdtl tbody .row-check-wip:checked').length;

    $('#checkAllWip').prop('checked', total === checked);
});

$('#tmp_bomwipdtl').on('draw.dt', function () {
    $('#checkAllWip').prop('checked', false);
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



function getCheckedDetailIdsCost() {
    let ids = [];
    $('.row-check-cost:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}



function getCheckedDetailIdsWip() {
    let ids = [];
    $('.row-check-wip:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}


document.addEventListener('keydown', function(e) {

// CTRL + Q
if (e.ctrlKey && e.key.toLowerCase() === 'q') {

e.preventDefault();

document.getElementById('btnAddDetailMaterial').click();
}

});

$(document).ready(function () {

    table_bom();
    documentReadable();
    tabletmpBOMMaterial();
    tabletmpBOMCost();
    tabletmpBOMWip();

});