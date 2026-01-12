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
function tableRequest(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#treq');
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
                "url": HOST_URL + 'form/pa/list_pengadaan_hwsw',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
                    data.idgroup = $('#idgroup').val();
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
    var table = $('#treq');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
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
    var table = $('#treq');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#treq');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});


function documentReadable() {
    // var modal = new bootstrap.Modal(document.getElementById('modalDetailReq'));
    // modal.show();

    // // Kosongkan isi dan tampilkan loader
    // $("#detailContent").html("");
    // $("#detailLoader").removeClass("d-none");
    $.ajax({
        type: "GET",
        url: HOST_URL + "form/pa/showDetailReq/" + '?var=' + $('[name="id"]').val(),
        dataType: "json",
        dataFilter: function(data) {
            if (data) {
                var json = jQuery.parseJSON(data);
                var dt = json.dataTables.items[0]; // data pertama

                // semua field diisi tapi readonly/disabled
                $('[name="docno"]').val($.trim(dt.docno));
                $('[name="namapic"]').val($.trim(dt.namapic))
                $('[name="docdate"]').val($.trim(dt.docdate))
                $('[name="departemen"]').val($.trim(dt.departemen))
                $('[name="nmbarang"]').val($.trim(dt.nmbarang))
                $('[name="jumlah"]').val($.trim(dt.jumlah))
                $('[name="estimasi"]').val($.trim(dt.estimasi))
                $('[name="jnsbarang"]').val($.trim(dt.jnsbarang))
                $('[name="tujuan"]').val($.trim(dt.tujuan))
                $('[name="frekuensi"]').val($.trim(dt.frekuensi))
                $('[name="jnspengadaan"]').val($.trim(dt.jnspengadaan))
                $('[name="klasifikasi"]').val($.trim(dt.klasifikasi))
                $('[name="spesifikasi"]').val($.trim(dt.spesifikasi))
                $('[name="lampiran"]').val($.trim(dt.lampiran))
                $('[name="setuju"]').val($.trim(dt.setuju))
                $('[name="alasanreject"]').val($.trim(dt.alasanreject))

                // kalau ada lampiran file → tampilkan link / preview
                if ($.trim(dt.lampiran) && $.trim(dt.lampiran) !== "") {
                    let ext = $.trim(dt.lampiran).split('.').pop().toLowerCase();
                    let lampiranUrl = HOST_URL + "/uploads/pa/" + dt.lampiran;
                    if (["jpg", "jpeg", "png"].includes(ext)) {
                        $("#previewLampiran").html('<img src="' + lampiranUrl + '" class="img-fluid rounded shadow">');
                    } else if (ext === "pdf") {
                        $("#previewLampiran").html('<a href="' + lampiranUrl + '" target="_blank">📄 Lihat Lampiran PDF</a>');
                    } else {
                        $("#previewLampiran").html('<a href="' + lampiranUrl + '" target="_blank">🔗 Download Lampiran</a>');
                    }
                } else {
                    $("#previewLampiran").html("<em>Tidak ada lampiran</em>");
                }
                // $("#detailLoader").addClass("d-none");
                $('[name="docno"]').prop('readonly', true);

            }
        },
        complete: function () {
            $("#loadMe").modal("hide");
        },
        error: function(xhr, status, error) {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });
}


$('#formInputReqIT').bootstrapValidator({
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


function saveInputReqIT() {
    let typeform = $('[name="type"]').val(); // Ambil tipe form (INPUT / UPDATE)

    Swal.fire({
        title: 'Warning..!!!',
        text: `Form Pengadaan akan ${typeform === 'INPUT' ? 'dibuat' : 'diupdate'}, Anda yakin dengan data yang diberikan?`,
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
                "Nama PIC": $('[name="namapic"]').val(),
                "Departemen": $('[name="departemen"]').val(),
                "Nama Barang": $('[name="nmbarang"]').val(),
                "Jumlah": $('[name="jumlah"]').val(),

                "Jenis Barang": $('[name="jnsbarang"]').val(),
                "Keperluan": $('[name="tujuan"]').val(),
                "Spesifikasi": $('[name="spesifikasi"]').val(),
                "Klasifikasi": $('[name="klasifikasi"]').val()
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
                    namapic: $('[name="namapic"]').val(),
                    departemen: $('[name="departemen"]').val(),
                    nmbarang: $('[name="nmbarang"]').val(),
                    docdate: $('[name="docdate"]').val(),
                    jumlah: $('[name="jumlah"]').val(),
                    estimasi: $('[name="estimasi"]').val(),
                    jnsbarang: $('[name="jnsbarang"]').val(),
                    tujuan: $('[name="tujuan"]').val(),
                    frekuensi: $('[name="frekuensi"]').val(),
                    jnspengadaan: $('[name="jnspengadaan"]').val(),
                    klasifikasi: $('[name="klasifikasi"]').val(),
                    spesifikasi: $('[name="spesifikasi"]').val(),
                    lampiran: $('[name="lampiran"]').val(),
                    setuju: $('[name="setuju"]').val(),
                    alasanreject: $('[name="alasanreject"]').val()
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
                url: HOST_URL + 'form/pa/saveDataPengadaanHwsw',
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
                            window.location.replace(HOST_URL + 'form/pa/pengadaan_hwsw');
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



function editPengadaanHwSw(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Would be change? ' + e,
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
            window.location.replace(HOST_URL + 'form/pa/edit_pengadaan_hwsw' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}


$(document).ready(function() {
    // $('#ketentuanAksesModal').modal('show');

    tableRequest();
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
        documentReadableDetail();
    }


});