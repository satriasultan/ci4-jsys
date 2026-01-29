

function lockJobForm() {
    $('#formJob input, #formJob select, #formJob textarea').prop('readonly', true).prop('disabled', true);
    $('[name="idbranch"], [name="induk"], [name="level"], [name="alamatcust"]').prop('readonly', true);
}

function unlockJobForm() {
    $('#formJob input, #formJob select, #formJob textarea').prop('readonly', false).prop('disabled', false);
    $('[name="idbranch"], [name="induk"], [name="level"], [name="alamatcust"]').prop('readonly', true);
}

let previousFormData = {};


// function addChild() {
//     let parentId = $('[name="idbranch"]').val();
//     let parentText = $('[name="nmbranch"]').val();
//     let parentLevel = parseInt($('[name="level"]').val().trim());

//     if (!parentId) {
//         Swal.fire('Pilih Job', 'Silakan pilih parent Job terlebih dahulu', 'warning');
//         return;
//     }

//     previousFormData = {
//         idbranch: $('[name="idbranch"]').val(),
//         nmbranch: $('[name="nmbranch"]').val(),
//         nm2branch: $('[name="nm2branch"]').val(),
//         groupbranch: $('[name="groupbranch"]').val(),
//         idcustomer: $('[name="idcustomer"]').val(),
//         jenis: $('[name="jenis"]').val(),
//         induk: $('[name="induk"]').val(),
//         level: $('[name="level"]').val()
//     };


//     // Tampilkan konfirmasi
//     Swal.fire({
//         title: 'Tambah Child Job',
//         html: `Apakah Anda akan menambah child dari <b>${parentText}</b> (ID: ${parentId})?`,
//         icon: 'question',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Ya, Tambah Child',
//         cancelButtonText: 'Batal',
//         reverseButtons: true
//     }).then((result) => {
//         if (result.isConfirmed) {
//             // Reset form
//             $('#formJob')[0].reset();
            
//             // Sembunyikan tombol yang tidak diperlukan
//             $('#btnEditChild').hide();
//             $('#btnDeleteJob').hide();
            
//             // Ubah tombol Add Child menjadi Cancel
//             $('#btnAddChild')
//                 .html('<i class="fa fa-times"></i> Cancel')
//                 .removeClass('btn-success')
//                 .addClass('btn-secondary')
//                 .off('click') // Hapus event listener sebelumnya
//                 .on('click', cancelAddChild); // Tambah event untuk cancel
            
//             // Set nilai induk dan level
//             $('[name="induk"]').val(parentId);
//             $('[name="level"]').val(parentLevel + 1);
            
//             $('[name="idbranch"]').val(''); // Kosongkan ID Job baru
//             $('[name="nmbranch"]').val(''); // Kosongkan nama Job
//             $('[name="nm2branch"]').val(''); // Kosongkan nama Job 2
//             // checkbox
//             $('[name="isdetail"]').prop('checked', false);

//             // radio group
//             $('[name="groupbranch"]').prop('checked', false);

//             // select2
//             $('#idcustomer').val(null).trigger('change');
//             $('[name="jenis"]').val(''); // Kosongkan jenis
//             \
//             // Set tipe form
//             $('#formJob').data('type', 'INPUT');
            
//             // Buka form dan tampilkan submit
//             unlockJobForm();
//             $('[name="idbranch"]').prop('readonly', false).prop('disabled', false);
//             $('#submitJob').show();
            
//             // Fokus ke field pertama
//             $('[name="nmbranch"]').focus();
            
//             // Tampilkan notifikasi sukses
//             Swal.fire({
//                 title: 'Siap!',
//                 text: 'Silakan isi data child Job baru',
//                 icon: 'info',
//                 timer: 2000,
//                 showConfirmButton: false
//             });
//         }
//     });
// }

