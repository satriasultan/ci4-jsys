/* pengeluarankb.js */

var save_method; //for save method string
var table;
var initTable;
//"use strict";
function tablePengeluaranKBTrx() {
    var initTable = function () {
        var table = $('#tablepengeluarankbTrx');

        // Hindari inisialisasi DataTable berulang
        if ($.fn.DataTable.isDataTable('#tablepengeluarankbTrx')) {
            table.DataTable().destroy();
        }

        table.DataTable({
            processing: true,
            serverSide: true,
            order: [],
            language: languageDatatable(),

            paging: true,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,

            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            responsive: false,

            bFilter: true,

            lengthMenu: [
                [10, 25, 50, -1],
                ['10 rows', '25 rows', '50 rows', 'Show all']
            ],

            dom: 'Bfrtip',

            buttons: [
                'pageLength',
                'excel'
            ],

            ajax: {
                url: HOST_URL + 'ka/finance/list_pengeluarankb',
                type: 'POST',

                data: function (data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namasupplier = $('#namasupplier').val();
                    data.status = $('#status_filter').val();
                },

                dataFilter: function (data) {
                    var json = jQuery.parseJSON(data);

                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;

                    return JSON.stringify(json);
                }
            },

            columnDefs: [
                {
                    targets: -1,
                    orderable: false
                }
            ],

            initComplete: function () {
                var api = this.api();

                // Pastikan lebar kolom dihitung ulang
                setTimeout(function () {
                    api.columns.adjust();
                }, 100);
            },

            drawCallback: function () {
                this.api().columns.adjust();
            }
        });
    };

    return initTable();
}

