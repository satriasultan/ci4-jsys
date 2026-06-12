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

function tableNDKTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablendkTrx');
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
                "url": HOST_URL + 'arap/transaksi/list_ndk',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namasupplier = $('#namasupplier').val();
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

function reload_tableNDKTrx()
{
    var table = $('#tablendkTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function(){ //button filter event click
    var table = $('#tablendkTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tablendkTrx');
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
        url: HOST_URL + 'arap/transaksi/showing_ndktemp',
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
            defaultInitialPO = prefixParts[2].substring(0, 2);


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_cust_and_supplier' + '?var=' + json.dataTables.items[0].kdsupplier,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {

            // Tambahkan data alamat dan phone ke object
                var supplierData = datax.items[0];
                supplierData.alamat = json.dataTables.items[0].alamatsupplier;
                // supplierData.phone = data.phone;
                
                // create the option dan simpan data lengkap
                var option = new Option(
                    supplierData.nama,
                    supplierData.kode,
                    true,
                    true
                );
                
                $(option).data('supplier-data', supplierData); // Simpan data lengkap
                
                $('[name="kdsupplier"]').append(option).trigger('change');
                
                // Set alamat dan phone langsung
                $("#alamatsupplier").val(json.dataTables.items[0].alamatsupplier).prop('readonly', true);
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
                let anu =  $('[name="cabang"]').val();
                console.info(anu)

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
                url: HOST_URL + 'api/globalmodule/list_tax' + '?var=' + json.dataTables.items[0].idtax,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmtax, datax.items[0].idtax, true, true);
                $('[name="idtax"]').append(option).trigger('change');
                let anu2 =  $('[name="idtax"]').val();
                console.info(anu2)


                // manually trigger the `select2:select` event
                $('[name="idtax"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].perkiraanarap,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                $('[name="perkiraanarap"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="perkiraanarap"]').trigger({
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

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].perkiraanlawan,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                $('[name="perkiraanlawan"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="perkiraanlawan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

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
                setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
                $('[name="kurs"]').prop('readonly', false);
                // $("#phone").val(data.phone).prop('readonly', true);
            });
            skipRoleChange = true;
            $('[name="docdate"]').val(json.dataTables.items[0].docdate);
            // $('[name="senddate"]').val(json.dataTables.items[0].senddate);
            setJtsValue('[name="jthtempo"]', convertToDbNumber(json.dataTables.items[0].jthtempo));
            // setJtsValue('[name="biayavol"]', convertToDbNumber(json.dataTables.items[0].biayavol));
            // setJtsValue('[name="biayavol2"]', convertToDbNumber(json.dataTables.items[0].biayavol2));
            setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
            $('[name="isinclusive"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].isinclusive || '')).toUpperCase() === 'YES'
            );
            // $('[name="jthtempo"]').val(json.dataTables.items[0].jthtempo);
            $('[name="alamatsupplier"]').val(json.dataTables.items[0].alamatsupplier);
            // $('[name="alamatkirim"]').val(json.dataTables.items[0].alamatkirim);
            // $('[name="isinclusive"]').val(json.dataTables.items[0].isinclusive);
            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);
            // $('[name="hpdate"]').val(json.dataTables.items[0].hpdate);
            $('[name="dk"]').val(json.dataTables.items[0].dk.trim()).trigger('change');
            // $('[name="docnohp"]').val(json.dataTables.items[0].docnohp).prop('readonly',true);

            setJtsValue('[name="dpp"]', convertToDbNumber(json.dataTables.items[0].dpp));
            setJtsValue('[name="jumlahpajak"]', convertToDbNumber(json.dataTables.items[0].jumlahpajak));
            setJtsValue('[name="total"]', convertToDbNumber(json.dataTables.items[0].total));
            // $('[name="estpakai"]').val(json.dataTables.items[0].estpakai);
            

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
    dropdownParent: $('#modalUpdateNDK'),
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


