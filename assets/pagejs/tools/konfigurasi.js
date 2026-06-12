/*
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


let skipRoleChange = false;


// Function untuk mengumpulkan data dari semua form
function collectFormData() {
    var formData = {};
    
    // Kumpulkan semua input dan select dari semua tab
    $('#formKonfigurasi input, #formKonfigurasi select, #perkiraan-content select, #default-content select, #default-content input').each(function() {
        var fieldName = $(this).attr('name');
        if (fieldName) {
            if ($(this).attr('type') === 'checkbox') {
                // Untuk checkbox: kirim 'YES' jika checked, 'NO' jika tidak
                formData[fieldName] = $(this).is(':checked') ? 'YES' : 'NO';
            } else {
                // Untuk input/select lainnya
                formData[fieldName] = $(this).val();
            }
        }
    });
    
    return formData;
}



// Event: Edit Button
$('#btnEditKonfigurasi').on('click', function() {
    // Simpan data awal sebelum edit
    originalData = collectFormData();
    
    // Enable semua field
    setFieldsEditable(true);
    
    // Toggle buttons
    $(this).hide();
    $('#btnSimpanKonfigurasi, #btnCancelEdit').show();
});

// Event: Cancel Edit Button
$('#btnCancelEdit').on('click', function() {
    // Kembalikan data ke data awal
    populateFormData(originalData);
    
    // Disable semua field kembali
    setFieldsEditable(false);
    
    // Toggle buttons
    $(this).hide();
    $('#btnSimpanKonfigurasi').hide();
    $('#btnEditKonfigurasi').show();
    
    // // Tampilkan notifikasi
    // toastr.info('Edit dibatalkan', 'Info');
});


function setFieldsEditable(isEditable) {
    // Untuk input readonly
    $('#formKonfigurasi input, #default-content input').each(function() {
        $(this).prop('readonly', !isEditable);
        $(this).prop('disabled', !isEditable);
    });
    
    // Untuk select dan select2
    $('#formKonfigurasi select, #perkiraan-content select, #default-content select').each(function() {
        if (isEditable) {
            // Hapus atribut disabled dari DOM
            $(this).removeAttr('disabled');
            $(this).prop('disabled', false);
            
            // // Enable select2
            // if ($(this).hasClass('select2-hidden-accessible') && $(this).data('select2')) {
            //     $(this).select2('enable', true);
            // } else {
            //     // Re-initialize select2 jika perlu
            //     $(this).select2();
            //     $(this).select2('enable', true);
            // }
        } else {
            // Tambahkan kembali atribut disabled
            $(this).attr('disabled', 'disabled');
            $(this).prop('disabled', true);
            
            if ($(this).hasClass('select2-hidden-accessible') && $(this).data('select2')) {
                $(this).select2('enable', false);
            }
        }
    });
    
    // Toggle class edit-mode
    $('.card-body').toggleClass('edit-mode', isEditable);
}


// Function untuk mengisi data ke form
function populateFormData(data) {
    // Loop semua input dan select di semua form
    $('#formKonfigurasi input, #formKonfigurasi select, #perkiraan-content select, #default-content select, #default-content input').each(function() {
        var fieldName = $(this).attr('name');
        if (fieldName && data[fieldName] !== undefined) {
            if ($(this).is('select')) {
                $(this).val(data[fieldName]).trigger('change');
            } else {
                $(this).val(data[fieldName]);
            }
        }
    });
}


$('#btnSimpanKonfigurasi').on('click', function() {
    // Kumpulkan semua data
    var formData = collectFormData();
    
    // Disable button sementara untuk mencegah double submit
    $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
    
    // Kirim ke server
    $.ajax({
        url: HOST_URL + 'tools/konfigurasi/updateKonfigurasi',
        type: 'POST',
        data: JSON.stringify(formData), // Kirim sebagai JSON string
        dataType: 'json',
        contentType: 'application/json', // Set content type ke JSON
        success: function(response) {
            if (response.success) {
                // SweetAlert sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    // zIndex: 99999, // Tambahkan zIndex tinggi
                    onOpen: function() {
                    // Force bring to front
                        document.querySelector('.swal2-container').style.zIndex = '999999';
                        document.querySelector('.swal2-popup').style.zIndex = '1000000';
                    },
                    text: 'Konfigurasi berhasil disimpan',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'OK',
                    // timer: 2000,
                    showConfirmButton: true
                });
                
                // Disable kembali semua field
                documentReadable()
                setFieldsEditable(false);
                
                // Reset buttons
                $('#btnSimpanKonfigurasi, #btnCancelEdit').hide();
                $('#btnEditKonfigurasi').show();
                
                // Update original data dengan data terbaru dari response
                // if (response.data) {
                //     originalData = JSON.parse(JSON.stringify(response.data));
                // } else {
                //     originalData = collectFormData();
                // }
            } else {
                // SweetAlert error
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    // zIndex: 99999, // Tambahkan zIndex tinggi
                    onOpen: function() {
                    // Force bring to front
                        document.querySelector('.swal2-container').style.zIndex = '999999';
                        document.querySelector('.swal2-popup').style.zIndex = '1000000';
                    },
                    text: response.message || 'Gagal menyimpan konfigurasi',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            let errorMsg = 'Terjadi kesalahan saat menyimpan';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            // SweetAlert error
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                // zIndex: 99999, // Tambahkan zIndex tinggi
                onOpen: function() {
                    // Force bring to front
                        document.querySelector('.swal2-container').style.zIndex = '999999';
                        document.querySelector('.swal2-popup').style.zIndex = '1000000';
                    },
                text: errorMsg,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'OK'
            });
        },
        complete: function() {
            $('#btnSimpanKonfigurasi').prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Konfigurasi');
        }
    });
});

function isValidValue(value) {
    return value && value !== null && String(value).trim() !== '';
}


//EDIT ITEM
function documentReadable(){
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: false //Display loader!
    // });
    // var docno = $('[name="docno"]').val()

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'tools/konfigurasi/showing_konfigurasimst',
        data: {},
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;
            // Fungsi helper untuk mengecek value valid
                $('[name="pp"]').val(json.dataTables.items[0].pp).prop('readonly',true);
                $('[name="voidpp"]').val(json.dataTables.items[0].voidpp).prop('readonly',true);
                $('[name="po"]').val(json.dataTables.items[0].po).prop('readonly',true);
                $('[name="voidpo"]').val(json.dataTables.items[0].voidpo).prop('readonly',true);
                $('[name="lpb"]').val(json.dataTables.items[0].lpb).prop('readonly',true);
                $('[name="returbeli"]').val(json.dataTables.items[0].returbeli).prop('readonly',true);
                $('[name="refundbeli"]').val(json.dataTables.items[0].refundbeli).prop('readonly',true);

                // PENJUALAN
                $('[name="salesorder"]').val(json.dataTables.items[0].salesorder).prop('readonly', true);
                $('[name="voidso"]').val(json.dataTables.items[0].voidso).prop('readonly', true);
                $('[name="deliveryorder"]').val(json.dataTables.items[0].deliveryorder).prop('readonly', true);
                $('[name="suratjalan"]').val(json.dataTables.items[0].suratjalan).prop('readonly', true);
                $('[name="penjualan"]').val(json.dataTables.items[0].penjualan).prop('readonly', true);
                $('[name="penjualannon"]').val(json.dataTables.items[0].penjualannon).prop('readonly', true);
                $('[name="returpenjualan"]').val(json.dataTables.items[0].returpenjualan).prop('readonly', true);
                $('[name="retursj"]').val(json.dataTables.items[0].retursj).prop('readonly', true);
                $('[name="refundjual"]').val(json.dataTables.items[0].refundjual).prop('readonly', true);

                // PRODUKSI
                $('[name="workorder"]').val(json.dataTables.items[0].workorder).prop('readonly', true);
                $('[name="workorderexecution"]').val(json.dataTables.items[0].workorderexecution).prop('readonly', true);
                $('[name="materialrelease"]').val(json.dataTables.items[0].materialrelease).prop('readonly', true);
                $('[name="bpnm"]').val(json.dataTables.items[0].bpnm).prop('readonly', true);
                $('[name="penerimaanbarangprod"]').val(json.dataTables.items[0].penerimaanbarangprod).prop('readonly', true);
                $('[name="setorantarbagian"]').val(json.dataTables.items[0].setorantarbagian).prop('readonly', true);
                $('[name="pmkbarang"]').val(json.dataTables.items[0].pmkbarang).prop('readonly', true);
                $('[name="pnmbarang"]').val(json.dataTables.items[0].pnmbarang).prop('readonly', true);

                // KAS / BANK
                $('[name="kasmasuk"]').val(json.dataTables.items[0].kasmasuk).prop('readonly', true);
                $('[name="kaskeluar"]').val(json.dataTables.items[0].kaskeluar).prop('readonly', true);
                $('[name="bankmasuk"]').val(json.dataTables.items[0].bankmasuk).prop('readonly', true);
                $('[name="bankkeluar"]').val(json.dataTables.items[0].bankkeluar).prop('readonly', true);
                $('[name="setorangiro"]').val(json.dataTables.items[0].setorangiro).prop('readonly', true);
                $('[name="pencairangiro"]').val(json.dataTables.items[0].pencairangiro).prop('readonly', true);
                $('[name="tolakangiro"]').val(json.dataTables.items[0].tolakangiro).prop('readonly', true);
                $('[name="buktikaskecil"]').val(json.dataTables.items[0].buktikaskecil).prop('readonly', true);

                // FAKTUR PAJAK
                $('[name="fpm"]').val(json.dataTables.items[0].fpm).prop('readonly', true);
                $('[name="fpk"]').val(json.dataTables.items[0].fpk).prop('readonly', true);
                $('[name="bppph"]').val(json.dataTables.items[0].bppph).prop('readonly', true);

                // LAIN-LAIN
                $('[name="notadk"]').val(json.dataTables.items[0].notadk).prop('readonly', true);
                $('[name="jurnalumump"]').val(json.dataTables.items[0].jurnalumump).prop('readonly', true);
                $('[name="ptal"]').val(json.dataTables.items[0].ptal).prop('readonly', true);
                $('[name="koreksihargajual"]').val(json.dataTables.items[0].koreksihargajual).prop('readonly', true);
                $('[name="adjusmentstock"]').val(json.dataTables.items[0].adjusmentstock).prop('readonly', true);

                $('[name="prefixnofp"]').val(json.dataTables.items[0].prefixnofp).prop('readonly',true);
                // HPP
                if (isValidValue(json.dataTables.items[0].hpp)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].hpp,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="hpp"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                // Laba Kurs
                if (isValidValue(json.dataTables.items[0].labakurs)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].labakurs,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="labakurs"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                // Rugi Kurs
                if (isValidValue(json.dataTables.items[0].rugikurs)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].rugikurs,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="rugikurs"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                // Laba Ditahan Th Berjalan (LDTB)
                if (isValidValue(json.dataTables.items[0].ldtb)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].ldtb,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="ldtb"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                // Laba Ditahan Th Lalu (LDTL)
                if (isValidValue(json.dataTables.items[0].ldtl)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].ldtl,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="ldtl"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                // Perkiraan Produksi
                if (isValidValue(json.dataTables.items[0].pproduksi)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].pproduksi,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="pproduksi"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                //Pajak
                if (isValidValue(json.dataTables.items[0].idtax)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_tax' + '?var=' + json.dataTables.items[0].idtax,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmtax, datax.items[0].idtax, true, true);
                            $('[name="idtax"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                //Mata Uang
                if (isValidValue(json.dataTables.items[0].currcode)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_currency' + '?var=' + json.dataTables.items[0].currcode,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        // create the option and append to Select2
                        var currencyData = datax.items[0];
                        currencyData.kurs = json.dataTables.items[0].kurs;
                        // currencyData.phone = data.phone;
                        
                        // create the option dan simpan data lengkap
                        var option = new Option(currencyData.currname, currencyData.currcode, true, true);
                        $(option).data('currency-data', currencyData); // Simpan data lengkap
                        
                        $('[name="currcode"]').append(option).trigger('change');
                        
                        // Set alamat dan phone langsung
                        // setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
                        // $('[name="kurs"]').prop('readonly', true);
                        // $("#phone").val(data.phone).prop('readonly', true);
                    });
                }


                //Gudang
                if (isValidValue(json.dataTables.items[0].gudang)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + json.dataTables.items[0].gudang,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmlocation, datax.items[0].idlocation, true, true);
                            $('[name="gudang"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }


                // Kas Kecil
                if (isValidValue(json.dataTables.items[0].kaskecil)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].kaskecil,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="kaskecil"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }



                // Perkiraan Kas
                if (isValidValue(json.dataTables.items[0].pkas)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].pkas,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="pkas"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }


                // Perkiraan Persediaan
                if (isValidValue(json.dataTables.items[0].ppersediaan)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].ppersediaan,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="ppersediaan"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }


                // Perkiraan Surat Jalan
                if (isValidValue(json.dataTables.items[0].psj)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].psj,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="psj"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                 // Perkiraan Selisih
                if (isValidValue(json.dataTables.items[0].pselisih)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].pselisih,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="pselisih"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                //Gudang Retail
                if (isValidValue(json.dataTables.items[0].gudangretail)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + json.dataTables.items[0].gudangretail,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmlocation, datax.items[0].idlocation, true, true);
                            $('[name="gudangretail"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }


                 // Perkiraan Mutasi Masuk
                if (isValidValue(json.dataTables.items[0].pmutasimasuk)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].pmutasimasuk,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="pmutasimasuk"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }

                // Perkiraan Mutasi Keluar
                if (isValidValue(json.dataTables.items[0].pmutasikeluar)) {
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].pmutasikeluar,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax && datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                            $('[name="pmutasikeluar"]').append(option).trigger('change').prop('disabled', true);
                        }
                    });
                }


                $('[name="ispajak"]').prop(
                    'checked',
                    $.trim((json.dataTables.items[0].ispajak || '')).toUpperCase() === 'YES'
                );


                $('[name="sembunyilokasi"]').prop(
                    'checked',
                    $.trim((json.dataTables.items[0].sembunyilokasi || '')).toUpperCase() === 'YES'
                );

            // setSelect2Ajax('#hpp',     json.dataTables.items[0].hpp,     json.dataTables.items[0].hpp);
            // setSelect2Ajax('#labakurs',     json.dataTables.items[0].labakurs,     json.dataTables.items[0].labakurs);

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




var defaultInitialLocation = '';
$("#gudang").select2({
    placeholder: " -- Pilih Gudang Asal -- ",
    allowClear: true,
    width: '100%',
    dropdownParent: $('#modalUpdateLPB'),
    // minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
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
                _paramglobal_: defaultInitialLocation,
                _parameterx_: defaultInitialLocation,
                term: params.term,
            };
        },
        processResults: function(data, params) {

            // var searchTerm = $("#idgudang").data("select2").$dropdown.find("input").val();
            // if (data.items.length === 1 && data.items[0].text === searchTerm) {
            //     var option = new Option(data.items[0].nmlocation, data.items[0].idlocation, true, true);
            //     $('#idgudang').append(option).trigger('change').select2("close");
            //     // manually trigger the `select2:select` event
            //     $('#idgudang').trigger({
            //         type: 'select2:select',
            //         params: {
            //             data: data
            //         }
            //     });
            // }
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
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("change", function () {
   /*Sementara TUtup Location */
});





