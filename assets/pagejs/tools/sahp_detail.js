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

function tableSAHPTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablesaldoawalhpTrx');
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
                "url": HOST_URL + 'tools/settingawal/list_saldoawalhp',
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

function reload_tableSAHPTrx()
{
    var table = $('#tablesaldoawalhpTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function(){ //button filter event click
    var table = $('#tablesaldoawalhpTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tablesaldoawalhpTrx');
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
        url: HOST_URL + 'tools/settingawal/showing_saldoawalhptrx',
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
                url: HOST_URL + 'api/globalmodule/list_supplier_new' + '?var=' + json.dataTables.items[0].kdsupplier,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {

            // Tambahkan data alamat dan phone ke object
                var supplierData = datax.items[0];
                supplierData.alamat = json.dataTables.items[0].alamatsupplier;
                // supplierData.phone = data.phone;
                
                // create the option dan simpan data lengkap
                var option = new Option(supplierData.nmsupplier, supplierData.kdsupplier, true, true);
                $(option).data('supplier-data', supplierData); // Simpan data lengkap
                
                $('[name="kdsupplier"]').append(option).trigger('change').prop('disabled',true);
                
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
                $('[name="idtax"]').append(option).trigger('change').prop('disabled',true);;

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
                url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].perkiraan,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idcoa, datax.items[0].nmcoa, true, true);
                $('[name="perkiraan"]').append(option).trigger('change').prop('disabled',true);;

                // manually trigger the `select2:select` event
                $('[name="perkiraan"]').trigger({
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
                var option = new Option(datax.items[0].idcoa, datax.items[0].nmcoa, true, true);
                $('[name="perkiraanlawan"]').append(option).trigger('change').prop('disabled',true);;

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
                
                $('[name="currcode"]').append(option).trigger('change').prop('disabled',true);;
                
                // Set alamat dan phone langsung
                setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
                $('[name="kurs"]').prop('readonly', true);
                // $("#phone").val(data.phone).prop('readonly', true);
            });
            skipRoleChange = true;
            $('[name="docdate"]').val(json.dataTables.items[0].docdate).prop('disabled',true);
            // $('[name="senddate"]').val(json.dataTables.items[0].senddate);
            setJtsValue('[name="jthtempo"]', convertToDbNumber(json.dataTables.items[0].jthtempo));
            // setJtsValue('[name="biayavol"]', convertToDbNumber(json.dataTables.items[0].biayavol));
            // setJtsValue('[name="biayavol2"]', convertToDbNumber(json.dataTables.items[0].biayavol2));
            setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
            $('[name="ispajak"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].ispajak || '')).toUpperCase() === 'YES'
            );
            $('[name="jthtempo"]').val(json.dataTables.items[0].jthtempo).prop('disabled',true);
            $('[name="alamatsupplier"]').val(json.dataTables.items[0].alamatsupplier).prop('disabled',true);
            // $('[name="alamatkirim"]').val(json.dataTables.items[0].alamatkirim);
            $('[name="ispajak"]').val(json.dataTables.items[0].ispajak).prop('disabled',true);
            $('[name="dpp"]').val(json.dataTables.items[0].dpp).prop('disabled',true);
            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan).prop('disabled',true);
            $('[name="hpdate"]').val(json.dataTables.items[0].hpdate).prop('disabled',true);
            $('[name="jnshp"]').val(json.dataTables.items[0].jnshp.trim()).trigger('change').prop('disabled',true);
            $('[name="docnohp"]').val(json.dataTables.items[0].docnohp).prop('readonly',true);

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
    dropdownParent: $('#modalUpdateSAHP'),
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
    dropdownParent: $('#modalUpdateSAHP'),
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
                url: HOST_URL + 'tools/settingawal/updateStatusSAHP',
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
                            reload_tableSAHPTrx()
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
                url: HOST_URL + 'tools/settingawal/updateStatusSAHP',
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
                            reload_tableSAHPTrx()
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
    dropdownParent: $('#modalDetailSAHP'),
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
    
    hitungPajak();

}).on("select2:clear", function (e) {

    hitungPajak();

});



