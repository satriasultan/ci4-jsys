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
        var table = $('#tmutpromot');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": HOST_URL + ('trans/mutpromot/list_his_mutpromot'),
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
    var table = $('#tmutpromot');
    table.DataTable().ajax.reload(); //reload datatable ajax


}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tmutpromot');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tmutpromot');
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
    var markup = "<div class='select2-result-repository__title'>" + repo.nik + "</div>";
    if (repo.nmlengkap) {
        markup += "<div class='select2-result-repository__description'>" + repo.nmlengkap +" <i class='fa fa-circle-o'></i> "+ repo.nmdept +"</div>";
    }
    return markup;
}

function formatRepoSelection(repo) {
    return repo.nmlengkap || repo.text;
}

var defaultInitial = '';
$(".select2_kary").select2({
    placeholder: "Ketik Nik Atau Nama Karyawan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'trans/mutpromot/list_karyawan',
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


$('#formMutasiPromosi').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        startdate: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                },
            }
        },
        nik: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        evaluation: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        doctype: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        newplant: {
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
        },
        newdept: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        newsubdept: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        newjabatan: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        newlvljabatan: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        }
    },
    excluded: [':disabled']
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

            var validator = $('#formMutasiPromosi').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valdocno = $('[name="docno"]').val();
                var valtype = $('[name="type"]').val();
                var valnik = $('[name="nik"]').val();
                var valevaluation = $('[name="evaluation"]').val();
                var valstartdate = $('[name="startdate"]').val();
                var valdoctype = $('[name="doctype"]').val();
                var valkdregu = $('[name="kdregu"]').val();
                var valdescription = $('[name="description"]').val();
                var valnewdept = $('[name="newdept"]').val();
                var valnewsubdept = $('[name="newsubdept"]').val();
                var valnewjabatan = $('[name="newjabatan"]').val();
                var valnewlvljabatan = $('[name="newlvljabatan"]').val();
                var valnewplant = $('[name="newplant"]').val();

                var fillData = {
                    'success' : true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message' : '',
                    'body' : {
                        docno : valdocno,
                        type : valtype,
                        nik : valnik,
                        evaluation : valevaluation,
                        startdate : valstartdate,
                        doctype : valdoctype,
                        kdregu : valkdregu,
                        description : valdescription,
                        newdept : valnewdept,
                        newsubdept : valnewsubdept,
                        newjabatan : valnewjabatan,
                        newlvljabatan : valnewlvljabatan,
                        newplant : valnewplant
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/mutpromot/saveEntry' + '',
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
                                    window.location.replace(HOST_URL + 'trans/mutpromot')
                                }
                            })
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        swal({
                            title: "Galat!!",
                            text: json.messages,
                            type: "error"
                        });
                        bttn.prop('disabled', false);
                        //console.log("Failed To Loading Data");
                    }
                });
            }
            bttn.prop('disabled', false);
            return false;
        } else if (result.isDenied) {
            bttn.prop('disabled', false);
            return false;
        }
    })

}

