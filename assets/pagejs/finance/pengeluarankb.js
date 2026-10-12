var save_method; //for save method string
var table;
var initTable;
//"use strict";

function tablePengeluaranKBTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablepengeluarankbTrx');
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
                "url": HOST_URL + 'ka/finance/list_pengeluarankb',
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

function reload_tablePengeluaranKBTrx()
{
    var table = $('#tablepengeluarankbTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}




function documentReadable() {
    var docno = $.trim($('#docno').val());

    if (!docno) {
        console.warn('Nomor dokumen kosong.');
        return;
    }

    function clean(value) {
        return $.trim(value == null ? '' : String(value));
    }

    function findItem(items, field, value) {
        value = clean(value).toUpperCase();

        if (!Array.isArray(items)) {
            return null;
        }

        for (var i = 0; i < items.length; i++) {
            if (clean(items[i][field]).toUpperCase() === value) {
                return items[i];
            }
        }

        return null;
    }

    function setSelect2(selector, value, text, extraData) {
        var $select = $(selector);
        value = clean(value);
        text = clean(text);

        if (!$select.length || !value) {
            return;
        }

        var option = new Option(text || value, value, true, true);

        if (extraData) {
            $(option).data('select-data', extraData);
        }

        $select
            .empty()
            .append(option)
            .val(value)
            .trigger('change.select2');
    }

    function formatDocDate(value) {
        value = clean(value);

        if (!value) {
            return '';
        }

        // Format DD-MM-YYYY
        if (/^\d{2}-\d{2}-\d{4}$/.test(value)) {
            var dmy = moment(value, 'DD-MM-YYYY', true);

            if (dmy.isValid()) {
                return dmy.format('DD-MM-YYYY');
            }
        }

        // Format YYYY-MM-DD
        if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
            var ymd = moment(value, 'YYYY-MM-DD', true);

            if (ymd.isValid()) {
                return ymd.format('DD-MM-YYYY');
            }
        }

        // Format tanggal lain yang mungkin dikembalikan backend
        var date = moment(
            value,
            [
                'DD-MM-YYYY',
                'YYYY-MM-DD',
                'DD/MM/YYYY',
                'YYYY-MM-DD HH:mm:ss',
                moment.ISO_8601
            ],
            true
        );

        return date.isValid() ? date.format('DD-MM-YYYY') : '';
    }

    function setDocDate(value) {
        var formattedDate = formatDocDate(value);
        var $date = $('#docdate');

        if (!$date.length) {
            return;
        }

        $date.val(formattedDate);

        // Sinkronkan dengan DateRangePicker jika field menggunakannya.
        var picker = $date.data('daterangepicker');

        if (picker && formattedDate) {
            var parsedDate = moment(formattedDate, 'DD-MM-YYYY', true);

            if (parsedDate.isValid()) {
                picker.setStartDate(parsedDate);
                picker.setEndDate(parsedDate);
            }
        }

        $date.trigger('change');
    }

    function setSupplier(kdsupplier, alamat, namaSupplier, supplierData) {
        kdsupplier = clean(kdsupplier);
        alamat = clean(alamat);
        namaSupplier = clean(namaSupplier);

        if (kdsupplier) {
            const $supplier = $('#kdsupplier');

            const currentValue = String($supplier.val() || '');

            if (currentValue !== kdsupplier) {
                setSelect2(
                    '#kdsupplier',
                    kdsupplier,
                    namaSupplier || kdsupplier,
                    supplierData || null
                );
            }

            $supplier.prop('disabled', true);
        }

        $('#alamatsupplier')
            .val(alamat)
            .prop('readonly', true)
            .prop('disabled', false);
    }

    function loadBranch(cabang) {
        cabang = clean(cabang);

        if (!cabang) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: HOST_URL + 'api/globalmodule/list_branchjob',
            data: { var: cabang },
            dataType: 'json'
        }).done(function (result) {
            var items = result && Array.isArray(result.items)
                ? result.items
                : [];

            var branch = findItem(items, 'idbranch', cabang);

            if (!branch && items.length) {
                branch = items[0];
            }

            if (!branch) {
                // Tetap tampilkan kode cabang jika API tidak menemukan nama.
                setSelect2('#cabang', cabang, cabang);
                $('#cabang').prop('disabled', true);
                console.warn('Cabang tidak ditemukan:', cabang);
                return;
            }

            setSelect2(
                '#cabang',
                branch.idbranch || cabang,
                branch.nmbranch || branch.idbranch || cabang,
                branch
            );

            $('#cabang').prop('disabled', true);
        }).fail(function (xhr) {
            console.error(
                'Gagal memuat cabang:',
                xhr.status,
                xhr.responseText
            );

            setSelect2('#cabang', cabang, cabang);
            $('#cabang').prop('disabled', true);
        });
    }

    function loadSupplier(kdsupplier, alamat, namaSupplier) {
        kdsupplier = clean(kdsupplier);
        alamat = clean(alamat);
        namaSupplier = clean(namaSupplier);

        if (!kdsupplier) {
            setSupplier('', alamat, namaSupplier, null);
            return;
        }

        $.ajax({
            type: 'GET',
            url: HOST_URL + 'api/globalmodule/list_supplier_new',
            data: { var: kdsupplier },
            dataType: 'json'
        }).done(function (result) {
            var items = result && Array.isArray(result.items)
                ? result.items
                : [];

            var supplier = findItem(items, 'kdsupplier', kdsupplier);

            // API mungkin mengembalikan daftar hasil pencarian.
            if (!supplier && items.length === 1) {
                supplier = items[0];
            }

            var supplierName = supplier
                ? clean(supplier.nmsupplier)
                : '';

            // Prioritas nama: API supplier, nama pada header, kode supplier.
            supplierName = supplierName || namaSupplier || kdsupplier;

            setSupplier(
                kdsupplier,
                alamat,
                supplierName,
                supplier || null
            );
        }).fail(function (xhr) {
            console.error(
                'Gagal memuat supplier:',
                xhr.status,
                xhr.responseText
            );

            // Jangan biarkan supplier hilang jika API gagal.
            setSupplier(
                kdsupplier,
                alamat,
                namaSupplier || kdsupplier,
                null
            );
        });
    }

    function loadCurrency(currcode, kurs) {
        currcode = clean(currcode);
        kurs = clean(kurs);

        if (kurs !== '') {
            setJtsValue('#kurs', convertToDbNumber(kurs));
        }

        if (!currcode) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: HOST_URL + 'api/globalmodule/list_currency',
            data: { var: currcode },
            dataType: 'json'
        }).done(function (result) {
            var items = result && Array.isArray(result.items)
                ? result.items
                : [];

            var currency = findItem(items, 'currcode', currcode);

            if (!currency && items.length === 1) {
                currency = items[0];
            }

            var currencyName = currency
                ? clean(currency.currname)
                : '';

            setSelect2(
                '#currcode',
                currcode,
                currencyName || currcode,
                currency || null
            );

            $('#currcode').prop('disabled', true);

            if (kurs !== '') {
                setJtsValue('#kurs', convertToDbNumber(kurs));
            }
        }).fail(function (xhr) {
            console.error(
                'Gagal memuat mata uang:',
                xhr.status,
                xhr.responseText
            );

            setSelect2('#currcode', currcode, currcode);
            $('#currcode').prop('disabled', true);
        });
    }

    function loadPrkkas(prkkas) {
        prkkas = clean(prkkas);

        if (!prkkas) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: HOST_URL + 'api/globalmodule/list_coa',
            dataType: 'json',
            data: {
                _search_: prkkas,
                _page_: 1,
                _draw_: true,
                _start_: 1,
                _perpage_: 30,
                _paramglobal_: '',
                _parameterx_: " and trim(level)='5'",
                term: prkkas
            }
        }).done(function (result) {
            var items = result && Array.isArray(result.items)
                ? result.items
                : [];

            var coa = findItem(items, 'idcoa', prkkas);

            if (!coa && items.length === 1) {
                coa = items[0];
            }

            if (!coa) {
                setSelect2('#prkkas', prkkas, prkkas);
                $('#nmprkkas').val('');
                console.warn('Perkiraan Kas/Bank tidak ditemukan:', prkkas);
                return;
            }

            setSelect2(
                '#prkkas',
                coa.idcoa || prkkas,
                clean(coa.idcoa || prkkas) +
                (clean(coa.nmcoa) ? ' - ' + clean(coa.nmcoa) : ''),
                coa
            );

            $('#nmprkkas').val(clean(coa.nmcoa));
        }).fail(function (xhr) {
            console.error(
                'Gagal memuat Perkiraan Kas/Bank:',
                xhr.status,
                xhr.responseText
            );

            setSelect2('#prkkas', prkkas, prkkas);
        });
    }

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'ka/finance/showing_pengeluarankbtemp',
        data: { docno: docno },
        dataType: 'json',

        beforeSend: function () {
            $('#loadMe').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        },

        success: function (response) {
            console.log('showing_pengeluarankbtemp:', response);

            if (
                !response ||
                response.status !== true ||
                !response.data ||
                !response.data.header
            ) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data tidak ditemukan',
                    text: response && response.message
                        ? response.message
                        : 'Temporary Pengeluaran Kas/Bank tidak ditemukan.'
                });
                return;
            }

            var data = response.data.header;
            var status = clean(data.status).toUpperCase();

            // Header utama
            $('#idurut').val(clean(data.idurut));
            $('#status').val(status);
            $('#docno')
                .val(clean(data.docno) || docno)
                .prop('readonly', true);

            // Pecah nomor dokumen: BKK/2610/PA0001
            var parts = clean(data.docno || docno).split('/');

            $('#prefix').val(parts[0] || '').prop('readonly', true);
            $('#infix').val(parts[1] || '').prop('readonly', true);
            $('#sufix').val(parts.slice(2).join('/') || '').prop('readonly', true);

            if (parts[2]) {
                defaultInitialPP = parts[2].substring(0, 2);
            }

            // Tanggal
            setDocDate(data.docdate);

            // Field biasa
            $('#pemohon').val(clean(data.pemohon));
            $('#pp').val(clean(data.pp));
            $('#keterangan').val(clean(data.keterangan));

            // Total
            if (typeof setReadableNumber === 'function') {
                setReadableNumber('#total', data.total);
            } else {
                $('#total').val(data.total == null ? '' : data.total);
            }

            // Select2 dimuat secara asynchronous.
            loadBranch(data.cabang);
            loadSupplier(
                data.kdsupplier,
                data.alamatsupplier,
                data.nmsupplier
            );
            loadCurrency(data.currcode, data.kurs);
            loadPrkkas(data.prkkas);

            // Pastikan alamat langsung terisi tanpa menunggu API supplier.
            $('#alamatsupplier')
                .val(clean(data.alamatsupplier))
                .prop('readonly', true)
                .prop('disabled', false);

            // Kurs juga langsung diisi dari header.
            if (clean(data.kurs) !== '') {
                setJtsValue('#kurs', convertToDbNumber(data.kurs));
            }

            // DOC DATE: tidak bisa diedit
            $('#docdate')
                .prop('readonly', true)
                .prop('disabled', false)
                .css('pointer-events', 'none');

