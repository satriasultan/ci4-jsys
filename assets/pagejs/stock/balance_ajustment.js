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

function tableajustmentsTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tableajustmenttrx');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            "bFilter":true,
            "ajax": {
                "url": HOST_URL + 'stock/balance/list_ajustments',
                "type": "POST",
                "data": function(data) {
                    data.searchfilter = $('#searchitem').val()+'';
                    data.idbarang = $('#idbarang').val()+'';
                    data.idposition = $('#idposition').val()+'';
                },
                "dataFilter": function(data) {
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
                    "targets": [ -1 ], //last column
                    "orderable": false, //set not orderable
                },
            ],

        });

    };


    return initTable();
}

function reload_tableajustmentsTrx()
{
    var table = $('#tableajustmenttrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

function tableItem(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tsearchitem');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "bFilter":true,
            "ajax": {
                "url": HOST_URL + 'stock/balance/list_balance_ajustments',
                "type": "POST",
                "data": function(data) {
                    data.searchfilter = $('#searchitem').val()+'';
                    data.idbarang = $('#idbarang').val()+'';
                    data.idposition = $('#idposition').val()+'';
                },
                "dataFilter": function(data) {
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
                    "targets": [ -1 ], //last column
                    "orderable": false, //set not orderable
                },
            ],

        });

    };


    return initTable();
}



function reload_table()
{
    var table = $('#tsearchitem');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tsearchitem');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tsearchitem');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

//EDIT ITEM
function documentReadable(){
    $("#loadMe").modal({
        backdrop: "static", //remove ability to close modal with click
        keyboard: false, //remove option to close with keyboard
        show: true //Display loader!
    });
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'stock/balance/showing_item_ajustments_mst' + '?var=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            // Fetch the preselected item, and add to the control
            $('[name=""]').val(json.dataTables.items[0].idbarang);
            $('[name="nmbarang"]').val(json.dataTables.items[0].nmbarang);
            $('[name="docdate"]').val(json.dataTables.items[0].docdate1);
            $('[name="docref"]').val(json.dataTables.items[0].docref);
            $('[name="description"]').val(json.dataTables.items[0].description);
            if (json.dataTables.items[0].status.trim()==='I') { //mencegah trim
                $('[name="doctype"]').val(json.dataTables.items[0].doctype).trigger('change');
            } else {
                $('[name="doctype"]').val(json.dataTables.items[0].doctype.trim()).trigger('change');
            }


            //$('[name="idbarang"]').prop('readonly', true);
            $("#loadMe").modal("hide");

        },
        complete: function(){
            $("#loadMe").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });

    $("#loadMe").modal("hide");
}
/* FOR INPUT FUNCTION */



// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH GROUP ++++++++++++++++++++++++++++++++++++++++//

