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
let skipRoleChange = false;
//"use strict";

/* VIUW UTAMA*/
function table_workingorder() {
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tworkingorder');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language": languageDatatable(),
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter": true,
            "lengthMenu": [
                [10, 25, 50, -1],
                ['10 rows', '25 rows', '50 rows', 'Show all']
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength', 'excel'
            ],
            "ajax": {
                "url": HOST_URL + 'production/trans/list_workingorder_mst',
                "type": "POST",
                "data": function (data) {
                    data.tglrange = $('#tglrange').val();
                    data.idbarang = $('#idbarang_filter').val();
                    data.namasupplier = $('#namasupplier').val();
                    data.status = $('#status_filter').val(); //A,P,S,ALL
                },
                "dataFilter": function (data) {
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
                    "targets": [-1], //last column
                    "orderable": false, //set not orderable
                },
            ],

        });

    };


    return initTable();
}

function reload_workingorder() {
    var table = $('#tworkingorder');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$('#btn-filter-tx').click(function () { //button filter event click
    var table = $('#tworkingorder');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset-tx').click(function () { //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tworkingorder');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

/*-------- PENTING DI SETIAP MENU ---------*/


let currentKodeSuffix = '';

function generateDocNumber(prefix, infix, suffix) {
    if (!prefix || !infix || !suffix) return;
    $('#docno').val(prefix + '/' + infix + '/' + suffix);
}

$('#cabang').on('change', function () {

    if (skipRoleChange) return;

    let idbranch = $(this).val();

    if (!idbranch) return;

    $.ajax({
        url: HOST_URL + '/persediaan/trans/getBranch_ajustment_stock',
        method: 'GET',
        data: {idbranch: idbranch},
        dataType: 'json',
        success: function (res) {

            if (!res.success) {
                Swal.fire('Error', res.message, 'warning');
                return;
            }

            currentKodeSuffix = res.kode_suffix;

            $('#infix').val(res.infix);

            // SET PREFIX + TRIGGER CHANGE
            $('#prefix').val('WO').trigger('change');

            $('#sufix').val(currentKodeSuffix + '0001');

            var infix = (res.infix || '').toString();

            if (infix.length === 4) {

                $('#docdate').prop('disabled', false);

                var yy = infix.substring(0, 2);
                var mm = infix.substring(2, 4);

                var year = 2000 + parseInt(yy, 10);
                var month = parseInt(mm, 10) - 1;

                var today = moment();
                var startDate = moment([year, month, 1]);
                var endDate = moment(startDate).endOf('month');

                var $el = $('#docdate');
                var drp = $el.data('daterangepicker');

                if (drp) {

                    drp.minDate = startDate;
                    drp.maxDate = endDate;
                    drp.setStartDate(startDate);
                    drp.setEndDate(startDate);

                } else {

                    $el.daterangepicker({
                        autoUpdateInput: false,
                        singleDatePicker: true,
                        showDropdowns: true,
                        startDate: today,
                        minDate: startDate,
                        maxDate: endDate,
                        locale: {format: 'YYYY-MM-DD'},
                        cancelLabel: 'Clear'
                    });

                    $el.on('apply.daterangepicker', function (ev, picker) {
                        $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    });

                    $el.on('cancel.daterangepicker', function (ev, picker) {
                        $(this).val('');
                    });
                }

                $el.val(today.format('YYYY-MM-DD'));
            }

            generateDocNumber('WO', res.infix, currentKodeSuffix + '0001');

        }
    });

});


$('#prefix').on('change', function () {

    let prefix = $(this).val().toUpperCase();
    $(this).val(prefix);

    let infix = $('#infix').val();

    if (!prefix || !infix || !currentKodeSuffix) return;

    $.ajax({
        url: HOST_URL + '/production/trans/getNextSuffix_workingorder_mst',
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

            generateDocNumber(prefix, infix, res.suffix);

        }
    });

});


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
        data: function (params) {
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
    escapeMarkup: function (markup) {
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
    var markup = "<div class='select2-result-repository__description'>" + repo.idbranch + "   <i class='fa fa-circle-o'></i>   " + repo.nmbranch + "</div>";
    return markup;
}

function formatBranchSelection(repo) {
    return repo.nmbranch || repo.text;
}

/*-------- PENTING DI SETIAP MENU ---------*/

function documentReadable() {

    //showLoader();

    var docno = $('[name="docno"]').val();

    $.getJSON(HOST_URL + 'production/trans/showing_tmp_workingorder_mst', {docno: docno})
        .done(function (response) {

            if (!response.dataTables || !response.dataTables.items.length) {
                hideLoader();
                console.log("Data kosong");
                return;
            }

            const item = response.dataTables.items[0];

            // ===============================
            // SET BASIC FIELD (langsung saja)
            // ===============================
            $('[name="docno"]').val(item.docno).prop('readonly', true);

            let prefixParts = item.docno.trim().split('/');
            $('[name="prefix"]').val(prefixParts[0]).prop('readonly', true);
            $('[name="infix"]').val(prefixParts[1]).prop('readonly', true);
            $('[name="sufix"]').val(prefixParts[2]).prop('readonly', true);
            // setJtsValue('[name="buildfor"]', convertToDbNumber(item.buildfor));
            // setJtsValue('[name="minimumqty"]', convertToDbNumber(item.minimumqty));
            $('[name="docdate"]').val(item.docdate).prop('disabled', true);
            $('[name="docdatefinish"]').val(item.docdatefinish).prop('disabled', true);
            // $('[name="activedate"]').val(item.activedate).prop('disabled', true);
            // $('[name="docref"]').val(item.docref).prop('disabled', false);
            $('[name="keterangan"]').val(item.keterangan);
            $('[name="alamatcustomer"]').val(item.alamatcustomer);
            $('[name="nmcustomer"]').val(item.nmcustomer);
            $('[name="noso"]').val(item.noso);
            // $('#ttlprice').text(
            //     convertToDbNumber(item.ttlprice || 0)
            //         .toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         })
            // );
            // $('#ttlmaterial').text(
            //     convertToDbNumber(item.ttlmaterial || 0)
            //         .toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         })
            // );
            // $('#ttlcost').text(
            //     convertToDbNumber(item.ttlcost || 0)
            //         .toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         })
            // );
            // $('#ttlwip').text(
            //     convertToDbNumber(item.ttlwip || 0)
            //         .toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         })
            // );
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_customer' + '?var=' + item.kdcustomer,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {

            // Tambahkan data alamat dan phone ke object
                var customerData = datax.items[0];
                customerData.alamat = item.alamatcustomer;
                // customerData.phone = data.phone;
                
                // create the option dan simpan data lengkap
                var option = new Option(customerData.nmcustomer, customerData.kdcustomer, true, true);
                $(option).data('customer-data', customerData); // Simpan data lengkap
                
                $('[name="kdcustomer"]').append(option).trigger('change');
                defaultInitialCustSO = item.kdcustomer

                // Set alamat dan phone langsung
                $("#alamatcustomer").val(item.alamatcustomer).prop('readonly', true);
                // $("#phone").val(data.phone).prop('readonly', true);
            });


            // $.ajax({
            //     type: 'GET',
            //     url: HOST_URL + 'api/globalmodule/list_unit' + '?var=' + item.buildunit,
            //     dataType: 'json',
            //     delay: 250,
            // }).then(function (datax) {
            //     // create the option and append to Select2
            //     var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
            //     $('[name="buildunit"]').append(option).trigger('change');

            //     // manually trigger the `select2:select` event
            //     $('[name="buildunit"]').trigger({
            //         type: 'select2:select',
            //         params: {
            //             data: datax
            //         }
            //     });
            // });

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_branchjob' + '?var=' + item.cabang,
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

            // ===============================
            // LOAD SEMUA MASTER DATA PARALEL
            // ===============================


//             const branch1 = $.getJSON(
//                 HOST_URL + 'api/globalmodule/list_branchjob',
//                 { var: item.cabang }
//             );

//             branch1.done(function (res) {

//                 if (res.items && res.items.length > 0) {

//                     let data = res.items[0];

//                     // set option ke select2

//                     let el = $('[name="cabang"]');

// // set value
//                     let option = new Option(data.nmbranch, data.idbranch, true, true);
//                     el.empty().append(option).trigger('change');

// // 1. cegah dropdown kebuka
//                     el.on('select2:opening select2:unselecting select2:clearing', function (e) {
//                         e.preventDefault();
//                     });

// // 2. disable tombol clear (kalau ada)
//                     el.data('select2').$container.find('.select2-selection__clear').remove();

// // 3. matikan interaksi UI (super lock)
//                     el.next('.select2-container').css({
//                         'pointer-events': 'none',
//                         'background': '#e9ecef'
//                     });

//                 }

//             }).always(function () {
//                 hideLoader();
//             });
        })
        .fail(function () {
            console.log("Failed To Loading Data");
            hideLoader();
        })
        .always(function () {
            hideLoader();
        });

    hideLoader();
}

