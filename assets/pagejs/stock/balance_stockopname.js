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
            "responsive": false,
            "bFilter":true,
            "ajax": {
                "url": HOST_URL + 'stock/balance/list_balance_stock_opname',
                "type": "POST",
                "data": function(data) {
                    data.searchfilter = $('#searchitem').val()+'';
                    data.dateOpname = $('#dateOpname').val()+'';
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
        $('#searchitem').keyup(function(){
            var table = $('#tsearchitem');
            // table.append().search( $(this).val() ).draw();
            console.log('Selecting =>' + $(this).val());
            table.DataTable().ajax.reload();
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
    var table = $('#tberitaacara');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tberitaacara');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

//EDIT ITEM
function documentReadable(){
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: true //Display loader!
    // });
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'master/item/showDetailItem' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            // Fetch the preselected item, and add to the control
            $('[name="id"]').val(json.dataTables.items[0].id);
            $('[name="idbarang"]').val(json.dataTables.items[0].idbarang);
            $('[name="nmbarang"]').val(json.dataTables.items[0].nmbarang);
            //$('[name="idgroup"]').val(json.dataTables.items[0].idgroup);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mgroup' + '?var=' + json.dataTables.items[0].idgroup,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgroup, datax.items[0].idgroup, true, true);
                $('[name="idgroup"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgroup"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="subunitenable"]').val(json.dataTables.items[0].subunitenable.trim()).trigger('change');
            //$('[name="unit"]').val(json.dataTables.items[0].unit);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_unit' + '?var=' + json.dataTables.items[0].unit,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
                $('[name="unit"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="unit"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="subunit"]').val(json.dataTables.items[0].subunit);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_subunit' + '?var=' + json.dataTables.items[0].subunit,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
                $('[name="subunit"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="subunit"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="idbarcode"]').val(json.dataTables.items[0].idbarcode);
            $('[name="mfgdate"]').val(json.dataTables.items[0].mfgdate1);
            $('[name="expdate"]').val(json.dataTables.items[0].expdate1);
            $('[name="maks_daystock"]').val(json.dataTables.items[0].maks_daystock);
            //$('[name="deflocation"]').val(json.dataTables.items[0].deflocation);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + json.dataTables.items[0].deflocation,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlocation, datax.items[0].idlocation, true, true);
                $('[name="deflocation"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="deflocation"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="defarea"]').val(json.dataTables.items[0].defarea);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_marea' + '?var=' + json.dataTables.items[0].defarea,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmarea, datax.items[0].idarea, true, true);
                $('[name="defarea"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="defarea"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="description"]').val(json.dataTables.items[0].description);
            $('[name="chold"]').val(json.dataTables.items[0].chold.trim()).trigger('change');

            $('[name="idbarang"]').prop('readonly', true);

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

$('#form').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        qtyso: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        idareaso: {
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

function so_stock(id)
{
    save_method = 'update';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#form').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    var datenow = $('[name="datenow"]').val();
    var dateOpname = $('#dateOpname').val();
    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'stock/balance/showing_detail_so' + '?id=' + id + '&dateOpname=' + dateOpname,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('[name="type"]').val('UPDATE');
            //$('[name="id"]').val(data.id);
            $('[name="idbarangmove"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarangmove"]').val(data.nmbarang).prop("readonly", true);
            $('[name="idlocationold"]').val(data.idlocation).prop("readonly", true);
            $('[name="idareaold"]').val(data.idarea).prop("readonly", true);
            $('[name="batch"]').val(data.batch).prop("readonly", true);
            $('[name="qty"]').val(data.sisa).prop("readonly", true);
            $('[name="unit"]').val(data.unit).prop("readonly", true);
            $('[name="unitselisih"]').val(data.unit).prop("readonly", true);
            $('[name="unitso"]').val(data.unit).prop("readonly", true);
            $('[name="nmarea"]').val(data.nmarea).prop("readonly", true);
            $('[name="trxdate"]').val($('#dateOpname').val()).prop("readonly", true);

            /* ID BARANG SO & MOVING */
            //$('[name="idareaso"]').val(idarea).prop("readonly", false);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_marea' + '?var=' + data.idarea,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmarea, datax.items[0].idarea, true, true);
                $('[name="idareaso"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idareaso"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="idbarangso"]').val(idbarang).prop("readonly", false);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_item' + '?var=' + data.idbarang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                $('[name="idbarangso"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idbarangso"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="batchnew"]').val(data.batchso).prop("readonly", true);
            $.ajax({
                type: 'POST',
                url: HOST_URL + 'api/globalmodule/list_batch_item' ,
                dataType: 'json',
                delay: 250,
                data: {
                    'var': data.batch,
                    '_parameterx_': data.idbarang,

                },
            }).then(function (datax,params) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].batch, datax.items[0].batch, true, true);
                $('[name="batchnew"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="batchnew"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Update');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Update Posisi Barang'); // Set title to Bootstrap modal title


            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function saveOpnameStock() {
    var bttn = $('#btnSave');
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data akan disimpan, Apakah anda yakin...?',
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

            var validator = $('#form').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                // ajax adding data to database
                var form = $('#form');
                var formdata = false;
                if (window.FormData){
                    formdata = new FormData(form[0]);
                }
                $.ajax({
                    url: HOST_URL + 'stock/balance/saveStockOpname',
                    type: "POST",
                    data: formdata ? formdata : form.serialize(),
                    cache       : false,
                    contentType : false,
                    processData : false,
                    datatype : "JSON",
                    dataFilter: function (data) {
                        var json = jQuery.parseJSON(data);
                        console.log(json.status + 'HELLOWS');
                        if (json.status) //if success close modal and reload ajax table
                        {
                            Swal.fire({
                                title: 'Berhasil...!!!',
                                text: json.messages,
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
                                    //Swal.fire('Saved!', '', 'success')
                                    //reload_table()window.location.replace(HOST_URL + 'master/item')
                                    //window.location.reload()
                                    reload_table()
                                    $('#modal_form').modal('hide');
                                    bttn.prop('disabled', false);
                                }
                                // } else if (result.isDenied) {
                                //     Swal.fire('Changes are not saved', '', 'info')
                                // }
                            })
                            // var x = confirm(json.messages);
                            // if (x) return
                            //else return window.location.replace("<?php echo site_url('/trans/skperingatan')?>") + "";
                        } else {
                            //alert(json.messages);
                            Swal.fire({
                                title: 'Galat...!!!',
                                text: json.messages,
                                backdrop: true,
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                showDenyButton: false,
                                showCancelButton: true,
                                icon: 'error',
                                //denyButtonText: `Don't save`,
                            });
                        }
                        $('#btnSave').text('save'); //change button text
                        $('#btnSave').attr('disabled', false); //set button enable


                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // alert('Gagal Menyimpan / Ubah data / data sudah ada');
                        swal({
                            title: "Galat!!",
                            text: json.messages,
                            type: "error"
                        });
                        $('#btnSave').text('save'); //change button text
                        $('#btnSave').attr('disabled', false); //set button enable

                    }
                });
                //alert("FInish Input");
            }


        } else if (result.isDenied) {
            return false;
        }
    })

}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH GROUP ++++++++++++++++++++++++++++++++++++++++//

var defaultInitialGroup = '';
$("#idbarangso").select2({
    placeholder: "Pilih detail barang reclass",
    allowClear: true,
    //minimumInputLength: 0, // only start searching when the user has input 3 or more characters
    //maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item',
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
            var searchTerm = $("#idbarangso").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmbarang, data.items[0].idbarang, true, true);
                $('#idbarangso').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#idbarangso').trigger({
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
    $("#batchnew").val(null).trigger('change');
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
$("#idareaso").select2({
    placeholder: "Pilih area untuk so item ini",
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
    var markup ="<div class='select2-result-repository__description'>" + repo.nmarea +"</div>";
    return markup;
}

function formatAreaSelection(repo) {
    return repo.nmarea || repo.text;
}

/* TABLE SC_TRX.MOVING */
function tableStockOpname(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tsearchmoving');
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
            "autoWidth": false,
            "responsive": true,
            "bFilter":true,
            "ajax": {
                "url": HOST_URL + 'stock/balance/list_StockOpname',
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



function reload_table_moving()
{
    var table = $('#tsearchmoving');
    table.DataTable().ajax.reload(); //reload datatable ajax
}

/* SCANNER */
function open_scan()
{
    //$('[name="dob"]').datepicker('update',data.dob);
    read_qrcode();
    $('#open_scan').modal('show'); // show bootstrap modal when complete loaded
    $('.modal-title').text('Open Scanner'); // Set title to Bootstrap modal title
}
function read_qrcode(){
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
        "qr-reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);

}

var audio = document.getElementById('chatAudio');
function play(){
    audio.play()
}


$("#batchnew").select2({
    placeholder: "Choose Your Specification/Batch",
    allowClear: true,
    // maximumSelectionLength: 1,
    // multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_batch_item',
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
                _paramglobal_: "",
                _parameterx_: $("#idbarangso").val(),
                term: params.term,
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#batchnew").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].batch, true, true);
                $('#batchnew').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#batchnew').trigger({
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
    templateResult: formatBatch, // omitted for brevity, see the source of this page
    templateSelection: formatBatchSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {

});

/* Format Group */
function formatBatch(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.batch +"</div>";
    return markup;
}
function formatBatchSelection(repo) {
    return repo.batch || repo.text;
}
function new_spec(){
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
            return { newbatch: newbatch }
        }
    }).then((result) => {
        $.ajax({
            type: "POST",
            url: HOST_URL + 'api/globalmodule/add_newbatch' + '',
            dataType: 'json',
            data: {
                'idbarang' : $('[name="idbarangso"]').val(),
                'batch' : result.value.newbatch,
            },
            success: function (datax) {
                if (datax.status){
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


$(document).ready(function() {
    tableStockOpname();
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
    if ($('[name="type"]').val() === 'EDIT') {
        documentReadable();
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        documentReadableDetail();
    }


});