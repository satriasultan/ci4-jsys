/* =========================================================
   LAPORAN PP / PO / VOID PO — report.js
   Dipakai oleh:
   - v_outspp, v_pphistory     (jenisLaporan: outstanding | history)
   - v_outspo, v_pohistory     (jenisLaporan: outstanding_po | history_po)
   - v_voidpo                  (jenisLaporan: void_po)
   ========================================================= */

/* =========================================================
   STATE
   ========================================================= */
var lapState = {
    page: 1,
    perpage: 25,
    totalPage: 0,
    total: 0,
    inited: false
};

/* =========================================================
   DETEKSI MODE (PP / PO / VOID PO)
   ========================================================= */
function getMode() {
    var jenis = $('#jenisLaporan').val() || '';


    /* ====== LPB ====== */
    if (jenis === 'lpb') {
        return {
            type:       'LPB',
            previewUrl: HOST_URL + 'report/trans/previewLaporanLPB',
            bodyId:     'tblPreviewLPBBody',
            colspan:    27
        };
    }
    if (jenis === 'void_po') {
        return {
            type:       'VPO',
            previewUrl: HOST_URL + 'report/trans/previewLaporanVoidPO',
            bodyId:     'tblPreviewVPOBody',
            colspan:    19
        };
    }
    if (jenis === 'outstanding_po' || jenis === 'history_po') {
        return {
            type:       'PO',
            previewUrl: HOST_URL + 'report/trans/previewLaporanPO',
            bodyId:     'tblPreviewPOBody',
            colspan:    25
        };
    }
    return {
        type:       'PP',
        previewUrl: HOST_URL + 'report/trans/previewLaporanPP',
        bodyId:     'tblPreviewPPBody',
        colspan:    15
    };
}

/* =========================================================
   TARIK DATA → AJAX preview
   ========================================================= */
function tarikData(page) {
    page = page || 1;
    var mode = getMode();

    var jenis    = $('#jenisLaporan').val();
    var tgl      = $('#lapTglRange').val();
    var docno    = $('#lapDocno').val();
    var idbarang = $('#lapIdbarang').val();
    var kdsupp   = $('#lapKdsupplier').val();
    var cabang   = $('#lapCabang').val();

    // Filter khusus: PP pakai idbarang, PO/VPO pakai supplier
    var filterKhusus = (mode.type === 'PP') ? idbarang : kdsupp;

    if (!tgl && !docno && !filterKhusus && !cabang) {
        Swal.fire({
            icon: 'warning',
            title: 'Filter Kosong',
            text: 'Minimal isi salah satu filter untuk menarik data.'
        });
        return;
    }

    lapState.perpage = parseInt($('#perpageSelect').val()) || 25;

    $('#btnTarikData').prop('disabled', true)
        .html('<i class="fa fa-spinner fa-spin"></i> Memuat...');
    $('#' + mode.bodyId).html(
        '<tr><td colspan="' + mode.colspan + '" class="text-center text-muted">Memuat data...</td></tr>'
    );

    $.ajax({
        url: mode.previewUrl,
        type: 'POST',
        dataType: 'json',
        data: {
            jenisLaporan: jenis,
            tglrange:     tgl,
            docno:        docno,
            idbarang:     idbarang,
            kdsupplier:   kdsupp,
            cabang:       cabang,
            page:         page,
            perpage:      lapState.perpage
        },
        success: function (res) {
            if (!res || res.status !== 'ok') {
                Swal.fire('Error', 'Gagal memuat data.', 'error');
                return;
            }
            lapState.page      = res.page;
            lapState.totalPage = res.total_page;
            lapState.total     = res.total;
            lapState.inited    = true;

            renderPreview(res.data, res.page, res.perpage, mode);
            renderPager(res.page, res.total_page);
            $('#previewInfo').text(
                'Total ' + res.total.toLocaleString('id-ID') + ' baris — ' +
                'halaman ' + res.page + ' dari ' + (res.total_page || 1)
            );

            $('#previewWrapper').slideDown(150);
            $('#btnCetakExcel').prop('disabled', false);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
        },
        complete: function () {
            $('#btnTarikData').prop('disabled', false)
                .html('<i class="fa fa-search"></i> Tarik Data');
        }
    });
}