function reload_tablePengeluaranKBTrx() {
    if ($.fn.DataTable.isDataTable('#tablepengeluarankbTrx')) {
        $('#tablepengeluarankbTrx').DataTable().ajax.reload(null, false);
    } else {
        tablePengeluaranKBTrx();
    }
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
            $('#suffix').val(parts.slice(2).join('/') || '').prop('readonly', true);

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

$(document)
    .off('click.pengeluaranKBDetailRow', '#tabpengeluarankbdtl tbody tr')
    .on('click.pengeluaranKBDetailRow', '#tabpengeluarankbdtl tbody tr', function (e) {
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
    if (value === null || value === undefined || value === '') {
        return 0;
    }

    let str = String(value).trim();

    // Format: 3,000 atau 3,000.00
    // Koma = pemisah ribuan, titik = desimal
    if (str.includes(',')) {
        str = str.replace(/,/g, '');
    }

    const number = Number(str);

    return Number.isFinite(number) ? number : 0;
}

function finalPengeluaranKB() {
    // ==========================================
    // 1. AMBIL IDENTITAS DOKUMEN
    // ==========================================
    const idurut = $.trim(
        $('#idurut').val() ||
        $('#idurut_header').val() ||
        $('#idurut_hd').val() ||
        ''
    );

    const docno = $.trim(
        $('#docno').val() ||
        [
            $('#prefix').val(),
            $('#infix').val(),
            $('#suffix').val()
        ].filter(Boolean).join('/')
    );

    console.log('Finalisasi - ID Urut:', idurut);
    console.log('Finalisasi - Docno:', docno);

    // ==========================================
    // 2. PARSE ANGKA
    // Format: 3,000 atau 3,000.00
    // ==========================================
    function toNumber(value) {
        if (value === null || value === undefined || value === '') {
            return 0;
        }

        let normalized = String(value)
            .trim()
            .replace(/[^\d,.-]/g, '');

        // Koma merupakan pemisah ribuan.
        normalized = normalized.replace(/,/g, '');

        const result = Number(normalized);

        return Number.isFinite(result) ? result : NaN;
    }

    const rawHeader = $('#total').val();
    const rawAlokasi = $('#totalalokasi').val();

    const totalHeader = toNumber(rawHeader);
    const totalAlokasi = toNumber(rawAlokasi);
    const tolerance = 0.005;

    console.log('Finalisasi - Total Header:', rawHeader, totalHeader);
    console.log('Finalisasi - Total Alokasi:', rawAlokasi, totalAlokasi);

    // ==========================================
    // 3. VALIDASI DOKUMEN
    // ==========================================
    if (!docno) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Tersedia',
            text: 'Nomor Pengeluaran Kas/Bank belum tersedia.'
        });
        return;
    }

    if (!Number.isFinite(totalHeader) || !Number.isFinite(totalAlokasi)) {
        Swal.fire({
            icon: 'warning',
            title: 'Nilai Tidak Valid',
            text: 'Total Pengeluaran atau Total Alokasi bukan angka yang valid.'
        });
        return;
    }

    if (totalAlokasi < 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Finalisasi Ditolak',
            text: 'Total Alokasi tidak boleh negatif.'
        });
        return;
    }

    // ==========================================
    // 4. TOTAL WAJIB BALANCE
    // ==========================================
    if (Math.abs(totalHeader - totalAlokasi) >= tolerance) {
        Swal.fire({
            icon: 'warning',
            title: 'Finalisasi Ditolak',
            html:
                'Total Pengeluaran dan Total Alokasi harus sama.' +
                '<br><br>Total Pengeluaran: <b>' +
                totalHeader.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) +
                '</b><br>Total Alokasi: <b>' +
                totalAlokasi.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) +
                '</b><br><br>Dokumen tidak dapat difinalisasi.'
        });
        return;
    }

    // ==========================================
    // 5. KONFIRMASI FINALISASI
    // ==========================================
    Swal.fire({
        icon: 'question',
        title: 'Finalisasi Pengeluaran Kas Bank',
        html:
            'Dokumen akan difinalisasi dan tidak lagi berstatus draft.' +
            '<br><br>Total Pengeluaran: <b>' +
            totalHeader.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) +
            '</b><br>Total Alokasi: <b>' +
            totalAlokasi.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) +
            '</b>',
        showCancelButton: true,
        confirmButtonText: 'Ya, Finalisasi',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(function (result) {
        if (!result.isConfirmed) return;

        const $btn = $('#btnSimpan');
        $btn.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: HOST_URL + 'ka/finance/finalPengeluaranKB',
            dataType: 'json',
            data: {
                idurut: idurut,
                docno: docno
            },

            success: function (res) {
                console.log('Finalisasi response:', res);

                if (!res || res.success !== true) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Finalisasi',
                        text: (res && res.message) ||
                            'Gagal melakukan finalisasi.'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message ||
                        'Pengeluaran Kas Bank berhasil difinalisasi.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(function () {
                    window.location.href =
                        HOST_URL + 'ka/finance/pengeluarankb';
                });
            },

            error: function (xhr, textStatus, errorThrown) {
                console.error(
                    'finalPengeluaranKB error:',
                    xhr.status,
                    xhr.responseText,
                    textStatus,
                    errorThrown
                );

                let message =
                    'Terjadi kesalahan saat finalisasi Pengeluaran Kas Bank.';

                try {
                    const response = JSON.parse(xhr.responseText || '{}');

                    if (response.message) {
                        message = response.message;
                    }
                } catch (e) {}

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            },

            complete: function () {
                $btn.prop('disabled', false);
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
            $('#prefix').val(), $('#infix').val(), $('#suffix').val()
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

    return "<div class='select2-result-repository__description'>" + repo.currcode + "</div>";
}

function formatCurrencySelection(repo) {
    return repo.currcode || repo.text;
}


function updateCurrencySymbol() {
    const currcode = ($('#currcode').val() || 'IDR').trim().toUpperCase();

    $('.currency-symbol').text(currcode);
}

$(document).on('change', '#currcode', function () {
    updateCurrencySymbol();
});

$(document).ready(function () {
    updateCurrencySymbol();
});

