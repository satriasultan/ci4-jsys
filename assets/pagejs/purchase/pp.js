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
var initTable;
//"use strict";

function tablePPTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tableppTrx');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter":true,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'purchase/trans/list_pp',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    // data.idbarang = $('#idbarang_filter').val();
                    // data.namasupplier = $('#namasupplier').val();
                    data.status_filter = $('#status_filter').val(); //A,P,S,ALL
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

function reload_tablePPTrx()
{
    var table = $('#tableppTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tableppTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tableppTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});



function tablePPApprvTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tableppapprvTrx');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter":true,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'purchase/trans/list_pp_apprv',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namasupplier = $('#namasupplier').val();
                    data.status = $('#status_filter').val(); //A,P,S,ALL
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

function reload_tablePPApprvTrx()
{
    var table = $('#tableppapprvTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}




function setToCancel(docno) {
    Swal.fire({
        title: 'Batalkan pengajuan PP?',
        text: "Pengajuan dokumen akan dibatalkan",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/purchase/trans/updateStatusPP',
                type: 'POST',
                data: { docno: docno, status: 'C' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Pengajuan dokumen berhasil dibatalkan'
                        }).then(() => {
                            reload_tablePPTrx()
                            reload_tablePPApprvTrx()
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
                }
            });
        }
    });
}



function setToApproved(docno) {
    Swal.fire({
        title: 'Set PP menjadi Approve?',
        text: "Status dokumen akan diubah menjadi Approve",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/purchase/trans/updateStatusPP',
                type: 'POST',
                data: { docno: docno, status: 'A' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi Approve'
                        }).then(() => {
                            reload_tablePPTrx()
                            reload_tablePPApprvTrx()
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
                }
            });
        }
    });
}

function setToDisapproved(docno) {
    Swal.fire({
        title: 'Set PP menjadi Disapprove?',
        text: "Status dokumen akan diubah menjadi Disapprove",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ubah'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + '/purchase/trans/updateStatusPP',
                type: 'POST',
                data: { docno: docno, status: 'F' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status berhasil diubah menjadi Disapprove'
                        }).then(() => {
                            reload_tablePPTrx()
                            reload_tablePPApprvTrx()
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
                }
            });
        }
    });
}

let skipRoleChange = false;



//EDIT ITEM
function documentReadable(){
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: false //Display loader!
    // });
    var docno = $('[name="docno"]').val()

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'purchase/trans/showing_pptemp',
        data: { docno: docno },
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            $('[name="docno"]').val(json.dataTables.items[0].docno).prop('readonly', true);
            var docnoData = json.dataTables.items[0].docno.trim();
            let prefixParts = docnoData.split('/'); // ["JTS", "PH", "25", "08"]
            $('[name="prefix"]').val(prefixParts[0]).prop('readonly', true);
            $('[name="infix"]').val(prefixParts[1]).prop('readonly', true);
            $('[name="sufix"]').val(prefixParts[2]).prop('readonly', true);

            //$('[name="idgroup"]').val(json.dataTables.items[0].idgroup);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_branchjob' + '?var=' + json.dataTables.items[0].cabang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmbranch, datax.items[0].idbranch, true, true);
                $('[name="cabang"]').append(option).trigger('change').prop('disabled',true);

                // manually trigger the `select2:select` event
                $('[name="cabang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            skipRoleChange = true;
            $('[name="docdate"]').val(moment(json.dataTables.items[0].docdate).format('DD-MM-YYYY'));
            $('[name="estpakai"]').val(moment(json.dataTables.items[0].estpakai).format('DD-MM-YYYY'));
            if (prefixParts[0]) {
                // Ambil nilai estpakai yang sudah di-set
                const estpakaiValue = $('[name="estpakai"]').val();
                setupEstpakai(prefixParts[0], estpakaiValue);
            }
            $('[name="pemohon"]').val(json.dataTables.items[0].pemohon);

            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);
            //$('[name="chold"]').val(json.dataTables.items[0].chold.trim()).trigger('change');

            //$('[name="idbarang"]').prop('readonly', true);
            $("#loadMe").modal("hide");

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



// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH GROUP ++++++++++++++++++++++++++++++++++++++++//

var defaultInitialGroupBrng = '';
$("#idbarang").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width:'100%',
    minimumInputLength: 2,
    dropdownParent: $('#modalDetailPP'),
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item',
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
                _paramglobal_: defaultInitialGroupBrng,
                _parameterx_: defaultInitialGroupBrng,
                term: params.term,
            };
        },
        processResults: function (data, params) {
            // var searchTerm = $("#idbarang").data("select2").$dropdown.find("input").val();
            // if (data.items.length === 1 && data.items[0].text === searchTerm) {
            //     var option = new Option(data.items[0].nmbarang, data.items[0].idbarang, true, true);
            //     $('#idbarang').append(option).trigger('change').select2("close");
            //     // manually trigger the `select2:select` event
            //     $('#idbarang').trigger({
            //         type: 'select2:select',
            //         params: {
            //             data: data
            //         }
            //     });
            // }
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
    templateResult: formatItem, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    $("#batch").val(null).trigger('change');
});

