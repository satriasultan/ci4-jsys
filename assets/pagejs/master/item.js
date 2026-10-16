/*
item.js
 */


var save_method; //for save method string
var table;
//"use strict";
function tableItem(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#titem');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'master/data/list_mitem',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
                    data.idgroup = $('#idgroup').val();
                },
                "dataFilter": function(data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json); // return JSON string
                }
            },

            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [ -1 ], //last column
                    "orderable": false, //set not orderable
                },
            ],
        });

    };
    return initTable();
}

function reload_table()
{
    var table = $('#titem');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#titem');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#titem');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

//EDIT ITEM
function documentReadable(){
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: false //Display loader!
    // });
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'master/item/showDetailItem' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            // Fetch the preselected item, and add to the control
            $('[name="id"]').val(json.dataTables.items[0].id);
            $('[name="idbarang"]').val(json.dataTables.items[0].idbarang);
            $('[name="nmbarang"]').val(json.dataTables.items[0].nmbarang);
            $('[name="kdtax"]').val(json.dataTables.items[0].kdtax);
            $('[name="originalno"]').val(json.dataTables.items[0].originalno.trim());
            $('[name="lokasireff"]').val(json.dataTables.items[0].lokasireff);
            $('[name="satuantax"]').val(json.dataTables.items[0].satuantax);

            setJtsValue('[name="berat"]',convertToDbNumber(json.dataTables.items[0].berat));
            setJtsValue('[name="gw"]',convertToDbNumber(json.dataTables.items[0].gw));
            setJtsValue('[name="volume"]',convertToDbNumber(json.dataTables.items[0].volume));
            setJtsValue('[name="psize"]',convertToDbNumber(json.dataTables.items[0].psize));
            setJtsValue('[name="lsize"]',convertToDbNumber(json.dataTables.items[0].lsize));
            setJtsValue('[name="tsize"]',convertToDbNumber(json.dataTables.items[0].tsize));

            setJtsValue('[name="maks_daystock"]',convertToDbNumber(json.dataTables.items[0].maks_daystock));
            setJtsValue('[name="minstock"]',convertToDbNumber(json.dataTables.items[0].minstock1));

            $('[name="discontinue"]').prop(
                    'checked',
                    $.trim((json.dataTables.items[0].discontinue || '')).toUpperCase() === 'YES'
                );

            $('[name="issn"]').prop(
                    'checked',
                    $.trim((json.dataTables.items[0].issn || '')).toUpperCase() === 'YES'
                );
            //$('[name="idgroup"]').val(json.dataTables.items[0].idgroup);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mgroup' + '?var=' + json.dataTables.items[0].idgroup,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                const item = datax.items[0];
                var option = new Option(datax.items[0].nmgroup, datax.items[0].idgroup, true, true);
                $('[name="idgroup"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgroup"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: item
                    }
                });
            });
            $('[name="subunitenable"]').val(json.dataTables.items[0].subunitenable.trim()).trigger('change');
            //$('[name="unit"]').val(json.dataTables.items[0].unit);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_unit' + '?var=' + json.dataTables.items[0].unit,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
                $('[name="unit"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="unit"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="subunit"]').val(json.dataTables.items[0].subunit);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_subunit' + '?var=' + json.dataTables.items[0].subunit,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
                $('[name="subunit"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="subunit"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="idbarcode"]').val(json.dataTables.items[0].idbarcode);
            $('[name="mfgdate"]').val(json.dataTables.items[0].mfgdate1);
            $('[name="expdate"]').val(json.dataTables.items[0].expdate1);
            // $('[name="maks_daystock"]').val(json.dataTables.items[0].maks_daystock);
            //$('[name="deflocation"]').val(json.dataTables.items[0].deflocation);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + json.dataTables.items[0].deflocation,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlocation, datax.items[0].idlocation, true, true);
                $('[name="deflocation"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="deflocation"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="defarea"]').val(json.dataTables.items[0].defarea);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_marea' + '?var=' + json.dataTables.items[0].defarea,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmarea, datax.items[0].idarea, true, true);
                $('[name="defarea"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="defarea"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });



             $('[name="discontinue"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].discontinue || '')).toUpperCase() === 'YES'
            );


             $('[name="issn"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].issn || '')).toUpperCase() === 'YES'
            );


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_golonganbarang' + '?var=' + json.dataTables.items[0].idgolonganbarang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgolonganbarang, datax.items[0].idgolonganbarang, true, true);
                $('[name="idgolonganbarang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgolonganbarang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


             $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_jenisproduk' + '?var=' + json.dataTables.items[0].idjenisproduk,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmjenisproduk, datax.items[0].idjenisproduk, true, true);
                $('[name="idjenisproduk"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idjenisproduk"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


             $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_kelompokbarang' + '?var=' + json.dataTables.items[0].idkelompokbarang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmkelompokbarang, datax.items[0].idkelompokbarang, true, true);
                $('[name="idkelompokbarang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idkelompokbarang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


             $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_principal' + '?var=' + json.dataTables.items[0].idprincipal,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmprincipal, datax.items[0].idprincipal, true, true);
                $('[name="idprincipal"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idprincipal"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            setCoaSelect2('ppersediaan',  json.dataTables.items[0].ppersediaan);
            setCoaSelect2('psj',          json.dataTables.items[0].psj);
            setCoaSelect2('salesakun',    json.dataTables.items[0].salesakun);
            setCoaSelect2('pcogs',        json.dataTables.items[0].pcogs);
            setCoaSelect2('phpproduksi',  json.dataTables.items[0].phpproduksi);
            setCoaSelect2('pjasa',        json.dataTables.items[0].pjasa);
            setCoaSelect2('pwaste',       json.dataTables.items[0].pwaste);


            loadCoaByVisibleFields(json.dataTables.items[0]);


            // $('[name="minstock"]').val(json.dataTables.items[0].minstock1);
            $('[name="setminstock"]').val(json.dataTables.items[0].setminstock.trim()).trigger('change');

            $('[name="description"]').val(json.dataTables.items[0].description);
            $('[name="chold"]').val(json.dataTables.items[0].chold.trim()).trigger('change');

            $('[name="idbarang"]').prop('readonly', true);

        },
        complete: function(){
            // $("#loadMe").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            // $("#loadMe").modal("hide");
        }
    });

    // $("#loadMe").modal("hide");
}


function loadCoaByVisibleFields(data) {
    if ($('.field-ppersediaan').is(':visible')) {
        setCoaSelect2('ppersediaan', data.ppersediaan);
    }
    if ($('.field-psj').is(':visible')) {
        setCoaSelect2('psj', data.psj);
    }
    if ($('.field-salesakun').is(':visible')) {
        setCoaSelect2('salesakun', data.salesakun);
    }
    if ($('.field-pcogs').is(':visible')) {
        setCoaSelect2('pcogs', data.pcogs);
    }
    if ($('.field-phpproduksi').is(':visible')) {
        setCoaSelect2('phpproduksi', data.phpproduksi);
    }
    if ($('.field-pjasa').is(':visible')) {
        setCoaSelect2('pjasa', data.pjasa);
    }
    if ($('.field-pwaste').is(':visible')) {
        setCoaSelect2('pwaste', data.pwaste);
    }
}



function setCoaSelect2(fieldName, coaValue) {

    // guard: null / undefined / empty
    if (!coaValue || typeof coaValue !== 'string') {
        return;
    }

    var coaTrim = coaValue.trim();
    if (coaTrim === '') {
        return;
    }

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + coaTrim,
        dataType: 'json',
        delay: 100
    }).then(function (res) {

        if (!res.items || res.items.length === 0) return;

        var item = res.items[0];
        var $el = $('[name="' + fieldName + '"]');

        // =====================================================
        // Buat option + SIMPAN METADATA via jQuery .data()
        // =====================================================
        var $option = $('<option>', {
            value: trimStr(item.idcoa),
            text:  trimStr(item.nmcoa),
            selected: true
        });

        // Simpan data lengkap ke option element
        $option.data('data', {
            id:    trimStr(item.idcoa),
            text:  trimStr(item.nmcoa),
            idcoa: trimStr(item.idcoa),
            nmcoa: trimStr(item.nmcoa),
            level: item.level
        });

        // Hapus option lama, ganti baru
        $el.empty().append($option);

        // Trigger change + select2 select event
        $el.trigger('change');
        $el.trigger({
            type: 'select2:select',
            params: { data: item }
        });
    });
}