function savePengeluaranKBDetail() {
    const form = document.getElementById('formPengeluaranKBDetail');

    if (!form) {
        Swal.fire('Error', 'Form detail tidak ditemukan.', 'error');
        return;
    }

    // ==========================================
    // 1. AMBIL DATA FORM
    // ==========================================
    const nilai = pengeluaranKBNumber($('#nilai').val());
    const idcoa = $.trim($('#perkiraan').val() || '');
    const remarks = $.trim($('#keterangan_dtl').val() || '');
    const dk = $.trim($('#dk').val() || '').toUpperCase();

    const editIdurut = $.trim($('#detail_idurut').val() || '');
    const editUniqueid = $.trim($('#detail_uniqueid').val() || '');
    const isEdit = Boolean(editIdurut || editUniqueid);

    // ==========================================
    // 2. VALIDASI INPUT
    // ==========================================
    if (!idcoa) {
        Swal.fire(
            'Peringatan',
            'Pilih perkiraan/COA terlebih dahulu.',
            'warning'
        );

        $('#perkiraan').select2('open');
        return;
    }

    if (!remarks) {
        Swal.fire(
            'Peringatan',
            'Keterangan detail wajib diisi.',
            'warning'
        );

        $('#keterangan_dtl').trigger('focus');
        return;
    }

    if (dk !== 'D' && dk !== 'K') {
        Swal.fire(
            'Peringatan',
            'Debit/Kredit harus bernilai D atau K.',
            'warning'
        );
        return;
    }

    if (!Number.isFinite(nilai) || nilai <= 0) {
        Swal.fire(
            'Peringatan',
            'Nilai detail harus lebih besar dari 0.',
            'warning'
        );

        $('#nilai').trigger('focus');
        return;
    }

    // ==========================================
    // 3. VALIDASI TOTAL
    // ATURAN: TOTAL ALOKASI + NILAI <= TOTAL
    // ==========================================
    const totalHeader = pengeluaranKBNumber($('#total').val());
    const totalAlokasi = pengeluaranKBNumber($('#totalalokasi').val());
    const tolerance = 0.005;

    const totalSetelahSimpan = totalAlokasi + nilai;

    console.log('Validasi Pengeluaran Kas/Bank:', {
        totalHeader: totalHeader,
        totalAlokasi: totalAlokasi,
        nilaiBaru: nilai,
        totalSetelahSimpan: totalSetelahSimpan,
        isEdit: isEdit
    });

    if (totalSetelahSimpan > totalHeader + tolerance) {
        Swal.fire({
            icon: 'warning',
            title: 'Nilai Tidak Imbang',
            html:
                'Total Alokasi: <b>' +
                totalAlokasi.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) +
                '</b><br>Nilai Baru: <b>' +
                nilai.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) +
                '</b><br>Total Pengeluaran: <b>' +
                totalHeader.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) +
                '</b><br><br>Total Alokasi + Nilai Baru melebihi Total Pengeluaran.'
        });

        return;
    }

    // Jika total setelah ditambah nilai <= total header,
    // proses penyimpanan diperbolehkan.

    // ==========================================
    // 4. KONFIRMASI SIMPAN
    // ==========================================
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan detail Pengeluaran Kas/Bank?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        const $btn = $('#formPengeluaranKBDetail')
            .find('[type="submit"], #btnSavePengeluaranKBDetail')
            .first();

        const oldHtml = $btn.length ? $btn.html() : '';

        if ($btn.length) {
            $btn.prop('disabled', true)
                .html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Menyimpan...'
                );
        }

        // ==========================================
        // 5. SIAPKAN FORM DATA
        // ==========================================
        const formData = new FormData(form);

        const docno = $.trim($('#docno').val() || [
            $('#prefix').val(),
            $('#infix').val(),
            $('#suffix').val()
        ].filter(Boolean).join('/'));

        // Header
        formData.set('docno', docno);
        formData.set('cabang', $('#cabang').val() || '');
        formData.set('prefix', $('#prefix').val() || '');
        formData.set('infix', $('#infix').val() || '');
        formData.set('suffix', $('#suffix').val() || '');
        formData.set('docdate', $('#docdate').val() || '');
        formData.set('pp', $('#pp').val() || '');
        formData.set('kdsupplier', $('#kdsupplier').val() || '');
        formData.set('alamatsupplier', $('#alamatsupplier').val() || '');
        formData.set(
            'nmsupplier',
            $('#kdsupplier option:selected').text() || ''
        );
        formData.set('currcode', $('#currcode').val() || '');
        formData.set('kurs', convertToDbNumber($('#kurs').val()));
        formData.set('keterangan', $('#keterangan').val() || '');
        formData.set('prkkas', $('#prkkas').val() || '');
        formData.set('nmprkkas', $('#nmprkkas').val() || '');
        formData.set('total', convertToDbNumber($('#total').val()));

        // Identitas detail
        formData.set('idurut_detail', editIdurut);
        formData.set('uniqueid', editUniqueid);
        formData.set(
            'docno_detail',
            $('#detail_docno').val() || ''
        );

        // Referensi
        formData.set('nilai_ref', $('#nilai_ref').val() || '');
        formData.set('jurnalref', $('#jurnalref').val() || '');
        formData.set('nobukti', $('#nobukti').val() || '');

        // COA
        const selectedCoaId = $.trim($('#perkiraan').val() || '');
        const selectedCoaName = $.trim($('#nmperkiraan').val() || '');

        formData.set('perkiraan', selectedCoaId);
        formData.set('idperkiraan', selectedCoaId);
        formData.set('nmperkiraan', selectedCoaName);
        formData.set('namaperkiraan', selectedCoaName);

        // Detail
        formData.set('dk', dk);
        formData.set('costcenter', $('#costcenter').val() || '');
        formData.set('keterangan_dtl', remarks);
        formData.set('nilai', convertToDbNumber($('#nilai').val()));

        // ==========================================
        // 6. KIRIM KE SERVER
        // ==========================================
        $.ajax({
            url: HOST_URL + 'ka/finance/savePengeluaranKBDetail',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,

            success: function (res) {
                console.log('Response save detail:', res);

                if (!res || res.success !== true) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal Menyimpan',
                        text: (res && res.message)
                            ? res.message
                            : 'Server menolak penyimpanan detail.'
                    });
                    return;
                }

                // Reset form setelah berhasil
                form.reset();

                $('#detail_idurut, #detail_uniqueid, #detail_docno').val('');

                $('#perkiraan').val(null).trigger('change');
                $('#costcenter').val(null).trigger('change');
                $('#dk').val('D').trigger('change');

                $('#nmperkiraan').val('');
                $('#nilai_ref, #jurnalref, #nobukti').val('');

                $('#modalDetailPengeluaranKBLabel')
                    .text('Tambah Detail Pengeluaran Kas/Bank');

                $('#modalDetailPengeluaranKB').modal('hide');

                // Refresh tabel detail
                if (typeof reload_table_pengeluarankb_dtl === 'function') {
                    reload_table_pengeluarankb_dtl();
                } else if (typeof reloadTablePengeluaranKBDtl === 'function') {
                    reloadTablePengeluaranKBDtl();
                }

                // Hitung ulang total alokasi
                if (typeof refreshPengeluaranKBTotalAlokasi === 'function') {
                    refreshPengeluaranKBTotalAlokasi();
                }

                if (typeof documentReadable === 'function') {
                    documentReadable();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message || 'Detail berhasil disimpan.',
                    timer: 1500,
                    showConfirmButton: false
                });
            },

            error: function (xhr) {
                console.error(
                    'savePengeluaranKBDetail:',
                    xhr.status,
                    xhr.responseText
                );

                let message = 'Terjadi kesalahan saat menyimpan detail.';

                try {
                    const response = JSON.parse(xhr.responseText || '{}');

                    if (response.message) {
                        message = response.message;
                    }
                } catch (e) {
                    if (xhr.responseText) {
                        message = xhr.responseText.substring(0, 500);
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: message
                });
            },

            complete: function () {
                if ($btn.length) {
                    $btn.prop('disabled', false).html(oldHtml);
                }
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

    const idbranch = $(this).val();

    if (!idbranch) {
        currentKodeSuffix = '';
        $('#docno, #infix, #suffix').val('');
        return;
    }

    $.ajax({
        url: HOST_URL + '/ka/finance/getBranchInfoPengeluaranKB',
        method: 'GET',
        data: { idbranch: idbranch },
        dataType: 'json',
        cache: false,

        success: function (res) {
            if (!res || res.success !== true) {
                Swal.fire(
                    'Error',
                    (res && res.message) || 'Informasi cabang tidak ditemukan.',
                    'warning'
                );
                return;
            }

            // =========================================
            // DATA CABANG DAN NOMOR DOKUMEN
            // =========================================
            currentKodeSuffix = $.trim(res.kode_suffix || '');

            const infix = $.trim(String(res.infix || ''));

            if (!currentKodeSuffix || !infix) {
                Swal.fire(
                    'Error',
                    'Kode suffix atau periode dokumen tidak tersedia.',
                    'warning'
                );
                return;
            }

            $('#prefix').val('BKK');
            $('#infix').val(infix);

            // Ambil nomor berikutnya dari server.
            // Jangan mengisi nomor secara hardcode dengan 0001.
            loadNextSuffixPengeluaranKB();

            // =========================================
            // DEFAULT CURRENCY: IDR
            // =========================================
            $.ajax({
                url: HOST_URL +
                    'api/globalmodule/list_currency?var=' +
                    encodeURIComponent('IDR'),
                method: 'GET',
                dataType: 'json',
                cache: false,

                success: function (datax) {
                    if (
                        !datax ||
                        !Array.isArray(datax.items) ||
                        datax.items.length === 0
                    ) {
                        console.warn('Currency IDR tidak ditemukan.');
                        return;
                    }

                    const currencyData = {
                        ...datax.items[0],
                        kurs: 1
                    };

                    const $currency = $('[name="currcode"]');

                    const option = new Option(
                        currencyData.currname,
                        currencyData.currcode,
                        true,
                        true
                    );

                    $(option).data('currency-data', currencyData);

                    $currency.empty()
                        .append(option)
                        .val(currencyData.currcode)
                        .trigger('change');

                    setJtsValue('[name="kurs"]', 1);
                    $('[name="kurs"]').prop('readonly', true);
                },

                error: function (xhr) {
                    console.error(
                        'Gagal load Currency IDR:',
                        xhr.responseText
                    );
                }
            });

            // =========================================
            // KONFIGURASI PAJAK
            // =========================================
            const config = (
                Array.isArray(res.konfigurasi_umum) &&
                res.konfigurasi_umum.length > 0
            ) ? res.konfigurasi_umum[0] : null;


            // =========================================
            // BATAS TANGGAL BERDASARKAN INFIX YYMM
            // =========================================
            if (/^\d{4}$/.test(infix)) {
                $('#docdate').prop('disabled', false);

                const year = 2000 + parseInt(infix.substring(0, 2), 10);
                const month = parseInt(infix.substring(2, 4), 10) - 1;

                const startDate = moment([year, month, 1]);
                const endDate = moment(startDate).endOf('month');
                const today = moment();

                if (!startDate.isValid() || !endDate.isValid()) {
                    Swal.fire(
                        'Error',
                        'Periode tanggal tidak valid.',
                        'warning'
                    );
                    return;
                }

                const selectedDate = today.isBetween(
                    startDate,
                    endDate,
                    'day',
                    '[]'
                ) ? today : startDate;

                const $el = $('#docdate');
                let drp = $el.data('daterangepicker');

                if (drp) {
                    drp.minDate = startDate.clone();
                    drp.maxDate = endDate.clone();
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
                        locale: {
                            format: 'DD-MM-YYYY'
                        },
                        cancelLabel: 'Clear'
                    });

                    $el.off('.daterangepickerKB');

                    $el.on(
                        'apply.daterangepickerKB',
                        function (ev, picker) {
                            $(this).val(
                                picker.startDate.format('DD-MM-YYYY')
                            );
                        }
                    );

                    $el.on(
                        'cancel.daterangepickerKB',
                        function () {
                            $(this).val('');
                        }
                    );
                }

                $el.val(selectedDate.format('DD-MM-YYYY'));
            }

            // =========================================
            // ENABLE DAN RESET SUPPLIER
            // =========================================
            $('#kdsupplier')
                .prop('disabled', false)
                .val(null)
                .trigger('change');

            $('#alamatsupplier').val('');
        },

        error: function (xhr) {
            console.error(
                'getBranchInfoPengeluaranKB:',
                xhr.responseText
            );

            Swal.fire(
                'Error',
                'Gagal mengambil informasi cabang.',
                'error'
            );
        }
    });
});




