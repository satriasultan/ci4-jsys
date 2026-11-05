/*
 * lpb.js
 *
 */


var save_method; //for save method string
var table;
var initTable;
//"use strict";

function tableLPBTrx(){
    var initTable = function () {
        var table = $('#tablelpbTrx');
        table.DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
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
                "url": HOST_URL + 'purchase/trans/list_lpb',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namasupplier = $('#namasupplier').val();
                    data.status = $('#status_filter').val();
                },
                "dataFilter": function(data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json);
                }
            },
            "columnDefs": [
                {
                    "targets": [ -1 ],
                    "orderable": false,
                },
            ],
        });
    };
    return initTable();
}

function reload_tableLPBTrx()
{
    var table = $('#tablelpbTrx');
    table.DataTable().ajax.reload();
}

$('#btn-filter-tx').click(function(){
    var table = $('#tablelpbTrx');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function(){
    $('#form-filter')[0].reset();
    var table = $('#tablelpbTrx');
    table.DataTable().ajax.reload();
    $('#filter').modal('hide');
});

let skipRoleChange = false;


//EDIT ITEM
function documentReadable(){
    var docno = $('[name="docno"]').val()

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'purchase/trans/showing_lpbtemp',
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
            let prefixParts = docnoData.split('/');
            $('[name="prefix"]').val(prefixParts[0]).prop('readonly', true);
            $('[name="infix"]').val(prefixParts[1]).prop('readonly', true);
            $('[name="suffix"]').val(prefixParts[2]).prop('readonly', true);
            defaultInitialPO = prefixParts[2].substring(0, 2);

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_supplier_new' + '?var=' + json.dataTables.items[0].kdsupplier,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                var supplierData = datax.items[0];
                supplierData.alamat = json.dataTables.items[0].alamatsupplier;

                var option = new Option(supplierData.nmsupplier, supplierData.kdsupplier, true, true);
                $(option).data('supplier-data', supplierData);

                $('[name="kdsupplier"]').append(option).trigger('change').prop('disabled',true);
                defaultInitialSupPO = json.dataTables.items[0].kdsupplier
                $("#alamatsupplier").val(json.dataTables.items[0].alamatsupplier);
            });

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_branchjob' + '?var=' + json.dataTables.items[0].cabang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                var option = new Option(datax.items[0].nmbranch, datax.items[0].idbranch, true, true);
                $('[name="cabang"]').append(option).trigger('change').prop('disabled',true);

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
                var option = new Option(datax.items[0].nmtax, datax.items[0].idtax, true, true);
                $('[name="idtax"]').append(option).trigger('change');

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
                var currencyData = datax.items[0];
                currencyData.kurs = json.dataTables.items[0].kurs;

                var option = new Option(currencyData.currname, currencyData.currcode, true, true);
                $(option).data('currency-data', currencyData);

                $('[name="currcode"]').append(option).trigger('change');

                setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
                $('[name="kurs"]').prop('readonly', true);
            });


            skipRoleChange = true;
            $('[name="docdate"]').val(moment(json.dataTables.items[0].docdate).format('DD-MM-YYYY'));
            setJtsValue('[name="jthtempo"]', convertToDbNumber(json.dataTables.items[0].jthtempo));
            setJtsValue('[name="biayavol"]', convertToDbNumber(json.dataTables.items[0].biayavol));
            setJtsValue('[name="biayavol2"]', convertToDbNumber(json.dataTables.items[0].biayavol2));
            setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
            $('[name="isinclusive"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].isinclusive || '')).toUpperCase() === 'YES'
            );
            $('[name="alamatsupplier"]').val(json.dataTables.items[0].alamatsupplier);
            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);
            $('[name="nosj"]').val(json.dataTables.items[0].nosj);
            $('[name="nofaktur"]').val(json.dataTables.items[0].nofaktur);
            $('[name="syarat"]').val(json.dataTables.items[0].syarat);

            setJtsValue('[name="dpp"]', convertToDbNumber(json.dataTables.items[0].dpp));
            setJtsValue('[name="jumlahpajak"]', convertToDbNumber(json.dataTables.items[0].jumlahpajak));
            setJtsValue('[name="total"]', convertToDbNumber(json.dataTables.items[0].total));

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

