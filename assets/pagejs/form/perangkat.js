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
function tablePerangkat(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tperangkat');
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
                "url": HOST_URL + 'form/pa/list_perangkat',
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
    var table = $('#tperangkat');
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


function toggleAkses(val) {
    var tujuanField = document.getElementById('tujuanakses');

    if(val == 'YA'){
        document.getElementById('fieldtujuan').style.display = 'block';
    } else {
        document.getElementById('fieldtujuan').style.display = 'none';
        tujuanField.value = '';
    }

}

function toggleMasuk(val) {
    var masukField = document.getElementById('alasantidakmasuk');

    if(val == 'TIDAK'){
        document.getElementById('fieldMasuk').style.display = 'block';
    } else {
        document.getElementById('fieldMasuk').style.display = 'none';
        masukField.value = '';
    }

}

function toggleKeluar(val) {
    var keluarField = document.getElementById('alasantidakkeluar');

    if(val == 'TIDAK'){
        document.getElementById('fieldKeluar').style.display = 'block';
    } else {
        document.getElementById('fieldKeluar').style.display = 'none';
        keluarField.value = '';
    }

}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tperangkat');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tperangkat');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
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
        url: HOST_URL + 'form/pa/showDetailPerangkat' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            var dt = json.dataTables.items[0]; // data pertama

            // Isi semua field dengan trim
            $('[name="docno"]').val($.trim(dt.docno));
            $('[name="nmpemohon"]').val($.trim(dt.nmpemohon));
            $('[name="instansi"]').val($.trim(dt.instansi));
            $('[name="jabatan"]').val($.trim(dt.jabatan));
            $('[name="docdate"]').val($.trim(dt.docdate));
            $('[name="telepon"]').val($.trim(dt.telepon));
            $('[name="idsticker"]').val($.trim(dt.idsticker));

            $('[name="jnsperangkat"]').val($.trim(dt.jnsperangkat)).trigger('change');
            $('[name="otherField"]').val($.trim(dt.otherfield));
            $('[name="merk"]').val($.trim(dt.merk));
            $('[name="idperangkat"]').val($.trim(dt.idperangkat));
            $('[name="dtllain"]').val($.trim(dt.dtllain));
            $('[name="tujuan"]').val($.trim(dt.tujuan));
            $('[name="periodemulai"]').val($.trim(dt.periodemulai));
            $('[name="periodeakhir"]').val($.trim(dt.periodeakhir));
            $('[name="lokasi"]').val($.trim(dt.lokasi)).trigger('change');
            // $('[name="lokasi"]').val($.trim(dt.lokasi));
            $('[name="picjts"]').val($.trim(dt.picjts));
            $('[name="aksesinternet"]').val($.trim(dt.aksesinternet)).trigger('change');
            $('[name="tujuanakses"]').val($.trim(dt.tujuanakses));
            $('[name="ipmac"]').val($.trim(dt.ipmac));
            $('[name="wifi"]').val($.trim(dt.wifi));
            $('[name="ijinmasuk"]').val(
                dt.ijinmasuk === null
                    ? null
                    : (dt.ijinmasuk ? "YA" : "TIDAK")
            ).trigger('change');

            $('[name="ijinkeluar"]').val(
                dt.ijinkeluar === null
                    ? null
                    : (dt.ijinkeluar ? "YA" : "TIDAK")
            ).trigger('change');
            $('[name="alasantidakmasuk"]').val($.trim(dt.alasantidakmasuk));
            $('[name="alasantidakkeluar"]').val($.trim(dt.alasantidakkeluar));



            $('[name="keterangan"]').val($.trim(dt.description));

            var isDetail = $('[name="type"]').val() === 'DETAIL';
            if (isDetail) {
                // disable semua input, textarea, select, dan checkbox/radio
                $('input, textarea, select').prop('disabled', true);
            }

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

$('#formInputPerangkat').bootstrapValidator({
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


function saveInputPerangkat() {
    let typeform = $('[name="type"]').val(); // Ambil tipe form (INPUT / UPDATE)

    Swal.fire({
        title: 'Warning..!!!',
        text: `Form Perangkat akan ${typeform === 'INPUT' ? 'dibuat' : 'diupdate'}, Anda yakin dengan data yang diberikan?`,
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
                "Instansi": $('[name="instansi"]').val(),
                "Jabatan": $('[name="jabatan"]').val(),
                "Jenis Perangkat": $('[name="jnsperangkat"]').val(),
                "Merek & Tipe": $('[name="merk"]').val(),
                "Serial Number dan/atau ID Perangkat": $('[name="idperangkat"]').val(),
                "Tujuan Penggunaan": $('[name="tujuan"]').val(),
                "Periode Mulai": $('[name="periodemulai"]').val(),
                "Periode Selesai": $('[name="periodeakhir"]').val(),
                "Lokasi": $('[name="lokasi"]').val(),
                "PIC (JTS": $('[name="picjts"]').val(),
                "Akses Internet": $('[name="aksesinternet"]').val(),

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

            // Siapkan body data
            var fillData = {
                'success': true,
                'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                'message': '',
                'body': {
                    type: $('[name="type"]').val(),
                    docno: $('[name="docno"]').val(),
                    nmpemohon: $('[name="nmpemohon"]').val(),
                    instansi: $('[name="instansi"]').val(),
                    jabatan: $('[name="jabatan"]').val(),
                    docdate: $('[name="docdate"]').val(),
                    telepon: $('[name="telepon"]').val(),
                    idsticker: $('[name="idsticker"]').val(),
                    jnsperangkat: $('[name="jnsperangkat"]').val(),
                    merk: $('[name="merk"]').val(),
                    idperangkat: $('[name="idperangkat"]').val(),
                    dtllain: $('[name="dtllain"]').val(),
                    tujuan: $('[name="tujuan"]').val(),
                    periodemulai: $('[name="periodemulai"]').val(),
                    periodeakhir: $('[name="periodeakhir"]').val(),
                    lokasi: $('[name="lokasi"]').val(),
                    picjts: $('[name="picjts"]').val(),
                    aksesinternet: $('[name="aksesinternet"]').val(),
                    ipmac: $('[name="ipmac"]').val(),
                    wifi: $('[name="wifi"]').val(),
                    description: $('[name="keterangan"]').val(),

                    ijinmasuk: $('[name="ijinmasuk"]').val(),
                    alasantidakmasuk: $('[name="alasantidakmasuk"]').val(),
                    ijinkeluar: $('[name="ijinkeluar"]').val(),
                    alasantidakkeluar: $('[name="alasantidakkeluar"]').val(),


                    //mandatory case tertentu
                    tujuanakses: $('[name="tujuanakses"]').val(),
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
                url: HOST_URL + 'form/pa/saveDataPerangkat',
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
                            window.location.replace(HOST_URL + 'form/pa');
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
            window.location.replace(HOST_URL + 'form/pa/edit_perangkat' + '/?var=' + e)
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
            window.location.replace(HOST_URL + 'form/pa/detail_perangkat' + '/?var=' + e)
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
                url: HOST_URL + '/form/pa/updateStatus',
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
                url: HOST_URL + '/form/pa/updateStatus',
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
                url: HOST_URL + '/form/pa/updateStatus',
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
    tablePerangkat();
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