function loadNextSuffixPengeluaranKB() {

    let prefix = $.trim($('#prefix').val()).toUpperCase();
    let infix = $.trim($('#infix').val());

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
        cache: false,

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
    loadNextSuffixPengeluaranKB();
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
    const prefix = String($('#prefix').val() || '').trim();
    const infix  = String($('#infix').val() || '').trim();
    const suffix = String($('#suffix').val() || '').trim();
    const header = {
        docno: [prefix, infix, suffix].join('/'),
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
    var prkkas = $('#prkkas').val();

    if (!prkkas || prkkas.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Perkiraan Kas/Bank tidak boleh kosong!'
        });

        $('#prkkas').select2('open');
        return false;
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
                // Simbol mata uang mengikuti currency dari header
                const detailCurrency =
                    clean(savedHeader.currcode || $('#currcode').val() || 'IDR')
                        .toUpperCase();

                $('#detail_currency_symbol').text(detailCurrency);

                // Buka modal detail
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
    // HITUNG SELISIH TOTAL ALOKASI
    // =====================================================
    function toInteger(value) {
        const raw = String(value ?? '')
            .trim()
            .replace(/,/g, '')
            .replace(/[^0-9.\-]/g, '');

        const numberValue = Number(raw);
        return Number.isFinite(numberValue)
            ? Math.round(numberValue)
            : 0;
    }

    const total = toInteger($('#total').val());
    const totalAlokasi = toInteger($('#totalalokasi').val());

    // Selisih positif berarti nilai header masih kurang dialokasikan.
    const selisih = total - totalAlokasi;

    let nilaiModal = Math.abs(selisih);
    let dk = 'D';

    if (selisih < 0) {
        // Alokasi melebihi total header.
        dk = 'K';
    }

    // =====================================================
    // ISI NILAI DAN DK PADA MODAL
    // =====================================================
    $('#nilai').val(tterimaFormatNumber(nilaiModal));
    $('#dk').val(dk).trigger('change');

    console.log('TOTAL:', total);
    console.log('TOTAL ALOKASI:', totalAlokasi);
    console.log('SELISIH:', selisih);
    console.log('NILAI MODAL:', nilaiModal);
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

function editSelectedPengeluaranKBDetail() {
    const ids = getCheckedDetailIds();

    if (ids.length !== 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih 1 Detail',
            text: 'Pilih tepat satu detail untuk diedit.'
        });
        return;
    }

    const uniqueid = String(ids[0]).trim();
    const table = $('#tabpengeluarankbdtl').DataTable();

    let rowData = null;

    $('#tabpengeluarankbdtl tbody .row-check:checked').each(function () {
        rowData = table.row($(this).closest('tr')).data();
    });

    if (!rowData) {
        Swal.fire({
            icon: 'error',
            title: 'Data Tidak Ditemukan',
            text: 'Data detail tidak ditemukan. Muat ulang tabel lalu coba kembali.'
        });
        return;
    }

    // Backend mengirim data DataTables berbentuk array.
    // Urutan indeks mengikuti list_tmp_pengeluarankb_dtl() di Finance.php:
    // 0 uniqueid, 1 nobukti, 2 COA tampilan, 3 remarks, 4 dk,
    // 5 cabang, 6 nilai tampilan, 7 idcoa, 8 nmcoa,
    // 9 nilai_ref, 10 jurnalref, 11 costcenter/cabang.
    if (Array.isArray(rowData)) {
        if (String(rowData[0] ?? '').trim() !== uniqueid) {
            console.error('Unique ID checkbox dan data berbeda:', {
                checkbox: uniqueid,
                row: rowData
            });

            Swal.fire({
                icon: 'error',
                title: 'Unique ID Tidak Cocok',
                text: 'Unique ID pada checkbox berbeda dari data detail.'
            });
            return;
        }

        if (rowData.length < 12) {
            console.error('Kolom tambahan untuk edit belum dikirim backend:', rowData);

            Swal.fire({
                icon: 'error',
                title: 'Data Edit Belum Lengkap',
                text: 'Backend harus mengirim 12 kolom detail, termasuk idcoa, nmcoa, nilai_ref, jurnalref, dan costcenter.'
            });
            return;
        }

        rowData = {
            uniqueid: String(rowData[0] ?? '').trim(),
            nobukti: rowData[1] ?? '',
            remarks: rowData[3] ?? '',
            dk: rowData[4] ?? 'D',
            cabang: rowData[5] ?? '',
            nilai: $('<div>').html(rowData[6] ?? '').text().trim(),
            idcoa: rowData[7] ?? '',
            nmcoa: rowData[8] ?? '',
            nilai_ref: rowData[9] ?? '',
            jurnalref: rowData[10] ?? '',
            costcenter: rowData[11] ?? rowData[5] ?? ''
        };
    } else {
        const rowUniqueId = String(rowData.uniqueid ?? '').trim();

        if (rowUniqueId !== uniqueid) {
            console.error('Unique ID checkbox dan data berbeda:', {
                checkbox: uniqueid,
                row: rowData
            });

            Swal.fire({
                icon: 'error',
                title: 'Unique ID Tidak Cocok',
                text: 'Unique ID pada checkbox berbeda dari data detail.'
            });
            return;
        }
    }

    window.pengeluaranKBDetailRows = window.pengeluaranKBDetailRows || {};
    window.pengeluaranKBDetailRows[uniqueid] = rowData;

    $('#detail_uniqueid, #uniqueid').val(uniqueid);
    $('#detail_idurut').val($('#idurut').val() || '');
    $('#detail_docno').val($('#docno').val() || '');

    $('#nobukti').val(rowData.nobukti ?? '');
    $('#keterangan_dtl').val(
        rowData.keterangan_dtl ?? rowData.remarks ?? rowData.keterangan ?? ''
    );
    $('#dk').val(String(rowData.dk ?? 'D').trim().toUpperCase())
        .trigger('change');
    $('#nilai').val(pengeluaranKBFormatNumber(rowData.nilai));
    $('#nilai_ref').val(pengeluaranKBFormatNumber(rowData.nilai_ref ?? rowData.nilai));
    $('#jurnalref').val(rowData.jurnalref ?? '');

    const coa = String(rowData.idcoa ?? rowData.idperkiraan ?? rowData.perkiraan ?? '').trim();
    const coaName = String(rowData.nmcoa ?? rowData.nmperkiraan ?? rowData.namaperkiraan ?? '').trim();

    // Isi hidden field yang benar-benar dikirim ke backend.
    // Perubahan Select2 secara programatik tidak selalu memicu select2:select.
    $('#nmperkiraan').val(coaName);

    if (coa) {
        const optionText = coaName ? coa + ' - ' + coaName : coa;
        const option = new Option(optionText, coa, true, true);
        $('#perkiraan').empty().append(option).val(coa).trigger('change');
        // Pastikan nama tetap terisi setelah event change Select2.
        $('#nmperkiraan').val(coaName);
    } else {
        $('#perkiraan').val(null).trigger('change');
        $('#nmperkiraan').val('');
    }

    const cost = String(
        rowData.costcenter ?? rowData.costprofitcenter ?? rowData.cabangdtl ?? ''
    ).trim();

    if (cost) {
        if (!$('#costcenter option').filter(function () {
            return this.value === cost;
        }).length) {
            $('#costcenter').append(new Option(cost, cost, true, true));
        }

        $('#costcenter').val(cost).trigger('change');
    }

    // =====================================================
    // ISI REFERENSI HEADER PADA MODAL EDIT DETAIL
    // Field ini juga diisi pada proses Input Detail.
    // =====================================================
    const $detailModal = $('#modalDetailPengeluaranKB');
    const headerDocno = String(
        $('#docno').val() || $('#detail_docno').val() || $detailModal.attr('data-docno') || ''
    ).trim();
    const headerIdurut = String(
        $('#idurut').val() || $detailModal.attr('data-idurut') || $('#detail_idurut').val() || ''
    ).trim();
    const supplierCode = String($('#kdsupplier').val() || '').trim();
    let supplierName = String($('#nmsupplier').val() || '').trim();

    if (!supplierName) {
        const selectedSupplierText = String(
            $('#kdsupplier option:selected').text() || ''
        ).trim();
        if (selectedSupplierText && selectedSupplierText !== supplierCode) {
            supplierName = selectedSupplierText;
        }
    }

    $('#detail_supplier').val(
        supplierCode + (supplierName ? ' - ' + supplierName : '')
    );
    $('#detail_docno_display').val(headerDocno);
    $('#detail_idurut_display').val(headerIdurut);
    $('#detail_docno').val(headerDocno);

    // Simpan referensi header pada modal agar tetap tersedia saat update.
    $('#modalDetailPengeluaranKB')
        .attr('data-idurut', headerIdurut)
        .attr('data-docno', headerDocno);

    $('#modalDetailPengeluaranKBLabel').text('Edit Detail Pengeluaran Kas/Bank');
    $('#btnSavePengeluaranKBDetail').text('Update Detail');
    $('#modalDetailPengeluaranKB').modal('show');
}