$(document).on('click','.btn-edit',function(){

    let row = $(this).closest('tr');

    let input = row.find('.buildfor-input');

    // simpan value awal
    input.attr('data-old-value', input.val());

    $('.buildfor-input').prop('disabled', true);

    $('.btn-edit').removeClass('d-none');
    $('.btn-delete').removeClass('d-none');

    $('.btn-save-update').addClass('d-none');
    $('.btn-cancel-update').addClass('d-none');

    input.prop('disabled', false).focus();

    row.find('.btn-edit').addClass('d-none');
    row.find('.btn-delete').addClass('d-none');

    row.find('.btn-save-update').removeClass('d-none');
    row.find('.btn-cancel-update').removeClass('d-none');

    $('.wo-global-action').hide();
});

$(document).on('click','.btn-cancel-update',function(){

    let row = $(this).closest('tr');

    let input = row.find('.buildfor-input');

    let oldValue = input.attr('data-old-value');

    // kembalikan nilai semula
    input.val(oldValue);

    // disable lagi
    input.prop('disabled', true);

    // kembalikan tombol normal
    row.find('.btn-edit').removeClass('d-none');
    row.find('.btn-delete').removeClass('d-none');

    row.find('.btn-save-update').addClass('d-none');
    row.find('.btn-cancel-update').addClass('d-none');

    // tampilkan tombol lain
    $('.wo-global-action').show();

});

