var save_method; //for save method string
var table;
var initTable;
//"use strict";

let skipRoleChange = false;


//EDIT ITEM

// =============================================================
// HELPER READ HEADER TANDA TERIMA
// =============================================================
function setReadableText(selector, value) {

    const $el = $(selector);

    if (!$el.length) {
        return;
    }

    $el.val(
        value === null || value === undefined
            ? ''
            : String(value).trim()
    );
}


function setReadableNumber(selector, value) {

    const $el = $(selector);

    if (!$el.length) {
        return;
    }

    if (typeof setJtsValue === 'function') {
        setJtsValue(
            selector,
            convertToDbNumber(value)
        );
    } else {
        $el.val(value ?? '');
    }
}


function setReadableDate(selector, value) {

    const $el = $(selector);

    if (!$el.length) {
        return '';
    }

    const raw =
        $.trim(value || '');

    if (raw === '') {
        $el.val('');
        return '';
    }

    let m = moment(
        raw,
        'YYYY-MM-DD',
        true
    );

    if (m.isValid()) {
        const display = m.format('DD-MM-YYYY');
        $el.val(display);
        return display;
    }

    m = moment(
        raw,
        'DD-MM-YYYY',
        true
    );

    if (m.isValid()) {
        const display = m.format('DD-MM-YYYY');
        $el.val(display);
        return display;
    }

    $el.val(raw);
    return raw;
}


function documentReadable() {

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'arap/transaksi/showing_tmp_tterima',
        dataType: 'json',

        beforeSend: function () {
            $("#loadMe").modal({
                backdrop: "static",
                keyboard: false,
                show: true
            });
        },

        success: function (res) {

            console.log('showing_tmp_tterima:', res);

            if (
                !res ||
                res.status !== true ||
                !res.data ||
                !res.data.header
            ) {
                console.log(
                    'Tanda Terima belum mempunyai temporary.'
                );

                setTterimaInputMode();
                return;
            }

            const data = res.data.header;
            const detail = res.data.detail || [];
// =====================================================
// COA BANK / PERKIRAAN
// Jika coabank ada di database -> auto select
// =====================================================
            if ($.trim(data.coabank || '') !== '') {

                $.ajax({
                    type: 'POST',
                    url: HOST_URL + 'api/globalmodule/list_coa',
                    dataType: 'json',

                    data: {
                        _search_: $.trim(data.coabank),
                        _page_: 1,
                        _draw_: true,
                        _start_: 1,
                        _perpage_: 30,
                        _paramglobal_: "",
                        _parameterx_: " and trim(level)='5'",
                        term: $.trim(data.coabank)
                    },

                    success: function (coaResult) {

                        if (
                            !coaResult ||
                            !Array.isArray(coaResult.items) ||
                            coaResult.items.length === 0
                        ) {
                            return;
                        }

                        const coa = coaResult.items.find(function (item) {
                            return $.trim(
                                String(item.idcoa || '')
                            ) === $.trim(
                                String(data.coabank || '')
                            );
                        }) || coaResult.items[0];


                        if (!coa) {
                            return;
                        }


                        // Buat option Select2
                        const option = new Option(
                            coa.idcoa + ' - ' + coa.nmcoa,
                            coa.idcoa,
                            true,
                            true
                        );


                        $('#coabank')
                            .empty()
                            .append(option)
                            .val(coa.idcoa)
                            .trigger('change');


                        // Simpan nama akun
                        $('#nmcoabank').val(
                            $.trim(
                                coa.nmcoa || data.nmcoabank || ''
                            )
                        );


                        // Trigger event select2
                        $('#coabank').trigger({
                            type: 'select2:select',
                            params: {
                                data: coa
                            }
                        });

                    },

                    error: function (xhr) {

                        console.error(
                            'Gagal mengambil COA Bank:',
                            xhr.responseText
                        );
                    }
                });
            }
            console.log(
                'Temporary TT ditemukan:',
                data
            );

            // Hidden
            $('#idurut').val(
                $.trim(data.idurut || '')
            );

            $('#status').val(
                $.trim(data.status || 'E')
            );

            // Docno
            const docno =
                $.trim(data.docno || '');

            $('#docno').val(docno);

            // Prefix / infix / suffix
            if (docno !== '') {

                const parts =
                    docno.split('/');

                $('#prefix')
                    .val($.trim(parts[0] || ''))
                    .prop('readonly', true);

                $('#infix')
                    .val($.trim(parts[1] || ''))
                    .prop('readonly', true);

                $('#suffix')
                    .val(
                        $.trim(
                            parts.slice(2).join('/') || ''
                        )
                    )
                    .prop('readonly', true);

                if (parts[2]) {
                    defaultInitialPP =
                        $.trim(parts[2]).substring(0, 2);
                }
            }

            // Cabang: jangan jalankan handler branch saat UPDATE.
            if ($.trim(data.cabang || '') !== '') {

                $.ajax({
                    type: 'GET',
                    url:
                        HOST_URL +
                        'api/globalmodule/list_branchjob?var=' +
                        encodeURIComponent(
                            $.trim(data.cabang)
                        ),
                    dataType: 'json'
                }).done(function (branchData) {

                    if (
                        branchData &&
                        branchData.items &&
                        branchData.items.length > 0
                    ) {

                        const branch =
                            branchData.items[0];

                        const option =
                            new Option(
                                branch.nmbranch,
                                branch.idbranch,
                                true,
                                true
                            );

                        skipRoleChange = true;

                        $('#cabang')
                            .empty()
                            .append(option)
                            .val(branch.idbranch)
                            .trigger('change')
                            .prop('disabled', true);

                        skipRoleChange = false;
                    }

                }).fail(function (xhr) {

                    skipRoleChange = false;

                    console.error(
                        'Gagal mengambil data cabang:',
                        xhr.responseText
                    );
                });
            }

            // Supplier: jangan trigger select2:select karena handler
            // tersebut dapat menghitung ulang jatuh tempo.
            if ($.trim(data.kdsupplier || '') !== '') {

                $.ajax({
                    type: 'GET',
                    url:
                        HOST_URL +
                        'api/globalmodule/list_supplier_new?var=' +
                        encodeURIComponent(
                            $.trim(data.kdsupplier)
                        ),
                    dataType: 'json'
                }).done(function (supplierResult) {

                    if (
                        supplierResult &&
                        supplierResult.items &&
                        supplierResult.items.length > 0
                    ) {

                        const supplier =
                            supplierResult.items[0];

                        supplier.alamat =
                            $.trim(
                                data.alamatsupplier || ''
                            );

                        const option =
                            new Option(
                                supplier.nmsupplier,
                                supplier.kdsupplier,
                                true,
                                true
                            );

                        $(option).data(
                            'supplier-data',
                            supplier
                        );

                        $('#kdsupplier')
                            .empty()
                            .append(option)
                            .val(supplier.kdsupplier)
                            .trigger('change')
                            .prop('disabled', true);
                    }

                }).fail(function (xhr) {

                    console.error(
                        'Gagal mengambil supplier:',
                        xhr.responseText
                    );
                });
            }

            // Alamat supplier
            $('#alamatsupplier')
                .val(
                    $.trim(
                        data.alamatsupplier || ''
                    )
                )
                .prop('readonly', true)
                .prop('disabled', false);

            // PostgreSQL CHAR dapat memiliki trailing spaces.
            function dbDateToDisplay(value) {

                const raw =
                    $.trim(value || '');

                if (raw === '') {
                    return '';
                }

                let m =
                    moment(
                        raw,
                        'YYYY-MM-DD',
                        true
                    );

                if (m.isValid()) {
                    return m.format('DD-MM-YYYY');
                }

                m =
                    moment(
                        raw,
                        'DD-MM-YYYY',
                        true
                    );

                return m.isValid()
                    ? m.format('DD-MM-YYYY')
                    : '';
            }

            // -----------------------------------------------------
            // HEADER LENGKAP
            // -----------------------------------------------------
            // Field dibaca dari temporary header. Selector yang tidak
            // tersedia di view otomatis dilewati oleh helper.

            setReadableText(
                '#pemohon',
                data.pemohon
            );

            setReadableText(
                '#nmsupplier',
                data.nmsupplier
            );

            setReadableText(
                '#kotasupplier',
                data.kotasupplier
            );

            setReadableText(
                '#alamatsupplier',
                data.alamatsupplier
            );

            setReadableText(
                '#alamatkirim',
                data.alamatkirim
            );

            setReadableText(
                '#noinvoice',
                data.noinvoice
            );

            setReadableDate(
                '#tglinvoice',
                data.tglinvoice
            );

            setReadableText(
                '#nosj',
                data.nosj
            );

            setReadableDate(
                '#tglsj',
                data.tglsj
            );

            setReadableText(
                '#noaju',
                data.noaju
            );

            setReadableText(
                '#nobl',
                data.nobl
            );

            setReadableText(
                '#noawb',
                data.noawb
            );

            setReadableText(
                '#noinvoicebea',
                data.noinvoicebea
            );

            setReadableDate(
                '#tglinvoicebea',
                data.tglinvoicebea
            );

            setReadableText(
                '#nobkrev',
                data.nobkrev
            );

            setReadableText(
                '#nofakturpajak',
                data.nofakturpajak
            );

            setReadableDate(
                '#senddate',
                data.senddate
            );

            setReadableText(
                '#idtax',
                data.idtax
            );

            setReadableNumber(
                '#kurs',
                data.kurs
            );

            setReadableNumber(
                '#jthtempo',
                data.jthtempo
            );

            // Tanggal jatuh tempo disimpan sebagai kolom header tersendiri.
            setReadableDate(
                '#tgljthtempo',
                data.tgljthtempo
            );

            if ($('#isinclusive').length) {
                $('#isinclusive').prop(
                    'checked',
                    $.trim(data.isinclusive || '').toUpperCase() === 'YES'
                );
            }

            setReadableNumber(
                '#dpp',
                data.dpp
            );

            setReadableNumber(
                '#jumlahpajak',
                data.jumlahpajak
            );

            setReadableNumber(
                '#total',
                data.total
            );

            setReadableNumber(
                '#beaimport',
                data.beaimport
            );

            setReadableNumber(
                '#ppnimport',
                data.ppnimport
            );

            setReadableNumber(
                '#pphimport',
                data.pphimport
            );

            setReadableNumber(
                '#biayaangkut',
                data.biayaangkut
            );

            setReadableNumber(
                '#biayaasuransi',
                data.biayaasuransi
            );

            setReadableNumber(
                '#biayalain',
                data.biayalain
            );

            setReadableNumber(
                '#totalestimasi',
                data.totalestimasi
            );

            setReadableNumber(
                '#balance',
                data.balance
            );

            // Field audit / status bila tersedia di view.
            setReadableText('#inputby', data.inputby);
            setReadableText('#inputdate', data.inputdate);
            setReadableText('#updateby', data.updateby);
            setReadableText('#updatedate', data.updatedate);
            setReadableText('#printby', data.printby);
            setReadableText('#printdate', data.printdate);
            setReadableText('#docnotmp', data.docnotmp);


            const docdateDisplay =
                dbDateToDisplay(data.docdate);

            if (docdateDisplay !== '') {
                $('#docdate').val(docdateDisplay);
            }

            const senddateDisplay =
                dbDateToDisplay(data.senddate);

            if (senddateDisplay !== '') {
                $('#senddate').val(senddateDisplay);
            }

            const dueDateDisplay =
                dbDateToDisplay(data.tgljthtempo);

            if (dueDateDisplay !== '') {
                $('#tgljthtempo')
                    .val(dueDateDisplay)
                    .prop('readonly', true)
                    .prop('disabled', false);
            }

            // Jatuh tempo
            setJtsValue(
                '[name="jthtempo"]',
                convertToDbNumber(data.jthtempo)
            );

            // Currency
            if ($.trim(data.currcode || '') !== '') {

                $.ajax({
                    type: 'GET',
                    url:
                        HOST_URL +
                        'api/globalmodule/list_currency?var=' +
                        encodeURIComponent(
                            $.trim(data.currcode)
                        ),
                    dataType: 'json'
                }).done(function (currencyResult) {

                    if (
                        currencyResult &&
                        currencyResult.items &&
                        currencyResult.items.length > 0
                    ) {

                        const currency =
                            currencyResult.items[0];

                        currency.kurs =
                            data.kurs;

                        const option =
                            new Option(
                                currency.currname,
                                currency.currcode,
                                true,
                                true
                            );

                        $(option).data(
                            'currency-data',
                            currency
                        );

                        $('#currcode')
                            .empty()
                            .append(option)
                            .val(currency.currcode)
                            .trigger('change');
                    }

                }).fail(function (xhr) {

                    console.error(
                        'Gagal mengambil currency:',
                        xhr.responseText
                    );
                });
            }

            // Kurs tetap editable
            setJtsValue(
                '[name="kurs"]',
                convertToDbNumber(data.kurs)
            );

            $('#kurs')
                .prop('readonly', false)
                .prop('disabled', false);

            // Tax
            if ($.trim(data.idtax || '') !== '') {

                $.ajax({
                    type: 'GET',
                    url:
                        HOST_URL +
                        'api/globalmodule/list_tax?var=' +
                        encodeURIComponent(
                            $.trim(data.idtax)
                        ),
                    dataType: 'json'
                }).done(function (taxResult) {

                    if (
                        taxResult &&
                        taxResult.items &&
                        taxResult.items.length > 0
                    ) {

                        const tax =
                            taxResult.items[0];

                        const option =
                            new Option(
                                tax.nmtax,
                                tax.idtax,
                                true,
                                true
                            );

                        $('#idtax')
                            .empty()
                            .append(option)
                            .val(tax.idtax)
                            .trigger('change');
                    }

                }).fail(function (xhr) {

                    console.error(
                        'Gagal mengambil tax:',
                        xhr.responseText
                    );
                });
            }

            // Inclusive
            $('#isinclusive').prop(
                'checked',
                $.trim(
                    data.isinclusive || ''
                ).toUpperCase() === 'YES'
            );

            // Keterangan
            $('#keterangan').val(
                data.keterangan || ''
            );

            // Nilai
            setJtsValue(
                '[name="dpp"]',
                convertToDbNumber(data.dpp)
            );

            setJtsValue(
                '[name="jumlahpajak"]',
                convertToDbNumber(data.jumlahpajak)
            );

            setJtsValue(
                '[name="total"]',
                convertToDbNumber(data.total)
            );

            // Checklist
            setTterimaChecklist(data);

            // Detail
            if (
                typeof loadTterimaDetail ===
                'function'
            ) {
                loadTterimaDetail(detail);
            }

            // Lock identity
            lockTterimaHeader();

            console.log(
                'Tanda Terima masuk mode UPDATE'
            );
        },

        complete: function () {
            $("#loadMe").modal("hide");
        },

        error: function (
            jqXHR,
            textStatus,
            errorThrown
        ) {

            console.error(
                'Gagal membaca temporary Tanda Terima:',
                textStatus,
                errorThrown,
                jqXHR.responseText
            );

            setTterimaInputMode();

            $("#loadMe").modal("hide");
        }
    });
}


