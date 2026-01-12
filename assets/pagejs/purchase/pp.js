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

function tablePPTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tableppTrx');
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
                "url": HOST_URL + 'purchase/purchaseorder/list_pp',
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

function reload_tablePPTrx()
{
    var table = $('#tableppTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function(){ //button filter event click
    var table = $('#tableppTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tableppTrx');
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
        url: HOST_URL + 'purchase/purchaseorder/showing_item_pp_mst' + '?var=' + $('[name="docno"]').val(),
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
            $('[name="estdate"]').val(json.dataTables.items[0].estdate1);
            //$('[name="fjurnal"]').val(json.dataTables.items[0].fjurnal);
            $('[name="fjurnal"]').val(json.dataTables.items[0].fjurnal.trim()).trigger('change');
            $('[name="njurnal"]').val(json.dataTables.items[0].njurnal);
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
            //$('[name="idcostcenter"]').val(json.dataTables.items[0].idcostcenter);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_costcenter' + '?var=' + json.dataTables.items[0].idcostcenter,
                dataType: 'json',
                delay: 300,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
                var option = new Option(datax.items[0].nmcostcenter, datax.items[0].idcostcenter, true, true);
                $('[name="idcostcenter"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idcostcenter"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="docref"]').val(json.dataTables.items[0].docref);

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
    placeholder: "Choose Your Item List",
    allowClear: true,
    // maximumSelectionLength: 1,
    // multiple: false,
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
}).on("select2:select", function (e) {
    var data = e.params.data;
    $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    $("#batch").val(null).trigger('change');
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

/* DEFAULT BATCH */
var defaultInitialBatch = '';
$("#batch").select2({
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
                _paramglobal_: defaultInitialBatch,
                _parameterx_: $("#idbarang").val(),
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
// ID POSTION
var defaultInitialGroup = $("#idlocationm").val();
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


/* TABLE PP DETAIL */
function tablePPDetail(){
        /* Tabel PP Detail */
        var table = $('#tabppdtl').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
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
                "url": HOST_URL + 'purchase/purchaseorder/list_tmp_pp_dtl',
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
                {
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                },
            ],
            "select": {
                'style': 'multi'
            },
            "order": [[1, 'asc']]
            });

        // Handle form submission event
    $('#update_pp').on('click', function(e){
        // ajax adding data to database
        var form = $('#frm-example');
        var formdata = false;
        if (window.FormData){
            formdata = new FormData(form[0]);
        }
        var rows_selected = table.column(0).checkboxes.selected();
        var length_selected = rows_selected.length;
        // console.log(" TEST " + table.column(0).checkboxes.selected().length);

        if (length_selected === 1) {
            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId){
                // console
                // Create a hidden element
                $(form).append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'id[]')
                        .val(rowId)
                );
                /* UPDATE PP SETELAH PEMILIHAN & CLICK*/
                update_pp(rowId);
            });
            // Edit
        } else {
            Swal.fire({
                title: 'Ooops..!!!',
                text: '0 Item dipilih / Pemilihan Item tidak boleh lebih dari 1 ' + e,
                backdrop: true,
                allowOutsideClick: false,
                showConfirmButton: false,
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: `Ok`,
                icon: 'question',
                //denyButtonText: `Don't save`,
            });
        }
        // Prevent actual form submission
        e.preventDefault();
    });

    $('#delete_pp').on('click', function(e){
        // ajax adding data to database
        var form = $('#frm-example');
        var formdata = false;
        if (window.FormData){
            formdata = new FormData(form[0]);
        }
        var rows_selected = table.column(0).checkboxes.selected();
        var length_selected = rows_selected.length;
       //console.log(" TEST " + table.column(0).checkboxes.selected().length);

        if (length_selected > 0) {
            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId){
                // console
                // Create a hidden element
                $(form).append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'id[]')
                        .val(rowId)
                );
                /* DELETE PP SETELAH PEMILIHAN & CLICK*/
                Swal.fire({
                    title: 'Peringatan..!!!',
                    text: 'Beberapa Detail Barang Akan Dihapus, Yakin? ' + e,
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
                        /* AJAX EDIT HERE */
                        // console.log('BEKANTAL');
                        var formdata = false;
                        if (window.FormData){
                            formdata = new FormData(form[0]);
                        }
                        $.ajax({
                            url: HOST_URL + 'purchase/purchaseorder/delete_pp_dtl',
                            type: "POST",
                            data: formdata ? formdata : form.serialize(),
                            cache       : false,
                            contentType : false,
                            processData : false,
                            datatype : "JSON",
                            dataFilter: function (data) {
                                var json = jQuery.parseJSON(data);
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
                                    });
                                    reload_table_pp_dtl();
                                    windows.location.reload();
                                }
                            }, error: function (jqXHR, textStatus, errorThrown) {
                                // alert('Gagal Menyimpan / Ubah data / data sudah ada');
                                swal({
                                    title: "Galat!!",
                                    text: json.messages,
                                    type: "error"
                                });
                                reload_table_pp_dtl();

                            }
                        });

                    }

                })
            });
            // Edit
        } else {
            Swal.fire({
                title: 'Ooops..!!!',
                text: 'Minimal 1 Pilihan' + e,
                backdrop: true,
                allowOutsideClick: false,
                showConfirmButton: false,
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: `Ok`,
                icon: 'question',
                //denyButtonText: `Don't save`,
            });
        }
        // Prevent actual form submission
        e.preventDefault();
    });

    $('#testing').on('click', function(e){
        // ajax adding data to database

        var form = $('#frm-example');
        var formdata = false;
        if (window.FormData){
            formdata = new FormData(form[0]);
        }

        var rows_selected = table.column(0).checkboxes.selected();

        // Iterate over all selected checkboxes
        $.each(rows_selected, function(index, rowId){
            // console
            // Create a hidden element
            $(form).append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'id[]')
                    .val(rowId)
            );
        });
        // FOR DEMONSTRATION ONLY
        // The code below is not needed in production

        // Output form data to a console
        $('#example-console-rows').text(rows_selected.join(","));

        // Output form data to a console
        $('#example-console-form').text($(form).serialize());

        // Remove added elements
        $('input[name="id\[\]"]', form).remove();

        // Prevent actual form submission
        e.preventDefault();
    });

}


