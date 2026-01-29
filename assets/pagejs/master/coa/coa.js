

function lockCoaForm() {
    $('#formCoa input, #formCoa select').prop('readonly', true).prop('disabled', true);
    $('[name="idcoa"], [name="induk"], [name="level"]').prop('readonly', true);
}

function unlockCoaForm() {
    $('#formCoa input, #formCoa select').prop('readonly', false).prop('disabled', false);
    $('[name="idcoa"], [name="induk"], [name="level"]').prop('readonly', true);
}

let previousFormData = {};


function addChild() {
    let parentId = $('[name="idcoa"]').val();
    let parentText = $('[name="nmcoa"]').val();
    let parentLevel = parseInt($('[name="level"]').val().trim());

    if (!parentId) {
        Swal.fire('Pilih COA', 'Silakan pilih parent COA terlebih dahulu', 'warning');
        return;
    }

    previousFormData = {
        idcoa: $('[name="idcoa"]').val(),
        nmcoa: $('[name="nmcoa"]').val(),
        nm2coa: $('[name="nm2coa"]').val(),
        groupcoa: $('[name="groupcoa"]').val(),
        idcur: $('[name="idcur"]').val(),
        jenis: $('[name="jenis"]').val(),
        induk: $('[name="induk"]').val(),
        level: $('[name="level"]').val()
    };


    // Tampilkan konfirmasi
    Swal.fire({
        title: 'Tambah Child COA',
        html: `Apakah Anda akan menambah child dari <b>${parentText}</b> (ID: ${parentId})?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Tambah Child',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Reset form
            $('#formCoa')[0].reset();
            
            // Sembunyikan tombol yang tidak diperlukan
            $('#btnEditChild').hide();
            $('#btnDeleteCoa').hide();
            
            // Ubah tombol Add Child menjadi Cancel
            $('#btnAddChild')
                .html('<i class="fa fa-times"></i> Cancel')
                .removeClass('btn-success')
                .addClass('btn-secondary')
                .off('click') // Hapus event listener sebelumnya
                .on('click', cancelAddChild); // Tambah event untuk cancel
            
            // Set nilai induk dan level
            $('[name="induk"]').val(parentId);
            $('[name="level"]').val(parentLevel + 1);
            
            $('[name="idcoa"]').val(''); // Kosongkan ID COA baru
            $('[name="nmcoa"]').val(''); // Kosongkan nama COA
            $('[name="nm2coa"]').val(''); // Kosongkan nama COA 2
            // checkbox
            $('[name="isdetail"]').prop('checked', false);

            // radio group
            $('[name="groupcoa"]').prop('checked', false);

            // select2
            $('#idcur').val(null).trigger('change');
            $('[name="jenis"]').val(''); // Kosongkan jenis
            \
            // Set tipe form
            $('#formCoa').data('type', 'INPUT');
            
            // Buka form dan tampilkan submit
            unlockCoaForm();
            $('[name="idcoa"]').prop('readonly', false).prop('disabled', false);
            $('#submitCOA').show();
            
            // Fokus ke field pertama
            $('[name="nmcoa"]').focus();
            
            // Tampilkan notifikasi sukses
            Swal.fire({
                title: 'Siap!',
                text: 'Silakan isi data child COA baru',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

// Fungsi untuk cancel add child
function cancelAddChild() {
    Swal.fire({
        title: 'Batalkan Tambah Child?',
        text: 'Data yang sudah diisi akan hilang',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Lanjutkan Tambah'
    }).then((result) => {
        if (result.isConfirmed) {

            if (previousFormData) {
                $('[name="idcoa"]').val(previousFormData.idcoa || '');
                $('[name="nmcoa"]').val(previousFormData.nmcoa || '');
                $('[name="nm2coa"]').val(previousFormData.nm2coa || '');
                $('[name="groupcoa"]').val(previousFormData.groupcoa || '');
                $('[name="idcur"]').val(previousFormData.idcur || '');
                $('[name="jenis"]').val(previousFormData.jenis || '');
                $('[name="induk"]').val(previousFormData.induk || '');
                $('[name="level"]').val(previousFormData.level || '');
            }
            // Reset tombol Add Child ke kondisi semula
            $('#btnAddChild')
                .html('<i class="fa fa-plus"></i> Add Child')
                .removeClass('btn-secondary')
                .addClass('btn-success')
                .off('click')
                .on('click', addChild);
            
            // Reset form ke mode DETAIL
            $('#formCoa').data('type', 'DETAIL');
            $('#submitCOA').hide();
            lockCoaForm();

            previousFormData = {};
            
            // Tampilkan tombol lainnya
            $('#btnEditChild').show();
            $('#btnDeleteCoa').show();
        }
    });
}

function editChild() {
    let coaId = $('[name="idcoa"]').val();
    let coaName = $('[name="nmcoa"]').val();

    if (!coaId) {
        Swal.fire('Error', 'Tidak ada COA dipilih', 'error');
        return;
    }

    // Tampilkan konfirmasi
    Swal.fire({
        title: 'Edit COA',
        html: `Apakah Anda akan mengedit COA <b>${coaName}</b> (ID: ${coaId})?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Edit COA',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Sembunyikan tombol yang tidak diperlukan
            $('#btnAddChild').hide();
            $('#btnDeleteCoa').hide();
            
            // Ubah tombol Edit menjadi Cancel
            $('#btnEditChild')
                .html('<i class="fa fa-times"></i> Cancel')
                .removeClass('btn-warning')
                .addClass('btn-secondary')
                .off('click') // Hapus event listener sebelumnya
                .on('click', cancelEditChild); // Tambah event untuk cancel
            
            // Set tipe form
            $('#formCoa').data('type', 'EDIT');
            
            // Buka form dan tampilkan submit
            unlockCoaForm();
            $('#submitCOA').show();
            
            // Fokus ke field pertama
            $('[name="nmcoa"]').focus();
            
            // Tampilkan notifikasi
            // Swal.fire({
            //     title: 'Edit Mode',
            //     text: 'Silakan edit data COA',
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
            let coaId = $('[name="idcoa"]').val();
            
            $.ajax({
                url: HOST_URL + 'master/data/get_coa_detail',
                type: 'POST',
                data: { idcoa: coaId },
                success: function (html) {
                    $('#coaDetail').html(html);
                    
                    initCurrencySelect2();

                    initIdcurByAjax();       
                    // Set mode DETAIL dan sembunyikan submit
                    $('#formCoa').data('type', 'DETAIL');
                    $('#submitCOA').hide();
                    lockCoaForm();
                    
                    // Reset tombol Edit ke kondisi semula
                    $('#btnEditChild')
                        .html('<i class="fa fa-edit"></i> Edit COA')
                        .removeClass('btn-secondary')
                        .addClass('btn-warning')
                        .off('click')
                        .on('click', editChild);
                    
                    // Tampilkan tombol lainnya
                    $('#btnAddChild').show();
                    $('#btnDeleteCoa').show();
                    
                    // Swal.fire({
                    //     title: 'Dibatalkan!',
                    //     text: 'Edit COA dibatalkan',
                    //     icon: 'success',
                    //     timer: 1500,
                    //     showConfirmButton: false
                    // });
                }
            });
        }
    });
}


