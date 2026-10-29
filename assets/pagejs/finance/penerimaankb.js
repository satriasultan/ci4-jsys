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

function tablePenerimaanKBTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablepenerimaankbTrx');

        // Hindari inisialisasi DataTable berulang
        if ($.fn.DataTable.isDataTable('#tablepenerimaankbTrx')) {
            table.DataTable().destroy();
        }

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
            "scrollX": true, //agar kolom tidak wrap, tabel bisa di-scroll ke samping
            "scrollCollapse": true,
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
                "url": HOST_URL + 'ka/finance/list_penerimaankb',
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

            "initComplete": function () {
                var api = this.api();

                // Pastikan lebar kolom dihitung ulang
                setTimeout(function () {
                    api.columns.adjust();
                }, 100);
            },

            "drawCallback": function () {
                this.api().columns.adjust();
            }

        });

    };


    return initTable();
}

function reload_tablePenerimaanKBTrx()
{
    if ($.fn.DataTable.isDataTable('#tablepenerimaankbTrx')) {
        $('#tablepenerimaankbTrx').DataTable().ajax.reload(null, false); //reload datatable ajax
    } else {
        tablePenerimaanKBTrx();
    }
    //console.log('HALO HALO BANDUNG');
}


//EDIT ITEM
function documentReadable(){
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: false //Display loader!
    // });
    var docno = $('[name="docno"]').val()

    // Halaman list memuat file JS yang sama, tetapi tidak punya input docno.
    // Lewati proses ini bila nomor dokumen belum tersedia.
    if (docno === undefined || docno === null || String(docno).trim() === '') {
        $("#loadMe").modal("hide");
        return;
    }

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'ka/finance/showing_penerimaankbtemp',
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
            // Suffix tetap dapat diubah user (maks. 6 karakter)
            $('[name="suffix"]').val(prefixParts.slice(2).join('/') || '');
            defaultInitialPP = (prefixParts[2] || '').substring(0, 2);

            // Kode suffix (PT/PA/PB) diambil dari nomor dokumen yang ada
            if (prefixParts[2]) {
                currentKodeSuffix = prefixParts[2].substring(0, 2);
            }

            // Header sudah tersimpan -> kunci header
            // (hanya #total dan #keterangan yang tetap dapat diubah)
            lockPenerimaanKBHeader();


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
                url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + json.dataTables.items[0].prkkas,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmcoa, datax.items[0].idcoa, true, true);
                $('[name="prkkas"]').append(option).trigger('change').prop('disabled',true);

                // manually trigger the `select2:select` event
                $('[name="prkkas"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_customer' + '?var=' + json.dataTables.items[0].kdcustomer,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {

            // Tambahkan data alamat dan phone ke object
                var customerData = datax.items[0];
                customerData.alamat_kantor = json.dataTables.items[0].alamatcustomer;
                // customerData.phone = data.phone;
                
                // create the option dan simpan data lengkap
                var option = new Option(customerData.nmcustomer, customerData.kdcustomer, true, true);
                $(option).data('customer-data', customerData); // Simpan data lengkap
                
                $('[name="kdcustomer"]').append(option).trigger('change');
                
                // Set alamat dan phone langsung
                $("#alamatcustomer").val(customerData.alamat_kantor).prop('readonly', true);
                // $("#phone").val(data.phone).prop('readonly', true);
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
                $('[name="kurs"]').prop('readonly', true);
                $('[name="currcode"]').prop('disabled', true);
                // $("#phone").val(data.phone).prop('readonly', true);
            });
            skipRoleChange = true;
            // Tanggal ditampilkan dalam format dd-mm-yyyy
            $('[name="docdate"]').val(
                penerimaanKBDateDisplay(json.dataTables.items[0].docdate)
            ).prop('readonly', true);
            // $('[name="senddate"]').val(json.dataTables.items[0].senddate);
            // setJtsValue('[name="jthtempo"]', convertToDbNumber(json.dataTables.items[0].jthtempo));
            // setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
            // $('[name="isinclusive"]').prop(
            //     'checked',
            //     $.trim((json.dataTables.items[0].isinclusive || '')).toUpperCase() === 'YES'
            // );
            // // $('[name="jthtempo"]').val(json.dataTables.items[0].jthtempo);
            // $('[name="alamatsupplier"]').val(json.dataTables.items[0].alamatsupplier);
            // $('[name="alamatkirim"]').val(json.dataTables.items[0].alamatkirim);
            // $('[name="isinclusive"]').val(json.dataTables.items[0].isinclusive);
            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);
            // $('[name="syarat"]').val(json.dataTables.items[0].syarat);

            // var docnoUMB = (json.dataTables.items[0].docnoumb || '').trim();
            // if(docnoUMB === ''){
            //     $('#btnDP').prop('disabled', false);
            //     $('#btnDPWrapper').attr('title','Create Down Payment');
            // }else{
            //     $('#btnDP').prop('disabled', true);
            //     $('#btnDPWrapper').attr('title','PO sudah ada Down Payment');
            // }
            // setJtsValue('[name="total"]', convertToDbNumber(json.dataTables.items[0].total));
            // Nilai diterima (header) -> #total dan hidden #dpp
            setJtsValue('[name="total"]', convertToDbNumber(json.dataTables.items[0].dpp));
            $('#dpp').val(convertToDbNumber(json.dataTables.items[0].dpp));

            $('#total_awal').val(json.dataTables.items[0].total);
            $('#balance_awal').val(json.dataTables.items[0].balance);
            $('#balance').val(json.dataTables.items[0].balance);

            // Status tombol final diatur oleh hitungBalance()
            // setelah tabel detail selesai dimuat.
            // setJtsValue('[name="jumlahpajak"]', convertToDbNumber(json.dataTables.items[0].jumlahpajak));
            // setJtsValue('[name="total"]', convertToDbNumber(json.dataTables.items[0].total));
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