$("#idprincipal").select2({
    placeholder: "Ketik/Pilih Principal",
    allowClear: true,
    dropdownParent: $('#modalUpdateLPB'),
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_principal',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term,
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
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
    },
    templateResult: formatPrincipal,
    templateSelection: formatPrincipalSelection
}).on('select2:select', function (e) {
});


var defaultInitialLocation = '';
$("#idgudang").select2({
    placeholder: " -- Pilih Gudang Asal -- ",
    allowClear: true,
    width: '100%',
    dropdownParent: $('#modalUpdateLPB'),
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term,
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
    },
    templateResult: formatLocation,
    templateSelection: formatLocationSelection
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
    if (!guardPeriodeTutup()) return;
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
                url: HOST_URL + '/purchase/trans/updateStatusLPB',
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
                            reload_tableLPBTrx()
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
    if (!guardPeriodeTutup()) return;
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
                url: HOST_URL + '/purchase/trans/updateStatusLPB',
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
                            reload_tableLPBTrx()
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
var defaultInitialSupPO = '';
$("#docnopo").select2({
    placeholder: "Choose Your PO",
    allowClear: true,
    dropdownParent: $('#modalDetailLPB .modal-body'),
    width:'100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_po',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term,
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialPO,
                _paramglobalsup_: defaultInitialSupPO,
                _parameterx_: defaultInitialPO,
                term: params.term,
            };
        },
        processResults: function (data, params) {
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
    },
    templateResult: formatPO,
    templateSelection: formatPOSelection
}).on("select2:select", function (e) {
    var data = e.params.data;
});

/* Format Group */
function formatPO(repo) {
    if (repo.loading) {
        return repo.text;
    }

    return (
        "<div class='select2-result-repository__description'>" +
        (repo.docno || '') +
        " <i class='fa fa-circle-o'></i> " +
        (repo.keterangan || '') +
        "</div>"
    );
}

function formatPOSelection(repo) {
    if (!repo.docno) {
        return repo.text || '';
    }

    return (
        repo.docno +
        ' - ' +
        (repo.keterangan || '')
    );
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
                _search_: params.term,
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
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
    },
    templateResult: formatCurrency,
    templateSelection: formatCurrencySelection
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
                _search_: params.term,
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
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
    },
    templateResult: formatTax,
    templateSelection: formatTaxSelection
}).on("select2:select", function (e) {
});



function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}




$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});





/* TABLE PO DETAIL */
function tableLPBDetail(){
    var initTable = function () {
        var table = $('#tablpbdtl');
        table.DataTable({
            "processing": true,
            "serverSide": true,
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
                "url": HOST_URL + 'purchase/trans/list_tmp_lpb_dtl',
                "type": "POST",
                "data": function(data) {
                },
                "dataFilter": function(data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json);
                }
            },
            "columnDefs": [
                {
                    "targets": 0,
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center",
                    "render": function (data, type, row) {
                        return '<input type="checkbox" class="row-check" value="' + row[0] + '">';
                    }
                },
                {
                    "targets": [ -1 ],
                    "orderable": false
                }
            ]
        });

        $('#tablpbdtl tbody').on('click', 'tr', function(e) {
            // Jika yang diklik adalah checkbox itu sendiri, biarkan perilaku default
            if ($(e.target).is('input[type="checkbox"]')) {
                return;
            }

            // Jika user mengklik teks/tautan di dalam baris (misal ada tombol), jangan ditoggle
            if ($(e.target).is('a, button, i')) {
                return;
            }

            var checkbox = $(this).find('input[type="checkbox"].row-check');

            // Toggle nilai checkbox
            var isChecked = !checkbox.prop('checked');
            checkbox.prop('checked', isChecked);

            // PENTING: Trigger event 'change' agar event listener lain (seperti pengecekan checkAll) ikut berjalan
            checkbox.trigger('change');
        });
    }

    return initTable();
}