$(document).on('click','.btn-save-update',function(){

    let docno = $(this).data('id');

    let row = $(this).closest('tr');

    let buildfor = row.find('.buildfor-input').val();

    saveUpdateBOM(docno, buildfor);

});

function saveUpdateBOM(docno, buildfor){

    Swal.fire({
        title: 'Update Build For',
        html: `
            Yakin update Build For BOM
            <b>${docno}</b> ?
            <br><br>
            Qty Material, Cost dan WIP akan dihitung ulang.
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result)=>{

        if(!result.isConfirmed){
            return;
        }

        $.ajax({

            url: HOST_URL + 'production/trans/save_update_workingorder_bom',

            type:'POST',

            data:{
                docno:docno,
                buildfor:buildfor
            },

            dataType:'json',

            success:function(res){

                if(res.status){

                    Swal.fire({
                        icon:'success',
                        title:'Berhasil',
                        text:'Build For berhasil diperbarui',
                        timer:1500,
                        showConfirmButton:false
                    });

                    $('.wo-global-action').show();
                    let row = $('.btn-save-update[data-id="'+docno+'"]').closest('tr');

                    row.find('.buildfor-input').prop('disabled', true);

                    row.find('.btn-edit').removeClass('d-none');
                    row.find('.btn-delete').removeClass('d-none');

                    row.find('.btn-save-update').addClass('d-none');
                    row.find('.btn-cancel-update').addClass('d-none');

                    $('.wo-global-action').show();

                    reload_table_WOBOMMst();
                    reload_workingorder_material_dtl();
                    reload_workingorder_cost_dtl();
                    reload_workingorder_wip_dtl();

                }else{

                    Swal.fire({
                        icon:'error',
                        title:'Gagal',
                        text:res.message
                    });

                }

            }

        });

    });

}


$(document).on('click', '.btn-delete', function(){

    let id = $(this).data('id');

    btnDeleteDetail(id);

});



function btnDeleteDetail(ids){
    // const ids = getCheckedDetailIds();

    if(ids === null){
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih BOM yang akan dihapus'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Hapus BOM',
        html: `
            Yakin hapus BOM No. <b>${ids.trim()}</b>?<br><br>
            <span style="color:red;">
                Seluruh data Material, Cost, dan WIP yang terkait dengan BOM ini
                akan ikut terhapus dari Work Order.
            </span>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if(!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'production/trans/delete_workingorder_detail',
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
                    reload_table_WOBOMMst()
                    reload_workingorder_material_dtl();
                    reload_workingorder_cost_dtl();
                    reload_workingorder_wip_dtl();

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

// $('#btnInputDetail').on('click', function (e) {
//     btnInputDetailBOM();
// });
function btnInputDetailBOM() {

    // reset form
    $('#formBOMDetail')[0].reset();

    // readonly
    // $('[name="actualcost"]').prop("readonly", true);
    // $('[name="lastcost"]').prop("readonly", true);

    // reset select2
    $('#docnobom').val(null).trigger('change');

    // reset idurut
    $('#idurut').val('');

    // title modal
    $('#modalDetailBOMLabel').text('Tambah Detail BOM');

/*    // destroy jika sudah pernah init
    if ($('#idbarang').hasClass("select2-hidden-accessible")) {
        $('#idbarang').select2('destroy');
    }*/



    // show modal
    $('#modalDetailBOM').modal('show');
}




$('#btnAddDetailCost').on('click', function (e) {
    btnInputDetailCost();
});

function btnInputDetailCost() {

    // reset form
    $('#formWorkingOrderCostDtl')[0].reset();

    // readonly
    // $('[name="actualcost"]').prop("readonly", true);
    // $('[name="lastcost"]').prop("readonly", true);

    // reset select2
    $('#idbarangCost').val(null).trigger('change');

    // reset idurut
    $('#idurut').val('');

    // title modal
    $('#modalDtlBomCostTitle').text('Tambah Detail Cost');

/*    // destroy jika sudah pernah init
    if ($('#idbarang').hasClass("select2-hidden-accessible")) {
        $('#idbarang').select2('destroy');
    }*/



    // show modal
    $('#modalDtlBomCost').modal('show');
}




$('#btnAddDetailWip').on('click', function (e) {
    btnInputDetailWip();
});

function btnInputDetailWip() {

    // reset form
    $('#formWorkingOrderWipDtl')[0].reset();

    // readonly
    // $('[name="actualcost"]').prop("readonly", true);
    // $('[name="lastcost"]').prop("readonly", true);

    // reset select2
    $('#idbarangWip').val(null).trigger('change');

    // reset idurut
    $('#idurut').val('');

    // title modal
    $('#modalDtlBomWipTitle').text('Tambah Detail Wip');

/*    // destroy jika sudah pernah init
    if ($('#idbarang').hasClass("select2-hidden-accessible")) {
        $('#idbarang').select2('destroy');
    }*/



    // show modal
    $('#modalDtlBomWip').modal('show');
}


function setSelect2Ajax(selector, value, text) {
    if (!value) return;

    let option = new Option(text || value, value, true, true);
    $(selector).append(option).trigger('change');
}


function updateDetailBomMaterial(){
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
        url: HOST_URL + 'production/trans/get_workingorder_dtl',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                $('#idurutMaterial').val(res.items[0].idurut);
                $('#description_detail_material').val(res.items[0].description);
                $('#docno').val(res.items[0].docno);
                $('#idbarangMaterial').val(res.items[0].idbarang);
                $('#nmbarangmaterial').val(res.items[0].nmbarang);
                $('#unitMaterial').val(res.items[0].unit);
                // $('#qtymaterial').val(res.items[0].qty);
                setJtsValue('[name="qtymaterial"]', convertToDbNumber(res.items[0].qty));
                setJtsValue('[name="standartcostmaterial"]', convertToDbNumber(res.items[0].standartcost));
                setJtsValue('[name="totalcostmaterial"]', convertToDbNumber(res.items[0].totalcost));
                // $('#standartcostmaterial').val(res.items[0].standartcost);
                // $('#totalcostmaterial').val(res.items[0].totalcost);
                // setSelect2Ajax('#idbarangMaterial', res.items[0].idbarang, res.items[0].idbarang);

                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_item_std_cost' + '?var=' + res.items[0].idbarang.trim(),
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                    $('[name="idbarangMaterial"]').append(option).trigger('change')

                    // manually trigger the `select2:select` event
                    $('[name="idbarangMaterial"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });
                $('#modalDtlBomMaterialTitle').text('Update Detail Material');
                $('#modalDtlBomMaterial').modal('show');

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




function updateDetailBomCost(){
    const ids = getCheckedDetailIdsCost();
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
        url: HOST_URL + 'production/trans/get_workingorder_dtl',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                $('#idurutCost').val(res.items[0].idurut);
                $('#description_detail_cost').val(res.items[0].description);
                $('#docno').val(res.items[0].docno);
                $('#idbarangCost').val(res.items[0].idbarang);
                $('#nmbarangcost').val(res.items[0].nmbarang);
                $('#unitCost').val(res.items[0].unit);
                // $('#qtycost').val(res.items[0].qty);
                setJtsValue('[name="qtycost"]', convertToDbNumber(res.items[0].qty));
                setJtsValue('[name="standartcostcost"]', convertToDbNumber(res.items[0].standartcost));
                setJtsValue('[name="totalcostcost"]', convertToDbNumber(res.items[0].totalcost));
                // $('#standartcostcost').val(res.items[0].standartcost);
                // $('#totalcostcost').val(res.items[0].totalcost);
                // setSelect2Ajax('#idbarangCost', res.items[0].idbarang, res.items[0].idbarang);

                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_item_std_cost' + '?var=' + res.items[0].idbarang.trim(),
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                    $('[name="idbarangCost"]').append(option).trigger('change')

                    // manually trigger the `select2:select` event
                    $('[name="idbarangCost"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });
                $('#modalDtlBomCostTitle').text('Update Detail Cost');
                $('#modalDtlBomCost').modal('show');

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




function updateDetailBomWip(){
    const ids = getCheckedDetailIdsWip();
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
        url: HOST_URL + 'production/trans/get_workingorder_dtl',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                $('#idurutWip').val(res.items[0].idurut);
                $('#description_detail_wip').val(res.items[0].description);
                $('#docno').val(res.items[0].docno);
                $('#idbarangWip').val(res.items[0].idbarang);
                $('#nmbarangwip').val(res.items[0].nmbarang);
                $('#unitWip').val(res.items[0].unit);
                // $('#qtywip').val(res.items[0].qty);
                setJtsValue('[name="qtywip"]', convertToDbNumber(res.items[0].qty));
                setJtsValue('[name="standartcostwip"]', convertToDbNumber(res.items[0].standartcost));
                setJtsValue('[name="totalcostwip"]', convertToDbNumber(res.items[0].totalcost));
                // $('#standartcostwip').val(res.items[0].standartcost);
                // $('#totalcostwip').val(res.items[0].totalcost);
                // setSelect2Ajax('#idbarangWip', res.items[0].idbarang, res.items[0].idbarang);

                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_item_std_cost' + '?var=' + res.items[0].idbarang.trim(),
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    var option = new Option(datax.items[0].nmbarang, datax.items[0].idbarang, true, true);
                    $('[name="idbarangWip"]').append(option).trigger('change')

                    // manually trigger the `select2:select` event
                    $('[name="idbarangWip"]').trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });
                $('#modalDtlBomWipTitle').text('Update Detail Wip');
                $('#modalDtlBomWip').modal('show');

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


function btnDeleteDetailMaterial(){
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
            url: HOST_URL + 'production/trans/delete_workingorder_detail',
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
                    documentReadable()
                    $('#tmp_workingordermaterialdtl').DataTable().ajax.reload(null,false);

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



function btnDeleteDetailCost(){
    const ids = getCheckedDetailIdsCost();

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
            url: HOST_URL + 'production/trans/delete_workingorder_detail',
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
                    documentReadable()
                    $('#tmp_workingordercostdtl').DataTable().ajax.reload(null,false);

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





function btnDeleteDetailWip(){
    const ids = getCheckedDetailIdsWip();

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
            url: HOST_URL + 'production/trans/delete_workingorder_detail',
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
                    documentReadable()
                    $('#tmp_workingorderwipdtl').DataTable().ajax.reload(null,false);

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

var defaultInitialGroupBrng = '';
$("#idbarangMaterial").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    minimumInputLength: 2,
    dropdownParent: $('#modalDtlBomMaterial'),
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item_std_cost',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            var docdate = $('#docdate').val();
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialGroupBrng,
                _parameterx_: defaultInitialGroupBrng,
                loccode: '',
                docdate:  docdate,
                //loccode: $('[name="idlocation_dtl"]').val(),
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
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatItem, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {

    var data = e.params.data;

    $('[name="nmbarangmaterial"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unitMaterial"]').val(data.unit.trim()).prop("readonly", true);
    setJtsValue('[name="standartcostmaterial"]', convertToDbNumber(data.newcost));
    $('[name="totalcostmaterial"]').val().prop("readonly", true);

});



var defaultInitialGroupBrng = '';
$("#idbarangCost").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    minimumInputLength: 2,
    dropdownParent: $('#modalDtlBomCost'),
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item_std_cost',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            var docdate = $('#docdate').val();
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialGroupBrng,
                _parameterx_: defaultInitialGroupBrng,
                loccode: '',
                docdate:  docdate,
                //loccode: $('[name="idlocation_dtl"]').val(),
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
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatItem, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {

    var data = e.params.data;

    $('[name="nmbarangcost"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unitCost"]').val(data.unit.trim()).prop("readonly", true);
    setJtsValue('[name="standartcostcost"]', convertToDbNumber(data.newcost));
    $('[name="totalcostcost"]').val().prop("readonly", true);

});





var defaultInitialGroupBrng = '';
$("#idbarangWip").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    minimumInputLength: 2,
    dropdownParent: $('#modalDtlBomWip'),
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item_std_cost',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            var docdate = $('#docdate').val();
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialGroupBrng,
                _parameterx_: defaultInitialGroupBrng,
                loccode: '',
                docdate:  docdate,
                //loccode: $('[name="idlocation_dtl"]').val(),
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
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatItem, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {

    var data = e.params.data;

    $('[name="nmbarangwip"]').val(data.nmbarang.trim()).prop("readonly", true);
    $('[name="unitWip"]').val(data.unit.trim()).prop("readonly", true);
    setJtsValue('[name="standartcostwip"]', convertToDbNumber(data.newcost));
    $('[name="totalcostwip"]').val().prop("readonly", true);

});




$("#kdcustomer").select2({
    placeholder: "Ketik/Pilih Customer",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_customer',
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
    templateResult: formatCustomer, // omitted for brevity, see the source of this page
    templateSelection: formatCustomerSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    if (e.params && e.params.data) {
        var selectedData = e.params.data;
        
        $("#alamatcustomer").val(selectedData.alamat_kantor || '').prop('readonly', true);
        $("#nmcustomer").val(selectedData.nmcustomer || '')
        defaultInitialCustSO = selectedData.kdcustomer
        // $("#gradecustomer").val(selectedData.grade || '').prop('readonly', true);
        // $("#jthtempo").val(selectedData.jthtempo || '').prop('readonly', true);
        // $("#phone").val(selectedData.phone || '').prop('disabled', true);
    }
});


function formatCustomer(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdcustomer +"   <i class='fa fa-circle'></i>   "+ repo.nmcustomer + " </div>";
    return markup;
}
function formatCustomerSelection(repo) {
    return repo.nmcustomer || repo.text;
}




var defaultInitialBOM = '';
$("#docnobom").select2({
    placeholder: "Choose Your BOM",
    dropdownParent: $('#modalDetailBOM'),
    allowClear: true,
    width:'100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_bom',
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
                _paramglobal_: defaultInitialBOM,
                _parameterx_: defaultInitialBOM,
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
    templateResult: formatBOM, // omitted for brevity, see the source of this page
    templateSelection: formatBOMSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    // $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    // $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $("#batch").val(null).trigger('change');
});

/* Format Group */
function formatBOM(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.docno +"   <i class='fa fa-circle-o'></i>   "+ repo.keterangan +"</div>";
    return markup;
}
function formatBOMSelection(repo) {
    return repo.keterangan || repo.text;
}



var defaultIdbarangJadi = '';
$("#idbarang_jadi").select2({
    placeholder: "Choose Your Item List",
    allowClear: true,
    width: '100%',
    minimumInputLength: 2,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_item',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultIdbarangJadi,
                _parameterx_: defaultIdbarangJadi,
                loccode: '',
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
    escapeMarkup: function (markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatItem, // omitted for brevity, see the source of this page
    templateSelection: formatItemSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    // $('[name="nmbarangmaterial"]').val(data.nmbarang.trim()).prop("readonly", true);

});




var defaultInitialUnit = 'UNIT';
$("#buildunit").select2({
    placeholder: "Type/Chose Your Unit",
    allowClear: true,
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

});


function hitungTotalCostMaterial() {

    let qty = $('#qtymaterial').val().replace(/,/g, '');
    let cost = $('#standartcostmaterial').val().replace(/,/g, '');

    qty = parseFloat(qty) || 0;
    cost = parseFloat(cost) || 0;

    let total = qty * cost;

    $('#totalcostmaterial').val(
        total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
    );
}


function hitungTotalCostCost() {

    let qty = $('#qtycost').val().replace(/,/g, '');
    let cost = $('#standartcostcost').val().replace(/,/g, '');

    qty = parseFloat(qty) || 0;
    cost = parseFloat(cost) || 0;

    let total = qty * cost;

    $('#totalcostcost').val(
        total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
    );
}




function hitungTotalCostWip() {

    let qty = $('#qtywip').val().replace(/,/g, '');
    let cost = $('#standartcostwip').val().replace(/,/g, '');

    qty = parseFloat(qty) || 0;
    cost = parseFloat(cost) || 0;

    let total = qty * cost;

    $('#totalcostwip').val(
        total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })
    );
}


/* Format formatUnit */
function formatUnit(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idunit +"</div>";
    return markup;
}

function formatUnitSelection(repo) {
    return repo.idunit || repo.text;
}


function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}


$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});


function formatItem(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + repo.idbarang + "   <i class='fa fa-circle-o'></i>   " + repo.nmbarang + "</div>";
    return markup;
}

function formatItemSelection(repo) {
    return repo.nmbarang || repo.text;
}



function saveBOMDetail(formId) {


        // ===============================
        // Ambil dan validasi qty
        // ===============================
        // let buildfor = $('#buildfor').val();
        // let minimumqty = $('#minimumqty').val();

        // ===============================
        // Siapkan FormData
        // ===============================

        let formData = new FormData(document.getElementById(formId));

        //HEADER
        formData.append('docref', $('#docref').val());
        formData.append('cabang', $('#cabang').val());
        formData.append('pemohon', $('#pemohon').val());
        formData.append('docno', $('#docno').val());
        formData.append('kdcustomer', $('#kdcustomer').val());
        formData.append('nmcustomer', $('#nmcustomer').val());
        formData.append('alamatcustomer', $('#alamatcustomer').val());
        formData.append('docdate', $('#docdate').val());
        formData.append('docdatefinish', $('#docdatefinish').val());
        
        formData.append('noso', $('#noso').val());
        formData.append('pemohon', $('#pemohon').val());
        // formData.append('buildunit', $('#buildunit').val());
        // formData.append('buildfor', convertToDbNumber(buildfor));
        // formData.append('minimumqty', $('#minimumqty').val());
        formData.append('keterangan', $('#keterangan').val());
        //DETAIL
        // formData.append('idbarangMaterial', $('#idbarangMaterial').val());
        // formData.append('nmbarangmaterial', $('#nmbarangmaterial').val());
        // formData.append('unitMaterial', $('#unitMaterial').val());
        // formData.set('qtymaterial', convertToDbNumber(qtymaterial));
        // formData.set('standartcostmaterial', convertToDbNumber(standartcostmaterial));
        // formData.set('totalcostmaterial', convertToDbNumber(totalcostmaterial));
        // formData.append('description_detail_material', $('#description_detail_material').val());

        // if(detailType === 'MATERIAL'){
        //     let qtymaterial = $('#qtymaterial').val();
        //     let standartcostmaterial = $('#standartcostmaterial').val();
        //     let totalcostmaterial = $('#totalcostmaterial').val();
        //     formData.set('qtymaterial',convertToDbNumber($('#qtymaterial').val()));
        //     formData.set('standartcostmaterial',convertToDbNumber($('#standartcostmaterial').val()));
        //     formData.set('totalcostmaterial',convertToDbNumber($('#totalcostmaterial').val()));

        // }else if(detailType === 'COST'){
        //     let qtycost = $('#qtycost').val();
        //     let standartcostcost = $('#standartcostcost').val();
        //     let totalcostcost = $('#totalcostcost').val();
        //     formData.set('qtycost',convertToDbNumber($('#qtycost').val()));
        //     formData.set('standartcostcost',convertToDbNumber($('#standartcostcost').val()));
        //     formData.set('totalcostcost',convertToDbNumber($('#totalcostcost').val()));
        // }else if(detailType === 'WIP'){
        //     let qtywip = $('#qtywip').val();
        //     let standartcostwip = $('#standartcostwip').val();
        //     let totalcostwip = $('#totalcostwip').val();
        //     formData.set('qtywip',convertToDbNumber($('#qtywip').val()));
        //     formData.set('standartcostwip',convertToDbNumber($('#standartcostwip').val()));
        //     formData.set('totalcostwip',convertToDbNumber($('#totalcostwip').val()));
        // }
        // formData.append('doctype_detail', detailType);
        // Gabungkan docno
        formData.set(
            'docno',
            $('#prefix').val() + '/' +
            $('#infix').val() + '/' +
            $('#sufix').val()
        );

        // ===============================
        // AJAX SAVE
        // ===============================

        $.ajax({
            url: HOST_URL + 'production/trans/save_workingorder_mst',
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
                        text: res.message || 'Data Saved Success fully',
                        timer: 800,
                        showConfirmButton: false
                    });

                    if (res.reload === true) {
                        window.location.reload();
                        return;
                    }
                    
                    
                    reload_table_WOBOMMst()
                    documentReadable()
                    $('#modalDetailBOM').modal('hide');
                    reload_workingorder_material_dtl();
                    reload_workingorder_cost_dtl();
                    reload_workingorder_wip_dtl();
                    $('#formBOMDetail')[0].reset();

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
                    title: 'Server Error',
                    text: 'Terjadi kesalahan server'
                });
            }
        });


}



