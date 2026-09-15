


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
let isLoadingNDKEdit = false;


//EDIT ITEM
function documentReadable() {
    const docno = $.trim($('[name="docno"]').val() || '');

    if (!docno) {
        console.warn('documentReadable(): docno kosong');
        return;
    }

    const $form = $('#formPO');

    $("#loadMe").modal({
        backdrop: "static",
        keyboard: false,
        show: true
    });

    function getItem(response) {
        if (!response) {
            return null;
        }

        const dataTables = response.dataTables || response;
        const items = dataTables.items || [];

        return items.length ? items[0] : null;
    }

    function setSelect2Value(selector, id, text, disabled) {
        const $select = $(selector);

        if (!$select.length || id === undefined || id === null || id === '') {
            return;
        }

        $select.find('option').filter(function () {
            return String($(this).val()) === String(id);
        }).remove();

        const option = new Option(
            text || id,
            id,
            true,
            true
        );

        $select.append(option);

        $select.val(id).trigger('change');

        if (disabled !== undefined) {
            $select.prop('disabled', disabled);
        }
    }

    function addHiddenValue(name, value) {
        if (!$form.length) {
            return;
        }

        let $hidden = $form.find(
            'input[type="hidden"][data-ndk-hidden="' + name + '"]'
        );

        if (!$hidden.length) {
            $hidden = $('<input>', {
                type: 'hidden',
                name: name,
                'data-ndk-hidden': name
            }).appendTo($form);
        }

        $hidden.val(value == null ? '' : value);
    }

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'arap/transaksi/showing_ndktemp',
        data: {
            docno: docno
        },
        dataType: 'json',
        cache: false
    })
        .done(function (response) {

            const item = getItem(response);

            if (!item) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data tidak ditemukan',
                    text: 'Data NDK tidak ditemukan.'
                });

                return;
            }

            // ======================================================
            // DOCNO
            // ======================================================

            const loadedDocno = $.trim(item.docno || '');

            $('[name="docno"]')
                .val(loadedDocno)
                .prop('readonly', true);

            const prefixParts = loadedDocno.split('/');

            $('[name="prefix"]')
                .val(prefixParts[0] || '')
                .prop('readonly', true);

            $('[name="infix"]')
                .val(prefixParts[1] || '')
                .prop('readonly', true);

            $('[name="sufix"]')
                .val(prefixParts[2] || '')
                .prop('readonly', true);

            if (prefixParts[2]) {
                defaultInitialPO = prefixParts[2].substring(0, 2);
            }

            // ======================================================
            // SUPPLIER
            // ======================================================

            const supplierReq = $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_cust_and_supplier',
                data: {
                    var: item.kdsupplier || ''
                },
                dataType: 'json',
                cache: false
            }).then(function (response) {

                const supplier = getItem(response);

                if (!supplier) {
                    return;
                }

                supplier.alamat = item.alamatsupplier || '';

                setSelect2Value(
                    '[name="kdsupplier"]',
                    supplier.kode,
                    supplier.nama || supplier.kode,
                    false
                );

                $('[name="kdsupplier"]')
                    .find('option:selected')
                    .data('supplier-data', supplier);

                $('[name="alamatsupplier"]')
                    .val(item.alamatsupplier || '')
                    .prop('readonly', true);
            });

            // ======================================================
            // CABANG
            // ======================================================

            const branchReq = $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_branchjob',
                data: {
                    var: item.cabang || ''
                },
                dataType: 'json',
                cache: false
            }).then(function (response) {

                const branch = getItem(response);

                if (!branch) {
                    return;
                }

                setSelect2Value(
                    '[name="cabang"]',
                    branch.idbranch,
                    branch.nmbranch || branch.idbranch,
                    true
                );

                // disabled select tidak ikut submit
                addHiddenValue(
                    'cabang',
                    branch.idbranch
                );

                $('[name="cabang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: branch
                    }
                });
            });

            // ======================================================
            // TAX
            // ======================================================

            const taxReq = $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_tax',
                data: {
                    var: item.idtax || ''
                },
                dataType: 'json',
                cache: false
            }).then(function (response) {

                const tax = getItem(response);

                if (!tax) {
                    return;
                }

                setSelect2Value(
                    '[name="idtax"]',
                    tax.idtax,
                    tax.nmtax || tax.idtax,
                    false
                );

            });

            // ======================================================
            // COA ARAP
            // ======================================================

            const arapReq = $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_coa',
                data: {
                    var: item.perkiraanarap || ''
                },
                dataType: 'json',
                cache: false
            }).then(function (response) {

                const coa = getItem(response);

                if (!coa) {
                    return;
                }

                setSelect2Value(
                    '[name="perkiraanarap"]',
                    coa.idcoa,
                    coa.nmcoa || coa.idcoa,
                    false
                );

                $('[name="perkiraanarap"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: coa
                    }
                });
            });

            // ======================================================
            // SALESMAN
            // ======================================================

            const salesmanReq = $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_salesman',
                data: {
                    var: item.kdsalesman || ''
                },
                dataType: 'json',
                cache: false
            }).then(function (response) {

                const salesman = getItem(response);

                if (!salesman) {
                    return;
                }

                setSelect2Value(
                    '[name="kdsalesman"]',
                    salesman.kdsalesman,
                    salesman.nmsalesman || salesman.kdsalesman,
                    false
                );

                $('[name="kdsalesman"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: salesman
                    }
                });
            });

            // ======================================================
            // COA LAWAN
            // ======================================================

            const lawanReq = $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_coa',
                data: {
                    var: item.perkiraanlawan || ''
                },
                dataType: 'json',
                cache: false
            }).then(function (response) {

                const coa = getItem(response);

                if (!coa) {
                    return;
                }

                setSelect2Value(
                    '[name="perkiraanlawan"]',
                    coa.idcoa,
                    coa.nmcoa || coa.idcoa,
                    false
                );

                $('[name="perkiraanlawan"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: coa
                    }
                });
            });

            // ======================================================
            // CURRENCY
            // ======================================================

            const currencyReq = $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_currency',
                data: {
                    var: item.currcode || ''
                },
                dataType: 'json',
                cache: false
            }).then(function (response) {

                const currency = getItem(response);

                if (!currency) {
                    return;
                }

                currency.kurs = item.kurs;

                setSelect2Value(
                    '[name="currcode"]',
                    currency.currcode,
                    currency.currname || currency.currcode,
                    false
                );

                $('[name="currcode"]')
                    .find('option:selected')
                    .data('currency-data', currency);

                setJtsValue(
                    '[name="kurs"]',
                    convertToDbNumber(item.kurs)
                );

                $('[name="kurs"]').prop(
                    'readonly',
                    false
                );
            });

            // ======================================================
            // FIELD
            // ======================================================

            skipRoleChange = true;

            if (item.docdate) {
                const dateValue = moment(item.docdate);

                if (dateValue.isValid()) {
                    $('[name="docdate"]').val(
                        dateValue.format('DD-MM-YYYY')
                    );
                }
            }

            setJtsValue(
                '[name="jthtempo"]',
                Math.round(convertToDbNumber(item.jthtempo))
            );

            $('[name="isinclusive"]').prop(
                'checked',
                $.trim(item.isinclusive || '').toUpperCase() === 'YES'
            );

            $('[name="alamatsupplier"]')
                .val(item.alamatsupplier || '');

            $('[name="keterangan"]')
                .val(item.keterangan || '');

            $('[name="dk"]')
                .val($.trim(item.dk || ''))
                .trigger('change');



