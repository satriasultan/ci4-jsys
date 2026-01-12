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
function tableCapitalAuthorization(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#capital_authorization');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength" : 5,
            "language":  languageDatatable(),
            "ajax": {
                "url": HOST_URL + 'capital/masters/list_capital_authorization',
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
    var table = $('#capital_authorization');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#capital_authorization');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#capital_authorization');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
/* FOR INPUT FUNCTION */

function add_authorization()
{
    save_method = 'add';
    var validator = $('#formAuthorization').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formAuthorization')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Input User Authorization Level'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="id"]').val();
    $('[name="idgroupMgroup"]').prop("required", true);
    $('[name="nmgroupMgroup"]').prop("required", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}
function update_mgroup(id)
{
    save_method = 'update';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formAuthorization').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formAuthorization')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'capital/masters/showing_data_mgroup' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="typeMgroup"]').val('UPDATE');
            $('[name="idMgroup"]').val(data.id);
            $('[name="idgroupMgroup"]').val(data.idgroup).prop("readonly", true);
            $('[name="nmgroupMgroup"]').val(data.nmgroup.trim()).prop("readonly", false);
            $('[name="grouptypeMgroup"]').val(data.grouptype.trim()).prop("readonly", false);
            $('[name="descriptionMgroup"]').val(data.description.trim()).prop("readonly", false);
            $('[name="choldMgroup"]').val(data.chold.trim()).prop("readonly", false);


            $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Update');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Update Supplier'); // Set title to Bootstrap modal title


            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function delete_mgroup(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formAuthorization').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formAuthorization')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'capital/masters/showing_data_mgroup' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="typeMgroup"]').val('DELETE');
            $('[name="idMgroup"]').val(data.id);
            $('[name="idgroupMgroup"]').val(data.idgroup).prop("readonly", true);
            $('[name="nmgroupMgroup"]').val(data.nmgroup.trim()).prop("readonly", true);
            $('[name="grouptypeMgroup"]').val(data.grouptype.trim()).prop("readonly", true);
            $('[name="descriptionMgroup"]').val(data.description.trim()).prop("readonly", true);
            $('[name="choldMgroup"]').val(data.chold.trim()).prop("disabled", true);


            $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Group'); // Set title to Bootstrap modal title


            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