$(document).on('input', '#total', function () {
    hitungBalance();
});

/* =========================================================================
 * TANGGAL: tampilkan selalu dd-mm-yyyy
 * ========================================================================= */
function penerimaanKBDateDisplay(value) {
    const raw = String(value == null ? '' : value).trim();

    if (raw === '') {
        return '';
    }

    // yyyy-mm-dd -> dd-mm-yyyy
    let m = raw.match(/^(\d{4})-(\d{1,2})-(\d{1,2})/);

    if (m) {
        return String(m[3]).padStart(2, '0') + '-' +
            String(m[2]).padStart(2, '0') + '-' + m[1];
    }

    // dd-mm-yyyy / dd/mm/yyyy -> dd-mm-yyyy
    m = raw.match(/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/);

    if (m) {
        return String(m[1]).padStart(2, '0') + '-' +
            String(m[2]).padStart(2, '0') + '-' + m[3];
    }

    return raw;
}

/* =========================================================================
 * TOTAL ALOKASI = summary nilai detail
 *   KREDIT menambah, DEBIT mengurangi (baris hasil "Tarik Piutang" = KREDIT)
 *   Dokumen hanya dapat difinalisasi bila Total Alokasi = #total
 * ========================================================================= */
function refreshPenerimaanKBTotalAlokasi() {
    const $table = $('#tabpenerimaankbdtl');
    let total = 0;

    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().rows().every(function () {
            const row = this.data();

            if (!Array.isArray(row)) {
                return;
            }

            const dk = String(row[4] ?? '').trim().toUpperCase();
            const nilai = penerimaanKBNumber($('<div>').html(row[6] ?? '').text());

            if (dk === 'KREDIT' || dk === 'K' || dk === 'CREDIT' || dk === 'CR') {
                total += nilai;
            } else if (dk === 'DEBIT' || dk === 'D') {
                total -= nilai;
            }
        });
    }

    $('#totalalokasi').val(total.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));

    return total;
}

/* =========================================================================
 * SELISIH = #total (nilai yang harus dipenuhi) - #totalalokasi (summary detail)
 *   > 0  => alokasi masih KURANG
 *   < 0  => alokasi sudah LEBIH
 *   0    => sudah terpenuhi (boleh difinalisasi)
 * ========================================================================= */
function penerimaanKBSisaBalance() {
    const total = penerimaanKBNumber($('#total').val());
    const totalAlokasi = refreshPenerimaanKBTotalAlokasi();

    const selisih = total - totalAlokasi;

    return Math.abs(selisih) < 0.005 ? 0 : selisih;
}

/* =========================================================================
 * AUTO FILL DETAIL SETELAH MEMILIH COA (#idcoa)
 *   1. #dk    -> ter-trigger otomatis mengikuti selisih:
 *               #totalalokasi < #total  -> 'K' (menambah alokasi)
 *               #totalalokasi > #total  -> 'D' (mengurangi alokasi)
 *   2. #nilai -> ter-trigger otomatis diisi SELISIH #total dan #totalalokasi
 *               (hanya untuk input detail baru, tidak saat edit)
 * ========================================================================= */
function penerimaanKBAutoFillDetail(coaData) {

    // Selisih = #total - #totalalokasi
    const selisih = penerimaanKBSisaBalance();

    // 1) DK otomatis: kurang -> 'K', lebih -> 'D'
    const dk = selisih < 0 ? 'D' : 'K';

    $('#dk').val(dk).trigger('change');

    // 2) Nilai otomatis = selisih (nilai absolut)
    const isEdit = $.trim($('#idurut').val() || '') !== '';

    if (!isEdit) {
        const nilai = Math.abs(selisih);

        if (nilai > 0) {
            setJtsValue('[name="nilai"]', convertToDbNumber(nilai));
        }
    }

    return dk;
}

function hitungBalance() {

    const total = penerimaanKBNumber($('#total').val());
    const totalAlokasi = refreshPenerimaanKBTotalAlokasi();

    // Selisih: harus 0 agar dokumen dapat difinalisasi
    const selisih = total - totalAlokasi;

    // Nilai diterima juga disimpan ke hidden #dpp (kompatibilitas backend)
    $('#dpp').val(total);

    // #balance disembunyikan, hanya disimpan sebagai data
    $('#balance').val(selisih.toFixed(2));

    // Tombol final hanya aktif bila #total = #totalalokasi
    $('button[type="submit"]').prop('disabled', Math.abs(selisih) > 0.005);

    return selisih;
}

/* Simbol mata uang pada input Nilai modal detail */
function updateCurrencySymbol() {
    const currcode = ($('#currcode').val() || 'IDR').trim().toUpperCase();

    $('.currency-symbol').text(currcode !== '' ? currcode : 'IDR');
}

$(document).on('change', '#currcode', function () {
    updateCurrencySymbol();
});

