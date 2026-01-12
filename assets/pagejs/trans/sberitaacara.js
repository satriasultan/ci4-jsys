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
function tableBeritaAcara(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tberitaacara');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('trans/sberitaacara/list_sberita_acara'),
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
    var table = $('#tberitaacara');
    table.DataTable().ajax.reload(); //reload datatable ajax


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

    $('#nik').change(function () {
        //console.log("INI -> NIKNYA " + $(this).val());
        $.ajax({
            type: 'GET',
            url: HOST_URL + 'trans/sberitaacara/showTmp' + '?nik=' + $(this).val().trim(),
            dataType: 'json',
            dataFilter: function(data) {
                var json = jQuery.parseJSON(data);
                json.status = json.dataTables.status;
                json.total_count = json.dataTables.total_count;
                json.items = json.dataTables.items;
                json.incomplete_results = json.dataTables.incomplete_results;

                $('[name="department"]').val(json.dataTables.items[0].nmdept);
                $('[name="jabatan"]').val(json.dataTables.items[0].nmjabatan);
                $('[name="atasan1"]').val(json.dataTables.items[0].nmatasan1);
                $('[name="atasan2"]').val(json.dataTables.items[0].nmatasan2);
                $('[name="alamattinggal"]').val(json.dataTables.items[0].alamattinggal);
                $('[name="nohp1"]').val(json.dataTables.items[0].nohp1);
                $('[name="email"]').val(json.dataTables.items[0].email);
                //return JSON.stringify(json); // return JSON string
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log("Failed To Loading Data");
            }
        });

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

function add_sp_karyawan()
{
    save_method = 'add';
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Input SP Karyawan'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
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



var defaultInitialSaksi1 = '';
$("#saksi1").select2({
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
                _paramglobal_: defaultInitialSaksi1,
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

var defaultInitialSaksi2 = '';
$("#saksi2").select2({
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
                _paramglobal_: defaultInitialSaksi1,
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



$('#formBeritaAcara').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        docdate: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        laporan: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        lokasi: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        solusi: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        uraian: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        saksi1: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        saksi2: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        nik: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        }
    },
    excluded: [':disabled']
});
$('.zz').on('change', function(e) {

    var valnik = $('[name="nik"]').val();
    var valdocdate = $('[name="docdate"]').val();
    var vallaporan = $('[name="laporan"]').val();
    var vallokasi = $('[name="lokasi"]').val();
    var valuraian = $('[name="uraian"]').val();
    var valsolusi = $('[name="solusi"]').val();
    var valsaksi1 = $('[name="saksi1"]').val();
    var valsaksi2 = $('[name="saksi2"]').val();

    var fillData = {
        'success' : true,
        'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiojO',
        'message' : '',
        'body' : {
            nik : valnik,
            docdate : valdocdate,
            laporan : vallaporan,
            lokasi : vallokasi,
            uraian : valuraian,
            solusi : valsolusi,
            saksi1 : valsaksi1,
            saksi2 : valsaksi2,
        },
    };
    $.ajax({
        type: "POST",
        url: HOST_URL + 'trans/sberitaacara/fill_input' + '',
        dataType: 'json',
        contentType: "application/json",
        data: JSON.stringify(fillData),
        success: function (datax){
            console.log(datax.status);
            console.log(datax.messages);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
        }
    });
});


function finishInput() {
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);
    if (!confirm('Apakah Anda Yakin Akan Menyimpan Dokumen Ini....?')) {
        bttn.prop('disabled', false);
        return false;
    } else {
        var validator = $('#formBeritaAcara').data('bootstrapValidator');
        validator.validate();
        if (validator.isValid()) {
            var valnik = $('[name="nik"]').val();
            var valdocdate = $('[name="docdate"]').val();
            var vallaporan = $('[name="laporan"]').val();
            var vallokasi = $('[name="lokasi"]').val();
            var valuraian = $('[name="uraian"]').val();
            var valsolusi = $('[name="solusi"]').val();
            var valsaksi1 = $('[name="saksi1"]').val();
            var valsaksi2 = $('[name="saksi2"]').val();

            var fillData = {
                'success' : true,
                'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                'message' : '',
                'body' : {
                    nik : valnik,
                    docdate : valdocdate,
                    laporan : vallaporan,
                    lokasi : vallokasi,
                    uraian : valuraian,
                    solusi : valsolusi,
                    saksi1 : valsaksi1,
                    saksi2 : valsaksi2,
                },
            };
            $.ajax({
                type: "POST",
                url: HOST_URL + 'trans/sberitaacara/saveEntry' + '',
                dataType: 'json',
                contentType: "application/json",
                data: JSON.stringify(fillData),
                success: function (datax){
                    if (datax.status){
                        alert(datax.messages);
                        bttn.prop('disabled', false);
                        location.replace(HOST_URL + 'trans/sberitaacara' + '',);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    bttn.prop('disabled', false);
                    console.log("Failed To Loading Data");
                }
            });
        }
    }

}

$(document).ready(function() {
    tableBeritaAcara();
    //* input form */
    onReadyInput();
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});