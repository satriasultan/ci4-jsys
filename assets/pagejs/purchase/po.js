var save_method; //for save method string
var table;
var initTable;
//"use strict";

function tablePOTrx(){
    var initTable = function () {
        var table = $('#tablepoTrx');
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
                "url": HOST_URL + 'purchase/trans/list_po',
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

function reload_tablePOTrx()
{
    var table = $('#tablepoTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


function tablePOApprvTrx(){
    var initTable = function () {
        var table = $('#tablepoapprvTrx');
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
                "url": HOST_URL + 'purchase/trans/list_po_apprv',
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

function reload_tablePOApprvTrx()
{
    var table = $('#tablepoapprvTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
}

$('#btn-filter-tx').click(function(){ //button filter event click
    var table = $('#tablepoTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tablepoTrx');
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
        url: HOST_URL + 'purchase/trans/showing_potemp',
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
            defaultInitialPP = prefixParts[2].substring(0, 2);

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
            $('[name="docdate"]').val(moment(json.dataTables.items[0].docdate).format('DD-MM-YYYY'));
            $('[name="senddate"]').val(moment(json.dataTables.items[0].senddate).format('DD-MM-YYYY'));

            if (prefixParts[0]) {
                // Ambil nilai senddate yang sudah di-set
                const senddateValue = $('[name="senddate"]').val();
                setupEstpakai(prefixParts[0], senddateValue);
            }


            // $('[name="senddate"]').val(json.dataTables.items[0].senddate);
            setJtsValue('[name="jthtempo"]', convertToDbNumber(json.dataTables.items[0].jthtempo));
            setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
            $('[name="isinclusive"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].isinclusive || '')).toUpperCase() === 'YES'
            );
            // $('[name="jthtempo"]').val(json.dataTables.items[0].jthtempo);
            $('[name="alamatsupplier"]').val(json.dataTables.items[0].alamatsupplier);
            $('[name="alamatkirim"]').val(json.dataTables.items[0].alamatkirim);
            // $('[name="isinclusive"]').val(json.dataTables.items[0].isinclusive);
            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);
            $('[name="syarat"]').val(json.dataTables.items[0].syarat);

            var docnoUMB = (json.dataTables.items[0].docnoumb || '').trim();
            if(docnoUMB === ''){
                $('#btnDP').prop('disabled', false);
                $('#btnDPWrapper').attr('title','Create Down Payment');
            }else{
                $('#btnDP').prop('disabled', true);
                $('#btnDPWrapper').attr('title','PO sudah ada Down Payment');
            }

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


// ============================================================
// DOCUMENT STATUS
// ============================================================

function setToCancel(docno) {
    Swal.fire({
        title: 'Batalkan pembuatan PO?',
        text: "Pengajuan dokumen akan dibatalkan",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/purchase/trans/updateStatusPO',
                type: 'POST',
                data: { docno: docno, status: 'C' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Pengajuan dokumen berhasil dibatalkan'
                        }).then(() => {
                            reload_tablePOTrx()
                            reload_tablePOApprvTrx()
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
        title: 'Set PO menjadi Approve?',
        text: "Status dokumen akan diubah menjadi Approve",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/purchase/trans/updateStatusPO',
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
                            reload_tablePOTrx()
                            reload_tablePOApprvTrx()
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
        title: 'Set PO menjadi Disapprove?',
        text: "Status dokumen akan diubah menjadi Disapprove",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/purchase/trans/updateStatusPO',
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
                            reload_tablePOTrx()
                            reload_tablePOApprvTrx()
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


var defaultInitialPP = '';
$("#docnopp").select2({
    placeholder: "Choose Your PP",
    allowClear: true,
    dropdownParent: $('#modalDetailPO'),
    width:'100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_pp',
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
                _paramglobal_: defaultInitialPP,
                _parameterx_: defaultInitialPP,
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
    templateResult: formatPP, // omitted for brevity, see the source of this page
    templateSelection: formatPPSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    // $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    // $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $("#batch").val(null).trigger('change');
});

/* Format Group */
function formatPP(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.docno +"   <i class='fa fa-circle-o'></i>   "+ repo.keterangan +"</div>";
    return markup;
}
function formatPPSelection(repo) {
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

    if(!dpp) dpp = 0;

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

function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}


$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});


/* TABLE PO DETAIL */
function tablePODetail(){
    /* Tabel PO Detail */
    var initTable = function () {
        var table = $('#tabppdtl');
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
                "url": HOST_URL + 'purchase/trans/list_tmp_po_dtl',
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

        $('#tabppdtl tbody').on('click', 'tr', function(e) {
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


function reload_table_po_dtl()
{
    var table = $('#tabppdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#tabppdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tabppdtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tabppdtl tbody').on('change', '.row-check', function () {
    const total = $('#tabppdtl tbody .row-check').length;
    const checked = $('#tabppdtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tabppdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});

function getCheckedDetailIds(){
    let ids = [];
    $('.row-check:checked').each(function(){
        ids.push($(this).val());
    });
    return ids;
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
        url: HOST_URL + 'purchase/trans/get_po_detail',
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
                $('#docnoppmodal').val(res.data.docnopp);
                $('#idbarang').val(res.data.idbarang);
                $('#nmbarang').val(res.data.nmbarang);
                $('#multidisctype')
                    .val(
                        $.trim(res.data.multidisctype || 'NILAI')
                    )
                    .trigger('change');
                //$('#unit').val(res.data.unit);
                var idbarang = $.trim(res.data.idbarang);
                var idunit   = $.trim(res.data.unit);

                if (idbarang !== '' && idunit !== '') {

                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_unit_item',
                        data: {
                            idbarang: idbarang,
                            idunit: idunit
                        },
                        dataType: 'json'
                    }).then(function (datax) {

                        if (datax.items && datax.items.length > 0) {

                            var unitData = datax.items[0];

                            var option = new Option(
                                unitData.idunit,
                                unitData.idunit,
                                true,
                                true
                            );

                            $('#unit')
                                .empty()
                                .append(option)
                                .trigger('change');

                            // Simpan data lengkap unit
                            $(option).data('unit-data', unitData);

                        }

                    });

                }
                setJtsValue('[name="qty"]', convertToDbNumber(res.data.qty));
                setJtsValue('[name="qtybonus"]', convertToDbNumber(res.data.qtybonus));
                setJtsValue('[name="harga"]', convertToDbNumber(res.data.harga));
                // setJtsValue('[name="multidisc"]', convertToDbNumber(res.data.multidisc));
                setJtsValue('[name="nilai"]', convertToDbNumber(res.data.nilai));
                setJtsValue('[name="multidisc"]', convertToDbNumber(res.data.multidisc));


                currentEditId = res.data.idurut;
                // $('#qty').val(res.data.qty);
                // $('#qtybonus').val(res.data.qtybonus);
                // $('#harga').val(res.data.harga);
                // $('#multidisc').val(res.data.multidisc);
                // setSelect2Ajax('#idbarang', res.data.idbarang, res.data.idbarang);
                // setSelect2Ajax('#docnopp', res.data.docnopp, res.data.keterangan);

                $('#modalUpdatePOLabel').text('Update PO Detail');
                $('#modalUpdatePO').modal('show');

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
$('#modalUpdatePO').on('hidden.bs.modal', function () {
    currentEditId = null;
});

$(document).on('input', '.form-control', function () {
    // Jika sedang dalam mode edit, gunakan currentEditId
    if (currentEditId) {
        // Baca nilai qty, harga, dan multidisc
        let qty = parseFloat($('#qty').val().replace(/,/g, '')) || 0;
        let harga = parseFloat($('#harga').val().replace(/,/g, '')) || 0;
        // let multidisc = parseFloat($('#multidisc').val().replace(/,/g, '')) || 0;

        // Hitung nilai awal (qty * harga)
        let nilaiAwal = qty * harga;

        // Hitung diskon
        // let diskon = (nilaiAwal * multidisc) / 100;

        // Hitung nilai akhir setelah diskon
        let nilaiAkhir = nilaiAwal;

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
            url: HOST_URL + 'purchase/trans/delete_po_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json',
            success: function(res){
                if(res.status){
                    //
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Berhasil',
                    //     text: 'Data berhasil dihapus',
                    //     timer: 1500,
                    //     showConfirmButton: false
                    // });


                    $('#tabppdtl').DataTable().ajax.reload(null,false);
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
$('#formPOdetail').bootstrapValidator({
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


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});


function savePODetail() {


    // Swal.fire({
    //     title: 'Konfirmasi',
    //     text: 'Proses data PP ke Detail PO?',
    //     icon: 'question',
    //     showCancelButton: true,
    //     confirmButtonText: 'Ya, Proses',
    //     cancelButtonText: 'Batal',
    //     reverseButtons: true
    // }).then((result) => {
    //   if (!result.isConfirmed) return;
    // ==========================================
    // AMBIL DATA SUPPLIER DARI SELECT2
    // ==========================================
    let supplierData = $('#kdsupplier').select2('data');

    let kdsupplier = '';
    let nmsupplier = '';

    if (supplierData && supplierData.length > 0) {
        kdsupplier = supplierData[0].kdsupplier || supplierData[0].id || '';
        nmsupplier = supplierData[0].nmsupplier || supplierData[0].text || '';
    }

    let formData = new FormData(document.getElementById('formPODetail'));
    formData.append('docdate', $('#docdate').val());
    formData.append('cabang', $('#cabang').val());
    formData.append('senddate', $('#senddate').val());
    formData.append('jthtempo', convertToDbNumber($('#jthtempo').val()));
    // ==========================================
    // SUPPLIER
    // ==========================================
    formData.append('kdsupplier', kdsupplier);
    formData.append('nmsupplier', nmsupplier);

    formData.append(
        'isinclusive',
        $('#isinclusive').is(':checked') ? 'YES' : 'NO'
    );
    formData.append('alamatsupplier', $('#alamatsupplier').val());
    formData.append('idtax', $('#idtax').val());
    formData.append('currcode', $('#currcode').val());
    formData.append('kurs', convertToDbNumber($('#kurs').val()));
    formData.append('alamatkirim', $('#alamatkirim').val());
    formData.append('keterangan', $('#keterangan').val());
    formData.append('capexno', $('#capexno').val());
    // formData.append('estpakai', $('#estpakai').val());

    // docno gabungan (lebih aman pakai hidden header)
    formData.set('docno', $('#prefix').val() + '/' + $('#infix').val() + '/' + $('#sufix').val());
    // convert qty ke numeric DB
    let qty = $('#qty').val();
    let qtybonus = $('#qtybonus').val();
    let harga = $('#harga').val();


    let multidisc       = $('#multidisc').val();
    let multidisctype   = $('#multidisctype').val();
    let totaldiscount   = $('#totaldiscount').val();
    let nilai           = $('#nilai').val();
    let idhistory_price = $('#idhistory_price').val();


    formData.set('qty', convertToDbNumber(qty));
    formData.set('qtybonus', convertToDbNumber(qtybonus));
    formData.set('harga', convertToDbNumber(harga));
    // formData.set('multidisc', convertToDbNumber(multidisc));
    formData.set('nilai', convertToDbNumber(nilai));
    // formData.set('nilai', convertToDbNumber(nilai));
    formData.set('descriptionpo', $('#descriptionpo').val());
    formData.set('uniqueid', $('#uniqueid').val());
    formData.set('docnopp', $('#docnopp').val());
    formData.set(
        'multidisc',
        convertToDbNumber(multidisc)
    );

    formData.set(
        'multidisctype',
        multidisctype
    );

    formData.set(
        'totaldiscount',
        convertToDbNumber(totaldiscount)
    );

    formData.set(
        'idhistory_price',
        idhistory_price
    );

    formData.set(
        'nilai',
        convertToDbNumber(nilai)
    );
    // formData.set('descriptionpo', convertToDbNumber(qty));

    $.ajax({
        url: HOST_URL + 'purchase/trans/savePODetail',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,

        success: function (res) {

            // ==============================
            // JIKA GAGAL → TAMPILKAN SWAL
            // ==============================

            if (!res.success) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal',
                    text: res.message
                });

                return;
            }


            // ==============================
            // BERHASIL
            // TANPA SWAL
            // ==============================


            // Jika header baru dibuat → reload
            if (res.reload === true) {

                window.location.reload();

                return;
            }


            // ==============================
            // TUTUP MODAL
            // ==============================

            $('#modalDetailPO').modal('hide');

            $('#modalUpdatePO').modal('hide');


            // ==============================
            // RESET FORM
            // ==============================

            $('#formPOUpdate')[0].reset();

            $('#formPODetail')[0].reset();


            // ==============================
            // RELOAD TABLE
            // ==============================

            reload_table_po_dtl();

            documentReadable();
        },


        // ==============================
        // SERVER ERROR
        // ==============================

        error: function (xhr) {

            console.error(xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Terjadi kesalahan pada server (500)'
            });
        }
    });


    //});
}

function btnInputDetail() {

    let cabang = $('#cabang').val();

    if (!cabang || cabang.trim() === '') {
        Swal.fire({
            title: 'Peringatan',
            text: 'Cabang harus dipilih terlebih dahulu.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });

        $('#cabang').focus();
        return;
    }
    $('#formPODetail')[0].reset();


    // 🔹 Clear select2
    $('#docnopp').val(null).trigger('change');

    // Jika ada select2 lain, lakukan hal sama
    // $('#selectlain').val(null).trigger('change');

    $('#idurut').val(''); // pastikan id kosong (mode insert)

    $('#modalDetailPOLabel').text('Tambah Item Detail');
    $('#modalDetailPO').modal('show');
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

        $("#alamatsupplier").val(selectedData.alamat || '').prop('disabled', true);
        $("#jthtempo").val(selectedData.jthtempo || '')
        // $("#phone").val(selectedData.phone || '').prop('disabled', true);
    }
});


// =====================================================
// AUTO LOAD CURRENCY DARI API
// =====================================================
function loadDefaultCurrency(currcode, kurs = null, docdate = null) {

    currcode = $.trim(currcode || '').toUpperCase();

    if (currcode === '') {
        return;
    }


    // =============================================
    // JIKA IDR MAKA KURS SELALU 1
    // =============================================

    if (currcode === 'IDR') {

        setJtsValue(
            '[name="kurs"]',
            convertToDbNumber(1)
        );

        $('[name="kurs"]')
            .prop('readonly', true)
            .trigger('change');

        return;
    }


    // =============================================
    // LOAD DATA CURRENCY
    // =============================================

    $.ajax({
        type: 'GET',

        url: HOST_URL +
            'api/globalmodule/list_currency?var=' +
            encodeURIComponent(currcode),

        dataType: 'json',

        success: function (datax) {

            if (
                !datax ||
                !datax.items ||
                datax.items.length === 0
            ) {

                console.warn(
                    'Currency tidak ditemukan:',
                    currcode
                );

                return;
            }


            // =============================================
            // DATA CURRENCY
            // =============================================

            var currencyData = datax.items[0];


            // =============================================
            // SET SELECT CURRENCY
            // =============================================

            $('[name="currcode"] option[value="' +
                currcode.replace(/"/g, '\\"') +
                '"]').remove();


            var option = new Option(
                currencyData.currname,
                currencyData.currcode,
                true,
                true
            );


            $(option).data(
                'currency-data',
                currencyData
            );


            $('[name="currcode"]')
                .append(option)
                .trigger('change');


            // =============================================
            // JIKA KURS DIKIRIM LANGSUNG
            // =============================================

            if (
                kurs !== null &&
                kurs !== undefined &&
                kurs !== ''
            ) {

                setJtsValue(
                    '[name="kurs"]',
                    convertToDbNumber(kurs)
                );

                $('[name="kurs"]')
                    .prop('readonly', false)
                    .trigger('change');

                return;
            }


            // =============================================
            // AMBIL DOCDATE JIKA BELUM DIKIRIM
            // =============================================

            if (
                !docdate ||
                $.trim(docdate) === ''
            ) {

                docdate = $('[name="docdate"]').val();

            }


            // =============================================
            // VALIDASI IDCURR
            // =============================================

            if (
                !currencyData.idcurr
            ) {

                console.warn(
                    'ID Currency tidak ditemukan'
                );

                return;
            }


            // =============================================
            // AMBIL EXCHANGE RATE BERDASARKAN TANGGAL
            // =============================================

            $.ajax({

                type: 'GET',

                url:
                    HOST_URL +
                    'api/globalmodule/get_exchange_rate',

                data: {
                    idcurr: currencyData.idcurr,
                    docdate: docdate
                },

                dataType: 'json',

                success: function (response) {

                    var nilaiKurs = 1;


                    if (
                        response &&
                        response.status === true &&
                        response.data
                    ) {

                        nilaiKurs =
                            response.data.nilai || 1;

                    }


                    // =============================================
                    // SIMPAN KURS KE CURRENCY DATA
                    // =============================================

                    currencyData.kurs =
                        nilaiKurs;


                    // =============================================
                    // SET KURS
                    // =============================================

                    setJtsValue(
                        '[name="kurs"]',
                        convertToDbNumber(nilaiKurs)
                    );


                    $('[name="kurs"]')
                        .prop('readonly', false)
                        .trigger('change');


                },

                error: function (xhr) {

                    console.error(
                        'Gagal mengambil exchange rate:',
                        xhr.responseText
                    );

                }

            });

        },

        error: function (xhr) {

            console.error(
                'Gagal load currency:',
                xhr.responseText
            );

        }

    });

}


// =====================================================
// AUTO LOAD TAX DARI API
// =====================================================
function loadDefaultTax(idtax) {

    idtax = $.trim(idtax || '');

    if (idtax === '') {
        return;
    }


    $.ajax({
        type: 'GET',

        url: HOST_URL +
            'api/globalmodule/list_tax?var=' +
            encodeURIComponent(idtax),

        dataType: 'json',

        delay: 250,

        success: function (datax) {

            if (
                !datax ||
                !datax.items ||
                datax.items.length === 0
            ) {

                console.warn(
                    'Tax tidak ditemukan:',
                    idtax
                );

                return;
            }


            // =============================================
            // DATA TAX
            // =============================================

            var taxData = datax.items[0];


            // =============================================
            // HAPUS OPTION LAMA JIKA ADA
            // =============================================

            $('[name="idtax"] option[value="' +
                idtax.replace(/"/g, '\\"') +
                '"]').remove();


            // =============================================
            // CREATE OPTION SELECT2
            // =============================================

            var option = new Option(

                taxData.nmtax,
                taxData.idtax,

                true,
                true

            );


            // =============================================
            // APPEND OPTION
            // =============================================

            $('[name="idtax"]')
                .append(option)
                .trigger('change');


            // =============================================
            // TRIGGER SELECT2 SELECT
            // =============================================

            $('[name="idtax"]').trigger({

                type: 'select2:select',

                params: {

                    data: taxData

                }

            });

        },

        error: function (xhr) {

            console.error(
                'Gagal load tax:',
                xhr.responseText
            );

        }

    });

}

let currentKodeSuffix = '';

$('#cabang').on('change', function () {

    if (skipRoleChange) return;

    let idbranch = $(this).val();

    if (idbranch) {

        $.ajax({

            url: HOST_URL + '/purchase/trans/getBranchInfoPO',

            method: 'GET',

            data: {
                idbranch: idbranch
            },

            dataType: 'json',


            success: function (res) {

                if (!res.success) {

                    Swal.fire(
                        'Error',
                        res.message,
                        'warning'
                    );

                    return;
                }


                // =====================================================
                // DATA BRANCH
                // =====================================================

                currentKodeSuffix = res.kode_suffix;

                $('#infix').val(res.infix);

                $('#prefix').val('POB');

                $('#sufix').val(
                    currentKodeSuffix + '0001'
                );

                defaultInitialPP = currentKodeSuffix;


                // =====================================================
                // FORCE DEFAULT CURRENCY IDR
                // =====================================================

                var defaultCurrcode = 'IDR';

                $.ajax({

                    type: 'GET',

                    url:
                        HOST_URL +
                        'api/globalmodule/list_currency?var=' +
                        encodeURIComponent(defaultCurrcode),

                    dataType: 'json',

                    success: function (datax) {

                        // =============================================
                        // VALIDASI CURRENCY
                        // =============================================

                        if (
                            !datax ||
                            !datax.items ||
                            datax.items.length === 0
                        ) {

                            console.warn(
                                'Currency IDR tidak ditemukan'
                            );

                            return;
                        }


                        // =============================================
                        // DATA CURRENCY
                        // =============================================

                        var currencyData = datax.items[0];

                        // FORCE KURS IDR = 1
                        currencyData.kurs = 1;


                        // =============================================
                        // HAPUS CURRENCY SEBELUMNYA
                        // =============================================

                        $('[name="currcode"]')
                            .empty();


                        // =============================================
                        // CREATE OPTION
                        // =============================================

                        var option = new Option(

                            currencyData.currname,
                            currencyData.currcode,
                            true,
                            true

                        );


                        // Simpan data currency
                        $(option).data(
                            'currency-data',
                            currencyData
                        );


                        // =============================================
                        // SET SELECT2 IDR
                        // =============================================

                        $('[name="currcode"]')
                            .append(option)
                            .val(currencyData.currcode)
                            .trigger('change.select2');


                        // =============================================
                        // FORCE KURS = 1
                        // =============================================

                        setJtsValue(
                            '[name="kurs"]',
                            1
                        );


                        // =============================================
                        // KURS IDR READONLY
                        // =============================================

                        $('[name="kurs"]')
                            .prop('readonly', true);

                    },


                    error: function (xhr) {

                        console.error(
                            'Gagal load Currency IDR:',
                            xhr.responseText
                        );

                    }

                });


                // =====================================================
                // AMBIL KONFIGURASI UMUM
                // =====================================================

                var config = null;

                if (
                    Array.isArray(res.konfigurasi_umum) &&
                    res.konfigurasi_umum.length > 0
                ) {

                    config = res.konfigurasi_umum[0];

                }


                // =====================================================
                // AUTO SELECT TAX
                // =====================================================

                if (config) {

                    var idtax = $.trim(
                        config.idtax || ''
                    );


                    // =============================================
                    // LOAD TAX SELECT2
                    // =============================================

                    if (idtax !== '') {

                        loadDefaultTax(idtax);

                    }

                }


                // =====================================================
                // DATE RANGE BERDASARKAN INFIX
                // =====================================================

                var infix = (res.infix || '').toString();

                if (infix.length === 4) {

                    $('#docdate').prop(
                        'disabled',
                        false
                    );

                    var yy = infix.substring(0, 2);

                    var mm = infix.substring(2, 4);

                    var year = 2000 + parseInt(yy, 10);

                    var month = parseInt(mm, 10) - 1;

                    var today = moment();

                    var startDate = moment([
                        year,
                        month,
                        1
                    ]);

                    var endDate =
                        moment(startDate).endOf('month');


                    var $el = $('#docdate');

                    var drp =
                        $el.data('daterangepicker');


                    if (drp) {

                        drp.minDate = startDate;

                        drp.maxDate = endDate;

                        drp.setStartDate(startDate);

                        drp.setEndDate(startDate);

                    }


                    // =====================================================
                    // SET TANGGAL
                    // =====================================================

                    if (
                        today.isSameOrAfter(startDate) &&
                        today.isSameOrBefore(endDate)
                    ) {

                        $el.val(
                            today.format('DD-MM-YYYY')
                        );

                    } else {

                        $el.val(
                            startDate.format('DD-MM-YYYY')
                        );

                    }

                }


                // =====================================================
                // GENERATE DOCNO
                // =====================================================

                $('#docno').val(

                    'POB/' +
                    res.infix +
                    '/' +
                    currentKodeSuffix +
                    '0001'

                );


                // =====================================================
                // ALAMAT KIRIM
                // =====================================================

                if (idbranch === '01.02') {

                    $('#alamatkirim').val(
                        'JL. MAYJEND SUNGKONO NO. 90 KEL. PRAMBANGAN KEC.KEBOMAS GRESIK.'
                    );

                } else {

                    $('#alamatkirim').val(
                        'JL. RAYA TAMAN NO. 1 RT.014 RW.003 TAMAN, TAMAN SIDOARJO'
                    );

                }

            },


            error: function (xhr) {

                console.error(xhr.responseText);

                Swal.fire(
                    'Error',
                    'Gagal mengambil informasi cabang',
                    'error'
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
        url: HOST_URL + '/purchase/trans/getNextSuffixPO',
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
            setupEstpakai(prefix);
        }
    });
});


function setupEstpakai(prefix = '', existingValue = null) {
    const type = getPrefixType(prefix);
    const docDateValue = $('#docdate').val();
    const options = generateDateOptions(type, docDateValue);

    // Hapus daterangepicker lama
    if ($('#senddate').data('daterangepicker')) {
        $('#senddate').daterangepicker('destroy');
    }

    // Tentukan tanggal default
    let defaultDate;
    let selectedValue = existingValue;

    // Parse docDate
    const docDateMoment = moment(docDateValue, 'DD-MM-YYYY');
    const isDocDateValid = docDateMoment.isValid();

    if (existingValue) {
        // Coba parse existing value
        const parsed = moment(existingValue, 'DD-MM-YYYY');
        if (parsed.isValid()) {
            defaultDate = parsed;
            selectedValue = existingValue;
        } else {
            // Jika existingValue tidak valid, gunakan docDate
            if (isDocDateValid) {
                defaultDate = docDateMoment;
                selectedValue = defaultDate.format('DD-MM-YYYY');
            } else {
                defaultDate = options.length > 0 ? options[0].date : moment();
                selectedValue = defaultDate.format('DD-MM-YYYY');
            }
        }
    } else {
        // Gunakan docDate + 1 minggu/bulan sebagai default
        if (isDocDateValid) {
            // Default ke +1 minggu (atau +1 bulan untuk import)
            const defaultOption = options.length > 0 ? options[0] : null;
            if (defaultOption) {
                defaultDate = defaultOption.date;
                selectedValue = defaultOption.value;
            } else {
                defaultDate = docDateMoment;
                selectedValue = defaultDate.format('DD-MM-YYYY');
            }
        } else {
            defaultDate = options.length > 0 ? options[0].date : moment();
            selectedValue = defaultDate.format('DD-MM-YYYY');
        }
    }

    // Inisialisasi daterangepicker
    $('#senddate').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        startDate: defaultDate,
        locale: { format: 'DD-MM-YYYY' },
        cancelLabel: 'Clear'
    });

    $('#senddate').val(selectedValue);

    // Hapus event lama
    $('#senddate').off('apply.daterangepicker cancel.daterangepicker');

    // Event apply
    $('#senddate').on('apply.daterangepicker', function(ev, picker) {
        const dateStr = picker.startDate.format('DD-MM-YYYY');
        $(this).val(dateStr);
        updateActiveButton(dateStr);
    });

    // Event cancel
    $('#senddate').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('.senddate-btn').removeClass('active').css({
            'background-color': '#ffffff',
            'color': '#007bff'
        });
    });

    // Buat tombol pilihan cepat
    let buttonHtml = '<div style="display:flex; flex-wrap:wrap; gap:5px; margin-top:5px;">';

    options.forEach((opt) => {
        // Cek apakah nilai ini match dengan selectedValue
        const isActive = (selectedValue && opt.value === selectedValue);

        buttonHtml += `<button type="button"
                            class="senddate-btn ${isActive ? 'active' : ''}"
                            data-date="${opt.value}"
                            style="
                                padding: 6px 16px;
                                background-color: ${isActive ? '#007bff' : '#ffffff'};
                                color: ${isActive ? '#ffffff' : '#007bff'};
                                border: 1px solid #007bff;
                                border-radius: 4px;
                                cursor: pointer;
                                font-size: 13px;
                                transition: all 0.3s;
                            "
                            onmouseover="if(!this.classList.contains('active')){this.style.backgroundColor='#007bff'; this.style.color='#ffffff';}"
                            onmouseout="if(!this.classList.contains('active')){this.style.backgroundColor='#ffffff'; this.style.color='#007bff';}"
                            onclick="selectEstpakai('${opt.value}', this)">
                            ${opt.label}
                        </button>`;
    });

    buttonHtml += '</div>';

    // Tambahkan tombol setelah input
    if ($('#senddate_buttons').length === 0) {
        $('#senddate').after(`<div id="senddate_buttons">${buttonHtml}</div>`);
    } else {
        $('#senddate_buttons').html(buttonHtml);
    }
}

// Fungsi update active button
function updateActiveButton(date) {
    $('.senddate-btn').each(function() {
        const isActive = $(this).data('date') === date;
        $(this).toggleClass('active', isActive);

        if (isActive) {
            $(this).css({
                'background-color': '#007bff',
                'color': '#ffffff'
            });
        } else {
            $(this).css({
                'background-color': '#ffffff',
                'color': '#007bff'
            });
        }
    });
}

function selectEstpakai(date, element) {
    $('#senddate').val(date);

    const picker = $('#senddate').data('daterangepicker');
    if (picker) {
        picker.setStartDate(moment(date, 'DD-MM-YYYY'));
    }

    updateActiveButton(date);
}

function getPrefixType(prefix) {
    const cleanPrefix = prefix ? prefix.trim() : '';

    // Cek import: 3 char dan diakhiri 'I'
    if (cleanPrefix.length === 3 && cleanPrefix.endsWith('I')) {
        return 'import';
    }

    // Cek local: 'JI' atau lainnya
    if (cleanPrefix === 'JI') {
        return 'local';
    }

    return 'local'; // default
}

function generateDateOptions(type, docDate = null) {
    // Gunakan docDate jika ada, jika tidak gunakan hari ini
    let baseDate;
    if (docDate) {
        baseDate = moment(docDate, 'DD-MM-YYYY');
        // Jika docDate tidak valid, fallback ke hari ini
        if (!baseDate.isValid()) {
            baseDate = moment();
        }
    } else {
        baseDate = moment();
    }

    let options = [];

    if (type === 'import') {
        for (let i = 1; i <= 6; i++) {
            const date = baseDate.clone().add(i, 'months');
            options.push({
                value: date.format('DD-MM-YYYY'),
                label: `+${i} Bulan`,
                date: date
            });
        }
    } else {
        for (let i = 1; i <= 4; i++) {
            const date = baseDate.clone().add(i, 'weeks');
            options.push({
                value: date.format('DD-MM-YYYY'),
                label: `+${i} Minggu`,
                date: date
            });
        }
    }

    return options;
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

function showHistoryHarga()
{
    let idbarang = $.trim($('#idbarang').val() || '');
    let nmbarang = $.trim($('#nmbarang').val() || '');

    if(idbarang==''){
        Swal.fire('Warning','Pilih barang terlebih dahulu','warning');
        return;
    }

    $('#history_idbarang').val(idbarang);
    $('#history_nmbarang').val(nmbarang);

    $('#bodyHistoryHarga').html(
        '<tr><td colspan="7" class="text-center">'+
        '<i class="fa fa-spinner fa-spin"></i> Loading...'+
        '</td></tr>'
    );

    $('#modalHistoryHarga').modal('show');

    $.ajax({
        url : HOST_URL + '/purchase/trans/historyHargaPO',
        type: 'GET',
        data: {idbarang:idbarang},
        dataType:'json',

        success:function(res){

            let html='';

            if(res.data.length==0){

                html='<tr><td colspan="7" class="text-center">Tidak ada history harga</td></tr>';

            }else{

                $.each(res.data,function(i,row){

                    // Format harga dengan toLocaleString
                    let hargaFormatted = parseFloat(row.harga).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    html+=`
                    <tr>
                        <td class="text-center">${i+1}</td>
                        <td>${row.docdate}</td>
                        <td>${row.docno}</td>
                        <td>${row.supplier}</td>
                        <td class="text-end">${hargaFormatted}</td>
                        <td class="text-center">${row.currcode}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success pilihHarga"
                                    data-harga="${row.harga}">
                                <i class="fa fa-check"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                });

            }

            $('#bodyHistoryHarga').html(html);

        }
    });
}

$(document).on('click','.pilihHarga',function(){

    let harga = $(this).data('harga');

    let hargaFormatted = parseFloat(harga).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    $('#harga').val(hargaFormatted);

    $('#harga').trigger('input');

    $('#harga').trigger('keyup');
    $('#modalHistoryHarga').modal('hide');

});


$('#unit').select2({
    dropdownParent: $('#modalUpdatePO'),

    ajax: {
        url: HOST_URL + 'api/globalmodule/list_unit_item',

        type: 'GET',
        dataType: 'json',
        delay: 250,

        data: function (params) {

            return {
                idbarang: $.trim($('#idbarang').val()),

                _search_: params.term || ''
            };

        },

        processResults: function (data) {

            return {
                results: $.map(data.items, function (item) {

                    return {
                        id: $.trim(item.idunit),
                        text: $.trim(item.idunit),

                        basic_value: item.basic_value,
                        conv_value: item.conv_value,
                        idunit_tax: item.idunit_tax,
                        cdefault: item.cdefault
                    };

                })
            };

        }

    },

    placeholder: 'Pilih Satuan',
    allowClear: true
});

// =====================================================
// FUNGSI AMBIL NILAI ANGKA
// =====================================================
function getNumericValue(selector) {

    let value = $(selector).val() || 0;

    // Gunakan fungsi convert jika sudah tersedia
    if (typeof convertToDbNumber === 'function') {
        return parseFloat(convertToDbNumber(value)) || 0;
    }

    // Fallback
    value = value.toString()
        .replace(/\./g, '')
        .replace(',', '.');

    return parseFloat(value) || 0;
}


// =====================================================
// FUNGSI SET NILAI
// =====================================================
function setNumericValue(selector, value) {

    value = parseFloat(value) || 0;

    // Jika menggunakan plugin jtsseparator
    if (typeof setJtsValue === 'function') {

        setJtsValue(selector, value);

    } else {

        $(selector).val(value);

    }
}


// =====================================================
// HITUNG TOTAL DISCOUNT DAN NILAI
// =====================================================
function calculatePODiscount() {

    // ==========================================
    // AMBIL DATA
    // ==========================================
    let qty = getNumericValue('#qty');
    let harga = getNumericValue('#harga');
    let multidisc = getNumericValue('#multidisc');

    let multidisctype = ($('#multidisctype').val() || 'NILAI')
        .toUpperCase();


    // ==========================================
    // NILAI KOTOR
    // ==========================================
    let subtotal = qty * harga;

    let totalDiscount = 0;


    // ==========================================
    // DISCOUNT NILAI
    //
    // Qty × Discount
    // ==========================================
    if (multidisctype === 'NILAI') {

        totalDiscount = qty * multidisc;

    }


        // ==========================================
        // DISCOUNT PERCENT
        //
        // Qty × Harga × % / 100
    // ==========================================
    else if (
        multidisctype === 'PERCENT' ||
        multidisctype === '%'
    ) {

        totalDiscount =
            subtotal * multidisc / 100;

    }


    // ==========================================
    // NILAI AKHIR
    //
    // Qty × Harga - Total Discount
    // ==========================================
    let nilai = subtotal - totalDiscount;


    // ==========================================
    // JANGAN MINUS
    // ==========================================
    if (nilai < 0) {
        nilai = 0;
    }


    // ==========================================
    // UPDATE FIELD
    // ==========================================
    setNumericValue('#totaldiscount', totalDiscount);

    setNumericValue('#nilai', nilai);


    // Debug
    console.log({
        qty: qty,
        harga: harga,
        multidisc: multidisc,
        multidisctype: multidisctype,
        subtotal: subtotal,
        totalDiscount: totalDiscount,
        nilai: nilai
    });
}
// =====================================================
// EVENT QUANTITY
// =====================================================
$(document).on(
    'input change keyup',
    '#qty',
    function () {

        calculatePODiscount();

    }
);


// =====================================================
// EVENT HARGA
// =====================================================
$(document).on(
    'input change keyup',
    '#harga',
    function () {

        calculatePODiscount();

    }
);


// =====================================================
// EVENT DISCOUNT DIKETIK
// =====================================================
$(document).on(
    'input change keyup',
    '#multidisc',
    function () {

        calculatePODiscount();

    }
);


// =====================================================
// EVENT JENIS DISCOUNT
// =====================================================
$(document).on(
    'change',
    '#multidisctype',
    function () {

        calculatePODiscount();

    }
);

/*ubah currency */
function updateExchangeRate() {

    var $currcode = $('[name="currcode"]');

    var currcode = $.trim(
        $currcode.val() || ''
    ).toUpperCase();

    // =============================================
    // TIDAK ADA CURRENCY
    // =============================================

    if (!currcode) {

        setJtsValue(
            '[name="kurs"]',
            0
        );

        return;
    }


    // =============================================
    // IDR SELALU 1
    // =============================================

    if (currcode === 'IDR') {

        setJtsValue(
            '[name="kurs"]',
            1
        );

        $('[name="kurs"]')
            .prop('readonly', true);

        return;
    }


    // =============================================
    // SELAIN IDR
    // =============================================

    $('[name="kurs"]')
        .prop('readonly', false);


    // =============================================
    // AMBIL DATA CURRENCY
    // =============================================

    var currencyData = $currcode
        .find('option:selected')
        .data('currency-data');


    // Fallback Select2
    if (!currencyData) {

        var select2Data = $currcode.select2('data');

        if (
            select2Data &&
            select2Data.length > 0
        ) {

            currencyData = select2Data[0];

        }

    }


    if (
        !currencyData ||
        !currencyData.idcurr
    ) {

        console.warn(
            'ID Currency tidak ditemukan'
        );

        return;
    }


    // =============================================
    // AMBIL DOCDATE
    // =============================================

    var docdate = $.trim(
        $('[name="docdate"]').val() || ''
    );


    if (!docdate) {

        console.warn(
            'Document Date belum diisi'
        );

        return;
    }


    // =============================================
    // LOADING
    // =============================================

    $.ajax({

        type: 'GET',

        url:
            HOST_URL +
            'api/globalmodule/get_exchange_rate',

        data: {
            idcurr: currencyData.idcurr,
            docdate: docdate
        },

        dataType: 'json',

        success: function (response) {

            if (
                response &&
                response.status === true &&
                response.data &&
                response.data.nilai !== null &&
                response.data.nilai !== undefined
            ) {

                var nilaiKurs = response.data.nilai;

                setJtsValue(
                    '[name="kurs"]',
                    nilaiKurs
                );

            } else {

                console.warn(
                    'Exchange rate tidak ditemukan'
                );

                // JANGAN OTOMATIS JADI 0
                // Biarkan nilai sebelumnya
            }

        },

        error: function (xhr) {

            console.error(
                'Gagal mengambil exchange rate:',
                xhr.responseText
            );

        }

    });

}

$(document).on(
    'change',
    '[name="currcode"]',
    function () {

        updateExchangeRate();

    }
);

$(document).on(
    'change',
    '[name="docdate"]',
    function () {

        updateExchangeRate();

    }
);

$(document).ready(function () {

    // Default currency
    loadDefaultCurrency('IDR');

    setJtsValue(
        '[name="kurs"]',
        convertToDbNumber(1)
    );

    $('[name="kurs"]').prop('readonly', true);

});
/*ubah currency end*/


$(document).ready(function() {
    // Handle form submission event
    // Handle form submission event


    tablePOTrx();
    tablePOApprvTrx();
    tablePODetail();
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