function hitungPajak() {

    let dpp = $('#dpp').val().replace(/,/g,'');
    let idtax = $('#idtax').val();
    let isPajak = $('#ispajak').is(':checked');

    if(!dpp) dpp = 0;

    if(isPajak){    
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
$('#ispajak').on('change', function(){
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
function tableSAHPDetail(){
        /* Tabel PO Detail */
    var initTable = function () {
        var table = $('#tabsaldoawalhpdtl');
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
                "url": HOST_URL + 'tools/settingawal/list_tmp_saldoawalhp_dtl',
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


function reload_table_saldoawalhp_dtl()
{
    var table = $('#tabsaldoawalhpdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}



// CHECK ALL
$('#tabsaldoawalhpdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tabsaldoawalhpdtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tabsaldoawalhpdtl tbody').on('change', '.row-check', function () {
    const total = $('#tabsaldoawalhpdtl tbody .row-check').length;
    const checked = $('#tabsaldoawalhpdtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tabsaldoawalhpdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});
function getSelectedSAHPDetail(){
    return $('#tabsaldoawalhpdtl tbody .row-check:checked')
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

function resetSelect2Fields() {
    // Reset idprincipal
    $('[name="idprincipal"]').val(null).trigger('change');
    $('[name="idprincipal"]').empty();
    
    // Reset idgudang
    $('[name="idgudang"]').val(null).trigger('change');
    $('[name="idgudang"]').empty();
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
    resetSelect2Fields();

    $.ajax({
        url: HOST_URL + 'tools/settingawal/get_saldoawalhp_detail',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                $('#idurut').val(res.data.idurut);
                $('#uniqueid').val(res.data.uniqueid);
                $('#descriptionpp').val(res.data.descriptionpp);
                $('#descriptionpo').val(res.data.descriptionpo);
                $('#docno').val(res.data.docno);
                $('#docnopomodal').val(res.data.docnopo);
                $('#idbarang').val(res.data.idbarang);
                $('#nmbarang').val(res.data.nmbarang);
                $('#idspec').val(res.data.idspec);
                $('#unit').val(res.data.unit);
                setJtsValue('[name="qty"]', convertToDbNumber(res.data.qty));
                setJtsValue('[name="qtybonus"]', convertToDbNumber(res.data.qtybonus));
                setJtsValue('[name="harga"]', convertToDbNumber(res.data.harga));
                setJtsValue('[name="volitem"]', convertToDbNumber(res.data.volitem));
                setJtsValue('[name="biaya"]', convertToDbNumber(res.data.biaya));
                setJtsValue('[name="biaya2"]', convertToDbNumber(res.data.biaya2));
                setJtsValue('[name="multidisc"]', convertToDbNumber(res.data.multidisc));
                setJtsValue('[name="nilai"]', convertToDbNumber(res.data.nilai));


                currentEditId = res.data.idurut;
                // $('#qty').val(res.data.qty);
                // $('#qtybonus').val(res.data.qtybonus);
                // $('#harga').val(res.data.harga);
                // $('#multidisc').val(res.data.multidisc);
                // setSelect2Ajax('#idprincipal', res.data.idprincipal, res.data.idprincipal);
                // setSelect2Ajax('#idgudang', res.data.idgudang, res.data.idgudang);
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_principal' + '?var=' + res.data.idprincipal,
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].nmprincipal, datax.items[0].idprincipal, true, true);
                    $('[name="idprincipal"]').append(option).trigger('change')

                    // manually trigger the `select2:select` event
                    $('[name="idprincipal"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });

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
                // setSelect2Ajax('#docnopp', res.data.docnopp, res.data.keterangan);

                $('#modalUpdateSAHPLabel').text('Update Retur Beli Detail');
                $('#modalUpdateSAHP').modal('show');

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

// Reset currentEditId ketika modal ditutup
$('#modalUpdateSAHP').on('hidden.bs.modal', function () {
    currentEditId = null;
});

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
            url: HOST_URL + 'tools/settingawal/delete_saldoawalhp_detail',
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

                    $('#tabsaldoawalhpdtl').DataTable().ajax.reload(null,false);
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
$('#formSAHPdetail').bootstrapValidator({
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


function saveSAHPDetail() {

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan data Retur Beli Detail?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        let formData = new FormData(document.getElementById('formSAHPDetail'));
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
            url: HOST_URL + 'tools/settingawal/saveSAHPDetail',
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
                $('#modalDetailSAHP').modal('hide');
                $('#modalUpdateSAHP').modal('hide');
                $('#formSAHPUpdate')[0].reset();
                reload_table_saldoawalhp_dtl();
                documentReadable()
                $('#formSAHPDetail')[0].reset();
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

function btnInputDetail() {

    let cabang = $('#cabang').val();

    if (!cabang || cabang.trim() === '') {
        alert('Cabang harus dipilih terlebih dahulu');
        $('#cabang').focus();
        return; // stop proses
    }

    $('#formSAHPDetail')[0].reset();

    
    // 🔹 Clear select2
    $('#docnopo').val(null).trigger('change');

    // Jika ada select2 lain, lakukan hal sama
    // $('#selectlain').val(null).trigger('change');

    $('#idurut').val(''); // pastikan id kosong (mode insert)

    $('#modalDetailSAHPLabel').text('Tambah Item Detail');
    $('#modalDetailSAHP').modal('show');
}




function formatSupplier(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdsupplier +"   <i class='fa fa-circle'></i>   "+ repo.nmsupplier + " </div>";
    return markup;
}
function formatSupplierSelection(repo) {
    return repo.nmsupplier || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#kdsupplier").select2({
    placeholder: "Ketik/Pilih Supplier",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_supplier_new',
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
        // $("#phone").val(selectedData.phone || '').prop('disabled', true);
    }
});


let currentKodeSuffix = '';

$('#cabang').on('change', function () {
    if (skipRoleChange) return; // skip

    let idbranch = $(this).val();

    if(idbranch){
        $.ajax({
                url: HOST_URL + 'tools/settingawal/getBranchInfoSAHP',
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
                    $('#prefix').val('AHP');             // default
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
                        'AHP/' + res.infix + '/' + currentKodeSuffix + '0001'
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
        url: HOST_URL + 'tools/settingawal/getNextSuffixSAHP',
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

$("#perkiraan").select2({
        
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
                var searchTerm = $("#perkiraan").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].idcoa, true, true);
                    $('#perkiraan').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#perkiraan').trigger({
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

function generateDocnoJurnal() {
    let jenis = $('#jnshp').val();
    let tanggal = $('#hpdate').val();
    let sufix = $('#sufix').val();

    // PREFIX
    let prefix = '';
    if (jenis === 'DEBIT') {
        prefix = 'SAD';
    } else if (jenis === 'KREDIT') {
        prefix = 'SAK';
    }

    // INFIX (YYMM)
    let infix = '';
    if (tanggal) {
        let date = new Date(tanggal);
        let year = date.getFullYear().toString().slice(-2);
        let month = ('0' + (date.getMonth() + 1)).slice(-2);
        infix = year + month;
    }

    // FINAL DOCNO
    if (prefix && infix && sufix) {
        let docno = prefix + '/' + infix + '/' + sufix;
        $('#docnohp').val(docno);
    }
}

$('#jnshp, #hpdate').on('change', function () {
    generateDocnoJurnal();
});

$('#hpdate').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD'));

    generateDocnoJurnal();
});

$('#sufix').on('keyup', function () {
    generateDocnoJurnal();
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





    tableSAHPTrx();
    // tablePOApprvTrx();
    // tableSAHPDetail();
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