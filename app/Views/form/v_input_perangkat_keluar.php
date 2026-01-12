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
                <h3 class="card-title">Form Izin Membawa Perangkat IT Pribadi Keluar Perusahaan</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('#')?>" method="post" id="formInputPerangkatKeluar" enctype="multipart/form-data" role="form">
                <div class="card-body" style="background-color: #e2e2f9;">
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
                                    <!-- <input type="text" name="jabatan" class="form-control" id="jabatan" maxlength="50" placeholder="Jabatan" style="text-transform:uppercase;"> -->
                                    <select name="jabatan" id="jabatan" class="form-control inform" placeholder="Pilih Jabatan">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="departmen">Departemen/Bagian</label>
                                    <!-- <input type="text" name="departmen" class="form-control" id="departmen" maxlength="50" placeholder="Departemen" style="text-transform:uppercase;"> -->
                                    <select name="departmen" id="departmen" class="form-control inform" placeholder="Pilih Department">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jnsform">Jenis Form</label>
                                    <select name="jnsform" class="form-control" style="text-transform:uppercase;" >
                                        <option value="">--Pilih Jenis Form--</option>
                                        <option value="BARU"> FORM BARU </option>
                                        <option value="PERPANJANGAN"> PERPANJANGAN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="osticket">No. Ticket(OS Ticket) <small style="color:red">diisi oleh IT</small></label>
                                    <input type="text" name="osticket" class="form-control" id="osticket" maxlength="50" placeholder="No. OS Ticket" style="text-transform:uppercase;" <?= $disabled ?>>
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="idsticker">ID Sticker <small class="text-muted">Jika Diizinkan</small></label>
                                    <input type="text" name="idsticker" class="form-control" id="idsticker" maxlength="50" placeholder="ID Sticker" style="text-transform:uppercase;">
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-laptop"></i> Informasi Perangkat
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jnsperangkat">Jenis Perangkat</label>
                                    <select name="jnsperangkat" class="form-control" style="text-transform:uppercase;"      onchange="togglePerangkat(this.value)">
                                        <option value="">--Pilih Jenis Akses--</option>
                                        <option value="LAPTOP"> LAPTOP </option>
                                        <option value="DESKTOP"> DESKTOP </option>
                                        <option value="TABLET"> TABLET </option>
                                        <option value="PRINTER"> PRINTER </option>
                                        <option value="SCANNER"> SCANNER </option>
                                        <option value="OTHER">LAIN-LAIN </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldOther" style="display:none;">
                                <!-- Field khusus Lain-lain -->
                                <div class="form-group" >
                                    <label for="otherField">Lain-lain</label>
                                    <input type="text" name="otherField" class="form-control" id="otherField" maxlength="60" placeholder="Keterangan Lain-lain" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="merk">Merek & Tipe </label>
                                    <input type="text" name="merk" class="form-control" id="merk" maxlength="50" placeholder="Merek & Tipe" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="noasset">No Asset/ID Sticker </label>
                                    <input type="text" name="noasset" class="form-control" id="noasset" maxlength="50" placeholder="No Asset/ID Sticker" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="qty">Quantity </label>
                                    <input type="text" name="qty" class="form-control"maxlength="50" id="qty" placeholder="Quantity" style="text-transform:uppercase;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-file-text"></i> Informasi Penggunaan
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="keperluan">Keperluan dan Alasan Membawa Perangkat Keluar</label>
                                    <textarea name="keperluan" class="form-control" rows="5" placeholder="Keperluan dan Alasan Membawa Perangkat Keluar..."  style="text-transform:uppercase;"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tujuan">Tujuan (Tempat)</label>
                                    <textarea name="tujuan" class="form-control" rows="3" placeholder="Tujuan (Tempat)..."  style="text-transform:uppercase;"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="periode">Periode </label>
                                    <select name="periode" class="form-control" style="text-transform:uppercase;"   onchange="togglePeriode(this.value)">
                                        <option value="">--Pilih Periode--</option>
                                        <option value="RUTIN"> RUTIN </option>
                                        <option value="TERTENTU"> WAKTU TERTENTU</option>
                                    </select>
                                    <small class="text-muted">( Maksimal 6 Bulan, jika diatas 6 bulan harus memperbarui form permohonan )</small>
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldPeriode"  style="display:none;" >
                                <div class="form-group">
                                    <label for="periodemulai">Periode Mulai</label>
                                    <input type="text" name="periodemulai" class="form-control" id="periodemulai" placeholder="Periode Mulai">
                                </div>
                                <div class="form-group">
                                    <label for="periodeakhir">Periode Selesai</label>
                                    <input type="text" name="periodeakhir" class="form-control" id="periodeakhir" placeholder="Periode Selesai">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="picakun">Pengguna Perangkat</label>
                                    <select name="picakun" class="form-control" id="picakun" style="text-transform:uppercase;"     onchange="toggleKaryawan(this.value)">
                                        <option value="">--Pilih PIC Akun--</option>
                                        <option value="PEMOHON">PEMOHON SENDIRI</option>
                                        <option value="OTHER">ORANG LAIN</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Field khusus Lain-lain -->
                            <div class="col-md-3" id="fieldOtherKaryawan" style="display:none;">
                                <div class="form-group">
                                    <label for="nmpic">Nama PIC</label>
                                    <input type="text" name="nmpic" class="form-control" id="nmkaryawanlain" maxlength="60" placeholder="Nama PIC" style="text-transform:uppercase;">
                                </div>
                                <div class="form-group">
                                    <label for="jabatanpic">Jabatan PIC</label>
                                    <input type="text" name="jabatanpic" class="form-control" id="jabatanpic" maxlength="60" placeholder="Jabatan PIC" style="text-transform:uppercase;">
                                </div>
                                <div class="form-group">
                                    <label for="departemenpic">Instansi/Departemen</label>
                                    <input type="text" name="departemenpic" class="form-control" id="departemenpic" maxlength="60" placeholder="Departemen PIC" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Description">Catatan/Informasi <small style="color: red;">(diisi oleh IT)</small></label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Catatn/Informasi..."  style="text-transform:uppercase;" <?= $disabled ?>></textarea>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-sign-in-alt"></i> Pemeriksaan Perangkat (Sebelum Keluar)
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tglpemeriksaan">Tanggal Pemeriksaan <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <input type="text" name="tglpemeriksaan" class="form-control" id="tglpemeriksaan" placeholder="Tanggal Pemeriksaan"  <?= $disabled ?>>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dataperangkat" class="form-label">Data Pada Perangkat <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <textarea name="dataperangkat" id="dataperangkat" class="form-control" rows="3" <?= $disabled ?>></textarea>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="enkripsidata" name="enkripsidata" value="1" <?= $disabled ?>>
                                    <label class="form-check-label" for="enkripsidata">Enkripsi data</label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="kondisi" class="form-label">Kondisi Perangkat <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <textarea name="kondisi" id="kondisi" class="form-control" rows="3" <?= $disabled ?>></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tglpenyerahan">Tanggal Penyerahan Perangkat <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <input type="text" name="tglpenyerahan" class="form-control" id="tglpenyerahan" placeholder="Tanggal Penyerahan Perangkat" <?= $disabled ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-sign-out-alt"></i> Pengembalian Perangkat
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tglpengembalian">Tanggal Pengembalian <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <input type="text" name="tglpengembalian" class="form-control" id="tglpengembalian" placeholder="Tanggal Pengembalian"  <?= $disabled ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pembaruanizin">Pembaruan Izin <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <select name="pembaruanizin" class="form-control" style="text-transform:uppercase;" onchange="togglePembaruanIzin(this.value)" <?= $disabled ?>>
                                        <option value="">--Pilih--</option>
                                        <option value="YA"> YA </option>
                                        <option value="TIDAK"> TIDAK </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldPembaruan" style="display:none;">
                                <div class="form-group">
                                    <label for="alasanterlambat" class="form-label">Alasan Jika Terlambat <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <textarea name="alasanterlambat" id="alasanterlambat" class="form-control" rows="3" <?= $disabled ?>></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="keteranganlain" class="form-label">Keterangan Lain <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <textarea name="keteranganlain" id="keteranganlain" class="form-control" rows="3" <?= $disabled ?>></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('form/pa/laptop') ?>" class="btn btn-default">Kembali</a>
                    <button id="btnSave" onclick="saveInputPerangkatKeluar();" class="btn btn-primary float-right">Submit</button>
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


