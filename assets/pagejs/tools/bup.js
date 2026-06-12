function closePeriod(el) {
    var periode = $('#periode').val().trim();
    var btnLabel = $(el).data('label');

    // Validasi
    if (periode === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Periode tidak boleh kosong!'
        });
        return;
    }

    let actionText = '';
    let confirmText = '';

    if (btnLabel === 'Open Period') {
        actionText = 'membuka';
        confirmText = `Periode ${periode} akan dibuka kembali.\nTransaksi akan diperbolehkan lagi.`;
    } else {
        actionText = 'menutup';
        confirmText = `Periode ${periode} akan ditutup.\nTransaksi tidak dapat dilakukan setelah ini.`;
    }

    
    // Konfirmasi
    Swal.fire({
        title: 'Konfirmasi',
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Kirim data ke server
            $.ajax({
                url: HOST_URL + 'tools/konfigurasi/processClosePeriod',
                type: 'POST',
                data: {
                    periode: periode
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reload halaman untuk menampilkan data terbaru
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan: ' + error
                    });
                }
            });
        }
    });
}


