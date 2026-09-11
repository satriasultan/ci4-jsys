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
var initTable;
//"use strict";

function tableSuratJalanTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablesuratjalanTrx');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter":true,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'sales/postsales/list_suratjalan',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namacustomer = $('#namacustomer').val();
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

function reload_tableSuratJalanTrx()
{
    var table = $('#tablesuratjalanTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}



$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tablesuratjalanTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tablesuratjalanTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});


function tableSuratJalanApprvTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablesuratjalanapprvTrx');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter":true,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'sales/postsales/list_suratjalan_apprv',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namacustomer = $('#namacustomer').val();
                    data.status = $('#status_filter').val(); //A,P,S,ALL
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

function reload_tableSuratJalanApprvTrx()
{
    var table = $('#tablesuratjalanapprvTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function(){ //button filter event click
    var table = $('#tablesuratjalanTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tablesuratjalanTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

let skipRoleChange = false;



//EDIT ITEM
function documentReadable(){
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: false //Display loader!
    // });
    var docno = $('[name="docno"]').val()

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'sales/postsales/showing_suratjalantemp',
        data: { docno: docno },
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            $('[name="docno"]').val(json.dataTables.items[0].docno).prop('readonly', true);
            var docnoData = json.dataTables.items[0].docno.trim();
            let prefixParts = docnoData.split('/'); // ["JTS", "PH", "25", "08"]
            $('[name="prefix"]').val(prefixParts[0]).prop('readonly', true);
            $('[name="infix"]').val(prefixParts[1]).prop('readonly', true);
            $('[name="sufix"]').val(prefixParts[2]).prop('readonly', true);


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_customer' + '?var=' + json.dataTables.items[0].kdcustomer,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {

            // Tambahkan data alamat dan phone ke object
                var customerData = datax.items[0];
                customerData.alamat = json.dataTables.items[0].alamatcustomer;
                // customerData.phone = data.phone;
                
                // create the option dan simpan data lengkap
                var option = new Option(customerData.nmcustomer, customerData.kdcustomer, true, true);
                $(option).data('customer-data', customerData); // Simpan data lengkap
                
                $('[name="kdcustomer"]').append(option).trigger('change');
                defaultInitialCustDO = json.dataTables.items[0].kdcustomer

                // Set alamat dan phone langsung
                $("#alamatcustomer").val(json.dataTables.items[0].alamatcustomer).prop('readonly', true);
                // $("#phone").val(data.phone).prop('readonly', true);
            });


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_customer' + '?var=' + json.dataTables.items[0].kdcustomerdeliv,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {

            // Tambahkan data alamat dan phone ke object
                var customerdelivData = datax.items[0];
                customerdelivData.alamat = json.dataTables.items[0].alamatcustomerdeliv;
                // customerdelivData.phone = data.phone;
                
                // create the option dan simpan data lengkap
                var option = new Option(customerdelivData.nmcustomer, customerdelivData.kdcustomer, true, true);
                $(option).data('customerdeliv-data', customerdelivData); // Simpan data lengkap
                
                $('[name="kdcustomerdeliv"]').append(option).trigger('change');
                
                // Set alamat dan phone langsung
                $("#alamatcustomerdeliv").val(json.dataTables.items[0].alamatcustomerdeliv).prop('readonly', true);
                // $("#phone").val(data.phone).prop('readonly', true);
            });
            

            //$('[name="idgroup"]').val(json.dataTables.items[0].idgroup);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_branchjob' + '?var=' + json.dataTables.items[0].cabang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmbranch, datax.items[0].idbranch, true, true);
                $('[name="cabang"]').append(option).trigger('change').prop('disabled',true);

                // manually trigger the `select2:select` event
                $('[name="cabang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_salesman' + '?var=' + json.dataTables.items[0].kdsalesman,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmsalesman, datax.items[0].kdsalesman, true, true);
                $('[name="kdsalesman"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="kdsalesman"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            
            skipRoleChange = true;
            $('[name="docdate"]').val(json.dataTables.items[0].docdate).prop('disabled',true);
            
            // setJtsValue('[name="biayavol"]', convertToDbNumber(json.dataTables.items[0].biayavol));
            // setJtsValue('[name="biayavol2"]', convertToDbNumber(json.dataTables.items[0].biayavol2));
            
            // $('[name="jthtempo"]').val(json.dataTables.items[0].jthtempo);
            $('[name="alamatcustomer"]').val(json.dataTables.items[0].alamatcustomer);
            $('[name="alamatcustomerdeliv"]').val(json.dataTables.items[0].alamatcustomerdeliv);
            
            // $('[name="isinclusive"]').val(json.dataTables.items[0].isinclusive);
            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);
            // $('[name="nodp"]').val(json.dataTables.items[0].nodp);
            // $('[name="carabayar"]').val(json.dataTables.items[0].carabayar).trigger('change');


            // $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);
            //$('[name="chold"]').val(json.dataTables.items[0].chold.trim()).trigger('change');

            //$('[name="idbarang"]').prop('readonly', true);
            $("#loadMe").modal("hide");

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


