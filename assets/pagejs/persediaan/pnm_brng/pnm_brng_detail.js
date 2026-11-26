/*
 * =========================================================================
 * pnm_brng_detail.js  —  HALAMAN DETAIL PENERIMAAN BARANG (I.Q.A.6)
 * URL  : persediaan/trans/detail_pnm_brng_mst
 * -------------------------------------------------------------------------
 * Modal "Laporan Jurnal Transaksi" — struktur, styling, dan perilaku
 * SAMA DENGAN modul LPB (purchase/trans/detailLPB).
 *
 * Endpoint (modul ini, read-only):
 *   POST persediaan/trans/laporan_jurnal_transaksi_pnm
 * ========================================================================= */

(function ($) {
    'use strict';

    var JURNAL_URL = (typeof HOST_URL !== 'undefined' ? HOST_URL : '')
        + 'persediaan/trans/laporan_jurnal_transaksi_pnm';

    var tableLaporanJurnal = null;

    /* ------------------------------------------------------------------ */
    /* HELPER                                                              */
    /* ------------------------------------------------------------------ */

    /* Bootstrap 5 tidak menyediakan jQuery plugin .modal(); pakai API native
       dengan fallback jQuery (aman untuk kedua versi). */
    function showModal(selector) {
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

    function formatNumber(value) {
        return parseFloat(value || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function destroyTable() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableLaporanJurnal')) {
            $('#tableLaporanJurnal').DataTable().destroy();
        }

        tableLaporanJurnal = null;
    }

    /* ------------------------------------------------------------------ */
    /* DATATABLE JURNAL (kolom & opsi mengikuti LPB)                       */
    /* ------------------------------------------------------------------ */

    function buildTableOptions(rows) {
        return {
            data: rows,
            destroy: true,
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
                    render: function (data, type, row, meta) {
                        if (String(row.urutan) === '1') {
                            return '';
                        }

                        return meta.row + 1;
                    }
                },
                {
                    data: 'trxdate',
                    render: function (data) {
                        return data || '';
                    }
                },
                {
                    data: 'docno',
                    render: function (data) {
                        return data || '';
                    }
                },
                {
                    /* PNM tidak punya supplier -> kolom ke-4 = Cost Center */
                    data: 'nmcostcenter',
                    render: function (data, type, row) {
                        if (String(row.urutan) === '1') {
                            return '';
                        }

                        return data || '';
                    }
                },
                {
                    data: 'idcoa',
                    render: function (data, type, row) {
                        if (String(row.urutan) === '1') {
                            return '';
                        }

                        return data || '';
                    }
                },
                {
                    data: 'nmcoa',
                    render: function (data, type, row) {
                        /* baris TOTAL */
                        if (String(row.urutan) === '1') {
                            return '<b style="font-size:16px">TOTAL</b>';
                        }

                        return data || '';
                    }
                },
                {
                    data: 'debet',
                    className: 'text-right text-end',
                    render: function (data) {
                        return formatNumber(data || 0);
                    }
                },
                {
                    data: 'kredit',
                    className: 'text-right text-end',
                    render: function (data) {
                        return formatNumber(data || 0);
                    }
                }
            ],
            createdRow: function (row, data) {
                if (String(data.urutan) === '1') {
                    $(row).css({
                        'font-weight': 'bold',
                        'background-color': '#f1f1f1'
                    });
                }
            }
        };
    }


    /* ------------------------------------------------------------------ */
    /* EVENT: BUKA LAPORAN JURNAL                                          */
    /* ------------------------------------------------------------------ */

    $(document).on('click', '#btnLaporanJurnal', function () {

        var docno = $.trim($('#docno').val());

        if (docno === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Penerimaan Barang belum dipilih',
                text: 'Silahkan buka transaksi Penerimaan Barang terlebih dahulu'
            });

            return;
        }

        showModal('#modalLaporanJurnal');

        destroyTable();

        $('#tableLaporanJurnal tbody').html(
            '<tr><td colspan="8" class="text-center">' +
            '<i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>'
        );

        $.ajax({
            url: JURNAL_URL,
            type: 'POST',
            dataType: 'json',
            data: { docno: docno }
        }).done(function (response) {

            if (!response || !response.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: (response && response.messages)
                        ? response.messages
                        : 'Gagal mengambil laporan jurnal'
                });

                $('#tableLaporanJurnal tbody').html(
                    '<tr><td colspan="8" class="text-center text-muted">' +
                    'Tidak ada data jurnal.</td></tr>'
                );

                return;
            }

            $('#tableLaporanJurnal tbody').empty();

            tableLaporanJurnal = $('#tableLaporanJurnal').DataTable(
                buildTableOptions(response.data || [])
            );

            setTimeout(function () {
                tableLaporanJurnal.columns.adjust();
            }, 300);

        }).fail(function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal mengambil laporan jurnal'
            });
        });
    });

    /* bersihkan DataTable ketika modal ditutup */
    $(document).on('hidden.bs.modal', '#modalLaporanJurnal', function () {
        destroyTable();
    });

})(jQuery);

