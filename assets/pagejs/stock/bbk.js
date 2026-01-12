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

function tablebbkTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablebbkTrx');
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
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'stock/bbk/list_bbk',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.idsection = $('#idsection').val();
                    data.status = $('#status_filter').val(); //A,P,S,ALL
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

function reload_tablebbkTrx()
{
    var table = $('#tablebbkTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}
$('#btn-filter-tx').click(function(){ //button filter event click
    var table = $('#tablebbkTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tablebbkTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

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
                "url": HOST_URL + 'stock/bbk/list_balance_bbk',
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
        url: HOST_URL + 'stock/bbk/showing_item_bbk_mst' + '?var=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            // Fetch the preselected item, and add to the control
            $('[name="idbarang"]').val(json.dataTables.items[0].idbarang);
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

            //$('[name="idsection"]').val(json.dataTables.items[0].idsection);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_costcenter' + '?var=' + json.dataTables.items[0].idsection,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmcostcenter, datax.items[0].idcostcenter, true, true);
                $('[name="idsection"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idsection"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="descsection"]').val(json.dataTables.items[0].descsection);
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
            $("#loadMe").modal("hide");
            console.log("Failed To Loading Data");
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
    console.log('Selecting =>' + $(this).val());
    var table = $('#tsearchitem');
    table.DataTable().ajax.reload();
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
function tablebbkDetail(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tabbkdtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": false,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter":true,
            "iDisplayLength": -1,
            "ajax": {
                "url": HOST_URL + 'stock/bbk/list_tmp_bbk_dtl',
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



function reload_table_dtlbbk()
{
    var table = $('#tabbkdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


$('#formbbkDetail').bootstrapValidator({
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


function add_bbk(id)
{
    save_method = 'input';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formbbkDetail').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formbbkDetail')[0].reset(); // reset form on modals
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
            $('[name="idlocationm"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangnm"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarang"]').val(data.nmbarang).prop("readonly", true);
            $('[name="idarea"]').val(data.idarea).prop("readonly", true);
            $('[name="nmarea"]').val(data.nmarea).prop("readonly", true);
            $('[name="batch"]').val(data.batch).prop("readonly", true);
            $('[name="onhandtrans"]').val(data.sisa).prop("readonly", false);
            $('[name="unit"]').val(data.unit).prop("readonly", true);
            $('[name="onhand"]').val(data.sisa).prop("readonly", true);
            $('[name="unitonhand"]').val(data.unit).prop("readonly", true);
            $('[name="descriptionm"]').val(data.description).prop("readonly", false);

            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Process');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Input Pemakaian Barang (PBL)'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function edit_bbk(id)
{
    save_method = 'input';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formbbkDetail').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formbbkDetail')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'stock/bbk/showing_item_bbk_dtl' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('UPDATE');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="idlocationm"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangnm"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarang"]').val(data.nmbarang).prop("readonly", true);
            $('[name="idarea"]').val(data.idarea).prop("readonly", true);
            $('[name="nmarea"]').val(data.nmarea).prop("readonly", true);
            $('[name="batch"]').val(data.batch).prop("readonly", true);
            $('[name="onhandtrans"]').val(data.onhandtrans).prop("readonly", false);
            $('[name="unit"]').val(data.unit).prop("readonly", true);
            $('[name="onhand"]').val(data.sisa).prop("readonly", true);
            $('[name="unitonhand"]').val(data.unit).prop("readonly", true);
            $('[name="descriptionm"]').val(data.description).prop("readonly", false);



            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Process');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Update Pemakaian Barang (PBL)'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_bbk(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formbbkDetail').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formbbkDetail')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'stock/bbk/showing_item_bbk_dtl' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('DELETE');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="idlocationm"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangnm"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarang"]').val(data.nmbarang).prop("readonly", true);
            $('[name="idarea"]').val(data.idarea).prop("readonly", true);
            $('[name="nmarea"]').val(data.nmarea).prop("readonly", true);
            $('[name="batch"]').val(data.batch).prop("readonly", true);
            $('[name="onhandtrans"]').val(data.onhandtrans).prop("readonly", true);
            $('[name="unit"]').val(data.unit).prop("readonly", true);
            $('[name="onhand"]').val(data.sisa).prop("readonly", true);
            $('[name="unitonhand"]').val(data.unit).prop("readonly", true);
            $('[name="descriptionm"]').val(data.description).prop("readonly", true);

            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Process');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function save_dtlbbk()
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

            var validator = $('#formbbkDetail').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var valid = $('[name="id"]').val();
                var validlocation = $('[name="idlocationm"]').val();
                var validarea = $('[name="idarea"]').val()+'';
                var validbarang = $('[name="idbarangnm"]').val();
                var valqtyonhand = $('[name="qtyonhand"]').val();
                var valqtyonhandtrans = $('[name="onhandtrans"]').val();
                var valunit = $('[name="unit"]').val();
                var valdescription = $('[name="descriptionm"]').val();
                var valbatch = $('[name="batch"]').val();

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
                        batch: valbatch,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'stock/bbk/saveDetailbbk' + '',
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
                                    reload_table_dtlbbk();
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
                            reload_table_dtlbbk();
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
                        reload_table_dtlbbk();
                        $('#modal_form').modal('hide');
                        bttn.prop('disabled', false);
                    }
                });
            }
        }
        bttn.prop('disabled', false);
    });
}


// COST CENTER
function formatCostcenter(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idcostcenter +"   <i class='fa fa-circle-o'></i>   "+ repo.nmcostcenter +"</div>";
    return markup;
}

function formatCostcenterSelection(repo) {
    return repo.nmcostcenter || repo.text;
}
//var defaultInitialDivision = $("#newdept").val();
$("#idsection").select2({
    placeholder: "Ketik/Pilih Bagian",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_costcenter',
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
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCostcenter, // omitted for brevity, see the source of this page
    templateSelection: formatCostcenterSelection // omitted for brevity, see the source of this page
});


function open_scan()
{
    //$('[name="dob"]').datepicker('update',data.dob);
    read_qrcode();
    $('#open_scan').modal('show'); // show bootstrap modal when complete loaded
    $('.modal-title').text('Open Scanner'); // Set title to Bootstrap modal title
}

function open_scan_lpb()
{
    //$('[name="dob"]').datepicker('update',data.dob);
    read_qrcode_lpbscan();
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

function read_qrcode_lpbscan(){
    function onScanSuccess(decodedText, decodedResult) {
        play();
        //alert(`Code scanned = ${decodedText}`, decodedResult);
        //alert('Code scanned Kintil= ' + decodedText + '');
        //$('#searchitem').val(decodedText).trigger('keyup');

        $('#doc_idrev').val(decodedText);
        $('#open_scan').modal('hide');

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

/* TABEL LIST */
function tableListPblWacc(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tlistpbl_wacc');
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
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'stock/bbk/list_pbl_wacc_pagination',
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

function reloadTlistPblWacc()
{
    var table = $('#tlistpbl_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-wacc').click(function(){ //button filter event click
    var table = $('#tlistpbl_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-wacc').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tlistpbl_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

var defaultInitialSupplier = '';
$("#namasupplier").select2({
    placeholder: "Type/Chose Your Supplier",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_supplier',
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
                _paramglobal_: defaultInitialSupplier,
                _parameterx_: defaultInitialSupplier,
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
    templateResult: formatSupplier, // omitted for brevity, see the source of this page
    templateSelection: formatSupplierSelection // omitted for brevity, see the source of this page
});
/* Format formatUnit */
function formatSupplier(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.nmsupplier +"</div>";
    return markup;
}

function formatSupplierSelection(repo) {
    return repo.nmsupplier || repo.text;
}

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
        data: function(params) {
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
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatItem, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
    //var table = $('#tsearchitem');
    //table.DataTable().ajax.reload(); //reload datatable ajax
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


$('#formLoadLPB').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        docnovoid: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },


    },
    excluded: [':disabled']
});

function load_from_lpb()
{
    save_method = 'input';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formLoadLPB').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formLoadLPB')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    $('[name="type"]').val('UPDATE');
    $('[name="docref"]').val('').prop("readonly", false);

    //$('[name="idlocation"]').prop('disabled', true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Process');
    //$('[name="dob"]').datepicker('update',data.dob);
    $('#modal_form_load_lpb').modal('show'); // show bootstrap modal when complete loaded
    $('.modal-title').text('Paste/Ketik Dokumen LPB Disini Untuk Meload Detail Barang'); // Set title to Bootstrap modal title
    //.removeClass("btn-primary").addClass("btn-danger"); // set button

}


$('#formLoadLPB').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        doc_idrev: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },


    },
    excluded: [':disabled']
});