// =============================================================
// CHECKLIST UI SYNC
// =============================================================
// prop('checked', true) tidak menjalankan event "change".
// Saat checklist diambil dari temporary/database, UI harus
// disinkronkan manual agar row menjadi hijau dan status menjadi OK.
function syncTterimaChecklistUI() {

    const $checkboxes = $('.cek-dokumen');

    if (!$checkboxes.length) {
        return;
    }

    const total = $checkboxes.length;
    const checked = $checkboxes.filter(':checked').length;

    // Jumlah dokumen
    $('#check_count').text(checked);

    // Sinkronkan setiap row checklist
    $checkboxes.each(function () {

        const id = $(this).attr('id');
        const $box = $('#box_' + id);

        if (!$box.length) {
            return;
        }

        const $status = $box.find('.document-check-status');

        if ($(this).is(':checked')) {

            $box.addClass('checked');

            $status
                .text('OK')
                .removeClass('text-muted')
                .addClass('text-success');

        } else {

            $box.removeClass('checked');

            $status
                .text('Belum')
                .removeClass('text-success')
                .addClass('text-muted');
        }
    });

    // Status keseluruhan
    const $badge = $('#lbl_check_status');

    if ($badge.length) {

        if (checked === 0) {

            $badge
                .removeClass('status-partial status-complete')
                .addClass('status-draft')
                .text('BELUM LENGKAP');

        } else if (checked < total) {

            $badge
                .removeClass('status-draft status-complete')
                .addClass('status-partial')
                .text('PARTIAL');

        } else {

            $badge
                .removeClass('status-draft status-partial')
                .addClass('status-complete')
                .text('LENGKAP');
        }
    }

    // Field status checking jika tersedia
    const $statusText = $('#checking_status_text');

    if ($statusText.length) {

        if (checked === 0) {

            $statusText.val('Belum dilakukan');

        } else if (checked < total) {

            $statusText.val(
                'Partial - ' + checked + ' dari ' + total + ' dokumen'
            );

        } else {

            $statusText.val(
                'Semua dokumen sudah diperiksa'
            );
        }
    }

    // Check All
    if ($('#checkAll').length) {

        $('#checkAll').prop(
            'checked',
            total > 0 && checked === total
        );
    }
}


// Klik manual checklist juga harus langsung mengubah UI.
$(document).off(
    'change.tterimaChecklist',
    '.cek-dokumen'
);

$(document).on(
    'change.tterimaChecklist',
    '.cek-dokumen',
    function () {
        syncTterimaChecklistUI();
    }
);


// =============================================================
// CHECKLIST + REFERENCE
// =============================================================
function setTterimaChecklist(data) {

    if (!data) {
        return;
    }

    function isChecked(value) {

        if (
            value === true ||
            value === 1 ||
            value === '1'
        ) {
            return true;
        }

        const val =
            String(value || '')
                .trim()
                .toLowerCase();

        return (
            val === 'true' ||
            val === 'yes' ||
            val === 'y' ||
            val === 't'
        );
    }

    $('#cekinvoice').prop(
        'checked',
        isChecked(data.cekinvoice)
    );

    $('#ceksj').prop(
        'checked',
        isChecked(data.ceksj)
    );

    $('#cekpenerimaan').prop(
        'checked',
        isChecked(data.cekpenerimaan)
    );

    $('#cekfakturpajak').prop(
        'checked',
        isChecked(data.cekfakturpajak)
    );

    $('#cekbeaimport').prop(
        'checked',
        isChecked(data.cekbeaimport)
    );

    $('#cekdokumen').prop(
        'checked',
        isChecked(data.cekdokumen)
    );

    $('#noinvoice').val(
        $.trim(data.noinvoice || '')
    );

    $('#nosj').val(
        $.trim(data.nosj || '')
    );

    $('#noaju').val(
        $.trim(data.noaju || '')
    );

    $('#nobl').val(
        $.trim(data.nobl || '')
    );

    $('#noawb').val(
        $.trim(data.noawb || '')
    );

    $('#nobkrev').val(
        $.trim(data.nobkrev || '')
    );

    $('#nofakturpajak').val(
        $.trim(data.nofakturpajak || '')
    );

    // Sinkronkan tampilan setelah nilai checkbox diisi dari database.
    syncTterimaChecklistUI();


    if ($('#checkAll').length) {

        const allChecked =
            $('#cekinvoice').is(':checked') &&
            $('#ceksj').is(':checked') &&
            $('#cekpenerimaan').is(':checked') &&
            $('#cekfakturpajak').is(':checked') &&
            $('#cekbeaimport').is(':checked') &&
            $('#cekdokumen').is(':checked');

        $('#checkAll').prop(
            'checked',
            allChecked
        );
    }
}