// var defaultInitialGroupBrng = '';
// $("#idbarang").select2({
//     placeholder: "Choose Your Item List",
//     allowClear: true,
//     width:'100%',
//     ajax: {
//         url: HOST_URL + 'api/globalmodule/list_item',
//         type: 'POST',
//         dataType: 'json',
//         delay: 250,
//         data: function(params) {
//             return {
//                 _search_: params.term, // search term
//                 _page_: params.page,
//                 _draw_: true,
//                 _start_: 1,
//                 _perpage_: 2,
//                 _paramglobal_: defaultInitialGroupBrng,
//                 _parameterx_: defaultInitialGroupBrng,
//                 term: params.term,
//             };
//         },
//         processResults: function (data, params) {
//             // var searchTerm = $("#idbarang").data("select2").$dropdown.find("input").val();
//             // if (data.items.length === 1 && data.items[0].text === searchTerm) {
//             //     var option = new Option(data.items[0].nmbarang, data.items[0].idbarang, true, true);
//             //     $('#idbarang').append(option).trigger('change').select2("close");
//             //     // manually trigger the `select2:select` event
//             //     $('#idbarang').trigger({
//             //         type: 'select2:select',
//             //         params: {
//             //             data: data
//             //         }
//             //     });
//             // }
//             params.page = params.page || 1;
//             return {
//                 results: data.items,
//                 pagination: {
//                     more: (params.page * 30) < data.total_count
//                 }
//             };
//         },

//         cache: false
//     },
//     escapeMarkup: function(markup) {
//         return markup;
//     }, // let our custom formatter work
//     // minimumInputLength: 1,
//     templateResult: formatItem, // omitted for brevity, see the source of this page
//     templateSelection: formatItemSelection // omitted for brevity, see the source of this page
// }).on("select2:select", function (e) {
//     var data = e.params.data;
//     $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
//     $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
//     $("#batch").val(null).trigger('change');
// });

// /* Format Group */
// function formatItem(repo) {
//     if (repo.loading) return repo.text;
//     var markup ="<div class='select2-result-repository__description'>" + repo.idbarang +"   <i class='fa fa-circle-o'></i>   "+ repo.nmbarang +"</div>";
//     return markup;
// }
// function formatItemSelection(repo) {
//     return repo.nmbarang || repo.text;
// }


