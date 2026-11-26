/* ==================================================================
 * jup_add.js
 * Grid detail inline Jurnal Umum Perkiraan (JUP) - halaman addJUP.
 * Mengikuti pola grid inline pnm_brng_add.js:
 *   - baris bisa langsung diedit pada grid (tanpa modal)
 *   - navigasi keyboard (ENTER / TAB / ESC / Alt+N)
 *   - Select2 AJAX untuk kode perkiraan (Globalmodule::list_coa)
 *   - Select2 AJAX untuk cost center (Globalmodule::list_branchjob)
 *   - total debit / kredit / balance realtime
 *   - Simpan Final: validasi -> simpan semua baris -> submit header
 * ================================================================== */

var JUP_GRID_FIELDS = [
    '.jup-input-coa',
    '.jup-input-dk',
    '.jup-input-cabang',
    '.jup-input-remarks',
    '.jup-input-nilai'
];

var jupAddState = {
    saving: false,
    loadedDocno: null,
    totalDebit: 0,
    totalKredit: 0,
    balance: 0
};

/* ------------------------------------------------------------------ */
/* HELPER                                                              */
/* ------------------------------------------------------------------ */
function jupAddExists() {
    return document.getElementById('jupGrid') !== null;
}

function jupAddEsc(text) {
    return $('<div>').text(text === null || text === undefined ? '' : String(text)).html();
}

function jupAddNum(value) {
    if (typeof parseJUPNilai === 'function') {
        return parseJUPNilai(value);
    }

    return Number(String(value === null || value === undefined ? '' : value).replace(/,/g, '')) || 0;
}

function jupAddFmt(value, dec) {
    var n = jupAddNum(value);
    var d = (dec === null || dec === undefined) ? 2 : dec;

    return n.toLocaleString('en-US', {
        minimumFractionDigits: d,
        maximumFractionDigits: d
    });
}

function jupAddIsDebit(dk) {
    if (typeof isJUPdebit === 'function') return isJUPdebit(dk);
    return ['DEBIT', 'D', 'DR'].indexOf(String(dk || '').toUpperCase()) !== -1;
}

function jupAddIsKredit(dk) {
    if (typeof isJUPkredit === 'function') return isJUPkredit(dk);
    return ['KREDIT', 'K', 'CREDIT', 'CR'].indexOf(String(dk || '').toUpperCase()) !== -1;
}

function jupAddNotify(title, text, icon) {
    Swal.fire({
        icon: icon || 'warning',
        title: title,
        text: text || ''
    });
}

function jupAddToast(message, icon) {
    Swal.fire({
        toast: true,
        position: 'bottom-end',
        icon: icon || 'success',
        title: message,
        showConfirmButton: false,
        timer: 1400,
        timerProgressBar: true
    });
}

function jupAddXhrMessage(xhr) {
    if (xhr && xhr.responseText) {
        try {
            var res = JSON.parse(xhr.responseText);
            if (res && res.message) return res.message;
        } catch (e) {
            /* bukan JSON */
        }
    }

    return 'Terjadi kesalahan saat menghubungi server.';
}


/* ------------------------------------------------------------------ */
/* BARIS GRID                                                          */
/* ------------------------------------------------------------------ */
function jupFieldIndex($el) {
    for (var i = 0; i < JUP_GRID_FIELDS.length; i++) {
        if ($el.is(JUP_GRID_FIELDS[i])) return i;
    }

    return -1;
}

function jupFocusField($tr, idx, openSelect) {
    if (!$tr || !$tr.length) return;

    var sel = JUP_GRID_FIELDS[idx];
    if (!sel) return;

    var $f = $tr.find(sel).first();
    if (!$f.length) return;

    if (openSelect && $f.data('select2')) {
        $f.select2('open');
        return;
    }

    var el = $f[0];

    if (el && typeof el.focus === 'function') {
        el.focus();
    }

    if (el && el.tagName === 'INPUT' && el.type === 'text' && typeof el.select === 'function') {
        el.select();
    }
}

function jupRowEmptyState() {
    if (!jupAddExists()) return;

    if ($('#jupGridBody tr.jup-row').length === 0 && $('#jupGridBody tr.jup-empty-row').length === 0) {
        $('#jupGridBody').append(
            '<tr class="jup-empty-row">' +
                '<td colspan="8"><i class="fa fa-plus-circle"></i> Belum ada detail perkiraan. ' +
                'Klik di sini atau tekan <b>Alt+N</b> untuk menambah baris.</td>' +
            '</tr>'
        );
    }
}