// =============================================================
// MODE INPUT
// =============================================================
function setTterimaInputMode() {

    console.log(
        'Tanda Terima masuk mode INPUT'
    );

    $('#idurut').val('');
    $('#status').val('');

    $('#cabang')
        .prop('disabled', false);

    $('#prefix, #infix, #suffix')
        .prop('readonly', false);

    $('#kdsupplier')
        .prop('disabled', false);

    $('#tgljthtempo')
        .prop('readonly', false)
        .prop('disabled', false);

    $('#alamatsupplier')
        .prop('readonly', false)
        .prop('disabled', false);

    $('#kurs')
        .prop('readonly', false)
        .prop('disabled', false);

    $('#senddate')
        .prop('disabled', false);

    $(
        '#cabang, #prefix, #infix, #suffix, ' +
        '#kdsupplier, #tgljthtempo, #alamatsupplier'
    ).removeClass('bg-light');

    if ($('#cabang').hasClass('select2-hidden-accessible')) {
        $('#cabang').trigger('change.select2');
    }

    if ($('#kdsupplier').hasClass('select2-hidden-accessible')) {
        $('#kdsupplier').trigger('change.select2');
    }
}


// =============================================================
// MODE UPDATE / LOCK IDENTITY
// =============================================================
function lockTterimaHeader() {

    console.log(
        'Lock identity header Tanda Terima'
    );

    $('#cabang')
        .prop('disabled', true)
        .addClass('bg-light');

    $('#prefix, #infix, #suffix')
        .prop('readonly', true)
        .addClass('bg-light');

    $('#kdsupplier')
        .prop('disabled', true)
        .addClass('bg-light');

    $('#tgljthtempo')
        .prop('readonly', true)
        .prop('disabled', false)
        .addClass('bg-light');

    $('#alamatsupplier')
        .prop('readonly', true)
        .prop('disabled', false)
        .addClass('bg-light');

    // Field lain tetap editable.
    $('#kurs')
        .prop('readonly', false)
        .prop('disabled', false);

    $('#jthtempo, #senddate, #currcode, #idtax, #isinclusive, #keterangan, #dpp, #jumlahpajak, #total')
        .prop('disabled', false);
}


/* FOR INPUT FUNCTION */


// ============================================================
// DOCUMENT STATUS
// ============================================================

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

// ============================================================
// DATATABLE TANDA TERIMA SUPPLIER
// Mengikuti pola DataTables existing seperti list_po()
// ============================================================

var tableTterimaTrx = null;

function initTableTterimaTrx() {

    // ============================================================
    // CEK TABLE
    // ============================================================

    if (!$('#tableTterimaTrx').length) {
        console.error(
            'Element #tableTterimaTrx tidak ditemukan.'
        );
        return;
    }


    // ============================================================
    // HINDARI DOUBLE INITIALIZATION
    // ============================================================

    if ($.fn.DataTable.isDataTable('#tableTterimaTrx')) {

        $('#tableTterimaTrx')
            .DataTable()
            .destroy();

        $('#tableTterimaTrx tbody').empty();
    }


    // ============================================================
    // INIT DATATABLE
    // ============================================================

    tableTterimaTrx = $('#tableTterimaTrx').DataTable({

        processing: true,
        serverSide: true,

        responsive: false,
        autoWidth: false,
        scrollX: false,

        pageLength: 10,

        order: [
            [3, 'desc']
        ],


        // ========================================================
        // AJAX
        // ========================================================

        ajax: {

            url: HOST_URL + 'arap/transaksi/listTterimaTrx',

            type: 'POST',


            data: function (d) {

                d.tglrange =
                    $('#tglrange').val() || '';

                d.status_filter =
                    $('#status_filter').val() || '';

                d.kdsupplier =
                    $('#kdsupplier').val() || '';

                d.noinvoice =
                    $('#noinvoice').val() || '';

            },


            // ====================================================
            // RESPONSE
            // ====================================================

            dataSrc: function (json) {

                console.log(
                    '=== RESPONSE TTERIMA ==='
                );

                console.log(json);


                // =================================================
                // RESPONSE DARI BACKEND ANDA:
                //
                // {
                //     dataTables: {
                //         draw: ...,
                //         recordsTotal: ...,
                //         recordsFiltered: ...,
                //         data: [...]
                //     }
                // }
                // =================================================

                if (
                    json &&
                    json.dataTables
                ) {

                    console.log(
                        'DataTables data:',
                        json.dataTables
                    );

                    console.log(
                        'Jumlah data:',
                        json.dataTables.data
                            ? json.dataTables.data.length
                            : 0
                    );


                    // PENTING:
                    // Karena response dibungkus dataTables,
                    // pindahkan properti DataTables ke root.

                    json.draw =
                        json.dataTables.draw;

                    json.recordsTotal =
                        json.dataTables.recordsTotal;

                    json.recordsFiltered =
                        json.dataTables.recordsFiltered;


                    return json.dataTables.data || [];
                }


                // =================================================
                // FALLBACK
                // Kalau suatu saat backend sudah mengirim:
                //
                // {
                //     draw: 1,
                //     recordsTotal: 3,
                //     recordsFiltered: 3,
                //     data: [...]
                // }
                // =================================================

                if (json && Array.isArray(json.data)) {

                    console.log(
                        'Response menggunakan format root data.'
                    );

                    return json.data;
                }


                console.error(
                    'Format response DataTables tidak dikenali:',
                    json
                );

                return [];
            },


            // ====================================================
            // ERROR
            // ====================================================

            error: function (xhr) {

                console.error(
                    'ERROR LIST TTERIMA:',
                    xhr.status,
                    xhr.responseText
                );

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengambil data Tanda Terima.'
                });

            }

        },


        // ========================================================
        // COLUMNS
        // ========================================================

        columns: [

            // ========================================================
            // 0 - NO
            // ========================================================
            {
                data: 0,
                orderable: false,
                searchable: false,
                className: 'text-center',
                width: '50px'
            },


            // ========================================================
            // 1 - ACTION
            // ========================================================
            {
                data: 1,
                orderable: false,
                searchable: false,
                className: 'text-center',
                width: '100px'
            },


            // ========================================================
            // 2 - DOCUMENT
            // ========================================================
            {
                data: 2,
                defaultContent: '',
                className: 'text-nowrap'
            },


            // ========================================================
            // 3 - TANGGAL
            // ========================================================
            {
                data: 3,
                defaultContent: '',
                className: 'text-center text-nowrap'
            },


            // ========================================================
            // 4 - NAMA SUPPLIER
            // ========================================================
            {
                data: 4,
                defaultContent: ''
            },


            // ========================================================
            // 5 - CURRENCY
            // ========================================================
            {
                data: 5,
                defaultContent: '',
                className: 'text-center'
            },


            // ========================================================
            // 6 - NILAI
            // ========================================================
            {
                data: 6,
                defaultContent: '',
                className: 'text-end'
            },


            // ========================================================
            // 7 - KETERANGAN
            // ========================================================
            {
                data: 7,
                defaultContent: ''
            },


            // ========================================================
            // 8 - STATUS DOKUMEN
            // ========================================================
            {
                data: 8,
                defaultContent: '',
                orderable: false,
                searchable: false,
                className: 'text-start'
            }

        ],

        // ========================================================
        // COLUMN DEFINITION
        // ========================================================

        columnDefs: [

            {
                targets: '_all',
                defaultContent: ''
            }

        ],


        // ========================================================
        // LANGUAGE
        // ========================================================

        language: {

            processing: 'Memproses...',

            search: 'Cari:',

            lengthMenu:
                'Tampilkan _MENU_ data',

            info:
                'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

            infoEmpty:
                'Menampilkan 0 sampai 0 dari 0 data',

            zeroRecords:
                'Data tidak ditemukan',

            emptyTable:
                'Belum ada data Tanda Terima',

            paginate: {

                first: 'Awal',

                last: 'Akhir',

                next: '›',

                previous: '‹'

            }

        }

    });

}

// ============================================================
// RELOAD TABLE
// ============================================================

function reload_tableTterimaTrx() {

    if (
        $.fn.DataTable.isDataTable(
            '#tableTterimaTrx'
        )
    ) {

        $('#tableTterimaTrx')
            .DataTable()
            .ajax.reload(
            null,
            false
        );

    } else {

        initTableTterimaTrx();

    }
}


// ============================================================
// INIT
// ============================================================

