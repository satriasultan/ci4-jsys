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
function tableInternet(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tinternet');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'form/pa/list_internet',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    // data.idgroup = $('#idgroup').val();
                    data.status_filter = $('#status_filter').val(); //A,P,S,ALL
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
    var table = $('#tinternet');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

// function toggleAkses(val) {
//     var emailField = document.getElementById('emailField');
//     var otherField = document.getElementById('otherField');

//     if (val === 'EMAIL') {
//         document.getElementById('fieldEmail').style.display = 'block';
//         document.getElementById('fieldOther').style.display = 'none';
//         otherField.value = '';
//     } else if (val === 'OTHER') {
//         document.getElementById('fieldEmail').style.display = 'none';
//         document.getElementById('fieldOther').style.display = 'block';
//         emailField.value = '';
//     } else {
//         document.getElementById('fieldEmail').style.display = 'none';
//         document.getElementById('fieldOther').style.display = 'none';
//         emailField.value = '';
//         otherField.value = '';
//     }
// }

function toggleAkses() {
    // tampilkan field sesuai checkbox
    document.getElementById('fieldEmail').style.display     = document.getElementById('aksesEmail').checked ? 'block' : 'none';
    document.getElementById('fieldAplikasi').style.display  = document.getElementById('aksesApp').checked   ? 'block' : 'none';
    document.getElementById('fieldOther').style.display     = document.getElementById('aksesOther').checked ? 'block' : 'none';

    // reset nilai input kalau uncheck
    if (!document.getElementById('aksesEmail').checked) {
        document.getElementById('emailField').value = '';
    }
    if (!document.getElementById('aksesApp').checked) {
        document.getElementById('appField').value = '';
    }
    if (!document.getElementById('aksesOther').checked) {
        document.getElementById('otherField').value = '';
    }
}


function toggleWaktu(val) {
    var tertentuField = document.getElementById('tertentuField');
    var otherField = document.getElementById('otherWaktuField');

    if(val == 'TERTENTU'){
        document.getElementById('fieldTertentu').style.display = 'block';
        document.getElementById('fieldOtherWaktu').style.display = 'none';
        tertentuField.value = '';
    } else if(val == 'OTHER'){
        document.getElementById('fieldOtherWaktu').style.display = 'block';
        document.getElementById('fieldTertentu').style.display = 'none';
        otherField.value = '';
    } else {
        document.getElementById('fieldTertentu').style.display = 'none';
        document.getElementById('fieldOtherWaktu').style.display = 'none';
        tertentuField.value = '';
        otherField.value = '';
    }

}

function toggleKaryawan(val) {
    var fieldOther = document.getElementById('fieldOtherKaryawan');
    if (val === 'OTHER') {
        fieldOther.style.display = 'block';
    } else {
        fieldOther.style.display = 'none';
        // Kosongkan semua input di dalam fieldOtherKaryawan
        fieldOther.querySelectorAll('input').forEach(function(input) {
            input.value = '';
        });
    }
}


$('#emailField').on('input', function () {
    const email = this.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email.length > 0 && !emailPattern.test(email)) {
        $(this).addClass('is-invalid');
    } else {
        $(this).removeClass('is-invalid');
    }
});


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tinternet');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tinternet');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

