/* =========================================================
 * AUTOFILL INPUT AWAL CABANG / JOB (MODUL JURNAL UMUM PERKIRAAN)
 * Halaman addJUP otomatis memilih cabang / job default
 * (PT JATIM TAMAN STEEL MFG - kode 01) mengikuti session user
 * yang sedang login, tetapi user tetap boleh memilih cabang lain.
 * Pola sama dengan modul PENERIMAAN KAS/BANK & ARAP (branch_autofill).
 * ========================================================= */

/* Cabang / job default bila data session tidak lengkap. */
var JUP_DEFAULT_BRANCH_ID = '01';
var JUP_DEFAULT_BRANCH_NAME = 'PT JATIM TAMAN STEEL MFG';

function jupCanonicalBranchKey(name) {
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

function jupFindBranchByName(branchName, callback) {
    var keyword = (branchName || '').toString().trim();

    if (keyword === '') {
        callback(null);
        return;
    }

    function pick(items) {
        var expected = jupCanonicalBranchKey(keyword);

        for (var i = 0; i < items.length; i++) {
            var itemName = items[i].nmbranch || items[i].text || '';

            if (jupCanonicalBranchKey(itemName) === expected) {
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

function jupFindBranchFromSession(branchId, branchName, callback) {
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

            jupFindBranchByName(branchName, callback);
        }).fail(function () {
            jupFindBranchByName(branchName, callback);
        });
        return;
    }

    jupFindBranchByName(branchName, callback);
}

function jupApplyInitialBranch(item, fireChange) {
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
    // Hanya halaman input JUP (grid detail inline #jupGrid).
    var $form = $('#formPO');
    var $cabang = $('#cabang');

    if (!$form.length || !$cabang.length || !document.getElementById('jupGrid')) {
        return;
    }

    // Dokumen draft / update: header (termasuk cabang) sudah dipulihkan oleh
    // jup.js (documentReadable) dan cabang dikunci agar nomor jurnal tidak berubah.
    if (($('#docno').val() || '').toString().trim() !== '') {
        return;
    }

    var sessionBranchId = ($cabang.attr('data-session-branch-id') || '').toString().trim();
    var sessionBranchName = ($cabang.attr('data-session-branch-name') || '').toString().trim();

    if (sessionBranchId === '' && sessionBranchName === '') {
        // Fallback cabang default PT JATIM TAMAN STEEL MFG (kode 01).
        sessionBranchId = JUP_DEFAULT_BRANCH_ID;
        sessionBranchName = JUP_DEFAULT_BRANCH_NAME;
    }

    jupFindBranchFromSession(sessionBranchId, sessionBranchName, function (item) {
        // User sudah memilih cabang secara manual sebelum data selesai dimuat.
        if (($cabang.val() || '').toString().trim() !== '') {
            return;
        }

        if (!item) {
            if (sessionBranchId === '') {
                // Data session tidak lengkap: biarkan user memilih manual.
                return;
            }

            // Fallback: tetap pakai data session / cabang default yang tersedia.
            item = {
                id: sessionBranchId,
                nmbranch: sessionBranchName || sessionBranchId
            };
        }

        // Fire change supaya jup.js membentuk infix, prefix, suffix, dan nomor
        // jurnal JUP sesuai cabang yang terpilih.
        jupApplyInitialBranch(item, true);
    });
});
