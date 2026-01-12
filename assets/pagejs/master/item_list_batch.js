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
        var table = $('#tsearchitem').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
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
                "url": HOST_URL + 'master/item/list_batch',
                "type": "POST",
                "data": function(data) {
                    data.idlocation_get = $('#idlocation_get').val();
                    data.searchfilter = $('#searchitem').val();
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


        $('#delete_batch').on('click', function(e){
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
                        text: 'Beberapa Master Barang Akan Dihapus, Dan Tidak Dapat Dikambalikan Yakin? ' + e,
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
                                url: HOST_URL + 'master/item/delete_batch_dtl',
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
                                        reload_table();
                                        windows.location.reload();
                                    }
                                }, error: function (jqXHR, textStatus, errorThrown) {
                                    // alert('Gagal Menyimpan / Ubah data / data sudah ada');
                                    swal({
                                        title: "Galat!!",
                                        text: json.messages,
                                        type: "error"
                                    });
                                    reload_table();

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

$('#formInputsearchitem').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        idbarang: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmbarang: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        unit: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idbarcode: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        deflocation: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        defarea: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        chold: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        }
    },
    excluded: [':disabled']
});

function saveInputsearchitem() {

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

            var validator = $('#formInputsearchitem').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                // ajax adding data to database
                var form = $('#formInputsearchitem');
                var formdata = false;
                if (window.FormData){
                    formdata = new FormData(form[0]);
                }
                $.ajax({
                    url: HOST_URL + 'master/item/saveDataItem',
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
                            }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    //Swal.fire('Saved!', '', 'success')
                                    window.location.replace(HOST_URL + 'master/item')
                                    //window.location.reload()
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
                                showCancelButton: false,
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

var defaultInitialGroup = 'BRG';
$("#idgroup").select2({
    placeholder: "Type/Chose Group",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mgroup',
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
    templateResult: formatGroup, // omitted for brevity, see the source of this page
    templateSelection: formatGroupSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    //$("#newsubdept option[value]").remove();
    //var newOptions = []; // the result of your JSON request
    //$("#id_subdept").val(null).trigger('change');
    //console.log($("#newdept").val());
});
/* Format Group */
function formatGroup(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idgroup +"   <i class='fa fa-circle-o'></i>   "+ repo.nmgroup +"</div>";
    return markup;
}

function formatGroupSelection(repo) {
    return repo.nmgroup || repo.text;
}



var defaultInitialUnit = 'UNIT';
$("#unit").select2({
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
    $("#subunit option[value]").remove();
    var newOptions = []; // the result of your JSON request
    $("#subunit").val(null).trigger('change');
    //console.log($("#newdept").val());
});
/* Format formatUnit */
function formatUnit(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idunit +"</div>";
    return markup;
}

function formatUnitSelection(repo) {
    return repo.idunit || repo.text;
}



var defaultInitialSubUnit = 'SUBUNIT';
$("#subunit").select2({
    placeholder: "Type/Chose Your Sub Unit",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_subunit',
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
                _paramglobal_: $("#unit").val(),
                _parameterx_: $("#unit").val(),
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


var defaultInitialLocation = '';
$("#deflocation").select2({
    placeholder: "Type/Chose Location",
    allowClear: true,
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
}).on("select2:selecting", function () {
    $("#defarea option[value]").remove();
    var newOptions = []; // the result of your JSON request
    $("#defarea").val(null).trigger('change');
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



var defaultInitialArea = '';
$("#defarea").select2({
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
                _paramglobal_: $("#deflocation").val(),
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
$('#searchalocation').keyup(function(){

    //readStockBalance($(this).val())
    readStockStiBalance($(this).val());
});
function readStockStiBalance(e)
{
    var parameterx = e;
    //console.log(HOST_URL + 'stock/balance/show_sti_allocation_map?docno=' + e + ' -- ');

    $('#inlineframe').replaceWith('' +
        '<iframe id="inlineframe"' +
        'title="Frame Report"' +
        'width="100%"' +
        'height="690px"' +
        //'src="http://localhost/ci4-inventory/stock/balance/show_sti_allocation_map?docno=010110206000001">' +
        'src="' + HOST_URL + 'stock/balance/show_sti_allocation_map?docno=' + e  +'">' +
        '</iframe>' +
        '' );
}

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

var defaultInitialLocation = '';
$("#idlocation").select2({
    placeholder: "Pilih Gudang Tujuan",
    allowClear: true,
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    //maximumSelectionLength: 1,
    multiple: false,
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
}).on("select2:selecting", function () {

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
$(document).ready(function() {

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