function pengeluaranKBFormatNumber(value) {
    const number = pengeluaranKBNumber(value);
    return number.toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

// -------------------------------------------------------------
// DELETE SELECTED
// -------------------------------------------------------------
function getSelectedTterimaDetailIds() {
    const ids = [];

    $('#tabpengeluarankbdtl tbody input[type="checkbox"]:checked').each(function () {
        const id = $(this).val();

        if (id !== undefined && id !== '') {
            ids.push(String(id));
        }
    });

    return ids;
}
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
                        'ka/finance/delete_pengeluarankb_detail',

                    data: {
                        docno: docno,
                        idurut: id,
                        ids: id
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

                // Ganti menjadi
                reload_table_pengeluarankb_dtl();
                refreshPengeluaranKBTotalAlokasi();

            })
            .catch(function (xhr) {

                console.error(
                    'Delete pengeluaran KB error:',
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

    const formatAmount = function (value) {
        if (typeof formatNumber === 'function') {
            return formatNumber(value ?? 0);
        }
        return escapeHtml(value ?? 0);
    };

    if (rows.length === 0) {
        $body.html(`
            <tr>
                <td colspan="7" class="text-center py-3">
                    <i class="fa fa-box-open text-muted me-1"></i>
                    Belum ada detail Pengeluaran Kas/Bank.
                </td>
            </tr>
        `);

        $('#totalalokasi').val(formatAmount(0));
        updateDetailActionButtons();
        return;
    }

    let html = '';

    rows.forEach(function (row) {
        // Identitas detail wajib uniqueid.
        const id = String(row.uniqueid ?? '').trim();

        if (!id) return;

        window.pengeluaranKBDetailRows[id] = row;

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

        const nilai = formatAmount(row.nilai);

        html += `
            <tr data-detail-id="${escapeHtml(id)}">
                <td class="text-center">
                    <input
                        type="checkbox"
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

    if (html === '') {
        $body.html(`
            <tr>
                <td colspan="7" class="text-center py-3">
                    Tidak ada detail dengan uniqueid yang valid.
                </td>
            </tr>
        `);

        $('#totalalokasi').val(formatAmount(0));
        updateDetailActionButtons();
        return;
    }

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
        total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
    );
    console.log('Data untuk Total Alokasi:', rows);
    console.log('Hasil Total Alokasi:', total);

    return total;
}



// Refresh total alokasi dari sumber detail terbaru setelah tambah/hapus/edit.
function refreshPengeluaranKBTotalAlokasi() {
    const docno = $.trim($('#docno').val() || '');
    if (!docno) {
        updatePengeluaranKBTotalAlokasi([]);
        return $.Deferred().resolve().promise();
    }

    return $.ajax({
        type: 'GET',
        url: HOST_URL + 'ka/finance/showing_pengeluarankbtemp',
        data: { docno: docno },
        dataType: 'json'
    }).done(function (response) {
        const details = response && response.data && Array.isArray(response.data.detail)
            ? response.data.detail
            : [];
        updatePengeluaranKBTotalAlokasi(details);
    }).fail(function (xhr) {
        console.error('Gagal refresh total alokasi:', xhr.responseText);
        // Fallback: reload tabel; jangan mengubah total menjadi nol saat API gagal.
        reload_table_pengeluarankb_dtl();
    });
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


/* TARIK LPB */
function btnTarikPengeluaranKB() {

    const docno = $.trim($('#docno').val() || '');
    const nobkrev = $.trim($('#nobkrev').val() || '');
    const kdsupplier = $.trim($('#kdsupplier').val() || '');

    if (docno === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Nomor Tanda Terima belum tersedia.'
        });
        return;
    }

    if (kdsupplier === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Supplier belum dipilih.'
        });
        return;
    }

    Swal.fire({
        title: 'Tarik Penerimaan?',
        text: 'Data penerimaan supplier / Nota Debit Kredit.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tarik',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: HOST_URL + 'ka/finance/tarikPenerimaanBarangSupplier',
            type: 'POST',
            dataType: 'json',
            data: {
                docno: docno,
                kdsupplier: kdsupplier,
                inputby: $('#inputby').val() || ''
            },

            beforeSend: function () {

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengambil data penerimaan.',
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
                        text: response.message || 'Tidak ada data penerimaan yang dapat ditarik.',
                        confirmButtonText: 'OK'
                    });

                    return;
                }

                // Ada data
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'Data penerimaan berhasil ditarik.',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Reload detail
                reload_table_pengeluarankb_dtl();
            },

            error: function (xhr) {

                Swal.close();

                console.error(xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengambil penerimaan.'
                });
            }
        });

    });
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