function reload_table_lpb_dtl()
{
    var table = $('#tablpbdtl');
    table.DataTable().ajax.reload();
}



// CHECK ALL
$('#tablpbdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tablpbdtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tablpbdtl tbody').on('change', '.row-check', function () {
    const total = $('#tablpbdtl tbody .row-check').length;
    const checked = $('#tablpbdtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tablpbdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
    checkLPBDetailAndLock();
});
function getSelectedLPBDetail(){
    return $('#tablpbdtl tbody .row-check:checked')
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

    // 🔧 Reset batch (sebelumnya idspec)
    $('[name="batch"]').val(null).trigger('change');
    $('[name="batch"]').empty();

    // 🔧 Reset etd & etdnotes
    $('[name="etd"]').val('');
    $('[name="etdnotes"]').val('');
}

function setSelect2Ajax(selector, value, text) {
    if (!value) return;

    var $select = $(selector);

    if (!$select.find('option[value="' + value + '"]').length) {
        var option = new Option(text || value, value, true, true);
        $select.append(option);
    }

    $select.val(value).trigger('change');
}

let currentEditId = null;
function btnUpdateDetail(){
    const ids = getCheckedDetailIds();

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
        url: HOST_URL + 'purchase/trans/get_lpb_detail',
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
                $('[name="docnopo"]').val(res.data.docnopo);
                $('#idbarang').val(res.data.idbarang);
                $('#nmbarang').val(res.data.nmbarang);
                $('#unit').val(res.data.unit);

                setJtsValue('[name="qty"]', convertToDbNumber(res.data.qty));
                setJtsValue('[name="qtybonus"]', convertToDbNumber(res.data.qtybonus));
                setJtsValue('[name="harga"]', convertToDbNumber(res.data.harga));
                setJtsValue('[name="volitem"]', convertToDbNumber(res.data.volitem));
                setJtsValue('[name="biaya"]', convertToDbNumber(res.data.biaya));
                setJtsValue('[name="biaya2"]', convertToDbNumber(res.data.biaya2));
                setJtsValue('[name="multidisc"]', convertToDbNumber(res.data.multidisc));
                setJtsValue('[name="nilai"]', convertToDbNumber(res.data.nilai));

                // 🔧 ETD & ETD NOTES
                // etd disimpan sebagai DATE (YYYY-MM-DD) → tampil DD-MM-YYYY
                if (res.data.etd) {
                    $('#etd').val(moment(res.data.etd).format('DD-MM-YYYY'));
                } else {
                    $('#etd').val('');
                }
                $('#etdnotes').val(res.data.etdnotes || '');

                currentEditId = res.data.idurut;

                // =====================================================
                // AUTO SELECT BATCH (sebelumnya idspec)
                // =====================================================

                var selectedBatch = $.trim(res.data.batch || '');
                var selectedItem  = $.trim(res.data.idbarang || '');

                // Reset batch dulu
                $('#batch')
                    .empty()
                    .val(null)
                    .trigger('change');

                if (selectedBatch === '') {
                    $('#batch')
                        .val(null)
                        .trigger('change');
                } else {
                    $.ajax({
                        type: 'POST',
                        url: HOST_URL + 'api/globalmodule/list_batch_item',
                        dataType: 'json',
                        data: {
                            _search_: selectedBatch,
                            _page_: 1,
                            _draw_: true,
                            _start_: 1,
                            _perpage_: 30,
                            _paramglobal_: '',
                            _parameterx_: selectedItem,
                            _var_: selectedBatch
                        }
                    }).then(function (datax) {

                        if (!datax || !datax.items || datax.items.length === 0) {
                            console.warn('Batch tidak ditemukan');
                            $('#batch').empty().val(null).trigger('change');
                            return;
                        }

                        var specData = datax.items.find(function (item) {
                            return $.trim(item.batch || '') === selectedBatch;
                        });

                        if (!specData) {
                            console.warn('Batch yang sesuai tidak ditemukan:', selectedBatch);
                            $('#batch').empty().val(null).trigger('change');
                            return;
                        }

                        specData.id = specData.batch;
                        specData.text = specData.batch;

                        var option = new Option(
                            specData.batch,
                            specData.batch,
                            true,
                            true
                        );

                        $(option).data('spec-data', specData);

                        $('#batch')
                            .empty()
                            .append(option)
                            .val(specData.batch)
                            .trigger('change');

                        $('#batch').trigger({
                            type: 'select2:select',
                            params: {
                                data: specData
                            }
                        });

                    });
                }
                /* ID PRINCIPAL */
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_principal?var=' + encodeURIComponent(res.data.idprincipal),
                    dataType: 'json',
                    delay: 250
                }).then(function (datax) {

                    if (!datax || !datax.items || datax.items.length === 0) {
                        console.warn('Principal tidak ditemukan');
                        return;
                    }

                    var principalData = datax.items[0];

                    var option = new Option(
                        principalData.idprincipal + ' - ' + principalData.nmprincipal,
                        principalData.idprincipal,
                        true,
                        true
                    );

                    $(option).data('principal-data', principalData);

                    $('[name="idprincipal"]')
                        .append(option)
                        .trigger('change')
                        .prop('disabled', true);
                });

                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + res.data.idgudang,
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    var option = new Option(datax.items[0].nmlocation, datax.items[0].idlocation, true, true);
                    $('[name="idgudang"]').append(option).trigger('change')

                    $('[name="idgudang"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });

                $('#modalUpdateLPBLabel').text('Update Penerimaan Pembelian');
                $('#modalUpdateLPB').modal('show');

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
$('#modalUpdateLPB').on('hidden.bs.modal', function () {
    currentEditId = null;
});

$(document).on('input', '.form-control', function () {
    if (currentEditId) {
        let qty = parseFloat($('#qty').val().replace(/,/g, '')) || 0;
        let harga = parseFloat($('#harga').val().replace(/,/g, '')) || 0;
        let multidisc = parseFloat($('#multidisc').val().replace(/,/g, '')) || 0;

        let nilaiAwal = qty * harga;
        let diskon = (nilaiAwal * multidisc) / 100;
        let nilaiAkhir = nilaiAwal - diskon;

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
            url: HOST_URL + 'purchase/trans/delete_lpb_detail',
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

                    $('#tablpbdtl').DataTable().ajax.reload(null,false);
                    documentReadable()
                    checkLPBDetailAndLock();

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
$('#formLPBdetail').bootstrapValidator({
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


function saveLPBDetail() {

    let ok =  $('#docnopo').val()
    let ok2 = $('[name="docnopo"]').val();

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

    let formData = new FormData(document.getElementById('formLPBDetail'));
    formData.append('docdate', $('#docdate').val());
    formData.append('cabang', $('#cabang').val());
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
    formData.append('nosj', $('#nosj').val());
    formData.append('nofaktur', $('#nofaktur').val());
    formData.append('idtax', $('#idtax').val());
    formData.append('currcode', $('#currcode').val());
    formData.append('kurs', convertToDbNumber($('#kurs').val()));
    formData.append('biayavol', convertToDbNumber($('#biayavol').val()));
    formData.append('biayavol2', convertToDbNumber($('#biayavol2').val()));
    formData.append('keterangan', $('#keterangan').val());

    // docno gabungan
    formData.set('docno', $('#prefix').val() + '/' + $('#infix').val() + '/' + $('#suffix').val());

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
    formData.set('idprincipal', $('#idprincipal').val() ?? '');
    formData.set('idgudang', $('#idgudang').val());

    // 🔧 BATCH (sebelumnya idspec)
    formData.set('batch', $('#batch').val() ?? '');

    // 🔧 ETD & ETD NOTES
    // etd di DB = DATE. Kalau user isi DD-MM-YYYY, ubah ke YYYY-MM-DD
    let etdVal = $.trim($('#etd').val() || '');
    let etdDb = '';
    if (etdVal !== '') {
        let m = moment(etdVal, 'DD-MM-YYYY', true);
        if (m.isValid()) {
            etdDb = m.format('YYYY-MM-DD');
        } else {
            etdDb = etdVal; // biar PHP yang validasi
        }
    }
    formData.set('etd', etdDb);
    formData.set('etdnotes', $.trim($('#etdnotes').val() || ''));

    let docnopo = $('#docnopo').val();

    if (!docnopo) {
        docnopo = $('#docnopomodal').val();
    }

    formData.set('docnopo', docnopo);

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

            let iconType = 'success';
            let titleText = 'Berhasil';

            if (res.message && res.message.toLowerCase().includes('sudah ada')) {
                iconType = 'info';
                titleText = 'Tidak Ada Perubahan';
            }

            if (res.reload === true) {
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
                return;
            }

            $('#modalDetailLPB').modal('hide');
            $('#modalUpdateLPB').modal('hide');
            $('#formLPBUpdate')[0].reset();
            reload_table_lpb_dtl();

            setTimeout(function () {
                checkLPBDetailAndLock();
            }, 300);

            documentReadable();
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
}

function btnInputDetail() {

    let cabang = $('#cabang').val();
    let kdsupplier = $('#kdsupplier').val();


    if (!cabang || cabang.trim() === '') {
        alert('Cabang harus dipilih terlebih dahulu');
        $('#cabang').focus();
        return;
    }

    if (!kdsupplier || kdsupplier.trim() === '') {
        alert('Supplier harus dipilih terlebih dahulu');
        $('#kdsupplier').focus();
        return;
    }

    $('#formLPBDetail')[0].reset();

    $('#docnopo').val(null).trigger('change');

    $('#idurut').val('');

    $('#modalDetailLPBLabel').text('Tarik Data PO Detail');
    $('#modalDetailLPB').modal('show');
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
                _search_: params.term,
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
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
    },
    templateResult: formatSupplier,
    templateSelection: formatSupplierSelection
}).on("select2:select", function (e) {
    if (e.params && e.params.data) {
        var selectedData = e.params.data;

        $("#alamatsupplier").val(selectedData.alamat || '').prop('disabled', true);
        defaultInitialSupPO = selectedData.kdsupplier
        $("#jthtempo").val(selectedData.jthtempo || '')
    }
});


$('#cabang').on('change', function () {
    if (skipRoleChange) return;

    let idbranch = $(this).val();

    if(idbranch){
        $.ajax({
            url: HOST_URL + '/purchase/trans/getBranchInfoLPB',
            method: 'GET',
            data: { idbranch: idbranch },
            dataType: 'json',
            success: function (res) {
                if (!res.success) {
                    Swal.fire('Error', res.message, 'warning');
                    return;
                }

                currentKodeSuffix = res.kode_suffix;
                $('#infix').val(res.infix);
                var prefix = res.prefix;
                $('#prefix').val(prefix);
                loadNextSuffixLPB()
                defaultInitialPO = currentKodeSuffix;

                var infix = (res.infix || '').toString();
                if (infix.length === 4) {
                    $('#docdate').prop('disabled', false);
                    var yy = infix.substring(0,2);
                    var mm = infix.substring(2,4);
                    var year = 2000 + parseInt(yy,10);
                    var month = parseInt(mm,10) - 1;

                    var logindate = res.logindate ? moment(res.logindate, 'DD-MM-YYYY') : moment();

                    var startDate = moment([year, month, 1]);
                    var endDate = moment(startDate).endOf('month');

                    var selectedDate = logindate.isBetween(startDate, endDate, 'day', '[]')
                        ? logindate
                        : startDate;

                    var $el = $('#docdate');
                    var drp = $el.data('daterangepicker');

                    if (drp) {
                        drp.minDate = startDate;
                        drp.maxDate = endDate;
                        drp.setStartDate(selectedDate);
                        drp.setEndDate(selectedDate);
                    } else {
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
                        $el.on('apply.daterangepicker', function(ev, picker) {
                            $(this).val(picker.startDate.format('DD-MM-YYYY'));
                            $(this).trigger('change');
                        });
                        $el.on('cancel.daterangepicker', function(ev, picker) {
                            $(this).val('');
                            $(this).trigger('change');
                        });
                    }

                    $el.val(selectedDate.format('DD-MM-YYYY'));
                }

                $('#docno').val(
                    prefix + '/' + res.infix + '/' + currentKodeSuffix + '0001'
                );

                var docdate = $('#docdate').val() || '';

                if (res.currcode) {
                    $('[name="currcode"]').empty().trigger('change');

                    var url = HOST_URL + 'api/globalmodule/list_currency' + '?var=' + res.currcode;
                    if (docdate) {
                        url += '&docdate=' + encodeURIComponent(docdate);
                    }

                    $.ajax({
                        type: 'GET',
                        url: url,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax.items && datax.items.length > 0) {
                            var currencyData = datax.items[0];

                            var option = new Option(currencyData.currname, currencyData.currcode, true, true);
                            $(option).data('currency-data', currencyData);

                            $('[name="currcode"]').append(option).trigger('change');

                            setJtsValue('[name="kurs"]', convertToDbNumber(currencyData.kurs || 1));
                            $('[name="kurs"]').prop('readonly', false);
                        }
                    }).fail(function() {
                        console.error('Failed to load currency data');
                    });
                }

                if (res.idtax) {
                    $('[name="idtax"]').empty().trigger('change');

                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_tax' + '?var=' + res.idtax,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax.items && datax.items.length > 0) {
                            var option = new Option(datax.items[0].nmtax, datax.items[0].idtax, true, true);
                            $('[name="idtax"]').append(option).trigger('change');

                            $('[name="idtax"]').trigger({
                                type: 'select2:select',
                                params: {
                                    data: datax
                                }
                            });
                        }
                    }).fail(function() {
                        console.error('Failed to load tax data');
                    });
                }
            }
        });
    }

});
function loadNextSuffixLPB() {

    let prefix = $.trim($('#prefix').val()).toUpperCase();
    let infix = $.trim($('#infix').val());

    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + '/purchase/trans/getNextSuffixLPB',
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
            let suffix = $.trim(
                res.suffix || ''
            );

            $('#suffix')
                .val(suffix)
                .trigger('change');
            $('#docno').val(
                prefix + '/' + infix + '/' + res.suffix
            );
        }
    });
};