$('#formAuthorization').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        username: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        kddept: {
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

function saveAuthorization()
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
            var validator = $('#formAuthorization').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype= $('[name="type"]').val();
                var valid= $('[name="id"]').val();
                var valusername= $('[name="username"]').val();
                var valkddept= $('[name="kddept"]').val();
                if ($('#level1_yes').is(":checked"))
                {
                    var vallevel1= $('#level1_yes').val();
                } else {
                    var vallevel1= $('#level1_no').val();
                }

                if ($('#level2_yes').is(":checked"))
                {
                    var vallevel2= $('#level2_yes').val();
                } else {
                    var vallevel2= $('#level2_no').val();
                }
                if ($('#level3_yes').is(":checked"))
                {
                    var vallevel3= $('#level3_yes').val();
                } else {
                    var vallevel3= $('#level3_no').val();
                }
                if ($('#level4_yes').is(":checked"))
                {
                    var vallevel4= $('#level4_yes').val();
                } else {
                    var vallevel4= $('#level4_no').val();
                }
                var valdescription= $('[name="description"]').val();
                var valchold= $('[name="chold"]').val();


                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        type: valtype,
                        id: valid,
                        username: valusername,
                        kddept: valkddept,
                        level_1: vallevel1,
                        level_2: vallevel2,
                        level_3: vallevel3,
                        level_4: vallevel4,
                        description: valdescription,
                        chold: valchold,

                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'capital/masters/save_capital_authorization' + '',
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
                                    reload_table();
                                    $('#modal_form').modal('hide');
                                    bttn.prop('disabled', false);
                                    //window.location.reload();
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

// +++++++++++++++++++++++++++++++++++++++++++++++++++++ MSUBGROUP ++++++++++++++++++++++++++++++++++++++++//
function tableMsubgroup(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tmsubgroup');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength" : 5,
            "language":  languageDatatable(),
            "ajax": {
                "url": HOST_URL + 'capital/masters/list_msubgroup',
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

function reload_table_msubgroup()
{
    var table = $('#tmsubgroup');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter-msubgroup').click(function(){ //button filter event click
    var table = $('#tmsubgroup');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-msubgroup').click(function(){ //button reset event click
    $('#form-filter-msubgroup')[0].reset();
    var table = $('#tmsubgroup');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
/* FOR INPUT FUNCTION */

function add_msubgroup()
{
    save_method = 'add';
    var validator = $('#formMsubgroup').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formMsubgroup')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form_msubgroup').modal('show'); // show bootstrap modal
    $('.modal-title-msubgroup').text('Input Sub Kategori Baru'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="id"]').val();
    $('[name="idgroupMgroup"]').prop("required", true);
    $('[name="nmgroupMgroup"]').prop("required", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}
function update_msubgroup(id)
{
    save_method = 'update';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formMsubgroup').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formMsubgroup')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'capital/masters/showing_data_msubgroup' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="typeMsubgroup"]').val('UPDATE');
            $('[name="idMsubgroup"]').val(data.idserial);
            //$('[name="idgroupMsubgroup"]').val(data.idgroup).prop("readonly", true);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mgroup' + '?var=' + data.idgroup,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgroup, datax.items[0].idgroup, true, true);
                $('[name="idgroupMsubgroup"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgroupMsubgroup"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="idsubgroupMsubgroup"]').val(data.idsubgroup.trim()).prop("readonly", true);
            $('[name="nmsubgroupMsubgroup"]').val(data.nmsubgroup.trim()).prop("readonly", false);
            $('[name="grouptypeMsubgroup"]').val(data.grouptype.trim()).prop("readonly", false);
            $('[name="descriptionMsubgroup"]').val(data.keterangan.trim()).prop("readonly", false);
            $('[name="choldMsubgroup"]').val(data.grouphold.trim()).prop("readonly", false);

            $('[name="idgroupMsubgroup"]').prop("disabled", true);

            $('#btnSaveMsubgroup').removeClass("btn-danger").addClass("btn-primary").text('Update');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form_msubgroup').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title-msubgroup').text('Update Sub Kategori'); // Set title to Bootstrap modal title


            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function delete_msubgroup(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formMsubgroup').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formMsubgroup')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'capital/masters/showing_data_msubgroup' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="typeMsubgroup"]').val('DELETE');
            $('[name="idMsubgroup"]').val(data.idserial);
            //$('[name="idgroupMsubgroup"]').val(data.idgroup).prop("readonly", true);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mgroup' + '?var=' + data.idgroup,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgroup, datax.items[0].idgroup, true, true);
                $('[name="idgroupMsubgroup"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgroupMsubgroup"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="idsubgroupMsubgroup"]').val(data.idsubgroup.trim()).prop("readonly", true);
            $('[name="nmsubgroupMsubgroup"]').val(data.nmsubgroup.trim()).prop("readonly", true);
            $('[name="grouptypeMsubgroup"]').val(data.grouptype.trim()).prop("readonly", true);
            $('[name="descriptionMsubgroup"]').val(data.keterangan.trim()).prop("readonly", true);
            $('[name="choldMsubgroup"]').val(data.grouphold.trim()).prop("disabled", true);

            $('[name="idgroupMsubgroup"]').prop("disabled", true);

            $('#btnSaveMsubgroup').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form_msubgroup').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title-msubgroup').text('Delete Sub Kategori'); // Set title to Bootstrap modal title


            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
$('#formMsubgroup').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        idgroupMgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmgroupMgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        choldMgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        }
    },
    excluded: [':disabled']
});

