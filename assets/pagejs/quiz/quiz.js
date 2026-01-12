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
function tableQuizmst(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tQuizmst');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('quiz/list_quizmst'),
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
    var table = $('#tQuizmst');
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
/* FOR INPUT FUNCTION */

function add_quiz_master()
{
    save_method = 'add';
    var validator = $('#form').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#form')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('INPUT MASTER QUIZ & OPEN SESI'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="id"]').val();
    $('[name="quizid"]').prop("required", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}
function update_quiz_master(id)
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
        url: HOST_URL + 'quiz/showing_data' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('UPDATE');
            $('[name="id"]').val(data.id);
            $('[name="quizid"]').val(data.quizid).prop("readonly", true);
            $('[name="quizname"]').val(data.quizname).prop("readonly", false);
            $('[name="description"]').val(data.description.trim());
            $('[name="status"]').val(data.status.trim());
            $('#btnSave').removeClass("btn-primary").addClass("btn-primary").text('Update');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Update Quiz Master'); // Set title to Bootstrap modal title

            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function delete_quiz_master(id)
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
        url: HOST_URL + 'quiz/showing_data' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('DELETE');
            $('[name="id"]').val(data.id);
            $('[name="quizid"]').val(data.quizid).prop("readonly", true);
            $('[name="quizname"]').val(data.quizname).prop("readonly", true);
            $('[name="description"]').val(data.description).prop("readonly", true);;
            $('[name="status"]').val(data.status).prop("readonly", true);
            $('.chold').prop("disabled", true);
            $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Quiz Master'); // Set title to Bootstrap modal title

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
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        quizid: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        quizname: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        description: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        status: {
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
                var valquizid = $('[name="quizid"]').val();
                var valquizname = $('[name="quizname"]').val();
                var valdescription = $('[name="description"]').val();
                var valstatus = $('[name="status"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        id: valID,
                        quizid: valquizid,
                        quizname: valquizname,
                        description: valdescription,
                        status: valstatus,
                        type: valtype
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'quiz/saveEntry' + '',
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
        //$('#modal_form').modal('hide');
        bttn.prop('disabled', false);

    });
}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH DETAIL UPLOAD ++++++++++++++++++++++++++++++++++++++++//

function tableQuizDtl(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tQuizdtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('quiz/list_questions'),
                "type": "POST",
                "data": function(data) {
                    data.quizid = $('#varquizid').val();
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

function reload_table_upload()
{
    var table = $('#tQuizdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}
// ADD BULLETIN

function add_question()
{
    save_method = 'add';
    var validator = $('#formQuestion').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formQuestion')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('INPUT DETAIL QUIZ'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="id"]').val();
    $('[name="quizid"]').prop("readonly", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}
function delete_image(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formUpload').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formUpload')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'quiz/bulletin/showing_data_image' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('DELETE');
            $('[name="id"]').val(data.id);
            $('[name="bulletinid"]').val(data.bulletinid).prop("readonly", true);
            $('[name="urut"]').val(data.urut).prop("readonly", true);
            $('[name="description"]').val(data.description).prop("readonly", true);
            $('[name="file_name"]').prop("disabled", true);

            $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Image'); // Set title to Bootstrap modal title

            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

$('#formQuestion').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        quizid: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        questionid: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        question: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ans1: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ansval1: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ans2: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ansval2: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ans3: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ansval3: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ans4: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ansval4: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ans5: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        ansval5: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        }
    },
    excluded: [':disabled']
});


function savingImage() {
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data akan diproses, Apakah anda yakin...?',
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
                var validator = $('#formQuestion').data('bootstrapValidator');
                validator.validate();
                if (validator.isValid()) {
                    var valtype = $('[name="type"]').val();
                    var valID = $('[name="id"]').val();
                    var valquizid = $('[name="quizid"]').val();
                    var valquestionid = $('[name="questionid"]').val();
                    var valquestion = $('[name="question"]').val();
                    var valans1 = $('[name="ans1"]').val();
                    var valansval1 = $('[name="ansval1"]').val();
                    var valans2 = $('[name="ans2"]').val();
                    var valansval2 = $('[name="ansval2"]').val();
                    var valans3 = $('[name="ans3"]').val();
                    var valansval3 = $('[name="ansval3"]').val();
                    var valans4 = $('[name="ans4"]').val();
                    var valansval4 = $('[name="ansval4"]').val();
                    var valans5 = $('[name="ans5"]').val();
                    var valansval5 = $('[name="ansval5"]').val();
                    var fillData = {
                        'success': true,
                        'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                        'message': '',
                        'body': {
                            id: valID,
                            type: valtype,
                            quizid: valquizid,
                            questionid: valquestionid,
                            question: valquestion,
                            ans1: valans1,
                            ansval1: valansval1,
                            ans2: valans2,
                            ansval2: valansval2,
                            ans3: valans3,
                            ansval3: valansval3,
                            ans4: valans4,
                            ansval4: valansval4,
                            ans5: valans5,
                            ansval5: valansval5
                        },
                    };
                    $.ajax({
                        type: "POST",
                        url: HOST_URL + 'quiz/saveQuestion' + '',
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
                                        reload_table_upload();
                                        $('#modal_form').modal('hide');
                                        $('#btnSave').prop('disabled', false);
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
                                reload_table_upload();
                                $('#modal_form').modal('hide');
                                $('#btnSave').prop('disabled', false);
                                //bttn.prop('disabled', false);
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
                            reload_table_upload();
                            $('#modal_form').modal('hide');
                            $('#btnSave').prop('disabled', false);
                        }
                    });
                }
            //$('#modal_form').modal('hide');
                $('#btnSave').prop('disabled', false);

        } else if (result.isDenied) {
            return false;
        }
    })

}

$(document).ready(function() {

    tableQuizmst();
    tableQuizDtl();
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});