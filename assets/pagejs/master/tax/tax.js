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

/* ================= BOOTSTRAP 5 MODAL HELPER =================
   Catatan : project memakai Bootstrap 5 (tanpa plugin jQuery),
   sehingga $('#modal').modal('show') TIDAK tersedia.
   Gunakan helper di bawah ini. */
function showBsModal(selector) {
    var el = document.querySelector(selector);
    if (!el || !window.bootstrap || !bootstrap.Modal) {
        return false;
    }
    bootstrap.Modal.getOrCreateInstance(el).show();
    return true;
}

function hideBsModal(selector) {
    var el = document.querySelector(selector);
    if (!el || !window.bootstrap || !bootstrap.Modal) {
        return false;
    }
    var inst = bootstrap.Modal.getInstance(el);
    if (inst) {
        inst.hide();
    }
    return true;
}

/* idtax master (scope form master, bukan hidden field modal) */
function masterIdtax() {
    return $.trim($('#formInputTaxMst [name="idtax"]').val() || '');
}

function masterType() {
    return $.trim($('#formInputTaxMst [name="type"]').val() || '');
}

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
    hideBsModal('#filter');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#t_tax');
    table.DataTable().ajax.reload(); //reload datatable ajax
    hideBsModal('#filter');
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
        url: HOST_URL + 'master/data/getMasterTax' + '?var=' + $('#formInputTaxMst [name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            if (!json || !json.dataTables || !json.dataTables.items || !json.dataTables.items.length) {
                return data;
            }

            var dt = json.dataTables.items[0]; // data pertama

            // Isi field master (scope form master)
            $('#formInputTaxMst [name="id"]').val($.trim(dt.id));
            $('#idtax_mst').val($.trim(dt.idtax));
            $('#nmtax_mst').val($.trim(dt.nmtax).toUpperCase());

        },
        complete: function(){
            hideBsModal('#loadMe');
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            hideBsModal('#loadMe');
        }
    });

    hideBsModal('#loadMe');
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

    // halaman INPUT langsung siap diisi (tanpa klik Update)
    if (masterType() === 'INPUT') {
        setEditMode();
    }
});

/* ================= MODE ================= */

function setViewMode(){
    $('#btnUpdate').removeClass('d-none');
    $('#btnSave').addClass('d-none');
    $('#btnCancel').addClass('d-none');

    $('#idtax_mst').prop('readonly', true).addClass('bg-light');
    $('#nmtax_mst').prop('readonly', true).addClass('bg-light');
}

function setEditMode(){
    // mode DETAIL tidak boleh diedit
    if (masterType() === 'DETAIL') {
        setViewMode();
        return;
    }

    $('#btnUpdate').addClass('d-none');
    $('#btnSave').removeClass('d-none');
    $('#btnCancel').removeClass('d-none');

    // RULE FIELD : Kode Pajak hanya bisa diisi pada saat INPUT
    if (masterType() === 'INPUT') {
        $('#idtax_mst').prop('readonly', false).removeClass('bg-light').focus();
    } else {
        $('#idtax_mst').prop('readonly', true).addClass('bg-light');
    }

    $('#nmtax_mst').prop('readonly', false).removeClass('bg-light');
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

                $('#id_mst').val(res.id);
                $('#idtax_mst').val(res.idtax);

                // reload data DULU
                reloadByIdTax(res.id);

                // lalu pastikan VIEW MODE
                setViewMode();

                // redirect jika INPUT
                if (masterType() === 'INPUT') {
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

            $('#id_mst').val($.trim(dt.id));
            $('#idtax_mst').val($.trim(dt.idtax));
            $('#nmtax_mst').val($.trim(dt.nmtax));

            // hanya atur FIELD, BUKAN MODE
            $('#idtax_mst').prop('readonly', true).addClass('bg-light');
            $('#nmtax_mst').prop('readonly', true).addClass('bg-light');
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
                    // scope ke form master (id dan idtax tidak boleh dobel id)
                    data.id    = $('#formInputTaxMst [name="id"]').val();
                    data.idtax = masterIdtax();

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
                        // row[0] = kolom ID (PK surrogate) dari server
                        return '<input type="checkbox" class="row-check" value="' + row[0] + '">';
                    }
                },
                {
                    // pemetaan kolom order di server hanya untuk 6 kolom,
                    // supaya tidak error saat sorting kolom lain
                    "targets": "_all",
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
    $('#tblMasterPajakDetail tbody .row-check:checked').each(function(){
        ids.push($(this).val());
    });
    return ids;
}


/* data sementara untuk modal (dipakai saat event shown.bs.modal) */
var taxDetailPendingData = null;

function resetTaxDetailForm(){
    var form = document.getElementById('formTaxDetail');
    if (form) { form.reset(); }

    $('#iddtl').val('');
    $('#idtax_dtl').val('');
    $('#status').val('P');
    $('#chold').val('NO');

    $('#prk_masukan, #prk_keluaran, #prk_fpj_msk, #prk_fpj_klr, #prk_fpj_msk_rep, #prk_fpj_klr_rep')
        .each(function(){
            var $el = $(this);
            $el.empty();
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.trigger('change');
            }
        });
}