function saveMsubgroup()
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
            var validator = $('#formMsubgroup').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtypeMsubgroup= $('[name="typeMsubgroup"]').val();
                var validMsubgroup= $('[name="idMsubgroup"]').val();
                var validgroupMsubgroup= $('[name="idgroupMsubgroup"]').val();
                var validsubgroupMsubgroup= $('[name="idsubgroupMsubgroup"]').val();
                var valnmsubgroupMsubgroup= $('[name="nmsubgroupMsubgroup"]').val();
                var valgrouptypeMsubgroup= $('[name="grouptypeMsubgroup"]').val();
                var valdescriptionMsubgroup= $('[name="descriptionMsubgroup"]').val();
                var valcholdMsubgroup= $('[name="choldMsubgroup"]').val();
                var valintype= 'CIT';


                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        id: validMsubgroup,
                        type: valtypeMsubgroup,
                        idgroup: validgroupMsubgroup,
                        nmsubgroup: valnmsubgroupMsubgroup,
                        idsubgroup: validsubgroupMsubgroup,
                        intype: valintype,
                        grouptype: valgrouptypeMsubgroup,
                        description: valdescriptionMsubgroup,
                        chold: valcholdMsubgroup,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'capital/masters/saveSubGrouping' + '',
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
                                    reload_table();
                                    $('#modal_form').modal('hide');
                                    bttn.prop('disabled', false);
                                    window.location.reload();
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

// +++++++++++++++++++++++++++++++++++++++++++++++++++++ MTYPE ++++++++++++++++++++++++++++++++++++++++//
function tableMtype(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tmtype');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength" : 5,
            "language":  languageDatatable(),
            "ajax": {
                "url": HOST_URL + 'capital/masters/list_mtype',
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

function reload_table_mtype()
{
    var table = $('#tmtype');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter-msubgroup').click(function(){ //button filter event click
    var table = $('#tmtype');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-msubgroup').click(function(){ //button reset event click
    $('#form-filter-msubgroup')[0].reset();
    var table = $('#tmtype');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
/* FOR INPUT FUNCTION */

function add_mtype()
{
    save_method = 'add';
    var validator = $('#formMtype').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formMtype')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form_mtype').modal('show'); // show bootstrap modal
    $('.modal-title-mtype').text('Input Jenis'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="id"]').val();
    $('[name="idtype"]').prop("required", true);
    $('[name="nmtype"]').prop("required", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}
function update_mtype(id)
{
    save_method = 'update';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formMtype').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formMtype')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'capital/masters/showing_data_msubgroup' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="typeMsubgroup"]').val('UPDATE');
            $('[name="idMsubgroup"]').val(data.idserial);
            //$('[name="idgroupMsubgroup"]').val(data.idgroup).prop("readonly", true);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mgroup' + '?var=' + data.idgroup,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgroup, datax.items[0].idgroup, true, true);
                $('[name="idgroupMsubgroup"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgroupMsubgroup"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="idsubgroupMsubgroup"]').val(data.idsubgroup.trim()).prop("readonly", true);
            $('[name="nmsubgroupMsubgroup"]').val(data.nmsubgroup.trim()).prop("readonly", false);
            $('[name="grouptypeMsubgroup"]').val(data.grouptype.trim()).prop("readonly", false);
            $('[name="descriptionMsubgroup"]').val(data.keterangan.trim()).prop("readonly", false);
            $('[name="choldMsubgroup"]').val(data.grouphold.trim()).prop("readonly", false);

            $('[name="idgroupMsubgroup"]').prop("disabled", true);

            $('#btnSaveMtype').removeClass("btn-danger").addClass("btn-primary").text('Update');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form_mtype').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title-mtype').text('Update Jenis'); // Set title to Bootstrap modal title


            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
function delete_mtype(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formMtype').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formMtype')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'capital/masters/showing_data_msubgroup' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="typeMsubgroup"]').val('DELETE');
            $('[name="idMsubgroup"]').val(data.idserial);
            //$('[name="idgroupMsubgroup"]').val(data.idgroup).prop("readonly", true);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mgroup' + '?var=' + data.idgroup,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgroup, datax.items[0].idgroup, true, true);
                $('[name="idgroupMsubgroup"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgroupMsubgroup"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="idsubgroupMsubgroup"]').val(data.idsubgroup.trim()).prop("readonly", true);
            $('[name="nmsubgroupMsubgroup"]').val(data.nmsubgroup.trim()).prop("readonly", true);
            $('[name="grouptypeMsubgroup"]').val(data.grouptype.trim()).prop("readonly", true);
            $('[name="descriptionMsubgroup"]').val(data.keterangan.trim()).prop("readonly", true);
            $('[name="choldMsubgroup"]').val(data.grouphold.trim()).prop("disabled", true);

            $('[name="idgroupMsubgroup"]').prop("disabled", true);

            $('#btnSaveMsubgroup').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form_mtype').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title-mtype').text('Delete Sub Kategori'); // Set title to Bootstrap modal title


            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