function reload_table_pp_dtl()
{
    var table = $('#tabppdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}

$('#formPPMasters').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        iddept: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        docdate: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        estdate: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        // njurnal: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         },
        //     }
        // },
        fjurnal: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },

    },
    excluded: [':disabled']
});
$('#formPPdetail').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        onhand: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        descriptionm: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },

    },
    excluded: [':disabled']
});

function add_pp(id)
{
    save_method = 'input';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formPPdetail').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formPPdetail')[0].reset(); // reset form on modals
    $('#idbarang').val(null).trigger('change');
    $('#batch').val(null).trigger('change');
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    $('[name="typeDTL"]').val('INPUT');
    $('[name="idDTL"]').val(0).prop("readonly", true);
    $('[name="idlocationmDTL"]').val(0).prop("readonly", true);
    $('[name="unit"]').val(0).prop("readonly", true);


    $('[name="idbarang"]').prop('disabled',false);
    $('[name="batch"]').prop('disabled',false);
    $('#btnNew').prop('disabled',false);
    $('#unit').prop('disabled',true);

    //$('[name="idlocation"]').prop('disabled', true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Process');
    //$('[name="dob"]').datepicker('update',data.dob);
    $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
    $('.modal-title').text('Input Detail Barang'); // Set title to Bootstrap modal title
    //.removeClass("btn-primary").addClass("btn-danger"); // set button

}

