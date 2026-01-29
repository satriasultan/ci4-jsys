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
function tableCustomer(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tcustomer');
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
                "url": HOST_URL + 'master/data/list_customer',
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
    var table = $('#tcustomer');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}




$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tcustomer');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tcustomer');
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
        url: HOST_URL + 'master/data/showDetailCustomer' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            var dt = json.dataTables.items[0]; // data pertama

            var isDetail = $('[name="type"]').val() === 'DETAIL';
            if (isDetail) {
                // disable semua input, textarea, select, dan checkbox/radio
                $('input, textarea, select').prop('disabled', true);
            }

            // Isi semua field dengan trim
            $('[name="kdcustomer"]').val($.trim(dt.kdcustomer));
            $('[name="nmcustomer"]').val($.trim(dt.nmcustomer).toUpperCase());
            // Prefill lokasi kantor
            $.ajax({
                url: HOST_URL + 'api/geolocation/list_provinsi?var=' + dt.provinsi_kantor,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namaprov, res.items[0].kodeprov, true, true);
                $('[name="provinsi_kantor"]').append(option).trigger('change');
                $('[name="provinsi_kantor"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            $.ajax({
                url: HOST_URL + 'api/geolocation/list_kota?var=' + dt.kota_kantor,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namakotakab, res.items[0].kodekotakab, true, true);
                $('[name="kota_kantor"]').append(option).trigger('change');
                $('[name="kota_kantor"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            $.ajax({
                url: HOST_URL + 'api/geolocation/list_kecamatan?var=' + dt.kec_kantor,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namakec, res.items[0].kodekec, true, true);
                $('[name="kec_kantor"]').append(option).trigger('change');
                $('[name="kec_kantor"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            $.ajax({
                url: HOST_URL + 'api/geolocation/list_desa?var=' + dt.kel_kantor,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namakeldesa, res.items[0].kodekeldesa, true, true);
                $('[name="kel_kantor"]').append(option).trigger('change');
                $('[name="kel_kantor"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            // Lokasi pengiriman
            $.ajax({
                url: HOST_URL + 'api/geolocation/list_provinsi?var=' + dt.provinsi_pengiriman,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namaprov, res.items[0].kodeprov, true, true);
                $('[name="provinsi_pengiriman"]').append(option).trigger('change');
                $('[name="provinsi_pengiriman"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            $.ajax({
                url: HOST_URL + 'api/geolocation/list_kota?var=' + dt.kota_pengiriman,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namakotakab, res.items[0].kodekotakab, true, true);
                $('[name="kota_pengiriman"]').append(option).trigger('change');
                $('[name="kota_pengiriman"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            $.ajax({
                url: HOST_URL + 'api/geolocation/list_kecamatan?var=' + dt.kec_pengiriman,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namakec, res.items[0].kodekec, true, true);
                $('[name="kec_pengiriman"]').append(option).trigger('change');
                $('[name="kec_pengiriman"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            $.ajax({
                url: HOST_URL + 'api/geolocation/list_desa?var=' + dt.kel_pengiriman,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namakeldesa, res.items[0].kodekeldesa, true, true);
                $('[name="kel_pengiriman"]').append(option).trigger('change');
                $('[name="kel_pengiriman"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            // Lokasi penagihan
            $.ajax({
                url: HOST_URL + 'api/geolocation/list_provinsi?var=' + dt.provinsi_penagihan,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namaprov, res.items[0].kodeprov, true, true);
                $('[name="provinsi_penagihan"]').append(option).trigger('change');
                $('[name="provinsi_penagihan"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            $.ajax({
                url: HOST_URL + 'api/geolocation/list_kota?var=' + dt.kota_penagihan,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namakotakab, res.items[0].kodekotakab, true, true);
                $('[name="kota_penagihan"]').append(option).trigger('change');
                $('[name="kota_penagihan"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            $.ajax({
                url: HOST_URL + 'api/geolocation/list_kecamatan?var=' + dt.kec_penagihan,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namakec, res.items[0].kodekec, true, true);
                $('[name="kec_penagihan"]').append(option).trigger('change');
                $('[name="kec_penagihan"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            $.ajax({
                url: HOST_URL + 'api/geolocation/list_desa?var=' + dt.kel_penagihan,
                dataType: 'json'
            }).then(function (res) {
                var option = new Option(res.items[0].namakeldesa, res.items[0].kodekeldesa, true, true);
                $('[name="kel_penagihan"]').append(option).trigger('change');
                $('[name="kel_penagihan"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
            });

            // === DATA ALAMAT DARI API (dt) ===
            var alamatKantor = {
                provinsi: dt.provinsi_kantor,
                kota: dt.kota_kantor,
                kec: dt.kec_kantor,
                kel: dt.kel_kantor,
                alamat: dt.alamat_kantor,
                kodepos: dt.kodepos_kantor
            };

            var alamatPengiriman = {
                provinsi: dt.provinsi_pengiriman,
                kota: dt.kota_pengiriman,
                kec: dt.kec_pengiriman,
                kel: dt.kel_pengiriman,
                alamat: dt.alamat_pengiriman,
                kodepos: dt.kodepos_pengiriman
            };

            var alamatPenagihan = {
                provinsi: dt.provinsi_penagihan,
                kota: dt.kota_penagihan,
                kec: dt.kec_penagihan,
                kel: dt.kel_penagihan,
                alamat: dt.alamat_penagihan,
                kodepos: dt.kodepos_penagihan
            };


            // === Pengiriman sama dengan kantor ===
            if (isAlamatSama(alamatKantor, alamatPengiriman)) {
                $('#copy_alamat_pengiriman').prop('checked', true);
            }

            // === Penagihan sama dengan kantor ===
            if (isAlamatSama(alamatKantor, alamatPenagihan)) {
                $('#copy_alamat_penagihan').prop('checked', true);
            }




            $('[name="alamat_kantor"]').val(dt.alamat_kantor);
            $('[name="kodepos_kantor"]').val(dt.kodepos_kantor);
            
            $('[name="alamat_pengiriman"]').val(dt.alamat_pengiriman);
            $('[name="kodepos_pengiriman"]').val(dt.kodepos_pengiriman);
            
            $('[name="alamat_penagihan"]').val(dt.alamat_penagihan);
            $('[name="kodepos_penagihan"]').val(dt.kodepos_penagihan);


            $('[name="chold"]').val($.trim(dt.chold)).trigger('change');
            $('[name="phone"]').val($.trim(dt.phone));
            // $('[name="idsticker"]').val($.trim(dt.idsticker).toUpperCase()); // dikomentari seperti di PHP
            $('[name="fax"]').val($.trim(dt.fax).toUpperCase());
            $('[name="email"]').val($.trim(dt.email).toLowerCase());
            $('[name="npwp"]').val($.trim(dt.npwp).toUpperCase());
            $('[name="npkp"]').val($.trim(dt.npkp).toUpperCase());
            setJtsValue('[name="jthtempo"]', convertToDbNumber(dt.jthtempo));
            setJtsValue('[name="plafon"]', convertToDbNumber(dt.plafon));
            $('[name="cp"]').val($.trim(dt.cp).toUpperCase());
            $('[name="jabatan"]').val($.trim(dt.jabatan).toUpperCase());
            $('[name="keterangan"]').val($.trim(dt.keterangan).toUpperCase());
            $('[name="coa"]').prop(
                'checked',
                $.trim((dt.coa || '')).toUpperCase() === 'YES'
            );

            $('[name="blacklist"]').prop(
                'checked',
                $.trim((dt.blacklist || '')).toUpperCase() === 'YES'
            );
            // =============== market =========================
            if (isValid(dt.idmarket)) {
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_market?var=' + dt.idmarket.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (res.items && res.items.length > 0) {
                        var option = new Option(
                            res.items[0].nmmarket,
                            res.items[0].idmarket,
                            true,
                            true
                        );
                        $('[name="idmarket"]').append(option).trigger('change');
                    }
                });
            }

            $('[name="namanpwp"]').val($.trim(dt.namanpwp).toUpperCase());
            $('[name="alamatnpwp"]').val($.trim(dt.alamatnpwp).toUpperCase());

            if (isValid(dt.idkotanpwp)) {
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/geolocation/list_kota?var=' + dt.idkotanpwp.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (res.items && res.items.length > 0) {
                        var option = new Option(
                            res.items[0].namakotakab,
                            res.items[0].kodekotakab,
                            true,
                            true
                        );
                        $('[name="idkotanpwp"]').append(option).trigger('change');
                    }
                });
            }


            $('[name="idcoretax"]').val($.trim(dt.idcoretax).toUpperCase());
            $('[name="idtku"]').val($.trim(dt.idtku).toUpperCase());
            $('[name="docnopembeli"]').val($.trim(dt.docnopembeli).toUpperCase());

            // =============== gradecust =========================
            if (isValid(dt.grade)) {
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_gradecust?var=' + dt.grade.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (res.items && res.items.length > 0) {
                        var option = new Option(
                            res.items[0].nmgradecust,
                            res.items[0].kdgradecust,
                            true,
                            true
                        );
                        $('[name="grade"]').append(option).trigger('change');
                    }
                });
            }
            // =============== salesman =========================
            if (isValid(dt.salesman)) {
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_salesman?var=' + dt.salesman.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (res.items && res.items.length > 0) {
                        var option = new Option(
                            res.items[0].nmsalesman,
                            res.items[0].kdsalesman,
                            true,
                            true
                        );
                        $('[name="salesman"]').append(option).trigger('change');
                    }
                });
            }
            // =============== kolektor =========================
            if (isValid(dt.kolektor)) {
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_kolektor?var=' + dt.kolektor.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (res.items && res.items.length > 0) {
                        var option = new Option(
                            res.items[0].nmkolektor,
                            res.items[0].kdkolektor,
                            true,
                            true
                        );
                        $('[name="kolektor"]').append(option).trigger('change');
                    }
                });
            }

            // $('[name="idmarket"]').val($.trim(dt.idmarket).toUpperCase());
            $('[name="koderetur"]').val($.trim(dt.koderetur).toUpperCase());
            
            // $('[name="koderetur"]').val($.trim(dt.koderetur).toUpperCase());
            $('[name="isfaktur"]').prop(
                'checked',
                $.trim((dt.isfaktur || '')).toUpperCase() === 'YES'
            );

            if (isValid(dt.harifakturin)) {
                $('[name="harifakturin[]"]')
                    .val(dt.harifakturin.split(','))
                    .trigger('change');
            }

            if (isValid(dt.haripenagihan)) {
                $('[name="haripenagihan[]"]')
                    .val(dt.haripenagihan.split(','))
                    .trigger('change');
            }

            if (isValid(dt.haripembayaran)) {
                $('[name="haripembayaran[]"]')
                    .val(dt.haripembayaran.split(','))
                    .trigger('change');
            }



            if (isValid(dt.defaultfor)){
                $('[name="defaultfor"]').val(dt.defaultfor.trim()).trigger('change');
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


function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}
/* FOR INPUT FUNCTION */

