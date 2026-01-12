//console.log('kontol' + HOST_URL);
var save_method; //for save method string
var table;

function tableReadable(){
    //read editable/input
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/skPeringatan/showResultTmpSP' + '',
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            //console.log("On Ready" + json.dataTables.items[0].nik);
            $('[name="startdate"]').val(json.dataTables.items[0].startdate);
            $('[name="enddate"]').val(json.dataTables.items[0].enddate);
            $('[name="docref"]').val(json.dataTables.items[0].docref);
            $('[name="doctype"]').val(json.dataTables.items[0].doctype);
            $('[name="description"]').val(json.dataTables.items[0].description);
            // Fetch the preselected item, and add to the control
            var select2_kary = $('.select2_kary');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'trans/skperingatan/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                //console.log(datax.items[0].nik + 'KONTOL');
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
            // Fetch the preselected item, and add to the control
            var doctypesp = $('#doctypesp');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'trans/skperingatan/list_mst_skperingatan_by_id' + '?var=' + json.dataTables.items[0].doctype,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].docname, datax.items[0].docno, true, true);
                doctypesp.append(option).trigger('change');

                // manually trigger the `select2:select` event
                doctypesp.trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
        }
    });

    //datepicker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true,
        orientation: "top auto",
        todayBtn: true,
        todayHighlight: true,
    });

    $('[name="nik"]').change(function () {
        console.log("INI -> NIKNYA " + $(this).val());
        $.ajax({
            type: 'GET',
            url: HOST_URL + 'trans/skperingatan/showTmpSP' + '?nik=' + $(this).val().trim(),
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

    })

};


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
        url: HOST_URL + 'trans/skperingatan/list_karyawan',
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




function formatRepoMstSp(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__title'>" + repo.docno + "</div>";
    if (repo.docname) {
        markup += "<div class='select2-result-repository__description'>" + repo.docname +" <i class='fa fa-circle-o'></i> "+ "" +"</div>";
    }
    return markup;
}

function formatRepoSelectionMstSp(repo) {
    return repo.docname || repo.text;
}
var defaultInitialsp = '';
$("#doctypesp").select2({
    placeholder: "Ketik Jenis Teguran",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'trans/skperingatan/list_mst_skperingatan',
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
                _paramglobal_: defaultInitialsp,
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
    templateResult: formatRepoMstSp, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelectionMstSp // omitted for brevity, see the source of this page
});

$('#formInputSkPeringatan').bootstrapValidator({
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
                    message: 'The field can not be empty'
                }
            }
        },
        docref: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        doctype: {
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
        nik: {
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
        }
    },
    excluded: [':disabled']
});



function finishInput() {

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

            var validator = $('#formInputSkPeringatan').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                // ajax adding data to database
                var form = $('#formInputSkPeringatan');
                var formdata = false;
                if (window.FormData){
                    formdata = new FormData(form[0]);
                }
                $.ajax({
                    url: HOST_URL + 'trans/skperingatan/saveDataSk',
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
                            //alert(json.messages);
                            //  swal({
                            //      title: "Good Job !!",
                            //      text: json.messages,
                            //      type: "success"
                            // //}).then((value) => {
                            //  }, function (value) {
                            //      swal(`The returned value is: ${value}`);
                            //  });

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
                                    window.location.replace(HOST_URL + 'trans/skperingatan')
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
                            swal({
                                title: "Galat!!",
                                text: json.messages,
                                type: "error"
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
                //alert("FInish Input");
            }


        } else if (result.isDenied) {
            return false;
        }
    })

}


$(".dateRangePick").daterangepicker({
    //singleDatePicker: true,
    //showDropdowns: true
},function () {
    $('#formInputSkPeringatan').bootstrapValidator('updateStatus', 'startdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'startdate');
});

$('.zz').on('changeDate show change', function(e) {
        $('#formInputSkPeringatan').bootstrapValidator('updateStatus', 'startdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'startdate');
});

$(document).ready(function() {
    tableReadable();
    //* input form */
    //onReadyInput();
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});