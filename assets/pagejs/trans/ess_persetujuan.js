//console.log('kontol' + HOST_URL);
var save_method; //for save method string
var table;

function tableReadable(){


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
        //console.log("INI -> NIKNYA " + $(this).val());
        $.ajax({
            type: 'GET',
            url: HOST_URL + 'trans/ess_cuti/showDetailNik' + '?nik=' + $(this).val().trim(),
            dataType: 'json',
            dataFilter: function(data) {
                var json = jQuery.parseJSON(data);
                json.status = json.dataTables.status;
                json.total_count = json.dataTables.total_count;
                json.items = json.dataTables.items;
                json.incomplete_results = json.dataTables.incomplete_results;

                $('[name="department"]').val(json.dataTables.items[0].nmdept);
                $('[name="nmsubdept"]').val(json.dataTables.items[0].nmsubdept);
                $('[name="id_gol"]').val(json.dataTables.items[0].id_gol);
                $('[name="nmgroupgol"]').val(json.dataTables.items[0].nmgroupgol);
                $('[name="plant"]').val(json.dataTables.items[0].locaname);
                $('[name="nikv"]').val(json.dataTables.items[0].nik);
                $('[name="costcenter"]').val(json.dataTables.items[0].nmcostcenter);
                $('[name="sisacuti"]').val(json.dataTables.items[0].sisacuti);
                //return JSON.stringify(json); // return JSON string
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //console.log("Failed To Loading Data");
            }
        });

    })

};

function documentReadable(){
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/ess_cuti/showDetailAbscut' + '?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            //console.log("On Ready" + json.dataTables.items[0].nik);
            // Fetch the preselected item, and add to the control
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'trans/ess_cuti/list_mtypecuti' + '?var=' + json.dataTables.items[0].idtypecuti,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmtypecuti, datax.items[0].idtypecuti, true, true);
                $('[name="idtypecuti"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idtypecuti"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


            var select2_kary = $('.select2_kary');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
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

            $('[name="nik"]').prop('disabled', true);
            $('[name="keterangan"]').val(json.dataTables.items[0].description);

            $('[name="idtypecuti"]').change(function(){
                var a = ['PA','DT','IZ'];
                $('.fillduration').empty();
                if ( inArray($(this).val().trim(), a)) {
                    $('.fillduration').append('' +
                        '<div class="form-group chainingtype"> ' +
                        ' <label>Input Durasi Menit</label>' +
                        '<div class="input-group">' +
                        '<div class="input-group-addon">' +
                        '<i class="fa fa-calendar"></i>' +
                        '</div>' +
                        '<input type="number" class="form-control" name="durasimenit" id="durasimenit"' +
                        'placeholder="Input Durasi Satuan Menit" maxLength="4" required>' +
                        '</div>' +
                        '</div> ');

                    $('[name="durasimenit"]').val(json.dataTables.items[0].durasimenit);

                }
                //console.log('CHAIN APPENX'+ json.dataTables.items[0].durasimenit);
            });

            $('[name="tgl"]').val(json.dataTables.items[0].tglawal1);



        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            //console.log("Failed To Loading Data");
        }
    });
}

function documentReadableDetail(){
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/ess_cuti/showDetailAbscut' + '?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            //console.log("On Ready" + json.dataTables.items[0].nik);
            // Fetch the preselected item, and add to the control
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'trans/ess_cuti/list_mtypecuti' + '?var=' + json.dataTables.items[0].idtypecuti,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmtypecuti, datax.items[0].idtypecuti, true, true);
                $('[name="idtypecuti"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idtypecuti"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


            var select2_kary = $('.select2_kary');
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
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

            $('[name="nik"]').prop('disabled', true);
            $('[name="keterangan"]').val(json.dataTables.items[0].description);

            $('[name="idtypecuti"]').change(function(){
                var a = ['PA','DT','IZ'];
                $('.fillduration').empty();
                if ( inArray($(this).val().trim(), a)) {
                    $('.fillduration').append('' +
                        '<div class="form-group chainingtype"> ' +
                        ' <label>Input Durasi Menit</label>' +
                        '<div class="input-group">' +
                        '<div class="input-group-addon">' +
                        '<i class="fa fa-calendar"></i>' +
                        '</div>' +
                        '<input type="number" class="form-control" name="durasimenit" id="durasimenit"' +
                        'placeholder="Input Durasi Satuan Menit" maxLength="4" required>' +
                        '</div>' +
                        '</div> ');

                    $('[name="durasimenit"]').val(json.dataTables.items[0].durasimenit);
                    $('[name="durasimenit"]').prop('disabled', true);

                }
                //console.log('CHAIN APPENX'+ json.dataTables.items[0].durasimenit);
            });

            $('[name="tgl"]').val(json.dataTables.items[0].tglawal1);

            $('[name="tgl"]').prop('disabled', true);
            $('[name="idtypecuti"]').prop('disabled', true);
            $('[name="keterangan"]').prop('disabled', true);
            $('#btnSave').hide();



        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            //console.log("Failed To Loading Data");
        }
    });
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
        url: HOST_URL + 'trans/ess_cuti/list_karyawan',
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