$(document).ready(function () {

    initTableTterimaTrx();

});

// ============================================================
// FORMAT NUMBER
// ============================================================

function formatTterimaNumber(data, type, row) {

    if (
        data === null ||
        data === undefined ||
        data === ''
    ) {
        return '';
    }

    const number = Number(
        String(data).replace(/,/g, '')
    );

    if (isNaN(number)) {
        return data;
    }

    return number.toLocaleString(
        'en-US',
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );
}


// ============================================================
// TRACEABILITY STATUS
// ============================================================

function getTterimaTraceStatus(row) {

    const documents = [

        row.cekinvoice,
        row.cekfakturpajak,
        row.ceksj,
        row.cekpenerimaan,
        row.cekbeaimport,
        row.cekdokumen

    ];

    const total = documents.length;

    const complete = documents.filter(function (value) {

        return (
            value === true ||
            value === 1 ||
            value === '1' ||
            String(value).toUpperCase() === 'Y' ||
            String(value).toUpperCase() === 'ADA' ||
            String(value).toUpperCase() === 'OK'
        );

    }).length;

    if (complete === total) {

        return {
            complete: complete,
            total: total,
            label: 'LENGKAP',
            class: 'success'
        };

    }

    if (complete === 0) {

        return {
            complete: 0,
            total: total,
            label: 'BELUM LENGKAP',
            class: 'danger'
        };

    }

    return {
        complete: complete,
        total: total,
        label: 'SEBAGIAN',
        class: 'warning'
    };
}


// ============================================================
// RELOAD
// ============================================================

function reload_tableTterimaTrx() {

    if (
        $.fn.DataTable.isDataTable(
            '#tableTterimaTrx'
        )
    ) {

        $('#tableTterimaTrx')
            .DataTable()
            .ajax.reload(null, false);

    } else {

        initTableTterimaTrx();

    }
}


// ============================================================
// ESCAPE HTML
// ============================================================

function escapeHtml(value) {

    if (value === null || value === undefined) {
        return '';
    }

    return $('<div>')
        .text(String(value))
        .html();
}


// ============================================================
// INIT
// ============================================================

$(document).ready(function () {

    initTableTterimaTrx();

});

$('#cabang').on('change', function () {
    if (skipRoleChange) return;

    let idbranch = $(this).val();

    if (idbranch) {
        $.ajax({
            url: HOST_URL + '/arap/transaksi/getBranchInfoTterima',
            method: 'GET',
            data: { idbranch: idbranch },
            dataType: 'json',

            success: function (res) {
                if (!res.success) {
                    Swal.fire('Error', res.message, 'warning');
                    return;
                }

                // =====================================================
                // DATA BRANCH
                // =====================================================
                currentKodeSuffix = res.kode_suffix;
                $('#infix').val(res.infix);
                var prefix = res.prefix;
                $('#prefix').val(prefix);
                loadNextSuffixTterima()
                defaultInitialPP = currentKodeSuffix;

                // =====================================================
                // DATE RANGE BERDASARKAN INFIX
                // =====================================================
                var infix = (res.infix || '').toString();

                if (infix.length === 4) {
                    $('#docdate').prop('disabled', false);
                    var yy = infix.substring(0, 2);
                    var mm = infix.substring(2, 4);
                    var year = 2000 + parseInt(yy, 10);
                    var month = parseInt(mm, 10) - 1;

                    // Default: logindate dari server, fallback ke hari ini
                    var logindate = res.logindate ? moment(res.logindate, 'DD-MM-YYYY') : moment();

                    var startDate = moment([year, month, 1]);
                    var endDate = moment(startDate).endOf('month');

                    // Kalau logindate di luar range, pakai startDate
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
                        $el.on('apply.daterangepicker', function (ev, picker) {
                            $(this).val(picker.startDate.format('DD-MM-YYYY'));
                            $(this).trigger('change');
                        });
                        $el.on('cancel.daterangepicker', function (ev, picker) {
                            $(this).val('');
                            $(this).trigger('change');
                        });
                    }

                    $el.val(selectedDate.format('DD-MM-YYYY'));
                }

                // =====================================================
                // GENERATE DOCNO
                // =====================================================
                $('#docno').val(
                    prefix + '/' + res.infix + '/' + currentKodeSuffix + '0001'
                );
                // =====================================================
                // ALAMAT KIRIM
                // =====================================================
                if (idbranch === '01.02') {
                    $('#alamatkirim').val('JL. MAYJEND SUNGKONO NO. 90 KEL. PRAMBANGAN KEC.KEBOMAS GRESIK.');
                } else {
                    $('#alamatkirim').val('JL. RAYA TAMAN NO. 1 RT.014 RW.003 TAMAN, TAMAN SIDOARJO');
                }


                // Ambil docdate dari field
                var docdate = $('#docdate').val() || '';

                // Load Currency dengan docdate
                if (res.currcode) {
                    // Kosongkan Select2 terlebih dahulu
                    $('[name="currcode"]').empty().trigger('change');

                    // Panggil API dengan docdate
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
                            // create the option and append to Select2
                            var currencyData = datax.items[0];


                            // create the option dan simpan data lengkap
                            var option = new Option(currencyData.currname, currencyData.currcode, true, true);
                            $(option).data('currency-data', currencyData); // Simpan data lengkap

                            $('[name="currcode"]').append(option).trigger('change');

                            // Set kurs
                            setJtsValue('[name="kurs"]', convertToDbNumber(currencyData.kurs || 1));
                            $('[name="kurs"]').prop('readonly', false);
                        }
                    }).fail(function() {
                        console.error('Failed to load currency data');
                    });
                }

                // Load Tax
                if (res.idtax) {
                    // Kosongkan Select2 terlebih dahulu
                    $('[name="idtax"]').empty().trigger('change');

                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_tax' + '?var=' + res.idtax,
                        dataType: 'json',
                        delay: 250,
                    }).then(function (datax) {
                        if (datax.items && datax.items.length > 0) {
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
                        }
                    }).fail(function() {
                        console.error('Failed to load tax data');
                    });
                }

            },

            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Gagal mengambil informasi cabang', 'error');
            }
        });
    }
});



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

    if (!e.params || !e.params.data) {
        return;
    }

    var selectedData = e.params.data;

    // ==============================
    // ALAMAT SUPPLIER
    // ==============================
    $("#alamatsupplier")
        .val(selectedData.alamat || '')
        .prop(
            'readonly',
            $.trim($('#status').val() || '') === 'E' &&
            $.trim($('#idurut').val() || '') !== ''
        )
        .prop('disabled', false);

    // ==============================
    // JATUH TEMPO SUPPLIER
    // ==============================
    var jthtempo = parseFloat(
        selectedData.jthtempo
    );

    if (isNaN(jthtempo)) {
        jthtempo = 0;
    }

    jthtempo = Math.round(jthtempo);

    // Set field
    $("#jthtempo").val(jthtempo);

    // ==============================
    // HITUNG TGL JATUH TEMPO
    // ==============================
    calculateTglJatuhTempo();

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

$('#prefix').on('blur', function () {
    loadNextSuffixTterima();
});


function cleanSuffix(value) {
    return String(value || '')
        .trim()
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, '')
        .substring(0, 6);
}



function setupEstpakai(prefix = '', existingValue = null) {

    const type =
        getPrefixType(prefix);

    const docDateValue =
        $.trim($('#docdate').val() || '');

    const options =
        generateDateOptions(
            type,
            docDateValue
        );

    if (
        $('#senddate').data(
            'daterangepicker'
        )
    ) {
        $('#senddate').daterangepicker(
            'destroy'
        );
    }

    let selectedValue =
        $.trim(
            existingValue || ''
        );

    let defaultDate = null;

    if (selectedValue !== '') {

        let parsed =
            moment(
                selectedValue,
                'DD-MM-YYYY',
                true
            );

        if (!parsed.isValid()) {
            parsed =
                moment(
                    selectedValue,
                    'YYYY-MM-DD',
                    true
                );
        }

        if (parsed.isValid()) {

            defaultDate = parsed;

            selectedValue =
                parsed.format(
                    'DD-MM-YYYY'
                );
        }
    }

    if (!defaultDate) {

        if (options.length > 0) {

            defaultDate =
                options[0].date;

            selectedValue =
                options[0].value;

        } else {

            defaultDate =
                moment();

            selectedValue =
                defaultDate.format(
                    'DD-MM-YYYY'
                );
        }
    }

    $('#senddate').daterangepicker({

        autoUpdateInput: false,

        singleDatePicker: true,

        showDropdowns: true,

        startDate: defaultDate,

        locale: {
            format: 'DD-MM-YYYY'
        },

        cancelLabel: 'Clear'
    });

    $('#senddate')
        .val(selectedValue);

    $('#senddate')
        .off(
            'apply.daterangepicker cancel.daterangepicker'
        );

    $('#senddate')
        .on(
            'apply.daterangepicker',
            function (
                ev,
                picker
            ) {

                const dateStr =
                    picker.startDate
                        .format(
                            'DD-MM-YYYY'
                        );

                $(this)
                    .val(dateStr);

                updateActiveButton(
                    dateStr
                );
            }
        );

    $('#senddate')
        .on(
            'cancel.daterangepicker',
            function () {

                $(this).val('');

                $('.senddate-btn')
                    .removeClass('active')
                    .css({
                        'background-color':
                            '#ffffff',
                        'color':
                            '#007bff'
                    });
            }
        );

    // =====================================================
    // BUTTON ESTIMASI PEMAKAIAN
    // =====================================================
    let buttonHtml = '';

    options.forEach(
        function (item) {

            const active =
                selectedValue === item.value
                    ? 'active'
                    : '';

            buttonHtml += `
<button
type="button"
class="btn btn-sm senddate-btn ${active}"
data-date="${item.value}"
onclick="selectEstpakai('${item.value}', this)"
style="
background-color: ${active ? '#007bff' : '#ffffff'};
color: ${active ? '#ffffff' : '#007bff'};
border: 1px solid #007bff;
margin-right: 4px;
margin-top: 4px;
"
>
${item.label}
</button>
`;
        }
    );

    if ($('#senddate_buttons').length === 0) {

        $('#senddate').after(
            '<div id="senddate_buttons">' +
            buttonHtml +
            '</div>'
        );

    } else {

        $('#senddate_buttons')
            .html(buttonHtml);
    }

    updateActiveButton(
        selectedValue
    );
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
    // loadDefaultCurrency('IDR');

    // setJtsValue(
    //     '[name="kurs"]',
    //     convertToDbNumber(1)
    // );

    // $('[name="kurs"]').prop('readonly', true);

});
/*ubah currency end*/

