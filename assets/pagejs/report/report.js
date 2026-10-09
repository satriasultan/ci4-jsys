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
        if (jenis === 'history_po') {
            return {
                type:       'PO_HISTORY',
                previewUrl: HOST_URL + 'report/trans/previewLaporanPO',
                bodyId:     'tblPreviewPOBody',
                colspan:    17        // No + 16 data
            };
        }
        return {
            type:       'PO',
            previewUrl: HOST_URL + 'report/trans/previewLaporanPO',
            bodyId:     'tblPreviewPOBody',
            colspan:    25        // 25 kolom (sesuai aslinya)
        };
    }
    /* ====== PP ====== */
    if (jenis === 'history') {
        return {
            type:       'PP_HISTORY',
            previewUrl: HOST_URL + 'report/trans/previewLaporanPP',
            bodyId:     'tblPreviewPPBody',
            colspan:    17        // 17 kolom (No + 16 data history)
        };
    }
    /* ====== SALES ORDER ====== */
    if (jenis === 'salesorder') {
        return {
            type:       'SO',
            previewUrl: HOST_URL + 'report/trans/previewLaporanSalesOrder',
            bodyId:     'tblPreviewSOBody',
            colspan:    22   // No + 21 data
        };
    }

    /* ====== PENJUALAN ====== */
    if (jenis === 'penjualan') {
        return {
            type:       'PJ',
            previewUrl: HOST_URL + 'report/trans/previewLaporanPenjualan',
            bodyId:     'tblPreviewPJBody',
            colspan:    42   // No + 41 data
        };
    }

    /* ====== MASTER BARANG ====== */
    if (jenis === 'masterbarang') {
        return {
            type:       'MB',
            previewUrl: HOST_URL + 'report/trans/previewLaporanMasterBarang',
            bodyId:     'tblPreviewMBBody',
            colspan:    42   // No + 41 data
        };
    }

    if (jenis === 'analisamutasi') {
        return {
            type:       'AM',
            previewUrl: HOST_URL + 'report/trans/previewLaporanAnalisaMutasi',
            bodyId:     'tblPreviewAMBody',
            colspan:    16
        };
    }

    if (jenis === 'kartustock') {
        return {
            type:       'KS',
            previewUrl: HOST_URL + 'report/trans/previewLaporanKartuStock',
            bodyId:     'tblPreviewKSBody',
            colspan:    15
        };
    }

    if (jenis === 'posisibrg') {
        return {
            type:       'PB',
            previewUrl: HOST_URL + 'report/trans/previewLaporanPosisiBrg',
            bodyId:     'tblPreviewPBBody',
            colspan:    20
        };
    }

    if (jenis === 'mastersupplier') {
        return {
            type:       'MS',
            previewUrl: HOST_URL + 'report/trans/previewLaporanMasterSupplier',
            bodyId:     'tblPreviewMSBody',
            colspan:    16
        };
    }
    if (jenis === 'mastercustomer') {
        return {
            type:       'MC',
            previewUrl: HOST_URL + 'report/trans/previewLaporanMasterCustomer',
            bodyId:     'tblPreviewMCBody',
            colspan:    42
        };
    }
    if (jenis === 'poharian') {
        return {
            type:       'PH',
            previewUrl: HOST_URL + 'report/trans/previewLaporanPOHarian',
            bodyId:     'tblPreviewPHBody',
            colspan:    12
        };
    }

    if (jenis === 'umurhutang') {
        return {
            type:       'UH',
            previewUrl: HOST_URL + 'report/trans/previewLaporanUmurHutang',
            bodyId:     'tblPreviewUHBody',
            colspan:    23
        };
    }
    if (jenis === 'umurpiutang') {
        return {
            type:       'UP',
            previewUrl: HOST_URL + 'report/trans/previewLaporanUmurPiutang',
            bodyId:     'tblPreviewUPBody',
            colspan:    24
        };
    }

    if (jenis === 'posisihutang') {
        return {
            type: 'PSH',
            previewUrl: HOST_URL + 'report/trans/previewLaporanPosisiHutang',
            bodyId: 'tblPreviewPSHBody',
            colspan: 9
        };
    }
    if (jenis === 'posisipiutang') {
        return {
            type: 'PSP',
            previewUrl: HOST_URL + 'report/trans/previewLaporanPosisiPiutang',
            bodyId: 'tblPreviewPSPBody',
            colspan: 9
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

    var jenis      = $('#jenisLaporan').val();
    var tgl        = $('#lapTglRange').val();
    var docno      = $('#lapDocno').val();
    var idbarang   = $('#lapIdbarang').val();
    var kdsupp     = $('#lapKdsupplier').val();
    var cabang     = $('#lapCabang').val();
    var idlocation = $('#lapIdlocation').val();
    var kdcust     = $('#lapKdcustomer').val();
    

    var filterKhusus = (mode.type === 'PP') ? idbarang
                    : (mode.type === 'SO' || mode.type === 'PJ') ? kdcust
                    : (mode.type === 'MB' || mode.type === 'AM' || mode.type === 'KS' || mode.type === 'PB') ? idbarang
                    : (mode.type === 'MC' || mode.type === 'UP' || mode.type === 'PSP') ? kdcust
                    : (mode.type === 'MS' || mode.type === 'PH' || mode.type === 'UH' || mode.type === 'PSH') ? kdsupp
                    : kdsupp;

    var modeBolehKosong = (mode.type === 'MB' || mode.type === 'MS' || mode.type === 'MC');
    if (!modeBolehKosong && !tgl && !docno && !filterKhusus && !cabang) {
        Swal.fire({ icon: 'warning', title: 'Filter Kosong', text: 'Minimal isi salah satu filter.' });
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
            kdprk:     $('#lapKdprk').val(),
            interval:  $('#lapInterval').val() || 30,
            cabang:       cabang,
            kdcustomer:   kdcust,
            idlocation:   idlocation,
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
        } else if (mode.type === 'PO_HISTORY') {
            /* ---------- PO HISTORY: 17 kolom ---------- */
            var st = (r.status_outstanding || '').toUpperCase();
            var badge = 'secondary';
            if (st === 'FINISH')           badge = 'success';
            else if (st === 'OUTSTANDING') badge = 'warning';
            else if (st === 'CANCEL')      badge = 'danger';

            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docno_po) + '</td>' +
                '<td>' + escapeHtml(r.jurnal) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.kdsupplier) + '</td>' +
                '<td>' + escapeHtml(r.nmsupplier) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty_realisasi) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai) + '</td>' +
                '<td>' + escapeHtml(r.job) + '</td>' +
                '<td>' + escapeHtml(r.nama_job) + '</td>' +
                '<td class="text-right">' + fmtNum(r.harga_po) + '</td>' +
                '<td>' + escapeHtml(r.ket_item_pp) + '</td>' +
                '<td>' + escapeHtml(r.tgl_kirim) + '</td>' +
                '<td><span class="badge badge-' + badge + '">' + escapeHtml(st) + '</span></td>' +
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
        } else if (mode.type === 'PP_HISTORY') {
            /* ---------- PP HISTORY: 17 kolom ---------- */
            var st = (r.status_outstanding || '').toUpperCase();
            var badge = 'secondary';
            if (st === 'FINISH')          badge = 'success';
            else if (st === 'OUTSTANDING') badge = 'warning';
            else if (st === 'CANCEL')      badge = 'danger';

            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docno_pp) + '</td>' +
                '<td>' + escapeHtml(r.jurnal) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.userid) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty_realisasi) + '</td>' +
                '<td>' + escapeHtml(r.nama_user) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td>' + escapeHtml(r.spec) + '</td>' +
                '<td>' + escapeHtml(r.ket_pp_header) + '</td>' +
                '<td>' + escapeHtml(r.ket_detail) + '</td>' +
                '<td>' + escapeHtml(r.tglpakai) + '</td>' +
                '<td>' + escapeHtml(r.job) + '</td>' +
                '<td><span class="badge badge-' + badge + '">' + escapeHtml(st) + '</span></td>' +
            '</tr>';
        } else if (mode.type === 'SO') {
            /* ---------- SALES ORDER: 22 kolom ---------- */
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.kdcustomer) + '</td>' +
                '<td>' + escapeHtml(r.nmcustomer) + '</td>' +
                '<td>' + escapeHtml(r.alamatcustomer) + '</td>' +
                '<td>' + escapeHtml(r.desa) + '</td>' +
                '<td>' + escapeHtml(r.kecamatan) + '</td>' +
                '<td>' + escapeHtml(r.nmkota) + '</td>' +
                '<td>' + escapeHtml(r.kdsalesman) + '</td>' +
                '<td>' + escapeHtml(r.nmsalesman) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.harga) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilaibruto) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilaidisc) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilaipajak) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai) + '</td>' +
                '<td>' + escapeHtml(r.job) + '</td>' +
                '<td>' + escapeHtml(r.namajob) + '</td>' +
            '</tr>';
        } else if (mode.type === 'PJ') {
            /* ---------- PENJUALAN: 42 kolom ---------- */
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.tgljt) + '</td>' +
                '<td>' + escapeHtml(r.currcode) + '</td>' +
                '<td class="text-right">' + fmtNum(r.kurs) + '</td>' +
                '<td>' + escapeHtml(r.kdcustomer) + '</td>' +
                '<td>' + escapeHtml(r.nmcustomer) + '</td>' +
                '<td>' + escapeHtml(r.alamatcustomer) + '</td>' +
                '<td>' + escapeHtml(r.nmkota) + '</td>' +
                '<td>' + escapeHtml(r.kdsalesman) + '</td>' +
                '<td>' + escapeHtml(r.nmsalesman) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qtybonus) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilaibonus) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilaibruto) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilaidisc) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilaipajak) + '</td>' +
                '<td>' + escapeHtml(r.job) + '</td>' +
                '<td>' + escapeHtml(r.namajob) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td>' + escapeHtml(r.npwp) + '</td>' +
                '<td>' + escapeHtml(r.market) + '</td>' +
                '<td>' + escapeHtml(r.jenismarket) + '</td>' +
                '<td>' + escapeHtml(r.region) + '</td>' +
                '<td>' + escapeHtml(r.idgudang) + '</td>' +
                '<td>' + escapeHtml(r.nmgudang) + '</td>' +
                '<td>' + escapeHtml(r.idspec) + '</td>' +
                '<td>' + escapeHtml(r.expdate) + '</td>' +
                '<td>' + escapeHtml(r.keteranganbarang) + '</td>' +
                '<td>' + escapeHtml(r.specbarang) + '</td>' +
                '<td>' + escapeHtml(r.idprincipal) + '</td>' +
                '<td>' + escapeHtml(r.nmprincipal) + '</td>' +
                '<td>' + escapeHtml(r.golongan) + '</td>' +
                '<td>' + escapeHtml(r.jenisproduk) + '</td>' +
                '<td>' + escapeHtml(r.kelompokbarang) + '</td>' +
                '<td>' + escapeHtml(r.desa) + '</td>' +
                '<td>' + escapeHtml(r.kecamatan) + '</td>' +
                '<td>' + escapeHtml(r.wilayah) + '</td>' +
                '<td>' + escapeHtml(r.keteranganpenjualan) + '</td>' +
            '</tr>';
        } else if (mode.type === 'MB') {
            /* ---------- MASTER BARANG: 42 kolom ---------- */
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td class="text-right">' + fmtNum(r.minstock) + '</td>' +
                '<td class="text-right">' + fmtNum(r.ppersediaan) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td>' + escapeHtml(r.description) + '</td>' +
                '<td>' + escapeHtml(r.discontinue) + '</td>' +
                '<td>' + escapeHtml(r.inputby) + '</td>' +
                '<td>' + escapeHtml(r.inputdate) + '</td>' +
                '<td>' + escapeHtml(r.idgroup) + '</td>' +
                '<td class="text-right">' + fmtNum(r.berat) + '</td>' +
                '<td class="text-right">' + fmtNum(r.lsize) + '</td>' +
                '<td class="text-right">' + fmtNum(r.psize) + '</td>' +
                '<td class="text-right">' + fmtNum(r.tsize) + '</td>' +
                '<td>' + escapeHtml(r.idtype) + '</td>' +
                '<td>' + escapeHtml(r.idgolonganbarang) + '</td>' +
                '<td>' + escapeHtml(r.idjenisproduk) + '</td>' +
                '<td>' + escapeHtml(r.idkelompokbarang) + '</td>' +
                '<td>' + escapeHtml(r.launchingdate) + '</td>' +
                '<td>' + escapeHtml(r.deflocation) + '</td>' +
                '<td>' + escapeHtml(r.batch) + '</td>' +
                '<td>' + escapeHtml(r.idprincipal) + '</td>' +
                '<td class="text-right">' + fmtNum(r.batasexpdate) + '</td>' +
                '<td>' + escapeHtml(r.fastmoving) + '</td>' +
                '<td>' + escapeHtml(r.withserialno) + '</td>' +
                '<td>' + escapeHtml(r.prksuratjalan) + '</td>' +
                '<td class="text-right">' + fmtNum(r.volume) + '</td>' +
                '<td>' + escapeHtml(r.lokasireff) + '</td>' +
                '<td>' + escapeHtml(r.satuandinkes) + '</td>' +
                '<td>' + escapeHtml(r.konversidinkes) + '</td>' +
                '<td>' + escapeHtml(r.job) + '</td>' +
                '<td>' + escapeHtml(r.approval) + '</td>' +
                '<td>' + escapeHtml(r.tglapproved) + '</td>' +
                '<td>' + escapeHtml(r.approvedby) + '</td>' +
                '<td>' + escapeHtml(r.prkrevenue) + '</td>' +
                '<td>' + escapeHtml(r.prkhpp) + '</td>' +
                '<td>' + escapeHtml(r.prkproduksi) + '</td>' +
                '<td>' + escapeHtml(r.kategoribarang) + '</td>' +
                '<td class="text-right">' + fmtNum(r.gw) + '</td>' +
                '<td>' + escapeHtml(r.kdtax) + '</td>' +
                '<td>' + escapeHtml(r.satuantax) + '</td>' +
            '</tr>';
        } else if (mode.type === 'AM') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td>' + escapeHtml(r.trxdate) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.keterangan) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty_debet) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_debet) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty_kredit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_kredit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.saldo_qty_akhir) + '</td>' +
                '<td class="text-right">' + fmtNum(r.saldo_cost_akhir) + '</td>' +
                '<td>' + escapeHtml(r.job) + '</td>' +
                '<td>' + escapeHtml(r.principal) + '</td>' +
                '<td>' + escapeHtml(r.perkiraan) + '</td>' +
            '</tr>';
        } else if (mode.type === 'KS') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.idlocation) + '</td>' +
                '<td>' + escapeHtml(r.nmgudang) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td>' + escapeHtml(r.trxdate) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.keterangan) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty_debet) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty_kredit) + '</td>' +
                '<td>' + escapeHtml(r.job) + '</td>' +
                '<td>' + escapeHtml(r.principal) + '</td>' +
                '<td>' + escapeHtml(r.batch) + '</td>' +
                '<td>' + escapeHtml(r.expdate) + '</td>' +
            '</tr>';
        } else if (mode.type === 'PB') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.idbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td>' + escapeHtml(r.batch) + '</td>' +
                '<td>' + escapeHtml(r.expdate) + '</td>' +
                '<td>' + escapeHtml(r.idlocation) + '</td>' +
                '<td>' + escapeHtml(r.nmlocation) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.saldo_awal) + '</td>' +
                '<td class="text-right">' + fmtNum(r.debet) + '</td>' +
                '<td class="text-right">' + fmtNum(r.kredit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.saldo_akhir) + '</td>' +
                '<td>' + escapeHtml(r.job_kode) + '</td>' +
                '<td>' + escapeHtml(r.namajob) + '</td>' +
                '<td>' + escapeHtml(r.golongan) + '</td>' +
                '<td>' + escapeHtml(r.jenisproduk) + '</td>' +
                '<td>' + escapeHtml(r.kelompok) + '</td>' +
                '<td>' + escapeHtml(r.principal) + '</td>' +
                '<td>' + escapeHtml(r.keteranganbarang) + '</td>' +
                '<td>' + escapeHtml(r.bomdesc) + '</td>' +
            '</tr>';
        } else if (mode.type === 'MS') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.kdsupplier) + '</td>' +
                '<td>' + escapeHtml(r.nmsupplier) + '</td>' +
                '<td>' + escapeHtml(r.cp) + '</td>' +
                '<td>' + escapeHtml(r.phone) + '</td>' +
                '<td>' + escapeHtml(r.fax) + '</td>' +
                '<td>' + escapeHtml(r.alamat) + '</td>' +
                '<td>' + escapeHtml(r.kodepos) + '</td>' +
                '<td>' + escapeHtml(r.npwp) + '</td>' +
                '<td>' + escapeHtml(r.keterangan) + '</td>' +
                '<td>' + escapeHtml(r.nmkota) + '</td>' +
                '<td>' + escapeHtml(r.nmmarket) + '</td>' +
                '<td>' + escapeHtml(r.email) + '</td>' +
                '<td>' + escapeHtml(r.jabatan) + '</td>' +
                '<td>' + escapeHtml(r.npkp) + '</td>' +
                '<td class="text-right">' + fmtNum(r.jthtempo) + '</td>' +
            '</tr>';
        } else if (mode.type === 'MC') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.kdcustomer) + '</td>' +
                '<td>' + escapeHtml(r.nmcustomer) + '</td>' +
                '<td>' + escapeHtml(r.cp) + '</td>' +
                '<td>' + escapeHtml(r.phone) + '</td>' +
                '<td>' + escapeHtml(r.fax) + '</td>' +
                '<td>' + escapeHtml(r.alamat) + '</td>' +
                '<td>' + escapeHtml(r.kodepos) + '</td>' +
                '<td>' + escapeHtml(r.npwp) + '</td>' +
                '<td>' + escapeHtml(r.keterangan) + '</td>' +
                '<td class="text-right">' + fmtNum(r.plafon) + '</td>' +
                '<td>' + escapeHtml(r.nmkota) + '</td>' +
                '<td>' + escapeHtml(r.nmmarket) + '</td>' +
                '<td>' + escapeHtml(r.blacklist) + '</td>' +
                '<td>' + escapeHtml(r.namanpwp) + '</td>' +
                '<td>' + escapeHtml(r.alamatnpwp) + '</td>' +
                '<td>' + escapeHtml(r.nmkotanpwp) + '</td>' +
                '<td>' + escapeHtml(r.email) + '</td>' +
                '<td>' + escapeHtml(r.jabatan) + '</td>' +
                '<td>' + escapeHtml(r.npkp) + '</td>' +
                '<td>' + escapeHtml(r.grade) + '</td>' +
                '<td>' + escapeHtml(r.koderetur) + '</td>' +
                '<td>' + escapeHtml(r.salesman) + '</td>' +
                '<td>' + escapeHtml(r.kolektor) + '</td>' +
                '<td>' + escapeHtml(r.billto) + '</td>' +
                '<td>' + escapeHtml(r.sendto) + '</td>' +
                '<td>' + escapeHtml(r.statusfpj) + '</td>' +
                '<td class="text-right">' + fmtNum(r.jthtempo) + '</td>' +
                '<td>' + escapeHtml(r.jenisidpembeli) + '</td>' +
                '<td>' + escapeHtml(r.idtkupembeli) + '</td>' +
                '<td>' + escapeHtml(r.namapsa) + '</td>' +
                '<td>' + escapeHtml(r.emailpsa) + '</td>' +
                '<td>' + escapeHtml(r.telppsa) + '</td>' +
                '<td>' + escapeHtml(r.noijinpsa) + '</td>' +
                '<td>' + escapeHtml(r.namaapoteker) + '</td>' +
                '<td>' + escapeHtml(r.kodestra) + '</td>' +
                '<td>' + escapeHtml(r.nosipa) + '</td>' +
                '<td>' + escapeHtml(r.telpapoteker) + '</td>' +
                '<td>' + escapeHtml(r.emailapoteker) + '</td>' +
                '<td>' + escapeHtml(r.alamatapoteker) + '</td>' +
                '<td>' + escapeHtml(r.masaberlakusipa) + '</td>' +
                '<td>' + escapeHtml(r.masaberlakupsa) + '</td>' +
            '</tr>';
        } else if (mode.type === 'PH') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.nmbarang) + '</td>' +
                '<td>' + escapeHtml(r.nmsupplier) + '</td>' +
                '<td class="text-right">' + fmtNum(r.qty) + '</td>' +
                '<td>' + escapeHtml(r.unit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.harga) + '</td>' +
                '<td>' + escapeHtml(r.currcode) + '</td>' +
                '<td class="text-right">' + fmtNum(r.kurs) + '</td>' +
                '<td>' + escapeHtml(r.keterangan) + '</td>' +
                '<td>' + escapeHtml(r.senddate) + '</td>' +
            '</tr>';
        } else if (mode.type === 'UH') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.kdsupplier) + '</td>' +
                '<td>' + escapeHtml(r.nmsupplier) + '</td>' +
                '<td>' + escapeHtml(r.kdprk) + '</td>' +
                '<td>' + escapeHtml(r.nmprk) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.tgljt) + '</td>' +
                '<td>' + escapeHtml(r.dk) + '</td>' +
                '<td>' + escapeHtml(r.keterangan) + '</td>' +
                '<td>' + escapeHtml(r.currcode) + '</td>' +
                '<td class="text-right">' + fmtNum(r.kurs) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_belum_jt) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_jt_1) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_jt_2) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_jt_3) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_jt_4) + '</td>' +
                '<td class="text-right">' + fmtNum(r.umur) + '</td>' +
                '<td>' + escapeHtml(r.kodejob) + '</td>' +
                '<td>' + escapeHtml(r.namajob) + '</td>' +
                '<td>' + escapeHtml(r.alamatsupplier) + '</td>' +
                '<td>' + escapeHtml(r.kotasupplier) + '</td>' +
            '</tr>';
        } else if (mode.type === 'UP') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.docno) + '</td>' +
                '<td>' + escapeHtml(r.kdcustomer) + '</td>' +
                '<td>' + escapeHtml(r.nmcustomer) + '</td>' +
                '<td>' + escapeHtml(r.kdprk) + '</td>' +
                '<td>' + escapeHtml(r.nmprk) + '</td>' +
                '<td>' + escapeHtml(r.docdate) + '</td>' +
                '<td>' + escapeHtml(r.tgljt) + '</td>' +
                '<td>' + escapeHtml(r.dk) + '</td>' +
                '<td>' + escapeHtml(r.keterangan) + '</td>' +
                '<td>' + escapeHtml(r.currcode) + '</td>' +
                '<td class="text-right">' + fmtNum(r.kurs) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_belum_jt) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_jt_1) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_jt_2) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_jt_3) + '</td>' +
                '<td class="text-right">' + fmtNum(r.nilai_jt_4) + '</td>' +
                '<td class="text-right">' + fmtNum(r.umur) + '</td>' +
                '<td>' + escapeHtml(r.kodejob) + '</td>' +
                '<td>' + escapeHtml(r.namajob) + '</td>' +
                '<td>' + escapeHtml(r.alamatcustomer) + '</td>' +
                '<td>' + escapeHtml(r.kotacustomer) + '</td>' +
                '<td>' + escapeHtml(r.kdsalesman) + '</td>' +
                '<td>' + escapeHtml(r.nmsalesman) + '</td>' +
            '</tr>';
        } else if (mode.type === 'PSH') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.kdprk) + '</td>' +
                '<td>' + escapeHtml(r.nmprk) + '</td>' +
                '<td>' + escapeHtml(r.kdsupplier) + '</td>' +
                '<td>' + escapeHtml(r.nmsupplier) + '</td>' +
                '<td class="text-right">' + fmtNum(r.saldo_awal) + '</td>' +
                '<td class="text-right">' + fmtNum(r.debet) + '</td>' +
                '<td class="text-right">' + fmtNum(r.kredit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.saldo_akhir) + '</td>' +
            '</tr>';
        } else if (mode.type === 'PSP') {
            html += '<tr>' +
                '<td>' + (startNo + i) + '</td>' +
                '<td>' + escapeHtml(r.kdprk) + '</td>' +
                '<td>' + escapeHtml(r.nmprk) + '</td>' +
                '<td>' + escapeHtml(r.kdcustomer) + '</td>' +
                '<td>' + escapeHtml(r.nmcustomer) + '</td>' +
                '<td class="text-right">' + fmtNum(r.saldo_awal) + '</td>' +
                '<td class="text-right">' + fmtNum(r.debet) + '</td>' +
                '<td class="text-right">' + fmtNum(r.kredit) + '</td>' +
                '<td class="text-right">' + fmtNum(r.saldo_akhir) + '</td>' +
            '</tr>';
        }
        else {
            /* ---------- PP OUTSTANDING: 15 kolom ---------- */
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
    $('#lapKdcustomer').val(null).trigger('change');
    $('#lapTglRange').val('');
    $('#lapIdlocation').val(null).trigger('change');
    $('#lapDocno').val('');
    $('#lapKdprk').val(null).trigger('change');
    $('#lapInterval').val('30');

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
    Select2 ID Barang — hanya untuk PP & MASTER BARANG
    ===================================================== */
    if ((isPP || jenis === 'masterbarang' || jenis === 'analisamutasi') && $('#lapIdbarang').length) {
        var defaultInitialGroupBrng = '';
        $('#lapIdbarang').select2({
            placeholder: "Choose Your Item List",
            dropdownParent: $(document.body),
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
    if ((isPO || isVPO || isLPB || jenis === 'mastersupplier' || jenis === 'poharian' || jenis === 'umurhutang' || jenis === 'posisihutang') && $('#lapKdsupplier').length) {
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
    Select2 Customer — untuk Sales Order
    ===================================================== */
    if ((jenis === 'salesorder' || jenis === 'penjualan' || jenis === 'mastercustomer' || jenis === 'umurpiutang' || jenis === 'posisipiutang') && $('#lapKdcustomer').length) {
        var defaultInitialCustomer = '';
        $('#lapKdcustomer').select2({
            dropdownParent: $(document.body),
            placeholder: "Type / Choose Customer",
            allowClear: true,
            width: '100%',
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_customer',
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
                        _paramglobal_: defaultInitialCustomer,
                        _parameterx_: defaultInitialCustomer,
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
            templateResult: formatCustomer,
            templateSelection: formatCustomerSelection
        });
    }

    /* =====================================================
    Select2 COA — untuk Umur Hutang & Umur Piutang
    ===================================================== */
    if ((jenis === 'umurhutang' || jenis === 'umurpiutang' || jenis === 'posisihutang' || jenis === 'posisipiutang') && $('#lapKdprk').length) {
        var defaultInitialCOA = '';
        $('#lapKdprk').select2({
            dropdownParent: $(document.body),
            placeholder: "Type / Choose Perkiraan",
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
                        _paramglobal_: defaultInitialCOA,
                        _parameterx_: defaultInitialCOA,
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
            templateResult: formatCOA,
            templateSelection: formatCOASelection
        });
    }

    /* =====================================================
    Select2 Gudang — untuk Kartu Stock
    ===================================================== */
    if ((jenis === 'kartustock' || jenis === 'posisibrg') && $('#lapIdlocation').length) {
        var defaultInitialLocation = '';

        $('#lapIdlocation').select2({
            dropdownParent: $(document.body),
            placeholder: " -- Pilih Gudang -- ",
            allowClear: true,
            width: '100%',
            maximumSelectionLength: 1,
            multiple: false,
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_mlocation',   // <-- pakai ini
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
                        _paramglobal_: defaultInitialLocation,
                        _parameterx_: defaultInitialLocation,
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
                cache: true
            },
            escapeMarkup: function (markup) { return markup; },
            templateResult: formatLocation,
            templateSelection: formatLocationSelection
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

function formatCustomer(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
           repo.kdcustomer + "   <i class='fa fa-circle-o'></i>   " +
           repo.nmcustomer + "</div>";
}
function formatCustomerSelection(repo) {
    return repo.nmcustomer || repo.text;
}

function formatLocation(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
           repo.idlocation + "   <i class='fa fa-circle-o'></i>   " +
           repo.nmlocation + "</div>";
}
function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}

function formatCOA(repo) {
    if (repo.loading) return repo.text;
    return "<div class='select2-result-repository__description'>" +
           repo.kdprk + "   <i class='fa fa-circle-o'></i>   " +
           repo.nmprk + "</div>";
}
function formatCOASelection(repo) {
    return repo.nmprk || repo.text;
}