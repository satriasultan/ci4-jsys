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
function tablePerangkatKeluar(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tkeluar');
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
                "url": HOST_URL + 'form/pa/list_perangkatkeluar',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.status_filter = $('#status_filter').val(); //A,P,S,ALL
                    // data.idgroup = $('#idgroup').val();
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
    var table = $('#tkeluar');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}



function togglePerangkat(val) {
    var otherField = document.getElementById('otherField');

    if (val === 'OTHER') {
        document.getElementById('fieldOther').style.display = 'block';
    } else {
        document.getElementById('fieldOther').style.display = 'none';
        otherField.value = '';
    }
}


function togglePeriode(val) {
    var mulaiField = document.getElementById('periodemulai');
    var akhirField = document.getElementById('periodeakhir');

    if(val == 'TERTENTU'){
        document.getElementById('fieldPeriode').style.display = 'block';
    } else {
        document.getElementById('fieldPeriode').style.display = 'none';
        mulaiField.value = '';
        akhirField.value = '';
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

function togglePembaruanIzin(val) {
    var terlambatField = document.getElementById('alasanterlambat');

    if(val == 'YA'){
        document.getElementById('fieldPembaruan').style.display = 'block';
    } else {
        document.getElementById('fieldPembaruan').style.display = 'none';
        terlambatField.value = '';
    }

}



$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tkeluar');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tkeluar');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});


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

//EDIT ITEM
function documentReadable(){
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: false //Display loader!
    // });
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'form/pa/showDetailPerangkatKeluar' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            var dt = json.dataTables.items[0]; // data pertama

            // Isi semua field dengan trim
            $('[name="docno"]').val($.trim(dt.docno));
            $('[name="nmpemohon"]').val($.trim(dt.nmpemohon));
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

            // $('[name="jabatan"]').val($.trim(dt.jabatan));
            $('[name="docdate"]').val($.trim(dt.docdate));
            $('[name="osticket"]').val($.trim(dt.osticket));
            $('[name="jnsform"]').val($.trim(dt.jnsform)).trigger('change');
            $('[name="idsticker"]').val($.trim(dt.idsticker));

            $('[name="jnsperangkat"]').val($.trim(dt.jnsperangkat)).trigger('change');
            $('[name="otherField"]').val($.trim(dt.otherfield));
            $('[name="merk"]').val($.trim(dt.merk));
            $('[name="noasset"]').val($.trim(dt.noasset));
            $('[name="qty"]').val($.trim(dt.qty));
            $('[name="keperluan"]').val($.trim(dt.keperluan));
            $('[name="tujuan"]').val($.trim(dt.tujuan));
            $('[name="periode"]').val($.trim(dt.periode)).trigger('change');
            $('[name="periodemulai"]').val($.trim(dt.periodemulai));
            $('[name="periodeakhir"]').val($.trim(dt.periodeakhir));
            $('[name="picakun"]').val($.trim(dt.picakun)).trigger('change');
            // $('[name="lokasi"]').val($.trim(dt.lokasi));
            // $('[name="picjts"]').val($.trim(dt.picjts));
            $('[name="nmpic"]').val($.trim(dt.nmpic));
            $('[name="jabatanpic"]').val($.trim(dt.jabatanpic));
            $('[name="departemenpic"]').val($.trim(dt.departemenpic));

            $('[name="tglpemeriksaan"]').val($.trim(dt.tglpemeriksaan));
            $('[name="pembaruanizin"]').val($.trim(dt.pembaruanizin)).trigger('change');
            $('[name="dataperangkat"]').val($.trim(dt.dataperangkat));
            if (dt.enkripsidata === "t") {
                $('[name="enkripsidata"]').prop("checked", true);
            } else {
                $('[name="enkripsidata"]').prop("checked", false);
            }            $('[name="kondisi"]').val($.trim(dt.kondisi));
            $('[name="tglpenyerahan"]').val($.trim(dt.tglpenyerahan));
            $('[name="tglpengembalian"]').val($.trim(dt.tglpengembalian));
            $('[name="pembaruanizin"]').val($.trim(dt.pembaruanizin)).trigger('change');

            $('[name="alasanterlambat"]').val($.trim(dt.alasanterlambat));
            $('[name="keteranganlain"]').val($.trim(dt.keteranganlain));

            var isDetail = $('[name="type"]').val() === 'DETAIL';
            if (isDetail) {
                // disable semua input, textarea, select, dan checkbox/radio
                $('input, textarea, select').prop('disabled', true);
            }


            // $('[name="ijinmasuk"]').val(
            //     dt.ijinmasuk === null
            //         ? null
            //         : (dt.ijinmasuk ? "YA" : "TIDAK")
            // ).trigger('change');

            // $('[name="ijinkeluar"]').val(
            //     dt.ijinkeluar === null
            //         ? null
            //         : (dt.ijinkeluar ? "YA" : "TIDAK")
            // ).trigger('change');
            // $('[name="alasantidakmasuk"]').val($.trim(dt.alasantidakmasuk));
            // $('[name="alasantidakkeluar"]').val($.trim(dt.alasantidakkeluar));



            // $('[name="description"]').val($.trim(dt.description));

            // Kalau mau readonly pada field tertentu:
            $('[name="docno"]').prop('readonly', true);
        },
        complete: function(){
            $("#loadMe").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });

    $("#loadMe").modal("hide");
}
/* FOR INPUT FUNCTION */