//UPDATE ITEM
function documentReadable() {
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'form/pa/showDetailInternet' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function (data) {
            var json = jQuery.parseJSON(data);
            var dt = json.dataTables.items[0]; // data pertama

            // Isi semua field dengan trim
            $('[name="docno"]').val($.trim(dt.docno));
            $('[name="nmpemohon"]').val($.trim(dt.nmpemohon));
            $('[name="deptpic"]').val($.trim(dt.deptpic));
            // $('[name="jabatan"]').val($.trim(dt.jabatan));
            $('[name="docdate"]').val($.trim(dt.docdate));
            // $('[name="departmen"]').val($.trim(dt.departmen));
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_departmen_nm' + '?var=' + dt.departmen,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmdept, datax.items[0].nmdept, true, true);
                $('[name="departmen"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="departmen"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_lvljabatan_nm' + '?var=' + dt.jabatan,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {


                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlvljabatan, datax.items[0].nmlvljabatan, true, true);
                $('[name="jabatan"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="jabatan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="osticket"]').val($.trim(dt.osticket));

            // $('[name="jnsakses"]').val($.trim(dt.jnsakses)).trigger('change');
            if (dt.aksesuser === "t") {
                $('[name="aksesUser"]').prop("checked", true);
            } else {
                $('[name="aksesUser"]').prop("checked", false);
            }
            if (dt.aksesinternet === "t") {
                $('[name="aksesInternet"]').prop("checked", true);
            } else {
                $('[name="aksesInternet"]').prop("checked", false);
            }
            if (dt.aksesemail === "t") {
                $('[name="aksesEmail"]').prop("checked", true);
                toggleAkses()
            } else {
                $('[name="aksesEmail"]').prop("checked", false);
            }
            if (dt.aksesapp === "t") {
                $('[name="aksesApp"]').prop("checked", true);
                toggleAkses()
            } else {
                $('[name="aksesApp"]').prop("checked", false);
            }
            if (dt.aksesother === "t") {
                $('[name="aksesOther"]').prop("checked", true);
                toggleAkses()
            } else {
                $('[name="aksesOther"]').prop("checked", false);
            }
            $('[name="appField"]').val($.trim(dt.appfield));
            $('[name="emailField"]').val($.trim(dt.emailfield));
            $('[name="otherField"]').val($.trim(dt.otherfield));
            $('[name="keperluan"]').val($.trim(dt.keperluan));
            $('[name="sifatakses"]').val($.trim(dt.sifatakses)).trigger('change');
            $('[name="waktuakses"]').val($.trim(dt.waktuakses)).trigger('change');
            $('[name="tertentuField"]').val($.trim(dt.tertentufield));
            $('[name="otherWaktuField"]').val($.trim(dt.otherwaktufield));
            $('[name="expdate"]').val($.trim(dt.expdate));

            $('[name="perangkatregist"]').val($.trim(dt.perangkatregist));
            $('[name="picakun"]').val($.trim(dt.picakun)).trigger('change');
            $('[name="nmpic"]').val($.trim(dt.nmpic));
            $('[name="jabatanpic"]').val($.trim(dt.jabatanpic));
            $('[name="departemenpic"]').val($.trim(dt.departemenpic));
            $('[name="description"]').val($.trim(dt.description));

            var isDetail = $('[name="type"]').val() === 'DETAIL';
            if (isDetail) {
                // disable semua input, textarea, select, dan checkbox/radio
                $('input, textarea, select').prop('disabled', true);
            }

            // Kalau mau readonly pada field tertentu:
            $('[name="docno"]').prop('readonly', true);

        },
        complete: function () {
            $("#loadMe").modal("hide");
        },
        error: function () {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });

    $("#loadMe").modal("hide");
}


/**/
function formatLevelJabatan(repo) {
    if (repo.loading) return repo.text;
    //var markup = "<div class='select2-result-repository__title'>" + repo.kdlvl + "</div>";
    var markup = "";
    if (repo.text) {
        markup += "<div class='select2-result-repository__description'>" + repo.text +"</div>";
    }
    return markup;
}

