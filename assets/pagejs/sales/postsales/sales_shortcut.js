/* =========================================================
 * SHORTCUT KEYBOARD MODUL PENJUALAN
 * Mengikuti pola modul PRODUCTION (Material Release):
 *   CTRL + Q : input item   (tombol + hijau)
 *   CTRL + E : update item  (tombol edit kuning)
 *   CTRL + D : hapus item   (tombol hapus merah)
 *   Panah atas/bawah : pindah sorotan baris detail
 *   Spasi : centang / lepas baris yang sedang disorot
 * Shortcut hanya aktif di halaman input (ada tombol #btnAddDetail).
 * ========================================================= */
var salesActiveRowIndex = -1;

function salesDetailTable() {
    var selectors = ['#tabsalesorderdtl', '#tabdeliveryorderdtl', '#tabsuratjalandtl', '#tabpenjualandtl'];

    for (var i = 0; i < selectors.length; i++) {
        if ($(selectors[i]).length) {
            return $(selectors[i]);
        }
    }

    return $();
}

function salesDetailRows() {
    return salesDetailTable().find('tbody tr');
}

function salesHighlightRow(index) {
    var rows = salesDetailRows();

    rows.removeClass('row-active');

    if (index >= 0 && index < rows.length) {
        var row = rows.eq(index);
        row.addClass('row-active');

        if (row[0] && typeof row[0].scrollIntoView === 'function') {
            row[0].scrollIntoView({ block: 'nearest' });
        }
    }
}

function salesIsTypingTarget(el) {
    if (!el) {
        return false;
    }

    var tag = (el.tagName || '').toUpperCase();

    return tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || el.isContentEditable;
}

$(document).on('keydown', function (e) {
    // Shortcut hanya untuk halaman input (bukan list / detail read-only).
    if (!document.getElementById('btnAddDetail')) {
        return;
    }

    var key = (e.key || '').toLowerCase();

    // CTRL + Q : Input item
    if (e.ctrlKey && key === 'q') {
        e.preventDefault();
        if (typeof btnInputDetail === 'function') {
            btnInputDetail();
        }
        return;
    }

    // CTRL + E : Update item
    if (e.ctrlKey && key === 'e') {
        e.preventDefault();
        if (typeof btnUpdateDetail === 'function') {
            btnUpdateDetail();
        }
        return;
    }

    // CTRL + D : Hapus item
    if (e.ctrlKey && key === 'd') {
        e.preventDefault();
        if (typeof btnDeleteDetail === 'function') {
            btnDeleteDetail();
        }
        return;
    }

    // Navigasi baris hanya bila fokus tidak di field form.
    if (salesIsTypingTarget(document.activeElement)) {
        return;
    }

    var rows = salesDetailRows();

    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        if (!rows.length) {
            return;
        }

        if (salesActiveRowIndex < 0) {
            salesActiveRowIndex = (e.key === 'ArrowDown') ? 0 : rows.length - 1;
        } else if (e.key === 'ArrowDown') {
            salesActiveRowIndex = Math.min(salesActiveRowIndex + 1, rows.length - 1);
        } else {
            salesActiveRowIndex = Math.max(salesActiveRowIndex - 1, 0);
        }

        e.preventDefault();
        salesHighlightRow(salesActiveRowIndex);
        return;
    }

    if (e.code === 'Space' && salesActiveRowIndex >= 0) {
        var cb = salesDetailRows().eq(salesActiveRowIndex).find('input.row-check');

        if (cb.length) {
            e.preventDefault();
            cb.prop('checked', !cb.prop('checked')).trigger('change');
        }
    }
});

/* =========================================================
 * KLIK TOMBOL DETAIL (selain shortcut keyboard)
 * Catatan: id tombol (btnUpdateDetail / btnDeleteDetail) sama
 * dengan nama fungsinya. Pada inline onclick, nama tersebut
 * di-resolve ke elemen (named access document) sehingga muncul
 * error "btnUpdateDetail is not a function". Karena itu klik
 * di-bind dari sini agar dieksekusi pada scope JS normal.
 * ========================================================= */
$(document).on('click', '#btnAddDetail, #btnUpdateDetail, #btnDeleteDetail', function (e) {
    e.preventDefault();

    if ($(this).prop('disabled')) {
        return;
    }

    switch (this.id) {
        case 'btnAddDetail':
            if (typeof btnInputDetail === 'function') {
                btnInputDetail();
            }
            break;
        case 'btnUpdateDetail':
            if (typeof btnUpdateDetail === 'function') {
                btnUpdateDetail();
            }
            break;
        case 'btnDeleteDetail':
            if (typeof btnDeleteDetail === 'function') {
                btnDeleteDetail();
            }
            break;
    }
});

$(document).on('draw.dt', function () {
    salesActiveRowIndex = -1;
    salesDetailTable().find('tbody tr').removeClass('row-active');
});