// ======================================================
// NILAI TRANSAKSI EDIT
// ======================================================

            isLoadingNDKEdit = true;

            console.log('====================================');
            console.log('        NDK EDIT NILAI TRANSAKSI');
            console.log('====================================');

            console.log('JSON DPP          =', item.dpp);
            console.log('JSON JUMLAH PAJAK =', item.jumlahpajak);
            console.log('JSON TOTAL        =', item.total);


// ======================================================
// HELPER FIELD
// ======================================================

            function getNDKField(id, name) {

                let $el = $('#' + id);

                if (!$el.length && name) {
                    $el = $('[name="' + name + '"]');
                }

                // Kalau ada lebih dari satu, ambil yang visible
                if ($el.length > 1) {
                    let $visible = $el.filter(':visible');

                    if ($visible.length) {
                        $el = $visible.first();
                    } else {
                        $el = $el.first();
                    }
                }

                return $el;
            }


// ======================================================
// PARSE NUMBER DARI JSON
// ======================================================

            function parseNDKEditNumber(value) {

                if (
                    value === null ||
                    value === undefined ||
                    value === ''
                ) {
                    return 0;
                }

                let str = String(value).trim();

                // Hilangkan separator ribuan koma
                str = str.replace(/,/g, '');

                let number = Number(str);

                if (isNaN(number)) {
                    console.warn(
                        'Gagal parse angka NDK:',
                        value
                    );

                    return 0;
                }

                return number;
            }


// ======================================================
// FORMAT
// ======================================================

            function formatNDKEditNumber(value) {

                return Number(value || 0).toLocaleString(
                    'en-US',
                    {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    }
                );

            }