function formatSalesman(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdsalesman +"   <i class='fa fa-circle'></i>   "+ repo.nmsalesman+"   <i class='fa fa-circle'></i>   "+ repo.alamat + " <i class='fa fa-circle'></i>  "+ repo.namakotakab +"</div>";
    return markup;
}
function formatSalesmanSelection(repo) {
    return repo.nmsalesman || repo.text;
}
//var defaultInitialGol = $("#newdept").val();
$("#kdsalesman").select2({
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



function formatPrincipal(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idprincipal +"   <i class='fa fa-circle'></i>   "+ repo.nmprincipal +"  </div>";
    return markup;
}
function formatPrincipalSelection(repo) {
    return repo.nmprincipal || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idprincipal").select2({
    placeholder: "Ketik/Pilih Principal",
    allowClear: true,
    // dropdownParent: $('#modalDetailSuratJalan'),
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_principal',
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
    templateResult: formatPrincipal, // omitted for brevity, see the source of this page
    templateSelection: formatPrincipalSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});



function setToCancel(docno) {
    Swal.fire({
        title: 'Batalkan Pengajuan Delivery Order?',
        text: "Status dokumen akan diubah menjadi Cancel",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/sales/postsales/updateStatusSuratJalan',
                type: 'POST',
                data: { docno: docno, status: 'C' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi Cancel'
                        }).then(() => {
                            reload_tableSuratJalanTrx()
                            reload_tableSuratJalanApprvTrx()
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


function setToApproved(docno) {
    Swal.fire({
        title: 'Set SuratJalan menjadi Approve?',
        text: "Status dokumen akan diubah menjadi Approve",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/sales/postsales/updateStatusSuratJalan',
                type: 'POST',
                data: { docno: docno, status: 'A' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi Approve'
                        }).then(() => {
                            reload_tableSuratJalanTrx()
                            reload_tableSuratJalanApprvTrx()
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

function setToDisapproved(docno) {
    Swal.fire({
        title: 'Set SuratJalan menjadi Disapprove?',
        text: "Status dokumen akan diubah menjadi Disapprove",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/sales/postsales/updateStatusSuratJalan',
                type: 'POST',
                data: { docno: docno, status: 'F' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi Disapprove'
                        }).then(() => {
                            reload_tableSuratJalanTrx()
                            reload_tableSuratJalanApprvTrx()
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


var defaultInitialDO = '';
var defaultInitialCustDO = '';
$("#docnodo").select2({
    placeholder: "Choose Your DO",
    dropdownParent: $('#modalDetailSuratJalan'),
    allowClear: true,
    width:'100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_do',
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
                _paramglobal_: defaultInitialDO,
                _paramglobalcust_: defaultInitialCustDO,
                _parameterx_: defaultInitialDO,
                term: params.term,
            };
        },
        processResults: function (data, params) {
            // var searchTerm = $("#idbarang").data("select2").$dropdown.find("input").val();
            // if (data.items.length === 1 && data.items[0].text === searchTerm) {
            //     var option = new Option(data.items[0].nmbarang, data.items[0].idbarang, true, true);
            //     $('#idbarang').append(option).trigger('change').select2("close");
            //     // manually trigger the `select2:select` event
            //     $('#idbarang').trigger({
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

        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatDO, // omitted for brevity, see the source of this page
    templateSelection: formatDOSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    // $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    // $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $("#batch").val(null).trigger('change');
});

/* Format Group */
function formatDO(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.docno +"   <i class='fa fa-circle-o'></i>   "+ repo.keterangan +"</div>";
    return markup;
}
function formatDOSelection(repo) {
    return repo.keterangan || repo.text;
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
            var docdate = $('#docdate').val() || '';
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
                docdate: docdate // Tambahkan docdate ke parameter
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
    setJtsValue('[name="kurs"]', convertToDbNumber(data.kurs));

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



function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}




$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});





/* TABLE PO DETAIL */
function tableSuratJalanDetail(){
        /* Tabel PO Detail */
    var initTable = function () {
        var table = $('#tabsuratjalandtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "language":  languageDatatable(),
            "paging": false,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter":true,
            "iDisplayLength": -1,
            "ajax": {
                "url": HOST_URL + 'sales/postsales/list_tmp_suratjalan_dtl',
                "type": "POST",
                "data": function(data) {
                    //data.searchfilter = $('#searchitem').val()+'';
                    //data.idbarang = $('#idbarang').val()+'';
                    //data.idposition = $('#idposition').val()+'';
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
                    "targets": 0,
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center",
                    "render": function (data, type, row) {
                        // row[1] = kolom ID (ID Tax)
                        return '<input type="checkbox" class="row-check" value="' + row[0] + '">';
                    }
                },
                {
                    "targets": [ -1 ],
                    "orderable": false
                }
            ]
        });

        $('#tabsuratjalandtl tbody').on('click', 'tr', function(e) {
            // Cegah jika yang diklik adalah checkbox itu sendiri (untuk menghindari double trigger)
            if ($(e.target).is('input[type="checkbox"]')) {
                return;
            }
            
            // Cari checkbox di dalam baris ini
            var checkbox = $(this).find('input[type="checkbox"].row-check');
            
            // Toggle status checkbox
            checkbox.prop('checked', !checkbox.prop('checked'));
            
            // Trigger event change jika diperlukan
            checkbox.trigger('change');
        });
    }

    return initTable();

}


function reload_table_suratjalan_dtl()
{
    var table = $('#tabsuratjalandtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}



// CHECK ALL
$('#tabsuratjalandtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tabsuratjalandtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tabsuratjalandtl tbody').on('change', '.row-check', function () {
    const total = $('#tabsuratjalandtl tbody .row-check').length;
    const checked = $('#tabsuratjalandtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tabsuratjalandtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});
function getSelectedSuratJalanDetail(){
    return $('#tabsuratjalandtl tbody .row-check:checked')
        .map(function () {
            return $(this).val();
        }).get();
}

function getCheckedDetailIds(){
    let ids = [];
    $('.row-check:checked').each(function(){
        ids.push($(this).val());
    });
    return ids;
}



function setSelect2Ajax(selector, value, text) {
    if (!value) return;

    let option = new Option(text || value, value, true, true);
    $(selector).append(option).trigger('change');
}

let currentEditId = null;
function btnUpdateDetail(){
    const ids = getCheckedDetailIds();
    console.log('IDS:', ids);

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih satu data yang akan diupdate'
        });
        return;
    }

    if(ids.length > 1){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Update hanya boleh satu data'
        });
        return;
    }

    const id = ids[0];

    $.ajax({
        url: HOST_URL + 'sales/postsales/get_suratjalan_detail',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                $('#idurut').val(res.data.idurut);
                $('#uniqueid').val(res.data.uniqueid);
                // $('#description').val(res.data.description);
                // $('#descriptionpo').val(res.data.descriptionpo);
                $('#docno').val(res.data.docno);
                $('#docnodomodal').val(res.data.docnodo);
                $('[name="docnodo"]').val(res.data.docnodo);
                // $('#docnosjmodal').val(res.data.docnosj);
                $('#idbarang').val(res.data.idbarang);
                $('#nmbarang').val(res.data.nmbarang);
                $('#unit').val(res.data.unit);
                setJtsValue('[name="qty"]', convertToDbNumber(res.data.qty));
                // setJtsValue('[name="qtybonus"]', convertToDbNumber(res.data.qtybonus));
                // setJtsValue('[name="harga"]', convertToDbNumber(res.data.harga));
                // setJtsValue('[name="volitem"]', convertToDbNumber(res.data.volitem));
                // setJtsValue('[name="biaya"]', convertToDbNumber(res.data.biaya));
                // setJtsValue('[name="biaya2"]', convertToDbNumber(res.data.biaya2));
                // setJtsValue('[name="multidisc"]', convertToDbNumber(res.data.multidisc));
                // setJtsValue('[name="nilai"]', convertToDbNumber(res.data.nilai));


                currentEditId = res.data.idurut;
                // $('#qty').val(res.data.qty);
                // $('#qtybonus').val(res.data.qtybonus);
                // $('#harga').val(res.data.harga);
                // $('#multidisc').val(res.data.multidisc);

                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + res.data.idgudang,
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].nmlocation, datax.items[0].idlocation, true, true);
                    $('[name="idgudang"]').append(option).trigger('change')

                    // manually trigger the `select2:select` event
                    $('[name="idgudang"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });
                // setSelect2Ajax('#idbarang', res.data.idbarang, res.data.idbarang);

                $('#modalUpdateSuratJalanLabel').text('Update SuratJalan Detail');
                $('#modalUpdateSuratJalan').modal('show');

            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Data tidak ditemukan'
                });
            }
        }
    });
}


var defaultInitialLocation = '';
$("#idgudang").select2({
    placeholder: " -- Pilih Gudang Asal -- ",
    allowClear: true,
    width: '100%',
    dropdownParent: $('#modalUpdateSuratJalan'),
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

// Reset currentEditId ketika modal ditutup
$('#modalUpdateSuratJalan').on('hidden.bs.modal', function () {
    currentEditId = null;
});


function btnDeleteDetail(){
    const ids = getCheckedDetailIds();

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih data yang akan dihapus'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin hapus ' + ids.length + ' data terpilih?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if(!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'sales/postsales/delete_suratjalan_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json',
            success: function(res){
                if(res.status){

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $('#tabsuratjalandtl').DataTable().ajax.reload(null,false);
                    documentReadable()

                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Gagal hapus data'
                    });
                }
            }
        });

    });
}


