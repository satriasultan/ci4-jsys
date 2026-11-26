/*
 * =========================================================================
 * pmk_brng_list.js  —  KHUSUS HALAMAN LIST PENERIMAAN BARANG (I.Q.A.6)
 * URL  : persediaan/trans/pmk_barang
 * -------------------------------------------------------------------------
 * Handler tombol aksi "Laporan Jurnal Transaksi" per baris.
 * Endpoint (modul ini, read-only):
 *   POST persediaan/trans/laporan_jurnal_transaksi_pmk
 * ========================================================================= */

(function ($) {
    'use strict';

    var JURNAL_URL = (typeof HOST_URL !== 'undefined' ? HOST_URL : '')
        + 'persediaan/trans/laporan_jurnal_transaksi_pmk';

    var tableJurnal = null;

    /* ------------------------------------------------------------------ */
    /* HELPER                                                              */
    /* ------------------------------------------------------------------ */
    function escHtml(text) {
        return $('<div>').text(text === null || text === undefined ? '' : String(text)).html();
    }

    function money(value) {
        var n = parseFloat(value);

        if (!isFinite(n)) {
            n = 0;
        }

        return n.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function fmtDate(value) {
        var s = $.trim(String(value || ''));

        if (s === '') {
            return '';
        }

        var part = s.substring(0, 10).split('-');

        if (part.length === 3) {
            return part[2] + '-' + part[1] + '-' + part[0];
        }

        return s;
    }

    /* Bootstrap 5 tidak punya jQuery plugin .modal() -> pakai API native */
    function showModalNative(selector) {
        var node = document.querySelector(selector);

        if (!node) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(node).show();
            return;
        }

        if ($.fn.modal) {
            $(selector).modal('show');
        }
    }

    function destroyJurnalTable() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableLaporanJurnalpmk')) {
            $('#tableLaporanJurnalpmk').DataTable().destroy();
        }

        tableJurnal = null;
    }

    /* ------------------------------------------------------------------ */
    /* BUKA MODAL + LOAD DATA JURNAL                                       */
    /* ------------------------------------------------------------------ */
    function openJurnal(docno) {
        docno = $.trim(docno || '');

        if (docno === '') {
            Swal.fire({
                icon: 'warning',
                title: 'No. Transaksi Kosong',
                text: 'Tidak dapat membuka laporan jurnal.'
            });
            return;
        }

        $('#modalLaporanJurnalpmk .pmk-journal-docno').text(docno);
        $('#jurnalTotalDebet, #jurnalTotalKredit').text('0.00');

        showModalNative('#modalLaporanJurnalpmk');

        destroyJurnalTable();

        $('#tableLaporanJurnalpmk tbody').html(
            '<tr><td colspan="7" class="text-center">' +
            '<i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>'
        );

        $.ajax({
            url: JURNAL_URL,
            type: 'POST',
            dataType: 'json',
            data: { docno: docno }
        }).done(function (res) {

            if (!res || !res.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: (res && res.messages) ? res.messages : 'Gagal mengambil laporan jurnal'
                });

                $('#tableLaporanJurnalpmk tbody').html(
                    '<tr><td colspan="7" class="text-center text-muted">Tidak ada data jurnal.</td></tr>'
                );

                return;
            }

            var rows = res.data || [];
            var totalRow = null;
            var detail = [];

            $.each(rows, function (i, row) {
                if (String(row.urutan) === '1') {
                    totalRow = row;
                } else {
                    detail.push(row);
                }
            });

            $('#jurnalTotalDebet').text(money(totalRow ? totalRow.debet : 0));
            $('#jurnalTotalKredit').text(money(totalRow ? totalRow.kredit : 0));

            $('#tableLaporanJurnalpmk tbody').empty();

            tableJurnal = $('#tableLaporanJurnalpmk').DataTable(buildJurnalOptions(detail));

        }).fail(function () {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Gagal mengambil laporan jurnal transaksi.'
            });
        });
    }

    /* ------------------------------------------------------------------ */
    /* KONFIGURASI DATATABLE JURNAL                                        */
    /* ------------------------------------------------------------------ */
    function buildJurnalOptions(detail) {
        return {
            data: detail,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            autoWidth: false,
            language: {
                emptyTable: 'Tidak ada data jurnal untuk transaksi ini.'
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    render: function (d, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'trxdate',
                    className: 'text-center',
                    render: function (d) {
                        return fmtDate(d);
                    }
                },
                {
                    data: 'docno',
                    render: function (d) {
                        return '<span class="jnum">' + escHtml(d) + '</span>';
                    }
                },
                {
                    data: 'nmcoa',
                    render: function (d, type, row) {
                        var coa = $.trim(row.idcoa || '');
                        var nm = $.trim(d || '');
                        var cc = $.trim(row.nmcostcenter || '');
                        var html = '';

                        if (coa !== '') {
                            html += '<span class="jcoa">' + escHtml(coa) + '</span> ';
                        }

                        html += escHtml(nm);

                        if (cc !== '') {
                            html += '<div class="jcc"><i class="fa fa-tag"></i> ' + escHtml(cc) + '</div>';
                        }

                        return html;
                    }
                },
                {
                    data: 'debet',
                    className: 'text-end',
                    render: function (d) {
                        return money(d);
                    }
                },
                {
                    data: 'kredit',
                    className: 'text-end',
                    render: function (d) {
                        return money(d);
                    }
                },
                {
                    data: 'ref_docno',
                    render: function (d, type, row) {
                        var ref = $.trim(d || '');
                        var dt = $.trim(row.ref_doctype || '');

                        if (ref === '') {
                            return '';
                        }

                        return escHtml(ref)
                            + (dt !== '' ? ' <span class="jref">(' + escHtml(dt) + ')</span>' : '');
                    }
                }
            ]
        };
    }

    /* ------------------------------------------------------------------ */
    /* EVENT                                                               */
    /* ------------------------------------------------------------------ */

    /* delegation: baris DataTable dibuat dinamis */
    $(document).on('click', '.pmk-act-jurnal', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openJurnal($(this).attr('data-docno'));
    });

    /* bersihkan DataTable saat modal ditutup */
    $(document).on('hidden.bs.modal', '#modalLaporanJurnalpmk', function () {
        destroyJurnalTable();
    });

})(jQuery);