// // Fungsi untuk cancel add child
// function cancelAddChild() {
//     Swal.fire({
//         title: 'Batalkan Tambah Child?',
//         text: 'Data yang sudah diisi akan hilang',
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#d33',
//         cancelButtonColor: '#3085d6',
//         confirmButtonText: 'Ya, Batalkan',
//         cancelButtonText: 'Lanjutkan Tambah'
//     }).then((result) => {
//         if (result.isConfirmed) {

//             if (previousFormData) {
//                 $('[name="idbranch"]').val(previousFormData.idbranch || '');
//                 $('[name="nmbranch"]').val(previousFormData.nmbranch || '');
//                 $('[name="nm2branch"]').val(previousFormData.nm2branch || '');
//                 $('[name="groupbranch"]').val(previousFormData.groupbranch || '');
//                 $('[name="idcustomer"]').val(previousFormData.idcustomer || '');
//                 $('[name="jenis"]').val(previousFormData.jenis || '');
//                 $('[name="induk"]').val(previousFormData.induk || '');
//                 $('[name="level"]').val(previousFormData.level || '');
//             }
//             // Reset tombol Add Child ke kondisi semula
//             $('#btnAddChild')
//                 .html('<i class="fa fa-plus"></i> Add Child')
//                 .removeClass('btn-secondary')
//                 .addClass('btn-success')
//                 .off('click')
//                 .on('click', addChild);
            
//             // Reset form ke mode DETAIL
//             $('#formJob').data('type', 'DETAIL');
//             $('#submitJob').hide();
//             lockJobForm();

//             previousFormData = {};
            
//             // Tampilkan tombol lainnya
//             $('#btnEditChild').show();
//             $('#btnDeleteJob').show();
//         }
//     });
// }