$('#nik').change(function () {

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'api/globalmodule/list_karyawan' + '?var=' + $(this).val().trim(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);

            $('[name="department"]').val(json.items[0].nmdept);
            $('[name="jabatan"]').val(json.items[0].nmjabatan);
            $('[name="atasan1"]').val(json.items[0].nmatasan1);
            $('[name="atasan2"]').val(json.items[0].nmatasan2);
            $('[name="atasan3"]').val(json.items[0].nmatasan3);
            $('[name="alamattinggal"]').val(json.items[0].alamattinggal);
            $('[name="nohp1"]').val(json.items[0].nohp1);
            $('[name="email"]').val(json.items[0].email_self);
            $('[name="plant"]').val(json.items[0].locaname);

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
        url: HOST_URL + 'trans/mutpromot/showOn' + '?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

          // var nikatasan1 = (json.dataTables.items[0].nikatasan1 === '') ? 'NULL' : json.dataTables.items[0].nikatasan1 ;
          // var nikatasan2 = (json.dataTables.items[0].nikatasan2 === '') ? 'NULL' : json.dataTables.items[0].nikatasan2 ;
          // var nikatasan3 = (json.dataTables.items[0].nikatasan3 === '') ? 'NULL' : json.dataTables.items[0].nikatasan3 ;

            $('[name="department"]').val(json.dataTables.items[0].nmdept);
            $('[name="jabatan"]').val(json.dataTables.items[0].nmjabatan);
            $('[name="atasan1"]').val(json.dataTables.items[0].nmatasan1);
            $('[name="atasan2"]').val(json.dataTables.items[0].nmatasan2);
            $('[name="atasan3"]').val(json.dataTables.items[0].nmatasan3);
            $('[name="alamattinggal"]').val(json.dataTables.items[0].alamattinggal);
            $('[name="nohp1"]').val(json.dataTables.items[0].nohp1);
            $('[name="email"]').val(json.dataTables.items[0].email_self);
            $('[name="plant"]').val(json.dataTables.items[0].locaname);
            $('[name="description"]').val(json.dataTables.items[0].description);
            $('#doctype').val(json.dataTables.items[0].doctype.trim()).trigger('change');

            var select2_kary = $('.select2_kary');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                select2_kary.append(option).trigger('change');

                // manually trigger the `select2:select` event
                select2_kary.trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_departmen' + '?var=' + json.dataTables.items[0].newdept,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {

                // create the option and append to Select2
                var option = new Option(datax.items[0].nmdept, datax.items[0].kddept, true, true);
                $('[name="newdept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newdept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_subdepartmen' + '?var=' + json.dataTables.items[0].newsubdept,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmsubdept, datax.items[0].kdsubdept, true, true);
                $('[name="newsubdept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newsubdept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_jabatan' + '?var=' + json.dataTables.items[0].newjabatan,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmjabatan, datax.items[0].kdjabatan, true, true);
                $('[name="newjabatan"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newjabatan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //level jabatan
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_lvljabatan' + '?var=' + json.dataTables.items[0].newlvljabatan,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlvljabatan, datax.items[0].kdlvl, true, true);
                $('[name="newlvljabatan"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newlvljabatan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //level jabatan
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_plant' + '?var=' + json.dataTables.items[0].newplant,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].locaname, datax.items[0].loccode, true, true);
                $('[name="newplant"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newplant"]').trigger({
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
        url: HOST_URL + 'trans/mutpromot/showOn' + '?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            // var nikatasan1 = (json.dataTables.items[0].nikatasan1 === '') ? 'NULL' : json.dataTables.items[0].nikatasan1 ;
            // var nikatasan2 = (json.dataTables.items[0].nikatasan2 === '') ? 'NULL' : json.dataTables.items[0].nikatasan2 ;
            // var nikatasan3 = (json.dataTables.items[0].nikatasan3 === '') ? 'NULL' : json.dataTables.items[0].nikatasan3 ;

            $('[name="department"]').val(json.dataTables.items[0].nmdept);
            $('[name="jabatan"]').val(json.dataTables.items[0].nmjabatan);
            $('[name="atasan1"]').val(json.dataTables.items[0].nmatasan1);
            $('[name="atasan2"]').val(json.dataTables.items[0].nmatasan2);
            $('[name="atasan3"]').val(json.dataTables.items[0].nmatasan3);
            $('[name="alamattinggal"]').val(json.dataTables.items[0].alamattinggal);
            $('[name="nohp1"]').val(json.dataTables.items[0].nohp1);
            $('[name="email"]').val(json.dataTables.items[0].email_self);
            $('[name="plant"]').val(json.dataTables.items[0].locaname);
            $('[name="description"]').val(json.dataTables.items[0].description);
            $('#doctype').val(json.dataTables.items[0].doctype.trim()).trigger('change');

            var select2_kary = $('.select2_kary');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                select2_kary.append(option).trigger('change');

                // manually trigger the `select2:select` event
                select2_kary.trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_departmen' + '?var=' + json.dataTables.items[0].newdept,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {

                // create the option and append to Select2
                var option = new Option(datax.items[0].nmdept, datax.items[0].kddept, true, true);
                $('[name="newdept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newdept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_subdepartmen' + '?var=' + json.dataTables.items[0].newsubdept,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmsubdept, datax.items[0].kdsubdept, true, true);
                $('[name="newsubdept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newsubdept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_jabatan' + '?var=' + json.dataTables.items[0].newjabatan,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmjabatan, datax.items[0].kdjabatan, true, true);
                $('[name="newjabatan"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newjabatan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //level jabatan
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_lvljabatan' + '?var=' + json.dataTables.items[0].newlvljabatan,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlvljabatan, datax.items[0].kdlvl, true, true);
                $('[name="newlvljabatan"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newlvljabatan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //level jabatan
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_plant' + '?var=' + json.dataTables.items[0].newplant,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].locaname, datax.items[0].loccode, true, true);
                $('[name="newplant"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="newplant"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $('[name="newplant"]').prop('disabled', true);
            $('[name="newlvljabatan"]').prop('disabled', true);
            $('[name="newjabatan"]').prop('disabled', true);
            $('[name="newsubdept"]').prop('disabled', true);
            $('[name="newdept"]').prop('disabled', true);
            $('[name="evaluation"]').prop('disabled', true);
            $('[name="doctype"]').prop('disabled', true);
            $('[name="description"]').prop('disabled', true);
            $('[name="nik"]').prop('disabled', true);
            $('[name="startdate"]').prop('disabled', true);
            $('#btnSave').hide();
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
                "url": HOST_URL + ('trans/mutpromot/list_persetujuanmutasi'),
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

// FILTER JABATAN

function beforePrint(id)
{
    var bttn = $('#btnsave-cetak');
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#form-cetak').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#form-cetak')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    bttn.removeClass("btn-primary").addClass("btn-warning").text('Print');
    //$('[name="dob"]').datepicker('update',data.dob);
    $('#docnoprint').val(id);
    $('#cetak').modal('show'); // show bootstrap modal when complete loaded
    $('.modal-title').text('Print Data Mutasi'); // Set title to Bootstrap modal title
}

//var defaultInitialNewSubDept = $("#newdept").val();
$("#cc1").select2({
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
});
$("#cc2").select2({
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
});
$("#cc3").select2({
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
});
$("#cc4").select2({
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
});

$('#form-cetak').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        nosk: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                },
            }
        },
        cc1: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                },
            }
        },
        cc2: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                },
            }
        },
    },
    excluded: [':disabled']
});
$('#btnsave-cetak').click(function(){ //button filter event click

    var bttn = $('#btnsave-cetak');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data Akan dicetak, Pstikan inputan benar adanya...?',
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

            var validator = $('#form-cetak').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valdocnoprint = $('[name="docnoprint"]').val();
                var valnosk = $('[name="nosk"]').val();
                var valcc1 = $('[name="cc1"]').val();
                var valcc2 = $('[name="cc2"]').val();
                var valcc3 = $('[name="cc3"]').val();
                var valcc4 = $('[name="cc4"]').val();
                var fillData = {
                    'success' : true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message' : '',
                    'body' : {
                        docno : valdocnoprint,
                        nosk : valnosk,
                        cc1 : valcc1,
                        cc2 : valcc2,
                        cc3 : valcc3,
                        cc4 : valcc4,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/mutpromot/beforeCetak' + '',
                    dataType: 'json',
                    contentType: "application/json",
                    data: JSON.stringify(fillData),
                    success: function (datax){
                        var encdoc = datax.enc_docno;
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
                                $('#cetak').modal('hide');
                                if (result.isConfirmed) {
                                    window.open(HOST_URL + 'trans/mutpromot/cetak' + '/' + '?enc_docno=' + encdoc, '_blank');
                                }
                            })
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        swal({
                            title: "Galat!!",
                            text: json.messages,
                            type: "error"
                        });
                        bttn.prop('disabled', false);
                        //console.log("Failed To Loading Data");
                        $('#cetak').modal('hide');
                    }
                });
            }
            bttn.prop('disabled', false);
            return false;
        } else if (result.isDenied) {
            bttn.prop('disabled', false);
            $('#cetak').modal('hide');
            return false;
        }
    })

});

$(document).ready(function() {
    $('#doctype').select2();
    tableMutasiPromosi();
    tablePersetujuanMutasi();
    console.log($('[name="newdept"]').val() + "<NEWDEPT");
    console.log($('[name="type"]').val() + "<TYPE");
    if ($('[name="type"]').val() === 'EDIT') {
        documentReadable();
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        documentReadableDetail();
    }

});