// Jika menggunakan DateRangePicker
            var datePicker = $('#docdate').data('daterangepicker');
            if (datePicker) {
                datePicker.element.prop('readonly', true);
                datePicker.container.hide();
            }

// NILAI TUKAR / KURS: tidak bisa diedit
            $('#kurs')
                .prop('readonly', true)
                .prop('disabled', false)
                .css('pointer-events', 'none');
            // Status E = masih dapat diedit.
            var canEdit = status === 'E';

            $('#btnAddDetail').prop('disabled', !canEdit);
            $('#btnSimpan').prop('disabled', !canEdit);

            // Field yang tidak boleh berubah ketika membaca dokumen.
            $('#cabang, #kdsupplier, #currcode').prop('disabled', true);
            $('#alamatsupplier').prop('readonly', true);

            console.log('Header Pengeluaran Kas/Bank:', data);
            console.log(
                'Detail Pengeluaran Kas/Bank:',
                response.data.detail || []
            );
            updatePengeluaranKBTotalAlokasi(
                response.data.detail || []
            );
            // Jika fungsi reload detail tersedia di file JS, panggil sesuai
            // implementasi modul setelah header berhasil dimuat.
        },

        error: function (xhr, textStatus, errorThrown) {
            console.error(
                'Gagal membaca temporary Pengeluaran Kas/Bank:',
                textStatus,
                errorThrown,
                xhr.responseText
            );

            Swal.fire({
                icon: 'error',
                title: 'Gagal membaca dokumen',
                text: 'Terjadi kesalahan saat mengambil data Pengeluaran Kas/Bank.'
            });
        },

        complete: function () {
            $('#loadMe').modal('hide');
        }
    });
}




$(document).on('input', '#dpp', function () {
    hitungBalance();
});

function hitungBalance() {

    let balanceAwal = parseFloat(
        $('#balance_awal').val().replace(/,/g, '')
    ) || 0;

    let dpp = parseFloat(
        $('#dpp').val().replace(/,/g, '')
    ) || 0;

    let balance = balanceAwal + dpp;

    // if (balance < 0) {
    //     balance = 0;
    // }

    $('#balance').val(balance.toLocaleString());

    $('button[type="submit"]').prop('disabled', balance != 0);

}
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
                _paramglobal_: "",
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
    var data = e.params.data || {};

    // Simpan nama COA ke hidden input
    $('#nmprkkas').val(data.nmcoa || '');

    console.log('ID COA:', data.idcoa || data.id);
    console.log('NAMA COA:', $('#nmprkkas').val());
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





/* ============================================================
 * DETAIL PENGELUARAN KAS/BANK
 * Tabel menggunakan layout mirip Tanda Terima:
 * checkbox, pilih baris, edit, hapus, hitung total alokasi.
 * ============================================================ */
