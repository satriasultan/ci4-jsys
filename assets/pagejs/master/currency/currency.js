/*
 * Created by PhpStorm.
 *  * Currency: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


var save_method; //for save method string
var table;
//"use strict";
function tableCurrency(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tcurrency');
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
            "responsive": true,
            "ajax": {
                "url": HOST_URL + 'master/data/list_currency',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
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
                    "targets": [ -2 ], //last column
                    "orderable": false, //set not orderable
                },
            ],
        });

    };
    return initTable();
}

function reload_table()
{
    var table = $('#tcurrency');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tberitaacara');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tberitaacara');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
/* FOR INPUT FUNCTION */
$('#form').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        roleid: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        rolename: {
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


function add_currency()
{
    save_method = 'add';
    var validator = $('#form').data('bootstrapValidator');
    validator.resetForm();
    $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
    $('#form')[0].reset(); // reset form on modals
    //$('.form-group').removeClass('has-error').removeClass('has-success'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Input Currency'); // Set Title to Bootstrap modal title
    $('[name="typeform"]').val('INPUT');
    $('[name="id"]').val();
    $('[name="currcode"]').prop("required", true);
    $('[name="currname"]').prop("required", true);
    $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
}


function documentReadable() {
    const idcurrency = $('[name="idcurrency"]').val();

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'master/data/showing_currency?id=' + idcurrency,
        dataType: 'text', // ubah dari 'json' ke 'text' agar bisa parse manual
        success: function (data) {
            var json = jQuery.parseJSON(data);

            json.status = json.dataTables?.status || '';
            json.total_count = json.dataTables?.total_count || 0;
            json.items = json.dataTables?.items || [];
            json.incomplete_results = json.dataTables?.incomplete_results || false;

            // Lanjutkan jika ada item
            if (json.dataTables && json.dataTables.items.length != 0) {
                let datax = json.dataTables.items[0];
                // $('[name="idcurrency"]').val(datax.id);
                $('[name="currname"]').val(datax.currname);
                $('[name="currcode"]').val(datax.currcode);

                //================ PEMBELIAN =============================
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.phutang.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {

                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="phutang"]').append(option).trigger('change');

                    // manually trigger the `select2:select` event
                    $('[name="phutang"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: res.items[0]
                        }
                    });
                });
                // PUM
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.pum.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="pum"]').append(option).trigger('change');
                    $('[name="pum"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // PBONUS
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.pbonus.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="pbonus"]').append(option).trigger('change');
                    $('[name="pbonus"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // HUTANG ANTAR CABANG
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.hutangac.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="hutangac"]').append(option).trigger('change');
                    $('[name="hutangac"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // HUTANG BIAYA 1
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.hutangbiaya1.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="hutangbiaya1"]').append(option).trigger('change');
                    $('[name="hutangbiaya1"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // HUTANG BIAYA 2
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.hutangbiaya2.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="hutangbiaya2"]').append(option).trigger('change');
                    $('[name="hutangbiaya2"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });



                // ========================= PENJUALAN ===================================
                // PIUTANG
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.ppiutang.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="ppiutang"]').append(option).trigger('change');
                    $('[name="ppiutang"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // UANG MUKA JUAL
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.pumjual.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="pumjual"]').append(option).trigger('change');
                    $('[name="pumjual"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // PENDAPATAN
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.ppendapatan.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="ppendapatan"]').append(option).trigger('change');
                    $('[name="ppendapatan"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // RETUR
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.pretur.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="pretur"]').append(option).trigger('change');
                    $('[name="pretur"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // DISC
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.pdisc.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="pdisc"]').append(option).trigger('change');
                    $('[name="pdisc"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // BONUS JUAL
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.pbonusjual.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="pbonusjual"]').append(option).trigger('change');
                    $('[name="pbonusjual"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // TUNAI
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.ptunai.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="ptunai"]').append(option).trigger('change');
                    $('[name="ptunai"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // PIUTANG AC
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.piutangac.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="piutangac"]').append(option).trigger('change');
                    $('[name="piutangac"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // PENDAPATAN AC
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.pendapatanac.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="pendapatanac"]').append(option).trigger('change');
                    $('[name="pendapatanac"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                // PENDAPATAN SERVICE
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + datax.pps.trim(),
                    dataType: 'json',
                    delay: 100,
                }).then(function (res) {
                    if (!res.items || res.items.length === 0) return;

                    var option = new Option(
                        res.items[0].nmcoa,
                        res.items[0].idcoa,
                        true,
                        true
                    );
                    $('[name="pps"]').append(option).trigger('change');
                    $('[name="pps"]').trigger({ type: 'select2:select', params: { data: res.items[0] } });
                });

                var isDetail = $('[name="type"]').val() === 'DETAIL';
                if (isDetail) {
                    // disable semua input, textarea, select, dan checkbox/radio
                    $('input, textarea, select').prop('disabled', true);
                }
            }
        },
        error: function () {
            console.error("Gagal memuat data currency");
        }
    });
}