function tableWOBOMMst(){
        /* Tabel PO Detail */
    var initTable = function () {
        var table = $('#tmp_workingorderbommst');
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
                "url": HOST_URL + 'production/trans/list_tmp_workingorder_bom_mst',
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
        });
    }

    return initTable();

}


function reload_table_WOBOMMst()
{
    var table = $('#tmp_workingorderbommst');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// $(document).on('click','.btn-edit',function(){

//     let id = $(this).data('id');

//     btnUpdateDetail(id);

// });


/* TABLE PP DETAIL */
function tabletmpWorkingOrderMaterial() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#tmp_workingordermaterialdtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "language": languageDatatable(),
            "paging": false,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter": true,
            "iDisplayLength": -1,
            "ajax": {
                "url": HOST_URL + 'production/trans/list_tmp_workingorder_material_dtl',
                "type": "POST",
                "data": function (data) {
                    //data.searchfilter = $('#searchitem').val()+'';
                    //data.idbarang = $('#idbarang').val()+'';
                    //data.idposition = $('#idposition').val()+'';
                },
                "dataFilter": function (data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json); // return JSON string
                }
            },
            //Set column definition initialisation properties.
            
            // Di dalam konfigurasi DataTable Anda:
            "drawCallback": function(settings) {
                // Panggil fungsi manual tadi
                // updateGrandTotal();
            }
        });
    }

    return initTable();

}


