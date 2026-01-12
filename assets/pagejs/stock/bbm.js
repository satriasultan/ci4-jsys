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
            "responsive": false,
            "bFilter":true,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'stock/bbm/list_bbm',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namasupplier = $('#namasupplier').val();
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

function reload_tableBbmTrx()
{
    var table = $('#tablebbmTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function(){ //button filter event click
    var table = $('#tablebbmTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tablebbmTrx');
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
                "url": HOST_URL + 'stock/bbm/list_balance_bbm',
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
            $('[name="docjns"]').val(json.dataTables.items[0].docjns.trim()).trigger('change');
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
var defarea = $('[name="idlocation_tx"]').val();
$("#idarea").select2({
    placeholder: "Type/ Scan your barcode position area",
    allowClear: true,
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
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
                _paramglobal_: defarea,
                _parameterx_: defarea,
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
                    "render": function (data, type, full, meta) {
                        return "<div class='text-wrap width-200'>" + data + "</div>";
                    },
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


function add_bbm(id)
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
        url: HOST_URL + 'stock/bbm/showing_item' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('[name="type"]').val('INPUT');
            $('[name="id"]').val(0).prop("readonly", true);
            $('[name="idlocationm"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangnm"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarang"]').val(data.nmbarang).prop("readonly", true);
            $('[name="onhand"]').val(0).prop("readonly", true);
            $('[name="onhanddirty"]').val(0).prop("readonly", false);
            $('[name="onhandtrans"]').val(0).prop("readonly", false);
            $('[name="descriptionm"]').val($('[name="desc_master"]').val()).prop("readonly", false);
            $('[name="unit"]').val(data.unit).prop("readonly", true);
            //$('[name="idarea"]').val(data.idarea).prop("readonly", true);
            $.ajax({
                type: 'POST',
                url: HOST_URL + 'api/globalmodule/list_marea',
                dataType: 'json',
                delay: 250,
                data: {
                    'var': $('[name="idarea_default"]').val().trim(),
                    '_paramglobal_': data.idlocation,

                },
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



            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Process');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Input Laporan Penerimaan Barang (LPB)'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function test_show(id)
{

    $('#modalxx').modal('show'); // show bootstrap modal when complete loaded
    //.removeClass("btn-primary").addClass("btn-danger"); // set button

}

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

            $('[name="batch"]').val(null).trigger('change');
            $('[name="type"]').val('UPDATE');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="idlocationm"]').val(data.idlocation).prop("readonly", true);
            $('[name="idbarangnm"]').val(data.idbarang).prop("readonly", true);
            $('[name="nmbarang"]').val(data.nmbarang).prop("readonly", true);
            $('[name="onhand"]').val(data.onhand).prop("readonly", true);
            if (data.holdrow==='YES') {
                $('[name="batch"]').val(data.batch).prop("readonly", true);
            } else {
                var idb_ = data.idbarang.trim() !== "" ? data.idbarang: "X";
                var bat_ = data.batch.trim() ? data.batch: "X";
                //$('[name="batch"]').val(data.batch).prop("readonly", false);
                $.ajax({
                    type: 'POST',
                    url: HOST_URL + 'api/globalmodule/list_batch_item' ,
                    dataType: 'json',
                    delay: 250,
                    data: {
                        'var': bat_,
                        '_parameterx_': idb_,

                    },
                }).then(function (datax,params) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].batch, datax.items[0].batch, true, true);
                    $('[name="batch"]').append(option).trigger('change');

                    // manually trigger the `select2:select` event
                    $('[name="batch"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });

            }

            $('[name="onhanddirty"]').val(data.onhanddirty).prop("readonly", false);
            $('[name="onhandtrans"]').val(data.onhandtrans).prop("readonly", false);
            $('[name="unit"]').val(data.unit).prop("readonly", true);
            //$('[name="idarea"]').val(data.idarea).prop("readonly", true);
            $.ajax({
                type: 'POST',
                url: HOST_URL + 'api/globalmodule/list_marea',
                dataType: 'json',
                delay: 250,
                data: {
                    'var': data.idarea,
                    '_paramglobal_': data.idlocation,

                },
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
            // $('#modal_form').modal({
            //     backdrop: 'static',
            //     keyboard: false,
            //     show: true
            // }); // show bootstrap modal when complete loaded
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
            $('[name="batch"]').val(data.batch).prop("readonly", true);
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
                        qtyonhanddirty: valqtyonhanddirty,
                        unit: valunit,
                        description: valdescription,
                        batch: valbatch,
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
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'stock/bbm/list_lbm_pagination',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namasupplier = $('#namasupplier').val();
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

function reloadTlistLbm()
{
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tlistlbm_wacc');
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

/* TABEL LIST PO FINAL */


//OPEN SCANNER
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




$("#docjns").on("change", function () {
console.log($(this).val() + 'H');
    if ($(this).val()==='PO') {

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
                data: function(params) {
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
                processResults: function(data, params) {

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
            escapeMarkup: function(markup) {
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
            var markup ="<div class='select2-result-repository__description'>" + repo.docno +"</div>";
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

$("#batch").select2({
    placeholder: "Silahkan pilih spek, click x untuk reset pilihan",
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
                _parameterx_: $('[name="idbarangnm"]').val(),
                term: params.term,
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#batch").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].batch, true, true);
                $('#batch').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#batch').trigger({
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
                'idbarang' : $('[name="idbarangnm"]').val(),
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
// TESTING
    //console.log(defarea);
    tableList_Lbm();

    tableBbmTrx();
    tableBbmDetail();
    tableItem();
    read_qrcode();
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




});