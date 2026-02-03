/*
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


var save_method; //for save method string
var table;
//"use strict";
function tableSupplier(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#t_tax');
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
                "url": HOST_URL + 'master/data/list_tax',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.status_filter = $('#status_filter').val(); //A,P,S,ALL
                    // data.idgroup = $('#idgroup').val();
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
    var table = $('#t_tax');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}




$('#btn-filter').click(function(){ //button filter event click
    var table = $('#t_tax');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#t_tax');
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
        url: HOST_URL + 'master/data/getMasterTax' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            var dt = json.dataTables.items[0]; // data pertama

            // Isi semua field dengan trim
            $('[name="idtax"]').val($.trim(dt.idtax));
            $('[name="nmtax"]').val($.trim(dt.nmtax).toUpperCase());

        },
        complete: function(){
            $("#loadMe").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });

    $("#loadMe").modal("hide");
}
/* FOR INPUT FUNCTION */








/* ********** VIEW MODE ***************** */
document.addEventListener('DOMContentLoaded', function () {

    // AMBIL ELEMENT SETELAH DOM SIAP
    window.btnUpdate = document.getElementById('btnUpdate');
    window.btnSave   = document.getElementById('btnSave');
    window.btnCancel = document.getElementById('btnCancel');

    // SAFETY CHECK (ANTI ERROR)
    if(!btnUpdate || !btnSave || !btnCancel){
        console.error('Button element not found');
        return;
    }

    setViewMode();
});

/* ================= MODE ================= */

function setViewMode(){
    $('#btnUpdate').removeClass('d-none');
    $('#btnSave').addClass('d-none');
    $('#btnCancel').addClass('d-none');

    $('#idtax').prop('readonly', true).addClass('bg-light');
    $('#nmtax').prop('readonly', true).addClass('bg-light');
}

function setEditMode(){
    $('#btnUpdate').addClass('d-none');
    $('#btnSave').removeClass('d-none');
    $('#btnCancel').removeClass('d-none');

    // RULE FIELD
    if ($('[name="type"]').val() === 'INPUT') {
        $('#idtax').prop('readonly', false).addClass('bg-light'); // PK tidak boleh edit
    } else {
        $('#idtax').prop('readonly', true).addClass('bg-light'); // PK tidak boleh edit
    }

    $('#nmtax').prop('readonly', false).removeClass('bg-light');
}



/* ================= ACTION ================= */
function updateMasterTax(id) {
    if (!id) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops',
            text: 'ID Tax tidak valid'
        });
        return;
    }

    Swal.fire({
        title: 'Update Master Tax?',
        text: 'Anda akan masuk ke halaman update Master Tax',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Update',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href =
                HOST_URL + 'master/data/update_tax?id=' + encodeURIComponent(id);
        }
    });
}

