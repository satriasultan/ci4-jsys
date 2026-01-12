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
function tableCapitalPengajuan(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#jasa_pembayaran');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength" : 10,
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": false,
            "bFilter":true,
            "cache": false,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel','colvis'
            ],
            "ajax": {
                "url": HOST_URL + 'capital/administration/list_jasa_pembayaran',
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
    var table = $('#jasa_pembayaran');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#jasa_pembayaran');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#jasa_pembayaran');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
/* FOR INPUT FUNCTION */

function add_pengajuan_it()
{
    save_method = 'add';
    var validator = $('#formPengajuan').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formPengajuan')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Input Reminder Jasa & Pembayaran'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');

    //$('[name="nik"]').val('').trigger('change');
    //$('[name="estprice_kdlvl"]').prop("readonly", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}
function detail_pembayaran(id) {
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Masuk Ke Detail Pembayaran ID  =  ' + id,
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
            window.location.replace(HOST_URL + 'capital/administration/detail_jasa_pembayaran?q='+ id)
        }
    });
}
function delete_pembayaran(id)
{
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Delete Data Pembayaran ID  =  ' + id,
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
            window.location.replace(HOST_URL + 'capital/administration/delete_pembayaran?q='+ id)
        }
    });
}

function persetujuan_pengajuan(id) {
    var bttn = $('#btnSave');
    bttn.prop('disabled', true);
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data akan dimintakan persetujuan ke manager ybs, yang berarti data tidak akan bisa di ubah, anda yakin...?',
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
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'capital/administration/permintaan_persetujuan_mngr'  + '/' + id,
                dataType: 'json',
                delay: 250,
                success: function(data) {
                    if (data.status){
                        Swal.fire({
                            title: 'Berhasil...!!!',
                            text: data.messages,
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
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });
        }
    });
}

$('#formPengajuan').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        kebutuhanjenis: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmlengkap: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        // kddept: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        idgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idsubgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        kdlvl: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        // estvendor: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
        keterangan: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },

    },
    excluded: [':disabled']
});

function savePengajuan()
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
            var validator = $('#formPengajuan').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                // ajax adding data to database
                var form = $('#formPengajuan');
                var formdata = false;
                if (window.FormData){
                    formdata = new FormData(form[0]);
                }
                $.ajax({
                    url: HOST_URL + 'capital/administration/save_jasa_pembayaran',
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
                                    //Swal.fire('Saved!', '', 'success')
                                    window.location.replace(HOST_URL + 'capital/administration/jasa_pembayaran')
                                    //window.location.reload()
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
                                title: 'Galat...!!!',
                                text: json.messages,
                                backdrop: true,
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                showDenyButton: false,
                                showCancelButton: false,
                                icon: 'error',
                                //denyButtonText: `Don't save`,
                            });
                        }
                        $('#btnSave').text('save'); //change button text
                        $('#btnSave').attr('disabled', false); //set button enable


                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // alert('Gagal Menyimpan / Ubah data / data sudah ada');
                        swal({
                            title: "Galat!!",
                            text: json.messages,
                            type: "error"
                        });
                        $('#btnSave').text('save'); //change button text
                        $('#btnSave').attr('disabled', false); //set button enable

                    }
                });
            } else {
                bttn.prop('disabled', false);
            }
        } else {
            bttn.prop('disabled', false);
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










function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>  <i class='fa fa-circle-o'></i>   "+ repo.nmdept +"  </div>";
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


/*---------------------------*/
function formatRepo(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__title'>" + repo.nik + "</div>";
    if (repo.nmlengkap) {
        markup += "<div class='select2-result-repository__description'>" + repo.nmlengkap +" <i class='fa fa-cube'></i> "+ repo.nmdept +"</div>";
    }
    return markup;
}

function formatRepoSelection(repo) {
    return repo.nmlengkap || repo.text;
}

var defaultInitial = '';
$(".select2_kary").select2({
    //dropdownParent: $('#modal_form'),
    placeholder: "Ketik Nik Atau Nama Karyawan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'capital/administration/list_karyawan',
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
}).on("change", function () {
    //$("#idsubgroup option[value]").remove();
    //var newOptions = []; // the result of your JSON request
    //$("#idsubgroup").val(null).trigger('change');
    //console.log($("#newdept").val());
    var iddept = $(this).val();
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'capital/administration/list_karyawan' + '?var=' + iddept,
        dataType: 'json',
        delay: 100,
    }).then(function (datanik) {
            console.log(datanik.items[0].id_dept);
    /*   list departement , disini ada nik meload department*/
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_departmen' + '?var=' + datanik.items[0].id_dept,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {

                // create the option and append to Select2
                var option = new Option(datax.items[0].nmdept, datax.items[0].kddept, true, true);
                $('[name="kddept"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="kddept"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });


            });

        $('[name="kddept"]').prop("disabled", true);
    });
});

function formatGroup(repo) {
    if (repo.loading) return repo.text;
        var markup = "";
        //var markup = "<div class='select2-result-repository__title'>" + repo.idgroup + "</div>";
    if (repo.nmgroup) {
        markup += "<div class='select2-result-repository__description'>" + repo.nmgroup +"</div>";
    }
    return markup;
}

function formatGroupSelection(repo) {
    return repo.nmgroup || repo.text;
}
var defaultGroup = 'CIT';
$("#idgroup").select2({
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
                _paramglobal_: defaultGroup,
                _parameterx_: defaultGroup,
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
    $("#idsubgroup option[value]").remove();
    var newOptions = []; // the result of your JSON request
    $("#idsubgroup").val(null).trigger('change');
    //console.log($("#newdept").val());
});





