<style>

    .section-block {
        background-color: #f8f6f6;
        border-left: 4px solid #007bff;
        /* border-bottom: 4px solid #007bff;  */
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
        box-shadow 0.4s ease,
        border-color 0.4s ease;

        transform: scale(1);
        will-change: transform;
    }

    .section-header {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 20px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 8px;
    }

    .section-block:hover {
        /* transform: scale(1.02); */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-left-color: #0056b3;
        border-bottom-color: #0056b3;
    }

    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
    }

    .form-control:disabled, .form-control[readonly] {
        background-color: #dfdfdf;
        opacity: 1;
    }

</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
                    <?php foreach ($y as $y1) { ?>
                        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                            <li class="breadcrumb-item"><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<?php echo $message;?>
<?php
$isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
$disabled = $isIT ? '' : 'disabled';
?>
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- jquery validation -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Akses Jaringan dan Sistem</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('#')?>" method="post" id="formInputInternet" enctype="multipart/form-data" role="form">
                <div class="card-body" style="background-color: #e2e2f9">
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-address-card"></i> Informasi Pemohon
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" id="type" name="type" value="<?= $type ?>" autocomplete="off">
                                    <input type="hidden" id="id" name="id" value="<?= $id ?>" autocomplete="off">
                                    <label for="docno">Dokumen</label>
                                    <input type="text" name="docno" class="form-control" id="docno" style="text-transform: uppercase"  value="<?php echo $docno; ?>" placeholder="Document Input" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nmpemohon">Nama Pemohon</label>
                                    <input type="text" name="nmpemohon" class="form-control" id="nmpemohon" maxlength="60" placeholder="Nama Pemohon" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="docdate">Tanggal Permintaan</label>
                                    <input type="text" name="docdate" class="form-control" id="docdate" placeholder="Tanggal Permintaan">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jabatan">Jabatan</label>
                                    <select name="jabatan" id="jabatan" class="form-control inform" placeholder="Pilih Jabatan">
                                    </select>
                                    <!-- <input type="text" name="jabatan" class="form-control" id="jabatan" maxlength="50" placeholder="Jabatan" style="text-transform:uppercase;"> -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="departmen">Departemen/Bagian</label>
                                    <select name="departmen" id="departmen" class="form-control inform" placeholder="Pilih Department">
                                    </select>
                                    <!-- <input type="text" name="departmen" class="form-control" id="departmen" maxlength="50" placeholder="Departemen/Bagian" style="text-transform:uppercase;"> -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="osticket">No. Ticket(OS Ticket) <small style="color:red">diisi oleh IT</small></label>
                                    <input type="text" name="osticket" class="form-control" id="osticket" maxlength="50" placeholder="No. OS Ticket" style="text-transform:uppercase;" <?= $disabled ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-globe"></i> Informasi Akses
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Jenis Akses</label><br>
                                <div class="row">
                                    <!-- Baris 1 -->
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="aksesUser" name="aksesUser" value="true" onchange="toggleAkses()">
                                            <label class="form-check-label" for="aksesUser">USER DOMAIN/DATA USER</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="aksesInternet" name="aksesInternet" value="true" onchange="toggleAkses()">
                                            <label class="form-check-label" for="aksesInternet">INTERNET</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="aksesEmail" name="aksesEmail" value="true" onchange="toggleAkses()">
                                            <label class="form-check-label" for="aksesEmail">EMAIL</label>
                                        </div>
                                    </div>

                                    <!-- Baris 2 -->
                                    <div class="col-md-4 mb-2 mt-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="aksesApp" name="aksesApp" value="true" onchange="toggleAkses()">
                                            <label class="form-check-label" for="aksesApp">APLIKASI</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2 mt-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="aksesOther" name="aksesOther" value="true" onchange="toggleAkses()">
                                            <label class="form-check-label" for="aksesOther">LAIN-LAIN</label>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Field tambahan -->
                            <div class="col-md-3" id="fieldEmail" style="display:none;">
                                <div class="form-group">
                                    <label for="emailField">Email</label>
                                    <input type="text" name="emailField" class="form-control" id="emailField" maxlength="100" placeholder="Isi Alamat Email" style="text-transform:lowercase;">
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldAplikasi" style="display:none;">
                                <div class="form-group">
                                    <label for="appField">Aplikasi</label>
                                    <input type="text" name="appField" class="form-control" id="appField" maxlength="100" placeholder="Nama Aplikasi" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldOther" style="display:none;">
                                <div class="form-group">
                                    <label for="otherField">Lain-lain</label>
                                    <input type="text" name="otherField" class="form-control" id="otherField" maxlength="60" placeholder="Jenis Akses Lain-lain" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Keperluan">Keperluan dan Alasan Akses</label>
                                    <textarea name="keperluan" class="form-control" rows="5" placeholder="Keperluan..."  style="text-transform:uppercase;"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sifatakses">Sifat Akses</label>
                                    <select name="sifatakses" class="form-control" style="text-transform:uppercase;" >
                                        <option value="">--Pilih Sifat Akses--</option>
                                        <option value="RUTIN"> RUTIN/PERMANEN </option>
                                        <option value="SEMENTARA">SEMENTARA </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="waktuakses">Waktu Akses</label>
                                    <select name="waktuakses" class="form-control" style="text-transform:uppercase;"  onchange="toggleWaktu(this.value)">
                                        <option value="">--Pilih Waktu Akses--</option>
                                        <option value="KERJA"> HARI & JAM KERJA </option>
                                        <option value="TERTENTU">WAKTU TERTENTU </option>
                                        <option value="OTHER">LAIN-LAIN </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldTertentu" style="display:none;">
                                <!-- Field khusus Deskripsikan Waktu Tertentu -->
                                <div class="form-group">
                                    <label for="tertentuField">Deskripsi Waktu Tertentu</label>
                                    <input type="text" name="tertentuField" class="form-control" id="tertentuField" maxlength="60" placeholder="Deskripsi Waktu Tertentu" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldOtherWaktu" style="display:none;">
                                <!-- Field khusus Lain-lain -->
                                <div class="form-group" >
                                    <label for="otherWaktuField">Lain-lain</label>
                                    <input type="text" name="otherWaktuField" class="form-control" id="otherWaktuField" maxlength="60" placeholder="Keterangan Waktu Lain" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="expdate">Masa Berlaku Akses <small class="text-muted">(Tanggal Berakhir Akses)</small></label>
                                    <input type="text" name="expdate" class="form-control" id="expdate" placeholder="Masa Berlaku Akses">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-file-text"></i> Informasi Tambahan
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="perangkatregist">Perangkat Terdaftar <small class="text-muted">(isi sesuai perangkat yang digunakan)</small></label>
                                    <input type="text" name="perangkatregist" class="form-control" id="perangkatregist" maxlength="60" placeholder="Perangkat Terdaftar" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="picakun">PIC Akun (Pengguna Akun)</label>
                                    <select name="picakun" class="form-control" id="picakun" style="text-transform:uppercase;"     onchange="toggleKaryawan(this.value)">
                                        <option value="">--Pilih PIC Akun--</option>
                                        <option value="PEMOHON">PEMOHON SENDIRI</option>
                                        <option value="OTHER">KARYAWAN LAIN</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Field khusus Lain-lain -->
                            <div class="col-md-6" id="fieldOtherKaryawan" style="display:none;">
                                <div class="form-group">
                                    <label for="nmpic">Nama PIC</label>
                                    <input type="text" name="nmpic" class="form-control" id="nmkaryawanlain" maxlength="60" placeholder="Nama PIC" style="text-transform:uppercase;">
                                </div>
                                <div class="form-group">
                                    <label for="jabatanpic">Jabatan PIC</label>
                                    <input type="text" name="jabatanpic" class="form-control" id="jabatanpic" maxlength="60" placeholder="Jabatan PIC" style="text-transform:uppercase;">
                                </div>
                                <div class="form-group">
                                    <label for="departemenpic">Departemen PIC</label>
                                    <input type="text" name="departemenpic" class="form-control" id="departemenpic" maxlength="60" placeholder="Departemen PIC" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Description">Catatan/Informasi <small style="color: red;">(diisi oleh IT)</small></label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Catatn/Informasi..."  style="text-transform:uppercase;" <?= $disabled ?>></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('form/pa/internet') ?>" class="btn btn-default">Kembali</a>
                    <button id="btnSave" onclick="saveInputInternet();" class="btn btn-primary float-right">Submit</button>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
    <!--/.col (left) -->
    <!-- right column -->
    <div class="col-md-6">

    </div>
    <!--/.col (right) -->
</div>

<!-- Modal Ketentuan Akses Internet -->
<div class="modal fade" id="ketentuanAksesModal" tabindex="-1" aria-labelledby="ketentuanAksesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="ketentuanAksesLabel">Ketentuan Akses Internet</h5>
                <!-- <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body" style="line-height: 1.6; font-size: 14px;">
                <ol>
                    <li>PIC Akun menyetujui dan mematuhi kebijakan keamanan informasi, kebijakan keamanan sistem / aplikasi, dan prosedur terkait.</li>
                    <li>PIC Akun dilarang mengalihkan dan/atau meminjamkan hak akses kepada orang lain.</li>
                    <li>Akun hanya boleh digunakan di Perangkat Perusahaan yang terdaftar atas seizin Atasan PIC Akun dan Manager IT.</li>
                    <li>PIC Akun dilarang menyalahgunakan akses untuk kepentingan selain penugasan yang telah ditetapkan perusahaan.</li>
                    <li>PIC Akun harus memberitahukan kepada atasan dan IT apabila ada perubahan hak akses (Resign, Mutasi, dll).</li>
                    <li>Pelanggaran terhadap ketentuan akses dan prosedur terkait akan menyebabkan pencabutan hak akses, tindakan disiplin, atau sanksi sesuai peraturan yang berlaku.</li>
                </ol>
                <p class="mt-3"><em style="color: red;">"Saya menyetujui dan bersedia mematuhi ketentuan ini, saya akan menggunakan hak akses sesuai dengan tugas dan pekerjaan yang diberikan oleh perusahaan dan saya akan melaporkan setiap masalah atau insiden keamanan informasi yang saya ketahui kepada atasan."</em></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>



<script type="application/javascript" src="<?= base_url('assets/pagejs/form/internet.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

        // Function untuk toggle field masa berlaku berdasarkan sifat akses
        function toggleMasaBerlaku(sifatAkses) {
            if (sifatAkses === "RUTIN") {
                $('[name="expdate"]').closest('.form-group').hide();
            } else {
                $('[name="expdate"]').closest('.form-group').show();
            }
        }

        // Panggil saat perubahan sifat akses
        $('[name="sifatakses"]').on('change', function() {
            toggleMasaBerlaku(this.value);
        });

        // Panggil saat page load untuk set initial state
        $(document).ready(function() {
            toggleMasaBerlaku($('[name="sifatakses"]').val());
        });

        $('#docdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#expdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#expdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#expdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>