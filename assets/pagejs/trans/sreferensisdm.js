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
"use strict";
function tableReferensiSdm(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#treferensi');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('trans/referensisdm/list_his_referensisdm'),
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
    var table = $('#treferensi');
    table.DataTable().ajax.reload(); //reload datatable ajax


}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#treferensi');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#treferensi');
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
        url: HOST_URL + 'api/globalmodule/list_karyawan',
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


$('#formReferensiSdm').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
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
        doctype: {
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

            var validator = $('#formReferensiSdm').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valnik = $('[name="nik"]').val();
                var valstartdate = $('[name="startdate"]').val();
                var valdoctype = $('[name="doctype"]').val();
                var valdescription = $('[name="description"]').val();

                var fillData = {
                    'success' : true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message' : '',
                    'body' : {
                        nik : valnik,
                        startdate : valstartdate,
                        doctype : valdoctype,
                        description : valdescription,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/referensisdm/saveEntry' + '',
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
                                    window.location.replace(HOST_URL + 'trans/referensisdm')
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
    //console.log("INI -> NIKNYA " + $(this).val());
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/referensisdm/showTmp' + '?nik=' + $(this).val().trim(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            $('[name="startdate"]').val(json.dataTables.items[0].startdatex);
            $('[name="department"]').val(json.dataTables.items[0].nmdept);
            $('[name="jabatan"]').val(json.dataTables.items[0].nmjabatan);
            $('[name="atasan1"]').val(json.dataTables.items[0].nmatasan1);
            $('[name="atasan2"]').val(json.dataTables.items[0].nmatasan2);
            $('[name="alamattinggal"]').val(json.dataTables.items[0].alamattinggal);
            $('[name="nohp1"]').val(json.dataTables.items[0].nohp1);
            $('[name="email"]').val(json.dataTables.items[0].email);
            $('[name="description"]').val(json.dataTables.items[0].description);
            //return JSON.stringify(json); // return JSON string
            //$('[name="evaluation"]').val(json.dataTables.items[0].evaluation.trim()).trigger('change');
            $('[name="doctype"]').val(json.dataTables.items[0].doctype.trim()).trigger('change');


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            //console.log("Failed To Loading Data");
        }
    });

});
$(document).ready(function() {
    tableReferensiSdm();
    //* input form */
    //onReadyInput();
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});