var defaultInitialLocation = '';
$("#gudangretail").select2({
    placeholder: " -- Pilih Gudang Retail -- ",
    allowClear: true,
    width: '100%',
    dropdownParent: $('#modalUpdateLPB'),
    // minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
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
                _paramglobal_: defaultInitialLocation,
                _parameterx_: defaultInitialLocation,
                term: params.term,
            };
        },
        processResults: function(data, params) {

            // var searchTerm = $("#idgudang").data("select2").$dropdown.find("input").val();
            // if (data.items.length === 1 && data.items[0].text === searchTerm) {
            //     var option = new Option(data.items[0].nmlocation, data.items[0].idlocation, true, true);
            //     $('#idgudang').append(option).trigger('change').select2("close");
            //     // manually trigger the `select2:select` event
            //     $('#idgudang').trigger({
            //         type: 'select2:select',
            //         params: {
            //             data: data
            //         }
            //     });
            // }
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
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("change", function () {
   /*Sementara TUtup Location */
});
/* Format Group */
function formatLocation(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idlocation +"   <i class='fa fa-circle-o'></i>   "+ repo.nmlocation +"</div>";
    return markup;
}

function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}





function formatCurrency(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.currcode +"   <i class='fa fa-circle'></i>   "+ repo.currname +"   <i class='fa fa-circle'></i>   "+  repo.kurs +"  </div>";
    return markup;
}
function formatCurrencySelection(repo) {
    return repo.currname || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#currcode").select2({
    placeholder: "Ketik/Pilih Currency",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_currency',
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
    templateResult: formatCurrency, // omitted for brevity, see the source of this page
    templateSelection: formatCurrencySelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    // setJtsValue('[name="kurs"]', convertToDbNumber(data.kurs));

});