function editChild() {
    let branchId = $('[name="idbranch"]').val();
    let branchName = $('[name="nmbranch"]').val();

    if (!branchId) {
        Swal.fire('Error', 'Tidak ada Job dipilih', 'error');
        return;
    }

    // Tampilkan konfirmasi
    Swal.fire({
        title: 'Edit Job',
        html: `Apakah Anda akan mengedit Job <b>${branchName}</b> (ID: ${branchId})?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Edit Job',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Sembunyikan tombol yang tidak diperlukan
            // $('#btnAddChild').hide();
            // $('#btnDeleteJob').hide();
            
            // Ubah tombol Edit menjadi Cancel
            $('#btnEditChild')
                .html('<i class="fa fa-times"></i> Cancel')
                .removeClass('btn-warning')
                .addClass('btn-secondary')
                .off('click') // Hapus event listener sebelumnya
                .on('click', cancelEditChild); // Tambah event untuk cancel
            
            // Set tipe form
            $('#formJob').data('type', 'EDIT');
            
            // Buka form dan tampilkan submit
            unlockJobForm();
            $('#submitJob').show();
            
            // Fokus ke field pertama
            $('[name="nmbranch"]').focus();
            
            // Tampilkan notifikasi
            // Swal.fire({
            //     title: 'Edit Mode',
            //     text: 'Silakan edit data Job',
            //     icon: 'info',
            //     timer: 2000,
            //     showConfirmButton: false
            // });
        }
    });
}

// Fungsi untuk cancel edit
function cancelEditChild() {
    Swal.fire({
        title: 'Batalkan Edit?',
        text: 'Perubahan yang belum disimpan akan hilang',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Lanjutkan Edit'
    }).then((result) => {
        if (result.isConfirmed) {
            // Reload data dari server untuk mengembalikan nilai asli
            let branchId = $('[name="idbranch"]').val();
            
            $.ajax({
                url: HOST_URL + 'master/data/get_job_detail',
                type: 'POST',
                data: { idbranch: branchId },
                success: function (html) {
                    $('#branchDetail').html(html);
                    
                    initCustomerSelect2();

                    initIdcustByAjax();       
                    // Set mode DETAIL dan sembunyikan submit
                    $('#formJob').data('type', 'DETAIL');
                    $('#submitJob').hide();
                    lockJobForm();
                    
                    // Reset tombol Edit ke kondisi semula
                    $('#btnEditChild')
                        .html('<i class="fa fa-edit"></i> Edit Job')
                        .removeClass('btn-secondary')
                        .addClass('btn-warning')
                        .off('click')
                        .on('click', editChild);
                    
                    // Tampilkan tombol lainnya
                    // $('#btnAddChild').show();
                    // $('#btnDeleteJob').show();
                    
                    // Swal.fire({
                    //     title: 'Dibatalkan!',
                    //     text: 'Edit Job dibatalkan',
                    //     icon: 'success',
                    //     timer: 1500,
                    //     showConfirmButton: false
                    // });
                }
            });
        }
    });
}


$(document).on('submit', '#formJob', function (e) {
    e.preventDefault();

    let type = $(this).data('type');

    let confirmTitle = (type === 'INPUT')
        ? 'Simpan Job Baru?'
        : 'Simpan Perubahan Job?';

    let confirmText = (type === 'INPUT')
        ? 'Data Job akan disimpan'
        : 'Perubahan data Job akan disimpan';

    Swal.fire({
        title: confirmTitle,
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'master/data/saveJob',
            type: 'POST',
            data: $(this).serialize() + '&type=' + type,
            dataType: 'json',
            success: function (res) {

                if (res.status === 'success') {
                    Swal.fire('Berhasil', res.message, 'success');

                    if (type === 'INPUT') {

                        $('#btnEditChild').show();
                        $('#btnDeleteJob').show();

                        $('#btnAddChild')
                            .html('<i class="fa fa-plus"></i> Add Child Job')
                            .removeClass('btn-secondary')
                            .addClass('btn-success')
                            .off('click')
                            .on('click', addChild);

                    } else if (type === 'EDIT') {

                        $('#btnAddChild').show();
                        $('#btnDeleteJob').show();

                        $('#btnEditChild')
                            .html('<i class="fa fa-plus"></i> Edit Job')
                            .removeClass('btn-secondary')
                            .addClass('btn-warning')
                            .off('click')
                            .on('click', editChild);
                    }

                    lockJobForm();
                    $('#submitJob').hide();
                    $('#formJob').data('type', 'DETAIL');

                    // refresh zTree
                    refreshJobTree();

                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });

    });
});

function deleteChild() {

    let idbranch = $('[name="idbranch"]').val();

    if (!idbranch) {
        Swal.fire('Error', 'ID Job tidak ditemukan', 'error');
        return;
    }

    Swal.fire({
        title: 'Hapus Job?',
        text: 'Data Job akan dinonaktifkan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'master/data/delete_branch',
            type: 'POST',
            dataType: 'json',
            data: { idbranch: idbranch },
            success: function (res) {

                if (res.status === 'success') {
                    Swal.fire('Berhasil', res.message, 'success');

                    // optional: clear form
                    $('#branchDetail').empty();

                    refreshJobTree()
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    });
}

function formatCustomer(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.kdcustomer +"   <i class='fa fa-circle'></i>   "+ repo.nmcustomer +"  </div>";
    return markup;
}
function formatCustomerSelection(repo) {
    return repo.nmcustomer || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idcustomer").select2({
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
}).on('select2:select', function (e) {
    let data = e.params.data;
    // safety check
    let alamat = data.alamat_kantor ?? '';
    $('[name="alamatcust"]').val(alamat);
});


function refreshJobTree() {
    $.ajax({
        url: HOST_URL + 'master/data/js_vtree_job_query',
        type: 'POST',
        dataType: 'json',
        success: function (data) {

            var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
            if (treeObj) {
                treeObj.destroy();
            }

            $.fn.zTree.init($("#treeDemo"), setting, data);
            fuzzySearch('treeDemo', '#key', null, true);
        }
    });
}