$("#nikfilter").select2({
    placeholder: "Ketik Nik Atau Nama Karyawan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'trans/ess_cuti/list_karyawan',
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

$("#nikdownload").select2({
    placeholder: "Ketik Nik Atau Nama Karyawan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'trans/ess_cuti/list_karyawan',
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

/**************************** crud version ************************************************************/
function editDocument(e){
    Swal.fire({
        title: 'Peringatan..!!!',
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
            window.location.replace(HOST_URL + 'trans/ess_cuti/edit' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}



function formatMtypecuti(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idtypecuti +"   <i class='fa fa-circle-o'></i>   "+ repo.nmtypecuti +"</div>";
    return markup;
}

function formatMtypecutiSelection(repo) {
    return repo.nmtypecuti || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#idtypecuti").select2({
    placeholder: "Ketik/Pilih Type Absensi",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'trans/ess_cuti/list_mtypecuti',
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
    templateResult: formatMtypecuti, // omitted for brevity, see the source of this page
    templateSelection: formatMtypecutiSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    //alert('CONTOLS' + $(this).val());
    var a = ['PA','DT','IZ'];
    $('.fillduration').empty();
    if ( inArray($(this).val(), a)) {
        $('.fillduration').append('' +
            '<div class="form-group chainingtype"> ' +
                ' <label>Input Durasi Menit</label>' +
                '<div class="input-group">' +
                    '<div class="input-group-addon">' +
                        '<i class="fa fa-calendar"></i>' +
                    '</div>' +
                    '<input type="number" class="form-control" name="durasimenit" id="durasimenit"' +
                           'placeholder="Input Durasi Satuan Menit" maxLength="4" required>' +
                '</div>' +
            '</div> ');
        //console.log('CHAIN APPEN'+ a);
    }


});


$("#idtypecutifilter").select2({
    placeholder: "Ketik/Pilih Type Absensi",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'trans/ess_cuti/list_mtypecuti',
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
    templateResult: formatMtypecuti, // omitted for brevity, see the source of this page
    templateSelection: formatMtypecutiSelection // omitted for brevity, see the source of this page
});
$("#idtypecutidownload").select2({
    placeholder: "Ketik/Pilih Type Absensi",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'trans/ess_cuti/list_mtypecuti',
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
    templateResult: formatMtypecuti, // omitted for brevity, see the source of this page
    templateSelection: formatMtypecutiSelection // omitted for brevity, see the source of this page
});



function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kddept +"   <i class='fa fa-circle-o'></i>   "+ repo.nmdept +"  </div>";
    return markup;
}

function formatNewdeptSelection(repo) {
    return repo.nmdept || repo.text;
}
var defaultInitialNewDept = '';
$("#id_dept_filter").select2({
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
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatNewdept, // omitted for brevity, see the source of this page
    templateSelection: formatNewdeptSelection // omitted for brevity, see the source of this page
});

$("#id_dept_download").select2({
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
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatNewdept, // omitted for brevity, see the source of this page
    templateSelection: formatNewdeptSelection // omitted for brevity, see the source of this page
});

function detailDocument(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Detail Inputan, Dokumen: ' + e,
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
            window.location.replace(HOST_URL + 'trans/ess_cuti/detail' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}


$('#formInputAbscut').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        nik: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        durasimenit: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        tgl: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
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

            var validator = $('#formInputAbscut').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                // ajax adding data to database
                var form = $('#formInputAbscut');
                var formdata = false;
                if (window.FormData){
                    formdata = new FormData(form[0]);
                }
                $.ajax({
                    url: HOST_URL + 'trans/ess_cuti/saveAbscut',
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
                                    window.location.replace(HOST_URL + 'trans/ess_cuti')
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
                                title: 'Gagal Fail...!!!',
                                text: json.messages,
                                backdrop: true,
                                allowOutsideClick: false,
                                showConfirmButton: true,
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
                //alert("FInish Input");
            }


        } else if (result.isDenied) {
            return false;
        }
    })

}


// $(".dateRangePick").daterangepicker({
//     //singleDatePicker: true,
//     //showDropdowns: true
// },function () {
//     $('#formInputLembur').bootstrapValidator('updateStatus', 'startdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'startdate');
// });
//
// $('.zz').on('changeDate show change', function(e) {
//         $('#formInputLembur').bootstrapValidator('updateStatus', 'startdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'startdate');
// });

$('#tgl').datepicker().on('changeDate show', function(e) {
    $('#formInputAbscut').bootstrapValidator('revalidateField', 'tgl');
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
$("#plant").select2({
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

function formatGol(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idgroupgol +"   <i class='fa fa-circle-o'></i>   "+ repo.nmgroupgol +"</div>";
    return markup;
}

function formatGolSelection(repo) {
    return repo.nmgroupgol || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#id_gol").select2({
    placeholder: "Ketik/Pilih Golongan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_golongan',
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
    templateResult: formatGol, // omitted for brevity, see the source of this page
    templateSelection: formatGolSelection // omitted for brevity, see the source of this page
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//var defaultInitialGol = $("#newdept").val();
$("#plantfilter").select2({
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

//var defaultInitialGol = $("#newdept").val();
$("#id_gol_filter").select2({
    placeholder: "Ketik/Pilih Golongan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_golongan',
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
    templateResult: formatGol, // omitted for brevity, see the source of this page
    templateSelection: formatGolSelection // omitted for brevity, see the source of this page
});

/* ABSCUT DATATABLE */

function tableMkriteria(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tMkriteria');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('trans/ess_cuti/list_mkriteria'),
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

function reload_table_kriteria()
{
    var table = $('#tMkriteria');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#formMkriteria').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        chold: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idtypecuti: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        nmtypecuti: {
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

function add_mkriteria()
{
    save_method = 'add';
    var validator = $('#formMkriteria').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formMkriteria')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('INPUT MASTER KRITERIA'); // Set Title to Bootstrap modal title
    $('[name="type"]').val('INPUT');
    $('[name="idtypecuti"]').prop("required", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}

function update_mkriteria(id)
{
    save_method = 'update';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formMkriteria').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formMkriteria')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'trans/ess_cuti/showing_data_kriteria' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('UPDATE');
            $('[name="idtypecuti"]').val(data.idtypecuti.trim()).prop("readonly", true);
            $('[name="nmtypecuti"]').val(data.nmtypecuti.trim());
            $('[name="description"]').val(data.description.trim());
            $('[name="chold"]').val(data.chold.trim());
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

function delete_mkriteria(id)
{
    save_method = 'delete';
    //$('#modal_form').removeData('bs.modal');
    var validator = $('#formMkriteria').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#formMkriteria')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'trans/ess_cuti/showing_data_kriteria' + '/' + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="type"]').val('DELETE');
            $('[name="idtypecuti"]').val(data.idtypecuti.trim()).prop("readonly", true);
            $('[name="nmtypecuti"]').val(data.nmtypecuti.trim()).prop("disabled", true);
            $('[name="description"]').val(data.description.trim()).prop("disabled", true);
            $('[name="chold"]').val(data.chold.trim()).prop("disabled", true);
            $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Delete');
            //$('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Delete Kriteria'); // Set title to Bootstrap modal title

            //.removeClass("btn-primary").addClass("btn-danger"); // set button

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function saveMkriteria()
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

            var validator = $('#formMkriteria').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                var valtype = $('[name="type"]').val();
                var validtypecuti = $('[name="idtypecuti"]').val();
                var valnmtypecuti = $('[name="nmtypecuti"]').val();
                var valdescription = $('[name="description"]').val();
                var valchold = $('[name="chold"]').val();

                var fillData = {
                    'success': true,
                    'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                    'message': '',
                    'body': {
                        idtypecuti: validtypecuti,
                        nmtypecuti: valnmtypecuti,
                        description: valdescription,
                        chold: valchold,
                        type: valtype,
                    },
                };
                $.ajax({
                    type: "POST",
                    url: HOST_URL + 'trans/ess_cuti/saveEntryKriteria' + '',
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
                                    reload_table_kriteria();
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
    tableMkriteria();
    $('[name="kdlembur"]').select2();
    if ($('[name="type"]').val() === 'EDIT') {
        documentReadable();
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        documentReadableDetail();
    }
    tableReadable();
    //console.log($('[name="type"]').val());


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