var defaultInitialGroup = '';
$("#idbarang").select2({
    placeholder: "Type/ Scan your barcode item",
    allowClear: true,
    minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: true,
    ajax: {
        url: HOST_URL + 'stock/balance/list_item',
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
                _paramglobal_: defaultInitialGroup,
                _parameterx_: defaultInitialGroup,
                term: params.term,
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#idbarang").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmbarang, data.items[0].idbarang, true, true);
                $('#idbarang').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#idbarang').trigger({
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
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatItem, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
    //insert to detail item

    var valtype = 'INPUT';
    var valid = 0;
    var validlocation = $('[name="idlocationto"]').val();
    var validbarangmove = $('[name="idbarangmove"]').val();
    var valnmbarangmove = $('[name="nmbarangmove"]').val();
    var valqtyonhand = $('[name="qtyonhand"]').val();
    var valunitonhand = $('[name="unitonhand"]').val();
    var validareafrom = $('[name="idareafrom"]').val();
    var valnmareafrom = $('[name="nmareafrom"]').val();
    var validareato = $('[name="idareato"]').val();
    var valnmareato = $('[name="nmareato"]').val();
    var valqtyajustments = $('[name="qtytransfers"]').val();
    var valunitajustments = $('[name="unittransfers"]').val();
    var valdescription = $('[name="descriptionm"]').val();

    var fillData = {
        'success': true,
        'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
        'message': '',
        'body': {
            type: valtype,
            id: valid,
            idlocationto: validlocationto,
            idlocationfrom: validlocationfrom,
            idbarangmove: validbarangmove,
            nmbarangmove: valnmbarangmove,
            qtyonhand: valqtyonhand,
            unitonhand: valunitonhand,
            idareafrom: validareafrom,
            nmareafrom: valnmareafrom,
            idareato: validareato,
            nmareato: valnmareato,
            qtyajustment: valqtyajustments,
            unitajustment: valunitajustments,
            description: valdescription,

        },
    };
    $.ajax({
        type: "POST",
        url: HOST_URL + 'stock/balance/saveDetailajustment' + '',
        dataType: 'json',
        contentType: "application/json",
        data: JSON.stringify(fillData),
        success: function (datax) {
            if (datax.status) {
                Swal.fire({
                    title: 'Berhasil...!!!',
                    text: datax.messages,
                    backdrop: true,
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: `Ok`,
                    icon: 'success',
                    //denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        // window.location.replace(HOST_URL + 'trans/referensisdm')
                        reload_table();
                        reload_table_ajustment();
                        $('#modal_form').modal('hide');
                        bttn.prop('disabled', false);
                    }
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: datax.messages,
                    backdrop: true,
                    allowOutsideClick: false,
                })
                reload_table();
                reload_table_ajustment();
                $('#modal_form').modal('hide');
                bttn.prop('disabled', false);
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
            reload_table();
            reload_table_ajustment();
            $('#modal_form').modal('hide');
            bttn.prop('disabled', false);
        }
    });

    var table = $('#talocationajustment');
        table.DataTable().ajax.reload(); //reload datatable ajax
        ///table.append().search( $(this).val() ).draw();
        //$('#filter').modal('hide');
});
/* Format Group */
function formatItem(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idbarang +"   <i class='fa fa-circle-o'></i>   "+ repo.nmbarang +"</div>";
    return markup;
}
function formatItemSelection(repo) {
    return repo.nmbarang || repo.text;
}
// ID POSTION
var defaultInitialGroup = '';
$("#idposition").select2({
    placeholder: "Type/ Scan your barcode position area",
    allowClear: true,
    minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: true,
    ajax: {
        url: HOST_URL + 'stock/balance/list_area',
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
                _paramglobal_: defaultInitialGroup,
                _parameterx_: defaultInitialGroup,
                term: params.term,
            };
        },
        processResults: function(data, params) {

            var searchTerm = $("#idposition").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmarea, data.items[0].idarea, true, true);
                $('#idposition').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#idposition').trigger({
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
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatArea, // omitted for brevity, see the source of this page
    templateSelection: formatAreaSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    var table = $('#tsearchitem');
    table.DataTable().ajax.reload(); //reload datatable ajax
});
/* Format Group */
function formatArea(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idarea +"   <i class='fa fa-circle-o'></i>   "+ repo.nmarea +"</div>";
    return markup;
}

function formatAreaSelection(repo) {
    return repo.nmarea || repo.text;
}

//ID LOCATION
var defaultInitialLocation = '';
$("#idlocation").select2({
    placeholder: "Type your location destination warehouse",
    allowClear: true,
    minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: true,
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
                _parameterx_: defaultInitialLocation,
                term: params.term,
            };
        },
        processResults: function(data, params) {

            var searchTerm = $("#idlocation").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmlocation, data.items[0].idlocation, true, true);
                $('#idlocation').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#idlocation').trigger({
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
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("change", function () {
   /* JIKA INI BERUBAH HARUS ADA VERIFIKASI PERSETUJUAN PEGHAPUSAN INPUTAN
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Change This Will Be Cleared Your Input All Item, Are you sure want continue? ' ,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        if (result.isConfirmed) {
           // window.location.replace(HOST_URL + 'master/item/edit' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })*/
});
/* Format Group */
function formatLocation(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idlocation +"   <i class='fa fa-circle-o'></i>   "+ repo.nmlocation +"</div>";
    return markup;
}

function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}