$('#checkAllDetail').on('change', function(){
    $('.row-check').prop('checked', this.checked);
});

// auto uncheck checkAll jika salah satu dilepas
$(document).on('change','.row-check', function(){
    if(!this.checked){
        $('#checkAllDetail').prop('checked', false);
    }
});

$('#formPOMasters').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        iddept: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        docdate: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        estdate: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        // njurnal: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         },
        //     }
        // },
        fjurnal: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },

    },
    excluded: [':disabled']
});
$('#formSuratJalandetail').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        onhand: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        descriptionm: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },

    },
    excluded: [':disabled']
});


function saveSuratJalanDetail() {

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan data SuratJalan Detail?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        let formData = new FormData(document.getElementById('formSuratJalanDetail'));
        formData.append('docdate', $('#docdate').val());
        formData.append('cabang', $('#cabang').val());
        // formData.append('delivdate', $('#delivdate').val());
        // formData.append('jthtempo', convertToDbNumber($('#jthtempo').val()));
        formData.append('kdcustomer', $('#kdcustomer').val());
        formData.append('kdcustomerdeliv', $('#kdcustomerdeliv').val());
        formData.append('kdsalesman', $('#kdsalesman').val());
        // formData.append('isinclusive', $('#isinclusive').is(':checked') ? 'YES' : 'NO');
        // formData.append('isopenprice', $('#isopenprice').is(':checked') ? 'YES' : 'NO');
        formData.append('alamatcustomerdeliv', $('#alamatcustomerdeliv').val());
        formData.append('alamatcustomer', $('#alamatcustomer').val());
        formData.append('gradecustomer', $('#gradecustomer').val());
        // formData.append('nofaktur', $('#nofaktur').val());
        // formData.append('idtax', $('#idtax').val());
        // formData.append('currcode', $('#currcode').val());
        // formData.append('kurs', convertToDbNumber($('#kurs').val()));
        // formData.append('biayavol', convertToDbNumber($('#biayavol').val()));
        // formData.append('biayavol2', convertToDbNumber($('#biayavol').val()));
        // formData.append('alamatkirim', $('#alamatkirim').val());
        formData.append('keterangan', $('#keterangan').val());
        // formData.append('carabayar', $('#carabayar').val());
        // formData.append('estpakai', $('#estpakai').val());

        // docno gabungan (lebih aman pakai hidden header)
        formData.set('docno', $('#prefix').val() + '/' + $('#infix').val() + '/' + $('#sufix').val());
        // convert qty ke numeric DB
        let qty = $('#qty').val();
        // let qtybonus = $('#qtybonus').val();
        // let harga = $('#harga').val();
        // let multidisc = $('#multidisc').val();
        // let nilai = $('#nilai').val();
        // let volitem = $('#volitem').val();
        // let biaya = $('#biaya').val();
        // let biaya2 = $('#biaya2').val();
        formData.set('qty', convertToDbNumber(qty));
        // formData.set('qtybonus', convertToDbNumber(qtybonus));
        // formData.set('harga', convertToDbNumber(harga));
        // formData.set('multidisc', convertToDbNumber(multidisc));
        // formData.set('nilai', convertToDbNumber(nilai));
        // formData.set('volitem', convertToDbNumber(volitem));
        // formData.set('biaya', convertToDbNumber(biaya));
        // formData.set('biaya2', convertToDbNumber(biaya2));
        // formData.set('description', $('#description').val());
        formData.set('uniqueid', $('#uniqueid').val());
        // formData.set('idprincipal', $('#idprincipal').val());
        formData.set('idgudang', $('#idgudang').val());
        // formData.set('idspec', $('#idspec').val());
        // formData.set('docnosj', $('#docnosj').val());
        // formData.set('docnodo', $('#docnodo').val());
        // formData.set('docnopo', $('#docnopo').val());
        let docnodo = $('#docnodo').val();

        // fallback kalau di modal update
        if (!docnodo) {
            docnodo = $('#docnodomodal').val();
        }

        formData.set('docnodo', docnodo);
        
        // formData.set('descriptionpo', convertToDbNumber(qty));

        $.ajax({
            url: HOST_URL + 'sales/postsales/saveSuratJalanDetail',
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
                $('#modalUpdateSuratJalan').modal('hide');
                $('#modalDetailSuratJalan').modal('hide');
                $('#formSuratJalanUpdate')[0].reset();
                reload_table_suratjalan_dtl();
                documentReadable()
                $('#formSuratJalanDetail')[0].reset();
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



function formatPrincipal(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idprincipal +"   <i class='fa fa-circle'></i>   "+ repo.nmprincipal +"  </div>";
    return markup;
}
function formatPrincipalSelection(repo) {
    return repo.nmprincipal || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idprincipal").select2({
    placeholder: "Ketik/Pilih Principal",
    allowClear: true,
    // dropdownParent: $('#modalUpdateLPB'),
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_principal',
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
    templateResult: formatPrincipal, // omitted for brevity, see the source of this page
    templateSelection: formatPrincipalSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});

function btnInputDetail() {

    $('#formSuratJalanDetail')[0].reset();

    
    // 🔹 Clear select2
    // $('#docnopo').val(null).trigger('change');
    // $('#idbarang').val(null).trigger('change');
    $('#docnodo').val(null).trigger('change');
    // $('#docnosj').val(null).trigger('change');
    

    // Jika ada select2 lain, lakukan hal sama
    // $('#selectlain').val(null).trigger('change');

    $('#idurut').val(''); // pastikan id kosong (mode insert)

    $('#modalDetailSuratJalanLabel').text('Tambah Item Detail');
    $('#modalDetailSuratJalan').modal('show');
}




function formatCustomer(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdcustomer +"   <i class='fa fa-circle'></i>   "+ repo.nmcustomer + " </div>";
    return markup;
}
function formatCustomerSelection(repo) {
    return repo.nmcustomer || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#kdcustomer").select2({
    placeholder: "Ketik/Pilih Customer",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_customer',
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
    templateResult: formatCustomer, // omitted for brevity, see the source of this page
    templateSelection: formatCustomerSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    if (e.params && e.params.data) {
        var selectedData = e.params.data;
        
        $("#alamatcustomer").val(selectedData.alamat_kantor || '').prop('readonly', true);
        defaultInitialCustDO = selectedData.kdcustomer
        $("#gradecustomer").val(selectedData.grade || '').prop('readonly', true);
        $("#jthtempo").val(selectedData.jthtempo || '').prop('readonly', true);
        // $("#phone").val(selectedData.phone || '').prop('disabled', true);
    }
});


// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#kdcustomerdeliv").select2({
    placeholder: "Ketik/Pilih Customer",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_customer',
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
    templateResult: formatCustomer, // omitted for brevity, see the source of this page
    templateSelection: formatCustomerSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    if (e.params && e.params.data) {
        var selectedData = e.params.data;
        
        $("#alamatcustomerdeliv").val(selectedData.alamat_kantor || '').prop('disabled', true);
        // $("#gradecustomerdeliv").val(selectedData.grade || '').prop('disabled', true);
        // $("#phone").val(selectedData.phone || '').prop('disabled', true);
    }
});


