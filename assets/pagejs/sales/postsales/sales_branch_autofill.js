/* =========================================================
 * AUTOFILL INPUT AWAL CABANG / JOB (MODUL PENJUALAN)
 * Cabang mengikuti session user yang sedang login.
 * Hanya PT JATIM TAMAN STEEL yang boleh memilih bebas.
 * Pola sama dengan modul PRODUCTION (Material Release).
 * ========================================================= */
function salesCanonicalBranchKey(name) {
    var key = (name || '').toString().toUpperCase().replace(/[^A-Z0-9]/g, '');

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

function salesFindBranchByName(branchName, callback) {
    var keyword = (branchName || '').toString().trim();

    if (keyword === '') {
        callback(null);
        return;
    }

    function pick(items) {
        var expected = salesCanonicalBranchKey(keyword);

        for (var i = 0; i < items.length; i++) {
            var itemName = items[i].nmbranch || items[i].text || '';

            if (salesCanonicalBranchKey(itemName) === expected) {
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

function salesFindBranchFromSession(branchId, branchName, callback) {
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

            salesFindBranchByName(branchName, callback);
        }).fail(function () {
            salesFindBranchByName(branchName, callback);
        });
        return;
    }

    salesFindBranchByName(branchName, callback);
}

function salesLockBranchSelect2($cabang) {
    // Kunci di level Select2: dropdown tidak bisa dibuka & pilihan tidak bisa diubah.
    var s2 = $cabang.data('select2');

    if (s2 && s2.options && typeof s2.options.set === 'function') {
        s2.options.set('disabled', true);
    }

    $cabang.off('select2:selecting.saleslock select2:unselecting.saleslock');
    $cabang.on('select2:selecting.saleslock select2:unselecting.saleslock', function (e) {
        if (e && e.params) {
            e.params.prevented = true;
        }
    });

    // Tampilan seperti field disabled (tidak bisa diklik).
    $cabang
        .next('.select2-container')
        .css({ 'pointer-events': 'none', 'background-color': '#e9ecef', 'border-radius': '4px' })
        .attr('title', 'Cabang / Job mengikuti user login');
}

function salesApplyInitialBranch(item, canChoose, fireChange) {
    var $cabang = $('#cabang');

    if (!$cabang.length || !item) {
        return;
    }

    var branchId = (item.id || item.idbranch || '').toString().trim();
    var branchName = (item.nmbranch || item.text || '').toString().trim();

    if (branchId === '') {
        return;
    }

    if (branchName === '') {
        branchName = branchId;
    }

    var optionExists = $cabang.find('option').filter(function () {
        return ($(this).val() || '').toString() === branchId;
    }).length > 0;

    if (canChoose) {
        // PT JATIM TAMAN STEEL: default cabang login, tetap boleh pilih cabang lain.
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
        salesLockBranchSelect2($cabang);
    }

    if (fireChange === false) {
        $cabang.trigger('change.select2');
    } else {
        $cabang.trigger('change');
    }

    if ($cabang.prop('disabled')) {
        $cabang
            .next('.select2-container')
            .find('.select2-selection__clear')
            .remove();
    }
}

$(function () {
    // Hanya halaman input (punya form transaksi dan belum ada nomor dokumen).
    var $form = $('#formPO');
    var $cabang = $('#cabang');

    if (!$form.length || !$cabang.length) {
        return;
    }

    var docno = ($('#docno').val() || '').toString().trim();

    if (docno !== '') {
        // Dokumen draft / detail: cabang mengikuti data header.
        return;
    }

    var sessionBranchId = ($cabang.attr('data-session-branch-id') || '').toString().trim();
    var sessionBranchName = ($cabang.attr('data-session-branch-name') || '').toString().trim();
    var canChooseBranch = ($cabang.attr('data-can-choose-branch') || '0').toString() === '1';

    salesFindBranchFromSession(sessionBranchId, sessionBranchName, function (item) {
        // Dokumen draft bisa terisi lebih dulu oleh documentReadable();
        // jangan menimpa cabang header yang sudah tersimpan.
        if (($('#docno').val() || '').toString().trim() !== '') {
            return;
        }

        if (!item) {
            if (sessionBranchId === '') {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Cabang / Job user login tidak ditemukan. Silakan hubungi IT.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Fallback: tetap pakai data session yang tersedia.
            item = {
                id: sessionBranchId,
                nmbranch: sessionBranchName || sessionBranchId
            };
        }

        salesApplyInitialBranch(item, canChooseBranch, true);
    });
});
