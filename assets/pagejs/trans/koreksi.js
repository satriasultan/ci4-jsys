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

$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tKoreksiCuti');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tKoreksiCuti');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
/* FOR INPUT FUNCTION */

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
        markup = "<div class='select2-result-repository__description'>" + repo.kdtrx +" <i class='fa fa-circle-o'></i> "+ repo.uraian +"</div>";
    return markup;
}

function formatRepoSelection(repo) {
    return repo.uraian || repo.text;
}

var doctypeinit = " and jenistrx='ITEMTRANS'";
$("#doctypeSKIPP").select2({
    placeholder: "Pilih & Ketik Type Transaksi",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'ga/itemtrans/option_trxtype_doctype',
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
                _paramglobal_: doctypeinit,
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



function formatPlant(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.loccode +"   <i class='fa fa-circle-o'></i>   "+ repo.locaname +"</div>";
    return markup;
}

function formatPlantSelection(repo) {
    return repo.locaname || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#loccode_destination").select2({
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





///////////// NIK ATASAN / ATASAN KARYAWAN
function formatRepoNik(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.nik +" <i class='fa fa-circle-o'></i> "+ " " + repo.nmlengkap + "</div>";

    return markup;
}

function formatRepoSelectionNik(repo) {
    return repo.nmlengkap || repo.text;
}
var defaultInitialnikatasan = '';
$("#nik").select2({
    placeholder: "Ketik Dan Pilih Nik",
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
                _paramglobal_: defaultInitialnikatasan,
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
    templateResult: formatRepoNik, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelectionNik // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
//console.log("change" + $(this).val());
                var fillData = {
                    'success' : true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message' : '',
                    'body' : {
                        nik : $(this).val(),
                        type : 'ADDING_ITEM',
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/koreksi/saveKoreksiCutiTmpDtl' + '',
                    dataType: 'json',
                    contentType: "application/json",
                    data: JSON.stringify(fillData),
                    success: function (datax){
                        reloadTmpItemtransDtl();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        Swal.fire({
                            title: 'Silahkan Ulangi...!!!',
                            text: 'Error Ada Kesalahan Data!!, Silahkan Ulangi',
                            backdrop: true,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: `Ok`,
                            icon: 'error',
                            //denyButtonText: `Don't save`,
                        });
                    }
                });

});

function update_koreksi_cuti(e){
    Swal.fire({
        title: 'Oiiiits..!!!',
        text: 'Data ini akan diubah, Anda yakin..?, Dokumen: ' + e,
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
            window.location.replace(HOST_URL + 'trans/koreksi/updateKoreksiCuti' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    });
}

function detail_koreksi_cuti(e){
    Swal.fire({
        title: 'Oiiiits..!!!',
        text: 'Data ini akan diubah, Anda yakin..?, Dokumen: ' + e,
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
            window.location.replace(HOST_URL + 'trans/koreksi/detailKoreksiCuti' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    });
}

function cancel_koreksi_cuti(e){
    Swal.fire({
        title: 'Oiiiits..!!!',
        text: 'Data ini akan diubah, Anda yakin..?, Dokumen: ' + e,
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
            window.location.replace(HOST_URL + 'trans/koreksi/cancelKoreksiCuti' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    });
}

function documentReadableInput(){
    $("#loadMe").modal({
        backdrop: "static", //remove ability to close modal with click
        keyboard: false, //remove option to close with keyboard
        show: true //Display loader!
    });

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/koreksi/showOn_Koreksi_Cuti',
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

          // var nikatasan1 = (json.dataTables.items[0].nikatasan1 === '') ? 'NULL' : json.dataTables.items[0].nikatasan1 ;
            $('#doctype').val(json.dataTables.items[0].doctype.trim()).trigger('change');
            $('[name="qty"]').val(json.dataTables.items[0].qty);
            $('[name="docdate"]').val(json.dataTables.items[0].docdate1);
            $('[name="docref"]').val(json.dataTables.items[0].docref);
            $('[name="description"]').val(json.dataTables.items[0].description);


            if ($('[name="status"]').val()==='I') {
                $('[name="qty"]').prop('disabled', true);
                $('[name="doctype"]').prop('disabled', true);
                $('[name="docdate"]').prop('disabled', true);
                $('[name="docref"]').prop('disabled', true);
                $('[name="description"]').prop('disabled', true);
            };

            //console.log($('[name="status"]').val() + " YEY");

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
        url: HOST_URL + 'trans/koreksi/showOn_Koreksi_Cuti_Trx' + '/?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            // var nikatasan1 = (json.dataTables.items[0].nikatasan1 === '') ? 'NULL' : json.dataTables.items[0].nikatasan1 ;
            $('#doctype').val(json.dataTables.items[0].doctype.trim()).trigger('change');
            $('[name="qty"]').val(json.dataTables.items[0].qty);
            $('[name="docdate"]').val(json.dataTables.items[0].docdate1);
            $('[name="docref"]').val(json.dataTables.items[0].docref);
            $('[name="description"]').val(json.dataTables.items[0].description);



            $('[name="qty"]').prop('disabled', true);
            $('[name="doctype"]').prop('disabled', true);
            $('[name="docdate"]').prop('disabled', true);
            $('[name="docref"]').prop('disabled', true);
            $('[name="description"]').prop('disabled', true);


            //console.log($('[name="status"]').val() + " YEY");

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




function tableTmpItemtransDtl(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#t_nikdtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": HOST_URL + ('trans/koreksi/list_tmp_koreksi_cuti_dtl'),
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
function reloadTmpItemtransDtl()
{
    var table = $('#t_nikdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax

}


function table_koreksi_cuti_mst(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tKoreksiCuti');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": HOST_URL + ('trans/koreksi/list_koreksi_cuti'),
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
function tableTrxItemtransDtl(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#t_nikdtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": HOST_URL + ('trans/koreksi/list_trx_koreksi_cuti_dtl'),
                "type": "POST",
                "data": function(data) {
                    data.docno = $('[name="docno"]').val();
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
function reloadTrxItemtransDtl()
{
    var table = $('#t_nikdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax

}
function reload_table()
{
    var table = $('#tKoreksiCuti');
    table.DataTable().ajax.reload(); //reload datatable ajax

}

$('#form').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        doctype: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        docdate: {
            validators: {
                notEmpty: {
                    message: 'hayoo... kolom ini tidak boleh kosong ya!!'
                }
            }
        },
        qty: {
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

$('#formItemTransDtl').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        qty: {
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


function update_itemtrans_dtl(id)
{
    save_method = 'update';
    console.log(save_method);
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formItemTransDtl').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formItemTransDtl')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modalItemTransDtl').modal('show'); // show bootstrap modal
    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'trans/koreksi/showing_koreksi_cuti_dtl' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('[name="type"]').val('UPDATE_ITEM');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="docno"]').val(data.docno).prop("readonly", true);
            $('[name="nik"]').val(data.nik).prop("readonly", true);
            $('[name="nmlengkap"]').val(data.nmlengkap).prop("readonly", true);
            $('[name="doctypedetail"]').val(data.doctype1).prop("readonly", true);
            $('[name="qtydetail"]').val(data.qty).prop("readonly", true);
            $('[name="nmdept"]').val(data.nmdept).prop("readonly", true);
            $('[name="nmjabatan"]').val(data.nmjabatan).prop("readonly", true);
            $('[name="descriptiondetail"]').val(data.description).prop("readonly", true);
            $('#btnSaveDtl').removeClass("btn-danger").addClass("btn-primary").text('Update');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Ubah data karyawan'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function delete_itemtrans_dtl(id)
{

    save_method = 'delete';
    console.log(save_method);
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formItemTransDtl').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formItemTransDtl')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modalItemTransDtl').modal('show'); // show bootstrap modal
    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'trans/koreksi/showing_koreksi_cuti_dtl' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('[name="type"]').val('DELETE_ITEM');
            $('[name="id"]').val(data.id).prop("readonly", true);
            $('[name="docno"]').val(data.docno).prop("readonly", true);
            $('[name="nik"]').val(data.nik).prop("readonly", true);
            $('[name="nmlengkap"]').val(data.nmlengkap).prop("readonly", true);
            $('[name="doctypedetail"]').val(data.doctype1).prop("readonly", true);
            $('[name="qtydetail"]').val(data.qty).prop("readonly", true);
            $('[name="nmdept"]').val(data.nmdept).prop("readonly", true);
            $('[name="nmjabatan"]').val(data.nmjabatan).prop("readonly", true);
            $('[name="descriptiondetail"]').val(data.description).prop("readonly", true);
            $('#btnSaveDtl').removeClass("btn-primary").addClass("btn-danger").text('Hapus');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Hapus data Detail'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function save_itemtrans_dtl()
{
    var bttn = $('#btnSaveDtl');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Oiiiits..!!!',
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

            var validator = $('#formItemTransDtl').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var valid = $('[name="id"]').val();
                var valdocno = $('[name="docno"]').val();
                var valdoctype = $('[name="doctype"]').val();
                var valqty = $('[name="qtydetail"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        docno: valdocno,
                        type: valtype,
                        doctype: valdoctype,
                        id: valid,
                        qty: valqty,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/koreksi/saveKoreksiCutiTmpDtl' + '',
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
                                    reloadTmpItemtransDtl();
                                    $('#modalItemTransDtl').modal('hide');
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
                            reloadTmpItemtransDtl();
                            $('#modalItemTransDtl').modal('hide');
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
                        reloadTmpItemtransDtl();
                        $('#modalItemTransDtl').modal('hide');
                        bttn.prop('disabled', false);
                    }
                });
            }
        }
        //$('#modal_form').modal('hide');
        bttn.prop('disabled', false);

    });
}

function finishInput()
{
     Swal.fire({
            title: 'Oiiiits..!!!',
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
                 var fillData = {
                        'success': true,
                        'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                        'message': '',
                        'body': {
                            docno: 'CONTOL',
                        },
                    };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/koreksi/finishInputKoreksiCuti' + '',
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
                                    window.location.replace(HOST_URL + 'trans/koreksi/koreksicuti')
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
//                            reloadTmpItemtransDtl();
//                            $('#modalItemTransDtl').modal('hide');
//                            bttn.prop('disabled', false);
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
//                        reloadTmpItemtransDtl();
//                        $('#modalItemTransDtl').modal('hide');
//                        bttn.prop('disabled', false);
                    }
                });
                }
        });

}

function save()
{
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);

    Swal.fire({
        title: 'Oiiiits..!!!',
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
            var validator = $('#form').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var valdocno = $('[name="docno"]').val();
                var valstatus = $('[name="status"]').val();
                var valdoctype = $('[name="doctype"]').val();
                var valqty = $('[name="qty"]').val();
                var valdocdate = $('[name="docdate"]').val();
                var valdocref = $('[name="docref"]').val();
                var valdescription = $('[name="description"]').val();
                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        type: valtype,
                        docno: valdocno,
                        status: valstatus,
                        qty: valqty,
                        doctype: valdoctype,
                        docdate: valdocdate,
                        docref: valdocref,
                        description: valdescription,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/koreksi/saveKoreksiCutiTmpMst' + '',
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
                                    window.location.replace(HOST_URL + 'trans/koreksi/input_koreksicuti')
                                    reload_table();
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




$(document).ready(function() {
    $('#doctype').select2();
    table_koreksi_cuti_mst();
    //datepicker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd-mm-yyyy",
        todayHighlight: true,
        orientation: "top auto",
        todayBtn: true,
    });
    $('#inouttype').select2();


    //console.log($('[name="type"]').val() + 'TEST CONSOLE');
    if ($('[name="type"]').val() === 'INPUT') {
        documentReadableInput();
        tableTmpItemtransDtl();
        reloadTmpItemtransDtl
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        documentReadableDetail();
        tableTrxItemtransDtl();
        reloadTrxItemtransDtl();
        $('#btnSave').hide();

    }
    /*
    if ($('[name="type"]').val() === 'EDIT') {
        documentReadableEdit();
        tableTmpItemtransDtl();
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        documentReadableDetail();
    }
    */


});