function addDetailTterima() {

    // =========================================================
    // CEK HEADER TEMPORARY DARI DATABASE
    // =========================================================
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'arap/transaksi/showing_tmp_tterima',
        dataType: 'json',

        beforeSend: function () {
            $("#loadMe").modal({
                backdrop: "static",
                keyboard: false,
                show: true
            });
        },

        success: function (res) {

            if (
                !res ||
                res.status !== true ||
                !res.data ||
                !res.data.header
            ) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Header Belum Ada',
                    text: 'Simpan header Tanda Terima terlebih dahulu.'
                });
                return;
            }

            const header = res.data.header;

            const idurut = $.trim(
                header.idurut || ''
            );

            const docno = $.trim(
                header.docno || ''
            );

            const cabang = $.trim(
                header.cabang || ''
            );

            const kdsupplier = $.trim(
                header.kdsupplier || ''
            );

            const nmsupplier = $.trim(
                header.nmsupplier || ''
            );

            const status = $.trim(
                header.status || ''
            ).toUpperCase();

            // Header wajib status E
            if (status !== 'E') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Header Tidak Dapat Diubah',
                    text: 'Status header bukan E (editable).'
                });
                return;
            }

            // Validasi header
            if (idurut === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'ID Header Tidak Ada',
                    text: 'ID header Tanda Terima belum tersedia.'
                });
                return;
            }

            if (cabang === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cabang Belum Diisi',
                    text: 'Cabang / Job pada header wajib diisi.'
                });
                return;
            }

            if (kdsupplier === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Supplier Belum Diisi',
                    text: 'Supplier pada header wajib diisi.'
                });
                return;
            }

            // =================================================
            // RESET FORM DETAIL SAJA
            // JANGAN menyentuh #idurut / #docno HEADER
            // =================================================
            const form = $('#formTterimaDtl');

            if (form.length && form[0]) {
                form[0].reset();
            }

            $('#detail_idurut').val(idurut);
            $('#detail_uniqueid').val('');
            $('#detail_docno').val(docno);

            $('#detail_supplier').val(
                kdsupplier +
                (nmsupplier !== ''
                    ? ' - ' + nmsupplier
                    : '')
            );

            $('#detail_docno_display').val(docno);
            $('#detail_idurut_display').val(idurut);

            $('#nobukti').val('');
            $('#keterangan_dtl').val('');

            $('#dk')
                .val('D')
                .trigger('change');

            $('#nilai').val('0');

            $('#perkiraan')
                .val(null)
                .trigger('change');

            $('#costcenter')
                .val(null)
                .trigger('change');

            // =================================================
            // MODE ADD
            // =================================================
            $('#modalUpdateTterimaDtlLabel')
                .text('Tambah Detail Tanda Terima');

            $('#btnSaveTterimaDtl')
                .html(
                    '<i class="fa fa-save me-1"></i> Simpan'
                )
                .data('mode', 'add');

            const modalElement =
                document.getElementById(
                    'modalUpdateTterimaDtl'
                );

            if (modalElement) {
                const modal =
                    bootstrap.Modal.getOrCreateInstance(
                        modalElement
                    );

                modal.show();
            }
        },

        error: function (xhr) {

            console.error(
                'showing_tmp_tterima error:',
                xhr.responseText
            );

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal membaca header Tanda Terima.'
            });
        },

        complete: function () {
            $("#loadMe").modal("hide");
        }
    });
}

$('#costcenter').select2({
    dropdownParent: $('#modalUpdateTterimaDtl .modal-content'),
    width: '100%',
    placeholder: 'Pilih Cost / Profit Center',
    allowClear: true
});

$('#perkiraan').select2({
    dropdownParent: $('#modalUpdateTterimaDtl .modal-content'),
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
                results: data.items,
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

    console.log('PERKIRAAN:', data.idcoa);
    console.log('NAMA PERKIRAAN:', data.nmcoa);


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


    const total =
        toInteger($('#total').val());

    const totalAlokasi =
        toInteger($('#totalalokasi').val());


    let selisih = 0;
    let dk = 'D';


    // =====================================================
    // TOTAL MASIH LEBIH BESAR
    // KEKURANGAN = DEBIT
    // =====================================================

    if (total > totalAlokasi) {

        selisih =
            total - totalAlokasi;

        dk = 'D';
    }


        // =====================================================
        // TOTAL LEBIH KECIL
        // KELEBIHAN ALOKASI = KREDIT
    // =====================================================

    else if (total < totalAlokasi) {

        selisih =
            totalAlokasi - total;

        dk = 'K';
    }


        // =====================================================
        // SUDAH SAMA
    // =====================================================

    else {

        selisih = 0;
        dk = 'D';
    }


    // =====================================================
    // ISI NILAI DAN DK
    // =====================================================

    $('#nilai').val(
        tterimaFormatNumber(selisih)
    );

    $('#dk')
        .val(dk)
        .trigger('change');


    console.log('TOTAL:', total);
    console.log('TOTAL ALOKASI:', totalAlokasi);
    console.log('SELISIH:', selisih);
    console.log('DK:', dk);
});

$('#coabank').select2({
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
                results: data.items,
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

    $('#nmcoabank').val(
        $.trim(data.nmcoa || '')
    );

    console.log('COA:', data.idcoa);
    console.log('NAMA COA:', data.nmcoa);
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


function loadNextSuffixTterima() {

    let prefix = $.trim($('#prefix').val()).toUpperCase();
    let infix = $.trim($('#infix').val());


    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + 'arap/transaksi/getNextSuffixTterima',
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
            const existingSendDate =
                $.trim($('#senddate').val() || '');

            setupEstpakai(
                prefix,
                existingSendDate || null
            );
        }
    });
};

$('#prefix').on('blur', function () {
    loadNextSuffixTterima();
});



