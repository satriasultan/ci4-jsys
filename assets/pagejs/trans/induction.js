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
function tableMutasiPromosi(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tinduction');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('trans/induction/list_induction'),
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
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
    var table = $('#tinduction');
    table.DataTable().ajax.reload(); //reload datatable ajax


}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tinduction');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tinduction');
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


function close_modal() {
    var validator = $('#form').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#form')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show');
}


function formatRepo(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__title'>" + repo.nik + " || " + repo.docno + "</div>";
    if (repo.nmlengkap) {
        markup += "<div class='select2-result-repository__description'>" + repo.nmlengkap +" <i class='fa fa-circle-o'></i> "+ repo.nmdept +"</div>";
    }
    return markup;
}

function formatRepoSelection(repo) {
    return repo.nmlengkap || repo.text;
}

var defaultInitial = '';
$("#docref").select2({
    placeholder: "Ketik Dokumen Mutasi / Nama Karyawan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'trans/induction/list_outstanding_mutasi',
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
                _paramglobal_: defaultInitial,
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
    templateResult: formatRepo, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
});


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
$("#newdept").select2({
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






function finishInput() {
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);


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
                var valdocno = $('[name="docno"]').val();
                var valtype = $('[name="type"]').val();

                var fillData = {
                    'success' : true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message' : '',
                    'body' : {
                        docno : valdocno,
                        type : valtype,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/induction/finalEntry' + '',
                    dataType: 'json',
                    contentType: "application/json",
                    data: JSON.stringify(fillData),
                    success: function (datax){
                        if (datax.status){
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
                                    window.location.replace(HOST_URL + 'trans/induction')
                                }
                            })
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        Swal.fire({
                            title: 'Galat...!!!',
                            text: 'Data Tidak Bisa Di Proses..!',
                            backdrop: true,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: `Ok`,
                            icon: 'error',
                            //denyButtonText: `Don't save`,
                        });
                        bttn.prop('disabled', false);
                        //console.log("Failed To Loading Data");
                    }
                });
            }
            bttn.prop('disabled', false);
            return false;

    })

}

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

function editMutasi(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data ini akan diubah, Anda yakin..?, Nik: ' + e,
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
            window.location.replace(HOST_URL + 'trans/mutpromot/editMutasi' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}
function detailMutasi(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data akan lihat data ini?, Nik: ' + e,
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
            window.location.replace(HOST_URL + 'trans/mutpromot/detailMutasi' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}


function documentReadable(){
    $("#loadMe").modal({
        backdrop: "static", //remove ability to close modal with click
        keyboard: false, //remove option to close with keyboard
        show: true //Display loader!
    });

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/induction/showOn',
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            $('[name="docrefx"]').val(json.dataTables.items[0].docref);
            $('[name="description"]').val(json.dataTables.items[0].description);
            $('[name="daterange"]').val(json.dataTables.items[0].daterange);
console.log(json.dataTables.items[0].daterange);
            var select2_kary = $('[name="docref"]');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'trans/induction/list_outstanding_mutasi' + '?var=' + json.dataTables.items[0].docref,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].docref, true, true);
                select2_kary.append(option).trigger('change');

                // manually trigger the `select2:select` event
                select2_kary.trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

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

    var status = $('[name="status"]').val();
    console.log(status);
    if (status !== '') {
        $('[name="description"]').prop('disabled', true);
        $('[name="daterange"]').prop('disabled', true);
        $('[name="docref"]').prop('disabled', true);
    }



    $("#loadMe").modal("hide");
}

function documentReadableDetail(){
    $("#loadMe").modal({
        backdrop: "static", //remove ability to close modal with click
        keyboard: false, //remove option to close with keyboard
        show: true //Display loader!
    });

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/induction/showoff',
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            $('[name="docrefx"]').val(json.dataTables.items[0].docref);
            $('[name="description"]').val(json.dataTables.items[0].description);
            $('[name="daterange"]').val(json.dataTables.items[0].daterange);
            console.log(json.dataTables.items[0].daterange);
            var select2_kary = $('[name="docref"]');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'trans/induction/list_outstanding_mutasi' + '?var=' + json.dataTables.items[0].docref,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].docref, true, true);
                select2_kary.append(option).trigger('change');

                // manually trigger the `select2:select` event
                select2_kary.trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

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

    var status = $('[name="status"]').val();
    console.log(status);
    if (status !== '') {
        $('[name="description"]').prop('disabled', true);
        $('[name="daterange"]').prop('disabled', true);
        $('[name="docref"]').prop('disabled', true);
    }



    $("#loadMe").modal("hide");
}


