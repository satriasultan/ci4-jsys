//console.log('kontol' + HOST_URL);
var save_method; //for save method string
var table;

/* PERSONAL STATUS VAKSIN DATATABLE */
function tableStatusVaksin(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tStatusVaksin');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('master/personal/list_statusvaksin'),
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

function reload_table_statusvaksin()
{
    var table = $('#tStatusVaksin');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#formStatusVaksin').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        chold: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idstsvaksin: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        nmstsvaksin: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        groupvaksin: {
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

    },
    excluded: [':disabled']
});

function add_statusvaksin()
{
    save_method = 'add';
    var validator = $('#formStatusVaksin').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formStatusVaksin')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('INPUT STATUS VAKSIN'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="idstsvaksin"]').prop("required", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}

function update_statusvaksin(id)
{
    save_method = 'update';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formStatusVaksin').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formStatusVaksin')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'master/personal/showing_data_statusvaksin' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('UPDATE');
            $('[name="idstsvaksin"]').val(data.idstsvaksin.trim()).prop("readonly", true);
            $('[name="nmstsvaksin"]').val(data.nmstsvaksin.trim()).prop("readonly", false);
            $('[name="groupvaksin"]').val(data.groupvaksin.trim()).prop("readonly", false);
            $('[name="description"]').val(data.description.trim()).prop("readonly", false);
            $('[name="chold"]').val(data.chold.trim()).prop("readonly", false);
            $('#btnSave').removeClass("btn-primary").addClass("btn-primary").text('Update');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Update Data Vaksin'); // Set title to Bootstrap modal title
            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function delete_statusvaksin(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formStatusVaksin').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formStatusVaksin')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'master/personal/showing_data_statusvaksin' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('DELETE');
            $('[name="idstsvaksin"]').val(data.idstsvaksin.trim()).prop("readonly", true);
            $('[name="nmstsvaksin"]').val(data.nmstsvaksin.trim()).prop("readonly", true);
            $('[name="groupvaksin"]').val(data.groupvaksin.trim()).prop("disabled", true);
            $('[name="description"]').val(data.description.trim()).prop("readonly", true);
            $('[name="chold"]').val(data.chold.trim()).prop("disabled", true);
            $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Status Vaksin'); // Set title to Bootstrap modal title

            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function detail_statusvaksin(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formStatusVaksin').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formStatusVaksin')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'master/personal/showing_data_statusvaksin' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('xx');
            $('[name="idstsvaksin"]').val(data.idstsvaksin.trim()).prop("readonly", true);
            $('[name="nmstsvaksin"]').val(data.nmstsvaksin.trim()).prop("readonly", true);
            $('[name="groupvaksin"]').val(data.groupvaksin.trim()).prop("disabled", true);
            $('[name="description"]').val(data.description.trim()).prop("readonly", true);
            $('[name="chold"]').val(data.chold.trim()).prop("disabled", true);
            $('#btnSave').hide();
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Detail Status Vaksin'); // Set title to Bootstrap modal title

            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function savestatusvaksin()
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

            var validator = $('#formStatusVaksin').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var validstsvaksin = $('[name="idstsvaksin"]').val();
                var valnmstsvaksin = $('[name="nmstsvaksin"]').val();
                var valgroupvaksin = $('[name="groupvaksin"]').val();
                var valdescription = $('[name="description"]').val();
                var valchold = $('[name="chold"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        idstsvaksin: validstsvaksin,
                        nmstsvaksin: valnmstsvaksin,
                        groupvaksin: valgroupvaksin,
                        description: valdescription,
                        chold: valchold,
                        type: valtype,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'master/personal/saveEntryStatusvaksin' + '',
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
                                    reload_table_statusvaksin();
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
/* ABSCUT DATATABLE */






$(document).ready(function() {
    tableStatusVaksin();

    $("#daterange").daterangepicker({
       //singleDatePicker: true,
       //showDropdowns: true
    });
    $("#daterange").val('');


    $("#daterangefilter").daterangepicker({
        //singleDatePicker: true,
        //showDropdowns: true
    });
    $("#daterangefilter").val('');
    //* input form */
    //onReadyInput();
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});