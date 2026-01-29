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
//"use strict";
function tableMarea(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tmlocation');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": HOST_URL + 'master/location/list_marea',
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

function reload_table()
{
    var table = $('#tmlocation');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tmlocation');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tmlocation');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
/* FOR INPUT FUNCTION */

function add_mlocation()
{
    save_method = 'add';
    var validator = $('#form').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#form')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Input Master Area'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="id"]').val();
    $('[name="idlocation"]').prop('disabled', false);
    $('#idlocation').val(null).trigger('change');    $('[name="idarea"]').prop("required", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}
function update_marea(id)
{
    save_method = 'update';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#form').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'master/location/showing_data_area' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('UPDATE');
            $('[name="id"]').val(data.id);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + data.idlocation.trim(),
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
            $('[name="idarea"]').val(data.idarea).prop("readonly", true);
            $('[name="nmarea"]').val(data.nmarea.trim()).prop("readonly", false);
            $('[name="idlocation"]').prop('disabled', true);
            $('[name="chold"]').val(data.chold.trim());
            $('.chold').prop("disabled", false);
            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Update');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Update Master Area'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function delete_marea(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#form').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'master/location/showing_data_area' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('DELETE');
            $('[name="id"]').val(data.id);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + data.idlocation.trim(),
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
            $('[name="idarea"]').val(data.idarea).prop("readonly", true);
            $('[name="nmarea"]').val(data.nmarea).prop("readonly", true);
            $('[name="idlocation"]').prop('disabled', true);
            $('[name="chold"]').val(data.chold.trim());
            $('.chold').prop("disabled", true);
            $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Location'); // Set title to Bootstrap modal title

            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
$('#form').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        idarea: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmarea: {
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

function save()
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

            var validator = $('#form').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var valID = $('[name="id"]').val();
                var validlocation= $('[name="idlocation"]').val();
                var validarea= $('[name="idarea"]').val();
                var valnmarea = $('[name="nmarea"]').val();
                var valchold = $('[name="chold"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        id: valID,
                        idlocation: validlocation,
                        idarea: validarea,
                        nmarea: valnmarea,
                        chold: valchold,
                        type: valtype
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'master/location/saveEntryArea' + '',
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
    });
}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH AREA ++++++++++++++++++++++++++++++++++++++++//

var defaultInitialLocation = '';
$("#idlocation").select2({
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
    //$("#newsubdept option[value]").remove();
    //var newOptions = []; // the result of your JSON request
    //$("#id_subdept").val(null).trigger('change');
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





$(document).ready(function() {

    tableMarea();
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});