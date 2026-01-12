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

function tableBbmTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablebbmTrx');
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
                "url": HOST_URL + 'stock/bbm/list_bbm',
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

function reload_tableBbmTrx()
{
    var table = $('#tablebbmTrx');
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
                "url": HOST_URL + 'stock/bbm/list_balance_transfers',
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
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: false //Display loader!
    // });
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'stock/bbm/showing_item_bbm_mst' + '?var=' + $('[name="docno"]').val(),
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
            //$('[name="idgroup"]').val(json.dataTables.items[0].idgroup);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + json.dataTables.items[0].idlocation,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlocation, datax.items[0].idlocation, true, true);
                $('[name="idlocation"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idlocation"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $('[name="docref"]').val(json.dataTables.items[0].docref);
            $('[name="docjns"]').val(json.dataTables.items[0].docjns);
            $('[name="description"]').val(json.dataTables.items[0].description);
            //$('[name="chold"]').val(json.dataTables.items[0].chold.trim()).trigger('change');

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

var defaultInitialGroupBrng = '';
$("#idbarang").select2({
    placeholder: "Type/ Scan your barcode item",
    allowClear: true,
    minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: true,
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
                _paramglobal_: defaultInitialGroupBrng,
                _parameterx_: defaultInitialGroupBrng,
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
}).on("select2:select", function () {
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: true //Display loader!
    // });
    var valtype = 'INPUT';
    var valid = '0';
    var validlocation = '';
    var validarea = '';
    var validbarang = $(this).val()+'';
    var valqtyonhand = '0';
    var valqtyonhandtrans = '0';
    var valunit = '';
    var valdescription = '';

    var fillData = {
        'success': true,
        'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
        'message': '',
        'body': {
            type: valtype,
            id: valid,
            idlocation: validlocation,
            idarea: validarea,
            idbarang: validbarang,
            qtyonhand: valqtyonhand,
            qtyonhandtrans: valqtyonhandtrans,
            unit: valunit,
            description: valdescription,
        },
    };
    $.ajax({
        type: "POST",
        url: HOST_URL + 'stock/bbm/saveDetailBbm' + '',
        dataType: 'json',
        contentType: "application/json",
        data: JSON.stringify(fillData),
        success: function (datax) {
            if (datax.status) {
                $("#loadMe").modal("hide");
                // window.location.replace(HOST_URL + 'trans/referensisdm')
                var table = $('#tabbmdtl');
                table.DataTable().ajax.reload(); //reload datatable ajax

                /*Swal.fire({
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
                    if (result.isConfirmed) {
                        // window.location.replace(HOST_URL + 'trans/referensisdm')
                        var table = $('#tabbmdtl');
                        table.DataTable().ajax.reload(); //reload datatable ajax
                        $("#loadMe").modal("hide");
                    }
                })*/
            } else {
                $("#loadMe").modal("hide");
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: datax.messages,
                    backdrop: true,
                    allowOutsideClick: false,
                })
                var table = $('#tabbmdtl');
                table.DataTable().ajax.reload(); //reload datatable ajax

            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $("#loadMe").modal("hide");
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Unable To Response Data',
                backdrop: true,
                allowOutsideClick: false,
            })
            var table = $('#tabbmdtl');
            table.DataTable().ajax.reload(); //reload datatable ajax

        }
    });
    console.log('Selecting ID =>' + $(this).val());
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
var defaultInitialGroup = $("#idlocationm").val();
$("#idarea").select2({
    placeholder: "Type/ Scan your barcode position area",
    allowClear: true,
    minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
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
                _paramglobal_: defaultInitialGroup,
                _parameterx_: defaultInitialGroup,
                term: params.term,
            };
        },
        processResults: function(data, params) {

            var searchTerm = $("#idarea").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmarea, data.items[0].idarea, true, true);
                $('#idarea').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#idarea').trigger({
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


/* TABLE SC_TRX.MOVING */
function tableBbmDetail(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tabbmdtl');
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
                "url": HOST_URL + 'stock/bbm/list_tmp_bbm_dtl',
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

function reload_table_dtlbbm()
{
    var table = $('#tabbmdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


$('#formBbmDetail').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        onhandtrans: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },


    },
    excluded: [':disabled']
});