/* Format Group */
function formatItem(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idbarang +"   <i class='fa fa-circle-o'></i>   "+ repo.nmbarang +"</div>";
    return markup;
}
function formatItemSelection(repo) {
    return repo.nmbarang || repo.text;
}



function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}




$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});





function editsearchitem(e){
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
            window.location.replace(HOST_URL + 'master/item/edit' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}


/* TABLE PP DETAIL */
function tablePPDetail(){
        /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#tabppdtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "language":  languageDatatable(),
            "paging": false,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter":true,
            "iDisplayLength": -1,
            "ajax": {
                "url": HOST_URL + 'purchase/trans/list_tmp_pp_dtl',
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

        $('#tabppdtl tbody').on('click', 'tr', function(e) {
            // Cegah jika yang diklik adalah checkbox itu sendiri (untuk menghindari double trigger)
            if ($(e.target).is('input[type="checkbox"]')) {
                return;
            }
            
            // Cari checkbox di dalam baris ini
            var checkbox = $(this).find('input[type="checkbox"].row-check');
            
            // Toggle status checkbox
            checkbox.prop('checked', !checkbox.prop('checked'));
            
            // Trigger event change jika diperlukan
            checkbox.trigger('change');
        });
    }

    return initTable();

}


function reload_table_pp_dtl()
{
    var table = $('#tabppdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}



// CHECK ALL
$('#tabppdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tabppdtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tabppdtl tbody').on('change', '.row-check', function () {
    const total = $('#tabppdtl tbody .row-check').length;
    const checked = $('#tabppdtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tabppdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});
function getSelectedPPDetail(){
    return $('#tabppdtl tbody .row-check:checked')
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



function setSelect2Ajax(selector, value, text) {
    if (!value) return;

    let option = new Option(text || value, value, true, true);
    $(selector).append(option).trigger('change');
}
function btnUpdateDetail(){
    const ids = getCheckedDetailIds();
    console.log('IDS:', ids);

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih satu data yang akan diupdate'
        });
        return;
    }

    if(ids.length > 1){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Update hanya boleh satu data'
        });
        return;
    }

    const id = ids[0];

    $.ajax({
        url: HOST_URL + 'purchase/trans/get_pp_detail',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                $('#idurut').val(res.data.idurut);
                $('#description').val(res.data.description);
                $('#docno').val(res.data.docno);
                $('#idbarang').val(res.data.idbarang);
                $('#capexno').val(res.data.capexno);
                $('#nmbarang').val(res.data.nmbarang);
                $('#unit').val(res.data.unit);
                $('#qty').val(res.data.qty);
                setSelect2Ajax('#idbarang', res.data.idbarang, res.data.idbarang);

                $('#modalDetailPPLabel').text('Update PP Detail');
                $('#modalDetailPP').modal('show');

            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Data tidak ditemukan'
                });
            }
        }
    });
}


function btnDeleteDetail(){
    const ids = getCheckedDetailIds();

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih data yang akan dihapus'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin hapus ' + ids.length + ' data terpilih?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if(!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'purchase/trans/delete_pp_detail',
            type: 'POST',
            data: { ids: ids },
            dataType: 'json',
            success: function(res){
                if(res.status){

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $('#tabppdtl').DataTable().ajax.reload(null,false);

                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Gagal hapus data'
                    });
                }
            }
        });

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