function reload_workingorder_material_dtl() {
    var table = $('#tmp_workingordermaterialdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#tmp_workingordermaterialdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tmp_workingordermaterialdtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tmp_workingordermaterialdtl tbody').on('change', '.row-check', function () {
    const total = $('#tmp_workingordermaterialdtl tbody .row-check').length;
    const checked = $('#tmp_workingordermaterialdtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tmp_workingordermaterialdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});



$('#checkAll').on('change', function () {
    $('.row-check').prop('checked', this.checked);
});

// auto uncheck checkAll jika salah satu dilepas
$(document).on('change', '.row-check', function () {
    if (!this.checked) {
        $('#checkAll').prop('checked', false);
    }
});



/* TABLE PP DETAIL */
function tabletmpWorkingOrderCost() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#tmp_workingordercostdtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "language": languageDatatable(),
            "paging": false,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter": true,
            "iDisplayLength": -1,
            "ajax": {
                "url": HOST_URL + 'production/trans/list_tmp_workingorder_cost_dtl',
                "type": "POST",
                "data": function (data) {
                    //data.searchfilter = $('#searchitem').val()+'';
                    //data.idbarang = $('#idbarang').val()+'';
                    //data.idposition = $('#idposition').val()+'';
                },
                "dataFilter": function (data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json); // return JSON string
                }
            },
            //Set column definition initialisation properties.
            
            // Di dalam konfigurasi DataTable Anda:
            "drawCallback": function(settings) {
                // Panggil fungsi manual tadi
                // updateGrandTotal();
            }
        });
    }

    return initTable();

}