$(document).ready(function () {
    updateCurrencySymbol();
});

/* =========================================================================
 * VALIDASI SEBELUM FINALISASI
 *   - Perkiraan Kas/Bank (prkkas) wajib diisi (COA lawan jurnal BANKIN)
 *   - Tanggal dokumen wajib diisi
 *   - #total wajib > 0 dan harus SAMA dengan #totalalokasi
 * ========================================================================= */
function penerimaanKBNumber(value) {
    if (value === null || value === undefined || value === '') {
        return 0;
    }

    let str = String(value).trim().replace(/[^\d,.-]/g, '');

    if (str.includes(',')) {
        str = str.replace(/,/g, '');
    }

    const number = Number(str);

    return Number.isFinite(number) ? number : 0;
}

$(document).on('submit.penerimaanKBFinal', '#formPO', function (e) {

    const prkkas = $.trim($('#prkkas').val() || '');

    if (prkkas === '') {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Perkiraan Kas/Bank (prkkas) wajib dipilih sebelum finalisasi.'
        });

        return false;
    }

    const docdate = $.trim($('#docdate').val() || '');

    if (docdate === '') {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Tanggal dokumen wajib diisi sebelum finalisasi.'
        });

        return false;
    }

    // #total harus SAMA dengan #totalalokasi (tidak ada validasi #balance)
    const total = penerimaanKBNumber($('#total').val());

    if (total <= 0) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Nilai yang harus dipenuhi wajib diisi dan lebih besar dari 0.'
        });

        return false;
    }

    const totalAlokasi = refreshPenerimaanKBTotalAlokasi();
    const selisih = hitungBalance();

    if (Math.abs(selisih) > 0.005) {
        e.preventDefault();

        const fmt = function (value) {
            return value.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };

        Swal.fire({
            icon: 'warning',
            title: 'Tidak Dapat Difinalisasi',
            html: 'Nilai (#total) harus sama dengan Total Alokasi (#totalalokasi).' +
                '<br><br>Nilai: <b>' + fmt(total) + '</b>' +
                '<br>Total Alokasi: <b>' + fmt(totalAlokasi) + '</b>' +
                '<br>Selisih: <b>' + fmt(Math.abs(selisih)) +
                (selisih > 0 ? ' (kurang)' : ' (lebih)') + '</b>'
        });

        return false;
    }
});
/* FOR INPUT FUNCTION */


// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH GROUP ++++++++++++++++++++++++++++++++++++++++//

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


var defaultInitialCOA = '';
$("#prkkas").select2({
    placeholder: "Choose Your COA",
    allowClear: true,
    width:'100%',
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
                _paramglobal_: defaultInitialCOA,
                _parameterx_: " and trim(level)='5'",
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    // $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    // $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $("#batch").val(null).trigger('change');
});


var defaultInitialCOA = '';
$("#idcoa").select2({
    placeholder: "Choose Your COA",
    allowClear: true,
    dropdownParent: $('#modalDetailPenerimaanKB'),
    width:'100%',
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
                _paramglobal_: defaultInitialCOA,
                _parameterx_: " and trim(level)='5'",
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    $('[name="nmcoa"]').val(data.nmcoa.trim()).prop("readonly", true);

    // =================================================
    // AUTO TRIGGER #dk DAN #nilai setelah COA dipilih
    //   - #dk    : 'D' bila COA = Perk. Kas/Bank header, selain itu 'K'
    //   - #nilai : otomatis terisi sisa balance (Total - Total Alokasi)
    // =================================================
    penerimaanKBAutoFillDetail(data);
});


function formatCOA(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idcoa +"   <i class='fa fa-circle'></i>   "+ repo.nmcoa +" <i class='fa fa-circle'></i> LEVEL "+ repo.level + " </div>";
    return markup;
}
function formatCOASelection(repo) {
    return repo.nmcoa || repo.text;
}





function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}




$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});





/* TABLE PO DETAIL */
function tablePenerimaanKBDetail(){
        /* Tabel PO Detail */
    var initTable = function () {
        var table = $('#tabpenerimaankbdtl');

        if (!table.length) {
            return;
        }

        if ($.fn.DataTable.isDataTable(table)) {
            table.DataTable().destroy();
        }

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
                "url": HOST_URL + 'ka/finance/list_tmp_penerimaankb_dtl',
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

                        "render": function(data, type, row){

                            const id = (data && typeof data === 'object') ? data.id : data;
                            const checked = (data && typeof data === 'object') ? (data.checked || '') : '';
                            const uniqueid = (data && typeof data === 'object') ? (data.uniqueid || '') : '';

                            return '<input type="checkbox" class="row-check" value="' +
                                $('<div>').text(id == null ? '' : id).html() + '"' +
                                ' data-uniqueid="' + $('<div>').text(uniqueid).html() + '" ' +
                                checked + ' aria-label="Pilih detail">';
                        }
                    },
                ],
                "drawCallback": function () {
                    // Setelah reload, status checkbox dibaca ulang dari database
                    // sehingga kondisi check-all mengikuti baris yang tercentang.
                    syncCheckAllPenerimaanKB();
                    updateDetailActionButtons();

                    // Total Alokasi & status tombol final (#total = #totalalokasi)
                    hitungBalance();
                }
        });
    }

    return initTable();

}


function reload_table_penerimaankb_dtl()
{
    var table = $('#tabpenerimaankbdtl');

    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().ajax.reload(null, false); //reload datatable ajax
    } else {
        tablePenerimaanKBDetail();
    }
}


