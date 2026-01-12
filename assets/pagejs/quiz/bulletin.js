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
function tableTbulletinmst(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tTbulletinmst');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('quiz/bulletin/list_bulletinmst'),
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
    var table = $('#tTbulletinmst');
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

function add_bulletin()
{
    save_method = 'add';
    var validator = $('#form').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#form')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('INPUT MASTER BULLETIN'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="id"]').val();
    $('[name="bulletinid"]').prop("required", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}
function update_bulletin(id)
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
        url: HOST_URL + 'quiz/bulletin/showing_data' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('UPDATE');
            $('[name="id"]').val(data.id);
            $('[name="bulletinid"]').val(data.bulletinid).prop("readonly", true);
            $('[name="bulletinname"]').val(data.bulletinname).prop("readonly", false);
            $('[name="description"]').val(data.description.trim());
            $('[name="status"]').val(data.status.trim());
            $('#btnSave').removeClass("btn-primary").addClass("btn-primary").text('Update');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Update Session Bulletin'); // Set title to Bootstrap modal title

            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function delete_bulletin(id)
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
        url: HOST_URL + 'quiz/bulletin/showing_data' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('DELETE');
            $('[name="id"]').val(data.id);
            $('[name="bulletinid"]').val(data.bulletinid).prop("readonly", true);
            $('[name="bulletinname"]').val(data.bulletinname).prop("readonly", true);
            $('[name="description"]').val(data.description).prop("readonly", true);;
            $('[name="status"]').val(data.status).prop("readonly", true);
            $('.chold').prop("disabled", true);
            $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Role'); // Set title to Bootstrap modal title

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
        bulletinid: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        bulletinname: {
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
                var valbulletinid = $('[name="bulletinid"]').val();
                var valbulletinname = $('[name="bulletinname"]').val();
                var valdescription = $('[name="description"]').val();
                var valstatus = $('[name="status"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        id: valID,
                        bulletinid: valbulletinid,
                        bulletinname: valbulletinname,
                        description: valdescription,
                        status: valstatus,
                        type: valtype
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'quiz/bulletin/saveEntry' + '',
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

function tableUploadGambar(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tUploadGambar');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('quiz/bulletin/list_upload_gambar'),
                "type": "POST",
                "data": function(data) {
                    data.bulletinid = $('#varbulletinid').val();
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
    var table = $('#tUploadGambar');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}
// ADD BULLETIN

function add_image()
{
    save_method = 'add';
    var validator = $('#formUpload').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formUpload')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('INPUT DETAIL GAMBAR BULLETIN'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="id"]').val();
    $('[name="bulletinid"]').prop("readonly", true);
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

$('#formUpload').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        bulletinid: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        urut: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        description: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        file_name: {
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
            if (save_method == 'add') {
                var validator = $('#formUpload').data('bootstrapValidator');
                validator.validate();
                if (validator.isValid()) {
                    // ajax adding data to database
                    var form = $('#formUpload');
                    var formdata = false;
                    if (window.FormData){
                        formdata = new FormData(form[0]);
                    }
                    $.ajax({
                        url: HOST_URL + 'quiz/bulletin/saveUpload',
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
                                        reload_table_upload();
                                        $('#modal_form').modal('hide');
                                        $('#btnSave').text('save'); //change button text
                                        $('#btnSave').attr('disabled', false); //set button enable
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
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: json.messages,
                                    backdrop: true,
                                    allowOutsideClick: false,
                                })
                            }
                            $('#btnSave').text('save'); //change button text
                            $('#btnSave').attr('disabled', false); //set button enable


                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            // alert('Gagal Menyimpan / Ubah data / data sudah ada');
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Unable To Response Data',
                                backdrop: true,
                                allowOutsideClick: false,
                            })
                            reload_table_upload();
                            $('#modal_form').modal('hide');
                            $('#btnSave').text('save'); //change button text
                            $('#btnSave').attr('disabled', false); //set button enable

                        }
                    });
                    //alert("FInish Input");
                }
           } else if (save_method == 'delete') {

                var valtype = $('[name="type"]').val();
                var valID = $('[name="id"]').val();
                var valbulletinid = $('[name="bulletinid"]').val();
                var valurut = $('[name="urut"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        id: valID,
                        bulletinid: valbulletinid,
                        urut: valurut,
                        type: valtype
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'quiz/bulletin/delUpload' + '',
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
                            reload_table_upload();
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
                        reload_table_upload();
                        $('#modal_form').modal('hide');
                        bttn.prop('disabled', false);
                    }
                });
            }



        } else if (result.isDenied) {
            return false;
        }
    })

}

$(document).ready(function() {

    tableTbulletinmst();
    tableUploadGambar();
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});