function reload_workingorder_cost_dtl() {
    var table = $('#tmp_workingordercostdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#tmp_workingordercostdtl thead').on('change', '#checkAllCost', function () {
    const checked = this.checked;

    $('#tmp_workingordercostdtl tbody .row-check-cost').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tmp_workingordercostdtl tbody').on('change', '.row-check-cost', function () {
    const total = $('#tmp_workingordercostdtl tbody .row-check-cost').length;
    const checked = $('#tmp_workingordercostdtl tbody .row-check-cost:checked').length;

    $('#checkAllCost').prop('checked', total === checked);
});

$('#tmp_workingordercostdtl').on('draw.dt', function () {
    $('#checkAllCost').prop('checked', false);
});




$('#checkAllCost').on('change', function () {
    $('.row-check-cost').prop('checked', this.checked);
});

// auto uncheck checkAll jika salah satu dilepas
$(document).on('change', '.row-check-cost', function () {
    if (!this.checked) {
        $('#checkAllCost').prop('checked', false);
    }
});






/* TABLE PP DETAIL */
function tabletmpWorkingOrderWip() {
    /* Tabel PP Detail */
    var initTable = function () {
        var table = $('#tmp_workingorderwipdtl');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "language": languageDatatable(),
            "paging": false,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter": true,
            "iDisplayLength": -1,
            "ajax": {
                "url": HOST_URL + 'production/trans/list_tmp_workingorder_wip_dtl',
                "type": "POST",
                "data": function (data) {
                    //data.searchfilter = $('#searchitem').val()+'';
                    //data.idbarang = $('#idbarang').val()+'';
                    //data.idposition = $('#idposition').val()+'';
                },
                "dataFilter": function (data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json); // return JSON string
                }
            },
            //Set column definition initialisation properties.
            
            // Di dalam konfigurasi DataTable Anda:
            "drawCallback": function(settings) {
                // Panggil fungsi manual tadi
                // updateGrandTotal();
            }
        });
    }

    return initTable();

}