/* =========================================================
   RENDER TABEL — PP / PO / VPO
   ========================================================= */
function renderPreview(rows, page, perpage, mode) {
    var bodyId = mode.bodyId;

    if (!rows || rows.length === 0) {
        $('#' + bodyId).html(
            '<tr><td colspan="' + mode.colspan + '" class="text-center text-muted">Tidak ada data sesuai filter.</td></tr>'
        );
        return;
    }

    var startNo = ((page - 1) * perpage) + 1;
    var html = '';

    rows.forEach(function (r, i) {
        
        if (mode.type === 'LPB') {
        /* ---------- LPB: 27 kolom ---------- */
        html += '<tr>' +
            '<td>' + (startNo + i) + '</td>' +
            '<td>' + escapeHtml(r.docno) + '</td>' +
            '<td>' + escapeHtml(r.docdate) + '</td>' +
            '<td>' + escapeHtml(r.cabang) + '</td>' +
            '<td>' + escapeHtml(r.kdsupplier) + '</td>' +
            '<td>' + escapeHtml(r.nmsupplier) + '</td>' +
            '<td>' + escapeHtml(r.alamatsupplier) + '</td>' +
            '<td>' + escapeHtml(r.pemohon) + '</td>' +
            '<td>' + escapeHtml(r.docnopo) + '</td>' +
            '<td>' + escapeHtml(r.idbarang) + '</td>' +
            '<td>' + escapeHtml(r.nmbarang) + '</td>' +
            '<td>' + escapeHtml(r.unit) + '</td>' +
            '<td class="text-right">' + fmtNum(r.qty) + '</td>' +
            '<td class="text-right">' + fmtNum(r.qtybonus) + '</td>' +
            '<td class="text-right">' + fmtNum(r.harga) + '</td>' +
            '<td class="text-right">' + fmtNum(r.multidisc) + '</td>' +
            '<td class="text-right">' + fmtNum(r.totaldiscount) + '</td>' +
            '<td class="text-right">' + fmtNum(r.nilai) + '</td>' +
            '<td class="text-right">' + fmtNum(r.nilaipajak) + '</td>' +
            '<td class="text-right">' + fmtNum(r.nilaikonversi) + '</td>' +
            '<td class="text-right">' + fmtNum(r.kurs) + '</td>' +
            '<td>' + escapeHtml(r.descriptionpo) + '</td>' +
            '<td>' + escapeHtml(r.descriptionpp) + '</td>' +
            '<td>' + escapeHtml(r.capexno) + '</td>' +
            '<td>' + escapeHtml(r.nofaktur) + '</td>' +
            '<td>' + escapeHtml(r.nosj) + '</td>' +
            '<td>' + escapeHtml(r.keterangan_lpb) + '</td>' +
        '</tr>';
    } else if (mode.type === 'VPO') {
            /* ---------- VOID PO: 19 kolom ---------- */
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.cabang) + '</td>' +
                '<td>' + escapeHtml(r.kdsupplier) + '</td>' +
                '<td>' + escapeHtml(r.nmsupplier) + '</td>' +
                '<td>' + escapeHtml(r.docnopo) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty) + '</td>' +
                '<td class="text-right">' + fmtNum(r.harga) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai) + '</td>' +
                '<td>' + escapeHtml(r.descriptionpo) + '</td>' +
                '<td>' + escapeHtml(r.descriptionpp) + '</td>' +
                '<td>' + escapeHtml(r.capexno) + '</td>' +
                // '<td>' + escapeHtml(r.status_vp) + '</td>' +
                '<td>' + escapeHtml(r.keterangan_vp) + '</td>' +
                '<td>' + escapeHtml(r.currcode) + '</td>' +
            '</tr>';
        } else if (mode.type === 'PO') {
            /* ---------- PO: 25 kolom ---------- */
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.cabang) + '</td>' +
                '<td>' + escapeHtml(r.kdsupplier) + '</td>' +
                '<td>' + escapeHtml(r.nmsupplier) + '</td>' +
                '<td>' + escapeHtml(r.pemohon) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qtybonus) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qtylpb) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qtyvoid) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty_proses) + '</td>' +
                '<td class="text-right">' + fmtNum(r.harga) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai) + '</td>' +
                '<td class="text-right">' + fmtNum(r.multidisc) + '</td>' +
                '<td class="text-right">' + fmtNum(r.totaldiscount) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilaipajak) + '</td>' +
                '<td>' + escapeHtml(r.descriptionpo) + '</td>' +
                '<td>' + escapeHtml(r.descriptionpp) + '</td>' +
                '<td>' + escapeHtml(r.status_dtl) + '</td>' +
                '<td>' + escapeHtml(r.status_po) + '</td>' +
                '<td>' + escapeHtml(r.keterangan_po) + '</td>' +
            '</tr>';
        } else {
            /* ---------- PP: 15 kolom ---------- */
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.cabang) + '</td>' +
                '<td>' + escapeHtml(r.pemohon) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qtypo) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qtyvoid) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty_proses) + '</td>' +
                '<td>' + escapeHtml(r.description) + '</td>' +
                '<td>' + escapeHtml(r.capexno) + '</td>' +
                '<td>' + escapeHtml(r.keterangan_pp) + '</td>' +
            '</tr>';
        }
    });

    $('#' + bodyId).html(html);
}