$('#formMtype').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        idgroupMgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmgroupMgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        choldMgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        }
    },
    excluded: [':disabled']
});

function saveMtype()
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
            var validator = $('#formMsubgroup').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtypeMsubgroup= $('[name="typeMsubgroup"]').val();
                var validMsubgroup= $('[name="idMsubgroup"]').val();
                var validgroupMsubgroup= $('[name="idgroupMsubgroup"]').val();
                var validsubgroupMsubgroup= $('[name="idsubgroupMsubgroup"]').val();
                var valnmsubgroupMsubgroup= $('[name="nmsubgroupMsubgroup"]').val();
                var valgrouptypeMsubgroup= $('[name="grouptypeMsubgroup"]').val();
                var valdescriptionMsubgroup= $('[name="descriptionMsubgroup"]').val();
                var valcholdMsubgroup= $('[name="choldMsubgroup"]').val();
                var valintype= 'CIT';


                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        id: validMsubgroup,
                        type: valtypeMsubgroup,
                        idgroup: validgroupMsubgroup,
                        nmsubgroup: valnmsubgroupMsubgroup,
                        idsubgroup: validsubgroupMsubgroup,
                        intype: valintype,
                        grouptype: valgrouptypeMsubgroup,
                        description: valdescriptionMsubgroup,
                        chold: valcholdMsubgroup,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'capital/masters/saveMtype' + '',
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
                                    reload_table();
                                    $('#modal_form_mtype').modal('hide');
                                    bttn.prop('disabled', false);
                                    window.location.reload();
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
                            $('#modal_form_mtype').modal('hide');
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
                        $('#modal_form_mtype').modal('hide');
                        bttn.prop('disabled', false);
                    }
                });
            }
        }
    });
}
















var defaultInitialGroup = 'CIT';
$("#idgroupMsubgroup").select2({
    placeholder: "Type/Pilih Kategori",
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


/*Location*/
function formatGroup(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idgroup +"   <i class='fa fa-circle-o'></i>   "+ repo.nmgroup +"</div>";
    return markup;
}

function formatGroupSelection(repo) {
    return repo.nmgroup || repo.text;
}



/* IDGROUP MTYPE */
$("#idgroupMtype").select2({
    placeholder: "Type/Pilih Kategori",
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
$("#idsubgroupMtype").select2({
    placeholder: "Type/Pilih Kategori",
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


/* USERNAME */
$("#username").select2({
    placeholder: "Type/Pilih Username",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_user',
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
                _paramglobal_: "",
                _parameterx_: "",
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
    templateResult: formatUserGroup, // omitted for brevity, see the source of this page
    templateSelection: formatGroupUserSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    //$("#newsubdept option[value]").remove();
    //var newOptions = []; // the result of your JSON request
    //$("#id_subdept").val(null).trigger('change');
    //console.log($("#newdept").val());
});
function formatUserGroup(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.username +"   <i class='fa fa-circle-o'></i>   </div>";
    return markup;
}

function formatGroupUserSelection(repo) {
    return repo.username || repo.text;
}


function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kddept +"   <i class='fa fa-circle-o'></i>   "+ repo.nmdept +"  </div>";
    return markup;
}

function formatNewdeptSelection(repo) {
    return repo.nmdept || repo.text;
}
var defaultInitialNewDept = '';
$("#kddept").select2({
    placeholder: "Ketik/Pilih Departemen",
    allowClear: true,
    tags: true,
    multiple: false,
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
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatNewdept, // omitted for brevity, see the source of this page
    templateSelection: formatNewdeptSelection // omitted for brevity, see the source of this page
});




$(document).ready(function() {

    tableCapitalAuthorization();
    //tableMtype();
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});