function edit_bbm(id)
{
    save_method = 'input';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formBbmDetail').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formBbmDetail')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'stock/bbm/showing_item_bbm_dtl' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('UPDATE');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="idlocationm"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangnm"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarang"]').val(data.nmbarang).prop("readonly", true);
            $('[name="onhand"]').val(data.onhand).prop("readonly", true);
            $('[name="onhanddirty"]').val(data.onhanddirty).prop("readonly", false);
            $('[name="onhandtrans"]').val(data.onhandtrans).prop("readonly", false);
            $('[name="unit"]').val(data.unit).prop("readonly", true);
            //$('[name="idarea"]').val(data.idarea).prop("readonly", true);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_marea' + '?var=' + data.idarea,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmarea, datax.items[0].idarea, true, true);
                $('[name="idarea"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idarea"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="descriptionm"]').val(data.description).prop("readonly", false);


            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Process');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Update Item Bukti Barang Masuk'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_bbm(id)
{
    save_method = 'update';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formBbmDetail').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formBbmDetail')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'stock/bbm/showing_item_bbm_dtl' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('DELETE');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="idlocationm"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangnm"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarang"]').val(data.nmbarang).prop("readonly", true);
            $('[name="onhand"]').val(data.onhand).prop("readonly", true);
            $('[name="onhanddirty"]').val(data.onhanddirty).prop("readonly", true);
            $('[name="onhandtrans"]').val(data.onhandtrans).prop("readonly", true);
            $('[name="unit"]').val(data.unit).prop("readonly", true);
            //$('[name="idarea"]').val(data.idarea).prop("readonly", true);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_marea' + '?var=' + data.idarea,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmarea, datax.items[0].idarea, true, true);
                $('[name="idarea"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idarea"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="descriptionm"]').val(data.description).prop("readonly", true);
            $('[name="idarea"]').prop("disabled", true);



            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Item Bukti Barang Masuk'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function save_dtlbbm()
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

            var validator = $('#formBbmDetail').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var valid = $('[name="id"]').val();
                var validlocation = $('[name="idlocationm"]').val();
                var validarea = $('[name="idarea"]').val()+'';
                var validbarang = $('[name="idbarangnm"]').val();
                var valqtyonhand = $('[name="onhand"]').val();
                var valqtyonhandtrans = $('[name="onhandtrans"]').val();
                var valqtyonhanddirty = $('[name="onhanddirty"]').val();
                var valunit = $('[name="unit"]').val();
                var valdescription = $('[name="descriptionm"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        type: valtype,
                        id: valid,
                        idlocation: validlocation,
                        idarea: validarea,
                        idbarang: validbarang,
                        qtyonhand: valqtyonhand,
                        qtyonhandtrans: valqtyonhandtrans,
                        qtyonhanddirty: valqtyonhanddirty,
                        unit: valunit,
                        description: valdescription,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'stock/bbm/saveDetailBbm' + '',
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
                                    reload_table_dtlbbm();
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
                            reload_table_dtlbbm();
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
                        reload_table_dtlbbm();
                        $('#modal_form').modal('hide');
                        bttn.prop('disabled', false);
                    }
                });
            }
        }
        bttn.prop('disabled', false);
    });
}

/* TABEL LIST FINAL */
function tableList_Lbm(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tlistlbm_wacc');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "ajax": {
                "url": HOST_URL + 'stock/bbm/list_lbm_pagination',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
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

function reloadTlistLbm()
{
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}
/* TABEL LIST PO FINAL */


$(document).ready(function() {
    tableBbmTrx();
    tableBbmDetail();
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
    //console.log($('[name="type"]').val());
    if ($('[name="type"]').val() === 'INPUT' || $('[name="type"]').val() === 'UPDATE' || $('[name="type"]').val() === 'DELETE' ) {
        documentReadable();
    }
    $("#loadMe").modal("hide");

    // TESTING
    tableList_Lbm();


});