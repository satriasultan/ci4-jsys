/* =========================================================
 * Riwayat BOM Working Order (read-only)
 * Dipakai oleh halaman list, add/edit, dan detail WO.
 * ========================================================= */

function woBomHistoryEsc(v) {
    return String(v === null || v === undefined ? '' : v)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function woBomHistoryBadge(status) {
    var s = String(status || '').toUpperCase();

    if (s === 'APPLIED') {
        return '<span class="badge bg-success">DITERAPKAN</span>';
    }
    if (s === 'PENDING') {
        return '<span class="badge bg-warning text-dark">MENUNGGU FINAL</span>';
    }
    if (s === 'CANCELLED') {
        return '<span class="badge bg-danger">DIBATALKAN</span>';
    }
    if (s === 'SUPERSEDED') {
        return '<span class="badge bg-dark">DIGANTIKAN</span>';
    }

    return '<span class="badge bg-light text-dark">' + woBomHistoryEsc(s) + '</span>';
}

function woBomHistoryDate(v) {
    if (!v) return '-';

    var d = new Date(String(v).replace(' ', 'T'));
    if (isNaN(d.getTime())) return woBomHistoryEsc(v);

    var pad = function (n) { return n < 10 ? '0' + n : '' + n; };

    return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() +
           ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
}

function openWoBomHistory(docno) {
    docno = String(docno || '').trim();

    if (!docno) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Nomor WO tidak ditemukan.'
        });
        return;
    }

    Swal.fire({
        title: 'Riwayat BOM WO',
        html: '<div class="text-center py-3"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>',
        showConfirmButton: false,
        allowOutsideClick: false,
        width: '950px'
    });

    $.ajax({
        url: HOST_URL + 'production/trans/list_workingorder_bom_history',
        type: 'POST',
        data: { docno: docno },
        dataType: 'json'
    }).done(function (res) {
        if (!res || !res.status) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: (res && res.message) || 'Gagal memuat riwayat BOM.'
            });
            return;
        }

        var html = '';
        html += '<div class="text-start mb-2"><b>WO:</b> ' + woBomHistoryEsc(res.docno || docno) + '</div>';

        if (res.message) {
            html += '<div class="alert alert-info text-start">' + woBomHistoryEsc(res.message) + '</div>';
        }

        // if (res.boms && res.boms.length) {
        //     html += '<div class="text-start mb-2"><b>BOM yang pernah dipakai:</b><br>';

        //     res.boms.forEach(function (b) {
        //         var label = woBomHistoryEsc(b.bomno);

        //         if (b.nmbarang_jadi) {
        //             label += ' &mdash; ' + woBomHistoryEsc(b.nmbarang_jadi);
        //         }

        //         html += '<span class="badge ' + (b.aktif ? 'bg-success' : 'bg-danger') +
        //                 ' me-1 mb-1">' + label +
        //                 (b.aktif ? ' (aktif)' : '') + '</span> ';
        //     });

        //     html += '</div>';
        // }

        if (!res.items || !res.items.length) {
            html += '<div class="alert alert-info text-start mb-0">Belum ada riwayat perubahan BOM untuk WO ini.</div>';

            Swal.fire({
                title: 'Riwayat BOM WO',
                html: html,
                width: '950px',
                showCloseButton: true,
                confirmButtonText: 'Tutup'
            });
            return;
        }

        html += '<div style="max-height:420px;overflow:auto;">';
        html += '<table class="table table-sm table-bordered table-striped text-start" style="font-size:12px;">';
        html += '<thead class="bg-primary text-white"><tr>' +
                '<th>Waktu</th><th>Oleh</th><th>Jenis</th><th>BOM Lama</th><th>BOM Baru</th>' +
                '<th>Alasan</th><th>Status</th><th>Dampak</th><th>Diterapkan</th>' +
                '</tr></thead><tbody>';

        res.items.forEach(function (r) {
            var isBuildfor = String(r.jenis || '').toUpperCase() === 'UBAH_BUILDFOR';

            var jenis = isBuildfor ? 'Ubah Build For' : 'Ganti BOM';

            var bomLama = woBomHistoryEsc(r.bomno_lama || '-');
            var bomBaru = woBomHistoryEsc(r.bomno_baru || '-');

            if (isBuildfor) {
                if (r.buildfor_lama !== null && r.buildfor_lama !== undefined && r.buildfor_lama !== '') {
                    bomLama += ' <small>(qty: ' + woBomHistoryEsc(r.buildfor_lama) + ')</small>';
                }
                if (r.buildfor_baru !== null && r.buildfor_baru !== undefined && r.buildfor_baru !== '') {
                    bomBaru += ' <small>(qty: ' + woBomHistoryEsc(r.buildfor_baru) + ')</small>';
                }
            }

            var dampak = (r.affected_count !== null && r.affected_count !== undefined && r.affected_count !== '')
                ? r.affected_count + ' dok'
                : '-';

            var apply = r.applydate
                ? (woBomHistoryEsc(r.applyby || '-') + '<br>' + woBomHistoryDate(r.applydate))
                : '-';

            html += '<tr>' +
                    '<td>' + woBomHistoryDate(r.inputdate) + '</td>' +
                    '<td>' + woBomHistoryEsc(r.inputby || '-') + '</td>' +
                    '<td>' + woBomHistoryEsc(jenis) + '</td>' +
                    '<td>' + bomLama + '</td>' +
                    '<td>' + bomBaru + '</td>' +
                    '<td>' + woBomHistoryEsc(r.keterangan || '-') + '</td>' +
                    '<td>' + woBomHistoryBadge(r.status) + '</td>' +
                    '<td>' + woBomHistoryEsc(dampak) + '</td>' +
                    '<td>' + apply + '</td>' +
                    '</tr>';
        });

        html += '</tbody></table></div>';

        Swal.fire({
            title: 'Riwayat BOM WO',
            html: html,
            width: '950px',
            showCloseButton: true,
            confirmButtonText: 'Tutup'
        });
    }).fail(function (xhr) {
        console.error(xhr.responseText);

        Swal.fire({
            icon: 'error',
            title: 'Server Error',
            text: 'Gagal memuat riwayat BOM.'
        });
    });
}