$('#formPPMasters').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        iddept: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        docdate: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        estdate: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        // njurnal: {
        //     validators: {
        //         notEmpty: {
        //             message: 'The field can not be empty'
        //         },
        //     }
        // },
        fjurnal: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },

    },
    excluded: [':disabled']
});
$('#formPPdetail').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'fa fa-close',
        validating: 'fa fa-repeat'
    },
    fields: {
        onhand: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        descriptionm: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },

    },
    excluded: [':disabled']
});


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tlistlbm_wacc');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});



var defaultInitialItem = '';
$("#idbarang_filter").select2({
    placeholder: "Type/ Choose your item",
    allowClear: true,
    dropdownParent: $("#filter"),
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    //maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'stock/balance/list_item',
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
                _paramglobal_: defaultInitialItem,
                _parameterx_: defaultInitialItem,
                term: params.term,
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#idbarang_filter").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmbarang, data.items[0].idbarang, true, true);
                $('#idbarang_filter').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#idbarang_filter').trigger({
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
    templateResult: formatItemFilter, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelectionFilter // omitted for brevity, see the source of this page
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
    //var table = $('#tsearchitem');
    //table.DataTable().ajax.reload(); //reload datatable ajax
    ///table.append().search( $(this).val() ).draw();
    //$('#filter').modal('hide');
});
/* Format Group */
function formatItemFilter(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idbarang +"   <i class='fa fa-circle-o'></i>   "+ repo.nmbarang +"</div>";
    return markup;
}
function formatItemSelectionFilter(repo) {
    return repo.nmbarang || repo.text;
}