function editsearchitem(e){
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
//lampu

$('#idbarang').change(function(){
    //console.log($(this).val() + 'TEST');
    /*
    if ($(this).val() !== '' ) {
        var fillData = {
            'success' : true,
            'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
            'message' : '',
            'body' : {
                stockcode : $(this).val(),
                type : 'ADDING_ITEM',
            },
        };
        $.ajax({
            type: "POST",
            url: HOST_URL + 'ga/itemtrans/saveItemtransDtl' + '',
            dataType: 'json',
            contentType: "application/json",
            data: JSON.stringify(fillData),
            success: function (datax){
                reloadTmpItemtransDtl();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                Swal.fire({
                    title: 'Silahkan Ulangi...!!!',
                    text: 'Error Ada Kesalahan Data!!, Silahkan Ulangi',
                    backdrop: true,
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: `Ok`,
                    icon: 'error',
                    //denyButtonText: `Don't save`,
                });
            }
        });
    }

     */
});

$('#searchalocation').keyup(function(){

    readStockBalance($(this).val())
});

function readStockBalance(e)
{
    var parameterx = e;
    $.ajax({
        url: HOST_URL + 'stock/balance/apiAlocationMap',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: {
                _paramglobal_: parameterx,
        },
        success: function(data) {
         //console.log(data.items[0].idarea);
         //console.log(data.dataitems.nmbarang);
        // console.log(inArray("16401-0001",[data.items[0].idarea,data.items[1]])=== true);
            /* Initialization */
            $("#nmbarang").val('');
            $("#transit").removeClass("bg-red").addClass("bg-white");
            $("#A1").removeClass("bg-red").addClass("bg-white");
            $("#A2").removeClass("bg-red").addClass("bg-white");
            $("#A3").removeClass("bg-red").addClass("bg-white");
            $("#A4").removeClass("bg-red").addClass("bg-white");
            $("#A5").removeClass("bg-red").addClass("bg-white");
            $("#B1").removeClass("bg-red").addClass("bg-white");
            $("#B2").removeClass("bg-red").addClass("bg-white");
            $("#B3").removeClass("bg-red").addClass("bg-white");
            $("#B4").removeClass("bg-red").addClass("bg-white");
            $("#B5").removeClass("bg-red").addClass("bg-white");
            $("#C1").removeClass("bg-red").addClass("bg-white");
            $("#C2").removeClass("bg-red").addClass("bg-white");
            $("#C3").removeClass("bg-red").addClass("bg-white");
            $("#C4").removeClass("bg-red").addClass("bg-white");
            $("#C5").removeClass("bg-red").addClass("bg-white");
            $("#D1").removeClass("bg-red").addClass("bg-white");
            $("#D2").removeClass("bg-red").addClass("bg-white");
            $("#D3").removeClass("bg-red").addClass("bg-white");
            $("#D4").removeClass("bg-red").addClass("bg-white");
            $("#D5").removeClass("bg-red").addClass("bg-white");
            /* Initialization */

            if (data.total_count > 0){
                var barang = data.dataitems.nmbarang;
                $("#nmbarang").val(barang);
            }
            const arr = data.items;
            arr.forEach(function (item, index) {
                //if (index === 0) {
                    console.log('index' + item.idarea, index);
                    if (item.idarea.trim() === "16401-0001") {
                        $("#transit").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0002") {
                        $("#A1").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0003") {
                        $("#A2").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0004") {
                        $("#A3").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0005") {
                        $("#A4").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0006") {
                        $("#A5").removeClass("bg-white").addClass("bg-red");
                    }
                    //B
                    if (item.idarea.trim() === "16401-0007") {
                        $("#B1").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0008") {
                        $("#B2").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0009") {
                        $("#B3").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0010") {
                        $("#B4").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0011") {
                        $("#B5").removeClass("bg-white").addClass("bg-red");
                    }
                    //C
                    if (item.idarea.trim() === "16401-0012") {
                        $("#C1").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0013") {
                        $("#C2").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0014") {
                        $("#C3").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0015") {
                        $("#C4").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0016") {
                        $("#C5").removeClass("bg-white").addClass("bg-red");
                    }
                    //D
                    if (item.idarea.trim() === "16401-0017") {
                        $("#D1").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0018") {
                        $("#D2").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0019") {
                        $("#D3").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0020") {
                        $("#D4").removeClass("bg-white").addClass("bg-red");
                    }
                    if (item.idarea.trim() === "16401-0021") {
                        $("#D5").removeClass("bg-white").addClass("bg-red");
                    }
               // }



            });



         if (parameterx ==='' || data.total_count < 1){
             $("#transit").removeClass("bg-red").addClass("bg-white");
             $("#A1").removeClass("bg-red").addClass("bg-white");
             $("#A2").removeClass("bg-red").addClass("bg-white");
             $("#A3").removeClass("bg-red").addClass("bg-white");
             $("#A4").removeClass("bg-red").addClass("bg-white");
             $("#A5").removeClass("bg-red").addClass("bg-white");
             $("#B1").removeClass("bg-red").addClass("bg-white");
             $("#B2").removeClass("bg-red").addClass("bg-white");
             $("#B3").removeClass("bg-red").addClass("bg-white");
             $("#B4").removeClass("bg-red").addClass("bg-white");
             $("#B5").removeClass("bg-red").addClass("bg-white");
             $("#C1").removeClass("bg-red").addClass("bg-white");
             $("#C2").removeClass("bg-red").addClass("bg-white");
             $("#C3").removeClass("bg-red").addClass("bg-white");
             $("#C4").removeClass("bg-red").addClass("bg-white");
             $("#C5").removeClass("bg-red").addClass("bg-white");
             $("#D1").removeClass("bg-red").addClass("bg-white");
             $("#D2").removeClass("bg-red").addClass("bg-white");
             $("#D3").removeClass("bg-red").addClass("bg-white");
             $("#D4").removeClass("bg-red").addClass("bg-white");
             $("#D5").removeClass("bg-red").addClass("bg-white");
         }
        }
    });
}

var defaultInitialArea = '';
$("#idareamove").select2({
    placeholder: "Type/Chose Your Sub Unit",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_marea',
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
                _paramglobal_: '',
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
    templateResult: formatArea, // omitted for brevity, see the source of this page
    templateSelection: formatAreaSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {

});
/*Location*/
function formatArea(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idarea +"   <i class='fa fa-circle-o'></i>   "+ repo.nmarea +"</div>";
    return markup;
}

function formatAreaSelection(repo) {
    return repo.nmarea || repo.text;
}

/* TABLE SC_TRX.MOVING */
function tableajustmentDetail(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#talocationajustment');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "bFilter":true,
            "ajax": {
                "url": HOST_URL + 'stock/balance/list_tmp_ajustments_dtl',
                "type": "POST",
                "data": function(data) {
                    //data.searchfilter = $('#searchitem').val()+'';
                    //data.idbarang = $('#idbarang').val()+'';
                    //data.idposition = $('#idposition').val()+'';
                },
                "dataFilter": function(data) {
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
                    "targets": [ -1 ], //last column
                    "orderable": false, //set not orderable
                },
            ],
        });
    };
    return initTable();
}



