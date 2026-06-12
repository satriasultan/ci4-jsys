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

function tableJUPTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablejupTrx');
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
                "url": HOST_URL + 'ka/accounting/list_jup',
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

function reload_tableJUPTrx()
{
    var table = $('#tablejupTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


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
        url: HOST_URL + 'ka/accounting/showing_juptemp',
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
            defaultInitialPP = prefixParts[2].substring(0, 2);


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
            $('[name="docdate"]').val(json.dataTables.items[0].docdate);
            // $('[name="senddate"]').val(json.dataTables.items[0].senddate);
            // setJtsValue('[name="jthtempo"]', convertToDbNumber(json.dataTables.items[0].jthtempo));
            // setJtsValue('[name="kurs"]', convertToDbNumber(json.dataTables.items[0].kurs));
            // $('[name="isinclusive"]').prop(
            //     'checked',
            //     $.trim((json.dataTables.items[0].isinclusive || '')).toUpperCase() === 'YES'
            // );
            // // $('[name="jthtempo"]').val(json.dataTables.items[0].jthtempo);
            // $('[name="alamatsupplier"]').val(json.dataTables.items[0].alamatsupplier);
            // $('[name="alamatkirim"]').val(json.dataTables.items[0].alamatkirim);
            // $('[name="isinclusive"]').val(json.dataTables.items[0].isinclusive);
            $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);
            // $('[name="syarat"]').val(json.dataTables.items[0].syarat);

            // var docnoUMB = (json.dataTables.items[0].docnoumb || '').trim();
            // if(docnoUMB === ''){
            //     $('#btnDP').prop('disabled', false);
            //     $('#btnDPWrapper').attr('title','Create Down Payment');
            // }else{
            //     $('#btnDP').prop('disabled', true);
            //     $('#btnDPWrapper').attr('title','PO sudah ada Down Payment');
            // }

            setJtsValue('[name="balance"]', convertToDbNumber(json.dataTables.items[0].balance));
            let balanceValue = ($('#balance').val()) || 0;
                
            if (balanceValue != 0) {
                // Disable tombol submit atau form
                $('button[type="submit"]').prop('disabled', true);
                // Atau disable seluruh input di form
                // $('#formJUPDetail :input').prop('disabled', true);
                
                // Tampilkan notifikasi bahwa form sudah tidak bisa digunakan
                Swal.fire({
                    icon: 'info',
                    title: 'Form Terkunci',
                    text: `Form telah dikunci karena balance bernilai ${balanceValue}. Tidak dapat menyimpan data baru.`,
                    timer: 3000,
                    showConfirmButton: true
                });
            } else {
                $('button[type="submit"]').prop('disabled', false);
            }
            // setJtsValue('[name="jumlahpajak"]', convertToDbNumber(json.dataTables.items[0].jumlahpajak));
            // setJtsValue('[name="total"]', convertToDbNumber(json.dataTables.items[0].total));
            // $('[name="estpakai"]').val(json.dataTables.items[0].estpakai);
            

            // $('[name="keterangan"]').val(json.dataTables.items[0].keterangan);
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

// var defaultInitialGroupBrng = '';
// $("#idbarang").select2({
//     placeholder: "Choose Your Item List",
//     allowClear: true,
//     width:'100%',
//     ajax: {
//         url: HOST_URL + 'api/globalmodule/list_item',
//         type: 'POST',
//         dataType: 'json',
//         delay: 250,
//         data: function(params) {
//             return {
//                 _search_: params.term, // search term
//                 _page_: params.page,
//                 _draw_: true,
//                 _start_: 1,
//                 _perpage_: 2,
//                 _paramglobal_: defaultInitialGroupBrng,
//                 _parameterx_: defaultInitialGroupBrng,
//                 term: params.term,
//             };
//         },
//         processResults: function (data, params) {
//             // var searchTerm = $("#idbarang").data("select2").$dropdown.find("input").val();
//             // if (data.items.length === 1 && data.items[0].text === searchTerm) {
//             //     var option = new Option(data.items[0].nmbarang, data.items[0].idbarang, true, true);
//             //     $('#idbarang').append(option).trigger('change').select2("close");
//             //     // manually trigger the `select2:select` event
//             //     $('#idbarang').trigger({
//             //         type: 'select2:select',
//             //         params: {
//             //             data: data
//             //         }
//             //     });
//             // }
//             params.page = params.page || 1;
//             return {
//                 results: data.items,
//                 pagination: {
//                     more: (params.page * 30) < data.total_count
//                 }
//             };
//         },

//         cache: false
//     },
//     escapeMarkup: function(markup) {
//         return markup;
//     }, // let our custom formatter work
//     // minimumInputLength: 1,
//     templateResult: formatItem, // omitted for brevity, see the source of this page
//     templateSelection: formatItemSelection // omitted for brevity, see the source of this page
// }).on("select2:select", function (e) {
//     var data = e.params.data;
//     $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
//     $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
//     $("#batch").val(null).trigger('change');
// });


var defaultInitialCOA = '';
$("#idcoa").select2({
    placeholder: "Choose Your COA",
    allowClear: true,
    dropdownParent: $('#modalDetailJUP'),
    width:'100%',
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
                _paramglobal_: defaultInitialCOA,
                _parameterx_: defaultInitialCOA,
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
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    var data = e.params.data;
    $('[name="nmcoa"]').val(data.nmcoa.trim()).prop("readonly", true);
    // $('[name="nmbarang"]').val(data.nmbarang.trim()).prop("readonly", true);
    // $('[name="unit"]').val(data.unit.trim()).prop("readonly", true);
    // $("#batch").val(null).trigger('change');
});


function formatCOA(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idcoa +"   <i class='fa fa-circle'></i>   "+ repo.nmcoa +" <i class='fa fa-circle'></i> LEVEL "+ repo.level + " </div>";
    return markup;
}
function formatCOASelection(repo) {
    return repo.nmcoa || repo.text;
}





function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}