function savePPDetail() {

    const requiredFields = [
        '#cabang',
        '#prefix',
        '#infix',
        '#sufix',
        '#docdate',
        '#pemohon',
        '#estpakai',
        '#keterangan'
    ];

    for (const selector of requiredFields) {
        const value = $(selector).val();

        if (value === null || value === undefined || String(value).trim() === '') {

            let fieldName = selector.replace('#', '');

            if (fieldName == 'cabang') {
                fieldName = 'Cabang / Job';
            } else if (fieldName == 'prefix') {
                fieldName = 'No. Jurnal';
            } else if (fieldName == 'docdate') {
                fieldName = 'Tanggal';
            } else if (fieldName == 'keterangan') {
                fieldName = 'Keterangan';
            }

            Swal.fire({
                title: 'Peringatan',
                text: 'Data ' + fieldName + ' wajib diisi.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            $('#modalDetailPP').modal('hide');
            $(selector).focus();
            return;
        }
    }

    let idbarang = $('#idbarang').val();
    if(idbarang == null || idbarang == undefined || idbarang == ''){
        Swal.fire({
            title: 'Peringatan',
            text: 'ID Barang Detail Item PP wajib diisi.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    let nmbarang = $('#nmbarang').val();
    if(nmbarang == null || nmbarang == undefined || nmbarang == ''){
        Swal.fire({
            title: 'Peringatan',
            text: 'Nama Barang Detail Item PP wajib diisi.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }
    let unit = $('#unit').val();
    if(unit == null || unit == undefined || unit == ''){
        Swal.fire({
            title: 'Peringatan',
            text: 'Satuan Detail Item PP wajib diisi.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }
    let qty = $('#qty').val();
    if(qty == null || qty == undefined || qty == 0){
        Swal.fire({
            title: 'Peringatan',
            text: 'Quantity Detail Item PP wajib diisi.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }
    let description = $('#description').val();
    if(description == null || description == undefined || description == ''){
        Swal.fire({
            title: 'Peringatan',
            text: 'Keterangan Detail Item PP wajib diisi.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan data PP Detail?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        let formData = new FormData(document.getElementById('formPPDetail'));
        formData.append('docdate', $('#docdate').val());
        formData.append('cabang', $('#cabang').val());
        formData.append('pemohon', $('#pemohon').val());
        formData.append('estpakai', $('#estpakai').val());
        formData.append('keterangan', $('#keterangan').val());

        // docno gabungan (lebih aman pakai hidden header)
        let formattedPrefix = $('#prefix').val().trim().padEnd(3, ' ');
        formData.set('docno', formattedPrefix + '/' + $('#infix').val() + '/' + $('#sufix').val());
        // convert qty ke numeric DB
        formData.set('qty', convertToDbNumber(qty));

        $.ajax({
            url: HOST_URL + 'purchase/trans/savePPDetail',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,

            success: function (res) {

                if (res.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data PP Detail berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    //  if (!res.success) {
                    //     Swal.fire('Error', res.message, 'error');
                    //     return;
                    // }

                    if (res.reload === true) {
                        window.location.reload();
                        return;
                    }

                    $('#modalDetailPP').modal('hide');
                    reload_table_pp_dtl();
                    $('#formPPDetail')[0].reset();

                } else {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: res.message || 'Gagal menyimpan data'
                    });
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan server'
                });
            }
        });

    });
}

function btnInputDetail() {

    let cabang = $('#cabang').val();

    if (!cabang || cabang.trim() === '') {
        Swal.fire({
            title: 'Peringatan',
            text: 'Cabang harus dipilih terlebih dahulu.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });

        $('#cabang').focus();
        return;
    }
    $('#formPPDetail')[0].reset();

    
    // 🔹 Clear select2
    $('#idbarang').val(null).trigger('change');

    // Jika ada select2 lain, lakukan hal sama
    // $('#selectlain').val(null).trigger('change');

    $('#idurut').val(''); // pastikan id kosong (mode insert)

    $('#modalDetailPPLabel').text('Tambah Item Detail');
    $('#modalDetailPP').modal('show');
}




let currentKodeSuffix = '';

$('#cabang').on('change', function () {
    if (skipRoleChange) return; // skip

    let idbranch = $(this).val();

    if(idbranch){
        $.ajax({
                url: HOST_URL + '/purchase/trans/getBranchInfo',
                method: 'GET',
                data: { idbranch: idbranch },
                dataType: 'json',
                success: function (res) {
                    if (!res.success) {
                        Swal.fire('Error', res.message, 'warning');
                        return;
                    }

                    currentKodeSuffix = res.kode_suffix; // PT / PA / PB
                    $('#infix').val(res.infix);          // YYMM
                    $('#prefix').val('PPB');             // default
                    $('#sufix').val(currentKodeSuffix + '0001');

                    var infix = (res.infix || '').toString();
                    if (infix.length === 4) {
                        $('#docdate').prop('disabled', false);
                        var yy = infix.substring(0,2);
                        var mm = infix.substring(2,4);
                        var year = 2000 + parseInt(yy,10);
                        var month = parseInt(mm,10) - 1; // moment month index

                        var today = moment();

                        var startDate = moment([year, month, 1]);
                        var endDate = moment(startDate).endOf('month');

                        var $el = $('#docdate');
                        var drp = $el.data('daterangepicker');

                        if (drp) {
                            // update limits & selected date
                            drp.minDate = startDate;
                            drp.maxDate = endDate;
                            drp.setStartDate(startDate);
                            drp.setEndDate(startDate);
                        } else {
                            // fallback: (re)initialize with limits
                            $el.daterangepicker({
                                autoUpdateInput: false,
                                singleDatePicker: true,
                                showDropdowns: true,
                                startDate: today,
                                minDate: startDate,
                                maxDate: endDate,
                                locale: { format: 'DD-MM-YYYY' },
                                cancelLabel: 'Clear'
                            });
                            // rebind handlers jika perlu (apply/cancel)
                            $el.on('apply.daterangepicker', function(ev, picker) {
                                $(this).val(picker.startDate.format('DD-MM-YYYY'));
                            });
                            $el.on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                            });
                        }

                        // isi input langsung (opsional)
                        $el.val(today.format('DD-MM-YYYY'));
                    }

                    $('#docno').val(
                        'PPB/' + res.infix + '/' + currentKodeSuffix + '0001'
                    );
                }
            });
    }
    
});


$('#prefix').on('blur', function () {
    let prefix = $(this).val().toUpperCase();
    let infix  = $('#infix').val();

    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + '/purchase/trans/getNextSuffixPP',
        method: 'GET',
        data: {
            prefix: prefix,
            infix: infix,
            kode_suffix: currentKodeSuffix
        },
        dataType: 'json',
        success: function (res) {
            if (!res.success) {
                Swal.fire('Error', res.message, 'warning');
                return;
            }

            $('#sufix').val(res.suffix);
            $('#docno').val(
                prefix + '/' + infix + '/' + res.suffix
            );
            setupEstpakai(prefix);
        }
    });
});

function setupEstpakai(prefix = '', existingValue = null) {
    const type = getPrefixType(prefix);
    const docDateValue = $('#docdate').val();
    const options = generateDateOptions(type, docDateValue);
    
    // Hapus daterangepicker lama
    if ($('#estpakai').data('daterangepicker')) {
        $('#estpakai').daterangepicker('destroy');
    }
    
    // Tentukan tanggal default
    let defaultDate;
    let selectedValue = existingValue;
    
    // Parse docDate
    const docDateMoment = moment(docDateValue, 'DD-MM-YYYY');
    const isDocDateValid = docDateMoment.isValid();
    
    if (existingValue) {
        // Coba parse existing value
        const parsed = moment(existingValue, 'DD-MM-YYYY');
        if (parsed.isValid()) {
            defaultDate = parsed;
            selectedValue = existingValue;
        } else {
            // Jika existingValue tidak valid, gunakan docDate
            if (isDocDateValid) {
                defaultDate = docDateMoment;
                selectedValue = defaultDate.format('DD-MM-YYYY');
            } else {
                defaultDate = options.length > 0 ? options[0].date : moment();
                selectedValue = defaultDate.format('DD-MM-YYYY');
            }
        }
    } else {
        // Gunakan docDate + 1 minggu/bulan sebagai default
        if (isDocDateValid) {
            // Default ke +1 minggu (atau +1 bulan untuk import)
            const defaultOption = options.length > 0 ? options[0] : null;
            if (defaultOption) {
                defaultDate = defaultOption.date;
                selectedValue = defaultOption.value;
            } else {
                defaultDate = docDateMoment;
                selectedValue = defaultDate.format('DD-MM-YYYY');
            }
        } else {
            defaultDate = options.length > 0 ? options[0].date : moment();
            selectedValue = defaultDate.format('DD-MM-YYYY');
        }
    }
    
    // Inisialisasi daterangepicker
    $('#estpakai').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        startDate: defaultDate,
        locale: { format: 'DD-MM-YYYY' },
        cancelLabel: 'Clear'
    });
    
    $('#estpakai').val(selectedValue);
    
    // Hapus event lama
    $('#estpakai').off('apply.daterangepicker cancel.daterangepicker');
    
    // Event apply
    $('#estpakai').on('apply.daterangepicker', function(ev, picker) {
        const dateStr = picker.startDate.format('DD-MM-YYYY');
        $(this).val(dateStr);
        updateActiveButton(dateStr);
    });
    
    // Event cancel
    $('#estpakai').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('.estpakai-btn').removeClass('active').css({
            'background-color': '#ffffff',
            'color': '#007bff'
        });
    });
    
    // Buat tombol pilihan cepat
    let buttonHtml = '<div style="display:flex; flex-wrap:wrap; gap:5px; margin-top:5px;">';
    
    options.forEach((opt) => {
        // Cek apakah nilai ini match dengan selectedValue
        const isActive = (selectedValue && opt.value === selectedValue);
        
        buttonHtml += `<button type="button" 
                            class="estpakai-btn ${isActive ? 'active' : ''}" 
                            data-date="${opt.value}"
                            style="
                                padding: 6px 16px;
                                background-color: ${isActive ? '#007bff' : '#ffffff'};
                                color: ${isActive ? '#ffffff' : '#007bff'};
                                border: 1px solid #007bff;
                                border-radius: 4px;
                                cursor: pointer;
                                font-size: 13px;
                                transition: all 0.3s;
                            "
                            onmouseover="if(!this.classList.contains('active')){this.style.backgroundColor='#007bff'; this.style.color='#ffffff';}"
                            onmouseout="if(!this.classList.contains('active')){this.style.backgroundColor='#ffffff'; this.style.color='#007bff';}"
                            onclick="selectEstpakai('${opt.value}', this)">
                            ${opt.label}
                        </button>`;
    });
    
    buttonHtml += '</div>';
    
    // Tambahkan tombol setelah input
    if ($('#estpakai_buttons').length === 0) {
        $('#estpakai').after(`<div id="estpakai_buttons">${buttonHtml}</div>`);
    } else {
        $('#estpakai_buttons').html(buttonHtml);
    }
}

// Fungsi update active button
function updateActiveButton(date) {
    $('.estpakai-btn').each(function() {
        const isActive = $(this).data('date') === date;
        $(this).toggleClass('active', isActive);
        
        if (isActive) {
            $(this).css({
                'background-color': '#007bff',
                'color': '#ffffff'
            });
        } else {
            $(this).css({
                'background-color': '#ffffff',
                'color': '#007bff'
            });
        }
    });
}

function selectEstpakai(date, element) {
    $('#estpakai').val(date);
    
    const picker = $('#estpakai').data('daterangepicker');
    if (picker) {
        picker.setStartDate(moment(date, 'DD-MM-YYYY'));
    }
    
    updateActiveButton(date);
}

function getPrefixType(prefix) {
    const cleanPrefix = prefix ? prefix.trim() : '';
    
    // Cek import: 3 char dan diakhiri 'I'
    if (cleanPrefix.length === 3 && cleanPrefix.endsWith('I')) {
        return 'import';
    }
    
    // Cek local: 'JI' atau lainnya
    if (cleanPrefix === 'JI') {
        return 'local';
    }
    
    return 'local'; // default
}

function generateDateOptions(type, docDate = null) {
    // Gunakan docDate jika ada, jika tidak gunakan hari ini
    let baseDate;
    if (docDate) {
        baseDate = moment(docDate, 'DD-MM-YYYY');
        // Jika docDate tidak valid, fallback ke hari ini
        if (!baseDate.isValid()) {
            baseDate = moment();
        }
    } else {
        baseDate = moment();
    }
    
    let options = [];
    
    if (type === 'import') {
        for (let i = 1; i <= 6; i++) {
            const date = baseDate.clone().add(i, 'months');
            options.push({
                value: date.format('DD-MM-YYYY'),
                label: `+${i} Bulan`,
                date: date
            });
        }
    } else {
        for (let i = 1; i <= 4; i++) {
            const date = baseDate.clone().add(i, 'weeks');
            options.push({
                value: date.format('DD-MM-YYYY'),
                label: `+${i} Minggu`,
                date: date
            });
        }
    }
    
    return options;
}




var defaultInitialBranch = '';
$("#cabang").select2({
    placeholder: "Type/ Choose your Branch",
    allowClear: true,
    width: '100%',
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_branchjob',
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
                _paramglobal_: defaultInitialBranch,
                _parameterx_: defaultInitialBranch,
                term: params.term,
            };
        },
        processResults: function (data, params) {
            var searchTerm = $("#cabang").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmbranch, data.items[0].idbranch, true, true);
                $('#cabang').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#cabang').trigger({
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
    templateResult: formatBranch, // omitted for brevity, see the source of this page
    templateSelection: formatBranchSelection // omitted for brevity, see the source of this page
}).on("change", function () {
    console.log('Selecting =>' + $(this).val());
    //var table = $('#tsearchitem');
    //table.DataTable().ajax.reload(); //reload datatable ajax
    ///table.append().search( $(this).val() ).draw();
    //$('#filter').modal('hide');
});
/* Format Group */
function formatBranch(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idbranch +"   <i class='fa fa-circle-o'></i>   "+ repo.nmbranch +"</div>";
    return markup;
}
function formatBranchSelection(repo) {
    return repo.nmbranch || repo.text;
}

/* TABEL LIST PO FINAL */


//OPEN SCANNER
function open_scan()
{
    //$('[name="dob"]').datepicker('update',data.dob);
    read_qrcode();
    $('#open_scan').modal('show'); // show bootstrap modal when complete loaded
    $('.modal-title').text('Open Scanner'); // Set title to Bootstrap modal title
}
function read_qrcode(){
    function onScanSuccess(decodedText, decodedResult) {
        play();
        //alert(`Code scanned = ${decodedText}`, decodedResult);
        //alert('Code scanned Kintil= ' + decodedText + '');
        //$('#searchitem').val(decodedText).trigger('keyup');

        $('#searchitem').val(decodedText);
        $('#open_scan').modal('hide');
        var table = $('#tsearchitem');
        table.DataTable().ajax.reload();

        //
        // var _this = $(decodedText); // copy of this object for further usage
        // clearTimeout(timer);
        // timer = setTimeout(function() {
        //     //$('#searchitem').val('');
        //     $('#searchitem').val(decodedText);
        //     $('#open_scan').modal('hide');
        //     var table = $('#tsearchitem');
        //     table.DataTable().ajax.reload();
        // }, 1000);
        html5QrcodeScanner.clear();
    }
    var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);

}

var audio = document.getElementById('chatAudio');
function play(){
    audio.play()
}

function formatNewdept(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.kddept +"   <i class='fa fa-circle-o'></i>   "+ repo.nmdept +"  </div>";
    return markup;
}

function formatNewdeptSelection(repo) {
    return repo.nmdept || repo.text;
}
var defaultInitialNewDept = '';
$("#iddept").select2({
    placeholder: "Pilih Bagian",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_departmen',
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
                _paramglobal_: defaultInitialNewDept,
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
    templateResult: formatNewdept, // omitted for brevity, see the source of this page
    templateSelection: formatNewdeptSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
});

$("#fjurnal").on("change", function () {
    //$("#njurnal").val('00011');
    //Ajax Load data from ajax
    $.ajax({
        url: HOST_URL + 'purchase/purchaseorder/njurnal' + '/?var=' + $("#fjurnal").val(),
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $("#njurnal").val(data.njurnal);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
});


$("#docjns").on("change", function () {
console.log($(this).val() + 'H');
    if ($(this).val()==='PO') {

        $('.sref').remove();
        $('.sreference').append('<div class="sref"> <label for="description">Reference ID/PO Number</label><select name="docref" id="docref2" class="form-control" required></select></div>');

        //LOAD PO
        var defaultInitialPO = $("#docref2").val();
        $("#docref2").select2({
            placeholder: "Ketik PO Outstanding",
            allowClear: true,
            //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
            maximumSelectionLength: 1,
            multiple: false,
            ajax: {
                url: HOST_URL + 'api/globalmodule/list_outstanding_po',
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
                        _paramglobal_: defaultInitialPO,
                        _parameterx_: defaultInitialPO,
                        term: params.term,
                    };
                },
                processResults: function(data, params) {

                    var searchTerm = $("#docref2").data("select2").$dropdown.find("input").val();
                    if (data.items.length === 1 && data.items[0].text === searchTerm) {
                        var option = new Option(data.items[0].docno, data.items[0].docno, true, true);
                        $('#docref2').append(option).trigger('change').select2("close");
                        // manually trigger the `select2:select` event
                        $('#docref2').trigger({
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
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            // minimumInputLength: 1,
            templateResult: formatOutstandingPO, // omitted for brevity, see the source of this page
            templateSelection: formatOutstandingPOSelection // omitted for brevity, see the source of this page
        }).on("change", function () {

        });


        /* Format Group */
        function formatOutstandingPO(repo) {
            if (repo.loading) return repo.text;
            var markup ="<div class='select2-result-repository__description'>" + repo.docno +"</div>";
            return markup;
        }

        function formatOutstandingPOSelection(repo) {
            return repo.docno || repo.text;
        }
    } else {
        $('.sref').remove();
        $('.sreference').append('<div class="sref"> <label for="description">Reference ID/PO Number</label> <input type="text" name="docref" class="form-control" id="docref1"  style="text-transform: uppercase" placeholder="Reference ID" required></div>');
    }


});

// COST CENTER
function formatCostcenter(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idcostcenter +"   <i class='fa fa-circle-o'></i>   "+ repo.nmcostcenter +"</div>";
    return markup;
}

function formatCostcenterSelection(repo) {
    return repo.nmcostcenter || repo.text;
}
//var defaultInitialDivision = $("#newdept").val();
$("#idcostcenter").select2({
    placeholder: "Ketik/Pilih Cost Center",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_costcenter',
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
    templateResult: formatCostcenter, // omitted for brevity, see the source of this page
    templateSelection: formatCostcenterSelection // omitted for brevity, see the source of this page
});


$(document).ready(function() {
    // Handle form submission event
    // Handle form submission event

    tablePPTrx();
    tablePPApprvTrx();
    tablePPDetail();
    // tableItem();
    //read_qrcode();
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
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);
    // if ($('[name="type"]').val() === 'EDIT') {
    //     documentReadable();
    // }
    //console.log($('[name="type"]').val());
    // if ($('[name="typeform"]').val() === 'INPUT' || $('[name="typeform"]').val() === 'UPDATE' || $('[name="typeform"]').val() === 'DELETE' ) {
        documentReadable();
    // }
    $("#loadMe").modal("hide");




});