var defaultInitialLocation = '';
$("#idgudang").select2({
    placeholder: " -- Pilih Gudang Asal -- ",
    allowClear: true,
    width: '100%',
    dropdownParent: $('#modalUpdateNDK'),
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


function setToApproved(docno) {
    Swal.fire({
        title: 'Set Retur Beli menjadi Approve?',
        text: "Status dokumen akan diubah menjadi Approve",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + 'arap/transaksi/updateStatusNDK',
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
                            reload_tableNDKTrx()
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
        title: 'Set Retur Beli menjadi Disapprove?',
        text: "Status dokumen akan diubah menjadi Disapprove",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + 'arap/transaksi/updateStatusNDK',
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
                            reload_tableNDKTrx()
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


var defaultInitialPO = '';
$("#docnopo").select2({
    placeholder: "Choose Your PO",
    allowClear: true,
    dropdownParent: $('#modalDetailNDK'),
    width:'100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_po',
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
                _paramglobal_: defaultInitialPO,
                _parameterx_: defaultInitialPO,
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
    templateResult: formatPO, // omitted for brevity, see the source of this page
    templateSelection: formatPOSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    // $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    // $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $("#batch").val(null).trigger('change');
});

/* Format Group */
function formatPO(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.docno +"   <i class='fa fa-circle-o'></i>   "+ repo.keterangan +"</div>";
    return markup;
}
function formatPOSelection(repo) {
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
            params.page = params.page || 1;

            let results = data.items.map(function(item){
                return {
                    id: item.idtax,       // <-- INI WAJIB
                    text: item.nmtax,     // <-- INI WAJIB
                    idtax: item.idtax,
                    nmtax: item.nmtax
                };
            });

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
    
    hitungPajak();

}).on("select2:clear", function (e) {

    hitungPajak();

});



function hitungPajak() {

    let dpp = $('#dpp').val().replace(/,/g,'');
    let idtax = $('#idtax').val();
    let isinclusive = $('#isinclusive').is(':checked');

    if(!dpp) dpp = 0;

    if(isinclusive){    
        $('#jumlahpajak').val('0');
        $('#total').val(parseFloat(dpp).toLocaleString());
        return;
    }

    if(!idtax){
        $('#jumlahpajak').val('0');
        $('#total').val(dpp);
        return;
    }

    $.ajax({
        url: HOST_URL + 'api/globalmodule/get_tax_percent',
        type: 'POST',
        data: {idtax:idtax},
        dataType:'json',
        success:function(res){

            let percent = res.percent || 0;

            let jumlahPajak = dpp * percent / 100;
            let total = parseFloat(dpp) + jumlahPajak;

            $('#jumlahpajak').val(jumlahPajak.toLocaleString());
            $('#total').val(total.toLocaleString());

        }
    });

}
$('#isinclusive').on('change', function(){
    if($(this).is(':checked')){
        $('#idtax').prop('disabled', true);
    } else {
        $('#idtax').prop('disabled', false);
    }
    hitungPajak();
});

$('#dpp').on('keyup change', function(){
    hitungPajak();
});


function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}




$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});





/* TABLE PO DETAIL */
function tableNDKDetail(){
        /* Tabel PO Detail */
    var initTable = function () {
        var table = $('#tabndkdtl');
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
                "url": HOST_URL + 'arap/transaksi/list_tmp_ndk_dtl',
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
    }

    return initTable();

}


function reload_table_ndk_dtl()
{
    var table = $('#tabndkdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}



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

let currentEditId = null;
$(document).on('input', '.form-control', function () {
    // Jika sedang dalam mode edit, gunakan currentEditId
    if (currentEditId) {
        // Baca nilai qty, harga, dan multidisc
        let qty = parseFloat($('#qty').val().replace(/,/g, '')) || 0;
        let harga = parseFloat($('#harga').val().replace(/,/g, '')) || 0;
        let multidisc = parseFloat($('#multidisc').val().replace(/,/g, '')) || 0;
        
        // Hitung nilai awal (qty * harga)
        let nilaiAwal = qty * harga;
        
        // Hitung diskon
        let diskon = (nilaiAwal * multidisc) / 100;
        
        // Hitung nilai akhir setelah diskon
        let nilaiAkhir = nilaiAwal - diskon;
        
        // Format ke en-US: separator ribuan = koma, desimal = titik
        $('#nilai').val(nilaiAkhir.toLocaleString('en-US', {
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2
        }));
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
$('#formNDKdetail').bootstrapValidator({
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


function formatSupplier(repo) {

    if (repo.loading) {
        return repo.text;
    }

    return `
        <div>
            <strong>${repo.kode}</strong> - ${repo.nama}
            <br>
            <small>${repo.tipe}</small>
        </div>
    `;
}

function formatSupplierSelection(repo) {
    return repo.nama || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#kdsupplier").select2({
    placeholder: "Ketik/Pilih Supplier",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_cust_and_supplier',
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
    templateResult: formatSupplier, // omitted for brevity, see the source of this page
    templateSelection: formatSupplierSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    if (e.params && e.params.data) {
        var selectedData = e.params.data;
        
        $("#alamatsupplier").val(selectedData.alamat || '');
        $("#jthtempo").val(selectedData.jthtempo || '');
        // $("#phone").val(selectedData.phone || '').prop('disabled', true);
    }
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



let currentKodeSuffix = '';

$('#cabang').on('change', function () {
    if (skipRoleChange) return; // skip

    let idbranch = $(this).val();

    if(idbranch){
        $.ajax({
                url: HOST_URL + 'arap/transaksi/getBranchInfoNDK',
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
                    $('#prefix').val('NTD');             // default
                    $('#sufix').val(currentKodeSuffix + '0001');
                    defaultInitialPO = currentKodeSuffix

                    var infix = (res.infix || '').toString();
                    if (infix.length === 4) {
                        $('#docdate').prop('disabled', false);
                        var yy = infix.substring(0,2);
                        var mm = infix.substring(2,4);
                        var year = 2000 + parseInt(yy,10);
                        var month = parseInt(mm,10) - 1; // moment month index

                        var today = moment();

                        var startDate = moment([year, month, 1]);
                        var endDate = moment(startDate).endOf('month');

                        var $el = $('#docdate');
                        var drp = $el.data('daterangepicker');

                        if (drp) {
                            // update limits & selected date
                            drp.minDate = startDate;
                            drp.maxDate = endDate;
                            drp.setStartDate(startDate);
                            drp.setEndDate(startDate);
                        } else {
                            // fallback: (re)initialize with limits
                            $el.daterangepicker({
                                autoUpdateInput: false,
                                singleDatePicker: true,
                                showDropdowns: true,
                                startDate: today,
                                minDate: startDate,
                                maxDate: endDate,
                                locale: { format: 'YYYY-MM-DD' },
                                cancelLabel: 'Clear'
                            });
                            // rebind handlers jika perlu (apply/cancel)
                            $el.on('apply.daterangepicker', function(ev, picker) {
                                $(this).val(picker.startDate.format('YYYY-MM-DD'));
                            });
                            $el.on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                            });
                        }

                        // isi input langsung (opsional)
                        $el.val(today.format('YYYY-MM-DD'));
                    }

                    $('#docno').val(
                        'NTD/' + res.infix + '/' + currentKodeSuffix + '0001'
                    );
                }
            });
    }
    
});


$('#prefix').on('blur', function () {
    let prefix = $(this).val().toUpperCase();
    let infix  = $('#infix').val();

    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + 'arap/transaksi/getNextSuffixNDK',
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



$('#dk').on('change', function () {
    let jenis = $(this).val().toUpperCase();
    let infix  = $('#infix').val();
    let prefix = '';
    if (jenis === 'DEBIT') {
        prefix = 'NTD';
    } else if (jenis === 'KREDIT') {
        prefix = 'NTK';
    }


    if (!prefix || !infix || !currentKodeSuffix) return;


    $.ajax({
        url: HOST_URL + 'arap/transaksi/getNextSuffixNDK',
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




function generateDocnoJurnal() {
    let jenis = $('#dk').val();
    // let tanggal = $('#hpdate').val();
    let infix = $('#infix').val();
    let sufix = $('#sufix').val();

    // PREFIX
    let prefix = '';
    if (jenis === 'DEBIT') {
        prefix = 'NTD';
    } else if (jenis === 'KREDIT') {
        prefix = 'NTK';
    }

    // INFIX (YYMM)
    // let infix = '';
    // if (tanggal) {
    //     let date = new Date(tanggal);
    //     let year = date.getFullYear().toString().slice(-2);
    //     let month = ('0' + (date.getMonth() + 1)).slice(-2);
    //     infix = year + month;
    // }

    // FINAL DOCNO
    if (prefix && infix && sufix) {
        let docno = prefix + '/' + infix + '/' + sufix;
        $('#prefix').val(prefix);
        $('#sufix').val(sufix);
        $('#infix').val(infix);
        $('#docno').val(docno);
    }
}

$('#dk').on('change', function () {
    generateDocnoJurnal();
});

$('#sufix').on('keyup', function () {
    generateDocnoJurnal();
});


$("#perkiraanarap").select2({
        
        placeholder: "Pilih Perkiraan",
        allowClear: true,
        // maximumSelectionLength: 1,
        // multiple: false,
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
                    _paramglobal_: "",
                    _parameterx_: "",
                    term: params.term,
                };
            },
            processResults: function (data, params) {
                var searchTerm = $("#perkiraanarap").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].idcoa, true, true);
                    $('#perkiraanarap').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#perkiraanarap').trigger({
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
        templateResult: formatCoa, // omitted for brevity, see the source of this page
        templateSelection: formatCoaSelection // omitted for brevity, see the source of this page
    }).on("select2:select", function (e) {

    });

    
$("#perkiraanlawan").select2({
        
        placeholder: "Pilih Perkiraan",
        allowClear: true,
        // maximumSelectionLength: 1,
        // multiple: false,
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
                    _paramglobal_: "",
                    _parameterx_: "",
                    term: params.term,
                };
            },
            processResults: function (data, params) {
                var searchTerm = $("#perkiraanlawan").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].idcoa, true, true);
                    $('#perkiraanlawan').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#perkiraanlawan').trigger({
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
        templateResult: formatCoa, // omitted for brevity, see the source of this page
        templateSelection: formatCoaSelection // omitted for brevity, see the source of this page
    }).on("select2:select", function (e) {

    });


     function formatCoa(repo) {
        if (repo.loading) return repo.text;

        var markup  = "<div class='select2-result-repository'>";
        markup += "  <div class='select2-result-repository__title'><b>" + repo.idcoa + "</b></div>";
        markup += "  <div class='select2-result-repository__description text-muted'>" + repo.nmcoa + "</div>";
        markup += "</div>";

        return markup;
    }

    function formatCoaSelection(repo) {
        if (!repo.idcoa) return repo.text;
        return repo.idcoa + " - " + repo.nmcoa;
    }



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





    tableNDKTrx();
    // tablePOApprvTrx();
    tableNDKDetail();
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