function formatTax(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idtax +"   <i class='fa fa-circle'></i>   "+ repo.nmtax +"  </div>";
    return markup;
}
function formatTaxSelection(repo) {
    return repo.nmtax || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idtax").select2({
    placeholder: "Ketik/Pilih Pajak",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_tax',
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
    templateResult: formatTax, // omitted for brevity, see the source of this page
    templateSelection: formatTaxSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    // var data = e.params.data;

});





function formatCOA(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idcoa +"   <i class='fa fa-circle'></i>   "+ repo.nmcoa +" <i class='fa fa-circle'></i> LEVEL "+ repo.level + " </div>";
    return markup;
}
function formatCOASelection(repo) {
    return repo.nmcoa || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#hpp").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});


$("#labakurs").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#rugikurs").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#ldtb").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#ldtl").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});





$("#pproduksi").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



$("#kaskecil").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#pkas").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#ppersediaan").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});





$("#psj").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



$("#pselisih").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#pmutasimasuk").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});





$("#pmutasikeluar").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



var defaultInitialLocation = '';
$("#gudangretail").select2({
    placeholder: " -- Pilih Gudang Retail-- ",
    allowClear: true,
    width: '100%',
    // dropdownParent: $('#modalUpdateLPB'),
    // minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
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
                _paramglobal_: defaultInitialLocation,
                _parameterx_: defaultInitialLocation,
                term: params.term,
            };
        },
        processResults: function(data, params) {

            // var searchTerm = $("#idgudang").data("select2").$dropdown.find("input").val();
            // if (data.items.length === 1 && data.items[0].text === searchTerm) {
            //     var option = new Option(data.items[0].nmlocation, data.items[0].idlocation, true, true);
            //     $('#idgudang').append(option).trigger('change').select2("close");
            //     // manually trigger the `select2:select` event
            //     $('#idgudang').trigger({
            //         type: 'select2:select',
            //         params: {
            //             data: data
            //         }
            //     });
            // }
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
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("change", function () {
   /*Sementara TUtup Location */
});

var defaultInitialLocation = '';
$("#gudang").select2({
    placeholder: " -- Pilih Gudang -- ",
    allowClear: true,
    width: '100%',
    // dropdownParent: $('#modalUpdateLPB'),
    // minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
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
                _paramglobal_: defaultInitialLocation,
                _parameterx_: defaultInitialLocation,
                term: params.term,
            };
        },
        processResults: function(data, params) {

            // var searchTerm = $("#idgudang").data("select2").$dropdown.find("input").val();
            // if (data.items.length === 1 && data.items[0].text === searchTerm) {
            //     var option = new Option(data.items[0].nmlocation, data.items[0].idlocation, true, true);
            //     $('#idgudang').append(option).trigger('change').select2("close");
            //     // manually trigger the `select2:select` event
            //     $('#idgudang').trigger({
            //         type: 'select2:select',
            //         params: {
            //             data: data
            //         }
            //     });
            // }
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
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("change", function () {
   /*Sementara TUtup Location */
});
/* Format Group */
function formatLocation(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idlocation +"   <i class='fa fa-circle-o'></i>   "+ repo.nmlocation +"</div>";
    return markup;
}

function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}


function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}




$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});




