/* =========================================================================
 * Proses Tutup Bulan (Accounting Closing)
 * Endpoint: tools/proses/*
 * ========================================================================= */

function closingEsc(s) {
    return String(s === null || s === undefined ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function closingNum(v) {
    var n = parseFloat(v || 0);
    if (isNaN(n)) { n = 0; }
    return n.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function closingLoading() {
    Swal.fire({
        title: 'Memproses...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: function () { Swal.showLoading(); }
    });
}

function closingAlert(icon, title, html) {
    Swal.fire({ icon: icon, title: title, html: html, confirmButtonText: 'OK' });
}

function closingConfirm(title, html, confirmText, callback) {
    Swal.fire({
        title: title,
        html: html,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: 'Batal'
    }).then(function (result) {
        if (result.isConfirmed) { callback(); }
    });
}

/* Kirim POST ke endpoint closing dan tampilkan hasilnya */
function closingPost(url, data, reloadOnSuccess) {
    closingLoading();

    $.ajax({
        url: HOST_URL + url,
        type: 'POST',
        data: data || {},
        dataType: 'json'
    }).done(function (response) {
        if (response && response.success) {
            var msg = response.message || 'Proses berhasil.';
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                html: msg,
                confirmButtonText: 'OK'
            }).then(function () {
                if (reloadOnSuccess !== false) { location.reload(); }
            });
        } else {
            closingAlert('error', 'Gagal!', closingEsc((response && response.message) ? response.message : 'Terjadi kesalahan.'));
        }
    }).fail(function (xhr, status, error) {
        closingAlert('error', 'Error!', 'Terjadi kesalahan: ' + closingEsc(error || status));
    });
}

function closingIdurut() {
    var id = parseInt($('#closingIdurut').val(), 10);
    return isNaN(id) ? 0 : id;
}

function closingParam() {
    return {
        idurut: closingIdurut(),
        periode: $('#closingPeriodeInput').val() || $('#closingPeriode').val(),
        tglmulai: $('#closingTglMulai').val(),
        tglakhir: $('#closingTglAkhir').val(),
        approver: $('#closingApprover').val()
    };
}

/* =========================================================================
 * SELECT2 COA (master sc_mst.coa)
 * ========================================================================= */
function closingInitSelect2(selector, initial) {
    if (!$(selector).length) { return; }

    $(selector).select2({
        dropdownParent: $(document.body),
        placeholder: 'Type / Choose Perkiraan',
        allowClear: true,
        width: '100%',
        minimumInputLength: 2,
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
                    _perpage_: 2,
                    _paramglobal_: initial,
                    _parameterx_: initial,
                    term: params.term
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: { more: (params.page * 30) < data.total_count }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) { return markup; },
        templateResult: function (repo) {
            if (repo.loading) { return repo.text; }
            return "<div><b>" + closingEsc(repo.idcoa || repo.id || '') + "</b> - " + closingEsc(repo.nmcoa || '') + "</div>";
        },
        templateSelection: function (repo) {
            var id = repo.idcoa || repo.id || '';
            if (!id) { return repo.text || ''; }
            return id + ' - ' + (repo.nmcoa || '');
        }
    });
}

/* =========================================================================
 * 1. REGISTRASI PERIODE
 * ========================================================================= */
function closingRegisterPeriode() {
    var p = closingParam();
    if (!p.periode) { closingAlert('warning', 'Peringatan', 'Periode tidak boleh kosong.'); return; }

    closingConfirm('Konfirmasi', 'Registrasi/update periode <b>' + closingEsc(p.periode) + '</b>?',
        'Ya, Simpan!', function () {
            closingPost('tools/proses/registerClosingPeriod', p, true);
        });
}


/* =========================================================================
 * 2. DIAGNOSTIK
 * ========================================================================= */