/* =========================================================================
 * CHECKED ROW + ACTION BUTTON (CRUD DETAIL PENERIMAAN KAS/BANK)
 * Mengikuti pola Pengeluaran Kas/Bank:
 *   - kolom pertama = checkbox (checked) per baris
 *   - tombol Edit aktif bila tepat 1 baris dicentang
 *   - tombol Hapus aktif bila ada baris yang dicentang
 * ========================================================================= */
function getCheckedPenerimaanKBDetailIds() {
    return $('#tabpenerimaankbdtl tbody .row-check:checked')
        .map(function () { return String($(this).val() || '').trim(); })
        .get()
        .filter(Boolean);
}

function updateDetailActionButtons() {
    const ids = getCheckedPenerimaanKBDetailIds();

    $('#btnEditDetail').prop('disabled', ids.length !== 1);
    $('#btnDeleteDetail').prop('disabled', ids.length === 0);
}

function syncCheckAllPenerimaanKB() {
    const $checks = $('#tabpenerimaankbdtl tbody .row-check');
    const checked = $checks.filter(':checked').length;

    $('#checkAll')
        .prop('checked', $checks.length > 0 && checked === $checks.length)
        .prop('indeterminate', checked > 0 && checked < $checks.length);
}

/* Update status detail (F = dipilih / kosong = belum) lalu hitung ulang balance header */
function updatePenerimaanKBDetailStatus(id, isChecked) {

    if (id === undefined || id === null || String(id).trim() === '') {
        return;
    }

    $.ajax({
        url: HOST_URL + 'ka/finance/update_status_penerimaankb_dtl',
        type: 'POST',
        dataType: 'json',
        data: {
            id: id,
            status: isChecked ? 'F' : ''
        },
        success: function () {
            documentReadable();
            reload_table_penerimaankb_dtl();
        },
        error: function (xhr) {
            console.error('update_status_penerimaankb_dtl:', xhr.responseText);
        }
    });
}

function bulkUpdatePenerimaanKBDetailStatus(ids, isChecked) {

    if (!ids || ids.length === 0) {
        return;
    }

    const requests = ids.map(function (id) {
        return $.ajax({
            url: HOST_URL + 'ka/finance/update_status_penerimaankb_dtl',
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                status: isChecked ? 'F' : ''
            }
        });
    });

    Promise.all(requests).then(function () {
        documentReadable();
        reload_table_penerimaankb_dtl();
    }).catch(function (xhr) {
        console.error('bulk update_status_penerimaankb_dtl:', xhr);
    });
}

function editSelectedPenerimaanKBDetail() {
    const ids = getCheckedPenerimaanKBDetailIds();

    if (ids.length !== 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih 1 Detail',
            text: 'Pilih tepat satu detail untuk diedit.'
        });
        return;
    }

    btnUpdateDetail(ids[0]);
}

function deleteSelectedPenerimaanKBDetail() {
    const ids = getCheckedPenerimaanKBDetailIds();

    if (ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Belum Ada Pilihan',
            text: 'Pilih detail yang akan dihapus.'
        });
        return;
    }

    btnDeleteDetail(ids);
}


/* =========================================================================
 * TARIK PIUTANG CUSTOMER
 * Konsep sama dengan "Tarik Pengeluaran Kas Bank" (yang menarik HUTANG
 * supplier), tetapi di sini menarik PIUTANG customer dari jurnal transaksi
 * penjualan (sales/postsales/penjualan) yang sudah POSTED.
 * ========================================================================= */
function btnTarikPenerimaanKB() {

    // pastikan nomor dokumen mengikuti prefix/infix/suffix terbaru
    const docno      = syncPenerimaanKBDocno() || $.trim($('#docno').val() || '');
    const kdcustomer = $.trim($('#kdcustomer').val() || '');
    const currcode   = $.trim($('#currcode').val() || '');

    if (docno === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Nomor bukti Penerimaan Kas/Bank belum tersedia.'
        });
        return;
    }

    if (kdcustomer === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Customer belum dipilih.'
        });
        return;
    }

    Swal.fire({
        title: 'Tarik Piutang?',
        text: 'Data piutang customer dari transaksi penjualan (POSTED).',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tarik',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: HOST_URL + 'ka/finance/tarikPiutangCustomer',
            type: 'POST',
            dataType: 'json',
            data: {
                docno: docno,
                kdcustomer: kdcustomer,
                currcode: currcode,
                inputby: $('#inputby').val() || ''
            },

            beforeSend: function () {

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengambil sisa piutang customer.',
                    allowOutsideClick: false,
                    didOpen: function () {
                        Swal.showLoading();
                    }
                });

            },

            success: function (response) {

                Swal.close();

                // Tidak ada data
                if (
                    response.status !== true ||
                    !response.data ||
                    response.data.length === 0
                ) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Data Tidak Ditemukan',
                        text: response.message || 'Tidak ada sisa piutang yang dapat ditarik.',
                        confirmButtonText: 'OK'
                    });

                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'Piutang customer berhasil ditarik.',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Reload detail + hitung ulang balance header
                documentReadable();
                reload_table_penerimaankb_dtl();
            },

            error: function (xhr) {

                Swal.close();

                console.error(xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menarik piutang customer.'
                });
            }
        });

    });
}


