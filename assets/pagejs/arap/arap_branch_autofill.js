/* =========================================================
 * AUTOFILL INPUT AWAL CABANG / JOB (MODUL ARAP)
 * Cabang otomatis mengikuti session user yang sedang login
 * (default PT JATIM TAMAN STEEL / PLANT sesuai user login),
 * tetapi user tetap boleh memilih cabang lain.
 * Pola sama dengan modul SALES (sales_branch_autofill.js).
 * ========================================================= */
function arapCanonicalBranchKey(name) {
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

function arapFindBranchByName(branchName, callback) {
    var keyword = (branchName || '').toString().trim();

    if (keyword === '') {
        callback(null);
        return;
    }

    function pick(items) {
        var expected = arapCanonicalBranchKey(keyword);

        for (var i = 0; i < items.length; i++) {
            var itemName = items[i].nmbranch || items[i].text || '';

            if (arapCanonicalBranchKey(itemName) === expected) {
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

function arapFindBranchFromSession(branchId, branchName, callback) {
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

            arapFindBranchByName(branchName, callback);
        }).fail(function () {
            arapFindBranchByName(branchName, callback);
        });
        return;
    }

    arapFindBranchByName(branchName, callback);
}

function arapApplyInitialBranch(item, fireChange) {
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

    // Default ke cabang login, tetapi user tetap boleh memilih cabang lain.
    if (!optionExists) {
        $cabang.append(new Option(branchName, branchId, true, true));
    } else {
        $cabang.val(branchId);
    }

    $cabang.prop('disabled', false);

    if (fireChange === false) {
        $cabang.trigger('change.select2');
    } else {
        $cabang.trigger('change');
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

    arapFindBranchFromSession(sessionBranchId, sessionBranchName, function (item) {
        // Dokumen draft bisa terisi lebih dulu; jangan timpa cabang header.
        if (($('#docno').val() || '').toString().trim() !== '') {
            return;
        }

        if (!item) {
            if (sessionBranchId === '') {
                // Data session tidak lengkap: biarkan user memilih manual.
                return;
            }

            // Fallback: tetap pakai data session yang tersedia.
            item = {
                id: sessionBranchId,
                nmbranch: sessionBranchName || sessionBranchId
            };
        }

        arapApplyInitialBranch(item, true);
    });
});