function formatDateTT(date) {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}-${month}-${year}`;
}


function calculateTglJatuhTempo() {

    let docdate = $.trim($('#docdate').val());
    let jthtempo = $.trim($('#jthtempo').val());

    console.log('DOC DATE:', docdate);
    console.log('JTH TEMPO:', jthtempo);

    if (docdate === '' || jthtempo === '') {
        $('#tgljthtempo').val('');
        return;
    }

    // Ambil angka saja
    jthtempo = parseInt(
        jthtempo.replace(/[^0-9]/g, ''),
        10
    );

    if (isNaN(jthtempo)) {
        $('#tgljthtempo').val('');
        return;
    }

    // ==========================================
    // PARSE DD-MM-YYYY
    // ==========================================
    let parts = docdate.split('-');

    if (parts.length !== 3) {
        $('#tgljthtempo').val('');
        return;
    }

    let day   = parseInt(parts[0], 10);
    let month = parseInt(parts[1], 10) - 1;
    let year  = parseInt(parts[2], 10);

    let tanggalTT = new Date(
        year,
        month,
        day
    );

    if (isNaN(tanggalTT.getTime())) {
        $('#tgljthtempo').val('');
        return;
    }

    // ==========================================
    // TANGGAL TT + JATUH TEMPO
    // ==========================================
    tanggalTT.setDate(
        tanggalTT.getDate() + jthtempo
    );

    // ==========================================
    // FORMAT DD-MM-YYYY
    // ==========================================
    let hasil =
        String(tanggalTT.getDate()).padStart(2, '0') +
        '-' +
        String(tanggalTT.getMonth() + 1).padStart(2, '0') +
        '-' +
        tanggalTT.getFullYear();

    console.log('HASIL:', hasil);

    $('#tgljthtempo').val(hasil);
}


// =============================================================
// SAVE HEADER TANDA TERIMA
// =============================================================
function saveTterimaHeader() {

    const form = $('#formTterima');

    if (!form.length) {
        console.error('Form #formTterima tidak ditemukan.');
        return;
    }


    // =====================================================
    // VALIDASI FIELD WAJIB
    // =====================================================

    const docno =
        $.trim($('[name="docno"]').val() || '');

    const docdate =
        $.trim($('[name="docdate"]').val() || '');

    const cabang =
        $.trim($('[name="cabang"]').val() || '');

    const kdsupplier =
        $.trim($('[name="kdsupplier"]').val() || '');

    const tgljthtempo =
        $.trim($('[name="tgljthtempo"]').val() || '');


    if (docno === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Nomor Tanda Terima belum terbentuk.'
        });
        return;
    }


    if (docdate === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Tanggal Tanda Terima belum diisi.'
        });
        return;
    }


    if (cabang === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Cabang belum dipilih.'
        });
        return;
    }


    if (kdsupplier === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Supplier belum dipilih.'
        });
        return;
    }


    if (tgljthtempo === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Tanggal jatuh tempo belum diisi.'
        });
        return;
    }


    // =====================================================
    // CHECKLIST
    // =====================================================

    const checklist = {

        cekinvoice:
            $('#cekinvoice').is(':checked') ? '1' : '0',

        ceksj:
            $('#ceksj').is(':checked') ? '1' : '0',

        cekpenerimaan:
            $('#cekpenerimaan').is(':checked') ? '1' : '0',

        cekfakturpajak:
            $('#cekfakturpajak').is(':checked') ? '1' : '0',

        cekbeaimport:
            $('#cekbeaimport').is(':checked') ? '1' : '0',

        cekdokumen:
            $('#cekdokumen').is(':checked') ? '1' : '0'
    };


    // =====================================================
    // FORM DATA
    // =====================================================

    const formData =
        new FormData(form[0]);


    // -----------------------------------------------------
    // Pastikan hidden field ikut terkirim
    // -----------------------------------------------------

    formData.set(
        'idurut',
        $('#idurut').val() || ''
    );

    formData.set(
        'docno',
        $('[name="docno"]').val() || ''
    );

    formData.set(
        'status',
        $('#status').val() || 'E'
    );


    // -----------------------------------------------------
    // SELECT2 DISABLED TETAP IKUT POST
    // -----------------------------------------------------
    // Field disabled tidak dikirim oleh browser/FormData.
    // Ambil value langsung dari Select2 lalu set manual ke FormData.
    formData.set(
        'cabang',
        $.trim($('#cabang').val() || '')
    );

    formData.set(
        'kdsupplier',
        $.trim($('#kdsupplier').val() || '')
    );

    formData.set(
        'coabank',
        $.trim($('#coabank').val() || '')
    );

    formData.set(
        'nmcoabank',
        $.trim($('#nmcoabank').val() || '')
    );
    if ($('#kdsupplier').hasClass('select2-hidden-accessible')) {
        const supplierData = $('#kdsupplier').select2('data');

        if (supplierData && supplierData.length > 0) {
            formData.set(
                'nmsupplier',
                $.trim(
                    supplierData[0].nmsupplier ||
                    supplierData[0].text ||
                    ''
                )
            );
        }
    }


    formData.set(
        'cekinvoice',
        $('#cekinvoice').is(':checked') ? '1' : '0'
    );

    formData.set(
        'ceksj',
        $('#ceksj').is(':checked') ? '1' : '0'
    );

    formData.set(
        'cekpenerimaan',
        $('#cekpenerimaan').is(':checked') ? '1' : '0'
    );

    formData.set(
        'cekfakturpajak',
        $('#cekfakturpajak').is(':checked') ? '1' : '0'
    );

    formData.set(
        'cekbeaimport',
        $('#cekbeaimport').is(':checked') ? '1' : '0'
    );

    formData.set(
        'cekdokumen',
        $('#cekdokumen').is(':checked') ? '1' : '0'
    );


    // =====================================================
    // CONVERT CHECKBOX INCLUSIVE
    // =====================================================

    formData.set(
        'isinclusive',
        $('#isinclusive').is(':checked')
            ? 'YES'
            : 'NO'
    );


    // =====================================================
    // LOADING
    // =====================================================

    $('#btnSimpanChecklist')
        .prop('disabled', true);

    $("#loadMe").modal({
        backdrop: "static",
        keyboard: false,
        show: true
    });


    // =====================================================
    // SAVE
    // =====================================================

    $.ajax({

        type: 'POST',

        url:
            HOST_URL +
            'arap/transaksi/saveTterima',

        data: formData,

        processData: false,

        contentType: false,

        dataType: 'json',

        success: function (res) {

            console.log(
                'saveTterima response:',
                res
            );


            // =================================================
            // GAGAL
            // =================================================

            if (!res || res.success !== true) {

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text:
                        res && res.message
                            ? res.message
                            : 'Gagal menyimpan Tanda Terima.'
                });

                return;
            }


            // =================================================
            // UPDATE HIDDEN FIELD
            // =================================================

            if (
                res.data &&
                res.data.idurut !== undefined
            ) {

                $('#idurut').val(
                    res.data.idurut
                );
            }


            if (
                res.data &&
                res.data.docno !== undefined
            ) {

                $('[name="docno"]').val(
                    res.data.docno
                );
            }


            $('#status').val(
                res.data.status || 'E'
            );


            // =================================================
            // SUCCESS
            // =================================================

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text:
                    res.message ||
                    'Header Tanda Terima berhasil disimpan.',
                timer: 1500,
                showConfirmButton: false
            });


            // =================================================
            // SET MODE UPDATE
            //
            // Setelah save pertama, baca kembali temporary
            // milik user dari server.
            // =================================================

            setTimeout(function () {

                documentReadable();

            }, 300);


            // =================================================
            // RELOAD DETAIL
            // =================================================

            reloadTterimaDetailFresh();
        },


        error: function (
            jqXHR,
            textStatus,
            errorThrown
        ) {

            console.error(
                'saveTterima error:',
                jqXHR.responseText
            );

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text:
                    'Terjadi kesalahan saat menyimpan Tanda Terima.'
            });
        },


        complete: function () {

            $('#btnSimpanChecklist')
                .prop('disabled', false);

            $("#loadMe").modal("hide");
        }
    });
}


// =============================================================
// DETAIL TANDA TERIMA
// CHECKBOX + ACTION TOOLBAR
// =============================================================

window.tterimaDetailRows = window.tterimaDetailRows || {};

function tterimaEscapeHtml(value) {

    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


function tterimaFormatNumber(value) {

    const n = Number(
        String(value ?? '')
            .replace(/,/g, '')
    );

    if (!Number.isFinite(n)) {
        return '0';
    }

    return n.toLocaleString(
        'en-US',
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );
}


// -------------------------------------------------------------
// TOTAL NILAI DETAIL
// -------------------------------------------------------------
function updateTterimaDetailTotal(detail) {

    const rows = Array.isArray(detail) ? detail : [];

    let total = 0;

    rows.forEach(function (row) {

        let nilai = row.nilai ?? 0;

        nilai = String(nilai)
            .trim()
            .replace(/,/g, '')
            .replace(/[^0-9.\-]/g, '');

        const numberValue = Number(nilai);

        if (!Number.isFinite(numberValue)) {
            return;
        }

        const dk = String(row.dk ?? '')
            .trim()
            .toUpperCase();

        if (dk === 'D') {
            total += numberValue;
        } else if (dk === 'K') {
            total -= numberValue;
        }
    });

    $('#totalalokasi').val(
        tterimaFormatNumber(total)
    );
}


// -------------------------------------------------------------
// RENDER LIST DETAIL
// -------------------------------------------------------------
function loadTterimaDetail(detail) {

    const rows =
        Array.isArray(detail)
            ? detail
            : [];

    const $body =
        $('#bodyTterimaDetail');

    if (!$body.length) {
        return;
    }

    window.tterimaDetailRows = {};

    $('#checkAll')
        .prop('checked', false)
        .prop('indeterminate', false);

    syncTterimaDetailActionButtons();

    if (rows.length === 0) {

        $body.html(`
<tr>
<td colspan="7"
class="allocation-empty text-center py-3">
    <i class="fa fa-box-open text-muted me-1"></i>
Belum ada detail Tanda Terima.
</td>
</tr>
`);

        $('#totalalokasi').val(
            tterimaFormatNumber(0)
        );

        return;
    }

    let html = '';

    rows.forEach(function (row) {

        const id =
            String(
                row.idurut ??
                row.idurut_detail ??
                ''
            ).trim();

        if (id === '') {
            return;
        }

        window.tterimaDetailRows[id] = row;

        const nobukti =
            tterimaEscapeHtml(
                String(row.nobukti ?? '').trim()
            );

        const noperkiraan =
            tterimaEscapeHtml(
                String(row.noperkiraan ?? '').trim()
            );

        const namaperkiraan =
            tterimaEscapeHtml(
                String(row.namaperkiraan ?? '').trim()
            );

        const keterangan =
            tterimaEscapeHtml(
                String(row.keterangan ?? '').trim()
            );

        const dk =
            String(row.dk ?? '')
                .trim()
                .toUpperCase();

        const costprofitcenter =
            tterimaEscapeHtml(
                String(
                    row.costprofitcenter ??
                    row.costcenter ??
                    ''
                ).trim()
            );

        const nilai =
            tterimaFormatNumber(
                row.nilai
            );

        html += `
<tr data-detail-id="${tterimaEscapeHtml(id)}">

    <td class="text-center">
    <input type="checkbox"
class="detail-row-check"
value="${tterimaEscapeHtml(id)}"
data-idurut="${tterimaEscapeHtml(id)}"
aria-label="Pilih detail ${tterimaEscapeHtml(id)}">
    </td>
<td>${nobukti || '-'}</td>
<td>
    <div>${tterimaEscapeHtml(noperkiraan)}</div>
    <small class="text-muted">${tterimaEscapeHtml(namaperkiraan)}</small>