function tablePengeluaranKBDetail() {
    const $table = $('#tabpengeluarankbdtl');
    if (!$table.length) return;

    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().destroy();
    }

    $table.DataTable({
        processing: true,
        serverSide: true,
        language: languageDatatable(),
        paging: false,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: false,
        ajax: {
            url: HOST_URL + 'ka/finance/list_tmp_pengeluarankb_dtl',
            type: 'POST',
            dataFilter: function (raw) {
                const json = JSON.parse(raw);
                const dt = json.dataTables || json;
                json.draw = dt.draw || 0;
                json.recordsTotal = dt.recordsTotal || 0;
                json.recordsFiltered = dt.recordsFiltered || 0;
                json.data = dt.data || [];
                return JSON.stringify(json);
            },
            error: function (xhr) {
                console.error('list_tmp_pengeluarankb_dtl:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Detail',
                    text: 'Data detail Pengeluaran Kas/Bank tidak dapat dimuat.'
                });
            }
        },
        columnDefs: [{
            targets: 0,
            orderable: false,
            searchable: false,
            className: 'text-center',
            render: function (data, type, row) {
                // ID detail dikirim sebagai kolom pertama oleh endpoint lama.
                const id = $('<div>').text(data ?? '').html();
                return '<input type="checkbox" class="row-check" value="' + id + '" aria-label="Pilih detail">';
            }
        }],
        drawCallback: function () {
            $('#checkAll')
                .prop('checked', false)
                .prop('indeterminate', false);

            updateDetailActionButtons();

            // Jangan panggil updatePengeluaranKBTotalAlokasi()
            // menggunakan array DataTable di sini.
        }

    });

    $table.find('tbody')
        .off('click.pengeluaranKBDetail', 'tr')
        .on('click.pengeluaranKBDetail', 'tr', function (e) {
            if ($(e.target).closest('input, button, a, select, label, .select2-container').length) return;
            const $check = $(this).find('.row-check').first();
            if ($check.length) $check.prop('checked', !$check.prop('checked')).trigger('change');
        });

    $table
        .off('change.pengeluaranKBDetail', '.row-check')
        .on('change.pengeluaranKBDetail', '.row-check', function () {
            const $checks = $table.find('tbody .row-check');
            const checked = $checks.filter(':checked').length;
            $('#checkAll')
                .prop('checked', $checks.length > 0 && checked === $checks.length)
                .prop('indeterminate', checked > 0 && checked < $checks.length);
            updateDetailActionButtons();
        });

    $('#checkAll')
        .off('change.pengeluaranKBDetail')
        .on('change.pengeluaranKBDetail', function () {
            $table.find('tbody .row-check').prop('checked', this.checked);
            updateDetailActionButtons();
        });
}

function reload_table_pengeluarankb_dtl() {
    if ($.fn.DataTable.isDataTable('#tabpengeluarankbdtl')) {
        $('#tabpengeluarankbdtl').DataTable().ajax.reload(null, false);
    } else {
        tablePengeluaranKBDetail();
    }
}

function getCheckedDetailIds() {
    return $('#tabpengeluarankbdtl tbody .row-check:checked')
        .map(function () { return String($(this).val() || '').trim(); })
        .get()
        .filter(Boolean);
}

function updateDetailActionButtons() {
    const ids = getCheckedDetailIds();
    $('#btnEditDetail, #btnUpdateDetail, #btnUpdatePengeluaranKBDetail')
        .prop('disabled', ids.length !== 1);
    $('#btnDeleteDetail, #btnDeletePengeluaranKBDetail')
        .prop('disabled', ids.length === 0);
}

function setSelect2Ajax(selector, value, text) {
    const $select = $(selector);
    if (!$select.length) return;
    if (value === undefined || value === null || String(value).trim() === '') {
        $select.val(null).trigger('change');
        return;
    }
    const strValue = String(value);
    const exists = $select.find('option').filter(function () {
        return this.value === strValue;
    }).length > 0;
    if (!exists) $select.append(new Option(text || strValue, strValue, true, true));
    $select.val(strValue).trigger('change');
}


function pengeluaranKBNumber(value) {
    let s = String(value ?? '0').trim().replace(/,/g, '').replace(/[^0-9.\-]/g, '');
    const n = Number(s);
    return Number.isFinite(n) ? n : 0;
}


function finalPengeluaranKB() {
    const idurut = $.trim($('#idurut').val() || '');
    const docno = $.trim($('#docno').val() || '');

    function toInteger(value) {
        let normalized = String(value ?? '')
            .trim()
            .replace(/[^\d,.-]/g, '');

        // Format 2,000,000 atau 2.000.000 = pemisah ribuan
        if (/^-?\d{1,3}(,\d{3})+$/.test(normalized) ||
            /^-?\d{1,3}(\.\d{3})+$/.test(normalized)) {
            normalized = normalized.replace(/[,.]/g, '');
        } else {
            normalized = normalized.replace(/\./g, '').replace(',', '.');
        }

        const numberValue = Number(normalized);
        return Number.isFinite(numberValue) ? Math.round(numberValue) : 0;
    }

    const rawHeader = $('#total').val();
    const rawAlokasi = $('#totalalokasi').val();

    console.log('Elemen total:', $('#total').length, rawHeader);
    console.log('Elemen alokasi:', $('#totalalokasi').length, rawAlokasi);

    const nilaiHeader = toInteger(rawHeader);
    const nilaiAlokasi = toInteger(rawAlokasi);

    console.log('Finalisasi - Total Header:', $('#total').val(), nilaiHeader);
    console.log('Finalisasi - Total Alokasi:', $('#totalalokasi').val(), nilaiAlokasi);

    // Lanjutkan validasi dan popup finalisasi Anda di sini.

    if (nilaiAlokasi < 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Finalisasi Ditolak',
            text: 'Total Alokasi tidak boleh negatif. Dokumen tidak dapat difinalisasi.'
        });
        return;
    }

    if (docno === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Tersedia',
            text: 'Nomor Pengeluaran Kas Bank belum tersedia.'
        });
        return;
    }

    const isBalance = nilaiHeader === nilaiAlokasi;

    if (!isBalance) {
        Swal.fire({
            icon: 'warning',
            title: 'Finalisasi Ditolak',
            html:
                'Total Pengeluaran dan Total Alokasi tidak balance.' +
                '<br><br>Total Pengeluaran: <b>' +
                nilaiHeader.toLocaleString('id-ID') +
                '</b><br>Total Alokasi: <b>' +
                nilaiAlokasi.toLocaleString('id-ID') +
                '</b><br><br>Dokumen tidak dapat difinalisasi.',
            confirmButtonText: 'Tutup'
        });
        return;
    }

    Swal.fire({
        icon: 'question',
        title: 'Finalisasi Pengeluaran Kas Bank',
        html:
            'Dokumen akan difinalisasi dan tidak lagi berstatus draft.' +
            '<br><br>Total Pengeluaran: <b>' +
            nilaiHeader.toLocaleString('id-ID') +
            '</b><br>Total Alokasi: <b>' +
            nilaiAlokasi.toLocaleString('id-ID') +
            '</b>',
        showCancelButton: true,
        confirmButtonText: 'Ya, Finalisasi',
        cancelButtonText: 'Batal'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $('#btnSimpan').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: HOST_URL + 'ka/finance/finalPengeluaranKB',
            dataType: 'json',
            data: {
                idurut: idurut,
                docno: docno
            },

            success: function (res) {

                console.log('finalPengeluaranKB response:', res);

                if (!res || res.success !== true) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Finalisasi',
                        text: res && res.message
                            ? res.message
                            : 'Gagal melakukan finalisasi.'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message ||
                        (isBalance
                            ? 'Pengeluaran Kas Bank berhasil difinalisasi.'
                            : 'Pengeluaran Kas Bank berhasil difinalisasi meskipun tidak balance.'),
                    timer: 1500,
                    showConfirmButton: false
                }).then(function () {
                    window.location.href =
                        HOST_URL + 'ka/finance/pengeluarankb';
                });
            },

            error: function (jqXHR, textStatus, errorThrown) {
                console.error(
                    'finalPengeluaranKB error:',
                    jqXHR.responseText,
                    textStatus,
                    errorThrown
                );

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat finalisasi Pengeluaran Kas Bank.'
                });
            },

            complete: function () {
                $('#btnSimpan').prop('disabled', false);
            }
        });
    });
}

