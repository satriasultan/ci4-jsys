/*
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */
var lastWOENO = '';

var save_method; //for save method string
var table;
var initTable;
let skipRoleChange = false;
//"use strict";

/* VIUW UTAMA*/
function table_materialrelease() {
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tmaterialrelease');
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
                "url": HOST_URL + 'production/trans/list_materialrelease_mst',
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

function reload_materialrelease() {
    var table = $('#tmaterialrelease');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function () { //button filter event click
    var table = $('#tmaterialrelease');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function () { //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tmaterialrelease');
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
            $('#prefix').val('PMR').trigger('change');

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

            generateDocNumber('PMR', res.infix, currentKodeSuffix + '0001');

        }
    });

});


$('#prefix').on('change', function () {

    let prefix = $(this).val().toUpperCase();
    $(this).val(prefix);

    let infix = $('#infix').val();

    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + '/production/trans/getNextSuffix_materialrelease_mst',
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

    $.getJSON(HOST_URL + 'production/trans/showing_trx_materialrelease_mst', {docno: docno})
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
            setJtsValue('[name="buildfor"]', convertToDbNumber(item.buildfor));
            // setJtsValue('[name="minimumqty"]', convertToDbNumber(item.minimumqty));
            $('[name="docdate"]').val(item.docdate).prop('disabled', true);
            $('[name="pemohon"]').val(item.pemohon).prop('disabled', true);
            $('[name="batchno"]').val(item.batchno).prop('readonly',true);
            $('[name="buildfor"]').prop('disabled', true).prop('readonly',true);
            // $('[name="activedate"]').val(item.activedate).prop('disabled', true);
            // $('[name="docref"]').val(item.docref).prop('disabled', false);
            $('[name="keterangan"]').val(item.keterangan).prop('readonly',true);
            $('[name="tabno"]').val(item.tabno).prop('readonly',true);
            // $('#ttlprice').text(
            //     convertToDbNumber(item.ttlprice || 0)
            //         .toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         })
            // );
            // $('#ttlmaterial').text(
            //     convertToDbNumber(item.ttlmaterial || 0)
            //         .toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         })
            // );
            // $('#ttlcost').text(
            //     convertToDbNumber(item.ttlcost || 0)
            //         .toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         })
            // );
            // $('#ttlwip').text(
            //     convertToDbNumber(item.ttlwip || 0)
            //         .toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         })
            // );
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

            setSelect2Ajax('#woeno', item.woeno, item.woeno);
            setSelect2Ajax('#wono', item.wono, item.wono);
            setSelect2Ajax('#bomno', item.bomno, item.bomno);
            setSelect2Ajax('#idbarang_jadi', item.idbarang_jadi, item.idbarang_jadi);
            setSelect2Ajax('#buildunit', item.buildunit, item.buildunit);
            setSelect2Ajax('#bagian', item.bagian, item.bagian);
            setSelect2Ajax('#idlocation', item.idlocation, item.idlocation);
            $('[name="idlocation"]').prop("disabled", true);
            $('[name="woeno"]').prop("disabled", true);
            $('[name="wono"]').prop("disabled", true);
            $('[name="bomno"]').prop("disabled", true);
            $('[name="tabno"]').prop("disabled", true);
            $('[name="bagian"]').prop("disabled", true);
            $('[name="idbarang_jadi"]').prop("disabled", true);
            $('[name="buildunit"]').prop("disabled", true);
            // $('[name="wono"]').prop("disabled", true);

            skipRoleChange = true;
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

    // reset form
    $('#formMaterialReleaseMaterialDtl')[0].reset();

    // readonly
    // $('[name="actualcost"]').prop("readonly", true);
    // $('[name="lastcost"]').prop("readonly", true);

    // reset select2
    $('#idbarang').val(null).trigger('change');

    // reset idurut
    $('#idurut').val('');

    // title modal
    $('#modalDtlMaterialReleaseTitle').text('Tambah Detail Material');
    resetMaterialReleaseModal();

/*    // destroy jika sudah pernah init
    if ($('#idbarang').hasClass("select2-hidden-accessible")) {
        $('#idbarang').select2('destroy');
    }*/



    // show modal
    $('#modalDtlMaterialRelease').modal('show');
}

function resetMaterialReleaseModal(){

    $('#issub').prop('checked', false);

    $('#divItemBom').show();
    $('#divItemSub').hide();

    $('#idbarang').val(null).trigger('change');
    $('#idbarang_bom').val(null).trigger('change');

    $('#nmbarang').val('');
    $('#unit').val('');
    $('#qty').val('');

}




function setSelect2Ajax(selector, value, text) {
    if (!value) return;

    let option = new Option(text || value, value, true, true);
    $(selector).append(option).trigger('change');
}


function updateDetailMaterialRelease(){
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
        url: HOST_URL + 'production/trans/get_materialrelease_dtl',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                $('#idurut').val(res.items[0].idurut);
                $('#description').val(res.items[0].description);
                $('#docno').val(res.items[0].docno);
                $('#idbarang').val(res.items[0].idbarang);
                $('#nmbarang').val(res.items[0].nmbarang);
                $('#unit').val(res.items[0].unit);
                $('#issub').prop('checked',res.items[0].issub == 'T');
                // $('#qtymaterial').val(res.items[0].qty);
                setJtsValue('[name="qty"]', convertToDbNumber(res.items[0].qty));
                // setJtsValue('[name="standartcostmaterial"]', convertToDbNumber(res.items[0].standartcost));
                // setJtsValue('[name="totalcostmaterial"]', convertToDbNumber(res.items[0].totalcost));
                // $('#standartcostmaterial').val(res.items[0].standartcost);
                // $('#totalcostmaterial').val(res.items[0].totalcost);
                // setSelect2Ajax('#idbarang', res.items[0].idbarang, res.items[0].idbarang);

                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_item_std_cost' + '?var=' + res.items[0].idbarang.trim(),
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                    $('[name="idbarang"]').append(option).trigger('change')

                    // manually trigger the `select2:select` event
                    $('[name="idbarang"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });
                $('#modalDtlMaterialReleaseTitle').text('Update Detail Material');
                $('#modalDtlMaterialRelease').modal('show');

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

$('#issub').on('change', function () {

    if ($(this).is(':checked')) {

        // tampilkan substitute
        $('#divItemBom').hide();
        $('#divItemSub').show();

        // kosongkan item BOM
        $('#idbarang_bom').val(null).trigger('change');

    } else {

        // tampilkan BOM
        $('#divItemSub').hide();
        $('#divItemBom').show();

        // kosongkan substitute
        $('#idbarang').val(null).trigger('change');

    }

    $('#nmbarang').val('');
    $('#unit').val('');
    $('#qty').val('');

});



function btnDeleteDetailMaterialRelease(){
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
            url: HOST_URL + 'production/trans/delete_materialrelease_detail',
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
                    reload_materialrelease_dtl();

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


var defaultInitialGroupBrng = '';
$("#idbarang").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    minimumInputLength: 2,
    dropdownParent: $('#modalDtlMaterialRelease'),
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
                // docdate:  docdate,
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

    $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // setJtsValue('[name="standartcost"]', convertToDbNumber(data.newcost));
    // $('[name="totalcost"]').val().prop("readonly", true);

});


var defaultInitialBomItem = '';

$("#idbarang_bom").select2({
    placeholder: "Choose BOM Item",
    allowClear: true,
    width: '100%',
    minimumInputLength: 0,
    dropdownParent: $('#modalDtlMaterialRelease'),
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item_bom',
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
                wono: $('#wono').val(),
                term: params.term
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: {
                    more: (params.page * 20) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function (markup) {
        return markup;
    },
    templateResult: formatItem,
    templateSelection: formatItemSelection

}).on("select2:select", function (e) {

    var data = e.params.data;

    $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $('[name="qty"]').val(data.qty.trim())
    setJtsValue('[name="qty"]', convertToDbNumber(data.qty));
    // setJtsValue('[name="standartcost"]', convertToDbNumber(data.newcost));
    // $('[name="totalcost"]').val().prop("readonly", true);

});

var defaultInitialWOE = '';
$("#woeno").select2({
    placeholder: "Choose Your WOE",
    // dropdownParent: $('#modalDetailWO'),
    allowClear: true,
    width:'100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_woe',
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
                _paramglobal_: defaultInitialWOE,
                _parameterx_: defaultInitialWOE,
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
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatWOE, // omitted for brevity, see the source of this page
    templateSelection: formatWOESelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    resetWO()
    resetBOM();
    enableBOM();
    var data = e.params.data;
    // $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    // $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $("#batch").val(null).trigger('change');
    // simpan woe sebelumnya
    lastWOENO = $('#woeno').val();
    defaultInitialWO = data.wono
    defaultInitialBOM = data.bomno
    defaultIdbarangJadi = data.idbarang_jadi
    $("#wono").val(data.wono.trim()).trigger('change').prop('disabled',true)
    $("#bomno").val(data.bomno.trim()).trigger('change').prop('disabled',true)
    $("#buildfor").val(data.buildfor.trim()).prop('readonly',true)
    $("#buildunit").val('KG').trigger('change').prop('disabled',true)
    $("#idbarang_jadi").val(data.idbarang_jadi.trim()).trigger('change').prop('disabled',true)
    setSelect2Ajax('#wono', data.wono, data.wono);
    setSelect2Ajax('#bomno', data.bomno, data.bomno);
    setSelect2Ajax('#idbarang_jadi', data.idbarang_jadi, data.idbarang_jadi);
    setSelect2Ajax('#buildunit', 'KG', 'KG');

    saveMaterialReleaseFromWO(lastWOENO);
});



function setSelect2Ajax(selector, value, text) {
    if (!value) return;

    let option = new Option(text || value, value, true, true);
    $(selector).append(option).trigger('change');
}

/* Format Group */
function formatWOE(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.docno +"   <i class='fa fa-circle-o'></i>   "+ repo.keterangan +"</div>";
    return markup;
}
function formatWOESelection(repo) {
    return repo.keterangan || repo.text;
}


var defaultInitialWO = '';
$("#wono").select2({
    placeholder: "Choose Your WO",
    // dropdownParent: $('#modalDetailWO'),
    allowClear: true,
    width:'100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_wo',
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
                _paramglobal_: defaultInitialWO,
                _parameterx_: defaultInitialWO,
                docref: $("#wono").val(),   // WO yang dipilih
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
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatWO, // omitted for brevity, see the source of this page
    templateSelection: formatWOSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    resetBOM();
    enableBOM();
    var data = e.params.data;
    // $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    // $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $("#batch").val(null).trigger('change');
});


$('#woeno').on('select2:clear', function () {
    resetWO();
});

$('#wono').on('select2:clear', function () {
    resetBOM();
});

$('#bomno').on('select2:clear', function () {
    // $('[name="desc_bom"]').val('');
    $('[name="buildfor"]').val('');
    resetItem();
});

/* Format Group */
function formatWO(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.docno +"   <i class='fa fa-circle-o'></i>   "+ repo.keterangan +"</div>";
    return markup;
}
function formatWOSelection(repo) {
    return repo.keterangan || repo.text;
}


var defaultInitialBOM = '';
$("#bomno").select2({
    placeholder: "Choose Your BOM",
    // dropdownParent: $('#modalDetailBOM'),
    allowClear: true,
    width:'100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_workingorder_bom',
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
                _paramglobal_: defaultInitialBOM,
                _parameterx_: defaultInitialBOM,
                docref: $("#wono").val(),   // WO yang dipilih
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
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatBOM, // omitted for brevity, see the source of this page
    templateSelection: formatBOMSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    resetItem();
    enableItem();
    var data = e.params.data;
    // $('[name="desc_bom"]').val(data.desc_bom.trim()).prop("readonly", true);
    $('[name="buildfor"]').val(data.buildfor.trim()).prop("readonly", true);
    $('[name="buildunit"]').val(data.buildunit.trim()).prop("readonly", true);
    // $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $("#batch").val(null).trigger('change');
});

/* Format Group */
function formatBOM(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.docno +"   <i class='fa fa-circle-o'></i>   "+ repo.desc_bom +"</div>";
    return markup;
}
function formatBOMSelection(repo) {
    return repo.desc_bom || repo.text;
}



var defaultIdbarangJadi = '';
$("#idbarang_jadi").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    // minimumInputLength: 2,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_workingorder_item',
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
                docref: $("#wono").val(),   // WO yang dipilih
                docnobom: $("#bomno").val(),
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



var defaultInitialLocation = '';
$("#idlocation").select2({
    placeholder: "Type/Chose Location",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
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
                _paramglobal_: defaultInitialLocation,
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
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {

    var data = e.params.data;
    $('[name="nmlocation"]').val(data.nmlocation.trim())

    // $("#defarea option[value]").remove();
    // var newOptions = []; // the result of your JSON request
    // $("#defarea").val(null).trigger('change');
    //console.log($("#newdept").val());
});

/*Location*/
function formatLocation(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idlocation +"   <i class='fa fa-circle-o'></i>   "+ repo.nmlocation +"</div>";
    return markup;
}

function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}




var defaultInitialBagian = '';
$("#bagian").select2({
    placeholder: "Type/Chose Location",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
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
                _paramglobal_: defaultInitialBagian,
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
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {

    var data = e.params.data;
    // $('[name="nmlocation"]').val(data.nmlocation.trim())

    // $("#defarea option[value]").remove();
    // var newOptions = []; // the result of your JSON request
    // $("#defarea").val(null).trigger('change');
    //console.log($("#newdept").val());
});


var defaultInitialUnit = 'UNIT';
$("#buildunit").select2({
    placeholder: "Type/Chose Your Unit",
    allowClear: true,
    width:'100%',
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

function resetWO() {

    $('#wono')
        .val(null)
        .trigger('change')
        .prop('disabled', true);

    // $('[name="desc_wo"]').val('');
    // $('[name="buildfor"]').val('');
    resetBOM()
    resetItem();
}

function enableWO() {
    $('#bomno').prop('disabled', false);
}


function resetBOM() {

    $('#bomno')
        .val(null)
        .trigger('change')
        .prop('disabled', true);

    $('[name="desc_bom"]').val('');
    $('[name="buildfor"]').val('');

    resetItem();
}

function enableBOM() {
    $('#bomno').prop('disabled', false);
}

function resetItem() {

    $('#idbarang_jadi')
        .val(null)
        .trigger('change')
        .prop('disabled', true);

    $('[name="nmbarang_jadi"]').val('');
}

function enableItem() {
    $('#idbarang_jadi').prop('disabled', false);
}


function hitungTotalCost() {

    let qty = $('#qty').val().replace(/,/g, '');
    let cost = $('#standartcost').val().replace(/,/g, '');

    qty = parseFloat(qty) || 0;
    cost = parseFloat(cost) || 0;

    let total = qty * cost;

    $('#totalcost').val(
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


function saveMaterialReleaseFromWO(oldWoeno)
{
    let formData = new FormData();


    formData.append('old_woeno', oldWoeno);
    formData.append('docref', $('#docref').val());
    formData.append('cabang', $('#cabang').val());
    formData.append('pemohon', $('#pemohon').val());

    formData.append(
        'docno',
        $('#prefix').val() + '/' +
        $('#infix').val() + '/' +
        $('#sufix').val()
    );

    formData.append('docdate', $('#docdate').val());
    formData.append('woeno', $('#woeno').val());
    formData.append('wono', $('#wono').val());
    formData.append('bomno', $('#bomno').val());

    formData.append('idbarang_jadi', $('#idbarang_jadi').val());
    formData.append('nmbarang_jadi', $('#nmbarang_jadi').val());

    formData.append('buildfor', convertToDbNumber($('#buildfor').val()));
    formData.append('buildunit', $('#buildunit').val());

    formData.append('batchno', $('#batchno').val());

    formData.append('idlocation', $('#idlocation').val());
    formData.append('nmlocation', $('#nmlocation').val());

    formData.append('bagian', $('#bagian').val());

    formData.append('keterangan', $('#keterangan').val());

    $.ajax({
        url: HOST_URL + 'production/trans/save_materialrelease_from_wo',
        type: 'POST',
        processData:false,
        contentType:false,
        dataType:'json',
        data:formData,

        success:function(res){

            if(res.success){

                reload_materialrelease_dtl();
                documentReadable();

            }else{

                Swal.fire({
                    icon:'warning',
                    title:'Warning',
                    text:res.message
                });

            }

        }
    });

}



function saveMaterialReleaseDetail(formId) {


        // ===============================
        // Ambil dan validasi qty
        // ===============================
        let buildfor = $('#buildfor').val();
        // let minimumqty = $('#minimumqty').val();

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
        formData.append('nmbarang_jadi', $('#nmbarang_jadi').val());
        formData.append('woeno', $('#woeno').val());
        formData.append('wono', $('#wono').val());
        formData.append('bomno', $('#bomno').val());
        formData.append('bagian', $('#bagian').val());
        formData.append('idlocation', $('#idlocation').val());
        formData.append('nmlocation', $('#nmlocation').val());
        formData.append('tabno', $('#tabno').val());
        formData.append('batchno', $('#batchno').val());

        formData.append('docdate', $('#docdate').val());
        formData.append('keterangan', $('#keterangan').val());
        // formData.append('minimumqty', convertToDbNumber(minimumqty));
        formData.append('buildunit', $('#buildunit').val());
        formData.append('buildfor', convertToDbNumber(buildfor));
        formData.append(
            'issub',
            $('#issub').is(':checked') ? 'T' : 'F'
        );
        // formData.append('minimumqty', $('#minimumqty').val());
        formData.append('keterangan', $('#keterangan').val());
        //DETAIL
        // formData.append('idbarang', $('#idbarang').val());
        // formData.append('nmbarangmaterial', $('#nmbarangmaterial').val());
        // formData.append('unitMaterial', $('#unitMaterial').val());
        // formData.set('qty', convertToDbNumber(qty));
        // formData.set('standartcostmaterial', convertToDbNumber(standartcostmaterial));
        // formData.set('totalcostmaterial', convertToDbNumber(totalcostmaterial));
        // formData.append('description_detail_material', $('#description_detail_material').val());

        // if(detailType === 'MATERIAL'){
        let qty = $('#qty').val();
        // let standartcostmaterial = $('#standartcostmaterial').val();
        // let totalcostmaterial = $('#totalcostmaterial').val();
        formData.set('qty',convertToDbNumber($('#qty').val()));
        // formData.set('standartcostmaterial',convertToDbNumber($('#standartcostmaterial').val()));
        // formData.set('totalcostmaterial',convertToDbNumber($('#totalcostmaterial').val()));

        // }else if(detailType === 'COST'){
        //     let qtycost = $('#qtycost').val();
        //     let standartcostcost = $('#standartcostcost').val();
        //     let totalcostcost = $('#totalcostcost').val();
        //     formData.set('qtycost',convertToDbNumber($('#qtycost').val()));
        //     formData.set('standartcostcost',convertToDbNumber($('#standartcostcost').val()));
        //     formData.set('totalcostcost',convertToDbNumber($('#totalcostcost').val()));
        // }else if(detailType === 'WIP'){
        //     let qtywip = $('#qtywip').val();
        //     let standartcostwip = $('#standartcostwip').val();
        //     let totalcostwip = $('#totalcostwip').val();
        //     formData.set('qtywip',convertToDbNumber($('#qtywip').val()));
        //     formData.set('standartcostwip',convertToDbNumber($('#standartcostwip').val()));
        //     formData.set('totalcostwip',convertToDbNumber($('#totalcostwip').val()));
        // }
        // formData.append('doctype_detail', detailType);
        // Gabungkan docno
        formData.set(
            'docno',
            $('#prefix').val() + '/' +
            $('#infix').val() + '/' +
            $('#sufix').val()
        );

        // ===============================
        // AJAX SAVE
        // ===============================

        $.ajax({
            url: HOST_URL + 'production/trans/save_materialrelease_mst',
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

                    // if(detailType === 'MATERIAL'){
                    $('#modalDtlMaterialRelease').modal('hide');
                    reload_materialrelease_dtl();
                    $('#formMaterialReleaseMaterialDtl')[0].reset();
                    // } else if(detailType === 'COST'){
                    //     $('#modalDtlBomCost').modal('hide');
                    //     reload_materialrelease_cost_dtl();
                    //     $('#formMaterialReleaseCostDtl')[0].reset();
                    // } else if(detailType === 'WIP'){
                    //     $('#modalDtlBomWip').modal('hide');
                    //     reload_materialrelease_wip_dtl();
                    //     $('#formMaterialReleaseWipDtl')[0].reset();
                    // }
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
function tabletrxMaterialRelease() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#trx_materialreleasedtl');
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
                "url": HOST_URL + 'production/trans/list_trx_materialrelease_dtl',
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


function reload_materialrelease_dtl() {
    var table = $('#trx_materialreleasedtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#trx_materialreleasedtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#trx_materialreleasedtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#trx_materialreleasedtl tbody').on('change', '.row-check', function () {
    const total = $('#trx_materialreleasedtl tbody .row-check').length;
    const checked = $('#trx_materialreleasedtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#trx_materialreleasedtl').on('draw.dt', function () {
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


function getCheckedDetailIds() {
    let ids = [];
    $('.row-check:checked').each(function () {
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

    table_materialrelease();
    documentReadable();
    tabletrxMaterialRelease();
    // tabletrxMaterialReleaseCost();
    // tabletrxMaterialReleaseWip();

});