$(document).on('click','.btn-edit',function(){

    let id = $(this).data('id');

    btnUpdateDetail(id);

});


// CHECK ALL (centang semua baris sekaligus)
$(document).on('change', '#tabpenerimaankbdtl #checkAll', function () {

    const checked = this.checked;
    const $checks = $('#tabpenerimaankbdtl tbody .row-check');

    $checks.prop('checked', checked);

    syncCheckAllPenerimaanKB();
    updateDetailActionButtons();

    const ids = $checks.map(function () { return String($(this).val() || '').trim(); })
        .get()
        .filter(Boolean);

    bulkUpdatePenerimaanKBDetailStatus(ids, checked);

});

// CHECK / UNCHECK PER ROW (sekaligus update status + balance)
$(document).on('change', '#tabpenerimaankbdtl tbody .row-check', function () {

    syncCheckAllPenerimaanKB();
    updateDetailActionButtons();

    updatePenerimaanKBDetailStatus($(this).val(), $(this).is(':checked'));

});

// KLIK BARIS = TOGGLE CHECKBOX (pola sama dengan Pengeluaran Kas/Bank)
$(document)
    .off('click.penerimaanKBDetailRow', '#tabpenerimaankbdtl tbody tr')
    .on('click.penerimaanKBDetailRow', '#tabpenerimaankbdtl tbody tr', function (e) {

        if ($(e.target).closest('input, button, a, select, label, .select2-container').length) {
            return;
        }

        const $checkbox = $(this).find('.row-check').first();

        if (!$checkbox.length) {
            return;
        }

        $checkbox
            .prop('checked', !$checkbox.prop('checked'))
            .trigger('change');
    });

// SET KONDISI CHECKALL SAAT DATATABLE RELOAD
$('#tabpenerimaankbdtl').on('draw.dt', function () {

    syncCheckAllPenerimaanKB();
    updateDetailActionButtons();

});


function setSelect2Ajax(selector, value, text) {
    if (!value) return;

    let option = new Option(text || value, value, true, true);
    $(selector).append(option).trigger('change');
}