let currentKodeSuffix = '';

$('#cabang').on('change', function () {
    if (skipRoleChange) return; // skip

    let idbranch = $(this).val();

    if(idbranch){
        $.ajax({
                url: HOST_URL + '/sales/postsales/getBranchInfoSuratJalan',
                method: 'GET',
                data: { idbranch: idbranch },
                dataType: 'json',
                success: function (res) {
                    if (!res.success) {
                        Swal.fire('Error', res.message, 'warning');
                        return;
                    }

                    currentKodeSuffix = res.kode_suffix; // PT / PA / PB
                    $('#infix').val(res.infix);          // YYMM
                    var prefix = res.prefix;
                    $('#prefix').val(prefix);             // default
                    $('#sufix').val(currentKodeSuffix + '0001');

                    var infix = (res.infix || '').toString();
                    if (infix.length === 4) {
                        $('#docdate').prop('disabled', false);
                        var yy = infix.substring(0,2);
                        var mm = infix.substring(2,4);
                        var year = 2000 + parseInt(yy,10);
                        var month = parseInt(mm,10) - 1; // moment month index

                        // Gunakan logindate dari response sebagai default
                        var logindate = res.logindate ? moment(res.logindate, 'DD-MM-YYYY') : moment();
                        
                        // Pastikan logindate dalam range bulan infix
                        var startDate = moment([year, month, 1]);
                        var endDate = moment(startDate).endOf('month');
                        
                        // Jika logindate dalam range, gunakan logindate,否则 gunakan startDate
                        var selectedDate = logindate.isBetween(startDate, endDate, 'day', '[]') 
                            ? logindate 
                            : startDate;

                        var $el = $('#docdate');
                        var drp = $el.data('daterangepicker');

                        if (drp) {
                            // update limits & selected date
                            drp.minDate = startDate;
                            drp.maxDate = endDate;
                            drp.setStartDate(selectedDate);
                            drp.setEndDate(selectedDate);
                        } else {
                            // fallback: (re)initialize with limits
                            $el.daterangepicker({
                                autoUpdateInput: false,
                                singleDatePicker: true,
                                showDropdowns: true,
                                startDate: selectedDate,
                                minDate: startDate,
                                maxDate: endDate,
                                locale: { format: 'DD-MM-YYYY' },
                                cancelLabel: 'Clear'
                            });
                            // rebind handlers
                            $el.on('apply.daterangepicker', function(ev, picker) {
                                $(this).val(picker.startDate.format('DD-MM-YYYY'));
                                // Trigger change untuk update kurs
                                $(this).trigger('change');
                            });
                            $el.on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                                // Trigger change untuk reset kurs
                                $(this).trigger('change');
                            });
                        }

                        // isi input dengan selectedDate
                        $el.val(selectedDate.format('DD-MM-YYYY'));
                    }

                    $('#docno').val(
                        prefix + '/' + res.infix + '/' + currentKodeSuffix + '0001'
                    );

                    // Ambil docdate dari field
                    var docdate = $('#docdate').val() || '';
                }
            });
    }
    
});


