/* =========================================================
 * AUTOFILL INPUT AWAL CABANG / JOB — MODUL PURCHASE
 * Dipakai oleh : ADD PO (v_add_po.php) & ADD LPB (v_add_lpb.php)
 * Pola sama dengan modul PRODUCTION (lihat v_add_penerimaanbp.php):
 *  - Cabang otomatis mengikuti session user yang login.
 *  - Hanya user PT JATIM TAMAN STEEL yang boleh memilih cabang lain;
 *    user lain dikunci ke cabang login.
 *  - Dokumen draft: cabang mengikuti header & nomor tidak digenerate ulang.
 * ========================================================= */

(function ($) {
    'use strict';

    function normalizeBranchKey(name) {
        return (name || '').toString().toUpperCase().replace(/[^A-Z0-9]/g, '');
    }

    function canonicalBranchKey(name) {
        var key = normalizeBranchKey(name);

        var aliases = {
            'PLANI': 'PLANTI',
            'PLAN1': 'PLANTI',
            'PLANT1': 'PLANTI',
            'PLANII': 'PLANTII',
            'PLAN2': 'PLANTII',
            'PLANT2': 'PLANTII',
            'PTJATIMTAMANSTEEL': 'PTJATIMTAMANSTEELMFG'
        };

        return aliases[key] || key;
    }

    function findBranchByName(branchName, callback) {
        var keyword = (branchName || '').toString().trim();

        if (keyword === '') {
            callback(null);
            return;
        }

        function pick(items) {
            var expected = canonicalBranchKey(keyword);

            for (var i = 0; i < items.length; i++) {
                var itemName = items[i].nmbranch || items[i].text || '';

                if (canonicalBranchKey(itemName) === expected) {
                    return items[i];
                }
            }

            return null;
        }

        $.ajax({
            type: 'POST',
            url: HOST_URL + 'api/globalmodule/list_branchjob',
            data: {
                _search_: keyword,
                _page_: 1,
                _perpage_: 50,
                _draw_: true,
                _start_: 1
            },
            dataType: 'json'
        }).done(function (data) {
            var items = (data && data.items) ? data.items : [];
            var match = pick(items);

            if (match) {
                callback(match);
                return;
            }

            // Fallback: ambil seluruh cabang lalu cocokkan nama.
            $.ajax({
                type: 'POST',
                url: HOST_URL + 'api/globalmodule/list_branchjob',
                data: {
                    _search_: '',
                    _page_: 1,
                    _perpage_: 100,
                    _draw_: true,
                    _start_: 1
                },
                dataType: 'json'
            }).done(function (allData) {
                var allItems = (allData && allData.items) ? allData.items : [];
                callback(pick(allItems));
            }).fail(function () {
                callback(null);
            });
        }).fail(function () {
            callback(null);
        });
    }

    function findBranchFromSession(branchId, branchName, callback) {
        if (branchId !== '') {
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_branchjob',
                data: { var: branchId },
                dataType: 'json'
            }).done(function (data) {
                var items = (data && data.items) ? data.items : [];

                if (items.length > 0) {
                    callback(items[0]);
                    return;
                }

                findBranchByName(branchName, callback);
            }).fail(function () {
                findBranchByName(branchName, callback);
            });
            return;
        }

        findBranchByName(branchName, callback);
    }

    function applyInitialBranch(item, canChoose, fireChange) {
        var $cabang = $('#cabang');

        if (!$cabang.length || !item) {
            return;
        }

        var branchId = (item.id || item.idbranch || '').toString().trim();
        var branchName = (item.nmbranch || item.text || '').toString().trim();

        if (branchId === '') {
            return;
        }

        // Jangan biarkan option tanpa label bila nama cabang tidak ditemukan.
        if (branchName === '') {
            branchName = branchId;
        }

        var optionExists = $cabang.find('option').filter(function () {
            return ($(this).val() || '').toString() === branchId;
        }).length > 0;

        if (canChoose) {
            // PT JATIM TAMAN STEEL: tetap default ke cabang login,
            // tetapi user boleh mencari / memilih cabang lain.
            if (!optionExists) {
                $cabang.append(new Option(branchName, branchId, true, true));
            } else {
                $cabang.val(branchId);
            }

            $cabang.prop('disabled', false);
        } else {
            // User lain: kunci ke cabang login.
            $cabang.empty();
            $cabang.append(new Option(branchName, branchId, true, true));
            $cabang.prop('disabled', true);
        }

        if (fireChange === false) {
            // Update tampilan Select2 saja. Handler change modul tidak dipanggil
            // agar nomor dokumen draft tidak digenerate ulang.
            $cabang.trigger('change.select2');
        } else {
            // Catatan: Select2 4.0.13 tidak menyediakan method disable/enable,
            // cukup gunakan prop('disabled') agar container ikut ter-update.
            $cabang.trigger('change');
        }

        // Select2 tetap menampilkan tombol clear (allowClear: true) walau
        // elemen sedang disabled. Sembunyikan saat dikunci agar tidak membingungkan.
        if ($cabang.prop('disabled')) {
            $cabang
                .next('.select2-container')
                .find('.select2-selection__clear')
                .remove();
        }
    }

    $(function () {
        var $form = $('#formPO'); // Add PO & Add LPB memakai id form yang sama
        var $cabang = $('#cabang');

        if (!$form.length || !$cabang.length) {
            return;
        }

        // Dokumen draft: nomor sudah final (di-set server-side & po.js/lpb.js).
        var hasDraftDoc = ($('#docno').val() || '').toString().trim() !== '';
        var draftCabang = ($cabang.attr('data-draft-cabang') || '').toString().trim();

        var sessionBranchId = ($cabang.attr('data-session-branch-id') || '').toString().trim();
        var sessionBranchName = ($cabang.attr('data-session-branch-name') || '').toString().trim();
        var canChooseBranch = ($cabang.attr('data-can-choose-branch') || '0').toString() === '1';

        // Dokumen draft: cabang header sudah tersimpan, jadi ikuti data header.
        var initialBranchId = draftCabang !== '' ? draftCabang : sessionBranchId;
        var initialBranchName = draftCabang !== '' ? '' : sessionBranchName;

        // Draft selalu dikunci agar cabang header tidak berubah diam-diam.
        var lockBranch = !canChooseBranch || hasDraftDoc;

        findBranchFromSession(initialBranchId, initialBranchName, function (item) {
            if (!item) {
                if (!lockBranch) {
                    // User PT JATIM TAMAN STEEL: biarkan memilih manual.
                    return;
                }

                if (initialBranchId !== '') {
                    // Fallback: tetap kunci memakai data yang tersedia
                    // agar user tidak terhambat bila lookup nama gagal.
                    item = {
                        id: initialBranchId,
                        nmbranch: initialBranchName || initialBranchId
                    };
                } else {
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Cabang / Job user login tidak ditemukan. Silakan hubungi IT.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });

                    return;
                }
            }

            applyInitialBranch(item, !lockBranch, !hasDraftDoc);
        });

        // Form header PO/LPB dikirim native (bukan FormData): select yang disabled
        // tidak ikut ter-POST, jadi buka kembali saat submit.
        $(document).on('submit', '#formPO', function () {
            if ($('#cabang').prop('disabled')) {
                $('#cabang').prop('disabled', false);
            }
        });
    });
})(jQuery);