$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});





/* TABLE PO DETAIL */
function tableJUPDetail(){
        /* Tabel PO Detail */
    var initTable = function () {
        var table = $('#tabjupdtl');
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
                "url": HOST_URL + 'ka/accounting/list_tmp_jup_dtl',
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
    }

    return initTable();

}


function reload_table_jup_dtl()
{
    var table = $('#tabjupdtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}



// CHECK ALL
$('#tabjupdtl thead').on('change', '#checkAll', function () {
    const checked = this.checked;

    $('#tabjupdtl tbody .row-check').prop('checked', checked);
});

// JIKA SALAH SATU ROW UNCHECK → CHECKALL MATI
$('#tabjupdtl tbody').on('change', '.row-check', function () {
    const total = $('#tabjupdtl tbody .row-check').length;
    const checked = $('#tabjupdtl tbody .row-check:checked').length;

    $('#checkAll').prop('checked', total === checked);
});

$('#tabjupdtl').on('draw.dt', function () {
    $('#checkAll').prop('checked', false);
});
function getSelectedPODetail(){
    return $('#tabjupdtl tbody .row-check:checked')
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

let currentEditId = null;
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
        url: HOST_URL + 'ka/accounting/get_jup_detail',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(res){
            if(res.status){

                $('#idurut').val(res.data.idurut);
                $('#uniqueid').val(res.data.uniqueid);
                $('#remarks').val(res.data.remarks);
                $('#dk').val(res.data.dk.trim());
                $('#cabangdtl').val(res.data.cabang);
                // $('#docnoppmodal').val(res.data.docnopp);
                $('#idcoa').val(res.data.idcoa);
                $('#nmcoa').val(res.data.nmcoa);
                // setJtsValue('[name="multidisc"]', convertToDbNumber(res.data.multidisc));
                setJtsValue('[name="nilai"]', convertToDbNumber(res.data.nilai));
                setSelect2Ajax('#idcoa', res.data.idcoa, res.data.idcoa);



                currentEditId = res.data.idurut;
                // $('#qty').val(res.data.qty);
                // $('#qtybonus').val(res.data.qtybonus);
                // $('#harga').val(res.data.harga);
                // $('#multidisc').val(res.data.multidisc);
                // setSelect2Ajax('#idcoa', res.data.idbarang, res.data.idbarang);
                // setSelect2Ajax('#docnopp', res.data.docnopp, res.data.keterangan);

                $('#modalDetailJUPLabel').text('Update JUP Detail');
                $('#modalDetailJUP').modal('show');

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

// Reset currentEditId ketika modal ditutup
$('#modalUpdateJUP').on('hidden.bs.modal', function () {
    currentEditId = null;
});

// $(document).on('input', '.form-control', function () {
//     // Jika sedang dalam mode edit, gunakan currentEditId
//     if (currentEditId) {
//         // Baca nilai qty, harga, dan multidisc
//         let qty = parseFloat($('#qty').val().replace(/,/g, '')) || 0;
//         let harga = parseFloat($('#harga').val().replace(/,/g, '')) || 0;
//         // let multidisc = parseFloat($('#multidisc').val().replace(/,/g, '')) || 0;
        
//         // Hitung nilai awal (qty * harga)
//         let nilaiAwal = qty * harga;
        
//         // Hitung diskon
//         // let diskon = (nilaiAwal * multidisc) / 100;
        
//         // Hitung nilai akhir setelah diskon
//         let nilaiAkhir = nilaiAwal;
        
//         // Format ke en-US: separator ribuan = koma, desimal = titik
//         $('#nilai').val(nilaiAkhir.toLocaleString('en-US', {
//             minimumFractionDigits: 2, 
//             maximumFractionDigits: 2
//         }));
//     }
// });

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
            url: HOST_URL + 'ka/accounting/delete_jup_detail',
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

                    $('#tabjupdtl').DataTable().ajax.reload(null,false);

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

$('#formPOMasters').bootstrapValidator({
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
$('#formPOdetail').bootstrapValidator({
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




function saveJUPDetail() {

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Simpan data JUP Detail?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if (!result.isConfirmed) return;

        let formData = new FormData(document.getElementById('formJUPDetail'));
        formData.append('docdate', $('#docdate').val());
        formData.append('cabang', $('#cabang').val());
        // formData.append('senddate', $('#senddate').val());
        // formData.append('jthtempo', convertToDbNumber($('#jthtempo').val()));
        // formData.append('kdsupplier', $('#kdsupplier').val());
        // formData.append('isinclusive', $('#isinclusive').is(':checked') ? 'YES' : 'NO');
        // formData.append('alamatsupplier', $('#alamatsupplier').val());
        // formData.append('idtax', $('#idtax').val());
        // formData.append('currcode', $('#currcode').val());
        // formData.append('kurs', convertToDbNumber($('#kurs').val()));
        // formData.append('alamatkirim', $('#alamatkirim').val());
        formData.append('keterangan', $('#keterangan').val());
        // formData.append('estpakai', $('#estpakai').val());

        // docno gabungan (lebih aman pakai hidden header)
        formData.set('docno', $('#prefix').val() + '/' + $('#infix').val() + '/' + $('#sufix').val());
        // convert qty ke numeric DB
        
        let nilai = $('#nilai').val();
        // formData.set('multidisc', convertToDbNumber(multidisc));
        formData.set('nilai', convertToDbNumber(nilai));
        // formData.set('nilai', convertToDbNumber(nilai));
        // formData.set('remarks', $('#remarks').val());
        // formData.set('uniqueid', $('#uniqueid').val());
        // formData.set('docnopp', $('#docnopp').val());
        
        // formData.set('descriptionpo', convertToDbNumber(qty));

        $.ajax({
            url: HOST_URL + 'ka/accounting/saveJUPDetail',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,

            success: function (res) {

                if (!res.success) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: res.message
                    });

                    return;
                }

                // ==============================
                // SUCCESS
                // ==============================

                // Jika tidak ada item baru (semua sudah ada)
                let iconType = 'success';
                let titleText = 'Berhasil';

                if (res.message && res.message.toLowerCase().includes('sudah ada')) {
                    iconType = 'info';
                    titleText = 'Tidak Ada Perubahan';
                }

                Swal.fire({
                    icon: iconType,
                    title: titleText,
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Jika header baru dibuat → reload
                if (res.reload === true) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                    return;
                }

                // Jika hanya tambah detail
                $('#modalDetailJUP').modal('hide');
                // $('#modalUpdateJUP').modal('hide');
                // $('#formPOUpdate')[0].reset();
                reload_table_jup_dtl();
                documentReadable()
                $('#formJUPDetail')[0].reset();
                // let balanceValue = ($('#balance').val()) || 0;
                
                // if (balanceValue != 0) {
                //     // Disable tombol submit atau form
                //     $('button[type="submit"]').prop('disabled', true);
                //     // Atau disable seluruh input di form
                //     // $('#formJUPDetail :input').prop('disabled', true);
                    
                //     // Tampilkan notifikasi bahwa form sudah tidak bisa digunakan
                //     Swal.fire({
                //         icon: 'info',
                //         title: 'Form Terkunci',
                //         text: `Form telah dikunci karena balance bernilai ${balanceValue}. Tidak dapat menyimpan data baru.`,
                //         timer: 3000,
                //         showConfirmButton: true
                //     });
                // } else {
                //     $('button[type="submit"]').prop('disabled', false);
                // }
            },

            error: function (xhr) {

                console.error(xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan pada server (500)'
                });
            }
        });


    });
}

function btnInputDetail() {

    // let cabang = $('#cabang').val();

    // if (!cabang || cabang.trim() === '') {
    //     alert('Cabang harus dipilih terlebih dahulu');
    //     $('#cabang').focus();
    //     return; // stop proses
    // }
    $('#formJUPDetail')[0].reset();

    
    // 🔹 Clear select2
    $('#idcoa').val(null).trigger('change');

    // Jika ada select2 lain, lakukan hal sama
    // $('#selectlain').val(null).trigger('change');

    $('#idurut').val(''); // pastikan id kosong (mode insert)

    $('#modalDetailJUPLabel').text('Tambah Item Detail');
    $('#modalDetailJUP').modal('show');
}




function formatSupplier(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdsupplier +"   <i class='fa fa-circle'></i>   "+ repo.nmsupplier + " </div>";
    return markup;
}
function formatSupplierSelection(repo) {
    return repo.nmsupplier || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#kdsupplier").select2({
    placeholder: "Ketik/Pilih Supplier",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_supplier_new',
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
    templateResult: formatSupplier, // omitted for brevity, see the source of this page
    templateSelection: formatSupplierSelection // omitted for brevity, see the source of this page
}).on("select2:select", function (e) {
    if (e.params && e.params.data) {
        var selectedData = e.params.data;
        
        $("#alamatsupplier").val(selectedData.alamat || '').prop('disabled', true);
        $("#jthtempo").val(selectedData.jthtempo || '')
        // $("#phone").val(selectedData.phone || '').prop('disabled', true);
    }
});


let currentKodeSuffix = '';
let skipRoleChange = false;

$('#cabang').on('change', function () {
    if (skipRoleChange) return; // skip

    let idbranch = $(this).val();

    if(idbranch){
        $.ajax({
                url: HOST_URL + '/ka/accounting/getBranchInfoJUP',
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
                    $('#prefix').val('JUP');             // default
                    $('#sufix').val(currentKodeSuffix + '0001');
                    defaultInitialPP = currentKodeSuffix
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
                                locale: { format: 'YYYY-MM-DD' },
                                cancelLabel: 'Clear'
                            });
                            // rebind handlers jika perlu (apply/cancel)
                            $el.on('apply.daterangepicker', function(ev, picker) {
                                $(this).val(picker.startDate.format('YYYY-MM-DD'));
                            });
                            $el.on('cancel.daterangepicker', function(ev, picker) {
                                $(this).val('');
                            });
                        }

                        // isi input langsung (opsional)
                        $el.val(today.format('YYYY-MM-DD'));
                    }

                    $('#docno').val(
                        'JUP/' + res.infix + '/' + currentKodeSuffix + '0001'
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
        url: HOST_URL + '/ka/accounting/getNextSuffixJUP',
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



$(document).ready(function() {
    // Handle form submission event
    // Handle form submission event





    tableJUPTrx();
    // tablePOApprvTrx();
    tableJUPDetail();
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