function detailMasterTax(id) {
    if (!id) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops',
            text: 'ID Tax tidak valid'
        });
        return;
    }

    Swal.fire({
        title: 'Detail Master Tax?',
        text: 'Anda akan masuk ke halaman detail Master Tax',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Detail',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#a0a9b2',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href =
                HOST_URL + 'master/data/detail_tax?id=' + encodeURIComponent(id);
        }
    });
}
function hapusMasterTax(id) {

    if (!id) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops',
            text: 'ID Tax tidak valid'
        });
        return;
    }

    Swal.fire({
        title: 'Hapus Master Tax?',
        html: `
            <b>ID Tax:</b> ${id}<br>
            <span class="text-danger">
                Data Master & Detail Tax akan dihapus permanen
            </span>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {

            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: HOST_URL + 'master/data/hapus_master_tax',
                type: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message
                        }).then(() => {
                            reload_table();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan server'
                    });
                }
            });
        }
    });
}


function updateData(){
    setEditMode();
}

function cancelUpdate(){

    Swal.fire({
        title: 'Batalkan perubahan?',
        text: 'Perubahan yang belum disimpan akan dibatalkan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Tidak',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
    }).then((result) => {

        if (result.isConfirmed) {

            // 🔥 kalau loadData pakai AJAX / DataTable
            if (typeof loadData === 'function') {

                const res = loadData();

                // JIKA loadData return Promise
                if (res && typeof res.then === 'function') {
                    res.then(() => {
                        setViewMode();
                    });
                } else {
                    // JIKA loadData synchronous
                    setViewMode();
                }

            } else {
                setViewMode();
            }
        }
    });
}


function saveData(){
    if(!confirm('Simpan perubahan data?')) return;

    $.ajax({
        url: HOST_URL + 'master/data/save_tax_master',
        type: "POST",
        data: $('#formInputTaxMst').serialize(),
        dataType: "json",
        success: function(res){
            if(res.status){

                $('#id').val(res.id);
                $('#idtax').val(res.idtax);

                // reload data DULU
                reloadByIdTax(res.id);

                // lalu pastikan VIEW MODE
                setViewMode();

                // redirect jika INPUT
                if ($('[name="type"]').val() === 'INPUT') {
                    window.location.href =
                        HOST_URL + 'master/data/update_tax?id=' + res.id;
                    return;
                }

            }else{
                alert(res.message || 'Gagal menyimpan data');
            }
        },
        error: function(){
            alert('Server error');
        }
    });
}



function reloadByIdTax(id){
    $.ajax({
        url: HOST_URL + 'master/data/getMasterTax',
        type: 'GET',
        data: { var: id },
        dataType: 'json',
        success: function(json){

            if(!json || !json.dataTables || !json.dataTables.items.length){
                alert('Data tidak ditemukan');
                return;
            }

            const dt = json.dataTables.items[0];

            $('#id').val($.trim(dt.id));
            $('#idtax').val($.trim(dt.idtax));
            $('#nmtax').val($.trim(dt.nmtax));

            // hanya atur FIELD, BUKAN MODE
            $('#idtax').prop('readonly', true).addClass('bg-light');
            $('#nmtax').prop('readonly', true).addClass('bg-light');
        },
        error: function(){
            alert('Server error saat load data');
        }
    });
}




/* ********** VIEW MODE ***************** */


/* ********** TABLE TAX DETAIL  ***************** */


function tableTaxDetail(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tblMasterPajakDetail');
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
                "url": HOST_URL + 'master/data/list_tax_detail',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.status_filter = $('#status_filter').val(); //A,P,S,ALL
                    data.id =  $('[name="id"]').val();
                    data.idtax =  $('[name="idtax"]').val();

                    // data.idgroup = $('#idgroup').val();
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
                    "targets": 0,
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center",
                    "render": function (data, type, row) {
                        // row[1] = kolom ID (ID Tax)
                        return '<input type="checkbox" class="row-check" value="' + row[0] + '">';
                    }
                },
                {
                    "targets": [ -1 ],
                    "orderable": false
                }
            ]

        });

    };
    return initTable();
}

function reload_table_tax()
{
    var table = $('#tblMasterPajakDetail');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

// CHECK ALL
$('#tblMasterPajakDetail thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tblMasterPajakDetail tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tblMasterPajakDetail tbody').on('change', '.row-check', function () {
    const total = $('#tblMasterPajakDetail tbody .row-check').length;
    const checked = $('#tblMasterPajakDetail tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tblMasterPajakDetail').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});
function getSelectedTaxDetail(){
    return $('#tblMasterPajakDetail tbody .row-check:checked')
        .map(function () {
            return $(this).val();
        }).get();
}

function getCheckedDetailIds(){
    let ids = [];
    $('.row-check:checked').each(function(){
        ids.push($(this).val());
    });
    return ids;
}


function btnInputDetail(){
    // reset form modal
    $('#formTaxDetail')[0].reset();
    $('#modalTaxDetailLabel').text('Tambah Tax Detail');
    $('#modalTaxDetail').modal('show');
}

function setSelect2Ajax(selector, value, text) {
    if (!value) return;

    let option = new Option(text || value, value, true, true);
    $(selector).append(option).trigger('change');
}

function btnUpdateDetail(){
    const ids = getCheckedDetailIds();
    console.log('IDS:', ids);   // 🔥 INI KUNCI
    if(ids.length === 0){
        alert('Pilih satu data yang akan diupdate');
        return;
    }

    if(ids.length > 1){
        alert('Update hanya boleh satu data');
        return;
    }

    const id = ids[0];

    $.ajax({
        url: HOST_URL + 'master/data/get_tax_detail',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){
                // isi form modal
                $('#id').val(res.data.id);
                $('#iddtl').val(res.data.iddtl);
                $('#idtax').val(res.data.idtax);
                $('#idgrouptax').val(res.data.idgrouptax);
                $('#nmgrouptax').val(res.data.nmgrouptax);
                $('#kodepajak').val(res.data.kodepajak);
                $('#percentation').val(res.data.percentation);
                setSelect2Ajax('#prk_masukan',     res.data.prk_masukan,     res.data.prk_masukan);
                setSelect2Ajax('#prk_keluaran',    res.data.prk_keluaran,    res.data.prk_keluaran);
                setSelect2Ajax('#prk_fpj_msk',     res.data.prk_fpj_msk,     res.data.prk_fpj_msk);
                setSelect2Ajax('#prk_fpj_klr',     res.data.prk_fpj_klr,     res.data.prk_fpj_klr);
                setSelect2Ajax('#prk_fpj_msk_rep', res.data.prk_fpj_msk_rep, res.data.prk_fpj_msk_rep);
                setSelect2Ajax('#prk_fpj_klr_rep', res.data.prk_fpj_klr_rep, res.data.prk_fpj_klr_rep);
                $('#rumus_dpp_lain').val(res.data.rumus_dpp_lain);
                $('#rumus_pajak').val(res.data.rumus_pajak);



                $('#modalTaxDetailLabel').text('Update Tax Detail');
                $('#modalTaxDetail').modal('show');
            }else{
                alert(res.message || 'Data tidak ditemukan');
            }
        }
    });
}
function btnDeleteDetail(){
    const ids = getCheckedDetailIds();

    if(ids.length === 0){
        alert('Pilih data yang akan dihapus');
        return;
    }

    if(!confirm('Yakin hapus '+ids.length+' data terpilih?')) return;

    $.ajax({
        url: HOST_URL + 'master/data/delete_tax_detail',
        type: 'POST',
        data: { ids: ids },
        dataType: 'json',
        success: function(res){
            if(res.status){
                alert('Data berhasil dihapus');
                $('#tblMasterPajakDetail').DataTable().ajax.reload(null,false);
            }else{
                alert(res.message || 'Gagal hapus data');
            }
        }
    });
}

$('#checkAllDetail').on('change', function(){
    $('.row-check').prop('checked', this.checked);
});

// auto uncheck checkAll jika salah satu dilepas
$(document).on('change','.row-check', function(){
    if(!this.checked){
        $('#checkAllDetail').prop('checked', false);
    }
});


function initSelect2COA(){
    $("#prk_masukan").select2({
        dropdownParent: $('#modalTaxDetail'), // WAJIB di modal
        placeholder: "Pilih Perkiraan",
        allowClear: true,
        // maximumSelectionLength: 1,
        // multiple: false,
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
                    _paramglobal_: "",
                    _parameterx_: "",
                    term: params.term,
                };
            },
            processResults: function (data, params) {
                var searchTerm = $("#prk_masukan").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].idcoa, true, true);
                    $('#prk_masukan').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#prk_masukan').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                }
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
        templateResult: formatCoa, // omitted for brevity, see the source of this page
        templateSelection: formatCoaSelection // omitted for brevity, see the source of this page
    }).on("select2:select", function (e) {

    });

    /* Format Group */
    /* Format Group */
    function formatCoa(repo) {
        if (repo.loading) return repo.text;

        var markup  = "<div class='select2-result-repository'>";
        markup += "  <div class='select2-result-repository__title'><b>" + repo.idcoa + "</b></div>";
        markup += "  <div class='select2-result-repository__description text-muted'>" + repo.nmcoa + "</div>";
        markup += "</div>";

        return markup;
    }

    function formatCoaSelection(repo) {
        if (!repo.idcoa) return repo.text;
        return repo.idcoa + " - " + repo.nmcoa;
    }


    /* Modal Perkiraan */
    $("#prk_keluaran").select2({
        dropdownParent: $('#modalTaxDetail'), // WAJIB di modal
        placeholder: "Pilih Perkiraan",
        allowClear: true,
        // maximumSelectionLength: 1,
        // multiple: false,
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
                    _paramglobal_: "",
                    _parameterx_: "",
                    term: params.term,
                };
            },
            processResults: function (data, params) {
                var searchTerm = $("#prk_keluaran").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].idcoa, true, true);
                    $('#prk_keluaran').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#prk_keluaran').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                }
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
        templateResult: formatCoa, // omitted for brevity, see the source of this page
        templateSelection: formatCoaSelection // omitted for brevity, see the source of this page
    }).on("select2:select", function (e) {

    });


    /*aa*/
    /* Modal Perkiraan */
    $("#prk_fpj_msk").select2({
        dropdownParent: $('#modalTaxDetail'), // WAJIB di modal
        placeholder: "Pilih Perkiraan",
        allowClear: true,
        // maximumSelectionLength: 1,
        // multiple: false,
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
                    _paramglobal_: "",
                    _parameterx_: "",
                    term: params.term,
                };
            },
            processResults: function (data, params) {
                var searchTerm = $("#prk_fpj_msk").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].idcoa, true, true);
                    $('#prk_fpj_msk').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#prk_fpj_msk').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                }
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
        templateResult: formatCoa, // omitted for brevity, see the source of this page
        templateSelection: formatCoaSelection // omitted for brevity, see the source of this page
    }).on("select2:select", function (e) {

    });

    /* Modal Perkiraan */
    $("#prk_fpj_klr").select2({
        dropdownParent: $('#modalTaxDetail'), // WAJIB di modal
        placeholder: "Pilih Perkiraan",
        allowClear: true,
        // maximumSelectionLength: 1,
        // multiple: false,
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
                    _paramglobal_: "",
                    _parameterx_: "",
                    term: params.term,
                };
            },
            processResults: function (data, params) {
                var searchTerm = $("#prk_fpj_klr").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].idcoa, true, true);
                    $('#prk_fpj_klr').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#prk_fpj_klr').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                }
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
        templateResult: formatCoa, // omitted for brevity, see the source of this page
        templateSelection: formatCoaSelection // omitted for brevity, see the source of this page
    }).on("select2:select", function (e) {

    });


/* *  Modal Perkiraan */
    $("#prk_fpj_msk_rep").select2({
        dropdownParent: $('#modalTaxDetail'), // WAJIB di modal
        placeholder: "Pilih Perkiraan",
        allowClear: true,
        // maximumSelectionLength: 1,
        // multiple: false,
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
                    _paramglobal_: "",
                    _parameterx_: "",
                    term: params.term,
                };
            },
            processResults: function (data, params) {
                var searchTerm = $("#prk_fpj_msk_rep").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].idcoa, true, true);
                    $('#prk_fpj_msk_rep').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#prk_fpj_msk_rep').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                }
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
        templateResult: formatCoa, // omitted for brevity, see the source of this page
        templateSelection: formatCoaSelection // omitted for brevity, see the source of this page
    }).on("select2:select", function (e) {

    });


    /* *  Modal Perkiraan */
    $("#prk_fpj_klr_rep").select2({
        dropdownParent: $('#modalTaxDetail'), // WAJIB di modal
        placeholder: "Pilih Perkiraan",
        allowClear: true,
        // maximumSelectionLength: 1,
        // multiple: false,
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
                    _paramglobal_: "",
                    _parameterx_: "",
                    term: params.term,
                };
            },
            processResults: function (data, params) {
                var searchTerm = $("#prk_fpj_klr_rep").data("select2").$dropdown.find("input").val();
                if (data.items.length === 1 && data.items[0].text === searchTerm) {
                    var option = new Option(data.items[0].idcoa, true, true);
                    $('#prk_fpj_klr_rep').append(option).trigger('change').select2("close");
                    // manually trigger the `select2:select` event
                    $('#prk_fpj_klr_rep').trigger({
                        type: 'select2:select',
                        params: {
                            data: data
                        }
                    });
                }
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
        templateResult: formatCoa, // omitted for brevity, see the source of this page
        templateSelection: formatCoaSelection // omitted for brevity, see the source of this page
    }).on("select2:select", function (e) {

    });




}
$('#modalTaxDetail').on('shown.bs.modal', function () {
    initSelect2COA();
});


function saveTaxDetail(){

    if(!confirm('Simpan data Tax Detail?')) return;

    $.ajax({
        url: HOST_URL + 'master/data/save_tax_detail',
        type: 'POST',
        data: $('#formTaxDetail').serialize(),
        dataType: 'json',
        success: function(res){

            if(res.status){

                alert('Data Tax Detail berhasil disimpan');

                // tutup modal
                $('#modalTaxDetail').modal('hide');

                // reload datatable (tanpa reset paging)
                $('#tblMasterPajakDetail')
                    .DataTable()
                    .ajax.reload(null, false);

                // reset form setelah simpan
                $('#formTaxDetail')[0].reset();

            }else{
                alert(res.message || 'Gagal menyimpan data');
            }

        },
        error: function(xhr){
            console.error(xhr.responseText);
            alert('Terjadi kesalahan server');
        }
    });
}


/* ********** END TABLE TAX DETAIL ***************** */


$(document).ready(function() {
    // $('#ketentuanModal').modal({
    //     backdrop: 'static',   // klik luar modal tidak menutup
    //     keyboard: false       // tombol Esc tidak menutup
    // });
    // $('#ketentuanModal').modal('show');
    tableTaxDetail();
    tableSupplier();
    // $('#checkboxnik').change(function() {
    //     // this will contain a reference to the checkbox
    //     if (this.checked) {
    //         var valnik = $('#nik').val();
    //         // the checkbox is now checked
    //         //alert('Checked');
    //         $('#username').prop('readonly', true);
    //         $('#username').val(valnik);
    //     } else {
    //         // the checkbox is now no longer checked
    //         //alert('Un Checked');
    //         $('#username').prop('readonly', false);
    //         $('#username').val('');
    //     }
    // });
    // $('#nik').change(function() {
    //     if ($('#checkboxnik').is(':checked')){
    //         var valnik = $(this).val();
    //         $('#username').val(valnik);
    //     }
    // });
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);

    console.log($('[name="type"]').val() + " loging ")
    if ($('[name="type"]').val() === 'UPDATE') {
        documentReadable();
    }
    if ($('[name="type"]').val() === 'DETAIL') {

        $('#btnSave').hide();

        // disable semua input
        $('#formTaxDetail')
            .find('input, textarea, select')
            .prop('disabled', true);

        $('#formTaxDetail')
            .find('select.select2')
            .prop('disabled', true)
            .trigger('change.select2');

        // 🔥 disable SEMUA tombol di halaman
        $('button, .btn')
            .prop('disabled', true)
            .addClass('disabled');

        // kecuali tombol tertentu
        $('#btnClose, #btnBack')
            .prop('disabled', false)
            .removeClass('disabled');

        documentReadable();
    }





});