// =========================================================
// SET READONLY MODE untuk field Satuan Utama
// - Select2 → disabled (visual) + hidden input (data)
// - Input → readonly
// Hanya aktif saat EDIT / DETAIL
// =========================================================
function lockSatuanUtamaFields(mode) {

    // MODE INPUT → jangan lock apa-apa
    if (mode !== 'EDIT' && mode !== 'DETAIL') {
        return;
    }

    // ==========================================
    // 1. UNIT — jangan disable, kasih overlay (visual lock)
    // ==========================================
    var unitVal = $('#unit').val();
    if (unitVal) {
        $('#unit_hidden').val(unitVal);
    }

    // Disable interaksi visual, tapi #unit tetap aktif submit
    // Pakai wrapper overlay supaya user tidak bisa klik
    var $unitWrap = $('#unit').closest('.form-group, .input-group, .col-md-3');
    if ($unitWrap.length && !$unitWrap.find('.lock-overlay').length) {
        $unitWrap.css('position', 'relative').append(
            '<div class="lock-overlay" style="' +
            'position:absolute; top:0; left:0; right:0; bottom:0;' +
            'background: rgba(241,245,249,0.6);' +
            'cursor: not-allowed;' +
            'z-index: 10;' +
            '"></div>'
        );
    }

    // ==========================================
    // 2. SUB UNIT — sama
    // ==========================================
    var subunitVal = $('#subunit').val();
    if (subunitVal) {
        $('#subunit_hidden').val(subunitVal);
    }

    var $subWrap = $('#subunit').closest('.form-group, .input-group, .col-md-3');
    if ($subWrap.length && !$subWrap.find('.lock-overlay').length) {
        $subWrap.css('position', 'relative').append(
            '<div class="lock-overlay" style="' +
            'position:absolute; top:0; left:0; right:0; bottom:0;' +
            'background: rgba(241,245,249,0.6);' +
            'cursor: not-allowed;' +
            'z-index: 10;' +
            '"></div>'
        );
    }

    // ==========================================
    // 3. SATUAN TAX — input readonly (OK)
    // ==========================================
    $('#satuantax').prop('readonly', true).css({
        'background-color': '#f1f5f9',
        'cursor': 'not-allowed'
    });
}
$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});
/* FOR INPUT FUNCTION */

$('#formInputItem').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        idbarang: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmbarang: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idbarcode: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        chold: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        }
    },
    excluded: [':disabled']
});
let _saving = false;

