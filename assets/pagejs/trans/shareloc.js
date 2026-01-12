/*
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza
 *  Copyright© 2020 .All rights reserved.
 *
 */


var save_method; //for save method string
var table;
"use strict";
function tableShareloc(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tshareloc');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "dom": 'Bfrtip',
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "buttons": [
                'pageLength','excel', 'pdf', 'print'
            ],
            "ajax": {
                "url": HOST_URL + 'trans/shareloc/list_shareloc',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#daterangefilter').val();
                    data.dept = $('#id_dept_filter').val();
                    data.attend = $('#attend').val();
                    data.attendstatus = $('#attendstatus').val();
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
    var table = $('#tshareloc');
    table.DataTable().ajax.reload(); //reload datatable ajax
}
$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tshareloc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tshareloc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
/* FOR INPUT FUNCTION */

function onReadyInput(){

    //datepicker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true,
        orientation: "top auto",
        todayBtn: true,
    });

}


function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__title'>" + repo.kddept + "</div>";
    if (repo.nmdept) {
        markup += "<div class='select2-result-repository__description'>" + repo.kddept +" <i class='fa fa-circle-o'></i> "+ repo.nmdept +"</div>";
    }
    return markup;
}

function formatNewdeptSelection(repo) {
    return repo.nmdept || repo.text;
}
var defaultInitialNewDept = '';
$("#id_dept_filter").select2({
    placeholder: "Ketik/Pilih Departemen",
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
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatNewdept, // omitted for brevity, see the source of this page
    templateSelection: formatNewdeptSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    //$("#newsubdept option[value]").remove();
    //var newOptions = []; // the result of your JSON request
    $("#newsubdept").val(null).trigger('change');
    //console.log($("#newdept").val());
});


/*sub departement*/
function formatNewsubdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__title'>" + repo.kdsubdept + "</div>";
    if (repo.nmsubdept) {
        markup += "<div class='select2-result-repository__description'>" + repo.kdsubdept +" <i class='fa fa-circle-o'></i> "+ repo.nmsubdept +"</div>";
    }
    return markup;
}

function formatNewsubdeptSelection(repo) {
    return repo.nmsubdept || repo.text;
}
//var defaultInitialNewSubDept = $("#newdept").val();
$("#newsubdept").select2({
    placeholder: "Ketik/Pilih Sub Departemen",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_subdepartmen',
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
                _paramglobal_: $("#newdept").val(),
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
    templateResult: formatNewsubdept, // omitted for brevity, see the source of this page
    templateSelection: formatNewsubdeptSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#newjabatan option[value]").remove();
    // var newOptions = []; // the result of your JSON request
    // $("#newjabatan").append(newOptions).val("").trigger("change");
    $("#newjabatan").val(null).trigger('change');
});





/* jabatan */
function formatJabatan(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__title'>" + repo.kdjabatan + "</div>";
    if (repo.nmjabatan) {
        markup += "<div class='select2-result-repository__description'>" + repo.kdjabatan +" <i class='fa fa-circle-o'></i> "+ repo.nmjabatan +"</div>";
    }
    return markup;
}

function formatJabatanSelection(repo) {
    return repo.nmjabatan || repo.text;
}
//var defaultInitialNewSubDept = $("#newdept").val();
$("#newjabatan").select2({
    placeholder: "Ketik/Pilih Jabatan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_jabatan',
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
                _paramglobal_: $("#newsubdept").val(),
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
    templateResult: formatJabatan, // omitted for brevity, see the source of this page
    templateSelection: formatJabatanSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#newjabatan option[value]").remove();
    // var newOptions = []; // the result of your JSON request
    // $("#newjabatan").append(newOptions).val("").trigger("change");
    $("#newlvljabatan").val(null).trigger('change');
});


/* lvl JABATAN */
function formatLvlJabatan(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__title'>" + repo.kdlvl + "</div>";
    if (repo.nmlvljabatan) {
        markup += "<div class='select2-result-repository__description'>" + repo.kdlvl +" <i class='fa fa-circle-o'></i> "+ repo.nmlvljabatan +"</div>";
    }
    return markup;
}

function formatLvlJabatanSelection(repo) {
    return repo.nmlvljabatan || repo.text;
}
//var defaultInitialNewSubDept = $("#newdept").val();
$("#newlvljabatan").select2({
    placeholder: "Ketik/Pilih Level Jabatan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_lvljabatan',
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
    templateResult: formatLvlJabatan, // omitted for brevity, see the source of this page
    templateSelection: formatLvlJabatanSelection // omitted for brevity, see the source of this page
});


function formatPlant(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.loccode +"   <i class='fa fa-circle-o'></i>   "+ repo.locaname +"</div>";
    return markup;
}

function formatPlantSelection(repo) {
    return repo.locaname || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#newplant").select2({
    placeholder: "Ketik/Pilih Plant",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_plant',
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
    templateResult: formatPlant, // omitted for brevity, see the source of this page
    templateSelection: formatPlantSelection // omitted for brevity, see the source of this page
});


$('#docref').change(function () {
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/induction/list_outstanding_mutasi',
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);

            $('[name="nmnewdept"]').val(json.items[0].nmnewdept);
            $('[name="nmolddept"]').val(json.items[0].nmolddept);
            $('[name="docrefx"]').val(json.items[0].docno);

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            //console.log("Failed To Loading Data");
        }
    });

});


$(document).ready(function() {
    $('#attendstatus').select2();
    $('#attend').select2();

    $(".datepicker").datepicker();
    $("#daterangefilter").daterangepicker({
        //singleDatePicker: true,
        //showDropdowns: true
    });
    $("#daterangefilter").val('');
    tableShareloc();


});