function formatSubGroup(repo) {
    if (repo.loading) return repo.text;
    var markup = "";
    //var markup = "<div class='select2-result-repository__title'>" + repo.idsubgroup + "</div>";
    if (repo.nmsubgroup) {
        markup += "<div class='select2-result-repository__description'>" + repo.nmsubgroup +"</div>";
    }
    return markup;
}

function formatGroupSubSelection(repo) {
    return repo.nmsubgroup || repo.text;
}
var defaultInitialSubGroup = 'CIT';
$("#idsubgroup").select2({
    placeholder: "Type/Pilih Sub Kategori",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_msubgroup',
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
                _paramglobal_: defaultInitialSubGroup,
                _parameterx_: defaultInitialSubGroup,
                _var_: $("#idgroup").val(),
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
    templateResult: formatSubGroup, // omitted for brevity, see the source of this page
    templateSelection: formatGroupSubSelection // omitted for brevity, see the source of this page
}).on("change", function () {

    var arr = [ "11.01", "11.02" ];
    if (inArray( $(this).val(), arr )){
        //alert(" ZZ "+ $(this).val());
        $('[name="spec"]').prop("readonly", true);
    } else {
        //alert(" NOT OK "+ $(this).val());
        $('[name="spec"]').prop("readonly", false);
    }
});


/**/
function formatLevelJabatan(repo) {
    if (repo.loading) return repo.text;
    //var markup = "<div class='select2-result-repository__title'>" + repo.kdlvl + "</div>";
    var markup = "";
    if (repo.nmlvljabatan) {
        markup += "<div class='select2-result-repository__description'>" + repo.nmlvljabatan +"</div>";
    }
    return markup;
}

function formatLevelJabatanSelection(repo) {
    return repo.nmlvljabatan || repo.text;
}
var defaultInitialLvlJabatan = '';
$("#kdlvl").select2({
    placeholder: "Type/Pilih Level",
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
                _paramglobal_: defaultInitialLvlJabatan,
                _parameterx_: defaultInitialLvlJabatan,
                _var_: $("#idgroup").val(),
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
    templateResult: formatLevelJabatan, // omitted for brevity, see the source of this page
    templateSelection: formatLevelJabatanSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    //$("#newsubdept option[value]").remove();
    //var newOptions = []; // the result of your JSON request
    //$("#id_subdept").val(null).trigger('change');
    //console.log($("#newdept").val());
});


$("#kebutuhanjenis").on("change", function () {
    console.log($(this).val() + 'H');
    if ($(this).val()==='BARU') {
        $("#noassetit").prop("required", false);
        $("#noassetit").prop("readonly", true);

        //$('.sref').remove();
        //$('.sreference').append('<div class="sref"> <label for="description">Reference ID/PO Number</label><select name="docref" id="docref2" class="form-control" required></select></div>');

    } else {
        $("#noassetit").prop("required", true);
        $("#noassetit").prop("readonly", false);
        //$('.sref').remove();
        //$("#kebutuhanjenis")$('.sreference').append('<div class="sref"> <label for="description">Reference ID/PO Number</label> <input type="text" name="docref" class="form-control" id="docref1"  style="text-transform: uppercase" placeholder="Reference ID" required></div>');
    }


});

$("#kdlvl").on("change", function () {
    var idsubgroup = $('#idsubgroup').val();
    var id = $(this).val();

    if (!idsubgroup && id){
        alert('Peringatan !! sub kategori tidak boleh kosong!!');
        //$('#formPengajuan').bootstrapValidator('updateStatus', 'idsubgroup', 'NOT_VALIDATED').bootstrapValidator('validateField', 'idsubgroup');
    } else {
        //Ajax Load data from ajax
        $.ajax({
            //url: HOST_URL + 'capital/masters/showing_data_kdlvl' + '/' + id,
            url: HOST_URL + 'capital/masters/showing_data_kdlvl_msubgroup/?kdlvl' + '=' + id + '&idsubgroup=' + idsubgroup,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                //console.log(data.pricemax1 + " HALO ")
                var pricemax = 0;
                if (data.pricemax1.length != 0 ){
                    pricemax = data.pricemax1;
                } else {
                    pricemax = 0 ;
                }

                $('[name="estprice_kdlvl"]').val(pricemax).prop("readonly", true).trigger('change');

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
});






var defaultInitialNewDeptFilter = '';
$("#kddept_filter").select2({
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
                _paramglobal_: defaultInitialNewDeptFilter,
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

$("#idgroup_filter").select2({
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
                _paramglobal_: defaultGroup,
                _parameterx_: defaultGroup,
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
    $("#idsubgroup_filter option[value]").remove();
    var newOptions = []; // the result of your JSON request
    $("#idsubgroup_filter").val(null).trigger('change');
    //console.log($("#newdept").val());
});
$("#idsubgroup_filter").select2({
    placeholder: "Type/Pilih Sub Kategori",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_msubgroup',
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
                _paramglobal_: defaultInitialSubGroup,
                _parameterx_: defaultInitialSubGroup,
                _var_: $("#idgroup").val(),
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
    templateResult: formatSubGroup, // omitted for brevity, see the source of this page
    templateSelection: formatGroupSubSelection // omitted for brevity, see the source of this page
}).on("change", function () {
});

$(document).ready(function() {

    tableCapitalPengajuan()

    //tableMtype();
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});