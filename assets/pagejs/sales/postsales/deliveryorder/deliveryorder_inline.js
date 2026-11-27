/* Delivery Order â€“ add form (tampilan, field, input inline & tombol
 * mengikuti sales/postsales/addSalesOrder)
 * Hanya dipakai oleh sales/postsales/v_add_deliveryorder_inline.php
 */
(function ($) {
    'use strict';

    if (!document.getElementById('formDO')) {
        return;
    }

    var U = {
        load: HOST_URL + 'sales/postsales/doLoadDeliveryOrder',
        saveHeader: HOST_URL + 'sales/postsales/doSaveDeliveryOrderHeader',
        saveDraft: HOST_URL + 'sales/postsales/doSaveDeliveryOrderDraft',
        saveRow: HOST_URL + 'sales/postsales/doSaveDeliveryOrderRow',
        delRow: HOST_URL + 'sales/postsales/doDeleteDeliveryOrderRow',
        swapRow: HOST_URL + 'sales/postsales/doSwapDeliveryOrderRow',
        listSO: HOST_URL + 'sales/postsales/doListSO',
        listSODetail: HOST_URL + 'sales/postsales/doListSODetail',
        pullSO: HOST_URL + 'sales/postsales/doPullSO',
        stok: HOST_URL + 'sales/postsales/doStokDeliveryOrder',
        stokRows: HOST_URL + 'sales/postsales/doStokDeliveryOrderRows',
        kurs: HOST_URL + 'sales/postsales/doKursDeliveryOrder',
        branchInfo: HOST_URL + 'sales/postsales/getBranchInfoDeliveryOrder',
        nextSuffix: HOST_URL + 'sales/postsales/getNextSuffixDeliveryOrder',
        listItem: HOST_URL + 'api/globalmodule/list_item',
        listBatch: HOST_URL + 'api/globalmodule/list_batch_item',
        listLocation: HOST_URL + 'api/globalmodule/list_mlocation',
        listUnit: HOST_URL + 'api/globalmodule/list_unit_item',
        listCustomer: HOST_URL + 'api/globalmodule/list_customer',
        listSalesman: HOST_URL + 'api/globalmodule/list_salesman',
        listTax: HOST_URL + 'api/globalmodule/list_tax',
        listCurrency: HOST_URL + 'api/globalmodule/list_currency',
        listBranch: HOST_URL + 'api/globalmodule/list_branchjob'
    };

    var S = {
        kodeSuffix: '',
        rows: [],
        editIdurut: 0,
        editing: 0,       /* idurut baris yang sedang diedit in-place (0 = tidak ada) */
        openPrice: !!(window.DO_ADD && window.DO_ADD.openPrice === 'true'),
        soSelected: '',
        existing: '',
        submitting: false,
        stok: {},          /* idurut -> { stokakhir, qty, kosong, kurang } dari sc_trx.stkblc */
        docdate: ''        /* tanggal DO (Y-m-d) yang dipakai menghitung stok */
    };

    /* ---------------- helpers ---------------- */

    function num(v) {
        if (v === null || v === undefined || v === '') {
            return 0;
        }
        if (typeof v === 'number') {
            return v;
        }
        var s = String(v).replace(/\s/g, '');
        if (s.indexOf(',') > -1 && s.indexOf('.') > -1) {
            s = s.replace(/,/g, '');
        } else if (s.indexOf(',') > -1) {
            s = s.replace(/,/g, '.');
        }
        var n = parseFloat(s);
        return isNaN(n) ? 0 : n;
    }

    function fmt(v, dec) {
        dec = (dec === undefined) ? 2 : dec;
        return num(v).toLocaleString('en-US', {
            minimumFractionDigits: dec,
            maximumFractionDigits: dec
        });
    }

    /* Normalisasi hasil stok server jadi map idurut -> info stok.
       JSON bisa berupa object (key idurut) atau array (bila idurut berurutan). */
    function stokIndex(map) {
        var out = {};
        if (!map) { return out; }
        if ($.isArray(map)) {
            $.each(map, function (i, it) {
                if (it) { out[String(num(it.idurut))] = it; }
            });
        } else {
            $.each(map, function (k, it) {
                if (it) { out[String(num(it.idurut || k))] = it; }
            });
        }
        return out;
    }

    /* Stok akhir baris: utamakan hasil server (sc_trx.stkblc s/d tanggal DO) */
    function stokOf(idurut, fallback) {
        var info = S.stok[String(num(idurut))] || null;
        return info ? num(info.stokakhir) : num(fallback);
    }

    /* Sel stok + tanda seru bila stok kosong / qty melebihi stok */
    function stokHtml(stok, qty) {
        var s = num(stok);
        var q = num(qty);
        var kosong = (s <= 0);
        var kurang = kosong || q > s;
        var tip = kosong
            ? 'Stok kosong pada tanggal DO - baris ini hanya boleh Simpan Draft'
            : (q > s ? 'Qty melebihi stok akhir (stok ' + fmt(s, 2) + ')' : 'Stok akhir s/d tanggal DO');

        var html = '<span class="do-stok-val">' + fmt(s, 2) + '</span>';
        if (kurang) {
            html += '<span class="do-stok-warn" title="' + txt(tip) + '">!</span>';
        }
        return html;
    }


    function txt(v) {
        return $('<div>').text(v === null || v === undefined ? '' : String(v)).html();
    }

    function post(url, data, ok, fail) {
        $.ajax({ url: url, type: 'POST', data: data, dataType: 'json' })
            .done(function (res) {
                if (typeof ok === 'function') { ok(res); }
            })
            .fail(function (xhr) {
                if (typeof fail === 'function') {
                    fail(xhr);
                } else {
                    Swal.fire('Error', 'Gagal menghubungi server (' + xhr.status + ')', 'error');
                }
            });
    }

    function get(url, data, ok) {
        $.getJSON(url, data).done(function (res) {
            if (typeof ok === 'function') { ok(res); }
        });
    }

    function setOption(sel, value, label) {
        var $s = $(sel);
        if (!$s.length) { return; }
        var v = $.trim(value || '');
        if (v === '') { return; }
        if (!$s.find('option[value="' + v.replace(/"/g, '\\"') + '"]').length) {
            $s.append(new Option($.trim(label || v), v, true, true));
        }
        $s.val(v).trigger('change.select2');
    }

    function ajaxSelect(sel, url, payload, fmtResult, fmtSelection, onSelect) {
        var $s = $(sel);
        if (!$s.length) { return; }
        $s.select2({
            placeholder: $s.attr('placeholder') || 'Cari / pilih...',
            allowClear: true,
            width: '100%',
            dropdownParent: $(document.body),
            minimumInputLength: 0,
            ajax: {
                url: url,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var d = {
                        _search_: params.term,
                        _page_: params.page || 1,
                        _perpage_: 30,
                        _draw_: true,
                        _start_: 1,
                        term: params.term
                    };
                    if (payload) {
                        $.each(payload, function (k, v) { d[k] = v; });
                    }
                    return d;
                },
                processResults: function (data) {
                    return { results: (data && data.items) ? data.items : [] };
                },
                cache: false
            },
            escapeMarkup: function (m) { return m; },
            templateResult: fmtResult,
            templateSelection: fmtSelection
        }).on('select2:select', function (e) {
            if (typeof onSelect === 'function') {
                onSelect(e.params.data || {});
            }
        });
    }

    function fmtPair(kode, nama) {
        return function (repo) {
            if (repo.loading) { return repo.text; }
            var k = $.trim(repo[kode] || repo.id || '');
            var n = $.trim(repo[nama] || '');
            if (!n || n === k) { return '<div>' + txt(k) + '</div>'; }
            return '<div><strong>' + txt(k) + '</strong></div><small class="text-muted">' + txt(n) + '</small>';
        };
    }

    function fmtPairSel(nama) {
        return function (repo) {
            return $.trim(repo[nama] || repo.id || repo.text || '');
        };
    }

    /* Cabang / Job: kode + NAMA cabang (bukan hanya kode) */
    function fmtBranch(repo) {
        if (repo.loading) { return repo.text; }
        var k = $.trim(repo.idbranch || repo.id || '');
        var n = $.trim(repo.nmbranch || '');
        if (!n || n === k) { return '<div>' + txt(k) + '</div>'; }
        return '<div><strong>' + txt(k) + '</strong> &ndash; ' + txt(n) + '</div>';
    }

    function fmtBranchSel(repo) {
        if (!repo) { return ''; }

        var k = $.trim(repo.idbranch || repo.id || '');
        var n = $.trim(repo.nmbranch || '');

        // Option hasil paintBranch() menyimpan nama cabang di data-nm.
        if (!n && repo.element) {
            n = $.trim($(repo.element).attr('data-nm') || '');
        }

        if (n && n !== k) {
            return k ? (k + ' - ' + n) : n;
        }

        // Pilihan bawaan (option yang sudah terpasang) hanya membawa { id, text }.
        // Text option sudah berisi "kode - NAMA cabang", jadi pakai itu.
        var t = $.trim(repo.text || '');
        if (t && t !== k) { return t; }

        return k || t;
    }

    function fmtCust(repo) {
        if (repo.loading) { return repo.text; }
        var k = $.trim(repo.kdcustomer || repo.id || '');
        var n = $.trim(repo.nmcustomer || '');
        if (!n || n === k) { return '<div>' + txt(k) + '</div>'; }
        return '<div><strong>' + txt(k) + '</strong> - ' + txt(n) + '</div>';
    }

    function fmtCustSel(repo) {
        var k = $.trim(repo.kdcustomer || repo.id || '');
        var n = $.trim(repo.nmcustomer || '');
        return (k && n && k !== n) ? (k + ' - ' + n) : (k || n || repo.text || '');
    }

    function fmtItem(repo) {
        if (repo.loading) { return repo.text; }
        var k = $.trim(repo.idbarang || repo.id || '');
        var n = $.trim(repo.nmbarang || '');
        if (!n || n === k) { return '<div>' + txt(k) + '</div>'; }
        return '<div><strong>' + txt(k) + '</strong></div><small class="text-muted">' + txt(n) + '</small>';
    }

    function fmtItemSel(repo) {
        return $.trim(repo.idbarang || repo.id || repo.text || '');
    }

    function fmtSpec(repo) {
        if (repo.loading) { return repo.text; }
        return '<div>' + txt($.trim(repo.text || repo.idspec || repo.id || '')) + '</div>';
    }

    function fmtSpecSel(repo) {
        return $.trim(repo.text || repo.idspec || repo.id || '');
    }

    function fmtLoc(repo) {
        if (repo.loading) { return repo.text; }
        var k = $.trim(repo.idgudang || repo.id || '');
        var n = $.trim(repo.nmgudang || repo.name || '');
        if (!n || n === k) { return '<div>' + txt(k) + '</div>'; }
        return '<div><strong>' + txt(k) + '</strong></div><small class="text-muted">' + txt(n) + '</small>';
    }

    function fmtLocSel(repo) {
        return $.trim(repo.idgudang || repo.id || repo.text || '');
    }

    /* ---------------- nomor jurnal ---------------- */

    function refreshDocno() {
        var p = $.trim($('#prefix').val() || '').toUpperCase();
        var i = $.trim($('#infix').val() || '');
        var s = $.trim($('#suffix').val() || '').toUpperCase();

        $('#prefix').val(p);
        $('#suffix').val(s);

        if (p === '' || i === '' || s === '') {
            $('#docno').val('');
            return false;
        }

        $('#docno').val((p + '/' + i + '/' + s).substring(0, 30));
        return true;
    }

    function nextSuffix() {
        var p = $.trim($('#prefix').val() || '');
        var i = $.trim($('#infix').val() || '');
        var k = $.trim(S.kodeSuffix || '');

        if (p === '' || i === '' || k === '') {
            refreshDocno();
            return;
        }

        get(U.nextSuffix, { prefix: p, infix: i, kode_suffix: k }, function (res) {
            if (res && res.success && res.suffix) {
                $('#suffix').val($.trim(res.suffix));
            }
            refreshDocno();
        });
    }

    function loadBranchInfo() {
        var idbranch = $.trim($('#cabang').val() || '');
        if (idbranch === '') { return; }

        get(U.branchInfo, { idbranch: idbranch }, function (res) {
            if (!res || !res.success) {
                refreshDocno();
                return;
            }

            S.kodeSuffix = $.trim(res.kode_suffix || '');
            $('#infix').val($.trim(res.infix || ''));

            if ($.trim($('#prefix').val() || '') === '' && $.trim(res.prefix || '') !== '') {
                $('#prefix').val($.trim(res.prefix));
            }

            if (res.currcode && $('#currcode option[value="' + $.trim(res.currcode) + '"]').length) {
                $('#currcode').val($.trim(res.currcode)).trigger('change.select2');
            }
            if (res.idtax && $('#idtax option[value="' + $.trim(res.idtax) + '"]').length) {
                $('#idtax').val($.trim(res.idtax)).trigger('change.select2');
            }

            if (S.existing === '') {
                nextSuffix();
            } else {
                refreshDocno();
            }
        });
    }


    /* ---------------- header ---------------- */

    /* Label cabang: "kode - NAMA" (nama dari master branchjob / session) */
    function branchLabel(id, name) {
        id   = $.trim(id || '');
        name = $.trim(name || '');

        if (name && name !== id) { return id ? (id + ' - ' + name) : name; }

        return id || name;
    }

    /* Pasang / perbarui option cabang: value = kode, text = kode - NAMA */
    function paintBranch($cabang, id, name) {
        id = $.trim(id || '');
        if (id === '') { return; }

        var label = branchLabel(id, name);
        var $opt  = $cabang.find('option').filter(function () {
            return $.trim($(this).val() || '') === id;
        }).first();

        if (!$opt.length) {
            $opt = $(new Option(label, id, true, true));
            $cabang.append($opt);
        }

        $opt.text(label).attr('data-nm', $.trim(name || ''));
        $cabang.val(id).trigger('change.select2');
    }

    /* Lengkapi NAMA cabang dari master (sc_mst.branchjob) bila session tanpa nama */
    function ensureBranchName(sid, sname, $cabang) {
        if (!sid || $.trim(sname || '') !== '') { return; }

        $.getJSON(U.listBranch, { var: sid }).done(function (res) {
            var items = (res && res.items) ? res.items : [];
            if (!items.length) { return; }

            var item = items[0];
            var nm   = $.trim(item.nmbranch || '');
            if (nm === '') { return; }

            paintBranch($cabang, $.trim(item.idbranch || item.id || sid), nm);
        });
    }

    function initBranch() {
        var $cabang = $('#cabang');
        if (!$cabang.length) { return; }

        var sid = $.trim($cabang.attr('data-session-branch-id') || '');
        var sname = $.trim($cabang.attr('data-session-branch-name') || '');
        var canChoose = String($cabang.attr('data-can-choose-branch') || '0') === '1';

        // Tampilkan NAMA cabang (kode - nama), bukan hanya kodenya.
        if (sid) { paintBranch($cabang, sid, sname); }

        ajaxSelect('#cabang', U.listBranch, null, fmtBranch, fmtBranchSel, loadBranchInfo);

        if (!canChoose && sid) {
            $cabang.prop('disabled', true);
        }

        // Bila session tidak menyimpan nama cabang, ambil dari master branchjob.
        ensureBranchName(sid, sname, $cabang);

        loadBranchInfo();
    }

    function initDates() {
        var $d = $('#docdate');
        if (!$d.length) { return; }

        var v = $.trim($d.val());
        if (/^\d{4}-\d{2}-\d{2}/.test(v)) {
            var p = v.substring(0, 10).split('-');
            $d.val(p[2] + '-' + p[1] + '-' + p[0]);
        }
        if (!$d.val()) {
            var t = new Date();
            $d.val(('0' + t.getDate()).slice(-2) + '-' + ('0' + (t.getMonth() + 1)).slice(-2) + '-' + t.getFullYear());
        }

        if ($.fn.daterangepicker) {
            $d.daterangepicker({
                autoUpdateInput: false,
                singleDatePicker: true,
                showDropdowns: true,
                locale: { format: 'DD-MM-YYYY' },
                cancelLabel: 'Clear'
            });
            $d.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY'));
            });
            $d.on('cancel.daterangepicker', function () {
                $(this).val('');
            });
        }
    }

    function initCustomer() {
        ajaxSelect('#kdcustomer', U.listCustomer, null, fmtCust, fmtCustSel, function (data) {
            $('#nmcustomer').val($.trim(data.nmcustomer || ''));
            $('#alamatcustomer').val($.trim(data.alamat_kantor || ''));
            $('#gradecustomer').val($.trim(data.grade || ''));
            $('#jthtempo').val($.trim(data.jthtempo || ''));

            if (!$.trim($('#kdcustomerdeliv').val())) {
                setOption('#kdcustomerdeliv', $.trim(data.kdcustomer || ''), $.trim(data.nmcustomer || ''));
                $('#nmcustomerdeliv').val($.trim(data.nmcustomer || ''));
                $('#alamatcustomerdeliv').val($.trim(data.alamat_kantor || ''));
            }
        });

        ajaxSelect('#kdcustomerdeliv', U.listCustomer, null, fmtCust, fmtCustSel, function (data) {
            $('#nmcustomerdeliv').val($.trim(data.nmcustomer || ''));
            $('#alamatcustomerdeliv').val($.trim(data.alamat_kantor || ''));
        });
    }

    function initSalesman() {
        ajaxSelect('#kdsalesman', U.listSalesman, null,
            fmtPair('kdsalesman', 'nmsalesman'),
            fmtPairSel('nmsalesman'));
    }

    function initTax() {
        ajaxSelect('#idtax', U.listTax, null,
            fmtPair('idtax', 'nmtax'),
            fmtPairSel('nmtax'));
    }

    function initCurrency() {
        ajaxSelect('#currcode', U.listCurrency, null,
            fmtPair('currcode', 'nmcurrency'),
            fmtPairSel('currcode'));

        if (!$.trim($('#kurs').val() || '')) {
            $('#kurs').val('1');
        }

        /* Kurs otomatis dari sc_mst.exchangerate saat mata uang / tanggal berubah */
        $('#currcode').on('change', function () { refreshKurs(true); });
        $('#docdate').on('change apply.daterangepicker', function () { refreshKurs(false); refreshStokAll(); });
    }

    /**
     * Ambil kurs dari sc_mst.exchangerate (rate terakhir s/d docdate,
     * fallback rate terakhir). force=false → jangan timpa kurs yang sudah ada.
     */
    function refreshKurs(force) {
        var currcode = $.trim($('#currcode').val() || '');
        if (currcode === '') { return; }

        var isIdr = (currcode.toUpperCase() === 'IDR');
        if (!force && !isIdr && num($('#kurs').val()) > 1) { return; }

        post(U.kurs, {
            currcode: currcode,
            docdate: $.trim($('#docdate').val() || '')
        }, function (res) {
            if (!res || !res.success) { return; }

            var k = num(res.kurs);
            if (k <= 0) { return; }

            if (isIdr) { k = 1; }
            $('#kurs').val(fmt(k, 4).replace(/,/g, ''));
        });
    }


    function collectHeader() {
        return {
            docno: refreshDocno() ? $.trim($('#docno').val()) : '',
            docdate: $.trim($('#docdate').val()),
            cabang: $.trim($('#cabang').val()),
            pemohon: $.trim($('#pemohon').val()),
            kdcustomer: $.trim($('#kdcustomer').val()),
            nmcustomer: $.trim($('#nmcustomer').val()),
            alamatcustomer: $.trim($('#alamatcustomer').val()),
            gradecustomer: $.trim($('#gradecustomer').val()),
            kdcustomerdeliv: $.trim($('#kdcustomerdeliv').val()),
            nmcustomerdeliv: $.trim($('#nmcustomerdeliv').val()),
            alamatcustomerdeliv: $.trim($('#alamatcustomerdeliv').val()),
            kdsalesman: $.trim($('#kdsalesman').val()),
            jthtempo: $.trim($('#jthtempo').val()),
            idtax: $.trim($('#idtax').val()),
            isinclusive: $('#isinclusive').is(':checked') ? 'true' : 'false',
            isopenprice: $('#isopenprice').is(':checked') ? 'true' : 'false',
            currcode: $.trim($('#currcode').val()),
            kurs: num($('#kurs').val()) || 1,
            carabayar: $.trim($('#carabayar').val()),
            keterangan: $.trim($('#keterangan').val()),
            docnoso: $.trim($('#docnoso').val()),
            idrev: $.trim($('#idrev').val())
        };
    }

    function saveHeader(ok, silent) {
        var data = collectHeader();

        if (!data.docno) {
            if (!silent) { Swal.fire('Perhatian', 'Nomor DO belum lengkap (prefix / infix / suffix).', 'warning'); }
            return;
        }
        if (!data.kdcustomer) {
            if (!silent) { Swal.fire('Perhatian', 'Customer wajib dipilih.', 'warning'); }
            return;
        }

        post(U.saveHeader, data, function (res) {
            if (!res || !res.success) {
                Swal.fire('Gagal', (res && res.message) || 'Header DO gagal disimpan', 'error');
                return;
            }
            if (res.header) { applyHeader(res.header); }
            if (typeof ok === 'function') { ok(res.header || null); }
        });
    }

    function applyHeader(h) {
        if (!h) { return; }

        if (h.kdcustomer) { setOption('#kdcustomer', $.trim(h.kdcustomer), $.trim(h.nmcustomer || h.kdcustomer)); }
        if (h.kdcustomerdeliv) { setOption('#kdcustomerdeliv', $.trim(h.kdcustomerdeliv), $.trim(h.nmcustomerdeliv || h.kdcustomerdeliv)); }
        if (h.kdsalesman) { setOption('#kdsalesman', $.trim(h.kdsalesman), $.trim(h.kdsalesman)); }
        if (h.idtax) { setOption('#idtax', $.trim(h.idtax), $.trim(h.idtax)); }
        if (h.currcode) { setOption('#currcode', $.trim(h.currcode), $.trim(h.currcode)); }

        if (h.nmcustomer) { $('#nmcustomer').val($.trim(h.nmcustomer)); }
        if (h.alamatcustomer) { $('#alamatcustomer').val($.trim(h.alamatcustomer)); }
        if (h.gradecustomer) { $('#gradecustomer').val($.trim(h.gradecustomer)); }
        if (h.jthtempo !== undefined && h.jthtempo !== null) { $('#jthtempo').val($.trim(String(h.jthtempo))); }
        if (h.nmcustomerdeliv) { $('#nmcustomerdeliv').val($.trim(h.nmcustomerdeliv)); }
        if (h.alamatcustomerdeliv) { $('#alamatcustomerdeliv').val($.trim(h.alamatcustomerdeliv)); }
        if (h.pemohon) { $('#pemohon').val($.trim(h.pemohon)); }
        if (h.docnoso) { $('#docnoso').val($.trim(h.docnoso)); }
        if (h.idrev) { $('#idrev').val($.trim(h.idrev)); }
        if (h.keterangan) { $('#keterangan').val($.trim(h.keterangan)); }
        if (h.carabayar) { $('#carabayar').val($.trim(h.carabayar)); }
        if (h.kurs !== undefined && h.kurs !== null) { $('#kurs').val(num(h.kurs)); }

        if (h.isinclusive !== undefined) {
            $('#isinclusive').prop('checked', String(h.isinclusive).toLowerCase() === 'true');
        }
        if (h.isopenprice !== undefined) {
            var op = String(h.isopenprice).toLowerCase() === 'true';
            $('#isopenprice').prop('checked', op);
            $('#isopenpriceState').text(op ? 'YES' : 'NO');
            S.openPrice = op;
        }

        if (h.docdate) {
            var d = String(h.docdate).substring(0, 10).split('-');
            if (d.length === 3) { $('#docdate').val(d[2] + '-' + d[1] + '-' + d[0]); }
        }

        updateSummary(h);
    }

    function updateSummary(header) {
        var dpp = 0, pajak = 0, total = 0;

        if (header && header.dpp !== undefined && header.dpp !== null && header.total !== undefined) {
            dpp = num(header.dpp);
            pajak = num(header.jumlahpajak);
            total = num(header.total);
        } else {
            $.each(S.rows, function (i, r) {
                dpp += num(r.nilai);
                pajak += num(r.nilaipajak);
            });
            total = dpp + pajak;
        }

        $('#doDpp').val(fmt(dpp, 2));
        $('#doPajak').val(fmt(pajak, 2));
        $('#doTotal').val(fmt(total, 2));
    }


    /* =========================================================
     * INLINE DETAIL ENTRY (pola grid addSalesOrder)
     * Baris input berada di tfoot #tabdeliveryorderdtl / #doInlineFoot
     * ========================================================= */

    function doFoot() {
        return $('#doInlineFoot');
    }

    function doDraft() {
        var $new = doFoot().find('tr.so-inline-row').first();
        if ($new.length) { return $new; }
        /* baris tersimpan yang sedang diedit in-place */
        return $('#tabdeliveryorderdtl tbody tr.so-inline-edit').first();
    }

    function doEditInline() {
        return $('#tabdeliveryorderdtl tbody tr.so-inline-edit').first();
    }

    function doDropRow($tr) {
        if (!$tr || !$tr.length) { return; }

        $tr.find('select').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        $tr.remove();
    }

    /**
     * Tutup baris input yang terbuka (baris baru di footer dan/atau baris edit).
     * restore = true → baris edit dikembalikan dari data server (loadRows).
     */
    function doDestroyDraft(restore, done) {
        var $edit = doEditInline();
        var $new  = doFoot().find('tr.so-inline-row').first();

        var needRestore = ($edit.length > 0 && restore !== false);

        doDropRow($new);
        doDropRow($edit);

        S.editing = 0;
        doSyncEmpty();

        if (needRestore) { loadRows(done); }
        else if (typeof done === 'function') { done(); }
    }

    function doSyncEmpty() {
        var $table = $('#tabdeliveryorderdtl');
        if (!$table.length) { return; }
        $table.toggleClass('so-has-draft', doDraft().length > 0);
    }

    function doFocusItem($tr) {
        setTimeout(function () {
            var $item = $tr.find('select.so-inline-item');
            if ($item.data('select2')) {
                $item.select2('open');
            } else {
                $item.trigger('focus');
            }
        }, 50);
    }

    function doRecalc($tr) {
        if (!$tr || !$tr.length) { return; }

        var qty = num($tr.find('[data-field="qty"]').val());
        var harga = num($tr.find('[data-field="harga"]').val());
        var disc = num($tr.find('[data-field="multidisc"]').val());
        var nilai = qty * harga * (1 - (disc / 100));

        $tr.find('[data-field="nilai"]').val(fmt(nilai, 2));

        var qtyso = num($tr.find('[data-field="qtyso"]').val());
        $tr.find('[data-field="qty"]').toggleClass('is-invalid', qtyso > 0 && qty > qtyso);
    }

    function doResetUnits($tr) {
        var $unit = $tr.find('select[data-field="unit"]');
        if (!$unit.length) { return; }
        $unit.empty().append(new Option('-- Pilih --', ''));
    }

    function doFillUnits($tr, units, selected) {
        var $unit = $tr.find('select[data-field="unit"]');
        if (!$unit.length) { return; }

        var def = $.trim(selected || '');
        var found = false;
        var list = units || [];

        $unit.empty().append(new Option('-- Pilih --', ''));

        $.each(list, function (i, it) {
            var unit = $.trim(it.idunit || it.unit || '');
            if (unit === '') { return; }
            $unit.append(new Option(unit, unit));
            if (unit === def) { found = true; }
        });

        if (def !== '' && !found) {
            $unit.append(new Option(def, def));
        }

        if (def === '' && list.length > 0) {
            def = $.trim(list[0].unit_default || list[0].idunit || list[0].unit || '');
        }

        $unit.val(def || '');
    }

    function doLoadUnits($tr, idbarang, selected) {
        idbarang = $.trim(idbarang || '');
        var def = $.trim(selected || '');

        doResetUnits($tr);

        if (idbarang === '') { return; }

        if (def !== '') {
            $tr.find('select[data-field="unit"]').append(new Option(def, def)).val(def);
        }

        post(U.listUnit, { idbarang: idbarang }, function (res) {
            var items = (res && res.items) ? res.items : [];
            var first = items.length ? $.trim(items[0].unit_default || items[0].idunit || '') : '';
            doFillUnits($tr, items, def || first);
        }, function () {
            doFillUnits($tr, [], def);
        });
    }

    function doRefreshStok($tr) {
        var idbarang = $.trim($tr.find('select.so-inline-item').val() || '');
        var idgudang = $.trim($tr.find('select.so-inline-gudang').val() || '');
        var idspec = $.trim($tr.find('select.so-inline-spec').val() || '');
        var qty = num($tr.find('[data-field="qty"]').val());
        var $cell = $tr.find('.do-stok');

        if (idbarang === '' || idgudang === '') {
            $cell.html(stokHtml(0, qty));
            return;
        }

        /* Stok akhir dari sc_trx.stkblc s/d tanggal DO (header sc_tmp milik user) */
        post(U.stok, {
            docno: $.trim($('#docno').val() || ''),
            docdate: $.trim($('#docdate').val() || ''),
            idbarang: idbarang,
            idgudang: idgudang,
            idspec: idspec,
            qty: qty
        }, function (res) {
            if (res && res.success) {
                $tr.data('stok', num(res.stok));
                $cell.html(stokHtml(res.stok, qty));
            }
        });
    }

    /* Tandai ulang stok baris input saat qty berubah (tanda seru real-time) */
    function doPaintStok($tr) {
        var stok = $tr.data('stok');
        if (stok === undefined) { stok = 0; }
        $tr.find('.do-stok').html(stokHtml(stok, num($tr.find('[data-field="qty"]').val())));
    }


    function doInitSelects($tr) {
        var $item = $tr.find('select.so-inline-item');
        var $spec = $tr.find('select.so-inline-spec');
        var $gudang = $tr.find('select.so-inline-gudang');
        var dropdownParent = $(document.body);

        $item.select2({
            placeholder: 'Cari / pilih barang',
            allowClear: true,
            minimumInputLength: 2,
            width: '100%',
            dropdownParent: dropdownParent,
            ajax: {
                url: U.listItem,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _search_: params.term,
                        _page_: params.page || 1,
                        _draw_: true,
                        _start_: 1,
                        _perpage_: 2,
                        _paramglobal_: '',
                        _parameterx_: '',
                        term: params.term
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: (data && data.items) ? data.items : [],
                        pagination: { more: (params.page * 30) < (data ? data.total_count : 0) }
                    };
                },
                cache: false
            },
            escapeMarkup: function (m) { return m; },
            templateResult: fmtItem,
            templateSelection: fmtItemSel
        }).on('select2:select', function (e) {
            var data = e.params.data || {};
            $(this).find('option:selected').attr('data-nm', $.trim(data.nmbarang || ''));
            doLoadUnits($tr, data.idbarang || data.id || '', data.unit || '');
            $spec.val(null).trigger('change');
            doRefreshStok($tr);
        }).on('select2:clear', function () {
            $(this).find('option:selected').removeAttr('data-nm');
            doResetUnits($tr);
            $spec.val(null).trigger('change');
            $tr.find('.do-stok').text('0.00');
        });

        $spec.select2({
            placeholder: 'Batch / Spec',
            allowClear: true,
            width: '100%',
            dropdownParent: dropdownParent,
            ajax: {
                url: U.listBatch,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _search_: params.term,
                        _page_: params.page || 1,
                        _draw_: true,
                        _start_: 1,
                        _perpage_: 30,
                        _paramglobal_: '',
                        _parameterx_: $.trim($item.val() || ''),
                        term: params.term
                    };
                },
                processResults: function (data) {
                    return { results: (data && data.items) ? data.items : [] };
                },
                cache: false
            },
            escapeMarkup: function (m) { return m; },
            templateResult: fmtSpec,
            templateSelection: fmtSpecSel
        }).on('select2:select select2:clear', function () {
            doRefreshStok($tr);
        });

        $gudang.select2({
            placeholder: 'Gudang...',
            allowClear: true,
            width: '100%',
            dropdownParent: dropdownParent,
            ajax: {
                url: U.listLocation,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _search_: params.term,
                        _page_: params.page || 1,
                        _perpage_: 2,
                        _draw_: true,
                        _start_: 1,
                        term: params.term
                    };
                },
                processResults: function (data) {
                    return { results: (data && data.items) ? data.items : [] };
                },
                cache: false
            },
            escapeMarkup: function (m) { return m; },
            templateResult: fmtLoc,
            templateSelection: fmtLocSel
        }).on('select2:select select2:clear', function () {
            doRefreshStok($tr);
        });
    }


    /* Bangun <tr> input inline (15 kolom mengikuti header grid) */
    function doBuildRow(row) {
        row = row || null;
        var itemSo = row
            ? ('<div><strong>' + txt($.trim(row.idbarangso || '-')) + '</strong></div>' +
               '<small class="text-muted">' + txt($.trim(row.nmbarangso || '')) + '</small>')
            : '<span class="text-muted">-</span>';

        var hintNum = 'Gunakan titik (.) untuk desimal, maksimal 4 angka di belakang koma';

        var $tr = $('<tr class="so-inline-row table-warning"></tr>')

            .append($('<td class="text-center so-col-no"></td>')
                .append('<i class="fa fa-plus-circle" style="color:#16a34a"></i>'))
            .append($('<td></td>').append(itemSo))
            .append($('<td></td>').append('<select class="so-inline-item" style="width:100%"></select>'))
            .append($('<td></td>').append('<select class="so-inline-spec" style="width:100%"></select>'))
            .append($('<td></td>').append('<select class="form-control form-control-sm so-inline-unit" data-field="unit" style="width:100%"><option value="">-- Pilih --</option></select>'))
            .append($('<td></td>').append('<input type="text" class="form-control form-control-sm text-end" data-field="qtyso" readonly tabindex="-1" placeholder="0">'))
            .append($('<td></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="qty" placeholder="0" inputmode="decimal" title="' + hintNum + '">'))
            .append($('<td class="do-col-price"></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="harga" placeholder="0" inputmode="decimal" title="' + hintNum + '">'))
            .append($('<td class="do-col-price"></td>').append('<input type="text" class="form-control form-control-sm text-end" data-field="hargasatuan" readonly tabindex="-1" placeholder="0">'))
            .append($('<td class="do-col-price"></td>').append('<input type="text" class="form-control form-control-sm jaccseparator text-end" data-field="multidisc" placeholder="0" inputmode="decimal" title="' + hintNum + '">'))
            .append($('<td class="do-col-price"></td>').append('<input type="text" class="form-control form-control-sm text-end" data-field="nilai" readonly tabindex="-1" placeholder="0" title="Nilai dihitung otomatis">'))
            .append($('<td class="text-end so-col-stok do-stok"></td>'))
            .append($('<td></td>').append('<select class="so-inline-gudang" style="width:100%"></select>'))
            .append($('<td></td>').append('<input type="text" class="form-control form-control-sm" data-field="description" maxlength="200" placeholder="Keterangan" style="text-transform:uppercase">'))

            .append($('<td class="text-center so-col-aksi"></td>')
                .append('<button type="button" class="so-inline-btn so-inline-save" title="Simpan baris (Enter)"><i class="fa fa-check"></i></button> ')
                .append('<button type="button" class="so-inline-btn so-inline-cancel" title="Batal (Esc)"><i class="fa fa-times"></i></button>'));

        $tr.data('row', row);

        if (row) {
            if (row.idbarang) {
                $tr.find('select.so-inline-item').append(new Option($.trim(row.idbarang), $.trim(row.idbarang), true, true));
                $tr.find('select.so-inline-item').find('option:selected').attr('data-nm', $.trim(row.nmbarang || ''));
            }
            if (row.idspec) {
                $tr.find('select.so-inline-spec').append(new Option($.trim(row.idspec), $.trim(row.idspec), true, true));
            }
            if (row.idgudang) {
                $tr.find('select.so-inline-gudang').append(new Option($.trim(row.idgudang), $.trim(row.idgudang), true, true));
            }

            $tr.find('[data-field="qtyso"]').val(num(row.qtyso));
            $tr.find('[data-field="qty"]').val(num(row.qty));
            $tr.find('[data-field="harga"]').val(num(row.harga));
            $tr.find('[data-field="hargasatuan"]').val(num(row.hargasatuan));
            $tr.find('[data-field="multidisc"]').val(num(row.multidisc));
            $tr.find('[data-field="description"]').val($.trim(row.description || ''));
            $tr.data('stok', num(row.stokakhir));
            $tr.find('.do-stok').text(fmt(row.stokakhir, 2));
        }

        return $tr;
    }

    /* Pasang select2 + satuan + hitung ulang untuk baris input */
    function doInitInputRow($tr, row) {
        if ($tr[0] && typeof $tr[0].scrollIntoView === 'function') {
            $tr[0].scrollIntoView({ block: 'nearest' });
        }

        doInitSelects($tr);
        doLoadUnits($tr, row ? $.trim(row.idbarang) : '', row ? $.trim(row.unit) : '');
        doRefreshStok($tr);
        doRecalc($tr);
        doSyncEmpty();
        doFocusItem($tr);
    }

    /* Tambah baris baru (footer grid) */
    function doAddRow(row) {
        var $foot = doFoot();
        if (!$foot.length) { return $(); }

        var $footRow = $foot.find('tr.so-inline-row').first();
        if ($footRow.length) {
            if (!row) { doFocusItem($footRow); return $footRow; }
            doDropRow($footRow);
        }

        if (!row) {
            var $edit = doEditInline();
            if ($edit.length) {
                // Selesaikan dulu baris edit (data dikembalikan dari server), lalu buka baris baru.
                doDestroyDraft(true, function () { doAddRow(null); });
                return $edit;
            }
        }

        var $tr = doBuildRow(row);
        $foot.append($tr);
        doInitInputRow($tr, row);

        return $tr;
    }

    /* Edit baris tersimpan LANGSUNG pada baris tersebut (bukan baris baru di footer) */
    function doEditRow(row) {
        if (!row) { return; }

        var id = num(row.idurut);
        if (!id) { doAddRow(row); return; }

        var $edit = doEditInline();
        if ($edit.length) {
            if (num($edit.attr('data-idurut')) === id) { doFocusItem($edit); return; }
            doDestroyDraft(true, function () { doEditRow(row); });
            return;
        }

        var $footRow = doFoot().find('tr.so-inline-row').first();
        if ($footRow.length) { doDropRow($footRow); }

        var $orig = $('#tabdeliveryorderdtl tbody tr[data-idurut="' + id + '"]');
        if (!$orig.length) { doAddRow(row); return; }

        var $tr = doBuildRow(row);
        $tr.removeClass('so-inline-row').addClass('so-inline-edit');
        $tr.attr('data-idurut', id).attr('data-view', $orig.attr('data-view') || '');

        $orig.replaceWith($tr);
        S.editing = id;

        doInitInputRow($tr, row);
    }


    function doPayload($tr) {
        var origin = $tr.data('row') || null;
        var idbarang = $.trim($tr.find('select.so-inline-item').val() || '');
        var nmbarang = $.trim($tr.find('select.so-inline-item option:selected').attr('data-nm') || '');

        if (!nmbarang && origin) { nmbarang = $.trim(origin.nmbarang || ''); }

        return {
            idurut: origin ? num(origin.idurut) : 0,
            docnoso: $.trim(origin ? (origin.docnoso || '') : $('#docnoso').val()),
            idrev: $.trim(origin ? (origin.idrev || '') : $('#idrev').val()),
            idurutso: origin ? num(origin.idurutso) : 0,
            uniqueid: $.trim(origin ? (origin.uniqueid || '') : ''),
            uniqueidso: $.trim(origin ? (origin.uniqueidso || '') : ''),
            idbarangso: $.trim(origin ? (origin.idbarangso || '') : ''),
            nmbarangso: $.trim(origin ? (origin.nmbarangso || '') : ''),
            idbarang: idbarang,
            nmbarang: nmbarang || idbarang,
            idgudang: $.trim($tr.find('select.so-inline-gudang').val() || ''),
            idspec: $.trim($tr.find('select.so-inline-spec').val() || ''),
            unit: $.trim($tr.find('select[data-field="unit"]').val() || ''),
            qtyso: num($tr.find('[data-field="qtyso"]').val()),
            qty: num($tr.find('[data-field="qty"]').val()),
            harga: num($tr.find('[data-field="harga"]').val()),
            hargasatuan: origin ? num($tr.find('[data-field="hargasatuan"]').val()) : num($tr.find('[data-field="harga"]').val()),
            multidisc: num($tr.find('[data-field="multidisc"]').val()),
            nilai: num($tr.find('[data-field="nilai"]').val()),
            idtax: $.trim($('#idtax').val()),
            currcode: $.trim($('#currcode').val()),
            kurs: num($('#kurs').val()) || 1,
            stokakhir: num($tr.data('stok')),
            description: $.trim($tr.find('[data-field="description"]').val())
        };
    }

    function doSave($tr, after) {
        if (!$tr || !$tr.length) { return; }

        var p = doPayload($tr);

        if (!p.idbarang) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Item DO wajib dipilih' });
            doFocusItem($tr);
            return;
        }
        if (!p.unit) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Satuan wajib dipilih' });
            $tr.find('select[data-field="unit"]').trigger('focus');
            return;
        }
        if (p.qty <= 0) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Qty wajib diisi dan harus lebih besar dari 0' });
            $tr.find('[data-field="qty"]').trigger('focus');
            return;
        }
        if (!p.idgudang) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Gudang wajib dipilih' });
            var $g = $tr.find('select.so-inline-gudang');
            if ($g.data('select2')) { $g.select2('open'); } else { $g.trigger('focus'); }
            return;
        }
        if (p.qtyso > 0 && p.qty > p.qtyso) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Qty DO tidak boleh melebihi Qty SO (' + fmt(p.qtyso, 2) + ')' });
            $tr.find('[data-field="qty"]').trigger('focus');
            return;
        }
        if (!refreshDocno()) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Nomor DO belum lengkap' });
            return;
        }
        if (!$.trim($('#kdcustomer').val())) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Customer wajib dipilih' });
            return;
        }

        var $btn = $tr.find('.so-inline-save').prop('disabled', true);

        saveHeader(function () {
            p.docno = $.trim($('#docno').val());

            post(U.saveRow, p, function (res) {
                $btn.prop('disabled', false);
                if (!res || !res.success) {
                    Swal.fire('Gagal', (res && res.message) || 'Item DO gagal disimpan', 'error');
                    return;
                }
                doDestroyDraft(false);
                loadRows();
                if (res.header) { applyHeader(res.header); }
                if (typeof after === 'function') { after(res); }
            }, function () {
                $btn.prop('disabled', false);
            });
        }, true);
    }


    /* ---------------- daftar baris tersimpan ---------------- */

    function loadRows(done) {
        var docno = $.trim($('#docno').val());
        if (!docno) {
            S.rows = [];
            S.stok = {};
            S.docdate = '';
            renderRows();
            updateSummary(null);
            if (typeof done === 'function') { done(); }
            return;
        }

        get(U.load, { docno: docno }, function (res) {
            if (!res || !res.success) {
                if (typeof done === 'function') { done(); }
                return;
            }
            if (res.header) { applyHeader(res.header); }

            /* Stok akhir server: sc_trx.stkblc s/d docdate header (milik inputby user) */
            S.stok    = stokIndex(res.stok);
            S.docdate = res.docdate || '';

            S.rows = res.rows || [];
            $.each(S.rows, function (i, r) {
                r.stokakhir = stokOf(r.idurut, r.stokakhir);
            });

            renderRows();
            updateSummary(res.header || null);
            if (typeof done === 'function') { done(); }
        });
    }

    /* Refresh stok akhir semua baris tersimpan (dipakai saat tanggal DO berubah) */
    function refreshStokAll() {
        var docno = $.trim($('#docno').val());
        if (!docno || !S.rows.length) { return; }

        post(U.stokRows, {
            docno: docno,
            docdate: $.trim($('#docdate').val() || '')
        }, function (res) {
            if (!res || !res.success) { return; }

            S.stok = stokIndex(res.rows);
            S.docdate = res.docdate || '';

            $.each(S.rows, function (i, r) {
                r.stokakhir = stokOf(r.idurut, r.stokakhir);

                var $cell = $('#tabdeliveryorderdtl tbody tr[data-idurut="' + num(r.idurut) + '"] td.so-col-stok');
                if ($cell.length && !$cell.hasClass('do-stok')) {
                    $cell.html(stokHtml(r.stokakhir, r.qty));
                }
            });
        });
    }



    function renderRows() {
        var $tb = $('#tabdeliveryorderdtl tbody');
        $tb.empty();

        if (!S.rows.length) {
            $tb.append('<tr><td colspan="15" class="dataTables_empty"><i class="fa fa-plus-circle"></i> Belum ada item &mdash; tekan Alt+N atau klik tombol + di atas tabel</td></tr>');
            doSyncEmpty();
            return;
        }

        var html = '';

        $.each(S.rows, function (i, r) {
            var sub = ($.trim(r.idbarang) !== $.trim(r.idbarangso) && $.trim(r.idbarangso) !== '');
            var over = (num(r.qtyso) > 0 && num(r.qty) > num(r.qtyso));

            var itemSo = r.idbarangso
                ? '<div><strong>' + txt($.trim(r.idbarangso)) + '</strong></div><small class="text-muted">' + txt($.trim(r.nmbarangso)) + '</small>'
                : '<span class="text-muted">-</span>';

            var itemDo = '<div><strong>' + txt($.trim(r.idbarang)) + '</strong>'
                + (sub ? ' <span class="badge bg-warning text-dark" title="Barang disubstitusi dari SO">SUBST</span>' : '')
                + '</div><small class="text-muted">' + txt($.trim(r.nmbarang)) + '</small>';

            html += '<tr data-idurut="' + num(r.idurut) + '" data-view="' + num(r.idurut_view) + '">'
                + '<td class="text-center"><input type="checkbox" class="row-check" value="' + num(r.idurut) + '"></td>'
                + '<td>' + itemSo + '</td>'
                + '<td>' + itemDo + '</td>'
                + '<td>' + txt($.trim(r.idspec)) + '</td>'
                + '<td class="so-col-unit-cell">' + txt($.trim(r.unit)) + '</td>'
                + '<td class="text-end">' + fmt(r.qtyso, 2) + '</td>'
                + '<td class="text-end' + (over ? ' text-danger fw-bold' : '') + '">' + fmt(r.qty, 2) + '</td>'
                + '<td class="text-end do-col-price">' + fmt(r.harga, 2) + '</td>'
                + '<td class="text-end do-col-price">' + fmt(r.hargasatuan, 2) + '</td>'
                + '<td class="text-end do-col-price">' + fmt(r.multidisc, 2) + '</td>'
                + '<td class="text-end do-col-price">' + fmt(r.nilai, 2) + '</td>'
                + '<td class="text-end so-col-stok">' + stokHtml(stokOf(r.idurut, r.stokakhir), r.qty) + '</td>'
                + '<td>' + txt($.trim(r.idgudang)) + '</td>'
                + '<td>' + txt($.trim(r.description)) + '</td>'
                + '<td class="text-center so-col-aksi-cell">'
                + '<button type="button" class="so-row-btn so-row-btn-edit" data-id="' + num(r.idurut) + '" title="Update baris"><i class="fa fa-pencil"></i></button> '
                + '<button type="button" class="so-row-btn so-row-btn-delete" data-id="' + num(r.idurut) + '" title="Hapus baris"><i class="fa fa-trash"></i></button>'
                + '</td>'
                + '</tr>';
        });

        $tb.html(html);
        doSyncEmpty();
    }

    function findRow(idurut) {
        var found = null;
        $.each(S.rows, function (i, r) {
            if (num(r.idurut) === num(idurut)) { found = r; }
        });
        return found;
    }

    function checkedRows() {
        var list = [];
        $('#tabdeliveryorderdtl tbody input.row-check:checked').each(function () {
            list.push(num($(this).val()));
        });
        return list;
    }


    /* ---------------- ubah urutan / hapus ---------------- */

    function moveRow(idurutView, dir) {
        var docno = $.trim($('#docno').val());
        if (!docno || !idurutView) { return; }

        post(U.swapRow, { docno: docno, idurut_view: idurutView, direction: dir }, function (res) {
            if (!res || !res.success) {
                if (res && res.message) { Swal.fire('Info', res.message, 'info'); }
                return;
            }
            S.rows = res.rows || [];
            renderRows();
            if (res.header) { updateSummary(res.header); }
        });
    }

    function deleteRow(idurut) {
        var docno = $.trim($('#docno').val());
        if (!docno || !idurut) { return; }

        Swal.fire({
            title: 'Hapus item ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.isConfirmed) { return; }

            post(U.delRow, { docno: docno, idurut: idurut }, function (res) {
                if (!res || !res.success) {
                    Swal.fire('Gagal', (res && res.message) || 'Item gagal dihapus', 'error');
                    return;
                }
                loadRows();
                if (res.header) { updateSummary(res.header); }
            });
        });
    }

    function editCheckedRow() {
        var ids = checkedRows();
        if (!ids.length) {
            Swal.fire('Info', 'Centang satu baris yang akan diubah.', 'info');
            return;
        }
        if (ids.length > 1) {
            Swal.fire('Info', 'Pilih satu baris saja untuk diubah.', 'info');
            return;
        }

        var row = findRow(ids[0]);
        if (row) { doEditRow(row); }
    }

    function deleteCheckedRow() {
        if (doDraft().length) {
            doDestroyDraft();
            return;
        }

        var ids = checkedRows();
        if (!ids.length) {
            Swal.fire('Info', 'Centang baris yang akan dihapus.', 'info');
            return;
        }
        if (ids.length > 1) {
            Swal.fire('Info', 'Hapus satu baris dalam satu waktu.', 'info');
            return;
        }

        deleteRow(ids[0]);
    }

    function moveChecked(dir) {
        var ids = checkedRows();
        if (ids.length !== 1) {
            Swal.fire('Info', 'Centang satu baris untuk dipindah urutannya.', 'info');
            return;
        }

        var row = findRow(ids[0]);
        if (!row) { return; }

        moveRow(num(row.idurut_view), dir);
    }


    /* ---------------- tarik SO (parent-child, pilih sebagian detail) ---------------- */

    var SO_CACHE = {};   /* docnoso -> { loaded: bool, rows: [] } */

    function soDate(v) {
        var s = String(v || '').substring(0, 10);
        var p = s.split('-');
        return (p.length === 3) ? (p[2] + '-' + p[1] + '-' + p[0]) : s;
    }

    function soRow(no) {
        return {
            parent: $('#tblSOList tr.so-pick-parent[data-docnoso="' + no + '"]'),
            child: $('#tblSOList tr.so-pick-child[data-docnoso="' + no + '"]')
        };
    }

    /* Overlay loading modal Tarik SO (rapi, tidak menggeser layout tabel) */
    function soLoading(on, text) {
        var $ov = $('#soPickLoading');
        if (!$ov.length) { return; }
        if (on) {
            $('#soPickLoadingText').text(text || 'Memuat daftar Sales Order...');
            $ov.addClass('show');
        } else {
            $ov.removeClass('show');
        }
    }

    function loadSOList() {
        var $tb = $('#tblSOList tbody');
        SO_CACHE = {};
        $('#soChkAll').prop('checked', false).prop('indeterminate', false);
        $tb.html('<tr><td colspan="8" class="so-pick-empty"><i class="fa fa-spinner fa-spin"></i> Memuat daftar SO...</td></tr>');
        soLoading(true, 'Memuat daftar Sales Order...');

        post(U.listSO, {
            kdcustomer: $.trim($('#kdcustomer').val() || ''),
            search: $.trim($('#soSearch').val() || '')
        }, function (res) {
            soLoading(false);
            var rows = (res && res.rows) ? res.rows : [];

            if (!rows.length) {
                $tb.html('<tr><td colspan="8" class="so-pick-empty">Tidak ada SO outstanding yang bisa ditarik untuk customer ini.</td></tr>');
                soUpdateCount();
                return;
            }

            var h = '';
            $.each(rows, function (i, r) {
                var no = $.trim(r.docno);
                var sisaNilai = num(r.sisa_nilai);
                var sisaQty   = num(r.sisa_qty);
                var jmlBaris  = num(r.jml_baris);

                h += '<tr class="so-pick-parent" data-docnoso="' + txt(no) + '">'
                    + '<td class="so-pick-toggle"><i class="fa fa-caret-right"></i></td>'
                    + '<td class="text-center"><input type="checkbox" class="so-pick-head" value="' + txt(no) + '" title="Centang semua detail SO ini"></td>'
                    + '<td class="so-pick-col-no">' + txt(no) + '</td>'
                    + '<td>' + txt(soDate(r.docdate)) + '</td>'
                    + '<td class="so-pick-col-cust">' + txt($.trim(r.nmcustomer || '')) + '</td>'
                    + '<td class="text-end do-col-price">' + fmt(r.total, 2) + '</td>'
                    + '<td class="text-end" title="Sisa ' + txt(fmt(sisaQty, 2)) + ' qty dari ' + txt(jmlBaris) + ' baris">'
                    + '<span class="so-pick-sisa do-col-price">' + fmt(sisaNilai, 2) + '</span>'
                    + '<span class="so-pick-sisaq">' + fmt(sisaQty, 2) + ' qty / ' + txt(jmlBaris) + ' item</span>'
                    + '</td>'
                    + '<td>' + txt($.trim(r.status || '')) + '</td>'
                    + '</tr>'
                    + '<tr class="so-pick-child" data-docnoso="' + txt(no) + '" style="display:none;">'
                    + '<td colspan="8" class="so-pick-child-cell"></td>'
                    + '</tr>';
            });

            $tb.html(h);
            soUpdateCount();
        }, function () {
            soLoading(false);
            $tb.html('<tr><td colspan="8" class="so-pick-empty text-danger">Gagal memuat daftar SO.</td></tr>');
        });
    }

    function soRenderChild(no, cb) {
        var cache = SO_CACHE[no] || (SO_CACHE[no] = { loaded: false, rows: [] });
        var $cell = $('#tblSOList tr.so-pick-child[data-docnoso="' + no + '"] td.so-pick-child-cell');

        if (!$cell.length) {
            if (cb) { cb(); }
            return;
        }

        if (!cache.loaded) {
            $cell.html('<div class="so-pick-loading"><i class="fa fa-spinner fa-spin"></i> Memuat detail SO...</div>');

            post(U.listSODetail, {
                docnoso: no,
                docno: $.trim($('#docno').val() || '')
            }, function (res) {
                cache.loaded = true;
                cache.rows = (res && res.rows) ? res.rows : [];
                soRenderChild(no, cb);
            }, function () {
                cache.loaded = true;
                cache.rows = [];
                soRenderChild(no, cb);
            });

            return;
        }

        if (!cache.rows.length) {
            $cell.html('<div class="so-pick-loading">Tidak ada detail pada SO ini.</div>');
            if (cb) { cb(); }
            return;
        }

        var h = '<div class="so-pick-dtl-wrap">'
            + '<div class="so-pick-dtl-title"><i class="fa fa-level-down"></i> Detail SO <b>' + txt(no) + '</b></div>'
            + '<table class="table table-sm so-pick-dtl mb-0"><thead><tr>'
            + '<th width="22"><input type="checkbox" class="so-pick-dtl-all" data-docnoso="' + txt(no) + '" title="Centang semua detail SO ini"></th>'
            + '<th>Item</th>'
            + '<th width="96">Spec</th>'
            + '<th width="44">Sat</th>'
            + '<th width="66" class="text-end">Qty SO</th>'
            + '<th width="66" class="text-end">Qty DO</th>'
            + '<th width="66" class="text-end">Sisa</th>'
            + '<th width="94" class="text-end do-col-price">Harga</th>'
            + '</tr></thead><tbody>';

        $.each(cache.rows, function (i, d) {
            var uid   = $.trim(d.uniqueid || '');
            var sisa  = num(d.qtysisa);
            var sudah = num(d.sudah) === 1;
            var off   = (sudah || sisa <= 0);
            var ket   = sudah ? 'Sudah ditarik ke DO ini'
                              : (sisa <= 0 ? 'Sudah ditarik ke DO lain / full DO' : '');
            // Qty DO = qtydo SO + qty yang sudah masuk DO (termasuk draft).
            var qtyDo = num(d.qtydo) + num(d.qtyused);

            h += '<tr class="so-pick-dtl-row' + (off ? ' so-pick-dtl-done' : '') + '">'
                + '<td class="text-center">'
                + '<input type="checkbox" class="so-pick-dtl-chk" data-docnoso="' + txt(no) + '" value="' + txt(uid) + '"'
                + (off ? ' disabled title="' + txt(ket) + '"' : '') + '>'
                + '</td>'
                + '<td><span class="so-pick-tree">&#8627;</span><span class="so-pick-item">' + txt($.trim(d.idbarang || '')) + '</span> ' + txt($.trim(d.nmbarang || '')) + '</td>'
                + '<td>' + txt($.trim(d.idspec || '')) + '</td>'
                + '<td>' + txt($.trim(d.unit || '')) + '</td>'
                + '<td class="text-end">' + fmt(d.qty, 2) + '</td>'
                + '<td class="text-end">' + fmt(qtyDo, 2) + '</td>'
                + '<td class="text-end' + (sisa <= 0 ? ' text-danger' : '') + '">' + fmt(sisa, 2) + '</td>'
                + '<td class="text-end do-col-price">' + fmt(d.harga, 2) + '</td>'
                + '</tr>';
        });

        h += '</tbody></table></div>';
        $cell.html(h);

        soSyncHead(no);
        if (cb) { cb(); }
    }

    function soOpenChild(no, cb) {
        var r = soRow(no);
        if (!r.child.length) {
            if (cb) { cb(); }
            return;
        }

        r.child.show();
        r.parent.addClass('so-open').find('.so-pick-toggle i').removeClass('fa-caret-right').addClass('fa-caret-down');
        soRenderChild(no, cb);
    }

    function soCloseChild(no) {
        var r = soRow(no);
        if (!r.child.length) { return; }

        r.child.hide();
        r.parent.removeClass('so-open').find('.so-pick-toggle i').removeClass('fa-caret-down').addClass('fa-caret-right');
    }

    function soToggleChild(no) {
        var r = soRow(no);
        if (!r.child.length) { return; }

        if (r.child.is(':visible')) { soCloseChild(no); } else { soOpenChild(no); }
    }

    function soSetAllDetails(no, on) {
        if (on) { soEnsureSingle(no); }
        $('#tblSOList tr.so-pick-child[data-docnoso="' + no + '"] input.so-pick-dtl-chk:enabled').prop('checked', on);
        soSyncAllHeads();
    }

    /* Hanya boleh memilih detail dari satu SO (satu SO = satu DO) */
    function soEnsureSingle(no) {
        var cleared = 0;

        $('#tblSOList input.so-pick-dtl-chk:checked').each(function () {
            if (String($(this).attr('data-docnoso') || '') !== String(no)) {
                $(this).prop('checked', false);
                cleared++;
            }
        });

        if (cleared > 0) {
            $('.so-pick-note').html('<i class="fa fa-info-circle"></i> Satu DO hanya untuk <b>satu SO</b> &mdash; pilihan SO lain otomatis dibatalkan.');
        }

        return cleared;
    }

    function soSyncAllHeads() {
        $('#tblSOList tr.so-pick-parent').each(function () {
            soSyncHead(String($(this).attr('data-docnoso') || ''));
        });
    }

    function soSyncHead(no) {
        var $chk   = $('#tblSOList tr.so-pick-child[data-docnoso="' + no + '"] input.so-pick-dtl-chk:enabled');
        var total  = $chk.length;
        var picked = $chk.filter(':checked').length;

        $('#tblSOList input.so-pick-head[value="' + no + '"]')
            .prop('checked', total > 0 && picked === total)
            .prop('indeterminate', picked > 0 && picked < total);

        $('#tblSOList input.so-pick-dtl-all[data-docnoso="' + no + '"]')
            .prop('checked', total > 0 && picked === total)
            .prop('indeterminate', picked > 0 && picked < total);

        soSyncAll();
    }

    function soSyncAll() {
        var $chk   = $('#tblSOList input.so-pick-dtl-chk:enabled');
        var total  = $chk.length;
        var picked = $chk.filter(':checked').length;

        $('#soChkAll')
            .prop('checked', total > 0 && picked === total)
            .prop('indeterminate', picked > 0 && picked < total);

        soUpdateCount();
    }

    function soUpdateCount() {
        $('#soPickCount').text($('#tblSOList input.so-pick-dtl-chk:checked').length);
    }

    function pullSelectedSO() {
        var picks = {};
        var total = 0;

        $('#tblSOList input.so-pick-dtl-chk:checked').each(function () {
            var no  = String($(this).attr('data-docnoso') || '');
            var uid = $.trim(String($(this).val() || ''));
            if (!no || !uid) { return; }

            if (!picks[no]) { picks[no] = []; }
            picks[no].push(uid);
            total++;
        });

        if (!total) {
            Swal.fire('Perhatian', 'Centang minimal satu detail SO yang akan ditarik.', 'warning');
            return;
        }
        if (!refreshDocno()) {
            Swal.fire('Perhatian', 'Nomor DO belum lengkap.', 'warning');
            return;
        }
        if (!$.trim($('#kdcustomer').val())) {
            Swal.fire('Perhatian', 'Customer wajib dipilih.', 'warning');
            return;
        }

        var list = Object.keys(picks);

        var headSo = $.trim($('#docnoso').val() || '');
        if (S.rows.length && headSo && list[0] !== headSo) {
            Swal.fire('Perhatian', 'Satu DO hanya untuk satu SO. DO ini sudah berisi SO ' + headSo + '.', 'warning');
            return;
        }

        saveHeader(function () {
            $('#btnPullSO').prop('disabled', true);

            var docno = $.trim($('#docno').val());
            var idx = 0;
            var inserted = 0;

            function step() {
                if (idx >= list.length) {
                    $('#btnPullSO').prop('disabled', false);
                    loadRows();
                    $('#modalTarikSO').modal('hide');
                    Swal.fire(
                        'Berhasil',
                        inserted > 0
                            ? inserted + ' detail SO berhasil ditarik ke DO.'
                            : 'Tidak ada detail baru yang ditarik (detail sudah ada di DO ini).',
                        'success'
                    );
                    return;
                }

                var no = list[idx++];

                post(U.pullSO, { docno: docno, docnoso: no, ids: picks[no] }, function (res) {
                    if (!res || !res.success) {
                        $('#btnPullSO').prop('disabled', false);
                        if (inserted > 0) { loadRows(); }
                        Swal.fire('Gagal', (res && res.message) || 'SO gagal ditarik', 'error');
                        return;
                    }

                    inserted += num(res.inserted);
                    if (res.header) { applyHeader(res.header); }
                    step();
                }, function (xhr) {
                    $('#btnPullSO').prop('disabled', false);
                    if (inserted > 0) { loadRows(); }
                    Swal.fire('Error', 'Gagal menghubungi server (' + xhr.status + ')', 'error');
                });
            }

            step();
        }, true);
    }


    /* ---------------- events ---------------- */

    function bindGrid() {
        var $table = $('#tabdeliveryorderdtl');

        $table.on('click', 'tbody tr', function (e) {
            if ($(this).hasClass('so-inline-edit')) { return; }
            if ($(e.target).is('input, select, textarea, button') || $(e.target).closest('.so-row-btn').length) {
                return;
            }
            var $cb = $(this).find('input.row-check');
            if (!$cb.length) { return; }
            $cb.prop('checked', !$cb.prop('checked')).trigger('change');
        });

        $('#checkAll').on('change', function () {
            $table.find('tbody input.row-check').prop('checked', $(this).is(':checked'));
        });

        $table.on('change', 'tbody input.row-check', function () {
            var total = $table.find('tbody input.row-check').length;
            var picked = $table.find('tbody input.row-check:checked').length;
            $('#checkAll').prop('checked', total > 0 && picked === total);
        });

        /* Tombol pensil: edit baris tersebut langsung (in-place) */
        $table.on('click', '.so-row-btn-edit', function () {
            var row = findRow(num($(this).attr('data-id')));
            if (row) { doEditRow(row); }
        });

        $table.on('click', '.so-row-btn-delete', function () {
            deleteRow(num($(this).attr('data-id')));
        });

        $table.on('click', 'tbody td.dataTables_empty', function () {
            doAddRow(null);
        });

        $table.on('click', '.so-inline-save', function () {
            doSave($(this).closest('tr'));
        });

        $table.on('click', '.so-inline-cancel', function () {
            doDestroyDraft();
        });

        $table.on('input change', '[data-field="qty"], [data-field="harga"], [data-field="multidisc"]', function () {
            var $tr = $(this).closest('tr');
            doRecalc($tr);
            if ($(this).attr('data-field') === 'qty') { doPaintStok($tr); }
        });

        $table.on('keydown', 'tr.so-inline-row input, tr.so-inline-row select, tr.so-inline-edit input, tr.so-inline-edit select', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                doSave($(this).closest('tr'));
            } else if (e.key === 'Escape') {
                e.preventDefault();
                doDestroyDraft();
            }
        });
    }

    function bindToolbar() {
        $('#btnAddDetail').on('click', function () { doAddRow(null); });
        $('#btnUpdateDetail').on('click', editCheckedRow);
        $('#btnDeleteDetail').on('click', deleteCheckedRow);
        $('#btnMoveUp').on('click', function () { moveChecked('up'); });
        $('#btnMoveDown').on('click', function () { moveChecked('down'); });

        $('#btnTarikSO').on('click', function () {
            if (!refreshDocno()) {
                Swal.fire('Perhatian', 'Lengkapi nomor DO terlebih dahulu.', 'warning');
                return;
            }
            if (!$.trim($('#kdcustomer').val())) {
                Swal.fire('Perhatian', 'Pilih customer terlebih dahulu.', 'warning');
                return;
            }
            $('#soSearch').val('');
            $('#modalTarikSO').modal('show');
            $('.so-pick-wrap').scrollTop(0);
            loadSOList();
        });

        $('#btnSearchSO').on('click', loadSOList);
        $('#soSearch').on('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); loadSOList(); }
        });

        /* Klik baris SO = expand/collapse detail (parent-child) */
        $('#tblSOList').on('click', 'tr.so-pick-parent', function (e) {
            if ($(e.target).is('input[type="checkbox"]')) { return; }
            soToggleChild(String($(this).attr('data-docnoso') || ''));
        });

        /* Checkbox di baris SO = centang semua detail SO tersebut */
        $('#tblSOList').on('change', 'input.so-pick-head', function () {
            var no = String($(this).val() || '');
            var on = $(this).is(':checked');

            if (on) {
                soOpenChild(no, function () { soSetAllDetails(no, true); });
            } else {
                soSetAllDetails(no, false);
            }
        });

        /* Checkbox "semua" di header tabel detail */
        $('#tblSOList').on('change', 'input.so-pick-dtl-all', function () {
            soSetAllDetails(String($(this).attr('data-docnoso') || ''), $(this).is(':checked'));
        });

        /* Checkbox per detail */
        $('#tblSOList').on('change', 'input.so-pick-dtl-chk', function () {
            var no = String($(this).attr('data-docnoso') || '');

            if ($(this).is(':checked')) {
                soEnsureSingle(no);
                soSyncAllHeads();
            } else {
                soSyncHead(no);
            }
        });

        /* Centang semua: karena satu DO hanya untuk satu SO, hanya SO pertama
           yang seluruh detailnya dicentang. */
        $('#soChkAll').on('change', function () {
            var on = $(this).is(':checked');
            var $parents = $('#tblSOList tr.so-pick-parent');

            if (!on) {
                $parents.each(function () {
                    var no = String($(this).attr('data-docnoso') || '');
                    soSetAllDetails(no, false);
                    soCloseChild(no);
                });
                return;
            }

            var no = String($parents.first().attr('data-docnoso') || '');
            if (!no) { return; }

            soOpenChild(no, function () { soSetAllDetails(no, true); });

            if ($parents.length > 1) {
                $('.so-pick-note').html('<i class="fa fa-info-circle"></i> Satu DO hanya untuk <b>satu SO</b> &mdash; "Centang semua" hanya memilih detail SO pertama.');
            }
        });

        $('#btnCollapseSO').on('click', function () {
            $('#tblSOList tr.so-pick-parent').each(function () {
                soCloseChild(String($(this).attr('data-docnoso') || ''));
            });
        });

        $('#btnPullSO').on('click', pullSelectedSO);

        $('#prefix, #suffix').on('change blur', function () { refreshDocno(); });
        $('#prefix').on('change', nextSuffix);
        $('#cabang').on('change', loadBranchInfo);

        $('#btnSimpanDO').on('click', function () {
            var $btn = $(this).prop('disabled', true);
            var done = function () { $btn.prop('disabled', false); };

            var submitDraft = function () {
                if (!refreshDocno()) {
                    Swal.fire('Perhatian', 'Nomor DO belum lengkap (prefix / infix / suffix).', 'warning');
                    done();
                    return;
                }

                post(U.saveDraft, collectHeader(), function (res) {
                    done();
                    if (!res || !res.success) {
                        Swal.fire('Gagal', (res && res.message) || 'Draft DO gagal disimpan', 'error');
                        return;
                    }

                    var kurang = (res.stok_kurang || []);
                    var noDraft = $.trim(res.docno || '');
                    var html = 'Dokumen tersimpan sebagai <b>DRAFT</b>'
                        + (noDraft ? ' dengan nomor <b>' + txt(noDraft) + '</b>' : '')
                        + '.<br>Qty SO dan stok belum dipotong sampai dokumen difinalkan.';

                    if (kurang.length) {
                        html += '<br><br><b>Stok belum mencukupi (' + kurang.length + ' item):</b><br>'
                            + txt(kurang.slice(0, 8).join(' | ')).replace(/\|/g, '<br>')
                            + (kurang.length > 8 ? '<br>...' : '')
                            + '<br><br>Baris dengan <b>stok kosong</b> belum bisa difinalkan &mdash; gunakan <b>Simpan Draft</b> sampai stok tersedia.';
                    }

                    Swal.fire({
                        icon: kurang.length ? 'warning' : 'success',
                        title: 'Draft tersimpan',
                        html: html,
                        confirmButtonText: 'Tutup & buka daftar DO',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(function () {
                        window.location.href = res.redirect || (HOST_URL + 'sales/postsales/deliveryorder');
                    });
                }, function () {
                    done();
                });
            };

            if (doDraft().length) { doSave(doDraft(), submitDraft); return; }
            submitDraft();
        });

        $('#btnFinalizeDO').on('click', function (e) {
            e.preventDefault();
            finalizeDO();
        });

        $('#formDO').on('submit', function (e) {
            if (S.submitting) { return; }
            e.preventDefault();
            finalizeDO();
        });

        /* global untuk sales_shortcut.js (Ctrl+Q / Alt+N / Ctrl+E / Ctrl+D) */
        window.btnInputDetail = function () { doAddRow(null); };
        window.btnUpdateDetail = editCheckedRow;
        window.btnDeleteDetail = deleteCheckedRow;

    }

    function finalizeDO() {
        if (doDraft().length) {
            Swal.fire('Perhatian', 'Masih ada baris yang belum disimpan.', 'warning');
            return;
        }
        if (!refreshDocno()) {
            Swal.fire('Perhatian', 'Nomor DO belum lengkap (prefix / infix / suffix).', 'warning');
            return;
        }
        if (!$.trim($('#kdcustomer').val())) {
            Swal.fire('Perhatian', 'Customer wajib dipilih.', 'warning');
            return;
        }
        if (!S.rows.length) {
            Swal.fire('Perhatian', 'Detail DO masih kosong.', 'warning');
            return;
        }

        /* Stok kosong (gudang + idbarang + spec) hanya boleh disimpan sebagai draft */
        var stokKosong = [];
        $.each(S.rows, function (i, r) {
            if (num(r.stokakhir) <= 0) {
                stokKosong.push(
                    $.trim(r.idbarang || '') + ' / ' + $.trim(r.idgudang || '') +
                    ' / ' + ($.trim(r.idspec || '') || '-')
                );
            }
        });

        if (stokKosong.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok kosong',
                html: 'Baris berikut stoknya kosong sehingga <b>hanya bisa Simpan Draft</b>:<br>'
                    + txt(stokKosong.slice(0, 8).join(' | ')).replace(/\|/g, '<br>')
                    + (stokKosong.length > 8 ? '<br>...' : '')
            });
            return;
        }

        Swal.fire({
            title: 'Finish Entry?',
            text: 'Data DO akan difinalkan dan diteruskan ke transaksi.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Simpan Final Data',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.isConfirmed) { return; }

            saveHeader(function () {
                S.submitting = true;
                document.getElementById('formDO').submit();
            }, true);
        });
    }


    $(function () {
        var ex = $.trim((window.DO_ADD && window.DO_ADD.existingDocno) || '');
        S.existing = ex;

        if (ex) {
            var p = ex.split('/');
            if (p.length === 3) {
                $('#prefix').val(p[0]);
                $('#infix').val(p[1]);
                $('#suffix').val(p[2]);
            }
        }

        initBranch();
        initDates();
        initCustomer();
        initSalesman();
        initTax();
        initCurrency();
        bindGrid();
        bindToolbar();

        refreshDocno();
        doSyncEmpty();

        if (ex) {
            loadRows();
        }
    });
})(jQuery);