function tableExchangeRate(){
    //"url": HOST_URL + 'stock/bbm/list_tmp_bbm_dtl',
    //var table = $('#tabbmdtl');
    var table = $('#t_exchange').DataTable({
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "language":  languageDatatable(),
        "paging": false,
        "lengthChange": true,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": false,
        "bFilter":true,
        "iDisplayLength": -1,
        "ajax": {
            "url": HOST_URL + 'master/data/showing_exchange_rate' + '?idcurrency=' + $('[name="idcurrency"]').val(),
            "type": "POST",
            "data": function(data) {
                //data.searchfilter = $('#searchitem').val()+'';
                //data.idbarang = $('#idbarang').val()+'';
                //data.idposition = $('#idposition').val()+'';
            },
            "dataFilter": function(data) {
                var json = jQuery.parseJSON(data);
                json.draw = json.dataTables.draw;
                json.recordsTotal = json.dataTables.recordsTotal;
                json.recordsFiltered = json.dataTables.recordsFiltered;
                json.data = json.dataTables.data;
                // const dataDetail = json.data;
                return JSON.stringify(json); // return JSON string
            }
        },
        //Set column definition initialisation properties.
        "columnDefs": [
            {
                "targets": [ 3 ], // kolom Action (tombol Update via modal popup)
                "orderable": false, //set not orderable
                "searchable": false,
                "render": function (data, type, full, meta) {
                    // tombol Update hanya untuk form UPDATE (bukan INPUT / DETAIL)
                    var typeform = $('[name="typeform"]').val();
                    return (typeform === 'UPDATE') ? data : '';
                }
            },
            {
                'targets': 0,
                'checkboxes': {
                    'selectRow': true,
                    'selectAll': true // Aktifkan fungsi centang semua

                }
            },
        ],
        "select": {
            'style': 'multi'
        },
        "order": [[1, 'asc']],
        "initComplete": function () {
            // checkTableData(); // Panggil fungsi setelah DataTable selesai diinisialisasi
        }
    });

    /* UPDATE : tombol pensil di setiap baris => buka modal popup Exchange Rate.
       Input inline pada tabel sudah tidak dipakai lagi. */
    $('#t_exchange').on('click', '.btn-edit-exchange', function (e) {
        e.preventDefault();
        openExchangeRateModal('UPDATE', this);
    });


    // Event listener untuk checkbox di header
    $('#t_exchange thead input[type="checkbox"]').on('change', function () {
        // Cek status checkbox di header (true/false)

        var isChecked = this.checked;

        // Update hanya checkbox di kolom pertama (index 0)
        $('#t_exchange tbody tr').each(function () {
            // Cari input[type="checkbox"] di kolom pertama saja
            var checkbox = $(this).find('td:first-child input[type="checkbox"]');
            checkbox.prop('checked', isChecked);
        });

        // Update DataTables (kolom pertama = index 0)
        if (isChecked) {
            table.column(0).checkboxes.select(); // Pilih semua di kolom pertama
        } else {
            table.column(0).checkboxes.deselect(); // Batalkan semua di kolom pertama
        }
    });



    /* DELETE : berdasarkan checklist pada kolom Act */
    $('#delete_exchange').on('click', function (e) {
        e.preventDefault();

        var rows_selected = table.column(0).checkboxes.selected().toArray(); // Konversi ke array

        if (rows_selected.length <= 0) {
            Swal.fire({
                title: 'Ooops..!!!',
                text: 'Minimal 1 Pilihan',
                backdrop: true,
                allowOutsideClick: false,
                showConfirmButton: true,
                showDenyButton: false,
                showCancelButton: false,
                confirmButtonText: `Ok`,
                icon: 'question',
            });
            return;
        }

        Swal.fire({
            title: 'Warning..!!!',
            text: 'Some Exchange details will be removed, are you sure? ' + rows_selected,
            backdrop: true,
            allowOutsideClick: false,
            showConfirmButton: true,
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Ok`,
            icon: 'question',
        }).then((result) => {
            if (!result.isConfirmed) return;

            var formdata = new FormData();
            formdata.append('idcurrency', $('[name="idcurrency"]').val()); // Tambahkan idcurrency
            rows_selected.forEach(function (rowId) {
                formdata.append('id[]', rowId); // Tambahkan ID dari database
            });

            $.ajax({
                url: HOST_URL + 'master/data/deleteExchangeRate',
                type: "POST",
                data: formdata,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (json) {
                    if (json && json.status) {
                        Swal.fire({
                            title: 'Success...!!!',
                            text: json.messages,
                            backdrop: true,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: `Ok`,
                            icon: 'success',
                        });
                        table.column(0).checkboxes.deselectAll();
                        reloadtableExchangeRate();
                    } else {
                        Swal.fire({
                            title: 'Gagal!!',
                            text: (json && json.messages) ? json.messages : 'Failed to delete data',
                            icon: 'error',
                            allowOutsideClick: false
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Galat!!",
                        text: 'Unable to process request.',
                        icon: "error",
                        allowOutsideClick: false
                    });
                }
            });
        });
    });
}


function reloadtableExchangeRate() {
    // var table = $('#t_exchange').DataTable();
    var table = $('#t_exchange');
    table.DataTable().ajax.reload(); //reload datatable ajax
    // if ($.fn.DataTable.isDataTable("#t_exchange")) {
    //     table.ajax.reload(function() {
    //         setTimeout(checkTableData, 500); // Jalankan checkTableData setelah reload selesai
    //     });
    // } else {
    //     console.warn("DataTable belum diinisialisasi.");
    // }
}


/* =========================================================
 * MODAL POPUP EXCHANGE RATE (Insert / Update)
 * Tabel #t_exchange hanya read only: checklist utk Delete,
 * tombol pensil utk Update => keduanya memakai modal ini.
 * ========================================================= */
function showExchangeRateModal() {
    var el = document.getElementById('modal_exchange');
    if (!el) return;

    if (window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(el).show();
    } else if ($.fn.modal) {
        $('#modal_exchange').modal('show');
    }
}

function hideExchangeRateModal() {
    var el = document.getElementById('modal_exchange');
    if (!el) return;

    if (window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(el).hide();
    } else if ($.fn.modal) {
        $('#modal_exchange').modal('hide');
    }
}

/* Tanggal + jam saat ini dengan format DD-MM-YYYY HH:mm:ss */
function currentExchangeDateValue() {
    var d = new Date();
    var p = function (n) { return (n < 10 ? '0' : '') + n; };

    return p(d.getDate()) + '-' + p(d.getMonth() + 1) + '-' + d.getFullYear()
        + ' ' + p(d.getHours()) + ':' + p(d.getMinutes()) + ':' + p(d.getSeconds());
}

/* Set value input exchange date + sinkronkan daterangepicker */
function setExchangeDateValue(value) {
    var $input = $('#md_exchangedate');
    $input.val(value || '');

    var drp = $input.data('daterangepicker');
    if (drp && typeof moment === 'function') {
        var m = moment(value, 'DD-MM-YYYY HH:mm:ss', true);
        if (m.isValid()) {
            drp.setStartDate(m);
            drp.setEndDate(m);
        }
    }
}

/* Dipanggil oleh button "Insert Exchange Rate" */
function insertNewExchange() {
    openExchangeRateModal('INSERT', null);
}

/* mode : 'INSERT' atau 'UPDATE'; button : tombol pensil pada baris tabel */
function openExchangeRateModal(mode, button) {
    var isUpdate = (mode === 'UPDATE');
    var $button = button ? $(button) : null;

    $('#md_exchange_type').val(isUpdate ? 'UPDATE' : 'INSERT');
    $('#md_exchange_id').val(isUpdate && $button ? ($button.attr('data-id') || '') : '');
    $('#modal_exchange_label').html('<i class="fa fa-exchange"></i> ' + (isUpdate ? 'Update Exchange Rate' : 'Insert Exchange Rate'));
    $('#md_exchange_error').hide().text('');

    var dateValue = (isUpdate && $button) ? ($button.attr('data-exchangedate') || '') : currentExchangeDateValue();
    var nilaiValue = (isUpdate && $button) ? ($button.attr('data-nilai') || '') : '';

    setExchangeDateValue(dateValue);
    $('#md_nilai').val(nilaiValue);

    showExchangeRateModal();
}

/* Simpan data dari modal popup : INSERT (tambah) atau UPDATE (per baris) */
function saveExchangeRate() {
    var mode = $('#md_exchange_type').val();
    var idcurrency = $('[name="idcurrency"]').val();
    var id = $('#md_exchange_id').val();
    var exchangedate = $.trim($('#md_exchangedate').val() || '');
    var nilaiRaw = $.trim($('#md_nilai').val() || '');
    var $error = $('#md_exchange_error');
    var $save = $('#md_exchange_save');

    var showError = function (msg) { $error.text(msg).show(); };

    $error.hide().text('');

    if (!idcurrency) {
        showError('Currency is not selected.');
        return;
    }

    if (exchangedate === '' || !/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2}$/.test(exchangedate)) {
        showError('Exchange date is required with format DD-MM-YYYY HH:mm:ss.');
        return;
    }

    if (nilaiRaw === '') {
        showError('Price is required.');
        return;
    }

    // rapikan nilai menjadi tepat 4 digit desimal (NUMERIC 18,4)
    normalizeNilaiExchange(document.getElementById('md_nilai'), true);
    var nilai = convertToDbNumber($('#md_nilai').val());

    $save.prop('disabled', true);

    if (mode === 'UPDATE') {
        if (!id) {
            $save.prop('disabled', false);
            showError('Exchange rate data is not valid.');
            return;
        }

        $.ajax({
            url: HOST_URL + 'master/data/update_exchangerate',
            type: 'POST',
            dataType: 'json',
            data: {
                updates: [{ idurut: id, exchangedate: exchangedate, nilai: nilai }],
                masterData: { idcurrency: idcurrency }
            },
            success: function (resp) {
                $save.prop('disabled', false);

                if (resp && resp.status === 'success') {
                    hideExchangeRateModal();
                    Swal.fire({
                        title: 'Success...!!!',
                        text: 'Successfully updated exchange rate',
                        icon: 'success',
                        confirmButtonText: 'Ok',
                        allowOutsideClick: false
                    });
                    reloadtableExchangeRate();
                } else {
                    showError((resp && resp.message) ? resp.message : 'Failed to update data.');
                }
            },
            error: function () {
                $save.prop('disabled', false);
                showError('Failed to update data. Please try again.');
            }
        });

        return;
    }

    var fillData = {
        'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
        'body': {
            idcurrency: idcurrency,
            exchangedate: exchangedate,
            nilai: nilai
        }
    };

    $.ajax({
        type: "POST",
        url: HOST_URL + 'master/data/insertNewExchange',
        dataType: 'json',
        contentType: "application/json",
        data: JSON.stringify(fillData),
        success: function (datax) {
            $save.prop('disabled', false);

            if (datax && datax.status) {
                hideExchangeRateModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: datax.message,
                    allowOutsideClick: false
                });
                reloadtableExchangeRate();
            } else {
                showError((datax && datax.message) ? datax.message : 'Unable to save data.');
            }
        },
        error: function () {
            $save.prop('disabled', false);
            showError('Unable to process request.');
        }
    });
}



$(document).on('keyup blur change', '.jtsseparator', function () {
    _jtsseparator(this);
});

/*
 * Khusus kolom exchange rate (nilai).
 * Format angka polos: hanya digit + satu titik desimal, TANPA koma / pemisah ribuan.
 * Presisi desimal mengikuti kolom NUMERIC(18,4) => maksimal 4 digit.
 */
$(document).on('keyup input change blur', '.nilai-exchange', function (e) {
    normalizeNilaiExchange(this, e.type === 'blur' || e.type === 'change');
});

function normalizeNilaiExchange(objek, trimDanglingDot) {
    if (!objek) return;

    const oldValue = String(objek.value === null || objek.value === undefined ? '' : objek.value);

    let caret = null;
    try {
        caret = objek.selectionStart;
    } catch (err) {
        caret = null;
    }

    // buang koma (tidak dipakai pada field ini) & karakter selain digit/titik
    let raw = oldValue
        .replace(/,/g, '')
        .replace(/[^0-9.]/g, '');

    if (raw === '') {
        objek.value = '';
        return;
    }

    const dotIndex = raw.indexOf('.');
    let integerPart = dotIndex === -1 ? raw : raw.slice(0, dotIndex);
    let decimalPart = dotIndex === -1 ? '' : raw.slice(dotIndex + 1).replace(/\./g, '');

    // maksimal 4 digit desimal (NUMERIC 18,4)
    decimalPart = decimalPart.slice(0, 4);

    // buang nol di depan (007 -> 7), sisakan satu digit
    integerPart = integerPart.replace(/^0+(?=\d)/, '');
    if (integerPart === '') integerPart = '0';

    let output = integerPart;
    if (dotIndex !== -1) {
        output = integerPart + '.' + decimalPart;
    }

    /* Saat blur/change: nilai dirapikan menjadi tepat 4 digit desimal
       (presisi kolom NUMERIC 18,4), contoh: 114.8 -> 114.8000, 17000 -> 17000.0000. */
    if (trimDanglingDot) {
        const parts = output.split('.');
        let decPart = parts.length > 1 ? parts[1] : '';
        while (decPart.length < 4) {
            decPart += '0';
        }
        output = parts[0] + '.' + decPart;
    }

    objek.value = output;

    // pertahankan posisi caret
    if (caret !== null && typeof objek.setSelectionRange === 'function') {
        let newCaret;
        if (caret >= oldValue.length) {
            newCaret = output.length;
        } else {
            const before = oldValue.slice(0, caret).replace(/,/g, '').replace(/[^0-9.]/g, '');
            newCaret = Math.min(before.length, output.length);
        }
        try {
            objek.setSelectionRange(newCaret, newCaret);
        } catch (err) {
            /* input type tertentu tidak mendukung setSelectionRange */
        }
    }
}

/* NOTE : tombol Update inline (#update_exchange) sudah dihapus.
   Update exchange rate dilakukan per baris lewat tombol pensil (modal popup),
   lihat openExchangeRateModal() & saveExchangeRate(). */

function formatNumber(value) {
    if (!value) return '0';

    // Pastikan nilai adalah angka dan hilangkan desimal jika ".00"
    let num = parseFloat(value);
    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num);
}




function formatCOA(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idcoa +"   <i class='fa fa-circle'></i>   "+ repo.nmcoa +" <i class='fa fa-circle'></i> LEVEL "+ repo.level + " </div>";
    return markup;
}
function formatCOASelection(repo) {
    return repo.nmcoa || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#phutang").select2({
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


$("#pum").select2({
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

$("#pbonus").select2({
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


$("#hutangac").select2({
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


$("#hutangbiaya1").select2({
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


$("#hutangbiaya2").select2({
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


// ======================= PENJUALAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#ppiutang").select2({
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


$("#pumjual").select2({
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

$("#ppendapatan").select2({
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


$("#pretur").select2({
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


$("#pdisc").select2({
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


$("#pbonusjual").select2({
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

$("#ptunai").select2({
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


$("#piutangac").select2({
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


$("#pendapatanac").select2({
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


$("#pps").select2({
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


// ======================= END OF PENJUALAN ===========================

function submitCurrency() {
    const bttn = $('#btnSave');
    bttn.prop('disabled', true);

    Swal.fire({
        title: 'Warning..!!!',
        text: `Data currency akan disimpan, apakah Anda yakin?`,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: `Ok`,
        icon: 'question',
    }).then((result) => {
        if (result.isConfirmed) {
            const fields = {
                "Currency Name": $('[name="currname"]').val(),
                "Currency Code": $('[name="currcode"]').val(),
            };

            // Validasi wajib isi
            for (let key in fields) {
                if (!fields[key] || fields[key].trim() === "") {
                    Swal.fire({
                        icon: "error",
                        title: "Validasi Gagal",
                        text: key + " tidak boleh kosong!",
                    }).then(() => bttn.prop('disabled', false));
                    return;
                }
            }


            // const is_verified = $('[name="is_verified"]').is(':checked') ? 1 : 0;

            const currencyData = {
                id: $('[name="idcurrency"]').val(), // <-- ini WAJIB ADA agar dikirim ke backend
                currname: $('[name="currname"]').val(),
                currcode: $('[name="currcode"]').val(),
                tipe: $('[name="typeform"]').val()
            };

            const formData = new FormData();
            formData.append('data', JSON.stringify({
                success: true,
                key: 'secureKeyCurrencySubmissionXYZ',
                message: '',
                body: currencyData
            }));

            $.ajax({
                url: HOST_URL + 'master/data/submitCurrency',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (res) {
                    if (res.status) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: res.messages,
                            backdrop: true,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK',
                            icon: 'success',
                        }).then((result) => {
                            if(result.isConfirmed){
                                window.location.href = HOST_URL + 'master/data/editCurrency/' +
                                    res.redirect_data.enc_id + '/' +
                                    res.redirect_data.enc_currname;
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res.messages || 'Terjadi kesalahan saat menyimpan data.'
                        });
                        bttn.prop('disabled', false);
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengirim data ke server.'
                    });
                    bttn.prop('disabled', false);
                }
            });
        } else {
            bttn.prop('disabled', false);
        }
    });
}




function saveFinalCurrency() {
    const bttn = $('#btnSave');
    bttn.prop('disabled', true);

    Swal.fire({
        title: 'Warning..!!!',
        text: `Data currency akan disimpan, apakah Anda yakin?`,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: `Ok`,
        icon: 'question',
    }).then((result) => {
        if (result.isConfirmed) {
            const fields = {
                "Currency Name": $('[name="currname"]').val(),
                "Currency Code": $('[name="currcode"]').val(),
            };

            // Validasi wajib isi
            for (let key in fields) {
                if (!fields[key] || fields[key].trim() === "") {
                    Swal.fire({
                        icon: "error",
                        title: "Validasi Gagal",
                        text: key + " tidak boleh kosong!",
                    }).then(() => bttn.prop('disabled', false));
                    return;
                }
            }


            // const is_verified = $('[name="is_verified"]').is(':checked') ? 1 : 0;

            const currencyData = {
                id: $('[name="idcurrency"]').val(), // <-- ini WAJIB ADA agar dikirim ke backend
                currname: $('[name="currname"]').val(),
                currcode: $('[name="currcode"]').val(),

                phutang: $('#phutang').val(),
                pum: $('#pum').val(),
                pbonus: $('#pbonus').val(),
                hutangac: $('#hutangac').val(),
                hutangbiaya1: $('#hutangbiaya1').val(),
                hutangbiaya2: $('#hutangbiaya2').val(),

                // ===== PERKIRAAN PENJUALAN =====
                ppiutang: $('#ppiutang').val(),
                pumjual: $('#pumjual').val(),
                ppendapatan: $('#ppendapatan').val(),
                pretur: $('#pretur').val(),
                pdisc: $('#pdisc').val(),
                pbonusjual: $('#pbonusjual').val(),
                ptunai: $('#ptunai').val(),
                piutangac: $('#piutangac').val(),
                pendapatanac: $('#pendapatanac').val(),
                pps: $('#pps').val(),
                tipe: $('[name="typeform"]').val()
            };

            const formData = new FormData();
            formData.append('data', JSON.stringify({
                success: true,
                key: 'secureKeyCurrencySubmissionXYZ',
                message: '',
                body: currencyData
            }));

            $.ajax({
                url: HOST_URL + 'master/data/saveFinalCurrency',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (res) {
                    if (res.status) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: res.messages,
                            backdrop: true,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK',
                            icon: 'success',
                        }).then((result) => {
                            if(result.isConfirmed){
                                window.location.href = HOST_URL + 'master/data/currency/';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res.messages || 'Terjadi kesalahan saat menyimpan data.'
                        });
                        bttn.prop('disabled', false);
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengirim data ke server.'
                    });
                    bttn.prop('disabled', false);
                }
            });
        } else {
            bttn.prop('disabled', false);
        }
    });
}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH PERMISSION ++++++++++++++++++++++++++++++++++++++++//



$(document).ready(function() {

    tableCurrency();
    $('#checkboxnik').change(function() {
        // this will contain a reference to the checkbox
        if (this.checked) {
            var valnik = $('#nik').val();
            // the checkbox is now checked
            //alert('Checked');
            $('#username').prop('readonly', true);
            $('#username').val(valnik);
        } else {
            // the checkbox is now no longer checked
            //alert('Un Checked');
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
    documentReadable();
    if($('[name="typeform"]').val() != 'INPUT'){
        tableExchangeRate()
    }
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);


});