/* Validasi final Pengeluaran Kas/Bank: pola konfirmasi Tanda Terima */
$(document)
    .off('submit.pengeluaranKBFinal', '#formPO')
    .on('submit.pengeluaranKBFinal', '#formPO', function (e) {
        if ($(this).data('confirmed-final') === true) {
            $(this).removeData('confirmed-final');
            return true;
        }

        e.preventDefault();

        const docno = $.trim($('#docno').val() || [
            $('#prefix').val(), $('#infix').val(), $('#sufix').val()
        ].filter(Boolean).join('/'));

        if (!docno) {
            Swal.fire({ icon: 'warning', title: 'Nomor Dokumen Kosong', text: 'Nomor Pengeluaran Kas/Bank belum tersedia.' });
            return;
        }

        const totalHeader = Math.round(pengeluaranKBNumber($('#total').val()));
        const totalAlokasi = Math.round(pengeluaranKBNumber($('#totalalokasi').val()));

        if (totalAlokasi < 0) {
            Swal.fire({ icon: 'warning', title: 'Total Alokasi Tidak Valid', text: 'Total Alokasi tidak boleh negatif.' });
            return;
        }

        const balance = totalHeader === totalAlokasi;
        Swal.fire({
            icon: balance ? 'question' : 'warning',
            title: balance ? 'Finalisasi Pengeluaran Kas/Bank' : 'Nilai Belum Balance',
            html: (balance
                    ? 'Data Pengeluaran Kas/Bank akan difinalisasi.'
                    : 'Total header dan total alokasi tidak balance. Apakah tetap ingin melanjutkan?') +
                '<br><br>Total Header: <b>' + totalHeader.toLocaleString('id-ID') +
                '</b><br>Total Alokasi: <b>' + totalAlokasi.toLocaleString('id-ID') + '</b>',
            showCancelButton: true,
            confirmButtonText: balance ? 'Ya, Finalisasi' : 'Ya, Tetap Lanjutkan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function (result) {
            if (!result.isConfirmed) return;
            const form = document.getElementById('formPO');
            if (!form) return;
            $(form).data('confirmed-final', true);
            form.submit();
        });
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
    var markup ="<div class='select2-result-repository__description'>" + repo.currcode +"   <i class='fa fa-circle'></i>   "+ repo.currname +"   <i class='fa fa-circle'></i>   "+  repo.kurs +"  </div>";
    return markup;
}
function formatCurrencySelection(repo) {
    return repo.currname || repo.text;
}



function savePengeluaranKBDetail() {

    console.log('Value perkiraan:', $('#perkiraan').val());
    console.log('Selected data:', $('#perkiraan').select2('data'));

    const form = document.getElementById('formPengeluaranKBDetail');
    if (!form) {
        Swal.fire({ icon: 'error', title: 'Form Tidak Ditemukan', text: 'Form detail tidak tersedia.' });
        return;
    }

    const nilai = pengeluaranKBNumber($('#nilai').val());
    const idcoa = $.trim($('#perkiraan').val() || '');
    const remarks = $.trim($('#keterangan_dtl').val() || '');
    const dk = $.trim($('#dk').val() || '').toUpperCase();

    if (!idcoa) {
        Swal.fire({ icon: 'warning', title: 'Perkiraan Wajib Diisi', text: 'Pilih perkiraan/COA terlebih dahulu.' });
        $('#perkiraan').select2('open');
        return;
    }
    if (!remarks) {
        Swal.fire({ icon: 'warning', title: 'Keterangan Wajib Diisi', text: 'Keterangan detail wajib diisi.' });
        $('#remarks').trigger('focus');
        return;
    }
    if (dk !== 'D' && dk !== 'K') {
        Swal.fire({ icon: 'warning', title: 'DK Tidak Valid', text: 'Debit/Kredit harus bernilai D atau K.' });
        $('#dk').trigger('focus');
        return;
    }
    if (nilai <= 0) {
        Swal.fire({ icon: 'warning', title: 'Nilai Tidak Valid', text: 'Nilai detail harus lebih besar dari 0.' });
        $('#nilai').trigger('focus');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan detail Pengeluaran Kas/Bank?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(function (result) {
        if (!result.isConfirmed) return;

        const $btn = $('#formPengeluaranKBDetail').find('[type="submit"], #btnSavePengeluaranKBDetail').first();
        const oldHtml = $btn.length ? $btn.html() : '';
        if ($btn.length) $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Menyimpan...');

        const formData = new FormData(form);

        const docno = $.trim($('#docno').val() || [
            $('#prefix').val(),
            $('#infix').val(),
            $('#sufix').val()
        ].filter(Boolean).join('/'));

        formData.set('docno', docno);
        formData.set('cabang', $('#cabang').val() || '');
        formData.set('prefix', $('#prefix').val() || '');
        formData.set('infix', $('#infix').val() || '');
        formData.set('sufix', $('#sufix').val() || '');
        formData.set('docdate', $('#docdate').val() || '');
        formData.set('pp', $('#pp').val() || '');
        formData.set('kdsupplier', $('#kdsupplier').val() || '');
        formData.set('alamatsupplier', $('#alamatsupplier').val() || '');
        formData.set('nmsupplier', $('#kdsupplier option:selected').text() || '');
        formData.set('currcode', $('#currcode').val() || '');
        formData.set('kurs', convertToDbNumber($('#kurs').val()));
        formData.set('keterangan', $('#keterangan').val() || '');
        formData.set('prkkas', $('#prkkas').val() || '');
        formData.set('nmprkkas', $('#nmprkkas').val() || '');
        formData.set('total', convertToDbNumber($('#total').val()));

        formData.set('idurut_detail', $('#detail_idurut').val() || '');
        formData.set('uniqueid', $('#detail_uniqueid').val() || '');
        formData.set('docno_detail', $('#detail_docno').val() || '');
        formData.set('nilai_ref', $('#nilai_ref').val() || '');
        formData.set('jurnalref', $('#jurnalref').val() || '');
        formData.set('nobukti', $('#nobukti').val() || '');
        formData.set('perkiraan', $('#perkiraan').val() || '');
        formData.set('nmperkiraan', $('#nmperkiraan').val() || '');
        formData.set('dk', $('#dk').val() || 'D');
        formData.set('costcenter', $('#costcenter').val() || '');
        formData.set('keterangan_dtl', $('#keterangan_dtl').val() || '');
        formData.set('nilai', convertToDbNumber($('#nilai').val()));

        $.ajax({
            url: HOST_URL + 'ka/finance/savePengeluaranKBDetail',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (res) {
                if (!res || res.success !== true) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: (res && res.message) || 'Detail gagal disimpan.'
                    });
                    return;
                }

                $('#modalDetailPengeluaranKB').modal('hide');
                form.reset();
                $('#uniqueid, #detail_id').val('');
                $('#idcoa').val(null).trigger('change');
                $('#cabangdtl').val(null).trigger('change');
                $('#dk').val('D').trigger('change');
                $('#nmcoa').val('');
                $('#modalDetailPengeluaranKBLabel').text('Tambah Detail Pengeluaran Kas/Bank');

                reload_table_pengeluarankb_dtl();
                if (typeof documentReadable === 'function') documentReadable();
            },
            error: function (xhr) {
                console.error('savePengeluaranKBDetail:', xhr.responseText);
                let message = 'Terjadi kesalahan saat menyimpan detail.';
                try {
                    const response = JSON.parse(xhr.responseText || '{}');
                    if (response.message) message = response.message;
                } catch (e) {}
                Swal.fire({ icon: 'error', title: 'Server Error', text: message });
            },
            complete: function () {
                if ($btn.length) $btn.prop('disabled', false).html(oldHtml);
            }
        });
    });
}