function formatLevelJabatanSelection(repo) {
    return repo.nmlvljabatan || repo.text;
}
var defaultInitialLvlJabatan = '';
$("#jabatan").select2({
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
        processResults: function (data, params) {
            params.page = params.page || 1;

            var newData = $.map(data.items, function (obj) {
                return {
                    id: obj.nmlvljabatan,     // ini yang jadi value di select2
                    text: obj.nmlvljabatan,   // ini yang ditampilkan
                    kdlvl: obj.kdlvl  // tetap simpan kode, bisa diakses manual
                };
            });

            return {
                results: newData,
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


function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>  <i class='fa fa-circle-o'></i>   "+ repo.text +"  </div>";
    return markup;
}

function formatNewdeptSelection(repo) {
    return repo.nmdept || repo.text;
}
var defaultInitialNewDept = '';
$("#departmen").select2({
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
        processResults: function (data, params) {
            params.page = params.page || 1;

            var newData = $.map(data.items, function (obj) {
                return {
                    id: obj.nmdept,     // ini yang jadi value di select2
                    text: obj.nmdept,   // ini yang ditampilkan
                    kddept: obj.kddept  // tetap simpan kode, bisa diakses manual
                };
            });

            return {
                results: newData,
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

/* FOR INPUT FUNCTION */

$('#formInputInternet').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        idbarang: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmbarang: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        unit: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idbarcode: {
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


function saveInputInternet() {
    let typeform = $('[name="type"]').val(); // Ambil tipe form (INPUT / UPDATE)

    Swal.fire({
        title: 'Warning..!!!',
        text: `Form Internet akan ${typeform === 'INPUT' ? 'dibuat' : 'diupdate'}, Anda yakin dengan data yang diberikan?`,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
    }).then((result) => {
        if (result.isConfirmed) {
            // Field wajib diisi
            var fields = {
                "Type": $('[name="type"]').val(),
                "Document Number": $('[name="docno"]').val(),
                "Nama Pemohon": $('[name="nmpemohon"]').val(),
                "Departemen": $('[name="departmen"]').val(),
                "Jabatan": $('[name="jabatan"]').val(),
                // "Jenis Akses": $('[name="jnsakses"]').val(),
                "Keperluan": $('[name="keperluan"]').val(),
                "Tanggal Dokumen": $('[name="docdate"]').val(),
                // "Tanggal Expired": $('[name="expdate"]').val()
            };

            
            // Jika sifatakses bukan "RUTIN", maka expdate mandatory
            if ($('[name="sifatakses"]').val() !== "RUTIN") {
                fields["Tanggal Expired"] = $('[name="expdate"]').val();
            }

            // Cek field kosong
            for (var key in fields) {
                if (!fields[key] || fields[key].trim() === "") {
                    Swal.fire({
                        icon: "error",
                        title: "Validation Error",
                        text: key + " tidak boleh kosong!",
                    });
                    return false;
                }
            }

            // Siapkan body data
            var fillData = {
                'success': true,
                'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                'message': '',
                'body': {
                    type: $('[name="type"]').val(),
                    docno: $('[name="docno"]').val(),
                    nmpemohon: $('[name="nmpemohon"]').val(),
                    deptpic: $('[name="deptpic"]').val(),
                    jabatan: $('[name="jabatan"]').val(),
                    docdate: $('[name="docdate"]').val(),
                    departmen: $('[name="departmen"]').val(),
                    osticket: $('[name="osticket"]').val(),
                    // jnsakses: $('[name="jnsakses"]').val(),
                    aksesUser: $('[name="aksesUser"]').is(':checked'),
                    aksesInternet: $('[name="aksesInternet"]').is(':checked'),
                    aksesEmail: $('[name="aksesEmail"]').is(':checked'),
                    aksesApp: $('[name="aksesApp"]').is(':checked'),
                    aksesOther: $('[name="aksesOther"]').is(':checked'),


                    emailField: $('[name="emailField"]').val(),
                    appField: $('[name="appField"]').val(),
                    otherField: $('[name="otherField"]').val(),
                    keperluan: $('[name="keperluan"]').val(),
                    sifatakses: $('[name="sifatakses"]').val(),
                    waktuakses: $('[name="waktuakses"]').val(),
                    tertentuField: $('[name="tertentuField"]').val(),
                    otherWaktuField: $('[name="otherWaktuField"]').val(),
                    expdate: $('[name="expdate"]').val(),
                    perangkatregist: $('[name="perangkatregist"]').val(),
                    picakun: $('[name="picakun"]').val(),
                    nmpic: $('[name="nmpic"]').val(),
                    jabatanpic: $('[name="jabatanpic"]').val(),
                    departemenpic: $('[name="departemenpic"]').val(),
                    description: $('[name="description"]').val()
                }
            };

            // Siapkan FormData untuk file + JSON
            var formData = new FormData();
            // let fileInput = $('[name="filedoc"]')[0];
            // if (fileInput && fileInput.files.length > 0) {
            //     formData.append('filedoc', fileInput.files[0]);
            // }
            formData.append('data', JSON.stringify(fillData));

            // Kirim AJAX
            $.ajax({
                url: HOST_URL + 'form/pa/saveDataInternet',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (datax) {
                    if (datax.status) {
                        Swal.fire({
                            title: 'Berhasil...!!!',
                            text: datax.messages,
                            backdrop: true,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            confirmButtonText: `Ok`,
                            icon: 'success',
                        }).then(() => {
                            window.location.replace(HOST_URL + 'form/pa/internet');
                        });
                    } else {
                        Swal.fire({
                            title: 'Galat...!!!',
                            text: datax.messages,
                            icon: 'error',
                            backdrop: true
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: 'Galat...!!!',
                        text: 'Terjadi kesalahan saat mengirim data.',
                        icon: 'error'
                    });
                }
            });
        }
    });
}



function editInternet(e){
    Swal.fire({
        title: 'Confirm..!!!',
        text: 'Edit this data? ' + e,
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
            window.location.replace(HOST_URL + 'form/pa/edit_internet' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}



function detailInternet(e){
    Swal.fire({
        title: 'Confirm..!!!',
        text: 'View this detail data? ' + e,
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
            window.location.replace(HOST_URL + 'form/pa/detail_internet' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}



function setToCancel(docno) {
    Swal.fire({
        title: 'Batal Pengajuan?',
        text: "Apakah anda yakin akan membatalkan pengajuan?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/form/pa/updateStatusInternet',
                type: 'POST',
                data: { docno: docno, status: 'C' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Pengajuan berhasil dibatalkan'
                        }).then(() => {
                            reload_table()
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
                }
            });
        }
    });
}



function setToDiizinkan(docno) {
    Swal.fire({
        title: 'Set status menjadi Diizinkan?',
        text: "Status dokumen akan diubah menjadi Diizinkan (A)",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/form/pa/updateStatusInternet',
                type: 'POST',
                data: { docno: docno, status: 'A' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi Diizinkan'
                        }).then(() => {
                            reload_table()
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
                }
            });
        }
    });
}

function setToTidakDiizinkan(docno) {
    Swal.fire({
        title: 'Set status menjadi Tidak Diizinkan?',
        text: "Status dokumen akan diubah menjadi Tidak Diizinkan (R)",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/form/pa/updateStatusInternet',
                type: 'POST',
                data: { docno: docno, status: 'R' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi Tidak Diizinkan'
                        }).then(() => {
                            reload_table()
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
                }
            });
        }
    });
}

$(document).ready(function() {
    $('#ketentuanAksesModal').modal({
        backdrop: 'static',   // klik luar modal tidak menutup
        keyboard: false       // tombol Esc tidak menutup
    });
    $('#ketentuanAksesModal').modal('show');

    tableInternet();
    $('#checkboxnik').change(function() {
        // this will contain a reference to the checkbox
        if (this.checked) {
            var valnik = $('#nik').val();
            // the checkbox is now checked
            //alert('Checked');
            $('#username').prop('readonly', true);
            $('#username').val(valnik);
        } else {
            // the checkbox is now no longer checked
            //alert('Un Checked');
            $('#username').prop('readonly', false);
            $('#username').val('');
        }
    });
    $('#nik').change(function() {
        if ($('#checkboxnik').is(':checked')){
            var valnik = $(this).val();
            $('#username').val(valnik);
        }
    });
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);
    if ($('[name="type"]').val() === 'UPDATE') {
        documentReadable();
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        $('#btnSave').hide();
        documentReadable();

        // documentReadableDetail();
    }


});