function reload_workingorder_wip_dtl() {
    var table = $('#tmp_workingorderwipdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}


// CHECK ALL
$('#tmp_workingorderwipdtl thead').on('change', '#checkAllWip', function () {
    const checked = this.checked;

    $('#tmp_workingorderwipdtl tbody .row-check-wip').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tmp_workingorderwipdtl tbody').on('change', '.row-check-wip', function () {
    const total = $('#tmp_workingorderwipdtl tbody .row-check-wip').length;
    const checked = $('#tmp_workingorderwipdtl tbody .row-check-wip:checked').length;

    $('#checkAllWip').prop('checked', total === checked);
});

$('#tmp_workingorderwipdtl').on('draw.dt', function () {
    $('#checkAllWip').prop('checked', false);
});




$('#checkAllWip').on('change', function () {
    $('.row-check-wip').prop('checked', this.checked);
});

// auto uncheck checkAll jika salah satu dilepas
$(document).on('change', '.row-check-wip', function () {
    if (!this.checked) {
        $('#checkAllWip').prop('checked', false);
    }
});


function getCheckedDetailIds() {
    let ids = [];
    $('.row-check:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}



function getCheckedDetailIdsCost() {
    let ids = [];
    $('.row-check-cost:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}



function getCheckedDetailIdsWip() {
    let ids = [];
    $('.row-check-wip:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}


document.addEventListener('keydown', function(e) {

// CTRL + Q
if (e.ctrlKey && e.key.toLowerCase() === 'q') {

e.preventDefault();

document.getElementById('btnAddDetailMaterial').click();
}

});

$(document).ready(function () {

    table_workingorder();
    documentReadable();
    tableWOBOMMst();
    tabletmpWorkingOrderMaterial();
    tabletmpWorkingOrderCost();
    tabletmpWorkingOrderWip();

});