function formatSupplier(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdsupplier +"   <i class='fa fa-circle'></i>   "+ repo.nmsupplier + " </div>";
    return markup;
}
function formatSupplierSelection(repo) {
    return repo.nmsupplier || repo.text;
}

// ======================= PENJUALAN ==================================

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
        let kursConvert = convertToDbNumber($('#kurs').val())
        let currcode = ($('#currcode').val() || '').trim();

        if(currcode === ''){

            Swal.fire({
                icon: 'warning',
                text: 'Currcode wajib dipilih terlebih dahulu'
            });

            $('#kdsupplier').val(null).trigger('change');

            return;
        }


        // $("#phone").val(selectedData.phone || '').prop('disabled', true);
    }
});

// function checkEnableSupplier() {
//     let docno  = $('#docno').val().trim();
//     let cabang = $('#cabang').val().trim();

//     if (docno !== '' && cabang !== '') {
//         $('#kdsupplier').prop('disabled', false);
//     } else {
//         $('#kdsupplier').prop('disabled', true);
//     }
// }

// // $('#docno, #cabang').on('change keyup', function () {
// //     checkEnableSupplier();
// // });

// $(document).ready(function () {
//     checkEnableSupplier();
// });

let currentKodeSuffix = '';
let skipRoleChange = false;