$('#formInputCustomer').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        kdcustomer: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmcustomer: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        // alamat: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // },
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
        // chold: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         }
        //     }
        // }
    },
    excluded: [':disabled']
});


function saveInputCustomer() {
    let typeform = $('[name="type"]').val(); // Ambil tipe form (INPUT / UPDATE)

    Swal.fire({
        title: 'Warning..!!!',
        text: `Data Customer akan ${typeform === 'INPUT' ? 'dibuat' : 'diupdate'}, Anda yakin dengan data yang diberikan?`,
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
                "Kode Customer": $('[name="kdcustomer"]').val(),
                "Nama Customer": $('[name="nmcustomer"]').val(),
                // "Alamat": $('[name="alamat"]').val(),
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

                    kdcustomer: $('[name="kdcustomer"]').val(),
                    nmcustomer: $('[name="nmcustomer"]').val(),

                    alamat_kantor: $('[name="alamat_kantor"]').val(),
                    kodepos_kantor: $('[name="kodepos_kantor"]').val(),
                    provinsi_kantor: $('[name="provinsi_kantor"]').val(),
                    kota_kantor: $('[name="kota_kantor"]').val(),
                    kec_kantor: $('[name="kec_kantor"]').val(),
                    kel_kantor: $('[name="kel_kantor"]').val(),
                    
                    alamat_pengiriman: $('[name="alamat_pengiriman"]').val(),
                    kodepos_pengiriman: $('[name="kodepos_pengiriman"]').val(),
                    provinsi_pengiriman: $('[name="provinsi_pengiriman"]').val(),
                    kota_pengiriman: $('[name="kota_pengiriman"]').val(),
                    kec_pengiriman: $('[name="kec_pengiriman"]').val(),
                    kel_pengiriman: $('[name="kel_pengiriman"]').val(),

                    alamat_penagihan: $('[name="alamat_penagihan"]').val(),
                    kodepos_penagihan: $('[name="kodepos_penagihan"]').val(),
                    provinsi_penagihan: $('[name="provinsi_penagihan"]').val(),
                    kota_penagihan: $('[name="kota_penagihan"]').val(),
                    kec_penagihan: $('[name="kec_penagihan"]').val(),
                    kel_penagihan: $('[name="kel_penagihan"]').val(),

                    phone: $('[name="phone"]').val(),
                    email: $('[name="email"]').val(),
                    fax: $('[name="fax"]').val(),
                    npwp: $('[name="npwp"]').val(),
                    npkp: $('[name="npkp"]').val(),
                    plafon: convertToDbNumber($('[name="plafon"]').val()),
                    jthtempo: convertToDbNumber($('[name="jthtempo"]').val()),
                    idmarket: $('[name="idmarket"]').val(),

                    cp: $('[name="cp"]').val(),
                    jabatan: $('[name="jabatan"]').val(),
                    keterangan: $('[name="keterangan"]').val(),
                    coa: $('#coa').is(':checked') ? 'YES' : 'NO',
                    blacklist: $('#blacklist').is(':checked') ? 'YES' : 'NO',

                    chold: $('[name="chold"]').val(),
                    namanpwp: $('[name="namanpwp"]').val(),
                    alamatnpwp: $('[name="alamatnpwp"]').val(),
                    idkotanpwp: $('[name="idkotanpwp"]').val(),
                    idcoretax: $('[name="idcoretax"]').val(),
                    idtku: $('[name="idtku"]').val(),
                    docnopembeli: $('[name="docnopembeli"]').val(),

                    grade: $('[name="grade"]').val(),
                    salesman: $('[name="salesman"]').val(),
                    kolektor: $('[name="kolektor"]').val(),

                    koderetur: $('[name="koderetur"]').val(),
                    isfaktur: $('#isfaktur').is(':checked') ? 'YES' : 'NO',
                    
                    harifakturin   : $('[name="harifakturin[]"]').val(),
                    haripenagihan  : $('[name="haripenagihan[]"]').val(),
                    haripembayaran : $('[name="haripembayaran[]"]').val(),


                    defaultfor: $('[name="defaultfor"]').val(),
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
                url: HOST_URL + 'master/data/saveDataCustomer',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (dt) {
                    if (dt.status) {
                        Swal.fire({
                            title: 'Berhasil...!!!',
                            text: dt.messages,
                            backdrop: true,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            confirmButtonText: `Ok`,
                            icon: 'success',
                        }).then(() => {
                            window.location.replace(HOST_URL + 'master/data/customer');
                        });
                    } else {
                        Swal.fire({
                            title: 'Galat...!!!',
                            text: dt.messages,
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




function editCustomer(e){
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
            window.location.replace(HOST_URL + 'master/data/edit_customer' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}



function detailCustomer(e){
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
            window.location.replace(HOST_URL + 'master/data/detail_customer' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}



function hapusCustomer(e) {
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
                url: HOST_URL + 'master/data/hapus_customer',
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


function isValid(val) {
    return val !== null && val !== undefined && String(val).trim() !== '';
}


$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});


/// ++++++++++++++++++++++++++++++ GEOLOCATION AREA ++++++++++++++++++++++++++++++++++++

function copyAlamatKantorKePengiriman() {
    if ($('#copy_alamat_pengiriman').is(':checked')) {

        injectSelect2Value(
            '[name="provinsi_pengiriman"]',
            $('[name="provinsi_kantor"]').val(),
            HOST_URL + 'api/geolocation/list_provinsi',
            'namaprov',
            'kodeprov'
        );

        injectSelect2Value(
            '[name="kota_pengiriman"]',
            $('[name="kota_kantor"]').val(),
            HOST_URL + 'api/geolocation/list_kota',
            'namakotakab',
            'kodekotakab'
        );

        injectSelect2Value(
            '[name="kec_pengiriman"]',
            $('[name="kec_kantor"]').val(),
            HOST_URL + 'api/geolocation/list_kecamatan',
            'namakec',
            'kodekec'
        );

        injectSelect2Value(
            '[name="kel_pengiriman"]',
            $('[name="kel_kantor"]').val(),
            HOST_URL + 'api/geolocation/list_desa',
            'namakeldesa',
            'kodekeldesa'
        );

        $('[name="alamat_pengiriman"]').val($('[name="alamat_kantor"]').val());
        $('[name="kodepos_pengiriman"]').val($('[name="kodepos_kantor"]').val());

    } else {
        clearSelect2('[name="provinsi_pengiriman"]');
        clearSelect2('[name="kota_pengiriman"]');
        clearSelect2('[name="kec_pengiriman"]');
        clearSelect2('[name="kel_pengiriman"]');

        $('[name="alamat_pengiriman"]').val('');
        $('[name="kodepos_pengiriman"]').val('');
    }
}



function copyAlamatKantorKePenagihan() {
    if ($('#copy_alamat_penagihan').is(':checked')) {

        injectSelect2Value(
            '[name="provinsi_penagihan"]',
            $('[name="provinsi_kantor"]').val(),
            HOST_URL + 'api/geolocation/list_provinsi',
            'namaprov',
            'kodeprov'
        );

        injectSelect2Value(
            '[name="kota_penagihan"]',
            $('[name="kota_kantor"]').val(),
            HOST_URL + 'api/geolocation/list_kota',
            'namakotakab',
            'kodekotakab'
        );

        injectSelect2Value(
            '[name="kec_penagihan"]',
            $('[name="kec_kantor"]').val(),
            HOST_URL + 'api/geolocation/list_kecamatan',
            'namakec',
            'kodekec'
        );

        injectSelect2Value(
            '[name="kel_penagihan"]',
            $('[name="kel_kantor"]').val(),
            HOST_URL + 'api/geolocation/list_desa',
            'namakeldesa',
            'kodekeldesa'
        );

        $('[name="alamat_penagihan"]').val($('[name="alamat_kantor"]').val());
        $('[name="kodepos_penagihan"]').val($('[name="kodepos_kantor"]').val());

    } else {
        clearSelect2('[name="provinsi_penagihan"]');
        clearSelect2('[name="kota_penagihan"]');
        clearSelect2('[name="kec_penagihan"]');
        clearSelect2('[name="kel_penagihan"]');

        $('[name="alamat_penagihan"]').val('');
        $('[name="kodepos_penagihan"]').val('');
    }
}

function clearSelect2(selector) {
    $(selector).val(null).trigger('change');
    // Jika ingin ekstra bersih, bisa juga kosongkan HTML option
    $(selector).html('').trigger('change');
}


function injectSelect2Value(selector, value, endpoint, labelField, idField) {
    if (!value) return;

    // jika option sudah ada → cukup set value
    if ($(selector).find('option[value="' + value + '"]').length) {
        $(selector).val(value).trigger('change');
        return;
    }

    $.ajax({
        url: endpoint,
        type: 'GET',
        dataType: 'json',
        data: { var: value },
        success: function (response) {
            if (!response.items || !response.items.length) return;

            let item = response.items[0];

            let text = item[labelField] ?? value;
            let val  = item[idField] ?? value;

            let newOption = new Option(text, val, true, true);
            $(selector).append(newOption).trigger('change');
        }
    });
}



// Provinsi Kantor
$("#provinsi_kantor").select2({
    placeholder: "Ketik/Pilih Provinsi Kantor",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_provinsi',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatProvinsi,
    templateSelection: formatProvinsiSelection
}).on("select2:selecting", () => {
    $("#kota_kantor, #kec_kantor, #kel_kantor").val(null).trigger('change');
});

// Kota Kantor
$("#kota_kantor").select2({
    placeholder: "Ketik/Pilih Kota Kantor",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kota',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
            _paramglobal_: $('#provinsi_kantor').val(),
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatKota,
    templateSelection: formatKotaSelection
}).on("select2:selecting", () => {
    $("#kec_kantor, #kel_kantor").val(null).trigger('change');
});

// Kecamatan Kantor
$("#kec_kantor").select2({
    placeholder: "Ketik/Pilih Kecamatan Kantor",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kecamatan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
            _paramglobal_: $('#kota_kantor').val(),
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatKecamatan,
    templateSelection: formatKecamatanSelection
}).on("select2:selecting", () => {
    $("#kel_kantor").val(null).trigger('change');
});

// Kelurahan Kantor
$("#kel_kantor").select2({
    placeholder: "Ketik/Pilih Kel/Desa Kantor",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_desa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
            _paramglobal_: $('#kec_kantor').val(),
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatDesa,
    templateSelection: formatDesaSelection
});


/// +++++++++++++++++++++ PENGIRIMAN +++++++++++++++++++++++++++++++++++

// Provinsi Pengiriman
$("#provinsi_pengiriman").select2({
    placeholder: "Ketik/Pilih Provinsi Pengiriman",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_provinsi',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatProvinsi,
    templateSelection: formatProvinsiSelection
}).on("select2:selecting", () => {
    $("#kota_pengiriman, #kec_pengiriman, #kel_pengiriman").val(null).trigger('change');
});

// Kota Pengiriman
$("#kota_pengiriman").select2({
    placeholder: "Ketik/Pilih Kota Pengiriman",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kota',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
            _paramglobal_: $('#provinsi_pengiriman').val(),
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatKota,
    templateSelection: formatKotaSelection
}).on("select2:selecting", () => {
    $("#kec_pengiriman, #kel_pengiriman").val(null).trigger('change');
});

// Kecamatan Pengiriman
$("#kec_pengiriman").select2({
    placeholder: "Ketik/Pilih Kecamatan Pengiriman",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kecamatan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
            _paramglobal_: $('#kota_pengiriman').val(),
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatKecamatan,
    templateSelection: formatKecamatanSelection
}).on("select2:selecting", () => {
    $("#kel_pengiriman").val(null).trigger('change');
});

// Kelurahan Pengiriman
$("#kel_pengiriman").select2({
    placeholder: "Ketik/Pilih Kel/Desa Pengiriman",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_desa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
            _paramglobal_: $('#kec_pengiriman').val(),
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatDesa,
    templateSelection: formatDesaSelection
});
// ++++++++++++++++++++++++ END OF PENGIRIMAN +++++++++++++++++++++++++


//+++++++++++++++++++++++++++ PENAGIHAN +++++++++++++++++++++++++++++++
// Provinsi Penagihan
$("#provinsi_penagihan").select2({
    placeholder: "Ketik/Pilih Provinsi Penagihan",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_provinsi',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatProvinsi,
    templateSelection: formatProvinsiSelection
}).on("select2:selecting", () => {
    $("#kota_penagihan, #kec_penagihan, #kel_penagihan").val(null).trigger('change');
});

// Kota Penagihan
$("#kota_penagihan").select2({
    placeholder: "Ketik/Pilih Kota Penagihan",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kota',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
            _paramglobal_: $('#provinsi_penagihan').val(),
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatKota,
    templateSelection: formatKotaSelection
}).on("select2:selecting", () => {
    $("#kec_penagihan, #kel_penagihan").val(null).trigger('change');
});

// Kecamatan Penagihan
$("#kec_penagihan").select2({
    placeholder: "Ketik/Pilih Kecamatan Penagihan",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_kecamatan',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
            _paramglobal_: $('#kota_penagihan').val(),
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatKecamatan,
    templateSelection: formatKecamatanSelection
}).on("select2:selecting", () => {
    $("#kel_penagihan").val(null).trigger('change');
});

// Kelurahan Penagihan
$("#kel_penagihan").select2({
    placeholder: "Ketik/Pilih Kel/Desa Penagihan",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/geolocation/list_desa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
            _search_: params.term,
            _page_: params.page,
            _draw_: true,
            _start_: 1,
            _perpage_: 2,
            _paramglobal_: $('#kec_penagihan').val(),
        }),
        processResults: (data, params) => ({
            results: data.items,
            pagination: { more: (params.page * 30) < data.total_count }
        }),
        cache: true
    },
    escapeMarkup: markup => markup,
    templateResult: formatDesa,
    templateSelection: formatDesaSelection
});
// ++++++++++++++++++++++++END OF PENAGIHAN ++++++++++++++++++++++++


function formatProvinsi(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kodeprov + " <i class='fa fa-circle-o'></i> " + repo.namaprov + "</div>";
    return markup;
}
function formatProvinsiSelection(repo) {
    return repo.namaprov || repo.text;
}

function formatKota(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kodekotakab + " <i class='fa fa-circle-o'></i> " + repo.namakotakab + "</div>";
    return markup;
}
function formatKotaSelection(repo) {
    return repo.namakotakab || repo.text;
}

function formatKecamatan(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kodekec + " <i class='fa fa-circle-o'></i> " + repo.namakec + "</div>";
    return markup;
}
function formatKecamatanSelection(repo) {
    return repo.namakec || repo.text;
}

function formatDesa(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kodekeldesa + " <i class='fa fa-circle-o'></i> " + repo.namakeldesa + "</div>";
    return markup;
}
function formatDesaSelection(repo) {
    return repo.namakeldesa || repo.text;
}




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
$("#idkotanpwp").select2({
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



function formatGradeCust(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdgradecust +"   <i class='fa fa-circle-o'></i>   "+ repo.nmgradecust +"</div>";
    return markup;
}
function formatGradeCustSelection(repo) {
    return repo.nmgradecust || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#grade").select2({
    placeholder: "Ketik/Pilih Grade",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_gradecust',
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
    templateResult: formatGradeCust, // omitted for brevity, see the source of this page
    templateSelection: formatGradeCustSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



function formatSalesman(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdsalesman +"   <i class='fa fa-circle'></i>   "+ repo.nmsalesman+"   <i class='fa fa-circle'></i>   "+ repo.alamat + " <i class='fa fa-circle'></i>  "+ repo.namakotakab +"</div>";
    return markup;
}
function formatSalesmanSelection(repo) {
    return repo.nmsalesman || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#salesman").select2({
    placeholder: "Ketik/Pilih Salesman",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_salesman',
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
    templateResult: formatSalesman, // omitted for brevity, see the source of this page
    templateSelection: formatSalesmanSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



function formatKolektor(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdkolektor +"   <i class='fa fa-circle-o'></i>   "+ repo.nmkolektor +"</div>";
    return markup;
}
function formatKolektorSelection(repo) {
    return repo.nmkolektor || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#kolektor").select2({
    placeholder: "Ketik/Pilih Kolektor",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_kolektor',
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
    templateResult: formatKolektor, // omitted for brevity, see the source of this page
    templateSelection: formatKolektorSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});


$('#harifakturin').select2({
    placeholder: 'Pilih hari',
    width: '100%'
});


$('#haripenagihan').select2({
    placeholder: 'Pilih hari',
    width: '100%'
});

$('#haripembayaran').select2({
    placeholder: 'Pilih hari',
    width: '100%'
});


function isAlamatSama(a, b) {
    return (
        a.provinsi === b.provinsi &&
        a.kota === b.kota &&
        a.kec === b.kec &&
        a.kel === b.kel &&
        (a.alamat || '').trim() === (b.alamat || '').trim() &&
        (a.kodepos || '').trim() === (b.kodepos || '').trim()
    );
}



$(document).ready(function() {
    // $('#ketentuanModal').modal({
    //     backdrop: 'static',   // klik luar modal tidak menutup
    //     keyboard: false       // tombol Esc tidak menutup
    // });
    // $('#ketentuanModal').modal('show');
    tableCustomer();
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