let currentEditId = null;
function btnUpdateDetail(id){

    if (id === undefined || id === null || String(id).trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih satu data yang akan diupdate'
        });
        return;
    }

    // if(ids.length === 0){
    //     Swal.fire({
    //         icon: 'warning',
    //         title: 'Peringatan',
    //         text: 'Pilih satu data yang akan diupdate'
    //     });
    //     return;
    // }

    // if(ids.length > 1){
    //     Swal.fire({
    //         icon: 'warning',
    //         title: 'Peringatan',
    //         text: 'Update hanya boleh satu data'
    //     });
    //     return;
    // }

    currentEditId = id;
    
    $.ajax({
        url: HOST_URL + 'ka/finance/get_penerimaankb_detail',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                // Normalisasi DK ke D / K
                const dkValue = String(res.data.dk ?? '').trim().toUpperCase();
                const dkNorm = ['D', 'DEBIT', 'DR'].includes(dkValue) ? 'D' : 'K';

                const coaName = String(res.data.nmcoa ?? '').trim();

                $('#idurut').val(res.data.idurut);
                $('#uniqueid').val(res.data.uniqueid);
                $('#remarks').val(res.data.remarks);
                $('#dk').val(dkNorm);
                $('#nobukti').val(res.data.nobukti);
                $('#cabangdtl').val(res.data.cabang);
                setJtsValue('[name="nilai"]', convertToDbNumber(res.data.nilai));

                // Referensi header (Customer / No. Jurnal / ID Header)
                fillPenerimaanKBDetailHeaderReference();

                $('#nilai_ref').val(res.data.nilai_ref ?? res.data.nilai ?? '');
                $('#jurnalref').val('YES');

                setSelect2Ajax(
                    '#idcoa',
                    res.data.idcoa,
                    coaName ? res.data.idcoa + ' - ' + coaName : res.data.idcoa
                );

                // Pastikan nama perkiraan tetap terisi setelah trigger change
                $('#nmcoa').val(coaName);

                currentEditId = res.data.idurut;

                $('#modalDetailPenerimaanKBLabel').text('Edit Detail Penerimaan Kas/Bank');
                $('#btnSavePenerimaanKBDetail').html('<i class="fa fa-save"></i> Update');
                $('#modalDetailPenerimaanKB').modal('show');

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

// Reset currentEditId + label tombol simpan ketika modal detail ditutup
$('#modalDetailPenerimaanKB').on('hidden.bs.modal', function () {
    currentEditId = null;

    $('#btnSavePenerimaanKBDetail').html('<i class="fa fa-save"></i> Simpan');
});

// $(document).on('input', '.form-control', function () {
//     // Jika sedang dalam mode edit, gunakan currentEditId
//     if (currentEditId) {
//         // Baca nilai qty, harga, dan multidisc
//         let qty = parseFloat($('#qty').val().replace(/,/g, '')) || 0;
//         let harga = parseFloat($('#harga').val().replace(/,/g, '')) || 0;
//         // let multidisc = parseFloat($('#multidisc').val().replace(/,/g, '')) || 0;
        
//         // Hitung nilai awal (qty * harga)
//         let nilaiAwal = qty * harga;
        
//         // Hitung diskon
//         // let diskon = (nilaiAwal * multidisc) / 100;
        
//         // Hitung nilai akhir setelah diskon
//         let nilaiAkhir = nilaiAwal;
        
//         // Format ke en-US: separator ribuan = koma, desimal = titik
//         $('#nilai').val(nilaiAkhir.toLocaleString('en-US', {
//             minimumFractionDigits: 2, 
//             maximumFractionDigits: 2
//         }));
//     }
// });

function btnDeleteDetail(ids){
    // Normalisasi: bisa dipanggil dengan 1 id (tombol per baris)
    // atau array id (tombol toolbar Hapus).
    if (!Array.isArray(ids)) {
        ids = (ids === undefined || ids === null || String(ids).trim() === '')
            ? []
            : [ids];
    }

    ids = ids.map(function (id) { return String(id).trim(); }).filter(Boolean);

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
            url: HOST_URL + 'ka/finance/delete_penerimaankb_detail',
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

                    documentReadable();
                    reload_table_penerimaankb_dtl();

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

$(document).on('click', '.btn-delete', function(){

    let id = $(this).data('id');

    btnDeleteDetail(id);

});

// Check-all detail lama (#checkAllDetail) sudah tidak dipakai.
// Check-all pada tabel detail Penerimaan Kas/Bank memakai #checkAll
// yang ditangani oleh handler di atas.

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



function formatCurrency(repo) {
    if (repo.loading) return repo.text;
    return repo.currcode || repo.text;
}

function formatCurrencySelection(repo) {
    return repo.currcode || repo.text;
}



function savePenerimaanKBDetail() {

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan data PenerimaanKB Detail?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        // Sinkronkan nilai diterima (#total -> hidden #dpp)
        $('#dpp').val(penerimaanKBNumber($('#total').val()));

        let formData = new FormData(document.getElementById('formPenerimaanKBDetail'));
        formData.append('docdate', $('#docdate').val());
        formData.append('cabang', $('#cabang').val());
        // Data header customer ikut dikirim supaya header sc_tmp.penerimaankb
        // tetap lengkap bila detail diinput lebih dulu (tombol +).
        formData.append('kdcustomer', $('#kdcustomer').val());
        formData.append('nmcustomer', $.trim($('#kdcustomer option:selected').text() || ''));
        formData.append('alamatcustomer', $('#alamatcustomer').val());
        formData.append('currcode', $('#currcode').val());
        formData.append('kurs', convertToDbNumber($('#kurs').val()));
        formData.append('prkkas', $('#prkkas').val() || '');
        formData.append('keterangan', $('#keterangan').val());
        // Nilai diterima header (konsep #total / dpp)
        formData.append('total', convertToDbNumber($('#total').val()));
        formData.append('dpp', convertToDbNumber($('#total').val()));
        // formData.append('estpakai', $('#estpakai').val());

        // docno gabungan (konsep sama dengan Pengeluaran Kas/Bank)
        formData.set('docno', penerimaanKBDocno());
        // convert qty ke numeric DB
        
        let nilai = $('#nilai').val();
        // formData.set('multidisc', convertToDbNumber(multidisc));
        formData.set('nilai', convertToDbNumber(nilai));
        // formData.set('nilai', convertToDbNumber(nilai));
        // formData.set('remarks', $('#remarks').val());
        // formData.set('uniqueid', $('#uniqueid').val());
        // formData.set('docnopp', $('#docnopp').val());
        
        // formData.set('descriptionpo', convertToDbNumber(qty));

        $.ajax({
            url: HOST_URL + 'ka/finance/savePenerimaanKBDetail',
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
                $('#modalDetailPenerimaanKB').modal('hide');
                // $('#modalUpdatePenerimaanKB').modal('hide');
                // $('#formPOUpdate')[0].reset();
                reload_table_penerimaankb_dtl();
                documentReadable()
                $('#formPenerimaanKBDetail')[0].reset();
                // let balanceValue = ($('#balance').val()) || 0;
                
                // if (balanceValue != 0) {
                //     // Disable tombol submit atau form
                //     $('button[type="submit"]').prop('disabled', true);
                //     // Atau disable seluruh input di form
                //     // $('#formPenerimaanKBDetail :input').prop('disabled', true);
                    
                //     // Tampilkan notifikasi bahwa form sudah tidak bisa digunakan
                //     Swal.fire({
                //         icon: 'info',
                //         title: 'Form Terkunci',
                //         text: `Form telah dikunci karena balance bernilai ${balanceValue}. Tidak dapat menyimpan data baru.`,
                //         timer: 3000,
                //         showConfirmButton: true
                //     });
                // } else {
                //     $('button[type="submit"]').prop('disabled', false);
                // }
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

/* =========================================================================
 * LOCK HEADER
 * Dipakai setelah header tersimpan (initPenerimaanKBHeader sukses) atau
 * saat halaman dibuka dengan header yang sudah ada.
 * Terkunci  : cabang, no. bukti (prefix/infix/suffix), tanggal, referensi,
 *             customer + alamat, mata uang, kurs, perk. kas/bank
 * Tetap bisa diubah : #total dan #keterangan
 * ========================================================================= */
function lockPenerimaanKBHeader() {

    // Input text / date -> readonly
    $('#prefix, #infix, #suffix, #docdate, #pp, #alamatcustomer')
        .prop('readonly', true)
        .addClass('bg-light');

    // Select (select2) -> disabled.
    // Select2 4.x membaca atribut disabled lewat MutationObserver,
    // jadi atribut juga di-set agar tampilannya ikut terkunci.
    $('#cabang, #kdcustomer, #currcode, #prkkas').each(function () {
        const $el = $(this);

        $el.prop('disabled', true).attr('disabled', 'disabled');

        const s2 = $el.data('select2');

        if (s2 && typeof s2._syncAttributes === 'function') {
            s2._syncAttributes();
        }
    });

    // #total dan #keterangan tetap dapat diubah
    $('#total, #keterangan').prop('readonly', false).prop('disabled', false);

    return true;
}

/* =========================================================================
 * Referensi header pada modal detail (Customer / No. Jurnal / ID Header)
 * Konsep sama dengan Pengeluaran Kas/Bank.
 * ========================================================================= */
function fillPenerimaanKBDetailHeaderReference() {
    const customerCode = $.trim($('#kdcustomer').val() || '');
    let customerName = $.trim($('#kdcustomer option:selected').text() || '');

    if (customerName === customerCode) {
        customerName = '';
    }

    const docno = penerimaanKBDocno();

    const idurut = $.trim($('#idurut_header').val() || '');

    $('#detail_customer').val(
        customerCode + (customerName ? ' - ' + customerName : '')
    );
    $('#detail_docno_display').val(docno);
    $('#detail_idurut_display').val(idurut);

    // docno detail harus sama dengan docno header
    $('#docno_detail').val(docno);
}

/* =========================================================================
 * BUKA MODAL DETAIL (form detail direset, referensi header diisi ulang)
 * ========================================================================= */
function openPenerimaanKBDetailModal() {
    $('#formPenerimaanKBDetail')[0].reset();

    // Clear select2
    $('#idcoa').val(null).trigger('change');

    $('#idurut').val('');
    $('#uniqueid').val('');
    $('#nilai_ref').val('');
    $('#jurnalref').val('YES');
    $('#nmcoa').val('');

    // Auto: #dk & #nilai mengikuti selisih #total vs #totalalokasi
    penerimaanKBAutoFillDetail(null);

    fillPenerimaanKBDetailHeaderReference();
    updateCurrencySymbol();

    $('#btnSavePenerimaanKBDetail').html('<i class="fa fa-save"></i> Simpan');
    $('#modalDetailPenerimaanKBLabel').text('Tambah Detail Penerimaan Kas/Bank');
    $('#modalDetailPenerimaanKB').modal('show');
}

/* =========================================================================
 * TAMBAH DETAIL
 * Konsep sama dengan addDetailPengeluaranKB() pada Pengeluaran Kas/Bank:
 *   1. validasi header
 *   2. simpan/inisialisasi header (initPenerimaanKBHeader)
 *   3. baca ulang header lalu buka modal detail
 * Penarikan piutang TIDAK dilakukan di sini.
 * ========================================================================= */
function btnInputDetail() {

    const docno      = penerimaanKBDocno();
    const docdate    = $.trim($('#docdate').val() || '');
    const cabang     = $.trim($('#cabang').val() || '');
    const kdcustomer = $.trim($('#kdcustomer').val() || '');
    const prkkas     = $.trim($('#prkkas').val() || '');

    if (docno === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Nomor dokumen belum tersedia.'
        });
        return;
    }

    if (docdate === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Tanggal dokumen wajib diisi.'
        });
        return;
    }

    if (cabang === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Cabang / Job wajib dipilih.'
        });

        $('#cabang').select2('open');
        return;
    }

    if (kdcustomer === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Customer wajib dipilih.'
        });

        $('#kdcustomer').select2('open');
        return;
    }

    if (prkkas === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Perkiraan Kas/Bank wajib dipilih terlebih dahulu.'
        });

        $('#prkkas').select2('open');
        return;
    }

    // =====================================================
    // SIMPAN / INISIALISASI HEADER
    // =====================================================
    const header = {
        docno: docno,
        docdate: docdate,
        cabang: cabang,
        kdcustomer: kdcustomer,
        nmcustomer: $.trim($('#kdcustomer option:selected').text() || ''),
        alamatcustomer: $.trim($('#alamatcustomer').val() || ''),
        prkkas: prkkas,
        currcode: $.trim($('#currcode').val() || ''),
        kurs: convertToDbNumber($('#kurs').val()),
        total: convertToDbNumber($('#total').val()),
        dpp: convertToDbNumber($('#total').val()),
        np: $.trim($('#pp').val() || ''),
        keterangan: $.trim($('#keterangan').val() || '')
    };

    $.ajax({
        url: HOST_URL + 'ka/finance/initPenerimaanKBHeader',
        type: 'POST',
        data: header,
        dataType: 'json',

        beforeSend: function () {
            Swal.fire({
                title: 'Memproses...',
                text: 'Menyiapkan header Penerimaan Kas/Bank.',
                allowOutsideClick: false,
                didOpen: function () {
                    Swal.showLoading();
                }
            });
        },

        success: function (res) {
            Swal.close();

            if (!res || res.success !== true) {
                Swal.fire(
                    'Gagal',
                    (res && res.message)
                        ? res.message
                        : 'Header gagal disimpan.',
                    'error'
                );
                return;
            }

            // Identitas header hasil simpan
            if (res.data) {
                $('#idurut_header').val(res.data.idurut || '');
                $('#docno').val(res.data.docno || docno);
            }

            // Header sudah tersimpan -> kunci header
            // (hanya #total dan #keterangan yang tetap dapat diubah)
            lockPenerimaanKBHeader();

            if (res.reload === true) {
                setTimeout(function () {
                    window.location.reload();
                }, 500);
                return;
            }

            // Baca ulang header lalu buka modal detail
            documentReadable();
            reload_table_penerimaankb_dtl();
            openPenerimaanKBDetailModal();
        },

        error: function (xhr) {
            Swal.close();

            console.error('initPenerimaanKBHeader:', xhr.responseText);

            Swal.fire('Error', 'Gagal menyimpan header.', 'error');
        }
    });
}