function closingDiagnostik() {
    var id = closingIdurut();
    if (id <= 0) { closingAlert('warning', 'Peringatan', 'Periode belum diregistrasi. Jalankan Registrasi Periode terlebih dahulu.'); return; }

    closingLoading();

    $.ajax({
        url: HOST_URL + 'tools/proses/getClosingDiagnostics',
        type: 'POST',
        data: { idurut: id },
        dataType: 'json'
    }).done(function (response) {
        if (!response || !response.success) {
            closingAlert('error', 'Gagal!', closingEsc((response && response.message) ? response.message : 'Diagnostik gagal.'));
            return;
        }

        var rows = '';
        $.each(response.diagnostics || [], function (i, d) {
            var val = parseFloat(d.metric_value || 0);
            var warna = '';
            if (d.check_code === 'JOURNAL_UNBALANCED' || d.check_code === 'UNCLASSIFIED_COA' || d.check_code === 'PERIOD_NET') {
                warna = (val === 0) ? 'style="color:#0f5132;font-weight:700;"' : 'style="color:#842029;font-weight:700;"';
            }
            rows += '<tr>' +
                '<td><b>' + closingEsc(d.check_code) + '</b></td>' +
                '<td>' + closingEsc(d.metric_name) + '</td>' +
                '<td class="text-right" ' + warna + '>' + closingNum(d.metric_value) + '</td>' +
                '<td><small>' + closingEsc(d.detail) + '</small></td>' +
                '</tr>';
        });
        $('#tblDiagnostikBody').html(rows || '<tr><td colspan="4" class="text-center text-muted">Tidak ada data.</td></tr>');

        var tb = response.trialbalance || {};
        $('#tblTrialBalanceBody').html(
            '<tr><th style="width:60%;">Total Debet</th><td class="text-right">' + closingNum(tb.total_debet) + '</td></tr>' +
            '<tr><th>Total Kredit</th><td class="text-right">' + closingNum(tb.total_kredit) + '</td></tr>' +
            '<tr><th>Selisih (D - K)</th><td class="text-right" style="font-weight:700;">' + closingNum(tb.selisih) + '</td></tr>' +
            '<tr><th>Jurnal Tidak Balance</th><td class="text-right" style="font-weight:700;">' + closingEsc(tb.jurnal_tidak_balance || 0) + '</td></tr>'
        );

        var un = response.unclassified || [];
        if (un.length === 0) {
            $('#tblUnclassifiedBody').html('<tr><td colspan="5" class="text-center" style="color:#0f5132;">Semua COA transaksi sudah diklasifikasikan.</td></tr>');
        } else {
            var uh = '';
            $.each(un, function (i, u) {
                uh += '<tr>' +
                    '<td><b>' + closingEsc(u.idcoa) + '</b></td>' +
                    '<td>' + closingEsc(u.nmcoa) + '</td>' +
                    '<td class="text-right">' + closingNum(u.total_debet) + '</td>' +
                    '<td class="text-right">' + closingNum(u.total_kredit) + '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-xs btn-outline-success btnClosingQuickCoa" data-idcoa="' + closingEsc(u.idcoa) + '" data-kelas="PNL">PNL</button> ' +
                        '<button type="button" class="btn btn-xs btn-outline-primary btnClosingQuickCoa" data-idcoa="' + closingEsc(u.idcoa) + '" data-kelas="BS">BS</button>' +
                    '</td>' +
                    '</tr>';
            });
            $('#tblUnclassifiedBody').html(uh);
        }

        Swal.fire({
            icon: 'success',
            title: 'Diagnostik selesai',
            html: 'Periode <b>' + closingEsc((response.periode || {}).periode || '') + '</b><br>' +
                  'Debet: <b>' + closingNum(tb.total_debet) + '</b> | Kredit: <b>' + closingNum(tb.total_kredit) + '</b><br>' +
                  'Jurnal tidak balance: <b>' + closingEsc(tb.jurnal_tidak_balance || 0) + '</b>',
            confirmButtonText: 'OK'
        });
    }).fail(function (xhr, status, error) {
        closingAlert('error', 'Error!', 'Terjadi kesalahan: ' + closingEsc(error || status));
    });
}

/* =========================================================================
 * 3 - 4. TAHAPAN CLOSING (mulai / tutup / buka kembali / otomatis)
 * ========================================================================= */
function closingMulai() {
    var id = closingIdurut();
    if (id <= 0) { closingAlert('warning', 'Peringatan', 'Periode belum diregistrasi.'); return; }

    closingConfirm('Konfirmasi', 'Mulai pemeriksaan closing (status <b>CHECKING</b>) untuk periode ini?',
        'Ya, Lanjutkan!', function () {
            closingPost('tools/proses/startClosingPeriod', { idurut: id }, true);
        });
}