<!-- Modal Ketentuan -->
<div class="modal fade" id="ketentuanModal" tabindex="-1" role="dialog" aria-labelledby="ketentuanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="ketentuanModalLabel">Ketentuan Perangkat IT Keluar Perusahaan</h5>
                <!-- <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <div class="modal-body" style="line-height: 1.6; font-size: 14px;">
                <ol>
                    <li>Seluruh perangkat IT yang digunakan oleh karwayan harus mempunyai sticker identitas asset dari perusahaan karena sebagai point pemeriksaan security saat perangkat akan dibawa masuk / keluar perusahaan. Jika perangkat IT yang digunakan tidak memiliki Sticker id asset perusahaan maka pengguna perangkat segera menghubungi Departemen IT / IRGA agar segera mendapat sticker identitas asset untuk perangkat tersebut.</li>
                    <li>Jika ditemukan ada unsur kesengajaan pengguna untuk tidak menempelkan sticker identitas asset pada perangkat IT yang digunakan, maka penggna dapat dikenai sanksi sesuai dengan peraturan yang berlaku.</li>
                    <li>Penyalahgunaan perangkat diluar dari penugasan perusahaan akan menyebabkan pencabutan izin, tindakan disiplin atau sanksi sesuai peraturan yang berlaku.</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>




<script type="application/javascript" src="<?= base_url('assets/pagejs/form/perangkatkeluar.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

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


        $('#periodemulai').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodemulai').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodemulai').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        $('#tglpemeriksaan').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#tglpemeriksaan').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#tglpemeriksaan').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#tglpenyerahan').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#tglpenyerahan').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#tglpenyerahan').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        $('#tglpengembalian').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#tglpengembalian').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#tglpengembalian').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        $('#periodeakhir').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodeakhir').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodeakhir').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });



    });

</script>