function formatCustomer(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdcustomer +"   <i class='fa fa-circle'></i>   "+ repo.nmcustomer + " </div>";
    return markup;
}
function formatCustomerSelection(repo) {
    return repo.nmcustomer || repo.text;
}

// ======================= PENJUALAN ==================================

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

        // Pemilihan customer HANYA mengisi data customer pada header.
        // Penarikan piutang TIDAK lagi otomatis di sini, melainkan
        // lewat tombol "Tarik Piutang Customer" (btnTarikPenerimaanKB).
        $("#alamatcustomer").val(selectedData.alamat_kantor || '').prop('disabled', true);
    }
});

// function checkEnableCustomer() {
//     let docno  = $('#docno').val().trim();
//     let cabang = $('#cabang').val().trim();

//     if (docno !== '' && cabang !== '') {
//         $('#kdcustomer').prop('disabled', false);
//     } else {
//         $('#kdcustomer').prop('disabled', true);
//     }
// }

// // $('#docno, #cabang').on('change keyup', function () {
// //     checkEnableCustomer();
// // });

// $(document).ready(function () {
//     checkEnableCustomer();
// });

let currentKodeSuffix = '';
let skipRoleChange = false;

$('#cabang').on('change', function () {
    if (skipRoleChange) return; // skip

    let idbranch = $(this).val();

    if(idbranch){
        $.ajax({
                url: HOST_URL + '/ka/finance/getBranchInfoPenerimaanKB',
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
                    $('#prefix').val('BKM');             // default
                    defaultInitialPP = currentKodeSuffix
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
                                locale: { format: 'DD-MM-YYYY' },
                                cancelLabel: 'Clear'
                            });
                            // rebind handlers jika perlu (apply/cancel)
                            $el.on('apply.daterangepicker', function(ev, picker) {
                                $(this).val(picker.startDate.format('DD-MM-YYYY'));
                            });
                            $el.on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                            });
                        }

                        // isi input langsung (opsional)
                        $el.val(today.format('DD-MM-YYYY'));
                    }

                    // Nomor dokumen hanya diisi otomatis bila belum ada.
                    // Nomor yang sudah ada / diisi user tidak ditimpa.
                    if ($.trim($('#docno').val() || '') === '') {
                        $('#suffix').val(currentKodeSuffix + '0001');
                        $('#docno').val(
                            'BKM/' + res.infix + '/' + currentKodeSuffix + '0001'
                        );
                    }
                    $('#kdcustomer').prop('disabled', false);

                    // reset customer lama
                    $('#kdcustomer').val(null).trigger('change');
                    $('#alamatcustomer').val('');
                }
            });
    }
    
});


