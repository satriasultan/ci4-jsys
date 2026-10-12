/* =========================================================
 * TANDA TERIMA SUPPLIER
 * DETAIL / VIEW ONLY
 *
 * SOURCE :
 * sc_trx.tterima_hd
 * sc_trx.tterima_dt
 *
 * Tidak ada:
 * - Save
 * - Update
 * - Delete
 * - Add
 * - Final
 * - AJAX perubahan data
 * ========================================================= */


(function () {

    'use strict';


    /* =====================================================
     * NUMBER
     * ===================================================== */

    function numberValue(value) {

        if (
            typeof value === 'number' &&
            Number.isFinite(value)
        ) {

            return value;
        }


        let text =
            String(value ?? '')
                .trim();


        if (text === '') {

            return 0;
        }


        /*
         * PostgreSQL biasanya menghasilkan:
         *
         * 1000.50
         *
         * tetapi tetap support:
         *
         * 1.000,50
         */

        if (
            text.indexOf('.') >= 0 &&
            text.indexOf(',') >= 0
        ) {

            text =
                text
                    .replace(/\./g, '')
                    .replace(',', '.');

        } else if (
            text.indexOf(',') >= 0
        ) {

            text =
                text.replace(',', '.');
        }


        const result =
            Number(
                text.replace(
                    /[^0-9.\-]/g,
                    ''
                )
            );


        return Number.isFinite(result)
            ? result
            : 0;
    }


    /* =====================================================
     * FORMAT NUMBER
     * ===================================================== */

    function formatNumber(value) {

        return numberValue(value)
            .toLocaleString(
                'id-ID',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );
    }


    /* =====================================================
     * ESCAPE HTML
     * ===================================================== */

    function escapeHtml(value) {

        return String(value ?? '')
            .replace(
                /[&<>"']/g,
                function (char) {

                    return {

                        '&':
                            '&amp;',

                        '<':
                            '&lt;',

                        '>':
                            '&gt;',

                        '"':
                            '&quot;',

                        "'":
                            '&#039;'

                    }[char];

                }
            );
    }


    /* =====================================================
     * LOAD DETAIL
     * ===================================================== */

    function loadTterimaDetail() {

        const details =
            Array.isArray(
                window.tterimaDetailData
            )
                ? window.tterimaDetailData
                : [];


        renderDetail(
            details
        );


        calculateSummary(
            details
        );
    }


    /* =====================================================
     * RENDER DETAIL
     * ===================================================== */

    function renderDetail(details) {

        const $tbody =
            $('#tableTterimaDetail tbody');


        if (!$tbody.length) {

            return;
        }


        $tbody.empty();


        if (!details.length) {

            $tbody.html(`

                <tr>

                    <td
                        colspan="8"
                        class="text-center text-muted py-4"
                    >

                        <i class="fa fa-info-circle me-1"></i>

                        Belum ada detail jurnal.

                    </td>

                </tr>

            `);

            return;
        }


        details.forEach(
            function (item, index) {

                item =
                    item || {};


                const dk =
                    String(
                        item.dk || ''
                    )
                        .trim()
                        .toUpperCase();


                let dkText =
                    '-';


                if (dk === 'D') {

                    dkText =
                        '<span class="badge bg-primary">Debit</span>';

                } else if (dk === 'K') {

                    dkText =
                        '<span class="badge bg-warning text-dark">Kredit</span>';
                }


                const nilai =
                    numberValue(
                        item.nilai
                    );


                $tbody.append(`

                    <tr>

                        <td
                            class="text-center"
                        >
                            ${index + 1}
                        </td>


                        <td>
                            ${escapeHtml(
                    item.nobukti || ''
                )}
                        </td>


                        <td>
                            <strong>
                                ${escapeHtml(
                    item.noperkiraan || ''
                )}
                            </strong>
                        </td>


                        <td>
                            ${escapeHtml(
                    item.namaperkiraan || ''
                )}
                        </td>


                        <td>
                            ${escapeHtml(
                    item.keterangan || ''
                )}
                        </td>


                        <td
                            class="text-center"
                        >
                            ${dkText}
                        </td>


                        <td>
                            ${escapeHtml(
                    item.costprofitcenter ||
                    item.costcenter ||
                    ''
                )}
                        </td>


                        <td
                            class="text-end"
                        >
                            ${formatNumber(
                    nilai
                )}
                        </td>

                    </tr>

                `);

            }
        );
    }


    /* =====================================================
     * SUMMARY
     *
     * Debit  = +
     * Kredit = -
     *
     * Selisih = Debit - Kredit
     * ===================================================== */

    function calculateSummary(details) {

        let debit =
            0;

        let kredit =
            0;


        details.forEach(
            function (item) {

                const nilai =
                    numberValue(
                        item.nilai
                    );


                const dk =
                    String(
                        item.dk || ''
                    )
                        .trim()
                        .toUpperCase();


                if (dk === 'D') {

                    debit +=
                        nilai;

                } else if (dk === 'K') {

                    kredit +=
                        nilai;
                }

            }
        );


        const selisih =
            debit -
            kredit;


        $('#totalDebit')
            .text(
                formatNumber(
                    debit
                )
            );


        $('#totalKredit')
            .text(
                formatNumber(
                    kredit
                )
            );


        $('#totalSelisih')
            .text(
                formatNumber(
                    selisih
                )
            );


        /*
         * Jika ada field balance di header,
         * pastikan tetap sesuai transaksi.
         *
         * Tidak mengubah database.
         */

        const headerBalance =
            numberValue(
                $('#balance').val()
            );


        if (
            Math.abs(
                headerBalance -
                selisih
            ) < 0.01
        ) {

            $('#balance')
                .attr(
                    'title',
                    'Balance sesuai detail jurnal'
                );

        } else {

            $('#balance')
                .attr(
                    'title',
                    'Balance header berbeda dengan detail jurnal'
                );
        }
    }


    /* =====================================================
     * DISABLE ALL FORM ELEMENT
     *
     * Pengaman tambahan.
     * Walaupun view sudah memakai disabled,
     * semua field tetap dipastikan disabled.
     * ===================================================== */

    function disableAllFields() {

        $('#formTterimaDetail')
            .find(
                'input, select, textarea'
            )
            .prop(
                'disabled',
                true
            );


        /*
         * Jangan disable table karena table
         * bukan input.
         */
    }


    /* =====================================================
     * HAPUS BUTTON ACTION
     *
     * Karena halaman ini view-only.
     * ===================================================== */

    function removeActionButtons() {

        $('#btnAddDetail')
            .remove();

        $('#btnReloadDetail')
            .remove();

        $('#btnSaveDraft')
            .remove();

        $('#btnFinal')
            .remove();

        $('#btnEditDetail')
            .remove();

        $('#btnDeleteDetail')
            .remove();

        $('#btnSimpanDetail')
            .remove();

        $('#btnSaveDetail')
            .remove();

        $('#btnUpdateDetail')
            .remove();

        $('#btnCetakTterima')
            .remove();

        $('#btnPrintTterima')
            .remove();

        /*
         * Jika ada button action di table
         */
        $('#tableTterimaDetail')
            .find(
                '.btn-action, .btn-edit, .btn-delete'
            )
            .remove();
    }


    /* =====================================================
     * DOCUMENT READY
     * ===================================================== */

    $(document).ready(
        function () {

            disableAllFields();

            removeActionButtons();

            loadTterimaDetail();

        }
    );


})();


function deleteTterima() {
    const docno = $('#docno').val();

    if (!docno) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Nomor dokumen tidak ditemukan.'
        });
        return;
    }

    Swal.fire({
        icon: 'warning',
        title: 'Hapus Tanda Terima?',
        text: 'Data Tanda Terima dengan nomor ' + docno + ' akan dihapus.',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545'
    }).then(function (result) {

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: HOST_URL + 'arap/transaksi/deleteTterima',
            type: 'POST',
            dataType: 'json',
            data: {
                docno: docno
            },

            beforeSend: function () {
                $('#btnDeleteTterima')
                    .prop('disabled', true)
                    .html(
                        '<i class="fa fa-spinner fa-spin"></i> Menghapus...'
                    );
            },

            success: function (response) {

                if (response.status === true) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Tanda Terima berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function () {
                        window.location.href =
                            HOST_URL + 'arap/transaksi/tterima';
                    });

                } else {

                    $('#btnDeleteTterima')
                        .prop('disabled', false)
                        .html(
                            '<i class="fa fa-trash"></i> Hapus Tanda Terima'
                        );

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message ||
                            'Tanda Terima gagal dihapus.'
                    });
                }
            },

            error: function (xhr) {

                $('#btnDeleteTterima')
                    .prop('disabled', false)
                    .html(
                        '<i class="fa fa-trash"></i> Hapus Tanda Terima'
                    );

                console.error(
                    'ERROR DELETE TTERIMA:',
                    xhr.status,
                    xhr.responseText
                );

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat menghapus Tanda Terima.'
                });
            }
        });
    });
}