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
function tableSupplier(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tsuppliers');
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
                "url": HOST_URL + 'master/data/list_suppliers',
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
    var table = $('#tsuppliers');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}




$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tsuppliers');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tsuppliers');
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
        url: HOST_URL + 'master/data/showDetailSupplier' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            var dt = json.dataTables.items[0]; // data pertama

            // Isi semua field dengan trim
            $('[name="kdsupplier"]').val($.trim(dt.kdsupplier));
            $('[name="nmsupplier"]').val($.trim(dt.nmsupplier).toUpperCase());
            $('[name="alamat"]').val($.trim(dt.alamat).toUpperCase());
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/geolocation/list_kota' + '?var=' + dt.idkota.trim(),
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].namakotakab, datax.items[0].kodekotakab, true, true);
                $('[name="idkota"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idkota"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });            
            $('[name="phone"]').val($.trim(dt.phone));
            // $('[name="idsticker"]').val($.trim(dt.idsticker).toUpperCase()); // dikomentari seperti di PHP
            $('[name="fax"]').val($.trim(dt.fax).toUpperCase());
            $('[name="email"]').val($.trim(dt.email).toLowerCase());
            $('[name="npwp"]').val($.trim(dt.npwp).toUpperCase());
            $('[name="npkp"]').val($.trim(dt.npkp).toUpperCase());
            $('[name="jthtempo"]').val((dt.jthtempo));
            $('[name="plafon"]').val((dt.plafon));
            $('[name="interngroup"]').prop(
                'checked',
                $.trim((dt.interngroup || '')).toUpperCase() === 'YES'
            );

            $('[name="blacklist"]').prop(
                'checked',
                $.trim((dt.blacklist || '')).toUpperCase() === 'YES'
            );
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_market' + '?var=' + dt.idmarket.trim(),
                dataType: 'json',
                delay: 100,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmmarket, datax.items[0].idmarket, true, true);
                $('[name="idmarket"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idmarket"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });    
            // $('[name="idmarket"]').val($.trim(dt.idmarket).toUpperCase());
            $('[name="cp"]').val($.trim(dt.cp).toUpperCase());
            $('[name="jabatan"]').val($.trim(dt.jabatan).toUpperCase());
            $('[name="keterangan"]').val($.trim(dt.keterangan).toUpperCase());
            $('[name="interngroup"]').val($.trim(dt.interngroup).toUpperCase());
            $('[name="blacklist"]').val($.trim(dt.blacklist).toUpperCase());

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

$('#formInputSupplier').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        kdsupplier: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmsupplier: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        alamat: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        phone: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        email: {
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


function saveInputSupplier() {
    let typeform = $('[name="type"]').val(); // Ambil tipe form (INPUT / UPDATE)

    Swal.fire({
        title: 'Warning..!!!',
        text: `Data Supplier akan ${typeform === 'INPUT' ? 'dibuat' : 'diupdate'}, Anda yakin dengan data yang diberikan?`,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
    }).then((result) => {
        if (result.isConfirmed) {
            
            var fields = {
                "Kode Supplier": $('[name="kdsupplier"]').val(),
                "Nama Supplier": $('[name="nmsupplier"]').val(),
                "Alamat": $('[name="alamat"]').val(),
                // "Kota": $('[name="idkota"]').val(),
                "No. Telepon": $('[name="phone"]').val(),
                "Email": $('[name="email"]').val(),
                // "Fax": $('[name="fax"]').val(),
                // "NPWP": $('[name="npwp"]').val(),
                // "NPKP": $('[name="npkp"]').val(),
                // "Plafon": $('[name="plafon"]').val(),
                // "Jatuh Tempo": $('[name="jthtempo"]').val(),
                // "Market": $('[name="idmarket"]').val(),
                // "Contact Person": $('[name="cp"]').val(),
                // "Jabatan": $('[name="jabatan"]').val()
            };


            for (var key in fields) {
                if (!fields[key] || fields[key].trim() === "") {
                    Swal.fire({
                        icon: "error",
                        title: "Validation Error",
                        text: key + " tidak boleh kosong!"
                    });
                    return false;
                }
            }

            var email = $('[name="email"]').val();
            if (email && !/^\S+@\S+\.\S+$/.test(email)) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Format Email tidak valid"
                });
                return false;
            }



            var fillData = {
                success: true,
                key: '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
                message: '',
                body: {
                    type: $('[name="type"]').val(),
                    id: $('[name="id"]').val(),

                    kdsupplier: $('[name="kdsupplier"]').val(),
                    nmsupplier: $('[name="nmsupplier"]').val(),
                    alamat: $('[name="alamat"]').val(),
                    idkota: $('[name="idkota"]').val(),

                    phone: $('[name="phone"]').val(),
                    email: $('[name="email"]').val(),
                    fax: $('[name="fax"]').val(),

                    npwp: $('[name="npwp"]').val(),
                    npkp: $('[name="npkp"]').val(),

                    plafon: $('[name="plafon"]').val(),
                    jthtempo: $('[name="jthtempo"]').val(),
                    idmarket: $('[name="idmarket"]').val(),

                    cp: $('[name="cp"]').val(),
                    jabatan: $('[name="jabatan"]').val(),

                    keterangan: $('[name="keterangan"]').val(),
                    
                    interngroup: $('#interngroup').is(':checked') ? 'YES' : 'NO',
                    blacklist: $('#blacklist').is(':checked') ? 'YES' : 'NO'
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
                url: HOST_URL + 'master/data/saveDataSupplier',
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
                            window.location.replace(HOST_URL + 'master/data/supplier');
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


$('#email').on('input', function () {
    const email = this.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email.length > 0 && !emailPattern.test(email)) {
        $(this).addClass('is-invalid');
    } else {
        $(this).removeClass('is-invalid');
    }
});




function editSupplier(e){
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
            window.location.replace(HOST_URL + 'master/data/edit_suppliers' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}



function detailSupplier(e){
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
            window.location.replace(HOST_URL + 'master/data/detail_suppliers' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}



function hapusSupplier(e) {
    Swal.fire({
        title: 'Confirm..!!!',
        text: 'Remove this data? ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: 'Ok',
        denyButtonText: 'Cancel',
        icon: 'question'
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: HOST_URL + 'master/data/hapus_suppliers',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: e
                },
                success: function (res) {

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message
                        }).then(() => {
                            // reload datatable / table
                            reload_table(); 
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }

                },
                error: function () {
                    Swal.fire(
                        'Error',
                        'Tidak dapat terhubung ke server',
                        'error'
                    );
                }
            });

        }

    });
}




// function setToCancel(docno) {
//     Swal.fire({
//         title: 'Batal Pengajuan?',
//         text: "Apakah anda yakin akan membatalkan pengajuan?",
//         icon: 'question',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Ya, ubah'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             $.ajax({
//                 url: HOST_URL + '/master/data/updateStatus',
//                 type: 'POST',
//                 data: { docno: docno, status: 'R' },
//                 dataType: 'json',
//                 success: function(res) {
//                     if (res.success) {
//                         Swal.fire({
//                             icon: 'success',
//                             title: 'Berhasil',
//                             text: 'Pengajuan berhasil dibatalkan'
//                         }).then(() => {
//                             reload_table()
//                         });
//                     } else {
//                         Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
//                     }
//                 },
//                 error: function() {
//                     Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
//                 }
//             });
//         }
//     });
// }


// kotakab
function formatKota(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kodekotakab +"   <i class='fa fa-circle-o'></i>   "+ repo.namakotakab +"</div>";
    return markup;
}
function formatKotaSelection(repo) {
    return repo.namakotakab || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#idkota").select2({
    placeholder: "Ketik/Pilih Kota",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kota',
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
    templateResult: formatKota, // omitted for brevity, see the source of this page
    templateSelection: formatKotaSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




function formatMarket(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idmarket +"   <i class='fa fa-circle-o'></i>   "+ repo.nmmarket +"</div>";
    return markup;
}
function formatMarketSelection(repo) {
    return repo.nmmarket || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#idmarket").select2({
    placeholder: "Ketik/Pilih Market",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_market',
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
    templateResult: formatMarket, // omitted for brevity, see the source of this page
    templateSelection: formatMarketSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



$(document).ready(function() {
    // $('#ketentuanModal').modal({
    //     backdrop: 'static',   // klik luar modal tidak menutup
    //     keyboard: false       // tombol Esc tidak menutup
    // });
    // $('#ketentuanModal').modal('show');
    tableSupplier();
    // $('#checkboxnik').change(function() {
    //     // this will contain a reference to the checkbox
    //     if (this.checked) {
    //         var valnik = $('#nik').val();
    //         // the checkbox is now checked
    //         //alert('Checked');
    //         $('#username').prop('readonly', true);
    //         $('#username').val(valnik);
    //     } else {
    //         // the checkbox is now no longer checked
    //         //alert('Un Checked');
    //         $('#username').prop('readonly', false);
    //         $('#username').val('');
    //     }
    // });
    // $('#nik').change(function() {
    //     if ($('#checkboxnik').is(':checked')){
    //         var valnik = $(this).val();
    //         $('#username').val(valnik);
    //     }
    // });
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