function btnInputDetail(){
    if (!masterIdtax()) {
        Swal.fire({
            icon: 'warning',
            title: 'Master Tax belum tersimpan',
            text: 'Isi dan simpan Kode Pajak master terlebih dahulu'
        });
        return;
    }

    taxDetailPendingData = null;
    resetTaxDetailForm();

    $('#modalTaxDetailLabel').html('<i class="fa fa-plus"></i> Input Tax Detail');

    if (!showBsModal('#modalTaxDetail')) {
        alert('Komponen modal tidak tersedia');
    }
}

function setSelect2Ajax(selector, value, text) {
    var $el = $(selector);

    $el.empty();

    if (value) {
        var option = new Option(text || value, value, true, true);
        $el.append(option);
    }

    $el.trigger('change');

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.trigger('change.select2');
    }
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
            if(res.status && res.data){
                // siapkan data, isi form dilakukan saat modal tampil
                taxDetailPendingData = res.data;

                $('#modalTaxDetailLabel').html('<i class="fa fa-edit"></i> Update Tax Detail');

                if (!showBsModal('#modalTaxDetail')) {
                    alert('Komponen modal tidak tersedia');
                }
            }else{
                alert(res.message || 'Data tidak ditemukan');
            }
        }
    });
}

/* Isi form modal dari data detail (dipanggil setelah modal tampil) */
function applyTaxDetailForm(row) {
    if (!row) {
        return;
    }

    $('#iddtl').val(row.id || '');
    $('#idtax_dtl').val($.trim(row.idtax || masterIdtax()));
    $('#idgrouptax').val($.trim(row.idgrouptax));
    $('#nmgrouptax').val($.trim(row.nmgrouptax));
    $('#kodepajak').val($.trim(row.kodepajak));
    $('#percentation').val(row.percentation === null ? '' : row.percentation);
    $('#rumus_dpp_lain').val($.trim(row.rumus_dpp_lain));
    $('#rumus_pajak').val($.trim(row.rumus_pajak));

    setSelect2Ajax('#prk_masukan',     $.trim(row.prk_masukan),     $.trim(row.prk_masukan));
    setSelect2Ajax('#prk_keluaran',    $.trim(row.prk_keluaran),    $.trim(row.prk_keluaran));
    setSelect2Ajax('#prk_fpj_msk',     $.trim(row.prk_fpj_msk),     $.trim(row.prk_fpj_msk));
    setSelect2Ajax('#prk_fpj_klr',     $.trim(row.prk_fpj_klr),     $.trim(row.prk_fpj_klr));
    setSelect2Ajax('#prk_fpj_msk_rep', $.trim(row.prk_fpj_msk_rep), $.trim(row.prk_fpj_msk_rep));
    setSelect2Ajax('#prk_fpj_klr_rep', $.trim(row.prk_fpj_klr_rep), $.trim(row.prk_fpj_klr_rep));
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

function initSelect2COA(){
    // cukup diinisialisasi sekali (hindari re-init saat modal dibuka ulang)
    if (window.__taxDetailSelect2Ready) {
        return;
    }
    window.__taxDetailSelect2Ready = true;

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

    // isi data update (jika ada) setelah select2 siap
    applyTaxDetailForm(taxDetailPendingData);

    // pastikan idtax detail selalu mengacu ke master
    if (!$.trim($('#idtax_dtl').val())) {
        $('#idtax_dtl').val(masterIdtax());
    }

    taxDetailPendingData = null;

    $('#idgrouptax').trigger('focus');
});


function saveTaxDetail(){

    if(!confirm('Simpan data Tax Detail?')) return;

    var idtax = $.trim($('#idtax_dtl').val()) || masterIdtax();
    $('#idtax_dtl').val(idtax);

    if (!idtax) {
        Swal.fire({
            icon: 'warning',
            title: 'Kode Pajak kosong',
            text: 'Simpan Master Tax terlebih dahulu'
        });
        return;
    }

    if (!$.trim($('#idgrouptax').val())) {
        Swal.fire({
            icon: 'warning',
            title: 'Jenis belum diisi',
            text: 'Jenis pajak wajib diisi'
        });
        $('#idgrouptax').trigger('focus');
        return;
    }

    $.ajax({
        url: HOST_URL + 'master/data/save_tax_detail',
        type: 'POST',
        data: $('#formTaxDetail').serialize(),
        dataType: 'json',
        success: function(res){

            if(res.status){

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message || 'Data Tax Detail berhasil disimpan'
                });

                // tutup modal
                hideBsModal('#modalTaxDetail');

                // reload datatable (tanpa reset paging)
                $('#tblMasterPajakDetail')
                    .DataTable()
                    .ajax.reload(null, false);

                // reset form setelah simpan
                taxDetailPendingData = null;
                resetTaxDetailForm();

            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: res.message || 'Gagal menyimpan data'
                });
            }

        },
        error: function(xhr){
            console.error(xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan server'
            });
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

    if (masterType() === 'UPDATE') {
        documentReadable();
    }

    if (masterType() === 'DETAIL') {

        $('#btnSave').addClass('d-none');
        $('#btnUpdate').addClass('d-none');

        // disable semua input master
        $('#formInputTaxMst')
            .find('input, textarea, select')
            .prop('disabled', true);

        // 🔥 disable SEMUA tombol di halaman
        $('.corp-form-container .card .card-actions button')
            .prop('disabled', true)
            .addClass('disabled');

        // kecuali tombol navigasi di footer
        $('.corp-form-container .card-footer .btn')
            .prop('disabled', false)
            .removeClass('disabled');

        documentReadable();
    }





});