function update_pp(id)
{
    save_method = 'input';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formPPdetail').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formPPdetail')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#idbarang').val(null).trigger('change');
    $('#batch').val(null).trigger('change');

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'purchase/purchaseorder/showing_item_pp_dtl' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="typeDTL"]').val('UPDATE');
            $('[name="idDTL"]').val(data.id).prop("readonly", false);
            $('[name="idlocationmDTL"]').val(data.idlocation).prop("readonly", false);
            /*triger select2*/
            //$('[name="idbarang"]').val(data.id).prop("readonly", false);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_item' + '?var=' + data.idbarang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                $('[name="idbarang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idbarang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            /*triger select2*/
            //$('[name="batch"]').val(data.id).prop("readonly", false);
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
                $('[name="batch"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="batch"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $('[name="onhand"]').val(data.onhandpo).prop("readonly", false);
            $('[name="unit"]').val(data.unit).prop("readonly", false);
            $('[name="descriptionm"]').val(data.description).prop("readonly", false);


            $('[name="idbarang"]').prop('disabled',true);
           //$('[name="batch"]').prop('disabled',true);
            //$('#btnNew').prop('disabled',true);
            $('#unit').prop('disabled',true);
            //$('[name="idlocation"]').prop('disabled', true);
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Process');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            }); // show bootstrap modal when complete loaded
            $('.modal-title').text('Ubah Data Barang'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
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
                'idbarang' : $('[name="idbarang"]').val(),
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
function saveMaster(){
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Akan menyimpan master...?',
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        if (result.isConfirmed)
        {
            var validator = $('#formPPMasters').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                // ajax adding data to database
                var form = $('#formPPMasters');
                var formdata = false;
                if (window.FormData){
                    formdata = new FormData(form[0]);
                }
                $.ajax({
                    url: HOST_URL + 'purchase/purchaseorder/save_pp_mst',
                    type: "POST",
                    data: formdata ? formdata : form.serialize(),
                    cache       : false,
                    contentType : false,
                    processData : false,
                    datatype : "JSON",
                    dataFilter: function (data) {
                        var json = jQuery.parseJSON(data);
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
                            });
                            window.location.replace(HOST_URL + 'purchase/purchaseorder/input_pp');
                        }
                    }, error: function (jqXHR, textStatus, errorThrown) {
                        // alert('Gagal Menyimpan / Ubah data / data sudah ada');
                        swal({
                            title: "Galat!!",
                            text: json.messages,
                            type: "error"
                        });
                        window.location.replace(HOST_URL + 'purchase/purchaseorder/input_pp');

                    }
                });
            }
        }
        else if (result.isDenied)
        {
            return false;
        }





    });
}
function save_dtlpp()
{
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data Permintaan Pembelian akan Proses, Apakah anda yakin...?',
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

            var validator = $('#formPPdetail').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="typeDTL"]').val();
                var valid = $('[name="idDTL"]').val();
                var validlocation = $('[name="idlocationmDTL"]').val();
                var validbarang = $('[name="idbarang"]').val();
                var valonhand = $('[name="onhand"]').val();
                var valunit = $('[name="unit"]').val();
                var valbatch = $('[name="batch"]').val();
                var valdescriptionm = $('[name="descriptionm"]').val();


                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        type: valtype,
                        id: valid,
                        idlocation: validlocation,
                        idbarang: validbarang,
                        onhand: valonhand,
                        unit: valunit,
                        batch: valbatch,
                        description: valdescriptionm,

                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'purchase/purchaseorder/saveDtlPP' + '',
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
                                    reload_table_pp_dtl();
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
                            reload_table_pp_dtl();
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

function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kddept +"   <i class='fa fa-circle-o'></i>   "+ repo.nmdept +"  </div>";
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
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialNewDept,
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
        success: function(data)
        {
            $("#njurnal").val(data.njurnal);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
});


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
$("#idcostcenter").select2({
    placeholder: "Ketik/Pilih Cost Center",
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


$(document).ready(function() {
    // Handle form submission event
    // Handle form submission event





    tablePPTrx();
    tablePPDetail();
    tableItem();
    //read_qrcode();
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