$('#cabang').on('change', function () {

    if (skipRoleChange) return;

    let idbranch = $(this).val();

    if (idbranch) {

        $.ajax({

            url: HOST_URL + '/ka/finance/getBranchInfoPengeluaranKB',

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

                // PREFIX KHUSUS PENGELUARAN KB
                $('#prefix').val('BKK');

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


                        // Simpan data lengkap currency

                        $(option).data(
                            'currency-data',
                            currencyData
                        );


                        // =============================================
                        // SELECT IDR
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
                // KONFIGURASI UMUM
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
                        2000 + parseInt(yy, 10);


                    var month =
                        parseInt(mm, 10) - 1;


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


                    // =============================================
                    // UPDATE DATERANGEPICKER
                    // =============================================

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
                        // FALLBACK INITIALIZE
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
                                format: 'DD-MM-YYYY'
                            },

                            cancelLabel: 'Clear'

                        });


                        // =============================================
                        // APPLY DATE
                        // =============================================

                        $el.off(
                            'apply.daterangepicker'
                        );

                        $el.on(
                            'apply.daterangepicker',
                            function (ev, picker) {

                                $(this).val(
                                    picker.startDate
                                        .format('DD-MM-YYYY')
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


                    // =============================================
                    // SET TANGGAL DEFAULT
                    // =============================================

                    if (
                        today.isSameOrAfter(startDate) &&
                        today.isSameOrBefore(endDate)
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
                // GENERATE DOCNO
                // =====================================================

                $('#docno').val(

                    'BKK/' +
                    res.infix +
                    '/' +
                    currentKodeSuffix +
                    '0001'

                );


                // =====================================================
                // ENABLE SUPPLIER
                // =====================================================

                $('#kdsupplier')
                    .prop('disabled', false);


                // =====================================================
                // RESET SUPPLIER
                // =====================================================

                $('#kdsupplier')
                    .val(null)
                    .trigger('change');


                // =====================================================
                // RESET ALAMAT SUPPLIER
                // =====================================================

                $('#alamatsupplier').val('');


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
        url: HOST_URL + '/ka/finance/getNextSuffixPengeluaranKB',
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


function loadPerSupplier() {

    console.log('=== loadPerSupplier DIJALANKAN ===');

    let kdsupplier = $.trim($('#kdsupplier').val());

    console.log('kdsupplier =', kdsupplier);

    if (!kdsupplier) {
        Swal.fire({
            icon: 'warning',
            title: 'Supplier',
            text: 'Supplier belum dipilih.'
        });
        return;
    }

    $.ajax({
        url: HOST_URL + 'ka/finance/loadPerSupplier',
        type: 'GET',
        dataType: 'json',

        data: {
            kdsupplier: kdsupplier
        },

        beforeSend: function () {

            console.log('=== AJAX DIKIRIM ===');

            console.log(
                'URL:',
                HOST_URL + 'ka/finance/loadPerSupplier'
            );

            console.log('PARAMETER:', {
                kdsupplier: kdsupplier
            });
        },

        success: function (response) {

            console.log('=== AJAX SUCCESS ===');
            console.log('Response:', response);

            if (!response.status) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Supplier',
                    text: response.message || 'Data supplier tidak ditemukan.'
                });

                return;
            }

            console.log('Data Supplier:', response.data);

            // proses data di sini
        },

        error: function (xhr, status, error) {

            console.log('=== AJAX ERROR ===');
            console.log('status:', status);
            console.log('error:', error);
            console.log('response:', xhr.responseText);

        }
    });
}



$("#cabangdtl").select2({
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
                $('#cabangdtl').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#cabangdtl').trigger({
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

//////////// HELPER









// ============================================================
// 6. DETAIL TANDA TERIMA: INPUT, EDIT, HAPUS, SIMPAN
// ============================================================

function addDetailPengeluaranKB() {
    const docno = $.trim($('#docno').val());

    if (!docno) {
        Swal.fire(
            'Peringatan',
            'Nomor dokumen wajib diisi.',
            'warning'
        );
        return;
    }

    const currentStatus = $.trim($('#status').val()).toUpperCase();

    if (currentStatus && currentStatus !== 'E') {
        Swal.fire(
            'Peringatan',
            'Header tidak dapat diubah karena status bukan E.',
            'warning'
        );
        return;
    }

    function clean(value) {
        return $.trim(value == null ? '' : String(value));
    }

    function showLoading() {
        $('#loadMe').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    function hideLoading() {
        $('#loadMe').modal('hide');
    }

    // Ambil nama supplier dari field atau pilihan Select2.
    const supplierCode = clean($('#kdsupplier').val());
    const selectedSupplierText = clean(
        $('#kdsupplier option:selected').text()
    );

    let supplierName = clean($('#nmsupplier').val());

    if (
        !supplierName &&
        selectedSupplierText &&
        selectedSupplierText !== supplierCode
    ) {
        supplierName = selectedSupplierText;
    }

    // Data header yang akan disimpan.
    const header = {
        docno: docno,
        docdate: $('#docdate').val(),
        cabang: $('#cabang').val(),
        pemohon: $('#pemohon').val(),
        kdsupplier: supplierCode,
        nmsupplier: supplierName,
        alamatsupplier: $('#alamatsupplier').val(),
        prkkas: $('#prkkas').val(),
        currcode: $('#currcode').val(),
        kurs: $('#kurs').val(),
        keterangan: $('#keterangan').val(),
        dpp: $('#dpp').val(),
        jumlahpajak: $('#ppn').val(),
        total: $('#total').val(),
        balance: $('#balance').val(),
        np: $('#np').val(),
        jthtempo: $('#jthtempo').val(),
        isinclusive: $('#isinclusive').val(),
        docnotmp: $('#docnotmp').val()
    };

    if (!header.docdate || !header.cabang) {
        Swal.fire(
            'Peringatan',
            'Tanggal dokumen dan cabang wajib diisi.',
            'warning'
        );
        return;
    }

    if (!header.kdsupplier) {
        Swal.fire(
            'Peringatan',
            'Supplier pada header wajib diisi.',
            'warning'
        );
        return;
    }

    // =====================================================
    // BACA ULANG HEADER SETELAH BERHASIL DISIMPAN
    // =====================================================
    function loadHeaderAndOpenModal() {
        $.ajax({
            type: 'GET',
            url: HOST_URL + 'ka/finance/showing_pengeluarankbtemp',
            data: {
                docno: docno
            },
            dataType: 'json',

            success: function (res) {
                if (
                    !res ||
                    res.status !== true ||
                    !res.data ||
                    !res.data.header
                ) {
                    Swal.fire(
                        'Gagal',
                        res && res.message
                            ? res.message
                            : 'Header tersimpan, tetapi data header gagal dibaca ulang.',
                        'error'
                    );
                    return;
                }

                const savedHeader = res.data.header;
                const idurut = clean(savedHeader.idurut);
                const savedDocno = clean(savedHeader.docno) || docno;
                const savedStatus = clean(savedHeader.status).toUpperCase();

                const savedSupplierCode =
                    clean(savedHeader.kdsupplier) || supplierCode;

                const savedSupplierName =
                    clean(savedHeader.nmsupplier) || supplierName;

                if (!idurut) {
                    Swal.fire(
                        'Peringatan',
                        'ID header tidak ditemukan. Modal detail tidak dapat dibuka.',
                        'warning'
                    );
                    return;
                }

                if (savedStatus !== 'E') {
                    Swal.fire(
                        'Peringatan',
                        'Header tidak dapat diubah karena status bukan E.',
                        'warning'
                    );
                    return;
                }

                // =================================================
                // RESET FORM DETAIL SAJA
                // Jangan reset form header.
                // =================================================
                const form = $('#formPengeluaranKBDetail');

                if (form.length && form[0]) {
                    form[0].reset();
                }

                // =================================================
                // ISI IDENTITAS HEADER KE MODAL DETAIL
                // =================================================
                $('#detail_idurut').val('');
                $('#detail_uniqueid').val('');
                $('#detail_docno').val(savedDocno);

                $('#detail_supplier').val(
                    savedSupplierCode +
                    (savedSupplierName !== ''
                        ? ' - ' + savedSupplierName
                        : '')
                );

                $('#detail_docno_display').val(savedDocno);
                $('#detail_idurut_display').val(idurut);

                // Mendukung nama ID alternatif bila masih digunakan view.
                $('#docno_detail').val(savedDocno);
                $('#idurut_detail').val(idurut);

                // Simpan identitas header pada modal.
                $('#modalDetailPengeluaranKB')
                    .attr('data-idurut', idurut)
                    .attr('data-docno', savedDocno);

                // =================================================
                // RESET INPUT DETAIL
                // =================================================
                $('#detail_id').val('');

                // Bersihkan unique ID untuk detail baru.
                $('#uniqueid, #detail_uniqueid').val('');

                $('#nobukti').val('');

                // Keterangan detail.
                $('#keterangan_dtl, #remarks').val('');

                // Debit sebagai nilai awal.
                $('#dk')
                    .val('D')
                    .trigger('change');

                // Nilai detail.
                $('#nilai').val('0');

                // Perkiraan / COA.
                $('#perkiraan, #idcoa')
                    .val(null)
                    .trigger('change');

                $('#nmcoa').val('');

                // Cost center / cabang detail.
                $('#costcenter, #cabangdtl')
                    .val(null)
                    .trigger('change');

                // Pastikan identitas header utama tetap tersimpan.
                $('#docno').val(savedDocno);
                $('#status').val(savedStatus);

                // =================================================
                // BUKA MODAL
                // =================================================
                $('#modalDetailPengeluaranKB').modal('show');

                console.log(
                    'Header Pengeluaran Kas/Bank:',
                    savedHeader
                );

                console.log('Siap tambah detail:', {
                    idurut: idurut,
                    docno: savedDocno,
                    kdsupplier: savedSupplierCode,
                    nmsupplier: savedSupplierName
                });
            },

            error: function (xhr, textStatus, errorThrown) {
                console.error(
                    'Gagal membaca ulang header:',
                    textStatus,
                    errorThrown,
                    xhr.responseText
                );

                Swal.fire(
                    'Error',
                    'Header tersimpan, tetapi gagal membaca data header untuk modal detail.',
                    'error'
                );
            },

            complete: function () {
                hideLoading();
            }
        });
    }

    // =====================================================
    // SIMPAN / INISIALISASI HEADER
    // =====================================================
    $.ajax({
        url: HOST_URL + 'ka/finance/initPengeluaranKBHeader',
        type: 'POST',
        data: header,
        dataType: 'json',

        beforeSend: function () {
            showLoading();
        },

        success: function (res) {
            if (!res || res.success !== true) {
                Swal.fire(
                    'Gagal',
                    res && res.message
                        ? res.message
                        : 'Header gagal disimpan.',
                    'error'
                );

                hideLoading();
                return;
            }

            // Baca header yang sudah tersimpan, lalu buka modal detail.
            loadHeaderAndOpenModal();
        },

        error: function (xhr) {
            console.error(
                'initPengeluaranKBHeader error:',
                xhr.responseText
            );

            Swal.fire(
                'Error',
                xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Gagal menyimpan header.',
                'error'
            );

            hideLoading();
        }
    });
}




$('#costcenter').select2({
    dropdownParent: $('#modalDetailPengeluaranKB .modal-content'),
    width: '100%',
    placeholder: 'Pilih Cost / Profit Center',
    allowClear: true
});

$('#perkiraan').select2({
    dropdownParent: $('#modalDetailPengeluaranKB .modal-content'),
    width: '100%',
    placeholder: 'Pilih Perkiraan',
    allowClear: true,

    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
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
                _parameterx_: " and trim(level)='5'",
                term: params.term
            };
        },

        processResults: function (data, params) {
            params.page = params.page || 1;

            return {
                results: (data.items || []).map(function (item) {
                    return {
                        ...item,
                        id: item.idcoa,
                        text: item.idcoa + ' - ' + item.nmcoa
                    };
                }),
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },

        cache: false
    },

    escapeMarkup: function (markup) {
        return markup;
    },

    templateResult: formatCoa,
    templateSelection: formatCoaSelection

}).on('select2:select', function (e) {

    const data = e.params.data || {};

    // Simpan ID COA dan nama perkiraan
    $('#perkiraan').val(data.idcoa || data.id || '');
    $('#nmperkiraan').val(data.nmcoa || '');

    console.log('PERKIRAAN:', $('#perkiraan').val());
    console.log('NAMA PERKIRAAN:', $('#nmperkiraan').val());

    // =====================================================
    // HITUNG SELISIH TOTAL
    // =====================================================
    function toInteger(value) {
        const raw = String(value || '')
            .trim()
            .replace(/,/g, '')
            .replace(/[^0-9.\-]/g, '');

        const numberValue = Number(raw);

        if (!Number.isFinite(numberValue)) {
            return 0;
        }

        return Math.round(numberValue);
    }

    const total = toInteger($('#total').val());
    const totalAlokasi = toInteger($('#totalalokasi').val());

    let selisih = 0;
    let dk = 'D';

    if (total > totalAlokasi) {
        selisih = total - totalAlokasi;
        dk = 'D';
    } else if (total < totalAlokasi) {
        selisih = totalAlokasi - total;
        dk = 'K';
    } else {
        selisih = 0;
        dk = 'D';
    }

    // =====================================================
    // ISI NILAI DAN DK
    // =====================================================
    $('#nilai').val(tterimaFormatNumber(selisih));

    $('#dk')
        .val(dk)
        .trigger('change');

    console.log('TOTAL:', total);
    console.log('TOTAL ALOKASI:', totalAlokasi);
    console.log('SELISIH:', selisih);
    console.log('DK:', dk);
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
// -------------------------------------------------------------
// EDIT SELECTED
// -------------------------------------------------------------
function editSelectedTterimaDetail() {

    const ids =
        getSelectedTterimaDetailIds();

    if (ids.length !== 1) {

        Swal.fire({
            icon: 'warning',
            title: 'Pilih 1 Detail',
            text: 'Pilih tepat satu detail untuk diedit.'
        });

        return;
    }

    const id = ids[0];

    const row =
        window.tterimaDetailRows[id];

    if (!row) {

        Swal.fire({
            icon: 'error',
            title: 'Data Tidak Ditemukan',
            text: 'Data detail tidak ditemukan.'
        });

        return;
    }

    // Header reference
    const idurutHeader =
        $.trim(
            $('#idurut').val() || ''
        );

    const docno =
        $.trim(
            $('#docno').val() || ''
        );
// No BK Revisi
    const nobkrev = $.trim(row.nobkrev || '');
    $('#nobkrev').val(nobkrev);

    $('#detail_idurut')
        .val(idurutHeader);

    $('#detail_uniqueid')
        .val(id);

    $('#detail_docno')
        .val(docno);

    const supplier =
        $.trim(
            $('#kdsupplier').val() || ''
        );

    const supplierText =
        $('#kdsupplier option:selected').text();

    $('#detail_supplier')
        .val(
            supplierText ||
            supplier ||
            '-'
        );

    $('#detail_docno_display')
        .val(docno);

    $('#detail_idurut_display')
        .val(idurutHeader);

    // Detail
    $('#nobukti')
        .val(
            String(row.nobukti ?? '').trim()
        );

    $('#keterangan_dtl')
        .val(
            String(row.keterangan ?? '')
                .trim()
        );

    $('#dk')
        .val(
            String(row.dk ?? 'D')
                .trim()
                .toUpperCase()
        )
        .trigger('change');

    $('#nilai')
        .val(
            tterimaFormatNumber(row.nilai)
        );

    // Perkiraan
    const coa =
        String(row.noperkiraan ?? '').trim();

    const coaName =
        String(row.namaperkiraan ?? '').trim();

    if (coa !== '') {

        const option =
            new Option(
                coaName !== ''
                    ? coa + ' - ' + coaName
                    : coa,
                coa,
                true,
                true
            );

        $('#perkiraan')
            .empty()
            .append(option)
            .trigger('change');

        /*baru*/
        $('#nilai_ref')
            .val(
                tterimaFormatNumber(row.nilai)
            );
        $('#jurnalref')
            .val(
                row.jurnalref
            );

    } else {

        $('#perkiraan')
            .val(null)
            .trigger('change');
    }

    // Cost / Profit Center
    const cost =
        String(
            row.costprofitcenter ??
            row.costcenter ??
            ''
        ).trim();

    if (cost !== '') {

        let $option =
            $('#costcenter option[value="' +
                cost.replace(/"/g, '\\"') +
                '"]');

        if (!$option.length) {

            const text =
                cost === '01.01'
                    ? '01.01 - PLANT I'
                    : cost === '01.02'
                        ? '01.02 - PLANT II'
                        : cost;

            $option =
                $(new Option(
                    text,
                    cost,
                    true,
                    true
                ));

            $('#costcenter')
                .append($option);

        }

        $('#costcenter')
            .val(cost)
            .trigger('change');

    } else {

        $('#costcenter')
            .val(null)
            .trigger('change');
    }

    $('#modalUpdateTterimaDtlLabel')
        .text('Edit Detail Tanda Terima');

    $('#btnSavePengeluaranKBDetail')
        .html(
            '<i class="fa fa-save me-1"></i> Update'
        )
        .data('mode', 'edit');

    const modalElement =
        document.getElementById(
            'modalUpdateTterimaDtl'
        );

    if (modalElement) {

        bootstrap.Modal
            .getOrCreateInstance(
                modalElement
            )
            .show();
    }
}

// -------------------------------------------------------------
// DELETE SELECTED
// -------------------------------------------------------------
function deleteSelectedTterimaDetail() {

    const ids =
        getSelectedTterimaDetailIds();

    if (ids.length === 0) {

        Swal.fire({
            icon: 'warning',
            title: 'Belum Ada Pilihan',
            text: 'Pilih detail yang akan dihapus.'
        });

        return;
    }

    const docno =
        $.trim(
            $('#docno').val() || ''
        );

    if (docno === '') {

        Swal.fire({
            icon: 'warning',
            title: 'No. TT Tidak Ada',
            text: 'Nomor Tanda Terima belum tersedia.'
        });

        return;
    }

    Swal.fire({
        icon: 'warning',
        title: 'Hapus Detail?',
        text:
            'Sebanyak ' +
            ids.length +
            ' detail akan dihapus.',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(function (result) {

        if (!result.isConfirmed) {
            return;
        }

        const requests =
            ids.map(function (id) {

                return $.ajax({

                    type: 'POST',

                    url:
                        HOST_URL +
                        'ka/finance/deleteTterimaDetail',

                    data: {
                        docno: docno,
                        idurut: id
                    },

                    dataType: 'json'

                });
            });

        Promise.all(requests)
            .then(function (responses) {

                const failed =
                    responses.filter(function (res) {

                        return !res ||
                            (
                                res.status !== true &&
                                res.success !== true
                            );
                    });

                if (failed.length > 0) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Sebagian Gagal',
                        text:
                            failed.length +
                            ' detail gagal dihapus.'
                    });

                } else {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text:
                            ids.length +
                            ' detail berhasil dihapus.',
                        timer: 1000,
                        showConfirmButton: false
                    });
                }

                window.location.reload();

            })
            .catch(function (xhr) {

                console.error(
                    'deleteTterimaDetail error:',
                    xhr
                );

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menghapus detail.'
                });
            });

    });
}

// ============================================================
// SAVE / UPDATE DETAIL TANDA TERIMA
// ADD  : idurut_detail kosong
// EDIT : idurut_detail terisi
// ============================================================


function reloadTterimaDetailFresh() {

    $.ajax({

        type: 'GET',

        url:
            HOST_URL +
            'ka/finance/showing_pengeluarankbtemp',

        data: {
            _t: Date.now()
        },

        dataType: 'json',

        cache: false,

        success: function (res) {

            console.log(
                'showing_pengeluarankbtemp setelah delete:',
                res
            );

            if (
                !res ||
                res.status !== true ||
                !res.data
            ) {
                loadPengeluaranKBDetail([]);
                syncPengeluaranKBDetailActionButtons();
                return;
            }

            const detail =
                Array.isArray(res.data.detail)
                    ? res.data.detail
                    : [];

            loadPengeluaranKBDetail(detail);

            $('#checkAll')
                .prop('checked', false)
                .prop('indeterminate', false);

            syncPengeluaranKBDetailActionButtons();
        },

        error: function (xhr) {

            console.error(
                'reloadTterimaDetailFresh error:',
                xhr.responseText
            );

            Swal.fire({
                icon: 'error',
                title: 'Gagal Reload Detail',
                text: 'Data detail gagal dimuat ulang dari server.'
            });
        }
    });
}

// -------------------------------------------------------------
// RENDER LIST DETAIL PENGELUARAN KAS/BANK
// -------------------------------------------------------------
function loadPengeluaranKBDetail(detail) {
    const rows = Array.isArray(detail) ? detail : [];
    const $body = $('#bodyPengeluaranKBDetail');

    if (!$body.length) return;

    window.pengeluaranKBDetailRows = {};

    $('#checkAll')
        .prop('checked', false)
        .prop('indeterminate', false);

    if (rows.length === 0) {
        $body.html(`
            <tr>
                <td colspan="7" class="text-center py-3">
                    <i class="fa fa-box-open text-muted me-1"></i>
                    Belum ada detail Pengeluaran Kas/Bank.
                </td>
            </tr>
        `);

        $('#totalalokasi').val(
            typeof formatNumber === 'function'
                ? formatNumber(0)
                : '0'
        );

        updateDetailActionButtons();
        return;
    }

    let html = '';

    rows.forEach(function (row) {
        const id = String(
            row.idurut ??
            row.idurut_detail ??
            ''
        ).trim();

        if (id === '') return;

        window.pengeluaranKBDetailRows[id] = row;

        const escapeHtml = function (value) {
            return String(value ?? '').replace(/[&<>"']/g, function (char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[char];
            });
        };

        const nobukti = escapeHtml(row.nobukti);
        const idcoa = escapeHtml(row.idcoa);
        const nmcoa = escapeHtml(row.nmcoa);
        const keterangan = escapeHtml(
            row.remarks ?? row.keterangan ?? ''
        );

        const dk = escapeHtml(
            String(row.dk ?? '').trim().toUpperCase()
        );

        const cabang = escapeHtml(
            row.cabangdtl ?? row.cabang ?? ''
        );

        const nilai = typeof formatNumber === 'function'
            ? formatNumber(row.nilai)
            : escapeHtml(row.nilai);

        html += `
            <tr data-detail-id="${escapeHtml(id)}">
                <td class="text-center">
                    <input type="checkbox"
                        class="row-check"
                        value="${escapeHtml(id)}"
                        aria-label="Pilih detail ${escapeHtml(id)}">
                </td>
                <td>${nobukti || '-'}</td>
                <td>
                    <div>${idcoa || '-'}</div>
                    <small class="text-muted">${nmcoa || ''}</small>
                </td>
                <td>${keterangan || '-'}</td>
                <td class="text-center">${dk}</td>
                <td>${cabang || '-'}</td>
                <td class="text-end">${nilai}</td>
            </tr>
        `;
    });

    $body.html(html);

    updatePengeluaranKBTotalAlokasi(rows);
    updateDetailActionButtons();
}


// -------------------------------------------------------------
// KLIK BARIS = TOGGLE CHECKBOX
// -------------------------------------------------------------
$(document)
    .off('click.pengeluaranKBDetailRow',
        '#tabpengeluarankbdtl tbody tr')
    .on('click.pengeluaranKBDetailRow',
        '#tabpengeluarankbdtl tbody tr',
        function (e) {

            if ($(e.target).closest(
                'input, button, a, select, label, .select2-container'
            ).length) {
                return;
            }

            const $checkbox = $(this).find('.row-check').first();

            if (!$checkbox.length) return;

            $checkbox
                .prop('checked', !$checkbox.prop('checked'))
                .trigger('change');
        });


// -------------------------------------------------------------
// CHECKBOX DETAIL
// -------------------------------------------------------------
$(document)
    .off('change.pengeluaranKBDetail',
        '#tabpengeluarankbdtl .row-check')
    .on('change.pengeluaranKBDetail',
        '#tabpengeluarankbdtl .row-check',
        function () {

            const $checks = $(
                '#tabpengeluarankbdtl tbody .row-check'
            );

            const total = $checks.length;
            const checked = $checks.filter(':checked').length;

            $('#checkAll')
                .prop('checked', total > 0 && total === checked)
                .prop('indeterminate', checked > 0 && checked < total);

            updateDetailActionButtons();
        });


// -------------------------------------------------------------
// CHECK ALL
// -------------------------------------------------------------
$(document)
    .off('change.pengeluaranKBCheckAll', '#checkAll')
    .on('change.pengeluaranKBCheckAll', '#checkAll', function () {

        $('#tabpengeluarankbdtl tbody .row-check')
            .prop('checked', this.checked);

        updateDetailActionButtons();
    });


// -------------------------------------------------------------
// UPDATE TOTAL ALOKASI: DEBIT - KREDIT
// -------------------------------------------------------------



function updatePengeluaranKBTotalAlokasi(response) {
    let total = 0;

    const rows = Array.isArray(response)
        ? response
        : (response?.data?.detail || response?.detail || []);

    rows.forEach(function (row) {
        let nilai;
        let dk;

        if (Array.isArray(row)) {
            // Format array dari endpoint DataTable
            dk = row[5];
            nilai = row[8];
        } else {
            // Format object dari response.data.detail
            dk = row.dk;
            nilai = row.nilai;
        }

        dk = String(dk ?? '').trim().toUpperCase();

        nilai = typeof pengeluaranKBNumber === 'function'
            ? pengeluaranKBNumber(nilai)
            : (parseFloat(nilai) || 0);

        if (dk === 'D' || dk === 'DEBIT') {
            total += nilai;
        } else if (
            dk === 'K' ||
            dk === 'C' ||
            dk === 'KREDIT' ||
            dk === 'CREDIT'
        ) {
            total -= nilai;
        }
    });

    $('#totalalokasi').val(
        total.toLocaleString('id-ID')
    );

    console.log('Data untuk Total Alokasi:', rows);
    console.log('Hasil Total Alokasi:', total);

    return total;
}


// -------------------------------------------------------------
// ENABLE / DISABLE ACTION PENGELUARAN KAS/BANK
// -------------------------------------------------------------
function syncPengeluaranKBDetailActionButtons() {

    const selected = getSelectedPengeluaranKBDetailIds();
    const count = selected.length;

    $('#btnEditDetail')
        .prop('disabled', count !== 1);

    $('#btnDeleteDetail')
        .prop('disabled', count === 0);

    // Check All
    const total = $('#tabpengeluarankbdtl tbody .row-check').length;
    const checked = $('#tabpengeluarankbdtl tbody .row-check:checked').length;

    if ($('#checkAll').length) {
        $('#checkAll').prop(
            'checked',
            total > 0 && checked === total
        );

        $('#checkAll').prop(
            'indeterminate',
            checked > 0 && checked < total
        );
    }
}

// -------------------------------------------------------------
// SELECTED ROWS PENGELUARAN KAS/BANK
// -------------------------------------------------------------
function getSelectedPengeluaranKBDetailIds() {

    const ids = [];

    $('#tabpengeluarankbdtl tbody .row-check:checked')
        .each(function () {

            const id = $.trim(
                $(this).data('idurut') ||
                $(this).val() ||
                ''
            );

            if (id !== '') {
                ids.push(String(id));
            }
        });

    return ids;
}

$(document).ready(function() {
    // Handle form submission event
    // Handle form submission event





    tablePengeluaranKBTrx();
    // tablePOApprvTrx();
    tablePengeluaranKBDetail();
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