// ======================================================
// AMBIL NILAI JSON
// ======================================================

            const dppEdit =
                parseNDKEditNumber(item.dpp);

            const jumlahPajakEdit =
                parseNDKEditNumber(item.jumlahpajak);

            const totalEdit =
                parseNDKEditNumber(item.total);


            console.log(
                'PARSED DPP          =',
                dppEdit
            );

            console.log(
                'PARSED JUMLAH PAJAK =',
                jumlahPajakEdit
            );

            console.log(
                'PARSED TOTAL        =',
                totalEdit
            );


// ======================================================
// FIELD
// ======================================================

            // const $dpp =
            //     getNDKField('dpp', 'dpp');

            const $dpp = getNDKField('dpp', 'dpp');

            console.log('========== CEK FIELD DPP ==========');
            console.log('jumlah #dpp       :', $('#dpp').length);
            console.log('jumlah name=dpp   :', $('[name="dpp"]').length);
            console.log('jumlah input      :', $('input').length);
            console.log('element #dpp      :', $('#dpp')[0]);
            console.log('element name=dpp  :', $('[name="dpp"]')[0]);
            console.log('===================================');

            const $jumlahPajak =
                getNDKField('jumlahpajak', 'jumlahpajak');

            const $total =
                getNDKField('total', 'total');


            console.log(
                'DPP ELEMENT          =',
                $dpp.length,
                $dpp
            );

            console.log(
                'JUMLAH PAJAK ELEMENT =',
                $jumlahPajak.length,
                $jumlahPajak
            );

            console.log(
                'TOTAL ELEMENT        =',
                $total.length,
                $total
            );


// ======================================================
// SET DPP
// ======================================================

            if ($dpp.length) {

                $dpp
                    .val(formatNDKEditNumber(dppEdit))
                    .prop('readonly', false);

            }
            console.log('SET DPP =', formatNDKEditNumber(dppEdit));
            console.log('HASIL DPP =', $dpp.val());

// ======================================================
// SET JUMLAH PAJAK
// ======================================================

            if ($jumlahPajak.length) {

                $jumlahPajak
                    .val(formatNDKEditNumber(jumlahPajakEdit));

            }


// ======================================================
// SET TOTAL
// ======================================================

            if ($total.length) {

                $total
                    .val(formatNDKEditNumber(totalEdit));

            }


// ======================================================
// PASTIKAN VALUE SUDAH MASUK
// ======================================================

            console.log(
                '===================================='
            );

            console.log(
                'FIELD DPP          =',
                $dpp.length ? $dpp.val() : 'ELEMENT TIDAK ADA'
            );

            console.log(
                'FIELD JUMLAH PAJAK =',
                $jumlahPajak.length
                    ? $jumlahPajak.val()
                    : 'ELEMENT TIDAK ADA'
            );

            console.log(
                'FIELD TOTAL        =',
                $total.length
                    ? $total.val()
                    : 'ELEMENT TIDAK ADA'
            );

            console.log(
                '====================================');


// ======================================================
// HIDDEN VALUE
// ======================================================

            addHiddenValue(
                'cabang',
                item.cabang || ''
            );

            addHiddenValue(
                'idtax',
                item.idtax || ''
            );


// ======================================================
// SELESAI LOAD EDIT
// ======================================================

            skipRoleChange = false;

            isLoadingNDKEdit = false;



// ------------------------------------------------------
// DISABLED FIELD
// ------------------------------------------------------

            addHiddenValue(
                'cabang',
                item.cabang || ''
            );

            addHiddenValue(
                'idtax',
                item.idtax || ''
            );

                skipRoleChange = false;


        })
        .fail(function (jqXHR, textStatus, errorThrown) {

            console.error(
                'Failed To Loading Data:',
                textStatus,
                errorThrown,
                jqXHR.responseText
            );

            Swal.fire({
                icon: 'error',
                title: 'Gagal memuat data',
                text: 'Data NDK gagal dimuat.'
            });
        })
        .always(function () {
            $("#loadMe").modal("hide");
        });
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


// =====================================================
// PAJAK NDK - FAST CALCULATION
// =====================================================

let taxPercentCache = {};
let taxRequest = null;
let taxRequestSeq = 0;
let taxCalcTimer = null;


// -----------------------------------------------------
// Parse angka
// -----------------------------------------------------
function getNDKNumber(value) {

    if (value === null || value === undefined || value === '') {
        return 0;
    }

    let str = String(value)
        .replace(/,/g, '')
        .replace(/\s/g, '');

    let number = parseFloat(str);

    return isNaN(number) ? 0 : number;
}