function jupRowMarkup() {
    return '' +
        '<tr class="jup-row" data-idurut="">' +
            '<td class="jup-col-no"><input type="checkbox" class="jup-row-check" value=""></td>' +
            '<td class="jup-cell jup-cell-coa">' +
                '<select class="form-control jup-cell-select jup-input-coa" style="width:100%"></select>' +
            '</td>' +
            '<td class="jup-cell jup-cell-ellipsis">' +
                '<input type="text" class="form-control jup-cell-input jup-input-nmcoa" readonly tabindex="-1">' +
            '</td>' +
            '<td class="jup-cell jup-cell-dk">' +
                '<select class="form-control jup-cell-select jup-input-dk">' +
                    '<option value="">-</option>' +
                    '<option value="D">D</option>' +
                    '<option value="K">K</option>' +
                '</select>' +
            '</td>' +
            '<td class="jup-cell jup-cell-cabang">' +
                '<select class="form-control jup-cell-select jup-input-cabang" style="width:100%"></select>' +
            '</td>' +
            '<td class="jup-cell">' +
                '<input type="text" class="form-control jup-cell-input jup-input-remarks" ' +
                    'maxlength="255" placeholder="Keterangan" style="text-transform:uppercase;">' +
            '</td>' +
            '<td class="jup-cell jup-cell-nilai">' +
                '<input type="text" class="form-control jup-cell-input jtsseparator ratakanan jup-input-nilai" ' +
                    'placeholder="0.00">' +
            '</td>' +
            '<td class="jup-col-aksi">' +
                '<button type="button" class="jup-btn jup-btn-row-clear" tabindex="-1" title="Batalkan perubahan baris (Esc)">' +
                    '<i class="fa fa-eraser"></i>' +
                '</button>' +
                '<button type="button" class="jup-btn jup-btn-row-remove" tabindex="-1" title="Hapus baris">' +
                    '<i class="fa fa-trash"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';
}

function jupAddRow(data) {
    if (jupAddState.saving || !jupAddExists()) {
        return $();
    }

    $('#jupGridBody tr.jup-empty-row').remove();

    var d = data || {};
    var idurut = $.trim(String(d.idurut || ''));
    var nilai = (d.nilai === null || d.nilai === undefined || String(d.nilai) === '')
        ? ''
        : jupAddFmt(d.nilai, 2);

    var $tr = $(jupRowMarkup());

    $('#jupGridBody').append($tr);

    var idcoa = $.trim(String(d.idcoa || ''));
    var nmcoa = $.trim(String(d.nmcoa || ''));
    var cabang = $.trim(String(d.cabang || ''));
    var dk = $.trim(String(d.dk || '')).toUpperCase();
    var remarks = $.trim(String(d.remarks || ''));

    /* baris baru: cost center mengikuti cabang header */
    if (cabang === '') {
        cabang = $.trim(String($('#cabang').val() || '')).toUpperCase();
    }

    jupInitCoaSelect($tr, idcoa, nmcoa);
    jupInitCabangSelect($tr, cabang);

    $tr.find('.jup-input-dk').val(dk);
    $tr.find('.jup-input-remarks').val(remarks);
    $tr.find('.jup-input-nilai').val(nilai);

    if (idurut !== '') {
        $tr.attr('data-idurut', idurut);
        $tr.find('.jup-row-check').val(idurut);
        $tr.data('orig', {
            idcoa: idcoa,
            nmcoa: nmcoa,
            cabang: cabang,
            dk: dk,
            remarks: remarks,
            nilai: nilai
        });
    }

    jupRecalc();

    return $tr;
}

function jupRemoveRow($tr) {
    if (jupAddState.saving || !$tr || !$tr.length) return;

    $tr.find('select').each(function () {
        if ($(this).data('select2')) {
            $(this).select2('destroy');
        }
    });

    $tr.remove();

    jupRowEmptyState();
    jupSyncSelection();
    jupRecalc();
}