function tablePersetujuanMutasi(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tPersetujuan');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('trans/mutpromot/list_persetujuanmutasi'),
                "type": "POST",
                "data": function(data) {
                    data.newdept = $('#newdept').val();
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

function reloadPersetujuanMutasi()
{
    var table = $('#tPersetujuan');
    table.DataTable().ajax.reload(); //reload datatable ajax

}


$('#form').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        docref: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        daterange: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        description: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        }
    },
    excluded: [':disabled']
});

function save()
{
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Ingin Melanjutkan Input Detail...?',
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
                var valtype = $('[name="type"]').val();
                var valdocref = $('[name="docref"]').val();
                var valdaterange = $('[name="daterange"]').val();
                var valdescription = $('[name="description"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        docref: valdocref,
                        type: valtype,
                        daterange: valdaterange,
                        description: valdescription,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/induction/saveInductionMst' + '',
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
                                    //disable input atas
                                    window.location.replace(HOST_URL + 'trans/induction/input')
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
                        $('#modal_form').modal('hide');
                        bttn.prop('disabled', false);
                    }
                });
            }
        }
        //$('#modal_form').modal('hide');
        bttn.prop('disabled', false);

    });
}


$('#formInductionDetail').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        docno: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        }
    },
    excluded: [':disabled']
});
/* INPUT DETAIL INDUCTION */
function add_induction_detail()
{
    save_method = 'add';
    var validator = $('#formInductionDetail').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formInductionDetail')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modalInductionDetail').modal('show'); // show bootstrap modal
    $('.modal-title').text('Input Induksi Karyawan'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUTDETAIL');
    $('[name="id"]').val();
    $('#btnSaveDetail').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}
function delete_induction_detail(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formInductionDetail').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formInductionDetail')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'trans/induction/showingInductionDetail' + '/?var=' + id,
        type: "GET",
        dataType: "JSON",
        dataFilter: function(data)
        {
            var json = jQuery.parseJSON(data);
            //console.log(json.dataTables.items[0].docid);
            $('[name="type"]').val('DELETEDETAIL');
            $('[name="docid"]').val(json.dataTables.items[0].docid);
            $('[name="docnamedetail"]').val(json.dataTables.items[0].docname).prop("readonly", true);
            $('[name="docdatedetail"]').val(json.dataTables.items[0].docdate1).prop("readonly", true);
            $('[name="descriptiondetail"]').val(json.dataTables.items[0].description).prop("readonly", true);;
            $('.chold').prop("disabled", true);
            $('#btnSaveDetail').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modalInductionDetail').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Detail'); // Set title to Bootstrap modal title

        }
    });

}
function save_induction_detail()
{
    var bttn = $('#btnSaveDetail');
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

            var validator = $('#formInductionDetail').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var valdocid = $('[name="docid"]').val();
                var valdocref = $('[name="docrefdetail"]').val();
                var valdocname = $('[name="docnamedetail"]').val();
                var valdocdate = $('[name="docdatedetail"]').val();
                var valdescription = $('[name="descriptiondetail"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        docid: valdocid,
                        docref: valdocref,
                        type: valtype,
                        docname: valdocname,
                        docdate: valdocdate,
                        description: valdescription,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/induction/saveInductionDetail' + '',
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
                                    reload_table_detail();
                                    $('#modalInductionDetail').modal('hide');
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
                            reload_table_detail();
                            $('#modalInductionDetail').modal('hide');
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
                        $('#modal_form').modal('hide');
                        bttn.prop('disabled', false);
                    }
                });
            }
        }
        //$('#modal_form').modal('hide');
        bttn.prop('disabled', false);

    });
}



function tableInductionDetail(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tinductiondetail');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('trans/induction/list_induction_detail'),
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
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

function reload_table_detail()
{
    var table = $('#tinductiondetail');
    table.DataTable().ajax.reload(); //reload datatable ajax


}

/* FINISH INPUT CANCEL & EDIT INDUCTION */
function finalInduction(e) {
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);

    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data akan diproses, & Akan dimintakan persetujuan ke direksi, anda yakin...?',
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
            var valdocno = $('[name="docno"]').val();
            var valtype = $('[name="type"]').val();

            var fillData = {
                'success' : true,
                'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                'message' : '',
                'body' : {
                    docno : valdocno,
                    type : valtype,
                },
            };
            $.ajax({
                type: "POST",
                url: HOST_URL + 'trans/induction/finalInduction' + '?var=' + e,
                dataType: 'json',
                contentType: "application/json",
                data: JSON.stringify(fillData),
                success: function (datax){
                    if (datax.status){
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
                                window.location.replace(HOST_URL + 'trans/induction')
                            }
                        })
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    Swal.fire({
                        title: 'Galat...!!!',
                        text: 'Data Tidak Bisa Di Proses..!',
                        backdrop: true,
                        allowOutsideClick: false,
                        showConfirmButton: true,
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: `Ok`,
                        icon: 'error',
                        //denyButtonText: `Don't save`,
                    });
                    bttn.prop('disabled', false);
                    //console.log("Failed To Loading Data");
                }
            });
        }
        bttn.prop('disabled', false);
        return false;

    })

}

