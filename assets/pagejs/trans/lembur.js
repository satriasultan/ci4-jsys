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
        console.log("INI -> NIKNYA " + $(this).val());
        $.ajax({
            type: 'GET',
            url: HOST_URL + 'trans/lembur/showDetailNik' + '?nik=' + $(this).val().trim(),
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
                //return JSON.stringify(json); // return JSON string
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log("Failed To Loading Data");
            }
        });

    })

};

function documentReadable(){
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/lembur/showDetailLembur' + '?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            //console.log("On Ready" + json.dataTables.items[0].nik);
            // Fetch the preselected item, and add to the control
            $('[name="tgl_kerja"]').val(json.dataTables.items[0].tgl_kerja1);
            $('[name="durasijam"]').val(json.dataTables.items[0].durasijam);
            $('[name="kdlembur"]').val(json.dataTables.items[0].kdlembur.trim()).trigger('change');


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
            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
        }
    });
}

function documentReadableDetail(){
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'trans/lembur/showDetailLembur' + '?docno=' + $('[name="docno"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            //console.log("On Ready" + json.dataTables.items[0].nik);
            // Fetch the preselected item, and add to the control
            $('[name="tgl_kerja"]').val(json.dataTables.items[0].tgl_kerja1);
            $('[name="durasijam"]').val(json.dataTables.items[0].durasijam);
            $('[name="kdlembur"]').val(json.dataTables.items[0].kdlembur.trim()).trigger('change');


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
            $('[name="tgl_kerja"]').prop('disabled', true);
            $('[name="kdlembur"]').prop('disabled', true);
            $('[name="durasijam"]').prop('disabled', true);
            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan).prop('disabled', true);
            $('#btnSave').prop('disabled', true).hide();

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
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
        url: HOST_URL + 'trans/lembur/list_karyawan',
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
            window.location.replace(HOST_URL + 'trans/lembur/edit' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}

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
            window.location.replace(HOST_URL + 'trans/lembur/detail' + '/?docno=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}


$('#formInputLembur').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        nik: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        durasijam: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        tgl_kerja: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        kdlembur: {
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

            var validator = $('#formInputLembur').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                // ajax adding data to database
                var form = $('#formInputLembur');
                var formdata = false;
                if (window.FormData){
                    formdata = new FormData(form[0]);
                }
                $.ajax({
                    url: HOST_URL + 'trans/lembur/saveLembur',
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
                                    window.location.replace(HOST_URL + 'trans/lembur')
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

$('#tgl_kerja').datepicker().on('changeDate show', function(e) {
    $('#formInputLembur').bootstrapValidator('revalidateField', 'tgl_kerja');
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

// COST CENTER
function formatCostcenter(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idcostcenter +"   <i class='fa fa-circle-o'></i>   "+ repo.nmcostcenter +"</div>";
    return markup;
}

function formatCostcenterSelection(repo) {
    return repo.nmcostcenter || repo.text;
}
//var defaultInitialDivision = $("#newdept").val();
$("#costcenter_filter").select2({
    placeholder: "Ketik/Pilih Cost Center",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_costcenter',
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
    templateResult: formatCostcenter, // omitted for brevity, see the source of this page
    templateSelection: formatCostcenterSelection // omitted for brevity, see the source of this page
});
$("#costcenter_download").select2({
    placeholder: "Ketik/Pilih Cost Center",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_costcenter',
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
    templateResult: formatCostcenter, // omitted for brevity, see the source of this page
    templateSelection: formatCostcenterSelection // omitted for brevity, see the source of this page
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

























$(document).ready(function() {
    $("#tLemburResume").dataTable({ "pageLength": 25,"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
    $('[name="kdlembur"]').select2();
    if ($('[name="type"]').val() === 'EDIT') {
        documentReadable();
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        documentReadableDetail();
    }
    tableReadable();
    console.log($('[name="type"]').val());


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
    $("#daterangerecalculate").daterangepicker({
        //singleDatePicker: true,
        //showDropdowns: true
    });
    $("#daterangerecalculate").val('');

    $("#daterangefilterResume").daterangepicker({
        //singleDatePicker: true,
        //showDropdowns: true
    });
    $("#daterangefilterResume").val('');
    $("#daterangeExcelResume").daterangepicker({
        //singleDatePicker: true,
        //showDropdowns: true
    });
    $("#daterangeExcelResume").val('');
    //* input form */

});