function closingTutupPeriode() {
    var p = closingParam();
    if (p.idurut <= 0) { closingAlert('warning', 'Peringatan', 'Periode belum diregistrasi.'); return; }

    closingConfirm('Konfirmasi Penutupan',
        'Tutup periode <b>' + closingEsc(p.periode) + '</b>?<br>' +
        '<small>Setelah ditutup (CLOSED / TUTUP), transaksi dan posting jurnal pada rentang tanggal periode ini akan ditolak.</small>',
        'Ya, Tutup Periode!', function () {
            closingPost('tools/proses/closeClosingPeriod', {
                idurut: p.idurut,
                approver: p.approver
            }, true);
        });
}

function closingProsesOtomatis() {
    var p = closingParam();
    if (!p.periode) { closingAlert('warning', 'Peringatan', 'Periode tidak boleh kosong.'); return; }

    closingConfirm('Konfirmasi Tutup Bulan',
        'Proses tutup bulan <b>otomatis</b> untuk periode <b>' + closingEsc(p.periode) + '</b><br>' +
        '<small>' + closingEsc(p.tglmulai) + ' s/d ' + closingEsc(p.tglakhir) + '</small><br><br>' +
        'Tahapan: registrasi &rarr; mulai closing &rarr; trial balance &rarr; <b>TUTUP</b>.<br>' +
        '<small>Approver: ' + closingEsc(p.approver) + ' (otomatis dari sesi login).</small>',
        'Ya, Proses Sekarang!', function () {
            closingPost('tools/proses/processTutupBulan', p, true);
        });
}

function closingBukaKembali() {
    var id = closingIdurut();
    if (id <= 0) { closingAlert('warning', 'Peringatan', 'Periode belum diregistrasi.'); return; }

    Swal.fire({
        title: 'Buka Kembali Periode',
        html: 'Periode akan berstatus <b>REOPENED</b> dan transaksi diizinkan kembali.<br><small>Alasan wajib diisi untuk audit.</small>',
        input: 'textarea',
        inputPlaceholder: 'Contoh: koreksi jurnal biaya bulan ini',
        inputAttributes: { rows: 3 },
        showCancelButton: true,
        confirmButtonText: 'Ya, Buka Kembali!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d97706',
        inputValidator: function (value) {
            if (!value || !value.trim()) { return 'Alasan buka kembali wajib diisi.'; }
        }
    }).then(function (result) {
        if (!result.isConfirmed) { return; }

        closingPost('tools/proses/reopenClosingPeriod', {
            idurut: id,
            reason: (result.value || '').trim()
        }, true);
    });
}

/* Alias kompatibilitas menu lama */
function tutupBulan() {
    closingProsesOtomatis();
}


/* =========================================================================
 * 8. KLASIFIKASI COA CLOSING (dipakai tombol "Klasifikasi Cepat")
 * ========================================================================= */
function closingSimpanCoaRule(idcoa, kelas, aktif, ket) {
    if (!idcoa) { closingAlert('warning', 'Peringatan', 'Pilih kode akun terlebih dahulu.'); return; }

    $.ajax({
        url: HOST_URL + 'tools/proses/saveClosingCoaRule',
        type: 'POST',
        data: {
            idcoa: idcoa,
            account_class: kelas,
            active: aktif,
            description: ket || ''
        },
        dataType: 'json'
    }).done(function (response) {
        if (response && response.success) {
            Swal.fire({
                icon: 'success',
                title: 'Tersimpan',
                text: response.message,
                timer: 1500,
                showConfirmButton: false
            }).then(function () { closingDiagnostik(); });
        } else {
            closingAlert('error', 'Gagal!', closingEsc((response && response.message) ? response.message : 'Terjadi kesalahan.'));
        }
    }).fail(function (xhr, status, error) {
        closingAlert('error', 'Error!', 'Terjadi kesalahan: ' + closingEsc(error || status));
    });
}

function closingKlasifikasiCepat(idcoa, kelas) {
    closingConfirm('Klasifikasi COA',
        'Klasifikasi akun <b>' + closingEsc(idcoa) + '</b> sebagai <b>' + closingEsc(kelas) + '</b> (aktif)?',
        'Ya, Simpan!', function () {
            closingSimpanCoaRule(idcoa, kelas, '1', '');
        });
}


/* =========================================================================
 * 10 - 11. JURNAL PENUTUP TAHUNAN
 * ========================================================================= */