/* =========================================================
   RENDER PAGER
   ========================================================= */
function renderPager(page, totalPage) {
    var html = '';
    if (totalPage <= 1) {
        $('#pagerPP').html('');
        return;
    }
    var prevDis = (page <= 1) ? 'disabled' : '';
    var nextDis = (page >= totalPage) ? 'disabled' : '';

    html += '<li class="page-item ' + prevDis + '"><a class="page-link" href="javascript:void(0)" onclick="gotoPage(' + (page - 1) + ')">&laquo;</a></li>';

    var start = Math.max(1, page - 3);
    var end   = Math.min(totalPage, start + 6);
    start     = Math.max(1, end - 6);

    if (start > 1) {
        html += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="gotoPage(1)">1</a></li>';
        if (start > 2) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
    }
    for (var i = start; i <= end; i++) {
        var active = (i === page) ? 'active' : '';
        html += '<li class="page-item ' + active + '"><a class="page-link" href="javascript:void(0)" onclick="gotoPage(' + i + ')">' + i + '</a></li>';
    }
    if (end < totalPage) {
        if (end < totalPage - 1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
        html += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="gotoPage(' + totalPage + ')">' + totalPage + '</a></li>';
    }

    html += '<li class="page-item ' + nextDis + '"><a class="page-link" href="javascript:void(0)" onclick="gotoPage(' + (page + 1) + ')">&raquo;</a></li>';

    $('#pagerPP').html(html);
}

function gotoPage(p) {
    if (p < 1 || (lapState.totalPage > 0 && p > lapState.totalPage)) return;
    tarikData(p);
}

function changePerpage() {
    lapState.page = 1;
    if (lapState.inited) tarikData(1);
}

/* =========================================================
   HELPERS
   ========================================================= */
function escapeHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function fmtNum(v) {
    if (v === null || v === undefined || v === '') return '0';
    var n = parseFloat(v);
    if (isNaN(n)) return v;
    return n.toLocaleString('id-ID');
}

/* =========================================================
   SUBMIT EXCEL
   ========================================================= */
function submitLaporan() {
    if (!lapState.inited) {
        Swal.fire('Info', 'Silakan Tarik Data terlebih dahulu.', 'info');
        return false;
    }

    var jenis = $('#jenisLaporan').val();
    if (!jenis) {
        Swal.fire('Error', 'Jenis laporan tidak dikenal.', 'error');
        return false;
    }

    Swal.fire({
        icon: 'question',
        title: 'Cetak Excel?',
        html: 'Akan mengekspor <b>' + lapState.total.toLocaleString('id-ID') + '</b> baris sesuai filter.<br>Lanjutkan?',
        showCancelButton: true,
        confirmButtonText: 'Ya, Cetak',
        cancelButtonText: 'Batal'
    }).then(function (r) {
        if (!r.isConfirmed) return;
        $('#formLaporanParam').submit();
        Swal.fire({
            icon: 'success',
            title: 'Sedang mengunduh...',
            text: 'File Excel akan segera terunduh.',
            timer: 2000,
            showConfirmButton: false
        });
    });

    return false;
}

/* =========================================================
   RESET
   ========================================================= */
function resetLaporanParam() {
    var form = document.getElementById('formLaporanParam');
    if (form) form.reset();

    // Reset select2
    $('#lapIdbarang').val(null).trigger('change');
    $('#lapKdsupplier').val(null).trigger('change');
    $('#lapCabang').val(null).trigger('change');
    $('#lapTglRange').val('');
    $('#lapDocno').val('');

    // Reset state
    lapState = { page: 1, perpage: 25, totalPage: 0, total: 0, inited: false };

    var mode = getMode();
    $('#previewWrapper').hide();
    $('#' + mode.bodyId).html(
        '<tr><td colspan="' + mode.colspan + '" class="text-center text-muted">Belum ada data.</td></tr>'
    );
    $('#pagerPP').html('');
    $('#previewInfo').text('-');
    $('#btnCetakExcel').prop('disabled', true);
}

/* =========================================================
   INIT — Select2, Daterangepicker
   ========================================================= */
var laporanSelect2Init = false;

$(function () {
    if (laporanSelect2Init) return;
    laporanSelect2Init = true;

    var jenis = $('#jenisLaporan').val() || '';
    var isPO  = (jenis === 'outstanding_po' || jenis === 'history_po');
    var isLPB = (jenis === 'lpb');
    var isVPO = (jenis === 'void_po');
    var isPP  = !isPO && !isVPO && !isLPB;

    /* --- Daterangepicker --- */
    $('#lapTglRange').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear', format: 'DD-MM-YYYY' }
    });
    $('#lapTglRange').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(
            picker.startDate.format('DD-MM-YYYY') + ' - ' +
            picker.endDate.format('DD-MM-YYYY')
        );
    });
    $('#lapTglRange').on('cancel.daterangepicker', function () {
        $(this).val('');
    });

    /* =====================================================
       Select2 ID Barang — hanya untuk PP
       ===================================================== */
    if (isPP && $('#lapIdbarang').length) {
        var defaultInitialGroupBrng = '';
        $('#lapIdbarang').select2({
            dropdownParent: $(document.body),
            placeholder: "Choose Your Item List",
            allowClear: true,
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_item',
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
                        _paramglobal_: defaultInitialGroupBrng,
                        _parameterx_: defaultInitialGroupBrng,
                        term: params.term,
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
            templateResult: formatItem,
            templateSelection: formatItemSelection
        });
    }

    /* =====================================================
       Select2 Supplier — untuk PO & VOID PO
       ===================================================== */
    if ((isPO || isVPO || isLPB) && $('#lapKdsupplier').length) {
        var defaultInitialSupplier = '';
        $('#lapKdsupplier').select2({
            dropdownParent: $(document.body),
            placeholder: "Type / Choose Supplier",
            allowClear: true,
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_supplier_new',
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
                        _paramglobal_: defaultInitialSupplier,
                        _parameterx_: defaultInitialSupplier,
                        term: params.term,
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
            templateResult: formatSupplier,
            templateSelection: formatSupplierSelection
        });
    }

    /* =====================================================
       Select2 Cabang — untuk semua laporan
       ===================================================== */
    if ($('#lapCabang').length) {
        var defaultInitialBranch = '';
        $('#lapCabang').select2({
            dropdownParent: $(document.body),
            placeholder: "Type / Choose your Branch",
            allowClear: true,
            width: '100%',
            multiple: false,
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_branchjob',
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
                        _paramglobal_: defaultInitialBranch,
                        _parameterx_: defaultInitialBranch,
                        term: params.term,
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
            templateResult: formatBranch,
            templateSelection: formatBranchSelection
        });
    }
});

/* =========================================================
   FORMAT HELPER
   ========================================================= */
function formatItem(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
           repo.idbarang + "   <i class='fa fa-circle-o'></i>   " +
           repo.nmbarang + "</div>";
}
function formatItemSelection(repo) {
    return repo.idbarang || repo.text;
}

function formatBranch(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
           repo.idbranch + "   <i class='fa fa-circle-o'></i>   " +
           repo.nmbranch + "</div>";
}
function formatBranchSelection(repo) {
    return repo.nmbranch || repo.text;
}

function formatSupplier(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
           repo.kdsupplier + "   <i class='fa fa-circle-o'></i>   " +
           repo.nmsupplier + "</div>";
}
function formatSupplierSelection(repo) {
    return repo.nmsupplier || repo.text;
}