$('#prefix').on('blur', function () {
    loadNextSuffixLPB();
});


function cleanSuffix(value) {
    return String(value || '')
        .trim()
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, '')
        .substring(0, 6);
}


var defaultInitialBranch = '';
$("#cabang").select2({
    placeholder: "Type/ Choose your Branch",
    allowClear: true,
    width: '100%',
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_branchjob',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term,
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
    },
    templateResult: formatBranch,
    templateSelection: formatBranchSelection
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
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
                _search_: params.term,
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialNewDept,
            };
        },
        processResults: function(data, params) {
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
    },
    templateResult: formatNewdept,
    templateSelection: formatNewdeptSelection
}).on("select2:selecting", function () {
});

$("#fjurnal").on("change", function () {
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
function new_spec() {

    // =============================================
    // RESET INPUT
    // =============================================

    $('#newbatch')
        .val('')
        .prop('disabled', false)
        .prop('readonly', false);


    // =============================================
    // OPEN MODAL BOOTSTRAP 5
    // =============================================

    const modalElement =
        document.getElementById('modalNewSpec');


    const modalNewSpec =
        bootstrap.Modal.getOrCreateInstance(
            modalElement
        );


    modalNewSpec.show();


    // =============================================
    // FOCUS INPUT
    // =============================================

    modalElement.addEventListener(
        'shown.bs.modal',
        function () {

            $('#newbatch').focus();

        },
        {
            once: true
        }
    );

}
function save_new_spec() {

    // =============================================
    // AMBIL DATA
    // =============================================

    const newbatch =
        $.trim(
            $('#newbatch').val() || ''
        ).toUpperCase();


    const idbarang =
        $.trim(
            $('[name="idbarang"]').val() || ''
        );


    // =============================================
    // VALIDASI BATCH
    // =============================================

    if (newbatch === '') {

        $('#newbatch').focus();

        alert(
            'Batch harus diisi.'
        );

        return;

    }


    // =============================================
    // VALIDASI BARANG
    // =============================================

    if (idbarang === '') {

        alert(
            'Item Barang belum dipilih.'
        );

        return;

    }


    $.ajax({

        type: 'POST',

        url:
            HOST_URL +
            'api/globalmodule/add_newbatch',

        dataType: 'json',

        data: {

            idbarang: idbarang,

            batch: newbatch

        },


        success: function (datax) {

            if (datax.status) {

                // =========================================
                // 🔧 MASUKKAN KE INPUT BATCH (sebelumnya idspec)
                // =========================================

                $('#batch')
                    .prop('disabled', false)
                    .prop('readonly', false)
                    .append(new Option(newbatch, newbatch, true, true))
                    .val(newbatch)
                    .trigger('change');


                // =========================================
                // TUTUP MODAL NEW SPEC
                // =========================================

                const modalElement =
                    document.getElementById(
                        'modalNewSpec'
                    );


                const modalInstance =
                    bootstrap.Modal.getInstance(
                        modalElement
                    );


                if (modalInstance) {

                    modalInstance.hide();

                }


                // =========================================
                // FOCUS KEMBALI KE BATCH
                // =========================================

                setTimeout(function () {

                    $('#batch').focus();

                }, 300);


                // =========================================
                // NOTIFIKASI
                // =========================================

                Swal.fire({

                    icon: 'success',

                    title: 'Berhasil',

                    text:
                        datax.messages ||
                        'Batch berhasil disimpan.'

                });

            } else {

                alert(
                    datax.messages ||
                    'Gagal menyimpan Batch.'
                );

            }

        },


        error: function (xhr) {

            console.error(
                xhr.responseText
            );


            alert(
                'Unable To Response Data'
            );

        }

    });

}
// 🔧 BATCH (sebelumnya idspec)
$("#batch").select2({

    placeholder: "Silahkan pilih batch, click x untuk reset pilihan",

    allowClear: true,

    dropdownParent: $("#modalUpdateLPB"),

    ajax: {

        url: HOST_URL + 'api/globalmodule/list_batch_item',

        type: 'POST',

        dataType: 'json',

        delay: 250,


        data: function (params) {

            return {

                _search_: params.term,

                _page_: params.page,

                _draw_: true,

                _start_: 1,

                _perpage_: 30,

                _paramglobal_: "",

                _parameterx_: $('[name="idbarang"]').val(),

                term: params.term

            };

        },


        processResults: function (data, params) {

            params.page = params.page || 1;


            var results = $.map(
                data.items || [],
                function (item) {

                    return {

                        id: item.batch,
                        text: item.batch,

                        batch: item.batch

                    };

                }
            );


            return {

                results: results,

                pagination: {

                    more:
                        (params.page * 30) <
                        (data.total_count || 0)

                }

            };

        },


        cache: false

    },


    escapeMarkup: function (markup) {

        return markup;

    },


    templateResult: formatBatch,

    templateSelection: formatBatchSelection

}).on("select2:select", function (e) {

    var data = e.params.data;

    console.log(
        'Batch dipilih:',
        data.batch
    );

});

/* Format Group */
function formatBatch(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.batch +"</div>";
    return markup;
}
function formatBatchSelection(repo) {
    return repo.batch || repo.text;
}


$('#btnDeleteLPB').on('click', function () {

    let docno = $.trim($('#docno').val());

    if (docno === '') {

        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Document Number LPB tidak ditemukan.'
        });

        return;
    }


    Swal.fire({
        title: 'Hapus Penerimaan?',
        html:
            'Yakin ingin menghapus penerimaan:<br><br>' +
            '<b>' + docno + '</b><br><br>' +
            '<span class="text-danger">' +
            'Data LPB, transaksi inventory dan jurnal terkait akan direverse.' +
            '</span>',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',

        confirmButtonText:
            '<i class="fa fa-trash"></i> Ya, Hapus',

        cancelButtonText: 'Cancel'

    }).then((result) => {

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({

            url: HOST_URL + 'purchase/trans/delete_document_lpb',

            type: 'POST',

            dataType: 'json',

            data: {
                docno: docno
            },


            beforeSend: function () {

                $('#btnDeleteLPB')
                    .prop('disabled', true)
                    .html(
                        '<i class="fa fa-spinner fa-spin"></i> Processing...'
                    );

            },


            success: function (response) {

                if (response.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text:
                            response.message ||
                            'Penerimaan berhasil dihapus.',

                        allowOutsideClick: false,
                        allowEscapeKey: false

                    }).then(() => {

                        window.location.href =
                            HOST_URL + 'purchase/trans/lpb';

                    });

                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text:
                        response.message ||
                        'Gagal menghapus penerimaan.'
                });


                $('#btnDeleteLPB')
                    .prop('disabled', false)
                    .html(
                        '<i class="fa fa-trash"></i> Hapus Penerimaan'
                    );

            },


            error: function (xhr) {

                console.error(
                    'DELETE LPB ERROR:',
                    xhr.responseText
                );


                let message =
                    'Terjadi kesalahan saat menghapus penerimaan.';


                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {

                    message =
                        xhr.responseJSON.message;

                }


                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });


                $('#btnDeleteLPB')
                    .prop('disabled', false)
                    .html(
                        '<i class="fa fa-trash"></i> Hapus Penerimaan'
                    );

            }

        });

    });

});