function setSelect2Ajax(selector, value, text) {
    if (!value) return;
    
    var $select = $(selector);
    
    // Cek apakah option sudah ada
    if (!$select.find('option[value="' + value + '"]').length) {
        var option = new Option(text || value, value, true, true);
        $select.append(option);
    }
    
    // Set value
    $select.val(value).trigger('change');
}


function saveLPBDetail() {

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan data Penerimaan Pembelian?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        let formData = new FormData(document.getElementById('formLPBDetail'));
        formData.append('docdate', $('#docdate').val());
        formData.append('cabang', $('#cabang').val());
        // formData.append('senddate', $('#senddate').val());
        formData.append('jthtempo', convertToDbNumber($('#jthtempo').val()));
        formData.append('kdsupplier', $('#kdsupplier').val());
        formData.append('isinclusive', $('#isinclusive').is(':checked') ? 'YES' : 'NO');
        formData.append('alamatsupplier', $('#alamatsupplier').val());
        formData.append('nosj', $('#nosj').val());
        formData.append('nofaktur', $('#nofaktur').val());
        formData.append('idtax', $('#idtax').val());
        formData.append('currcode', $('#currcode').val());
        formData.append('kurs', convertToDbNumber($('#kurs').val()));
        formData.append('biayavol', convertToDbNumber($('#biayavol').val()));
        formData.append('biayavol2', convertToDbNumber($('#biayavol').val()));
        // formData.append('alamatkirim', $('#alamatkirim').val());
        formData.append('keterangan', $('#keterangan').val());
        // formData.append('estpakai', $('#estpakai').val());

        // docno gabungan (lebih aman pakai hidden header)
        formData.set('docno', $('#prefix').val() + '/' + $('#infix').val() + '/' + $('#sufix').val());
        // convert qty ke numeric DB
        let qty = $('#qty').val();
        let qtybonus = $('#qtybonus').val();
        let harga = $('#harga').val();
        let multidisc = $('#multidisc').val();
        let nilai = $('#nilai').val();
        let volitem = $('#volitem').val();
        let biaya = $('#biaya').val();
        let biaya2 = $('#biaya2').val();
        formData.set('qty', convertToDbNumber(qty));
        formData.set('qtybonus', convertToDbNumber(qtybonus));
        formData.set('harga', convertToDbNumber(harga));
        formData.set('multidisc', convertToDbNumber(multidisc));
        formData.set('nilai', convertToDbNumber(nilai));
        formData.set('volitem', convertToDbNumber(volitem));
        formData.set('biaya', convertToDbNumber(biaya));
        formData.set('biaya2', convertToDbNumber(biaya2));
        formData.set('descriptionpo', $('#descriptionpo').val());
        formData.set('uniqueid', $('#uniqueid').val());
        formData.set('idprincipal', $('#idprincipal').val());
        formData.set('idgudang', $('#idgudang').val());
        formData.set('idspec', $('#idspec').val());
        formData.set('docnopo', $('#docnopo').val());
        
        // formData.set('descriptionpo', convertToDbNumber(qty));

        $.ajax({
            url: HOST_URL + 'purchase/trans/saveLPBDetail',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,

            success: function (res) {

                if (!res.success) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: res.message
                    });

                    return;
                }

                // ==============================
                // SUCCESS
                // ==============================

                // Jika tidak ada item baru (semua sudah ada)
                let iconType = 'success';
                let titleText = 'Berhasil';

                if (res.message && res.message.toLowerCase().includes('sudah ada')) {
                    iconType = 'info';
                    titleText = 'Tidak Ada Perubahan';
                }

                Swal.fire({
                    icon: iconType,
                    title: titleText,
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Jika header baru dibuat → reload
                if (res.reload === true) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                    return;
                }

                // Jika hanya tambah detail
                $('#modalDetailLPB').modal('hide');
                $('#modalUpdateLPB').modal('hide');
                $('#formLPBUpdate')[0].reset();
                reload_table_lpb_dtl();
                documentReadable()
                $('#formLPBDetail')[0].reset();
            },

            error: function (xhr) {

                console.error(xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan pada server (500)'
                });
            }
        });


    });
}



$("#fjurnal").on("change", function () {
    //$("#njurnal").val('00011');
    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'purchase/purchaseorder/njurnal' + '/?var=' + $("#fjurnal").val(),
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $("#njurnal").val(data.njurnal);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
});



$(document).ready(function() {
    // Handle form submission event
    // Handle form submission event



    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);
    // if ($('[name="type"]').val() === 'EDIT') {
    //     documentReadable();
    // }
    //console.log($('[name="type"]').val());
    // if ($('[name="typeform"]').val() === 'INPUT' || $('[name="typeform"]').val() === 'UPDATE' || $('[name="typeform"]').val() === 'DELETE' ) {
        // Saat inisialisasi pertama kali
    
        documentReadable();
    // }
    $("#loadMe").modal("hide");




});