function saveInputItem() {
    if (_saving) {
        alert('Sedang menyimpan, mohon tunggu...');
        return;
    }

    // ============================================================
    // AMBIL VALUE — prioritaskan hidden input untuk field yang di-lock
    // ============================================================
    var idbarang = ($('#idbarang').val() || '').trim();
    var nmbarang = ($('#nmbarang').val() || '').trim();

    // 🔥 FIX: Ambil dari select2 dulu, fallback ke hidden input
    var unit = ($('#unit').val() || '').trim();

    if (!unit) {
        // Fallback: cek hidden input (di-set oleh lockSatuanUtamaFields)
        unit = ($('#unit_hidden').val() || '').trim();
    }

    if (!unit) {
        // Fallback terakhir: ambil dari select2 data
        try {
            var s2data = $('#unit').select2('data');
            if (s2data && s2data.length > 0) {
                unit = (s2data[0].idunit || s2data[0].id || '').trim();
            }
        } catch (e) {}
    }

    // ============================================================
    // VALIDASI
    // ============================================================
    if (idbarang === '') {
        alert('Item ID wajib diisi.');
        $('#idbarang').focus();
        return;
    }

    if (nmbarang === '') {
        alert('Item Name wajib diisi.');
        $('#nmbarang').focus();
        return;
    }

    if (unit === '') {
        alert('Unit wajib dipilih.');
        $('#unit').focus();
        return;
    }

    // ============================================================
    // SEBELUM KIRIM: sinkronkan value ke field (karena select2 disabled)
    // ============================================================
    // Buat hidden input khusus untuk submit (kalau belum ada)
    if ($('#unit_submit').length === 0) {
        $('<input>', {
            type: 'hidden',
            name: 'unit',
            id: 'unit_submit'
        }).appendTo('#formInputItem');
    }
    $('#unit_submit').val(unit);

    // Kalau #unit disabled, form tidak akan kirim value-nya
    // Hidden input #unit_submit akan dipakai sebagai gantinya
    // Pastikan name="unit" yang aktif hanya satu

    _saving = true;
    $('#btnSave').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

    $.ajax({
        url: HOST_URL + 'master/item/saveDataItem',
        type: 'POST',
        data: $('#formInputItem').serialize(),
        dataType: 'JSON',
        success: function(res) {
            if (res.status) {
                alert(res.messages);
                window.location.href = HOST_URL + 'master/data/barang';
            } else {
                alert(res.messages);
            }
        },
        error: function() {
            alert('Terjadi kesalahan pada server.');
        },
        complete: function() {
            _saving = false;
            $('#btnSave').prop('disabled', false).html('Submit');
        }
    });
}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH GROUP ++++++++++++++++++++++++++++++++++++++++//

var defaultInitialGroup = 'BRG';
$("#idgroup").select2({
    placeholder: "Type/Chose Category Item",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mgroup',
        type: 'POST',
        width: '100%',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialGroup,
                _parameterx_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatGroup, // omitted for brevity, see the source of this page
    templateSelection: formatGroupSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
    const data = e.params.data;

    // normalize text (trim + uppercase)
    const groupName = (data.nmgroup || '').trim().toUpperCase();

    if (ALLOWED_GROUPS.includes(groupName)) {
        showUmumTab();
    } else {
        hideUmumTab();
    }


    applyGroupAccountRule(groupName);
});
/* Format Group */
function formatGroup(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idgroup +"   <i class='fa fa-circle-o'></i>   "+ repo.nmgroup +"</div>";
    return markup;
}

function formatGroupSelection(repo) {
    return repo.nmgroup || repo.text;
}

// $('#idgroup').on('select2:select', function (e) {
//     const data = e.params.data;

//     // normalize text (trim + uppercase)
//     const groupName = (data.items[0].nmgroup || '').trim().toUpperCase();

//     if (ALLOWED_GROUPS.includes(groupName)) {
//         showUmumTab();
//     } else {
//         hideUmumTab();
//     }


//     applyGroupAccountRule(groupName);
// });

function applyGroupAccountRule(groupName) {
    resetAccountFields();

    const fields = GROUP_FIELD_RULES[groupName];
    if (!fields) return;

    fields.forEach(f => {
        $('.field-' + f).show();
        $('#' + f).prop('required', true);
    });
}

const GROUP_FIELD_RULES = {
    'BARANG': [
        'ppersediaan', 'psj', 'salesakun', 'pcogs', 'phpproduksi'
    ],
    'PRODUKSI': [
        'ppersediaan', 'psj', 'salesakun', 'pcogs', 'phpproduksi'
    ],
    'BAHAN BAKU': [
        'ppersediaan', 'psj', 'salesakun', 'pcogs', 'phpproduksi'
    ],
    'BAHAN PEMBANTU': [
        'ppersediaan', 'psj', 'salesakun', 'pcogs', 'phpproduksi'
    ],
    'JASA': [
        'pjasa', 'salesakun'
    ],
    'PAKET': [
        'salesakun'
    ],
    'WIP': [
        'pwaste', 'salesakun'
    ]
};



function resetAccountFields() {
    const allFields = [
        'ppersediaan','psj','salesakun','pcogs',
        'phpproduksi','pjasa','pwaste'
    ];

    allFields.forEach(f => {
        $('.field-' + f).hide();
        $('#' + f).prop('required', false).val(null).trigger('change');
    });
}


const ALLOWED_GROUPS = [
    'BARANG',
    'PRODUKSI',
    'BAHAN BAKU',
    'BAHAN PEMBANTU'
];


function showUmumTab() {
    $('#tab-umum-wrapper').show();

    // optional: auto pindah ke tab umum
    // $('#umum-tab').tab('show');
}