function setLPBHeaderLock(isLocked) {

    $('#cabang').prop('disabled', isLocked);

    $('#prefix').prop('readonly', isLocked);
    $('#suffix').prop('readonly', isLocked);

    $('#docdate').prop('readonly', isLocked);

    $('#kdsupplier').prop('disabled', isLocked);

    $('#idtax').prop('disabled', isLocked);

    $('#currcode').prop('disabled', isLocked);

    $('#isinclusive').prop('disabled', isLocked);

    $('#cabang').trigger('change.select2');
    $('#kdsupplier').trigger('change.select2');
    $('#idtax').trigger('change.select2');
    $('#currcode').trigger('change.select2');
}


function checkLPBDetailAndLock() {

    const table = $('#tablpbdtl').DataTable();

    if (!table) {
        return;
    }

    const info = table.page.info();

    const totalDetail = parseInt(
        info.recordsDisplay || 0,
        10
    );

    setLPBHeaderLock(totalDetail > 0);
}

$(document).ready(function() {
    tableLPBTrx();
    tableLPBDetail();

    // 🔧 Inisialisasi daterangepicker untuk ETD di modal update
    $('#etd').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        locale: { format: 'DD-MM-YYYY' },
        cancelLabel: 'Clear'
    });

    $('#etd').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
    });
    $('#etd').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('#checkboxnik').change(function() {
        if (this.checked) {
            var valnik = $('#nik').val();
            $('#username').prop('readonly', true);
            $('#username').val(valnik);
        } else {
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

    documentReadable();
    $("#loadMe").modal("hide");
});