function editInduction(e) {
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Anda akan Ubah Data ?',
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
            var valdocno = $('[name="docno"]').val();
            var valtype = $('[name="type"]').val();

            var fillData = {
                'success' : true,
                'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                'message' : '',
                'body' : {
                    docno : valdocno,
                    type : valtype,
                },
            };
            $.ajax({
                type: "POST",
                url: HOST_URL + 'trans/induction/editInduction' + '?var=' + e,
                dataType: 'json',
                contentType: "application/json",
                data: JSON.stringify(fillData),
                success: function (datax){
                    if (datax.status){
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
                                window.location.replace(HOST_URL + 'trans/induction/input')
                            }
                        })
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    Swal.fire({
                        title: 'Galat...!!!',
                        text: 'Data Tidak Bisa Di Proses..!',
                        backdrop: true,
                        allowOutsideClick: false,
                        showConfirmButton: true,
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: `Ok`,
                        icon: 'error',
                        //denyButtonText: `Don't save`,
                    });

                    //console.log("Failed To Loading Data");
                }
            });
        }
        bttn.prop('disabled', false);
        return false;

    })

}

function cancelInduction(e) {
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Anda akan Ubah Data ?',
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
            var valdocno = $('[name="docno"]').val();
            var valtype = $('[name="type"]').val();

            var fillData = {
                'success' : true,
                'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                'message' : '',
                'body' : {
                    docno : valdocno,
                    type : valtype,
                },
            };
            $.ajax({
                type: "POST",
                url: HOST_URL + 'trans/induction/cancelInduction' + '?var=' + e,
                dataType: 'json',
                contentType: "application/json",
                data: JSON.stringify(fillData),
                success: function (datax){
                    if (datax.status){
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
                                window.location.replace(HOST_URL + 'trans/induction')
                            }
                        })
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    Swal.fire({
                        title: 'Galat...!!!',
                        text: 'Data Tidak Bisa Di Proses..!',
                        backdrop: true,
                        allowOutsideClick: false,
                        showConfirmButton: true,
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: `Ok`,
                        icon: 'error',
                        //denyButtonText: `Don't save`,
                    });
                    bttn.prop('disabled', false);
                    //console.log("Failed To Loading Data");
                }
            });
        }
        bttn.prop('disabled', false);
        return false;

    })

}


function detailInduction(e) {
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Anda akan melihat detail data ?',
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
            window.location.replace(HOST_URL + 'trans/induction/detail/?var=' + e)
        }
        bttn.prop('disabled', false);
        return false;

    })

}
function detailInduction2(e) {
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Anda akan melihat detail data ?',
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
            window.location.replace(HOST_URL + 'trans/induction/detail2/?var=' + e)
        }
        bttn.prop('disabled', false);
        return false;

    })

}

function approveDirectionInduction(e) {
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Anda akan melihat detail data ?',
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
            window.location.replace(HOST_URL + 'trans/induction/approveDirectionInduction/?var=' + e)
        }
        bttn.prop('disabled', false);
        return false;

    })

}

function approveHCInduction(e) {
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Anda akan melihat detail data ?',
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
            window.location.replace(HOST_URL + 'trans/induction/approveHCInduction/?var=' + e)
        }
        bttn.prop('disabled', false);
        return false;

    })

}
/* FINISH INPUT CANCEL & EDIT INDUCTION */

function tableInductionVerification(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tinductionVerification');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('trans/induction/list_persetujuan_induction'),
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
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

function reload_table_verification()
{
    var table = $('#tinductionVerification');
    table.DataTable().ajax.reload(); //reload datatable ajax


}

$(document).ready(function() {
    $(".datepicker").datepicker();
    tableMutasiPromosi();
    //documentReadable();
    tableInductionDetail();
    tableInductionVerification();

    if ($('[name="type"]').val() === 'EDIT') {
        documentReadable();
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        documentReadableDetail();
    }

});