function hideUmumTab() {
    // jika tab umum sedang aktif, pindahkan ke General
    if ($('#umum-tab').hasClass('active')) {
        $('#general-tab').tab('show');
    }

    $('#tab-umum-wrapper').hide();
}



var defaultInitialUnit = 'UNIT';
$("#unit").select2({
    placeholder: "Type/Chose Your Unit",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_unit',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialUnit,
                _parameterx_: defaultInitialUnit,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatUnit, // omitted for brevity, see the source of this page
    templateSelection: formatUnitSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#subunit option[value]").remove();
    var newOptions = []; // the result of your JSON request
    $("#subunit").val(null).trigger('change');
    //console.log($("#newdept").val());
});
/* Format formatUnit */
function formatUnit(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idunit +"</div>";
    return markup;
}

function formatUnitSelection(repo) {
    return repo.idunit || repo.text;
}



var defaultInitialSubUnit = 'SUBUNIT';
$("#subunit").select2({
    placeholder: "Type/Chose Your Sub Unit",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_subunit',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $("#unit").val(),
                _parameterx_: $("#unit").val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatUnit, // omitted for brevity, see the source of this page
    templateSelection: formatUnitSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {

});


var defaultInitialLocation = '';
$("#deflocation").select2({
    placeholder: "Type/Chose Location",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialLocation,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#defarea option[value]").remove();
    var newOptions = []; // the result of your JSON request
    $("#defarea").val(null).trigger('change');
    //console.log($("#newdept").val());
});

/*Location*/
function formatLocation(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idlocation +"   <i class='fa fa-circle-o'></i>   "+ repo.nmlocation +"</div>";
    return markup;
}

function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}



var defaultInitialArea = '';
$("#defarea").select2({
    placeholder: "Type/Chose Your Sub Unit",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_marea',
        type: 'POST',
        dataType: 'json',
        width: '100%',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $("#deflocation").val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatArea, // omitted for brevity, see the source of this page
    templateSelection: formatAreaSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {

});
/*Location*/
function formatArea(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idarea +"   <i class='fa fa-circle-o'></i>   "+ repo.nmarea +"</div>";
    return markup;
}

function formatAreaSelection(repo) {
    return repo.nmarea || repo.text;
}

function editItem(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Would be change? ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'master/data/edit' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}



function detailItem(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Would be change? ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'master/data/detail' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}




function formatGolonganBarang(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idgolonganbarang +"   <i class='fa fa-circle'></i>   "+ repo.nmgolonganbarang +"  </div>";
    return markup;
}
function formatGolonganBarangSelection(repo) {
    return repo.nmgolonganbarang || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idgolonganbarang").select2({
    placeholder: "Ketik/Pilih Golongan Barang",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_golonganbarang',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatGolonganBarang, // omitted for brevity, see the source of this page
    templateSelection: formatGolonganBarangSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});



function formatJenisProduk(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idjenisproduk +"   <i class='fa fa-circle'></i>   "+ repo.nmjenisproduk +"  </div>";
    return markup;
}
function formatJenisProdukSelection(repo) {
    return repo.nmjenisproduk || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idjenisproduk").select2({
    placeholder: "Ketik/Pilih Jenis Produk",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_jenisproduk',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatJenisProduk, // omitted for brevity, see the source of this page
    templateSelection: formatJenisProdukSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});



function formatKelompokBarang(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idkelompokbarang +"   <i class='fa fa-circle'></i>   "+ repo.nmkelompokbarang +"  </div>";
    return markup;
}
function formatKelompokBarangSelection(repo) {
    return repo.nmkelompokbarang || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idkelompokbarang").select2({
    placeholder: "Ketik/Pilih Kelompok Barang",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_kelompokbarang',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatKelompokBarang, // omitted for brevity, see the source of this page
    templateSelection: formatKelompokBarangSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});



function formatPrincipal(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idprincipal +"   <i class='fa fa-circle'></i>   "+ repo.nmprincipal +"  </div>";
    return markup;
}
function formatPrincipalSelection(repo) {
    return repo.nmprincipal || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idprincipal").select2({
    placeholder: "Ketik/Pilih Principal",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_principal',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatPrincipal, // omitted for brevity, see the source of this page
    templateSelection: formatPrincipalSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});




// =========================================================
// FORMAT SELECT2 — COA
// Tampilan: [Nama COA] · [ID COA] · Level [x]
// =========================================================

function formatCOA(repo) {
    if (repo.loading) return repo.text;

    // Normalisasi
    var nmcoa = (repo.nmcoa || '').toString().trim();
    var idcoa = (repo.idcoa || '').toString().trim();
    var level = (repo.level || '').toString().trim();

    // Kalau nmcoa kosong, fallback ke text select2
    if (!nmcoa && !idcoa) return repo.text;

    // Markup corporate
    var markup = '' +
        '<div class="select2-coa-item">' +
        '<div class="select2-coa-item__name">' + nmcoa + '</div>' +
        '<div class="select2-coa-item__meta">' +
        '<span class="select2-coa-item__id">' + idcoa + '</span>' +
        (level
            ? '<span class="select2-coa-item__sep">·</span>' +
            '<span class="select2-coa-item__level">Level ' + level + '</span>'
            : '') +
        '</div>' +
        '</div>';

    return markup;
}