function jupCancelRow($tr) {
    if (jupAddState.saving || !$tr || !$tr.length) return;

    var orig = $tr.data('orig');

    if (orig) {
        $tr.find('.jup-input-coa').val(orig.idcoa || '').trigger('change.select2');
        $tr.find('.jup-input-nmcoa').val(orig.nmcoa || '');
        $tr.find('.jup-input-dk').val(orig.dk || '');
        $tr.find('.jup-input-cabang').val(orig.cabang || '').trigger('change.select2');
        $tr.find('.jup-input-remarks').val(orig.remarks || '');
        $tr.find('.jup-input-nilai').val(orig.nilai || '');
        $tr.removeClass('jup-row-invalid').find('.jup-invalid').removeClass('jup-invalid');
        jupRecalc();

        return;
    }

    jupRemoveRow($tr);

    if ($('#jupGridBody tr.jup-row').length === 0) {
        jupAddRow();
    }
}

function jupResetRows() {
    if (!jupAddExists()) return;

    $('#jupGridBody select').each(function () {
        if ($(this).data('select2')) {
            $(this).select2('destroy');
        }
    });

    $('#jupGridBody').empty();
    $('#jupCheckAll').prop('checked', false);

    jupSyncSelection();
    jupRecalc();
}

function jupCollectRows() {
    var rows = [];
    var cabangHeader = $.trim(String($('#cabang').val() || '')).toUpperCase();

    $('#jupGridBody tr.jup-row').each(function () {
        var $tr = $(this);
        var cabang = $.trim(String($tr.find('.jup-input-cabang').val() || '')).toUpperCase();

        rows.push({
            $tr: $tr,
            idurut: $.trim(String($tr.attr('data-idurut') || '')),
            idcoa: $.trim(String($tr.find('.jup-input-coa').val() || '')).toUpperCase(),
            nmcoa: $.trim(String($tr.find('.jup-input-nmcoa').val() || '')).toUpperCase(),
            dk: $.trim(String($tr.find('.jup-input-dk').val() || '')).toUpperCase(),
            cabang: cabang === '' ? cabangHeader : cabang,
            remarks: $.trim(String($tr.find('.jup-input-remarks').val() || '')).toUpperCase(),
            nilai: jupAddNum($tr.find('.jup-input-nilai').val())
        });
    });

    return rows;
}

function jupValidateRows(rows) {
    for (var i = 0; i < rows.length; i++) {
        var r = rows[i];
        var label = 'Baris ' + (i + 1);

        if (r.idcoa === '') {
            return { message: label + ': kode perkiraan belum dipilih.', $tr: r.$tr, field: '.jup-input-coa' };
        }

        if (r.nmcoa === '') {
            return {
                message: label + ': nama perkiraan kosong. Silakan pilih ulang kode perkiraan.',
                $tr: r.$tr,
                field: '.jup-input-coa'
            };
        }

        if (r.dk !== 'D' && r.dk !== 'K') {
            return { message: label + ': kolom Debit/Kredit belum dipilih.', $tr: r.$tr, field: '.jup-input-dk' };
        }

        if (r.cabang === '') {
            return {
                message: label + ': Cost / Profit Center belum diisi (pilih cabang header terlebih dahulu).',
                $tr: r.$tr,
                field: '.jup-input-cabang'
            };
        }

        if (r.remarks === '') {
            return { message: label + ': keterangan baris masih kosong.', $tr: r.$tr, field: '.jup-input-remarks' };
        }

        if (!(r.nilai > 0)) {
            return { message: label + ': nilai harus lebih besar dari 0.', $tr: r.$tr, field: '.jup-input-nilai' };
        }
    }

    return null;
}



/* ------------------------------------------------------------------ */
/* SELECT2 PER BARIS                                                   */
/* ------------------------------------------------------------------ */
function jupCoaTemplate(repo) {
    if (repo.loading) return repo.text;

    var idcoa = repo.idcoa || '';
    var nmcoa = repo.nmcoa || '';
    var level = (repo.level === null || repo.level === undefined) ? '' : repo.level;

    return "<div class='select2-result-repository__description'>" +
        jupAddEsc(idcoa) + "   <i class='fa fa-circle'></i>   " + jupAddEsc(nmcoa) +
        (level === '' ? '' : " <i class='fa fa-circle'></i> LEVEL " + jupAddEsc(level)) +
        '</div>';
}

function jupCoaSelection(repo) {
    return repo.nmcoa || repo.text;
}

function jupBranchTemplate(repo) {
    if (repo.loading) return repo.text;

    return "<div class='select2-result-repository__description'>" +
        jupAddEsc(repo.idbranch || '') + "   <i class='fa fa-circle-o'></i>   " +
        jupAddEsc(repo.nmbranch || '') + '</div>';
}

