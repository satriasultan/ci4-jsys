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
                <h3 class="card-title">Form Izin Membawa Perangkat IT Pribadi ke Dalam Perusahaan</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('#')?>" method="post" id="formInputPerangkat" enctype="multipart/form-data" role="form">
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
                                    <label for="nmpemohon">Nama Pemohon/Pemilik Perangkat</label>
                                    <input type="text" name="nmpemohon" class="form-control" id="nmpemohon" maxlength="60" placeholder="Nama Pemohon/Pemilik Perangkat" style="text-transform:uppercase;">
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
                                    <label for="instansi">Instansi/Perusahaan</label>
                                    <input type="text" name="instansi" class="form-control" id="instansi" maxlength="50" placeholder="Instansi/Perusahaan" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jabatan">Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control" id="jabatan" maxlength="50" placeholder="Jabatan" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="telepon">No. Telp</label>
                                    <input type="text" name="telepon" class="form-control" id="telepon" maxlength="50" placeholder="No. Telp" style="text-transform:uppercase;">
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
                                    <label for="idperangkat">Serial Number dan/ atau ID Perangkat </label>
                                    <input type="text" name="idperangkat" class="form-control" id="idperangkat" maxlength="50" placeholder="Serial Number dan/ atau ID Perangkat" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dtllain">Detail Lain </label>
                                    <textarea type="text" name="dtllain" class="form-control" row="3" id="dtllain" placeholder="Detail Lain" style="text-transform:uppercase;"></textarea>
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
                                    <label for="Tujuan Penggunaan">Tujuan Penggunaan</label>
                                    <textarea name="tujuan" class="form-control" rows="5" placeholder="Tujuan Penggunaan..."  style="text-transform:uppercase;"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="periodemulai">Periode Mulai Penggunaan di Perusahaan (JTS)</label>
                                    <input type="text" name="periodemulai" class="form-control" id="periodemulai" placeholder="Periode Penggunaan di Perusahaan (JTS)">
                                </div>
                                <div class="form-group">
                                    <label for="periodeakhir">Periode Selesai Penggunaan di Perusahaan (JTS)</label>
                                    <input type="text" name="periodeakhir" class="form-control" id="periodeakhir" placeholder="Periode Penggunaan di Perusahaan (JTS)">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lokasi">Lokasi</label>
                                    <select name="lokasi" class="form-control" style="text-transform:uppercase;" >
                                        <option value="">--Pilih Lokasi--</option>
                                        <option value="P1"> JTS P1 (SIDOARJO) </option>
                                        <option value="P2"> JTS P2 (GRESIK)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="picjts">PIC (JTS) <small class="text-muted">( Tulis Detail Nama , Dept dan Jabatan )</small></label>
                                    <textarea name="picjts" class="form-control" rows="3" placeholder="PIC (JTS)..."  style="text-transform:uppercase;"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="aksesinternet">Akses Internet</label>
                                    <select name="aksesinternet" class="form-control" style="text-transform:uppercase;"      onchange="toggleAkses(this.value)">
                                        <option value="">--Pilih Jenis Akses--</option>
                                        <option value="YA"> YA </option>
                                        <option value="TIDAK"> TIDAK </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldtujuan" style="display:none;">
                                <!-- Field khusus Lain-lain -->
                                <div class="form-group" >
                                    <label for="tujuanakses">Tujuan Akses</label>
                                    <input type="text" name="tujuanakses" class="form-control" id="tujuanakses" maxlength="60" placeholder="Tujuan Akses" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ipmac">IP & MAC Address </label>
                                    <input type="text" name="ipmac" class="form-control" id="ipmac" maxlength="60" placeholder="IP & MAC Address" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="wifi">Terhubung ke Wifi </label>
                                    <input type="text" name="wifi" class="form-control" id="wifi" maxlength="60" placeholder="Terhubung ke Wifi" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Description">Catatan/Informasi <small style="color: red;">(diisi oleh IT)</small></label>
                                    <textarea name="keterangan"  id="keterangan" class="form-control" rows="3" placeholder="Catatn/Informasi..."  style="text-transform:uppercase;" <?= $disabled ?>></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-exchange-alt"></i> Informasi Perizinan 
                        </div>
                        <div class="row">
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ijinmasuk">Izin Masuk <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <select name="ijinmasuk" class="form-control" style="text-transform:uppercase;" onchange="toggleMasuk(this.value)" <?= $disabled ?>>
                                        <option value="">--Pilih--</option>
                                        <option value="YA"> YA </option>
                                        <option value="TIDAK"> TIDAK </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldMasuk" style="display:none;">
                                <div class="form-group">
                                    <label for="alasantidakmasuk" class="form-label">Alasan Tidak Masuk <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <textarea name="alasantidakmasuk" id="alasantidakmasuk" class="form-control" rows="3" <?= $disabled ?>></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ijinkeluar">Izin Keluar <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <select name="ijinkeluar" class="form-control" style="text-transform:uppercase;" onchange="toggleKeluar(this.value)" <?= $disabled ?>>
                                        <option value="">--Pilih--</option>
                                        <option value="YA"> YA </option>
                                        <option value="TIDAK"> TIDAK </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="fieldKeluar" style="display:none;">
                                <div class="form-group">
                                    <label for="alasantidakkeluar" class="form-label">Alasan Tidak Keluar <small style="color: red;">Hanya dapat diisi oleh IT</small></label>
                                    <textarea name="alasantidakkeluar" id="alasantidakkeluar" class="form-control" rows="3" <?= $disabled ?>></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('form/pa') ?>" class="btn btn-default">Kembali</a>
                    <button id="btnSave" onclick="saveInputPerangkat();" class="btn btn-primary float-right">Submit</button>
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
            <h5 class="modal-title" id="ketentuanModalLabel">Ketentuan Perangkat IT Pribadi</h5>
            <!-- <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button> -->
        </div>
        <div class="modal-body" style="line-height: 1.6; font-size: 14px;">
            <ol>
            <li>Perangkat IT pribadi ini akan digunakan hanya untuk kepentingan pekerjaan sesuai izin yang diberikan & tidak akan digunakan untuk kegiatan yang melanggar kebijakan keamanan informasi, aturan perusahaan, atau hukum yang berlaku.</li>
            <li>Jika Perangkat diizinkan dibawa masuk dan digunakan di area perusahaan maka perangkat diberikan tanda berupa Sticker Identitas dari PT JATIM TAMAN STEEL, MFG dan akan diperiksa sebelum masuk dan keluar area perusahaan.</li>
            <li>Memastikan dan menjamin perangkat pribadi bebas dari malware, virus, atau program ilegal / bajakan / non license dan berbahaya sebelum digunakan di area perusahaan.</li>
            <li>Segala konsekuensi, kerugian, kerusakan, kehilangan data, maupun penyalahgunaan lisensi yang terjadi dan/atau disebabkan oleh perangkat pribadi ini sepenuhnya menjadi tanggung jawab pemilik perangkat.</li>
            <li>PIC dari Perusahaan (PT JATIM TAMAN STEEL, MFG) hanya bertugas sebagai penerima tamu dan pengawas selama pihak eksternal membawa dan menggunakan perangkat IT Pribadinya di area perusahaan.</li>
            <li>Pihak perusahaan (PT JATIM TAMAN STEEL, MFG) tidak bertanggung jawab atas kerusakan atau kehilangan perangkat pribadi selama berada di area perusahaan.</li>
            </ol>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info" data-bs-dismiss="modal">Saya Mengerti</button>
        </div>
        </div>
    </div>
</div>




<script type="application/javascript" src="<?= base_url('assets/pagejs/form/perangkat.js') ?>"></script>
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