$('#prefix').on('blur', function () {
    let prefix = $(this).val().toUpperCase();
    let infix  = $('#infix').val();

    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + '/sales/postsales/getNextSuffixSuratJalan',
        method: 'GET',
        data: {
            prefix: prefix,
            infix: infix,
            kode_suffix: currentKodeSuffix
        },
        dataType: 'json',
        success: function (res) {
            if (!res.success) {
                Swal.fire('Error', res.message, 'warning');
                return;
            }

            $('#sufix').val(res.suffix);
            $('#docno').val(
                prefix + '/' + infix + '/' + res.suffix
            );
        }
    });
});




var defaultInitialBranch = '';
$("#cabang").select2({
    placeholder: "Type/ Choose your Branch",
    allowClear: true,
    width: '100%',
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_branchjob',
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
                _paramglobal_: defaultInitialBranch,
                _parameterx_: defaultInitialBranch,
                term: params.term,
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#cabang").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmbranch, data.items[0].idbranch, true, true);
                $('#cabang').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#cabang').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            }
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
    templateResult: formatBranch, // omitted for brevity, see the source of this page
    templateSelection: formatBranchSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
    //var table = $('#tsearchitem');
    //table.DataTable().ajax.reload(); //reload datatable ajax
    ///table.append().search( $(this).val() ).draw();
    //$('#filter').modal('hide');
});
/* Format Group */
function formatBranch(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idbranch +"   <i class='fa fa-circle-o'></i>   "+ repo.nmbranch +"</div>";
    return markup;
}
function formatBranchSelection(repo) {
    return repo.nmbranch || repo.text;
}

var audio = document.getElementById('chatAudio');
function play(){
    audio.play()
}

function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kddept +"   <i class='fa fa-circle-o'></i>   "+ repo.nmdept +"  </div>";
    return markup;
}

function formatNewdeptSelection(repo) {
    return repo.nmdept || repo.text;
}
var defaultInitialNewDept = '';
$("#iddept").select2({
    placeholder: "Pilih Bagian",
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
}).on("select2:selecting", function () {
});

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





    tableSuratJalanTrx();
    // tableSuratJalanApprvTrx();
    // tablePOApprvTrx();
    tableSuratJalanDetail();
    //read_qrcode();
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
    // if ($('[name="type"]').val() === 'EDIT') {
    //     documentReadable();
    // }
    //console.log($('[name="type"]').val());
    // if ($('[name="typeform"]').val() === 'INPUT' || $('[name="typeform"]').val() === 'UPDATE' || $('[name="typeform"]').val() === 'DELETE' ) {
        documentReadable();
    // }
    $("#loadMe").modal("hide");




});