function reload_table_ajustment()
{
    var table = $('#talocationajustment');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


$('#formAddItemajustment').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        qtymove: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        trxdate: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },

    },
    excluded: [':disabled']
});

function add_ajustments(id)
{
    save_method = 'input';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formAddItemajustment').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formAddItemajustment')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'stock/balance/showing_stkgdw' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('INPUT');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="idlocationfrom"]').val(data.idlocation).prop("readonly", true);
            $('[name="idlocationto"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangmove"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarangmove"]').val(data.nmbarang).prop("readonly", true);
            $('[name="qtyonhand"]').val(data.sisa).prop("readonly", true);
            $('[name="unitonhand"]').val(data.unit).prop("readonly", true);
            $('[name="idareato"]').val(data.idarea).prop("readonly", true);
            $('[name="idareafrom"]').val(data.idarea).prop("readonly", true);
            $('[name="nmareato"]').val(data.nmarea).prop("readonly", true);
            $('[name="nmareafrom"]').val(data.nmarea).prop("readonly", true);
            $('[name="qtytransfers"]').val(data.sisa);
            $('[name="unittransfers"]').val(data.unit).prop("readonly", true);

            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Process');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Adding Item ajustments'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function edit_ajustments(id)
{
    save_method = 'input';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formAddItemajustment').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formAddItemajustment')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'stock/balance/showing_item_ajustment_dtl' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('UPDATE');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="idlocationfrom"]').val(data.idlocation).prop("readonly", true);
            $('[name="idlocationto"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangmove"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarangmove"]').val(data.nmbarang).prop("readonly", true);
            $('[name="qtyonhand"]').val(data.sisa).prop("readonly", true);
            $('[name="unitonhand"]').val(data.unit).prop("readonly", true);
            $('[name="idareato"]').val(data.idareatrans).prop("readonly", true);
            $('[name="idareafrom"]').val(data.idarea).prop("readonly", true);
            $('[name="nmareato"]').val(data.nmareato).prop("readonly", true);
            $('[name="nmareafrom"]').val(data.nmareafrom).prop("readonly", true);
            $('[name="qtytransfers"]').val(data.onhandtrans);
            $('[name="unittransfers"]').val(data.unit).prop("readonly", true);
            $('[name="descriptionm"]').val(data.description);

            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Process');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Update Item ajustments'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_ajustments(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formAddItemajustment').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formAddItemajustment')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'stock/balance/showing_item_ajustment_dtl' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('DELETE');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="idlocationfrom"]').val(data.idlocation).prop("readonly", true);
            $('[name="idlocationto"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangmove"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarangmove"]').val(data.nmbarang).prop("readonly", true);
            $('[name="qtyonhand"]').val(data.sisa).prop("readonly", true);
            $('[name="unitonhand"]').val(data.unit).prop("readonly", true);
            $('[name="idareato"]').val(data.idareatrans).prop("readonly", true);
            $('[name="idareafrom"]').val(data.idarea).prop("readonly", true);
            $('[name="nmareato"]').val(data.nmareato).prop("readonly", true);
            $('[name="nmareafrom"]').val(data.nmareafrom).prop("readonly", true);
            $('[name="qtytransfers"]').val(data.onhandtrans).prop("readonly", true);
            $('[name="unittransfers"]').val(data.unit).prop("readonly", true);
            $('[name="descriptionm"]').val(data.description).prop("readonly", true);

            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Process');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Detail Item ajustment'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function save_dtlajustments()
{
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data akan Proses, Apakah anda yakin...?',
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

            var validator = $('#formAddItemajustment').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var valid = $('[name="id"]').val();
                var validlocationto = $('[name="idlocationto"]').val();
                var validlocationfrom = $('[name="idlocationfrom"]').val();

                var validbarangmove = $('[name="idbarangmove"]').val();
                var valnmbarangmove = $('[name="nmbarangmove"]').val();
                var valqtyonhand = $('[name="qtyonhand"]').val();
                var valunitonhand = $('[name="unitonhand"]').val();
                var validareafrom = $('[name="idareafrom"]').val();
                var valnmareafrom = $('[name="nmareafrom"]').val();
                var validareato = $('[name="idareato"]').val();
                var valnmareato = $('[name="nmareato"]').val();
                var valqtyajustments = $('[name="qtytransfers"]').val();
                var valunitajustments = $('[name="unittransfers"]').val();
                var valdescription = $('[name="descriptionm"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        type: valtype,
                        id: valid,
                        idlocationto: validlocationto,
                        idlocationfrom: validlocationfrom,
                        idbarangmove: validbarangmove,
                        nmbarangmove: valnmbarangmove,
                        qtyonhand: valqtyonhand,
                        unitonhand: valunitonhand,
                        idareafrom: validareafrom,
                        nmareafrom: valnmareafrom,
                        idareato: validareato,
                        nmareato: valnmareato,
                        qtyajustment: valqtyajustments,
                        unitajustment: valunitajustments,
                        description: valdescription,

                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'stock/balance/saveDetailajustment' + '',
                    dataType: 'json',
                    contentType: "application/json",
                    data: JSON.stringify(fillData),
                    success: function (datax) {
                        if (datax.status) {
                            Swal.fire({
                                title: 'Berhasil...!!!',
                                text: datax.messages,
                                backdrop: true,
                                allowOutsideClick: false,
                                showConfirmButton: true,
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: `Ok`,
                                icon: 'success',
                                //denyButtonText: `Don't save`,
                            }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    // window.location.replace(HOST_URL + 'trans/referensisdm')
                                    reload_table();
                                    reload_table_ajustment();
                                    $('#modal_form').modal('hide');
                                    bttn.prop('disabled', false);
                                }
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: datax.messages,
                                backdrop: true,
                                allowOutsideClick: false,
                            })
                            reload_table();
                            reload_table_ajustment();
                            $('#modal_form').modal('hide');
                            bttn.prop('disabled', false);
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
                        reload_table();
                        reload_table_ajustment();
                        $('#modal_form').modal('hide');
                        bttn.prop('disabled', false);
                    }
                });
            }
        }
        bttn.prop('disabled', false);
    });
}




$(document).ready(function() {
    tableajustmentsTrx();
    tableajustmentDetail();
    tableItem();
    $('#checkboxnik').change(function() {
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
    $('#nik').change(function() {
        if ($('#checkboxnik').is(':checked')){
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
    if ($('[name="type"]').val() === 'INPUT' || $('[name="type"]').val() === 'UPDATE' || $('[name="type"]').val() === 'DELETE' ) {
        documentReadable();
    }


});