function closingSiapkanJurnalPenutup() {
    var id = closingIdurut();
    var coa = $('#closingCoaLabaRugi').val();

    if (id <= 0) { closingAlert('warning', 'Peringatan', 'Periode belum diregistrasi.'); return; }
    if (!coa) { closingAlert('warning', 'Peringatan', 'COA laba/rugi (akun lawan) wajib dipilih.'); return; }

    closingConfirm('Konfirmasi Jurnal Penutup',
        'Buat jurnal penutup tahunan (DRAFT) untuk periode ini dengan akun lawan <b>' + closingEsc(coa) + '</b>?<br>' +
        '<small>Jurnal tidak langsung diposting; periksa detail lalu posting melalui menu jurnal.</small>',
        'Ya, Buat DRAFT!', function () {
            closingPost('tools/proses/prepareYearEndClosing', { idurut: id, coa: coa }, true);
        });
}

function closingYearEndDetail(jurnalId) {
    $('#modalClosingDetailBody').html('<div class="text-center text-muted">Memuat...</div>');
    $('#modalClosingDetail').modal('show');

    $.ajax({
        url: HOST_URL + 'tools/proses/getClosingYearEndJournal',
        type: 'POST',
        data: { jurnal_id: jurnalId },
        dataType: 'json'
    }).done(function (response) {
        if (!response || !response.success) {
            $('#modalClosingDetailBody').html('<div class="text-danger">' +
                closingEsc((response && response.message) ? response.message : 'Gagal memuat detail.') + '</div>');
            return;
        }

        var h = response.detail.header || {};
        var lines = response.detail.lines || [];

        var html = '<div class="mb-2"><b>Doc No:</b> ' + closingEsc(h.docno) +
            ' | <b>Tanggal:</b> ' + closingEsc(h.trxdate) +
            ' | <b>Status:</b> ' + closingEsc(h.status) + '</div>' +
            '<div class="table-responsive"><table class="table table-bordered table-sm">' +
            '<thead><tr>' +
            '<th style="width:60px;">Seq</th><th style="width:120px;">COA</th><th>Nama Akun</th>' +
            '<th style="width:140px; text-align:right;">Debet</th><th style="width:140px; text-align:right;">Kredit</th>' +
            '</tr></thead><tbody>';

        var td = 0, tk = 0;
        $.each(lines, function (i, l) {
            td += parseFloat(l.debet || 0);
            tk += parseFloat(l.kredit || 0);
            html += '<tr>' +
                '<td class="text-center">' + closingEsc(l.seq) + '</td>' +
                '<td>' + closingEsc(l.idcoa) + '</td>' +
                '<td>' + closingEsc(l.nmcoa) + '</td>' +
                '<td class="text-right">' + closingNum(l.debet) + '</td>' +
                '<td class="text-right">' + closingNum(l.kredit) + '</td>' +
                '</tr>';
        });

        html += '</tbody><tfoot><tr>' +
            '<th colspan="3" class="text-right">TOTAL</th>' +
            '<th class="text-right">' + closingNum(td) + '</th>' +
            '<th class="text-right">' + closingNum(tk) + '</th>' +
            '</tr></tfoot></table></div>';

        $('#modalClosingDetailBody').html(html);
    }).fail(function (xhr, status, error) {
        $('#modalClosingDetailBody').html('<div class="text-danger">Terjadi kesalahan: ' + closingEsc(error || status) + '</div>');
    });
}

/* =========================================================================
 * EVENT BINDING
 * ========================================================================= */
$(function () {
    $('#btnClosingRegister').on('click', function () { closingRegisterPeriode(); });
    $('#btnClosingDiagnostik').on('click', function () { closingDiagnostik(); });
    $('#btnClosingMulai').on('click', function () { closingMulai(); });
    $('#btnClosingTutup').on('click', function () { closingTutupPeriode(); });
    $('#btnClosingOtomatis').on('click', function () { closingProsesOtomatis(); });
    $('#btnClosingBuka').on('click', function () { closingBukaKembali(); });
    $('#btnClosingYearEnd').on('click', function () { closingSiapkanJurnalPenutup(); });

    $(document).on('click', '.btnClosingQuickCoa', function () {
        closingKlasifikasiCepat($(this).data('idcoa'), $(this).data('kelas'));
    });

    $(document).on('click', '.btnClosingYearEndDetail', function () {
        closingYearEndDetail($(this).data('id'));
    });
});