function saveLoadLpb(){
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data lpb akan ditarik sebagai pengeluaran barang,pastikan stock masih tersedia & belum ada pengeluaran pemakaian!!!',
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

            var validator = $('#formLoadLPB').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var valdoc_idrev = $('[name="doc_idrev"]').val();
                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        type: valtype,
                        doc_idrev: valdoc_idrev,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'stock/bbk/load_bbm_to_bbk' + '',
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
                                    reload_table_dtlbbk();
                                    $('#modal_form_load_lpb').modal('hide');
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
                            reload_table_dtlbbk();
                            $('#modal_form_load_lpb').modal('hide');
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
                        reload_table_dtlbbk();
                        $('#modal_form_load_lpb').modal('hide');
                        bttn.prop('disabled', false);
                    }
                });
            }
        }
        bttn.prop('disabled', false);
    });
}

$(document).ready(function() {
    tablebbkTrx();
    tablebbkDetail();
    tableItem();
    tableListPblWacc();


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
    console.log($('[name="type"]').val());
    if ($('[name="type"]').val() === 'INPUT' || $('[name="type"]').val() === 'UPDATE' || $('[name="type"]').val() === 'DELETE' ) {
        documentReadable();
    }

    $("#loadMe").modal("hide");
    //read_qrcode();

});