// -----------------------------------------------------
// Format angka
// -----------------------------------------------------
function formatNDKNumber(value) {

    return Number(value || 0).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });

}


// -----------------------------------------------------
// HITUNG PAJAK
// Tidak melakukan AJAX jika percent sudah tersedia
// -----------------------------------------------------
function hitungPajak() {

    // Jangan hitung pajak ketika sedang loading data EDIT
    if (isLoadingNDKEdit) {
        return;
    }

    let dpp = getNDKNumber($('[name="dpp"]').val());
    let idtax = $('[name="idtax"]').val();
    let isinclusive = $('[name="isinclusive"]').is(':checked');

    // =============================================
    // DPP kosong / 0
    // =============================================
    if (dpp <= 0) {

        $('#jumlahpajak').val('0');
        $('#total').val('0');

        return;
    }


    // =============================================
    // INCLUSIVE
    // =============================================
    if (isinclusive) {

        $('#jumlahpajak').val('0');
        $('#total').val(formatNDKNumber(dpp));

        return;
    }


    // =============================================
    // TANPA TAX
    // =============================================
    if (!idtax) {

        $('#jumlahpajak').val('0');
        $('#total').val(formatNDKNumber(dpp));

        return;
    }


    // =============================================
    // AMBIL PERCENT DARI CACHE
    // =============================================
    if (taxPercentCache[idtax] !== undefined) {

        calculateTaxNDK(
            dpp,
            taxPercentCache[idtax]
        );

        return;
    }


    // =============================================
    // LOAD TAX PERCENT
    // HANYA SEKALI
    // =============================================

    let requestSeq = ++taxRequestSeq;

    if (taxRequest) {
        taxRequest.abort();
    }


    taxRequest = $.ajax({

        url: HOST_URL + 'api/globalmodule/get_tax_percent',

        type: 'POST',

        data: {
            idtax: idtax
        },

        dataType: 'json',

        success: function(res) {

            // =====================================
            // ABAIKAN RESPONSE LAMA
            // =====================================
            if (requestSeq !== taxRequestSeq) {
                return;
            }


            let percent = parseFloat(res.percent) || 0;


            // =====================================
            // SIMPAN CACHE
            // =====================================
            taxPercentCache[idtax] = percent;


            // =====================================
            // HITUNG LANGSUNG
            // =====================================
            let currentDpp = getNDKNumber(
                $('#dpp').val()
            );

            let currentTax = $('#idtax').val();


            // Pastikan tax masih sama
            if (currentTax !== idtax) {
                return;
            }


            calculateTaxNDK(
                currentDpp,
                percent
            );

        },

        error: function(xhr, status) {

            if (status !== 'abort') {

                console.error(
                    'Gagal mengambil persentase pajak'
                );

            }

        },

        complete: function() {
            taxRequest = null;
        }

    });

}


// -----------------------------------------------------
// CALCULATE TANPA AJAX
// -----------------------------------------------------
function calculateTaxNDK(dpp, percent) {

    dpp = parseFloat(dpp) || 0;
    percent = parseFloat(percent) || 0;


    let jumlahPajak =
        dpp * percent / 100;


    let total =
        dpp + jumlahPajak;


    $('#jumlahpajak').val(
        formatNDKNumber(jumlahPajak)
    );


    $('#total').val(
        formatNDKNumber(total)
    );

}




// -----------------------------------------------------
// INCLUSIVE
// -----------------------------------------------------
$('#isinclusive').on('change', function() {

    if ($(this).is(':checked')) {

        $('#idtax').prop('disabled', true);

    } else {

        $('#idtax').prop('disabled', false);

    }


    // langsung hitung
    hitungPajak();

});