/* =========================================================================
 * SUFFIX / NO. BUKTI
 * Konsep sama dengan Pengeluaran Kas/Bank:
 *   - suffix dapat diubah user (maks. 6 karakter)
 *   - nomor dokumen dibentuk dari prefix/infix/suffix
 *   - bila nomor belum ada di transaksi, nomor tsb dapat dipakai sebagai docno
 * ========================================================================= */
function penerimaanKBDocno() {
    const fromParts = [
        $.trim($('#prefix').val() || ''),
        $.trim($('#infix').val() || ''),
        $.trim($('#suffix').val() || '')
    ].filter(Boolean).join('/');

    return fromParts !== ''
        ? fromParts
        : $.trim($('#docno').val() || '');
}

function syncPenerimaanKBDocno() {
    const docno = penerimaanKBDocno();

    if (docno !== '') {
        $('#docno').val(docno);
    }

    return docno;
}

function loadNextSuffixPenerimaanKB() {

    let prefix = $.trim($('#prefix').val()).toUpperCase();
    let infix  = $.trim($('#infix').val());

    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + '/ka/finance/getNextSuffixPenerimaanKB',
        method: 'GET',
        data: {
            prefix: prefix,
            infix: infix,
            kode_suffix: currentKodeSuffix
        },
        dataType: 'json',
        cache: false,
        success: function (res) {
            if (!res.success) {
                Swal.fire('Error', res.message, 'warning');
                return;
            }

            $('#suffix').val($.trim(res.suffix || ''));
            $('#docno').val(
                prefix + '/' + infix + '/' + res.suffix
            );
        }
    });
}

$('#prefix').on('blur', function () {
    loadNextSuffixPenerimaanKB();
});

// Suffix dapat diubah (maks. 6 karakter): nomor dokumen ikut menyesuaikan
$(document).on('change blur', '#suffix', function () {
    const suffix = $.trim($(this).val() || '').toUpperCase().slice(0, 6);

    $(this).val(suffix);

    if (suffix === '') return;

    syncPenerimaanKBDocno();
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





    tablePenerimaanKBTrx();
    // tablePOApprvTrx();
    tablePenerimaanKBDetail();
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