function formatCOASelection(repo) {
    // Tampilkan: NAMA · ID  (kalau ada keduanya)
    var nmcoa = (repo.nmcoa || '').toString().trim();
    var idcoa = (repo.idcoa || '').toString().trim();

    if (nmcoa && idcoa) {
        return nmcoa + ' · ' + idcoa;
    }
    if (nmcoa) return nmcoa;
    if (idcoa) return idcoa;
    return repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#ppersediaan").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



$("#psj").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#salesakun").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



$("#pcogs").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});





$("#phpproduksi").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#pjasa").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#pwaste").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



function deleteItem(id, idbarang) {
    if (!confirm('Yakin hapus barang [' + idbarang + ']? Data tidak dapat dikembalikan.')) return false;
    window.location.href = HOST_URL + 'master/item/del_item/' + id;
}

// =========================================================
// LOAD DETAIL / EDIT ITEM
// Dipakai oleh view v_input.php saat mode EDIT / DETAIL
// =========================================================
// =========================================================
// LOAD DETAIL / EDIT ITEM
// Dipakai oleh view v_input.php saat mode EDIT / DETAIL
// Support 2 format response:
//   - Lama: { dataTables: { items: [...] }, units: [...] }
//   - Baru: { status, header, units }
// =========================================================
function documentReadableDetail() {

    var idItem = $('[name="id"]').val();

    if (!idItem) {
        console.log('[documentReadableDetail] ID kosong, skip.');
        return;
    }

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'master/item/showDetailItem/' + idItem,
        dataType: 'json',


        success: function (res) {

            // =================================================
            // 1. NORMALISASI RESPONSE — dukung 2 format
            // =================================================
            var d = null;
            var units = [];

            if (res && res.dataTables && res.dataTables.items && res.dataTables.items.length) {
                // Format dari controller: { dataTables: { items: [...] }, units: [...] }
                d = res.dataTables.items[0];
                units = res.units || [];
            } else if (res && res.header) {
                // Format alternatif: { status, header, units }
                d = res.header;
                units = res.units || [];
            }

            if (!d) {
                alert((res && res.message) ? res.message : 'Data tidak ditemukan.');
                return;
            }

            // =================================================
            // 2. ISI FIELD — GENERAL TAB
            // =================================================
            $('[name="id"]').val(trimStr(d.id));
            $('[name="idbarang"]').val(trimStr(d.idbarang));
            $('[name="nmbarang"]').val(trimStr(d.nmbarang));
            $('[name="kdtax"]').val(trimStr(d.kdtax));
            $('[name="originalno"]').val(trimStr(d.originalno));
            $('[name="lokasireff"]').val(trimStr(d.lokasireff));
            $('[name="satuantax"]').val(trimStr(d.satuantax));
            $('[name="idbarcode"]').val(trimStr(d.idbarcode));
            $('[name="description"]').val(d.description || '');

            // Angka (pakai jtsseparator bila tersedia)
            setJtsValue('[name="berat"]',         convertToDbNumber(d.berat));
            setJtsValue('[name="gw"]',            convertToDbNumber(d.gw));
            setJtsValue('[name="volume"]',        convertToDbNumber(d.volume));
            setJtsValue('[name="psize"]',         convertToDbNumber(d.psize));
            setJtsValue('[name="lsize"]',         convertToDbNumber(d.lsize));
            setJtsValue('[name="tsize"]',         convertToDbNumber(d.tsize));
            setJtsValue('[name="maks_daystock"]', convertToDbNumber(d.maks_daystock));
            setJtsValue('[name="minstock"]',      convertToDbNumber(d.minstock || d.minstock1));

            // Checkbox
            $('[name="discontinue"]').prop('checked',
                trimStr(d.discontinue).toUpperCase() === 'YES');
            $('[name="issn"]').prop('checked',
                trimStr(d.issn).toUpperCase() === 'YES');

            // Select biasa (bukan select2)
            setSelectValue('[name="subunitenable"]', trimStr(d.subunitenable));
            setSelectValue('[name="setminstock"]',   trimStr(d.setminstock));
            setSelectValue('[name="chold"]',         trimStr(d.chold));

            // Tanggal (format dd-mm-yyyy untuk daterangepicker)
            if (d.mfgdate || d.mfgdate1) {
                $('[name="mfgdate"]').val(formatDateDisplay(d.mfgdate || d.mfgdate1));
            }
            if (d.expdate || d.expdate1) {
                $('[name="expdate"]').val(formatDateDisplay(d.expdate || d.expdate1));
            }

            // =================================================
            // 3. SELECT2 — GROUP, UNIT, SUBUNIT, DLL
            // Pakai API globalmodule (konsep asli dipertahankan)
            // =================================================

            loadSelect2ByVar(
                'api/globalmodule/list_mgroup',
                d.idgroup,
                '[name="idgroup"]',
                'idgroup',
                'nmgroup'
            );

            loadSelect2ByVar(
                'api/globalmodule/list_unit',
                d.unit,
                '[name="unit"]',
                'idunit',
                'idunit'
            );

            loadSelect2ByVar(
                'api/globalmodule/list_subunit',
                d.subunit,
                '[name="subunit"]',
                'idunit',
                'idunit'
            );

            loadSelect2ByVar(
                'api/globalmodule/list_golonganbarang',
                d.idgolonganbarang,
                '[name="idgolonganbarang"]',
                'idgolonganbarang',
                'nmgolonganbarang'
            );

            loadSelect2ByVar(
                'api/globalmodule/list_jenisproduk',
                d.idjenisproduk,
                '[name="idjenisproduk"]',
                'idjenisproduk',
                'nmjenisproduk'
            );

            loadSelect2ByVar(
                'api/globalmodule/list_kelompokbarang',
                d.idkelompokbarang,
                '[name="idkelompokbarang"]',
                'idkelompokbarang',
                'nmkelompokbarang'
            );

            loadSelect2ByVar(
                'api/globalmodule/list_principal',
                d.idprincipal,
                '[name="idprincipal"]',
                'idprincipal',
                'nmprincipal'
            );

            loadSelect2ByVar(
                'api/globalmodule/list_mlocation',
                d.deflocation,
                '[name="deflocation"]',
                'idlocation',
                'nmlocation'
            );

            loadSelect2ByVar(
                'api/globalmodule/list_marea',
                d.defarea,
                '[name="defarea"]',
                'idarea',
                'nmarea'
            );

            // =================================================
            // 4. COA (Account Tab)
            // =================================================
            setCoaSelect2('ppersediaan', d.ppersediaan);
            setCoaSelect2('psj',         d.psj);
            setCoaSelect2('salesakun',   d.salesakun);
            setCoaSelect2('pcogs',       d.pcogs);
            setCoaSelect2('phpproduksi', d.phpproduksi);
            setCoaSelect2('pjasa',       d.pjasa);
            setCoaSelect2('pwaste',      d.pwaste);

            if (typeof loadCoaByVisibleFields === 'function') {
                loadCoaByVisibleFields(d);
            }

            // =================================================
            // 5. GRID SATUAN
            // =================================================
            renderSatuanGrid(units);

            // =================================================
// 6. MODE DETAIL / EDIT
// =================================================
            var mode = $('[name="type"]').val();

            if (mode === 'DETAIL') {
                // DETAIL → disable semua (readonly visual)
                $('form#formInputItem')
                    .find('input, select, textarea')
                    .not('[type="hidden"]')
                    .prop('disabled', true);

                $('#btnSave, #btnAddSatuan').hide();
            } else if (mode === 'EDIT') {
                // EDIT → idbarang readonly
                $('[name="idbarang"]').prop('readonly', true).css({
                    'background-color': '#f1f5f9',
                    'cursor': 'not-allowed'
                });
            }

// 🔒 LOCK Satuan Utama (unit, subunit, satuantax)
// Value tetap terkirim via hidden input
            lockSatuanUtamaFields(mode);
        },



        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Failed To Loading Data:', textStatus, errorThrown);
            // if ($('#loadMe').length) $('#loadMe').modal('hide');
        }
    });
}