// -----------------------------------------------------
// DPP
// DEBOUNCE 100ms
// -----------------------------------------------------
$('#dpp').on('input', function() {

    clearTimeout(taxCalcTimer);


    taxCalcTimer = setTimeout(function() {

        hitungPajak();

    }, 100);

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
$('#formPO').on('submit', function (e) {

    let dpp = $('#dpp').val()
        .replace(/,/g, '')
        .trim();

    let nilai = parseFloat(dpp) || 0;

    // =============================================
    // VALIDASI NILAI
    // =============================================
    if (nilai <= 0) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Nilai Tidak Valid',
            text: 'Nilai transaksi tidak boleh 0.',
            confirmButtonText: 'OK'
        }).then(() => {
            $('#dpp').focus().select();
        });

        return false;
    }

    return true;
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
        $("#nmsupplier").val(selectedData.nama || '');
        $("#alamatsupplier").val(selectedData.alamat || '');
        $("#jthtempo").val(
            Math.round(parseFloat(selectedData.jthtempo) || 0)
        );
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

    if (skipRoleChange) return;

    let idbranch = $(this).val();

    if (idbranch) {

        $.ajax({

            url: HOST_URL + 'arap/transaksi/getBranchInfoNDK',

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

                // PREFIX KHUSUS NDK
                $('#prefix').val('NTD');

                $('#sufix').val(
                    currentKodeSuffix + '0001'
                );

                defaultInitialPO = currentKodeSuffix;


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

                        var currencyData =
                            datax.items[0];


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
                        // SELECT CURRENCY IDR
                        // =============================================

                        $('[name="currcode"]')
                            .append(option)
                            .val(currencyData.currcode)
                            .trigger('change');


                        // =============================================
                        // FORCE KURS = 1
                        // =============================================

                        setJtsValue(
                            '[name="kurs"]',
                            1
                        );


                        // =============================================
                        // KURS READONLY
                        // =============================================

                        $('[name="kurs"]')
                            .prop(
                                'readonly',
                                true
                            );

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

                    config =
                        res.konfigurasi_umum[0];

                }


                // =====================================================
                // AUTO SELECT TAX
                // =====================================================

                if (config) {

                    var idtax = $.trim(
                        config.idtax || ''
                    );


                    if (idtax !== '') {

                        loadDefaultTax(idtax);

                    }

                }


                // =====================================================
                // DATE RANGE BERDASARKAN INFIX
                // =====================================================

                var infix =
                    (res.infix || '').toString();


                if (infix.length === 4) {

                    $('#docdate').prop(
                        'disabled',
                        false
                    );


                    var yy =
                        infix.substring(0, 2);


                    var mm =
                        infix.substring(2, 4);


                    var year =
                        2000 + parseInt(
                            yy,
                            10
                        );


                    var month =
                        parseInt(
                            mm,
                            10
                        ) - 1;


                    var today =
                        moment();


                    var startDate =
                        moment([
                            year,
                            month,
                            1
                        ]);


                    var endDate =
                        moment(startDate)
                            .endOf('month');


                    var $el =
                        $('#docdate');


                    var drp =
                        $el.data(
                            'daterangepicker'
                        );


                    // =================================================
                    // UPDATE DATERANGEPICKER
                    // =================================================

                    if (drp) {

                        drp.minDate =
                            startDate;

                        drp.maxDate =
                            endDate;

                        drp.setStartDate(
                            startDate
                        );

                        drp.setEndDate(
                            startDate
                        );

                    } else {

                        // =============================================
                        // INITIALIZE DATERANGEPICKER
                        // =============================================

                        $el.daterangepicker({

                            autoUpdateInput: false,

                            singleDatePicker: true,

                            showDropdowns: true,

                            startDate:
                            startDate,

                            minDate:
                            startDate,

                            maxDate:
                            endDate,

                            locale: {
                                format:
                                    'DD-MM-YYYY'
                            },

                            cancelLabel:
                                'Clear'

                        });


                        // =============================================
                        // APPLY DATE
                        // =============================================

                        $el.off(
                            'apply.daterangepicker'
                        );

                        $el.on(
                            'apply.daterangepicker',
                            function (
                                ev,
                                picker
                            ) {

                                $(this).val(
                                    picker
                                        .startDate
                                        .format(
                                            'DD-MM-YYYY'
                                        )
                                );

                            }
                        );


                        // =============================================
                        // CANCEL DATE
                        // =============================================

                        $el.off(
                            'cancel.daterangepicker'
                        );

                        $el.on(
                            'cancel.daterangepicker',
                            function () {

                                $(this).val('');

                            }
                        );

                    }


                    // =================================================
                    // SET TANGGAL DEFAULT
                    // =================================================

                    if (
                        today.isSameOrAfter(
                            startDate
                        ) &&
                        today.isSameOrBefore(
                            endDate
                        )
                    ) {

                        $el.val(
                            today.format(
                                'DD-MM-YYYY'
                            )
                        );

                    } else {

                        $el.val(
                            startDate.format(
                                'DD-MM-YYYY'
                            )
                        );

                    }

                }


                // =====================================================
                // GENERATE DOCNO NDK
                // =====================================================

                $('#docno').val(

                    'NTD/' +
                    res.infix +
                    '/' +
                    currentKodeSuffix +
                    '0001'

                );

            },


            // =========================================================
            // AJAX ERROR
            // =========================================================

            error: function (xhr) {

                console.error(
                    xhr.responseText
                );

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
                    _parameterx_: " and trim(level)='5'",
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
                    _parameterx_: " and trim(level)='5'",
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