$('#formInputPerangkatKeluar').bootstrapValidator({
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


function saveInputPerangkatKeluar() {
    let typeform = $('[name="type"]').val(); // Ambil tipe form (INPUT / UPDATE)

    Swal.fire({
        title: 'Warning..!!!',
        text: `Form Perangkat Keluar akan ${typeform === 'INPUT' ? 'dibuat' : 'diupdate'}, Anda yakin dengan data yang diberikan?`,
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
                "Tanggal Permintaan": $('[name="docdate"]').val(),
                "Nama Pemohon": $('[name="nmpemohon"]').val(),
                "Departemen": $('[name="departmen"]').val(),
                "Jabatan": $('[name="jabatan"]').val(),
                "Jenis Form": $('[name="jnsform"]').val(),
                "Jenis Perangkat": $('[name="jnsperangkat"]').val(),
                "Merek & Tipe": $('[name="merk"]').val(),
                "No Asset/ID Sticker ": $('[name="noasset"]').val(),
                "Quantity": $('[name="qty"]').val(),
                "Keperluan dan Alasan Membawa Perangkat Keluar": $('[name="keperluan"]').val(),
                "Tujuan (Tempat)": $('[name="tujuan"]').val(),
                "Periode": $('[name="periode"]').val(),
                "Pengguna Perangkat": $('[name="picakun"]').val(),
            };

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

            
            // Validasi tambahan jika Periode = TERTENTU
            if (fields["Periode"].trim().toUpperCase() === "TERTENTU") {
                var periodemulai = $('[name="periodemulai"]').val();
                var periodeakhir = $('[name="periodeakhir"]').val();

                if (!periodemulai || periodemulai.trim() === "") {
                    Swal.fire({
                        icon: "error",
                        title: "Validation Error",
                        text: "Periode Mulai wajib diisi jika Periode = TERTENTU!",
                    });
                    return false;
                }

                if (!periodeakhir || periodeakhir.trim() === "") {
                    Swal.fire({
                        icon: "error",
                        title: "Validation Error",
                        text: "Periode Akhir wajib diisi jika Periode = TERTENTU!",
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
                    jabatan: $('[name="jabatan"]').val(),
                    departmen: $('[name="departmen"]').val(),
                    docdate: $('[name="docdate"]').val(),
                    jnsform: $('[name="jnsform"]').val(),
                    osticket: $('[name="osticket"]').val(),
                    jnsperangkat: $('[name="jnsperangkat"]').val(),
                    merk: $('[name="merk"]').val(),
                    noasset: $('[name="noasset"]').val(),
                    qty: $('[name="qty"]').val(),
                    keperluan: $('[name="keperluan"]').val(),
                    tujuan: $('[name="tujuan"]').val(),
                    periode: $('[name="periode"]').val(),
                    periodemulai: $('[name="periodemulai"]').val(),
                    periodeakhir: $('[name="periodeakhir"]').val(),
                    picakun: $('[name="picakun"]').val(),
                    nmpic: $('[name="nmpic"]').val(),
                    jabatanpic: $('[name="jabatanpic"]').val(),
                    departemenpic: $('[name="departemenpic"]').val(),
                    // description: $('[name="description"]').val(),

                    tglpemeriksaan: $('[name="tglpemeriksaan"]').val(),
                    dataperangkat: $('[name="dataperangkat"]').val(),
                    enkripsidata : $('[name ="enkripsidata"]').prop("checked") ? 1 : 0,
                    kondisi: $('[name="kondisi"]').val(),
                    tglpenyerahan: $('[name="tglpenyerahan"]').val(),

                    tglpengembalian: $('[name="tglpengembalian"]').val(),
                    pembaruanizin: $('[name="pembaruanizin"]').val(),
                    alasanterlambat: $('[name="alasanterlambat"]').val(),
                    keteranganlain: $('[name="keteranganlain"]').val(),
                    // kondisi: $('[name="kondisi"]').val(),
                    // kondisi: $('[name="kondisi"]').val(),


                    //mandatory case tertentu
                    otherField: $('[name="otherField"]').val()
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
                url: HOST_URL + 'form/pa/saveDataPerangkatKeluar',
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
                            window.location.replace(HOST_URL + 'form/pa/laptop');
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


function editPerangkat(e){
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
            window.location.replace(HOST_URL + 'form/pa/edit_perangkatkeluar' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}


function detailPerangkat(e){
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
            window.location.replace(HOST_URL + 'form/pa/detail_perangkatkeluar' + '/?var=' + e)
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
                url: HOST_URL + '/form/pa/updateStatusPK',
                type: 'POST',
                data: { docno: docno, status: 'R' },
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

function setToOpen(docno) {
    Swal.fire({
        title: 'Set status menjadi OPEN?',
        text: "Status dokumen akan diubah menjadi OPEN (O)",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/form/pa/updateStatusPK',
                type: 'POST',
                data: { docno: docno, status: 'O' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi OPEN'
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

function setToClosed(docno) {
    Swal.fire({
        title: 'Set status menjadi CLOSE?',
        text: "Status dokumen akan diubah menjadi CLOSE (C)",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/form/pa/updateStatusPK',
                type: 'POST',
                data: { docno: docno, status: 'C' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi CLOSE'
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


function setPerizinanMasuk(docno) {
    $('#docnoMasuk').val(docno);
    $('#ijinmasuk').val('');
    $('#alasantidakmasuk').val('');
    $('#alasanTidakMasukGroup').addClass('d-none');
    $('#modalIzinMasuk').modal('show');
}

function setPerizinanKeluar(docno) {
    $('#docnoKeluar').val(docno);
    $('#ijinkeluar').val('');
    $('#alasantidakkeluar').val('');
    $('#alasanTidakKeluarGroup').addClass('d-none');
    $('#modalIzinKeluar').modal('show');
}

// Tampilkan alasan jika TIDAK dipilih
$('#ijinmasuk').on('change', function(){
    if($(this).val() === 'false'){
        $('#alasanTidakMasukGroup').removeClass('d-none');
    } else {
        $('#alasanTidakMasukGroup').addClass('d-none');
    }
});

$('#ijinkeluar').on('change', function(){
    if($(this).val() === 'false'){
        $('#alasanTidakKeluarGroup').removeClass('d-none');
    } else {
        $('#alasanTidakKeluarGroup').addClass('d-none');
    }
});

function submitIzinMasuk() {
    let formData = $('#formIzinMasuk').serialize();
    var docno = $('#docnoMasuk').val(); // ambil dari input field
    var ijinmasuk = $('#ijinmasuk').is(':checked'); // checkbox true/false
    var alasan = $('#alasantidakmasuk').val(); // ambil dari textarea
    $.ajax({
        url: HOST_URL + '/form/pa/updateIzinMasuk',
        type: 'POST',
        data: {
            docno: docno,
            ijinmasuk: ijinmasuk,
            alasantidakmasuk: alasan
        },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Izin Masuk berhasil diupdate'
            });
            $('#modalIzinMasuk').modal('hide');
            reload_table()
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan: ' + error
            });
        }
    });
}

function submitIzinKeluar() {
    // let formData = $('#formIzinKeluar').serialize();
    var docno = $('#docno').val(); // ambil dari input field
    var ijinkeluar = $('#ijinkeluar').is(':checked'); // checkbox true/false
    var alasan = $('#alasantidakekluar').val(); // ambil dari textarea

    $.ajax({
        url: HOST_URL + '/form/pa/updateIzinKeluar',
        type: 'POST',
        data: {
            docno: docno,
            ijinkeluar: ijinkeluar,
            alasantidakkeluar: alasan
        },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Izin Keluar berhasil diupdate'
            });
            $('#modalIzinKeluar').modal('hide');
            reload_table()
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan: ' + error
            });
        }
    });
}




$(document).ready(function() {
    $('#ketentuanModal').modal({
        backdrop: 'static',   // klik luar modal tidak menutup
        keyboard: false       // tombol Esc tidak menutup
    });
    $('#ketentuanModal').modal('show');
    tablePerangkatKeluar();
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
    }


});