// =========================================================
// HELPER FUNCTIONS
// =========================================================

/** Trim aman untuk nilai apapun */
function trimStr(v) {
    if (v === null || v === undefined) return '';
    return String(v).trim();
}

/** Set value select biasa; tambah option kalau belum ada */
function setSelectValue(selector, value) {
    var v = trimStr(value);
    if (v === '') return;
    var $el = $(selector);
    if ($el.find('option[value="' + v + '"]').length === 0) {
        $el.append('<option value="' + v + '">' + v + '</option>');
    }
    $el.val(v).trigger('change');
}

/** Set nilai input jtsseparator (kalau ada pluginnya), fallback ke val() */
function setJtsValue(selector, value) {
    var $el = $(selector);
    if (!$el.length) return;

    if (typeof $el.jtsseparator === 'function') {
        try { $el.val(value).trigger('change'); return; } catch (e) {}
    }
    $el.val(value);
}

/** Format tanggal dari 'YYYY-MM-DD...' ke 'DD-MM-YYYY' */
function formatDateDisplay(str) {
    if (!str) return '';
    var p = String(str).substring(0, 10).split('-');
    if (p.length !== 3) return str;
    return p[2] + '-' + p[1] + '-' + p[0];
}

/** AJAX Select2 — append option, trigger change + select2:select */
function loadSelect2ByVar(urlPath, value, selector, valueField, labelField) {
    var v = trimStr(value);
    if (v === '') return;

    $.ajax({
        type: 'GET',
        url: HOST_URL + urlPath + '?var=' + encodeURIComponent(v),
        dataType: 'json',
        delay: 250
    }).then(function (datax) {

        if (!datax || !datax.items || !datax.items.length) return;

        var item = datax.items[0];
        var $el = $(selector);

        // Buang option lama yang bukan default (opsional)
        // $el.find('option').remove();

        var option = new Option(
            trimStr(item[labelField]),
            trimStr(item[valueField]),
            true,
            true
        );
        $el.append(option).trigger('change');

        // Trigger select2:select supaya event handler lain ikut jalan
        $el.trigger({
            type: 'select2:select',
            params: { data: item }
        });
    });
}

/** Render grid satuan ke #tableSatuan */
function renderSatuanGrid(units) {
    var $tbody = $('#tableSatuan tbody');
    $tbody.empty();

    if (!units || units.length === 0) {
        $tbody.append(
            '<tr><td colspan="7" class="text-center text-muted">' +
            'Belum ada satuan</td></tr>'
        );
        return;
    }

    $.each(units, function (i, u) {
        var row = '' +
            '<tr>' +
            '<td class="text-center">' + (i + 1) + '</td>' +
            '<td>' + trimStr(u.idunit) + '</td>' +
            '<td>' + (trimStr(u.idunit_tax) || '-') + '</td>' +
            '<td class="text-end">' + u.basic_value + '</td>' +
            '<td class="text-end">' + u.conv_value + '</td>' +
            '<td class="text-center">' + trimStr(u.cdefault) + '</td>' +
            '<td class="text-center">' +
            '<button type="button" class="btn btn-sm btn-danger" ' +
            'onclick="deleteUnitItem(\'' +
            trimStr(u.idbarang) + '\',\'' +
            trimStr(u.idunit) + '\')">' +
            '<i class="fa fa-trash"></i></button>' +
            '</td>' +
            '</tr>';
        $tbody.append(row);
    });
}