</td>
<td>${keterangan || '-'}</td>
<td class="text-center">
    ${tterimaEscapeHtml(dk)}
</td>
<td>${costprofitcenter || '-'}</td>
<td class="text-end">${nilai}</td>

</tr>
`;
    });

    $body.html(html);

    // Hitung total dari seluruh nilai detail yang tampil di tabel
    updateTterimaDetailTotal(rows);

    syncTterimaDetailActionButtons();
}
$(document).off('click.tterimaDetailRow', '#tabtterimadtl tbody tr');
$(document).on('click.tterimaDetailRow', '#tabtterimadtl tbody tr', function (e) {

    // Checkbox asli dibiarkan menjalankan native checked/change.
    if ($(e.target).is('input[type="checkbox"]')) {
        return;
    }

    // Jangan toggle saat klik elemen interaktif.
    if ($(e.target).closest('button, a, select, .select2-container').length) {
        return;
    }

    const $checkbox = $(this).find('.detail-row-check').first();

    if (!$checkbox.length) {
        return;
    }

    // Klik body baris = toggle checkbox + jalankan change handler.
    $checkbox
        .prop('checked', !$checkbox.prop('checked'))
        .trigger('change');
});

$(document).off('change.tterimaDetailRow', '#tabtterimadtl .detail-row-check');
$(document).on('change.tterimaDetailRow', '#tabtterimadtl .detail-row-check', function () {
    syncTterimaDetailActionButtons();
});



// -------------------------------------------------------------
// SELECTED ROWS
// -------------------------------------------------------------
function getSelectedTterimaDetailIds() {

    const ids = [];

    $('#bodyTterimaDetail .detail-row-check:checked')
        .each(function () {

            const id =
                $.trim(
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


// -------------------------------------------------------------
// ENABLE / DISABLE ACTION
// -------------------------------------------------------------
function syncTterimaDetailActionButtons() {

    const selected =
        getSelectedTterimaDetailIds();

    const count =
        selected.length;

    $('#btnEditDetail')
        .prop('disabled', count !== 1);

    $('#btnDeleteDetail')
        .prop('disabled', count === 0);

    // Check All
    const total =
        $('#bodyTterimaDetail .detail-row-check')
            .length;

    const checked =
        $('#bodyTterimaDetail .detail-row-check:checked')
            .length;

    if ($('#checkAll').length) {

        $('#checkAll').prop(
            'checked',
            total > 0 &&
            checked === total
        );

        $('#checkAll').prop(
            'indeterminate',
            checked > 0 &&
            checked < total
        );
    }
}


// -------------------------------------------------------------
// CHECK ALL
// -------------------------------------------------------------
$(document).off(
    'change.tterimaDetailCheckAll',
    '#checkAll'
);

$(document).on(
    'change.tterimaDetailCheckAll',
    '#checkAll',
    function () {

        const checked =
            $(this).is(':checked');

        $('#bodyTterimaDetail .detail-row-check')
            .prop('checked', checked);

        syncTterimaDetailActionButtons();
    }
);


// -------------------------------------------------------------
// CHECK PER ROW
// -------------------------------------------------------------
$(document).off(
    'change.tterimaDetailRowCheck',
    '#bodyTterimaDetail .detail-row-check'
);

$(document).on(
    'change.tterimaDetailRowCheck',
    '#bodyTterimaDetail .detail-row-check',
    function () {

        syncTterimaDetailActionButtons();

    }
);


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

    $('#btnSaveTterimaDtl')
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
                        'arap/transaksi/deleteTterimaDetail',

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

function saveTterimaDetail() {

    const $form = $('#formTterimaDtl');
    const $btn  = $('#btnSaveTterimaDtl');

    if (!$form.length) {
        console.error('formTterimaDtl tidak ditemukan.');
        return;
    }

    // =====================================================
    // FUNGSI LANJUT SAVE SETELAH HEADER DITEMUKAN
    // =====================================================
    function processSave(idurutHeader, docno) {

        idurutHeader = $.trim(idurutHeader || '');
        docno        = $.trim(docno || '');

        if (idurutHeader === '') {

            Swal.fire({
                icon: 'warning',
                title: 'Header Tidak Ditemukan',
                text: 'Header Tanda Terima temporary tidak ditemukan.'
            });

            return;
        }

        if (docno === '') {

            Swal.fire({
                icon: 'warning',
                title: 'Nomor Tanda Terima Kosong',
                text: 'Nomor Tanda Terima tidak ditemukan pada header.'
            });

            return;
        }

        // =================================================
        // DETAIL ID
        // =================================================
        const idurutDetail = $.trim(
            $('#detail_uniqueid').val() || ''
        );

        // =================================================
        // NO BUKTI
        // =================================================
        const nobukti = $.trim(
            $('#nobukti').val() || ''
        ).toUpperCase();

        // =================================================
        // PERKIRAAN
        // =================================================
        const perkiraan = $.trim(
            $('#perkiraan').val() || ''
        ).toUpperCase();

        if (perkiraan === '') {

            Swal.fire({
                icon: 'warning',
                title: 'Perkiraan Belum Diisi',
                text: 'Perkiraan wajib dipilih.'
            });

            $('#perkiraan').select2('open');

            return;
        }

        // =================================================
        // NAMA PERKIRAAN
        // =================================================
        let namaperkiraan = '';

        try {

            const coaData =
                $('#perkiraan').select2('data');

            if (
                coaData &&
                coaData.length > 0
            ) {

                namaperkiraan =
                    $.trim(
                        coaData[0].nmcoa ||
                        coaData[0].text ||
                        ''
                    ).toUpperCase();
            }

        } catch (e) {

            console.warn(
                'Data perkiraan Select2 tidak dapat dibaca:',
                e
            );
        }

        // =================================================
        // KETERANGAN
        // =================================================
        const keterangan =
            $.trim(
                $('#keterangan_dtl').val() || ''
            ).toUpperCase();

        if (keterangan === '') {

            Swal.fire({
                icon: 'warning',
                title: 'Keterangan Belum Diisi',
                text: 'Keterangan detail wajib diisi.'
            });

            $('#keterangan_dtl').trigger('focus');

            return;
        }

        // =================================================
        // DK
        // =================================================
        const dk =
            $.trim(
                $('#dk').val() || 'D'
            ).toUpperCase();

        if (
            dk !== 'D' &&
            dk !== 'K'
        ) {

            Swal.fire({
                icon: 'warning',
                title: 'DK Tidak Valid',
                text: 'Debit / Kredit harus D atau K.'
            });

            return;
        }

        // =================================================
        // COST CENTER
        // =================================================
        const costcenter =
            $.trim(
                $('#costcenter').val() || ''
            ).toUpperCase();

        // =================================================
        // NILAI
        // =================================================
        let nilai =
            $('#nilai').val() || '0';

        if (
            typeof convertToDbNumber ===
            'function'
        ) {

            nilai =
                convertToDbNumber(nilai);

        } else {

            nilai = String(nilai)
                .trim()
                .replace(/,/g, '')
                .replace(
                    /[^0-9.\-]/g,
                    ''
                );

            if (
                nilai === '' ||
                nilai === '-' ||
                !isFinite(Number(nilai))
            ) {
                nilai = '0';
            }
        }

        // =================================================
        // SET HIDDEN
        // Penting untuk mode UPDATE
        // =================================================
        $('#detail_idurut').val(
            idurutHeader
        );

        $('#detail_docno').val(
            docno
        );

        // =================================================
        // FORM DATA
        // =================================================
        const formData =
            new FormData();

        formData.set(
            'idurut',
            idurutHeader
        );

        formData.set(
            'docno',
            docno
        );

        formData.set(
            'idurut_detail',
            idurutDetail
        );

        formData.set(
            'uniqueid',
            idurutDetail
        );

        formData.set(
            'nobukti',
            nobukti
        );

        formData.set(
            'perkiraan',
            perkiraan
        );

        formData.set(
            'namaperkiraan',
            namaperkiraan
        );

        formData.set(
            'dk',
            dk
        );

        formData.set(
            'costcenter',
            costcenter
        );

        formData.set(
            'keterangan_dtl',
            keterangan
        );

        formData.set(
            'nilai',
            nilai
        );

        // =================================================
        // MODE
        // =================================================
        const mode =
            $btn.data('mode') ||
            (
                idurutDetail !== ''
                    ? 'edit'
                    : 'add'
            );

        console.log(
            'SAVE DETAIL',
            {
                mode: mode,
                idurutHeader: idurutHeader,
                docno: docno,
                idurutDetail: idurutDetail
            }
        );

        // =================================================
        // LOADING
        // =================================================
        const oldHtml =
            $btn.html();

        $btn
            .prop('disabled', true)
            .html(
                '<i class="fa fa-spinner fa-spin me-1"></i> Menyimpan...'
            );

        // =================================================
        // AJAX SAVE DETAIL
        // =================================================
        $.ajax({

            url:
                HOST_URL +
                'arap/transaksi/saveTterimaDetail',

            type: 'POST',

            data: formData,

            processData: false,

            contentType: false,

            dataType: 'json',

            success: function (res) {

                console.log(
                    'saveTterimaDetail response:',
                    res
                );

                if (
                    !res ||
                    res.success !== true
                ) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text:
                            res &&
                            res.message
                                ? res.message
                                : 'Detail gagal disimpan.'
                    });

                    return;
                }

                // tidak udah ada popup berhasil
                // Swal.fire({
                //     icon: 'success',
                //     title: 'Berhasil',
                //     text:
                //         res.message ||
                //         (
                //             mode === 'edit'
                //                 ? 'Detail berhasil diupdate.'
                //                 : 'Detail berhasil ditambahkan.'
                //         ),
                //     timer: 1000,
                //     showConfirmButton: false
                // });

                // =================================================
                // TUTUP MODAL
                // =================================================
                const modalElement =
                    document.getElementById(
                        'modalUpdateTterimaDtl'
                    );

                if (modalElement) {

                    bootstrap.Modal
                        .getOrCreateInstance(
                            modalElement
                        )
                        .hide();
                }

                // =================================================
                // RESET DETAIL
                // =================================================
                $('#detail_uniqueid').val('');

                $('#detail_idurut').val(
                    idurutHeader
                );

                $('#detail_docno').val(
                    docno
                );

                $('#nobukti').val('');

                $('#keterangan_dtl').val('');

                $('#nilai').val('0');

                $('#perkiraan')
                    .val(null)
                    .trigger('change');

                $('#costcenter')
                    .val(null)
                    .trigger('change');

                $('#dk').val('D');

                // =================================================
                // RELOAD LIST DETAIL
                // =================================================
                reloadTterimaDetailFresh();
            },

            error: function (
                xhr
            ) {

                console.error(
                    'saveTterimaDetail error:',
                    xhr.responseText
                );

                let message =
                    'Terjadi kesalahan saat menyimpan detail.';

                try {

                    const response =
                        JSON.parse(
                            xhr.responseText
                        );

                    if (
                        response &&
                        response.message
                    ) {

                        message =
                            response.message;
                    }

                } catch (e) {}

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: message
                });
            },

            complete: function () {

                $btn
                    .prop('disabled', false)
                    .html(oldHtml);
            }
        });
    }

    // =====================================================
    // AMBIL HEADER DARI HIDDEN
    // atau fallback ke HEADER UTAMA
    // =====================================================
    let idurutHeader =
        $.trim(
            $('#detail_idurut').val() ||
            $('#idurut').val() ||
            ''
        );

    let docno =
        $.trim(
            $('#detail_docno').val() ||
            $('#docno').val() ||
            ''
        );

    // =====================================================
    // SUDAH ADA HEADER DI HALAMAN
    // =====================================================
    if (
        idurutHeader !== '' &&
        docno !== ''
    ) {

        processSave(
            idurutHeader,
            docno
        );

        return;
    }

    // =====================================================
    // FALLBACK:
    // BACA HEADER TEMPORARY DARI DATABASE
    // JANGAN SAVE HEADER LAGI
    // =====================================================
    $.ajax({

        type: 'GET',

        url:
            HOST_URL +
            'arap/transaksi/showing_tmp_tterima',

        dataType: 'json',

        success: function (res) {

            console.log(
                'Header temporary untuk detail:',
                res
            );

            if (
                !res ||
                res.status !== true ||
                !res.data ||
                !res.data.header
            ) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Header Tidak Ditemukan',
                    text:
                        'Header Tanda Terima temporary belum tersedia.'
                });

                return;
            }

            const header =
                res.data.header;

            idurutHeader =
                $.trim(
                    header.idurut || ''
                );

            docno =
                $.trim(
                    header.docno || ''
                );

            if (
                idurutHeader === '' ||
                docno === ''
            ) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Header Tidak Lengkap',
                    text:
                        'ID atau Nomor Tanda Terima tidak ditemukan.'
                });

                return;
            }

            // Simpan ke hidden detail
            $('#detail_idurut').val(
                idurutHeader
            );

            $('#detail_docno').val(
                docno
            );

            // Lanjut SAVE DETAIL
            processSave(
                idurutHeader,
                docno
            );
        },

        error: function (
            xhr
        ) {

            console.error(
                'showing_tmp_tterima error:',
                xhr.responseText
            );

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text:
                    'Gagal membaca header Tanda Terima temporary.'
            });
        }
    });
}

function reloadTterimaDetailFresh() {

    $.ajax({

        type: 'GET',

        url:
            HOST_URL +
            'arap/transaksi/showing_tmp_tterima',

        data: {
            _t: Date.now()
        },

        dataType: 'json',

        cache: false,

        success: function (res) {

            console.log(
                'showing_tmp_tterima setelah delete:',
                res
            );

            if (
                !res ||
                res.status !== true ||
                !res.data
            ) {
                loadTterimaDetail([]);
                syncTterimaDetailActionButtons();
                return;
            }

            const detail =
                Array.isArray(res.data.detail)
                    ? res.data.detail
                    : [];

            loadTterimaDetail(detail);

            $('#checkAll')
                .prop('checked', false)
                .prop('indeterminate', false);

            syncTterimaDetailActionButtons();
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
// ============================================================
// DOCUMENT READY
// ============================================================

function finalTterima() {

    const idurut = $.trim(
        $('#idurut').val() || ''
    );

    const docno = $.trim(
        $('#docno').val() || ''
    );

    let totalHeader = $.trim(
        $('#total').val() || '0'
    );

    let totalAlokasi = $.trim(
        $('#totalalokasi').val() || '0'
    );


    // =====================================================
    // NORMALISASI ANGKA
    // =====================================================
    function toInteger(value) {

        value = String(value || '')
            .trim()
            .replace(/,/g, '')
            .replace(/[^0-9.\-]/g, '');

        const numberValue = Number(value);

        if (!Number.isFinite(numberValue)) {
            return 0;
        }

        return Math.round(numberValue);
    }


    const nilaiHeader =
        toInteger(totalHeader);

    const nilaiAlokasi =
        toInteger(totalAlokasi);


    // =====================================================
    // VALIDASI TOTAL ALOKASI
    // =====================================================
    if (nilaiAlokasi < 0) {

        Swal.fire({
            icon: 'warning',
            title: 'Tidak Valid',
            text: 'Total Alokasi tidak boleh negatif.'
        });

        return;
    }


    // =====================================================
    // VALIDASI BALANCE
    // =====================================================
    if (nilaiHeader !== nilaiAlokasi) {

        Swal.fire({
            icon: 'warning',
            title: 'Value Not Balance',
            html:
                'Total Tagihan : <b>' +
                tterimaFormatNumber(nilaiHeader) +
                '</b><br>' +
                'Total Alokasi : <b>' +
                tterimaFormatNumber(nilaiAlokasi) +
                '</b>'
        });

        return;
    }


    if (docno === '') {

        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Tersedia',
            text: 'Nomor Tanda Terima belum tersedia.'
        });

        return;
    }


    // =====================================================
    // KONFIRMASI
    // =====================================================
    Swal.fire({

        icon: 'question',

        title: 'Finalisasi Tanda Terima',

        text:
            'Tanda Terima akan difinalisasi dan tidak lagi berstatus draft.',

        showCancelButton: true,

        confirmButtonText: 'Ya, Finalisasi',

        cancelButtonText: 'Batal'

    }).then(function (result) {

        if (!result.isConfirmed) {
            return;
        }


        // =================================================
        // LOADING
        // =================================================
        $('#btnSimpan')
            .prop('disabled', true);


        // =================================================
        // POST FINAL
        // =================================================
        $.ajax({

            type: 'POST',

            url:
                HOST_URL +
                'arap/transaksi/finalTterima',

            dataType: 'json',

            data: {
                idurut: idurut,
                docno: docno
            },

            success: function (res) {

                console.log(
                    'finalTterima response:',
                    res
                );


                if (!res || res.success !== true) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Finalisasi',
                        text:
                            res && res.message
                                ? res.message
                                : 'Gagal melakukan finalisasi.'
                    });

                    return;
                }


                Swal.fire({

                    icon: 'success',

                    title: 'Berhasil',

                    text:
                        res.message ||
                        'Tanda Terima berhasil difinalisasi.',

                    timer: 1500,

                    showConfirmButton: false

                }).then(function () {

                    window.location.href =
                        HOST_URL +
                        'arap/transaksi/tterima';

                });

            },

            error: function (
                jqXHR,
                textStatus,
                errorThrown
            ) {

                console.error(
                    'finalTterima error:',
                    jqXHR.responseText
                );

                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text:
                        'Terjadi kesalahan saat finalisasi Tanda Terima.'
                });

            },

            complete: function () {

                $('#btnSimpan')
                    .prop('disabled', false);
            }
        });

    });
}


/* TARIK LPB */
function btnTarikPenerimaanTterima() {

    const docno = $.trim($('#docno').val() || '');
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
        text: 'Data penerimaan supplier akan dimasukkan ke detail Tanda Terima.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tarik',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: HOST_URL + 'arap/transaksi/tarikPenerimaanTterima',
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
                reloadTterimaDetailFresh();
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

function reloadTterimaDetail() {

    const docno = $.trim($('#docno').val() || '');

    if (docno === '') {
        return;
    }

    $.ajax({
        type: 'POST',
        url: HOST_URL + 'arap/transaksi/listTterimaDetail',
        data: {
            docno: docno
        },
        dataType: 'json',

        success: function (res) {

            console.log('RELOAD DETAIL:', res);

            if (!res || res.status !== true) {
                return;
            }

            loadTterimaDetail(
                Array.isArray(res.data)
                    ? res.data
                    : []
            );
        },

        error: function (xhr) {

            console.error(
                'RELOAD DETAIL ERROR:',
                xhr.responseText
            );
        }
    });
}


$(document).ready(function() {
    // Handle form submission event
    // Handle form submission event



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