function jupBranchSelection(repo) {
    return repo.nmbranch || repo.text;
}

function jupInitCoaSelect($tr, idcoa, nmcoa) {
    var $sel = $tr.find('.jup-input-coa');

    if (idcoa) {
        $sel.append(new Option(nmcoa || idcoa, idcoa, true, true));
    }

    $sel.select2({
        placeholder: 'Ketik kode / nama perkiraan',
        allowClear: true,
        width: '100%',
        dropdownAutoWidth: true,
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_coa',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term || '',
                    _page_: params.page || 1,
                    _draw_: true,
                    _start_: 1,
                    _perpage_: 30,
                    _paramglobal_: '',
                    _parameterx_: '',
                    term: params.term || ''
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                var items = (data && data.items ? data.items : []).map(function (it) {
                    var kode = $.trim(String(it.idcoa || it.id || ''));
                    var nama = $.trim(String(it.nmcoa || ''));

                    return {
                        id: kode,
                        text: kode + ' - ' + nama,
                        idcoa: kode,
                        nmcoa: nama,
                        level: it.level
                    };
                });

                return {
                    results: items,
                    pagination: {
                        more: (params.page * 30) < ((data && data.total_count) || 0)
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: jupCoaTemplate,
        templateSelection: jupCoaSelection
    }).on('select2:select', function (e) {
        if (jupAddState.saving) return;

        var data = (e.params && e.params.data) || {};
        var $row = $(this).closest('tr.jup-row');
        var nama = $.trim(String(data.nmcoa || ''));

        $row.find('.jup-input-nmcoa').val(nama);
        $row.removeClass('jup-row-invalid');
        $row.find('.jup-input-coa').removeClass('jup-invalid');

        /* isi otomatis DK + nilai sisa balance bila nilai masih kosong */
        var $nilai = $row.find('.jup-input-nilai');
        var $dk = $row.find('.jup-input-dk');
        var nilaiSekarang = jupAddNum($nilai.val());
        var balanceSekarang = jupAddState.balance;

        if (nilaiSekarang === 0 && $.trim(String($dk.val() || '')) === '' && Math.abs(balanceSekarang) >= 0.005) {
            $dk.val(balanceSekarang < 0 ? 'D' : 'K');
            $nilai.val(jupAddFmt(Math.abs(balanceSekarang), 2));
        }

        jupRecalc();
        jupFocusField($row, 1);
    });
}

function jupInitCabangSelect($tr, cabang) {
    var $sel = $tr.find('.jup-input-cabang');

    if (cabang) {
        $sel.append(new Option(cabang, cabang, true, true));
    }

    $sel.select2({
        placeholder: 'Ketik / pilih cost center',
        allowClear: true,
        width: '100%',
        dropdownAutoWidth: true,
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_branchjob',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term || '',
                    _page_: params.page || 1,
                    _draw_: true,
                    _start_: 1,
                    _perpage_: 30,
                    _paramglobal_: '',
                    _parameterx_: '',
                    term: params.term || ''
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                var items = (data && data.items ? data.items : []).map(function (it) {
                    var idbranch = $.trim(String(it.idbranch || it.id || ''));
                    var nmbranch = $.trim(String(it.nmbranch || ''));

                    return {
                        id: idbranch,
                        text: nmbranch !== '' ? nmbranch : idbranch,
                        idbranch: idbranch,
                        nmbranch: nmbranch
                    };
                });

                return {
                    results: items,
                    pagination: {
                        more: (params.page * 30) < ((data && data.total_count) || 0)
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        templateResult: jupBranchTemplate,
        templateSelection: jupBranchSelection
    }).on('select2:select', function () {
        if (jupAddState.saving) return;

        var $row = $(this).closest('tr.jup-row');

        $row.removeClass('jup-row-invalid');
        $row.find('.jup-input-cabang').removeClass('jup-invalid');

        jupFocusField($row, 3);
    });
}


/* ------------------------------------------------------------------ */
/* TOTAL & SELEKSI                                                     */
/* ------------------------------------------------------------------ */
function jupRecalc() {
    if (!jupAddExists()) return;

    var totalDebit = 0;
    var totalKredit = 0;

    $('#jupGridBody tr.jup-row').each(function () {
        var $tr = $(this);
        var dk = $.trim(String($tr.find('.jup-input-dk').val() || '')).toUpperCase();
        var nilai = jupAddNum($tr.find('.jup-input-nilai').val());

        if (jupAddIsDebit(dk)) {
            totalDebit += nilai;
        } else if (jupAddIsKredit(dk)) {
            totalKredit += nilai;
        }
    });

    var balance = totalDebit - totalKredit;

    if (Math.abs(balance) < 0.005) {
        balance = 0;
    }

    jupAddState.totalDebit = totalDebit;
    jupAddState.totalKredit = totalKredit;
    jupAddState.balance = balance;

    $('#jupTotalDebit').text(jupAddFmt(totalDebit, 2));
    $('#jupTotalKredit').text(jupAddFmt(totalKredit, 2));
    $('#balance').val(jupAddFmt(balance, 2));

    var seimbang = balance === 0;

    $('#jupBalance')
        .text(jupAddFmt(balance, 2))
        .removeClass('jup-ok jup-not-ok')
        .addClass(seimbang ? 'jup-ok' : 'jup-not-ok');

    $('#jupBalanceState')
        .removeClass('jup-badge-ok jup-badge-notok')
        .addClass(seimbang ? 'jup-badge-ok' : 'jup-badge-notok')
        .html(seimbang
            ? '<i class="fa fa-check-circle"></i> BALANCE'
            : '<i class="fa fa-exclamation-triangle"></i> SELISIH ' + jupAddEsc(jupAddFmt(Math.abs(balance), 2)));
}

function jupSyncSelection() {
    if (!jupAddExists()) return;

    var total = $('#jupGridBody tr.jup-row').length;
    var checked = $('#jupGridBody .jup-row-check:checked').length;

    $('#jupSelectedInfo').text(checked + ' dari ' + total + ' baris dipilih');
    $('#jupCheckAll')
        .prop('checked', total > 0 && total === checked)
        .prop('disabled', total === 0 || jupAddState.saving);

    $('#btnAddJUPDetail').prop('disabled', jupAddState.saving);
    $('#btnDeleteJUPDetail').prop('disabled', checked === 0 || jupAddState.saving);
}

function jupSetSaving(flag) {
    jupAddState.saving = !!flag;

    $('#jupSavingOverlay').toggleClass('d-none', !flag);
    $('#jupGrid').toggleClass('jup-busy', !!flag);

    var $btn = $('#btnSimpanFinalJUP');

    if (flag) {
        $btn.data('labelAsli', $btn.html()).prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin me-1"></i> Menyimpan...');
    } else {
        $btn.prop('disabled', false);
        var label = $btn.data('labelAsli');

        if (label) $btn.html(label);
    }

    jupSyncSelection();
}

function jupCommitField($el) {
    if (!$el || !$el.length) return;

    if ($el.hasClass('jup-input-nilai')) {
        var raw = $.trim(String($el.val() || ''));

        if (raw !== '') {
            $el.val(jupAddFmt(raw, 2));
        }
    } else if ($el.hasClass('jup-input-remarks')) {
        $el.val($.trim(String($el.val() || '')).toUpperCase());
    }

    $el.removeClass('jup-invalid');
    $el.closest('tr.jup-row').removeClass('jup-row-invalid');

    jupRecalc();
}

/* ------------------------------------------------------------------ */
/* DOKUMEN & MUAT DRAFT                                                */
/* ------------------------------------------------------------------ */
function jupDraftDocNo() {
    var prefix = $.trim(String($('#prefix').val() || '')).toUpperCase();
    var infix = $.trim(String($('#infix').val() || '')).toUpperCase();
    var sufix = $.trim(String($('#sufix').val() || '')).toUpperCase();

    if (prefix !== '' && infix !== '' && sufix !== '') {
        return prefix + '/' + infix + '/' + sufix;
    }

    return $.trim(String($('#docno').val() || '')).toUpperCase();
}

function jupLoadDraft() {
    if (!jupAddExists()) return;

    var docno = jupDraftDocNo();

    if (docno === '') {
        jupAddState.loadedDocno = '';
        jupResetRows();
        jupAddRow();

        return;
    }

    $.ajax({
        url: HOST_URL + 'ka/accounting/getJUPDraftRows',
        type: 'POST',
        dataType: 'json',
        data: { docno: docno }
    }).done(function (res) {
        jupAddState.loadedDocno = docno;

        jupResetRows();

        if (res && res.success === true && res.rows && res.rows.length) {
            res.rows.forEach(function (row) {
                jupAddRow(row);
            });
        }

        if ($('#jupGridBody tr.jup-row').length === 0) {
            jupAddRow();
        }

        jupSyncSelection();
        jupRecalc();

        if (res && res.success === true) {
            $('#docno').val(res.docno || docno);
        }
    }).fail(function () {
        jupAddState.loadedDocno = docno;
        jupResetRows();
        jupAddRow();
    });
}

function jupSyncDraftDocno() {
    if (!jupAddExists() || jupAddState.saving) return;

    var docno = jupDraftDocNo();

    if (docno === jupAddState.loadedDocno) return;

    if (docno === '') return; // tunggu sampai nomor jurnal terbentuk

    jupLoadDraft();
}

function jupPersistRows(rows) {
    var payload = rows.map(function (r) {
        return {
            idcoa: r.idcoa,
            nmcoa: r.nmcoa,
            dk: r.dk,
            cabang: r.cabang,
            remarks: r.remarks,
            nilai: r.nilai
        };
    });

    return $.ajax({
        url: HOST_URL + 'ka/accounting/saveJUPDetailsBulk',
        type: 'POST',
        dataType: 'json',
        data: {
            docno: jupDraftDocNo(),
            docdate: $.trim(String($('#docdate').val() || '')),
            cabang: $.trim(String($('#cabang').val() || '')),
            keterangan: $.trim(String($('#keterangan').val() || '')),
            rows: JSON.stringify(payload)
        }
    });
}

/* ------------------------------------------------------------------ */
/* HAPUS BARIS TERPILIH                                                */
/* ------------------------------------------------------------------ */
function jupDeleteSelected() {
    if (!jupAddExists() || jupAddState.saving) return;

    var $rows = $('#jupGridBody tr.jup-row').filter(function () {
        return $(this).find('.jup-row-check').is(':checked');
    });

    if ($rows.length === 0) {
        jupAddNotify('Belum Ada Baris Dipilih', 'Centang baris detail yang ingin dihapus terlebih dahulu.', 'info');
        return;
    }

    Swal.fire({
        icon: 'warning',
        title: 'Hapus Baris Terpilih',
        html: 'Hapus <b>' + $rows.length + '</b> baris detail perkiraan yang dipilih?',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $rows.each(function () {
            jupRemoveRow($(this));
        });

        if ($('#jupGridBody tr.jup-row').length === 0) {
            jupAddRow();
        }

        jupSyncSelection();
        jupRecalc();
    });
}

/* ------------------------------------------------------------------ */
/* SIMPAN FINAL                                                        */
/* ------------------------------------------------------------------ */
function jupFocusError(err) {
    if (!err || !err.$tr || !err.$tr.length) return;

    err.$tr.addClass('jup-row-invalid');
    err.$tr.find(err.field).addClass('jup-invalid');

    var el = err.$tr[0];

    if (el && typeof el.scrollIntoView === 'function') {
        el.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }

    var $f = err.$tr.find(err.field).first();

    if ($f.length) {
        if ($f.data('select2')) {
            $f.select2('open');
        } else {
            $f.trigger('focus');
        }
    }
}

function jupAddFinalSubmit() {
    if (!jupAddExists() || jupAddState.saving) return false;

    var form = document.getElementById('formPO');

    if (!form) return false;

    var cabang = $.trim(String($('#cabang').val() || ''));
    var docdate = $.trim(String($('#docdate').val() || ''));
    var docno = jupDraftDocNo();

    if (cabang === '') {
        jupAddNotify('Cabang / Job Belum Dipilih', 'Pilih Cabang / Job terlebih dahulu sebelum menyimpan data.', 'warning');
        return false;
    }

    if (docdate === '') {
        jupAddNotify('Tanggal Belum Diisi', 'Isi tanggal jurnal terlebih dahulu.', 'warning');
        return false;
    }

    if (docno === '') {
        jupAddNotify('Nomor Jurnal Belum Terbentuk', 'Nomor jurnal JUP belum terbentuk. Periksa Cabang / Job yang dipilih.', 'warning');
        return false;
    }

    var rows = jupCollectRows();

    if (rows.length === 0) {
        jupAddNotify('Detail Masih Kosong', 'Tambahkan minimal satu baris detail perkiraan sebelum menyimpan final data.', 'warning');
        return false;
    }

    var err = jupValidateRows(rows);

    if (err) {
        jupFocusError(err);
        jupAddNotify('Data Belum Lengkap', err.message, 'warning');
        return false;
    }

    var balance = jupAddState.balance;

    if (Math.abs(balance) >= 0.005) {
        jupAddNotify(
            'Balance Tidak Nol',
            'Total Debit (' + jupAddFmt(jupAddState.totalDebit, 2) + ') dan Total Kredit (' +
                jupAddFmt(jupAddState.totalKredit, 2) + ') harus sama. Selisih: ' + jupAddFmt(Math.abs(balance), 2) + '.',
            'warning'
        );
        return false;
    }

    Swal.fire({
        icon: 'question',
        title: 'Simpan Final Data',
        html: 'Jurnal <b>' + jupAddEsc(docno) + '</b> dengan <b>' + rows.length +
            '</b> baris detail akan disimpan final.<br>Data yang sudah final tidak dapat diubah lagi. Lanjutkan?',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        jupSetSaving(true);

        jupPersistRows(rows).done(function (res) {
            if (!res || res.success !== true) {
                jupSetSaving(false);
                jupAddNotify('Gagal Menyimpan Detail', (res && res.message) || 'Detail JUP gagal disimpan.', 'error');
                return;
            }

            if (res.docno) {
                $('#docno').val(res.docno);
            }

            /* lolos validasi pada handler submit jup.js */
            window.jupFinalSubmitting = true;
            form.submit();
        }).fail(function (xhr) {
            jupSetSaving(false);
            jupAddNotify('Gagal Menyimpan Detail', jupAddXhrMessage(xhr), 'error');
        });
    });

    return false;
}

/* ------------------------------------------------------------------ */
/* EVENT GRID                                                          */
/* ------------------------------------------------------------------ */
function jupToggleRowSelect($check) {
    $check.closest('tr.jup-row').toggleClass('jup-row-selected', $check.is(':checked'));
    jupSyncSelection();
}

function jupMoveFocus($field, step) {
    var $tr = $field.closest('tr.jup-row');

    if (!$tr.length) return;

    var idx = jupFieldIndex($field);

    if (idx === -1) return;

    var next = idx + step;

    if (next >= 0 && next < JUP_GRID_FIELDS.length) {
        var isSelect = JUP_GRID_FIELDS[next] === '.jup-input-coa' || JUP_GRID_FIELDS[next] === '.jup-input-cabang';

        jupFocusField($tr, next, isSelect);
        return;
    }

    /* pindah baris */
    var $nextTr = step > 0 ? $tr.nextAll('tr.jup-row').first() : $tr.prevAll('tr.jup-row').first();

    if (!$nextTr.length && step > 0) {
        $nextTr = jupAddRow();
    }

    if ($nextTr && $nextTr.length) {
        jupFocusField($nextTr, step > 0 ? 0 : JUP_GRID_FIELDS.length - 1, step > 0);
    }
}

function jupAddThenFocus() {
    if (!jupAddExists() || jupAddState.saving) return;

    var $tr = jupAddRow();

    jupSyncSelection();

    if ($tr && $tr.length) {
        var el = $tr[0];

        if (el && typeof el.scrollIntoView === 'function') {
            el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }

        jupFocusField($tr, 0, true);
    }
}

function jupBindGridEvents() {
    if (!jupAddExists()) return;

    var $body = $('#jupGridBody');

    /* tambah / hapus baris */
    $(document).on('click', '#btnAddJUPDetail', function (e) {
        e.preventDefault();
        jupAddThenFocus();
    });

    $(document).on('click', '#btnDeleteJUPDetail', function (e) {
        e.preventDefault();
        jupDeleteSelected();
    });

    $(document).on('click', '.jup-btn-row-clear', function (e) {
        e.preventDefault();
        jupCancelRow($(this).closest('tr.jup-row'));
    });

    $(document).on('click', '.jup-btn-row-remove', function (e) {
        e.preventDefault();
        jupRemoveRow($(this).closest('tr.jup-row'));
        jupSyncSelection();
    });

    $(document).on('click', 'tr.jup-empty-row', function () {
        jupAddThenFocus();
    });

    /* checkbox baris & pilih semua */
    $body.on('change', '.jup-row-check', function () {
        jupToggleRowSelect($(this));
    });

    $(document).on('change', '#jupCheckAll', function () {
        var checked = $(this).is(':checked');

        $body.find('tr.jup-row').each(function () {
            $(this).find('.jup-row-check').prop('checked', checked);
            $(this).toggleClass('jup-row-selected', checked);
        });

        jupSyncSelection();
    });

    /* klik kolom nomor untuk memilih baris */
    $body.on('click', 'tr.jup-row td.jup-col-no', function (e) {
        if ($(e.target).is('.jup-row-check')) return;

        var $check = $(this).find('.jup-row-check');

        $check.prop('checked', !$check.is(':checked'));
        jupToggleRowSelect($check);
    });

    /* perubahan nilai field */
    $body.on('change', '.jup-input-coa, .jup-input-dk, .jup-input-cabang, .jup-input-nilai', function () {
        jupCommitField($(this));
        jupRecalc();
    });

    $body.on('blur', '.jup-input-nilai, .jup-input-remarks', function () {
        jupCommitField($(this));
        jupRecalc();
    });

    /* navigasi keyboard */
    $body.on('keydown', '.jup-cell-input, .jup-cell-select', function (e) {
        var key = e.which || e.keyCode;

        if (key === 13) { /* Enter */
            e.preventDefault();
            jupCommitField($(this));
            jupMoveFocus($(this), e.shiftKey ? -1 : 1);
            return;
        }

        if (key === 40) { /* panah bawah */
            var $trDown = $(this).closest('tr.jup-row').nextAll('tr.jup-row').first();

            if ($trDown.length) {
                e.preventDefault();
                jupFocusField($trDown, Math.max(jupFieldIndex($(this)), 0), false);
            }
        }

        if (key === 38) { /* panah atas */
            var $trUp = $(this).closest('tr.jup-row').prevAll('tr.jup-row').first();

            if ($trUp.length) {
                e.preventDefault();
                jupFocusField($trUp, Math.max(jupFieldIndex($(this)), 0), false);
            }
        }

        if (key === 27) { /* Esc: batalkan perubahan baris */
            e.preventDefault();
            jupCancelRow($(this).closest('tr.jup-row'));
            return;
        }

        if (key === 46 && e.ctrlKey) { /* Ctrl+Delete */
            e.preventDefault();
            jupDeleteSelected();
        }
    });

    $(document).on('keydown', function (e) {
        if (e.altKey && !e.ctrlKey && (e.which === 78)) { /* Alt+N */
            e.preventDefault();
            jupAddThenFocus();
        }
    });
}

/* ------------------------------------------------------------------ */
/* INISIALISASI                                                        */
/* ------------------------------------------------------------------ */
function jupGridHasContent() {
    var found = false;

    $('#jupGridBody tr.jup-row').each(function () {
        var $tr = $(this);

        if ($.trim(String($tr.attr('data-idurut') || '')) !== '' ||
            $.trim(String($tr.find('.jup-input-coa').val() || '')) !== '' ||
            $.trim(String($tr.find('.jup-input-remarks').val() || '')) !== '' ||
            jupAddNum($tr.find('.jup-input-nilai').val()) !== 0) {
            found = true;

            return false; // break
        }
    });

    return found;
}

function jupInitDocnoWatcher() {
    var last = jupDraftDocNo();

    setInterval(function () {
        if (!jupAddExists() || jupAddState.saving) return;

        var current = jupDraftDocNo();

        if (current === last) return;

        last = current;

        /* jangan buang input user yang belum disimpan */
        if (jupGridHasContent()) return;

        if (current !== jupAddState.loadedDocno) {
            jupLoadDraft();
        }
    }, 1500);
}

function jupAddInit() {
    if (!jupAddExists()) return;

    jupBindGridEvents();
    jupSyncSelection();
    jupLoadDraft();
    jupInitDocnoWatcher();
}

$(function () {
    jupAddInit();
});

/* ------------------------------------------------------------------ */
/* API PUBLIK (dipakai jup.js)                                         */
/* ------------------------------------------------------------------ */
window.JUPGrid = {
    exists: jupAddExists,
    reload: jupLoadDraft,
    recalc: jupRecalc,
    getTotals: function () {
        return {
            totalDebit: jupAddState.totalDebit,
            totalKredit: jupAddState.totalKredit,
            balance: jupAddState.balance
        };
    }
};