$(document).ready(function() {

    tableItem();

    // =========================================================
    // 🔒 AUTO READONLY idbarang SAAT EDIT / DETAIL
    // Set LANGSUNG sebelum AJAX supaya user tidak sempat ubah
    // =========================================================
    var _modeForm = $('[name="type"]').val();

    if (_modeForm === 'EDIT' || _modeForm === 'DETAIL') {
        $('[name="idbarang"]')
            .prop('readonly', true)
            .css({
                'background-color': '#f1f5f9',
                'cursor': 'not-allowed'
            });
    }

    // =========================================================
    // CHECKBOX NIK (fitur lama — biarkan apa adanya)
    // =========================================================
    $('#checkboxnik').change(function() {
        if (this.checked) {
            var valnik = $('#nik').val();
            $('#username').prop('readonly', true);
            $('#username').val(valnik);
        } else {
            $('#username').prop('readonly', false);
            $('#username').val('');
        }
    });
    $('#nik').change(function() {
        if ($('#checkboxnik').is(':checked')){
            var valnik = $(this).val();
            $('#username').val(valnik);
        }
    });

    // =========================================================
    // LOAD DATA EDIT / DETAIL
    // Edit & Detail → pakai documentReadableDetail()
    // =========================================================
    if (_modeForm === 'EDIT' || _modeForm === 'DETAIL') {
        documentReadableDetail();
    }


    /* FUNGSI DOCUMEN SATUAN */
    /* =========================================================
       SATUAN BARANG
    ========================================================= */

    let tableSatuan;

    function initTableSatuan() {

        const idbarang = $('#idbarang').val();

        if (!idbarang) {
            console.warn('ID Barang belum tersedia.');
            return;
        }

        if ($.fn.DataTable.isDataTable('#tableSatuan')) {
            tableSatuan.ajax.reload(null, false);
            return;
        }

        tableSatuan = $('#tableSatuan').DataTable({

            processing: true,
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthChange: false,
            searching: false,
            ordering: false,
            info: false,

            ajax: {
                url: HOST_URL + 'master/data/barangUnitList',
                type: 'GET',
                data: function (d) {
                    d.idbarang = $('#idbarang').val();
                }
            },

            columns: [
                { data: 'no', className: 'text-center' },
                { data: 'idunit' },
                { data: 'idunit_tax' },
                { data: 'basic_value', className: 'text-end' },
                { data: 'conv_value',  className: 'text-end' },
                {
                    data: 'cdefault',
                    className: 'text-center',
                    render: function (data) {
                        if (data === 'YES') {
                            return `<span class="badge bg-success">YES</span>`;
                        }
                        return `<span class="badge bg-default">NO</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {

                        if (row.cdefault && row.cdefault.trim().toUpperCase() === 'YES') {
                            return '';
                        }

                        return `
                            <button type="button"
                                    class="btn btn-danger btn-sm btnDeleteSatuan"
                                    data-idbarang="${row.idbarang}"
                                    data-idunit="${row.idunit}"
                                    title="Hapus Satuan">
                                <i class="fa fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ],

            language: {
                emptyTable: 'Belum ada data satuan',
                processing: 'Memuat data...'
            }
        });
    }

    function loadTableSatuan() {

        const idbarang = $('#idbarang').val();

        if (!idbarang) {
            console.warn('ID Barang belum tersedia.');
            return;
        }

        if ($.fn.DataTable.isDataTable('#tableSatuan')) {
            tableSatuan.ajax.reload(null, false);
            return;
        }

        initTableSatuan();
    }
    /* =========================================================
       DELETE SATUAN
    ========================================================= */
    $(document).on('click', '.btnDeleteSatuan', function () {

        const button   = $(this);
        const idbarang = button.data('idbarang');
        const idunit   = button.data('idunit');

        if (!idbarang || !idunit) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Data tidak lengkap untuk dihapus.'
            });
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Hapus Satuan?',
            html:
                'Satuan <b>' + idunit + '</b> akan dihapus dari barang:<br>' +
                '<code>' + idbarang + '</code><br><br>' +
                '<span class="text-danger">Data tidak dapat dikembalikan.</span>',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {

            if (!result.isConfirmed) return;

            $.ajax({
                url: HOST_URL + 'master/data/deleteBarangUnit',
                type: 'POST',
                dataType: 'json',
                data: {
                    idbarang: idbarang,
                    idunit:   idunit
                },
                success: function (res) {

                    if (!res.success) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Gagal',
                            text: res.message
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Satuan berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Reload grid satuan
                    loadTableSatuan();
                },
                error: function (xhr) {
                    console.error('Delete error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Terjadi kesalahan saat menghapus satuan.'
                    });
                }
            });
        });
    });
    /* =========================================================
       TAMBAH SATUAN
    ========================================================= */
    $('#btnAddSatuan').on('click', function () {

        const idbarang    = $('#idbarang').val().trim();
        const idunit      = $('#idunit').val();
        const idunit_tax  = $('#idunit_tax').val().toUpperCase().trim();
        const basic_value = $('#basic_value').val();
        const conv_value  = $('#conv_value').val();
        const cdefault    = $('#cdefault').val();

        if (!idbarang) {
            Swal.fire({ icon: 'warning', title: 'Warning', text: 'ID Barang tidak ditemukan.' });
            return;
        }

        if (!idunit) {
            Swal.fire({ icon: 'warning', title: 'Warning', text: 'ID Unit Konversi wajib dipilih.' });
            return;
        }

        $.ajax({
            url: HOST_URL + 'master/data/saveBarangUnit',
            type: 'POST',
            dataType: 'json',
            data: {
                idbarang:    idbarang,
                idunit:      idunit,
                idunit_tax:  idunit_tax,
                basic_value: basic_value,
                conv_value:  conv_value,
                cdefault:    cdefault
            },
            success: function (res) {

                if (!res.success) {
                    Swal.fire({ icon: 'warning', title: 'Gagal', text: res.message });
                    return;
                }

                loadTableSatuan();

                $('#idunit').val('').trigger('change');
                $('#idunit_tax').val('').trigger('change');
                $('#basic_value').val('1.00');
                $('#conv_value').val('1.00');
                $('#cdefault').val('NO');
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan saat menyimpan satuan.'
                });
            }
        });
    });

    /* =========================================================
       DELETE SATUAN
    ========================================================= */
    $('#btnAddSatuan').on('click', function () {

        const idbarang    = $('#idbarang').val().trim();
        const idunit      = $('#idunit').val();
        const idunit_tax  = $('#idunit_tax').val().toUpperCase().trim();
        const basic_value = $('#basic_value').val();
        const conv_value  = $('#conv_value').val();
        const cdefault    = $('#cdefault').val();

        // ================================================
        // VALIDASI DASAR
        // ================================================
        if (!idbarang) {
            Swal.fire({ icon: 'warning', title: 'Warning', text: 'ID Barang tidak ditemukan.' });
            return;
        }

        if (!idunit) {
            Swal.fire({ icon: 'warning', title: 'Warning', text: 'ID Unit Konversi wajib dipilih.' });
            return;
        }

        // ================================================
        // 🔥 VALIDASI CONV_VALUE
        // ================================================
        var convNum = parseFloat(conv_value);

        if (isNaN(convNum) || convNum <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Konversi Tidak Valid',
                text: 'Conversion Value harus berupa angka positif.'
            });
            $('#conv_value').focus();
            return;
        }

        // 🔥 Rule 1: Default HARUS conv_value = 1
        if (cdefault === 'YES' && convNum !== 1) {
            Swal.fire({
                icon: 'error',
                title: 'Konversi Default Salah',
                html:
                    '<b>Satuan Default harus punya Conversion Value = 1</b><br><br>' +
                    'Satuan default adalah <b>satuan terkecil</b>. ' +
                    'Semua satuan lain diukur relatif terhadapnya.<br><br>' +
                    'Contoh yang benar:<br>' +
                    '<code>PCS → conv_value = 1, cdefault = YES</code><br>' +
                    '<code>PLASTIK → conv_value = 20, cdefault = NO</code>',
                confirmButtonText: 'Mengerti'
            });
            $('#conv_value').val('1.00').focus();
            return;
        }

        // 🔥 Rule 2: Non-default HARUS conv_value >= 1
        if (cdefault === 'NO' && convNum < 1) {
            Swal.fire({
                icon: 'error',
                title: 'Konversi Terlalu Kecil',
                html:
                    '<b>Conversion Value tidak boleh &lt; 1</b><br><br>' +
                    'Nilai <code>' + convNum + '</code> akan menyebabkan ' +
                    '<b>kerusakan data stock</b>.<br><br>' +
                    'Satuan default harus <b>satuan terkecil</b>. ' +
                    'Kalau satuan ini lebih kecil, ' +
                    '<b>jadikan satuan ini sebagai default</b>, ' +
                    'dan satuan default lama jadi non-default dengan conv_value = jumlah perkalian.<br><br>' +
                    '<b>Contoh salah:</b><br>' +
                    '<code>PCS → conv = 1, cdefault = YES</code><br>' +
                    '<code>BALL → conv = 0.25, cdefault = NO</code> ❌<br><br>' +
                    '<b>Contoh benar (BALL lebih kecil):</b><br>' +
                    '<code>BALL → conv = 1, cdefault = YES</code><br>' +
                    '<code>PCS → conv = 4, cdefault = NO</code> ✅',
                confirmButtonText: 'Mengerti'
            });
            $('#conv_value').focus();
            return;
        }

        // ================================================
        // KIRIM KE BACKEND
        // ================================================
        $.ajax({
            url: HOST_URL + 'master/data/saveBarangUnit',
            type: 'POST',
            dataType: 'json',
            data: {
                idbarang:    idbarang,
                idunit:      idunit,
                idunit_tax:  idunit_tax,
                basic_value: basic_value,
                conv_value:  conv_value,
                cdefault:    cdefault
            },
            success: function (res) {

                if (!res.success) {
                    Swal.fire({ icon: 'warning', title: 'Gagal', text: res.message });
                    return;
                }

                loadTableSatuan();

                $('#idunit').val('').trigger('change');
                $('#idunit_tax').val('').trigger('change');
                $('#basic_value').val('1.00');
                $('#conv_value').val('1.00');
                $('#cdefault').val('NO');
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan saat menyimpan satuan.'
                });
            }
        });
    });

    /* =========================================================
       LOAD SAAT TAB SATUAN DIBUKA
    ========================================================= */
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {

        const target = $(e.target).attr('href');

        if (target === '#satuantab') {
            loadTableSatuan();
        }
    });

});