$(document).on('submit', '#formCoa', function (e) {
    e.preventDefault();

    let type = $(this).data('type');

    let confirmTitle = (type === 'INPUT')
        ? 'Simpan COA Baru?'
        : 'Simpan Perubahan COA?';

    let confirmText = (type === 'INPUT')
        ? 'Data COA akan disimpan'
        : 'Perubahan data COA akan disimpan';

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
            url: HOST_URL + 'master/data/saveCOA',
            type: 'POST',
            data: $(this).serialize() + '&type=' + type,
            dataType: 'json',
            success: function (res) {

                if (res.status === 'success') {
                    Swal.fire('Berhasil', res.message, 'success');

                    if (type === 'INPUT') {

                        $('#btnEditChild').show();
                        $('#btnDeleteCoa').show();

                        $('#btnAddChild')
                            .html('<i class="fa fa-plus"></i> Add Child COA')
                            .removeClass('btn-secondary')
                            .addClass('btn-success')
                            .off('click')
                            .on('click', addChild);

                    } else if (type === 'EDIT') {

                        $('#btnAddChild').show();
                        $('#btnDeleteCoa').show();

                        $('#btnEditChild')
                            .html('<i class="fa fa-plus"></i> Edit COA')
                            .removeClass('btn-secondary')
                            .addClass('btn-warning')
                            .off('click')
                            .on('click', editChild);
                    }

                    lockCoaForm();
                    $('#submitCOA').hide();
                    $('#formCoa').data('type', 'DETAIL');

                    // refresh zTree
                    refreshCoaTree();

                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });

    });
});

function deleteChild() {

    let idcoa = $('[name="idcoa"]').val();

    if (!idcoa) {
        Swal.fire('Error', 'ID COA tidak ditemukan', 'error');
        return;
    }

    Swal.fire({
        title: 'Hapus COA?',
        text: 'Data COA akan dinonaktifkan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: HOST_URL + 'master/data/delete_coa',
            type: 'POST',
            dataType: 'json',
            data: { idcoa: idcoa },
            success: function (res) {

                if (res.status === 'success') {
                    Swal.fire('Berhasil', res.message, 'success');

                    // optional: clear form
                    $('#coaDetail').empty();

                    refreshCoaTree()
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }
        });
    });
}

function formatCurrency(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.currcode +"   <i class='fa fa-circle'></i>   "+ repo.currname +"  </div>";
    return markup;
}
function formatCurrencySelection(repo) {
    return repo.currname || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idcur").select2({
    placeholder: "Ketik/Pilih Currency",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_currency',
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
    templateResult: formatCurrency, // omitted for brevity, see the source of this page
    templateSelection: formatCurrencySelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});

function setInitialCurrency() {
    let idcur = $('#idcur_init').val();
    if (!idcur) return;

    $.ajax({
        url: HOST_URL + 'api/globalmodule/list_currency',
        type: 'POST',
        dataType: 'json',
        data: {
            _search_: '',
            _page_: 1,
            _draw_: true,
            _start_: 1,
            _perpage_: 1,
            _paramglobal_: idcur // asumsi backend bisa filter by id
        },
        success: function (res) {
            if (res.items && res.items.length > 0) {
                let cur = res.items[0];

                let option = new Option(
                    cur.currname,
                    cur.idcur,   // PENTING: value
                    true,
                    true
                );

                $('#idcur')
                    .append(option)
                    .trigger('change');
            }
        }
    });
}



function refreshCoaTree() {
    $.ajax({
        url: HOST_URL + 'master/data/js_vtree_query',
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
