<?php
/*
 * Author: Fiky Ashariza
 * Create Date: 3/23/21, 8:06 AM
 * Path Directory: v_input_karyawan.php
 */

use App\Libraries\Fiky_encryption;

$this->fiky_encryption = new Fiky_encryption();
?>
<style>
    /*div.dataTables_wrapper {*/
    /*    width: 1200px;*/
    /*    margin: 0 auto;*/
    /*}*/
</style>
<ol class="breadcrumb">
    <div class="pull-right"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
    <?php foreach ($y as $y1) { ?>
        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
            <li><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
        <?php } else { ?>
            <li class="active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
        <?php } ?>
    <?php } ?>
</ol>
<?php echo $message;?>
<?php /*
<div class="col-sm-12">
    <a href="<?php echo base_url('trans/karyawan/')?>" type="button" class="btn btn-default pull-left"><i class="fa fa-arrow-left"></i> Kembali</a>
</div>
 */ ?>

<!--	</div--><!-- ./col -->
<div class="nav-tabs-custom ">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_1" data-bs-toggle="tab">Detail Karyawan</a></li>
        <li><a href="#tab_2" data-bs-toggle="tab">Riwayat Pendidikan</a></li>
        <li><a href="#tab_3" data-bs-toggle="tab">Kartu Pendukung</a></li>
        <li><a href="#tab_4" data-bs-toggle="tab">Laporan Absensi</a></li>
        <li><a href="#tab_5" data-bs-toggle="tab">Riwayat Kesehatan</a></li>

        <li class="pull-right"><a href="<?php echo base_url('trans/karyawan')?>" class="text-muted"><i class="fa fa-arrow-circle-left"></i> Kembali </a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">

            <form action="<?php echo base_url('#')?>" method="post" id="formInputKaryawan" enctype="multipart/form-data" role="form">
                <div class="col-md-12">
                    <h3 ALIGN="center"> <?php echo $type ;?> KARYAWAN <?php echo $docno ;?></h3>
                </div>
                <div class="roxsdfjo">
                    <input type="hidden" id="type" name="type" value="<?= $type ?>" autocomplete="off">
                    <input type="hidden" id="docno" name="docno" value="<?= $docno ?>" autocomplete="off">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="box box-info col-sm-12">
                                <div class="box-body" style="padding:5px;">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >NIK</label>
                                                <input type="text" class="form-control input-sm" id="nik" name="nik" autocomplete="off" style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputnama form-group">Nama Lengkap</label>
                                                <input type="text" class="form-control input-sm" id="nmlengkap" name="nmlengkap" style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputnama form-group">Nama Panggilan</label>
                                                <input type="text" class="form-control input-sm" id="callname" name="callname" style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputnama form-group">Tanggal Lahir</label>
                                                <input type="text" class="form-control input-sm" id="tgllahir" name="tgllahir" style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputKelamin" >Jenis Kelamin</label>
                                                <?php /*<input type="text" class="form-control input-sm" id="jk" name="jk"> */ ?>
                                                <select class="form-control input-sm"  id="jk" name="jk">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Agama</label>
                                                <?php /*<input type="text" class="form-control input-sm" id="kdagama" name="kdagama"> */ ?>
                                                <select class="form-control input-sm"  id="kdagama" name="kdagama">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Gol Darah</label>
                                                <!--<input type="text" class="form-control input-sm" id="goldarah" name="goldarah" style="text-transform: uppercase">-->
                                                <select class="form-control input-sm" id="goldarah" name="goldarah">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Warga Negara</label>
                                                <select class="form-control input-sm" id="stswn" name="stswn">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Division</label>
                                                <select class="form-control input-sm" id="id_division" name="id_division">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Departemen</label>
                                                <select class="form-control input-sm" id="id_dept" name="id_dept">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Sub Departemen / Section</label>
                                                <select class="form-control input-sm" id="id_subdept" name="id_subdept">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Jabatan / Position</label>
                                                <select class="form-control input-sm" id="id_jabatan" name="id_jabatan">>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Level Jabatan / Grade</label>
                                                <select class="form-control input-sm" id="id_grade" name="id_grade">>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Cost Center</label>
                                                <select class="form-control input-sm" id="costcenter" name="costcenter">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Group Golongan</label>
                                                <select class="form-control input-sm" id="id_gol" name="id_gol">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Penempatan Plant/Warehouse</label>
                                                <select class="form-control input-sm" id="plant" name="plant">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Status Kepegawaian</label>
                                                <select class="form-control input-sm" id="statuskepegawaian" name="statuskepegawaian">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Tanggal Masuk Kerja</label>
                                                <input type="text" class="form-control input-sm" id="tglmasukkerja" name="tglmasukkerja"  style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputatasan1" >Atasan 1</label>
                                                <select class="form-control input-sm"  id="nikatasan1" name="nikatasan1">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputatasan2" >Atasan 2</label>
                                                <select class="form-control input-sm"  id="nikatasan2" name="nikatasan2">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Tanggal Keluar Kerja</label>
                                                <input type="text" class="form-control input-sm" id="tglkeluarkerja" name="tglkeluarkerja" style="text-transform: uppercase" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Alasan Resign</label>
                                                <textarea type="text" class="form-control input-sm" id="alasan_resign" name="alasan_resign" style="text-transform: uppercase"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="box box-info col-sm-12">
                                <div class="box-body" style="padding:5px;">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >No KTP</label>
                                                <input type="number" class="form-control input-sm" id="id_ktp" name="id_ktp" maxlength="35" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Provinsi KTP</label>
                                                <select class="form-control input-sm" id="id_provktp" name="id_provktp">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Kota KTP</label>
                                                <select class="form-control input-sm"  id="id_kotaktp" name="id_kotaktp" >
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Kecamatan KTP</label>
                                                <select class="form-control input-sm"  id="id_kecktp" name="id_kecktp">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Kel/Desa KTP</label>
                                                <select class="form-control input-sm"  id="id_desaktp" name="id_desaktp">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label for="inputdept" >RT</label>
                                                <input type="text" class="form-control input-sm" id="rtktp" name="rtktp" maxlength="4" placeholder="Input RW KTP" >
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="inputdept" >RW</label>
                                                <input type="text" class="form-control input-sm" id="rwktp" name="rwktp" maxlength="4" placeholder="Input RT KTP">
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="inputdept" >Kodepos</label>
                                                <input type="text" class="form-control input-sm" id="id_posktp" name="id_posktp" maxlength="10" placeholder="Kodepos KTP">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Alamat KTP</label>
                                                <textarea type="text" class="form-control input-sm" id="desc_ktp" name="desc_ktp" style="text-transform: uppercase"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="box box-info col-sm-12">
                                <div class="box-body" style="padding:5px;">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Type Domisili</label>
                                                <select class="form-control input-sm" id="typedomisili" name="typedomisili" placeholder="Pilih/Ketik Type Domisili">
                                                    <option value="Rumah Sendiri / Rumah Kontrak / Rumah Orang Tua"> Rumah Sendiri / Rumah Kontrak / Rumah Orang Tua </option>
                                                    <option value="Kost Sendiri"> Kost Sendiri </option>
                                                    <option value="Kost Sekeluarga"> Kost Sekeluarga </option>
                                                    <option value="Rumah Saudara"> Rumah Saudara </option>
                                                    <option value="Rumah Mertua"> Rumah Mertua </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Provinsi Domisili</label>
                                                <select class="form-control input-sm" id="id_provdom" name="id_provdom">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Kota Domisili</label>
                                                <select class="form-control input-sm" id="id_kotadom" name="id_kotadom">
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Kecamatan Domisili</label>
                                                <select class="form-control input-sm" id="id_kecdom" name="id_kecdom">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Kel/Desa Domisili</label>
                                                <select class="form-control input-sm" id="id_desadom" name="id_desadom">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label for="inputdept" >RT</label>
                                                <input type="text" class="form-control input-sm" id="rtdom" name="rtdom" maxlength="4" placeholder="Input RT Domisili">
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="inputdept" >RW</label>
                                                <input type="text" class="form-control input-sm" id="rwdom" name="rwdom" maxlength="4" placeholder="Input RW Domisili" >
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="inputdept" >Kodepos</label>
                                                <input type="text" class="form-control input-sm" id="id_posdom" name="id_posdom" maxlength="10" placeholder="Kodepos">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Alamat Domisili</label>
                                                <textarea type="text" class="form-control input-sm"  id="desc_domisili"  name="desc_domisili"style="text-transform: uppercase"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="box box-info col-sm-12">
                                <div class="box-body" style="padding:5px;">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >No HP 1</label>
                                                <input type="text" class="form-control input-sm"  id="nohp1" name="nohp1" style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >No HP 2</label>
                                                <input type="text" class="form-control input-sm"  id="nohp2" name="nohp2" style="text-transform: uppercase" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Email Pribadi</label>
                                                <input type="text" class="form-control input-sm" id="email_self" name="email_self" style="text-transform: uppercase" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Email Perusahaan</label>
                                                <input type="text" class="form-control input-sm" id="email_office" name="email_office" style="text-transform: uppercase" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >No HP Kontak Darurat</label>
                                                <input type="text" class="form-control input-sm" id="emergency_phone" name="emergency_phone" style="text-transform: uppercase" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Hubungan Dengan Kontak Darurat</label>
                                                <input type="text" class="form-control input-sm" id="emergency_relation" name="emergency_relation" style="text-transform: uppercase" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Nama Kontak Darurat</label>
                                                <input type="text" class="form-control input-sm" id="emergency_name" name="emergency_name" style="text-transform: uppercase" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >ID Absen</label>
                                                <input type="text" class="form-control input-sm" id="idabsen" name="idabsen" style="text-transform: uppercase" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Transportasi Ke Kantor</label>
                                                <select class="form-control input-sm" id="transportasi" name="transportasi">
                                                    <option value="MOBIL/MOTOR/SEPEDA"> MOBIL/MOTOR/SEPEDA </option>
                                                    <option value="ANTAR JEMPUT KANTOR"> ANTAR JEMPUT KANTOR </option>
                                                    <option value="ANTAR JEMPUT PRIBADI"> ANTAR JEMPUT PRIBADI </option>
                                                    <option value="JALAN KAKI"> JALAN KAKI </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Shift/ Non Shift</label>
                                                <select class="form-control input-sm" id="type_office" name="type_office">
                                                    <option value="NONSHIFT"> NON SHIFT </option>
                                                    <option value="SHIFT"> SHIFT </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <style>
                            div .imagewrappere{
                                width:180px;
                                height:170px;
                                background-repeat:no-repeat;
                                background-size:cover;
                            }
                        </style>
                        <div class="col-sm-4">
                            <div class="box box-info col-sm-12">
                                <div class="box-body" style="padding:5px;">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputimage" >Profile Picture (Default Image 250x250px)</label>
                                                <!--<input type="text" class="form-control input-sm" id="cimage" name="cimage">-->
                                                <div id="wrapper" class="imagewrapper">

                                                    <div align="center" id="image-holder">
                                                        <img src="<?php echo base_url('assets/img/user.png') ?>" class="thumb-image" style="max-width: 200px; max-height: 200px;">
                                                    </div>
                                                    <input id="fileUpload" type="file" name="cimage" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box box-info col-sm-12">
                                <div class="box-body" style="padding:5px;">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >No NPWP</label>
                                                <input type="text" class="form-control input-sm" id="id_npwp" name="id_npwp">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Status PTKP</label>
                                                <select class="form-control input-sm" id="sts_ptkp" name="sts_ptkp">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Status Kawin</label>
                                                <select class="form-control input-sm" id="sts_kawin" name="sts_kawin">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Pendidikan Terakhir</label>
                                                <select class="form-control input-sm" id="id_jenjang" name="id_jenjang">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Nama/Institusi Pendidikan Terakhir</label>
                                                <input type="text" class="form-control input-sm" id="pendidikan_terakhir" name="pendidikan_terakhir"  style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Jurusan</label>
                                                <input type="text" class="form-control input-sm" id="jurusan" name="jurusan"  style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >IPK Terakhir</label>
                                                <input type="text" class="form-control input-sm" id="ipk" name="ipk"  style="text-transform: uppercase" maxlength="6">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Tahun Lulus</label>
                                                <input type="text" class="form-control input-sm" id="lulus_tahun" name="lulus_tahun"  style="text-transform: uppercase" maxlength="6">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="box box-info col-sm-12">
                                <div class="box-body" style="padding:5px;">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Daftar Bank</label>
                                                <select class="form-control input-sm" id="id_bank" name="id_bank">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >No Rekening Pribadi</label>
                                                <input type="text" class="form-control input-sm"  id="id_rekening" name="id_rekening">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >No Bpjs Kesehatan</label>
                                                <input type="text" class="form-control input-sm" id="id_bpjs_kes" name="id_bpjs_kes" style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Cabang BPJS Kesehatan</label>
                                                <input type="text" class="form-control input-sm"  id="cab_bpjs_kes" name="cab_bpjs_kes" style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >NO Bpjs Ketenagakerjaan</label>
                                                <input type="text" class="form-control input-sm"  id="id_bpjs_naker" name="id_bpjs_naker" style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Cabang BPJS Ketenagakerjaan</label>
                                                <input type="text" class="form-control input-sm"  id="cab_bpjs_naker" name="cab_bpjs_naker" style="text-transform: uppercase">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Nama Ibu Kandung</label>
                                                <input type="text" class="form-control input-sm"  id="nmibukandung" name="nmibukandung">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Nama Ayah Kandung</label>
                                                <input type="text" class="form-control input-sm"  id="nmayahkandung" name="nmayahkandung">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputdept" >Punya Penyakit Bawaan ?</label>
                                                <select class="form-control input-sm" id="penyakitbawaan" name="penyakitbawaan" placeholder="Pilih Penyakit Bawaan">
                                                    <option value="TIDAK ADA"> TIDAK ADA </option>
                                                    <option value="Jantung / Cardio Veskular"> Jantung / Cardio Veskular </option>
                                                    <option value="TBC"> TBC </option>
                                                    <option value="asam urat"> asam urat </option>
                                                    <option value="Typhus lebih dari 3 kali"> Typhus lebih dari 3 kali </option>
                                                    <option value="Jantung / Cardio Veskular, Diabetes / Kencing Manis"> Jantung / Cardio Veskular, Diabetes / Kencing Manis </option>
                                                    <option value="Asma"> Asma </option>
                                                    <option value="Kanker"> Kanker </option>
                                                    <option value="Diabetes / Kencing Manis"> Diabetes / Kencing Manis </option>
                                                    <option value="Darah Tinggi / Hipertensi"> Darah Tinggi / Hipertensi </option>
                                                    <option value="Osteoporosis"> Osteoporosis </option>
                                                    <option value="Vertigo"> Vertigo </option>
                                                    <option value="Rabun Mata"> Rabun Mata </option>
                                                    <option value="Buta Warna"> Buta Warna </option>
                                                </select>
                                            </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <label for="inputdept" >Jika ada 1 keluarga yg memiliki penyakit bawaan (isi di bawah)</label>
                                                    <input type="text" class="form-control input-sm"  id="nmkeluargapenyakitbawaan" name="nmkeluargapenyakitbawaan">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <label for="inputdept" >Nakes</label>
                                                    <input type="text" class="form-control input-sm"  id="nakes" name="nakes">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <label for="inputdept" >Status Vaksin Covid</label>
                                                    <select class="form-control input-sm" id="area_covid" name="area_covid" placeholder="Input Karyawan Telah Vaksin/Belum">
                                                        <option value="BELUM"> BELUM </option>
                                                        <option value="VAKSIN1"> VAKSIN1 </option>
                                                        <option value="VAKSIN2"> VAKSIN2 </option>
                                                    </select>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">

                        </div>
                    </div>
                </div>
            </form>

        <!-- /.tab-pane -->
        </div>
        <div class="tab-pane" id="tab_2">
                <div class="box">
                    <div class="box-header">
                        <div class="">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#pendidikanf" class="btn btn-primary" style="margin:0px; color:#ffffff;"><i class="fa fa-plus"></i> Input</a>
                        </div>

                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive" style='overflow-x:scroll;'>
                        <table id="example1" class="table table-bordered table-striped" >
                            <thead>
                            <tr>
                                <th>No.</th>
                                <!--<th>NIK</th>
                                <th>Nama Karyawan</th>-->

                                <th>Action</th>
                                <th>ID</th>
                                <th>JENJANG</th>
                                <th>NAMA STUDY</th>
                                <th>JURUSAN</th>
                                <th>START</th>
                                <th>END</th>
                                <th>IPK</th>
                                <th>ALAMAT</th>
                                <th>STATUS</th>
                                <th>KETERANGAN</th>
                                <th>InputBy</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php $no=0; foreach($riwayat_pendidikan as $rpf): $no++;?>
                                <tr>
                                    <td width="2%"><?php echo $no;?></td>
                                    <td width="10%">
                                        <a class="btn btn-sm btn-success" href="<?php if(!empty($rpf->attachment)) { echo base_url('/assets/img/riwayat_pendidikan'.'/'.trim($rpf->attachment)); } else { echo '#'; } ?>"  title="Detail Attachment" <?php if(!empty($rpf->attachment)) { ?> target="_blank" <?php } ?>><i class="fa fa-eye"></i> </a>
                                        <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#upd_pendidikanf<?= $rpf->id ?>"title="Ubah Data"><i class="fa fa-gear"></i> </a>
                                        <a class="btn btn-sm btn-danger"  onclick="return confirm('Anda Yakin Menghapus Data Ini?')" href="<?= base_url('/trans/karyawan/save_pendidikanf_delete'.'/?id='.trim($rpf->id).'&nik='.trim($rpf->nik))?>"  title="Hapus Data" ><i class="fa fa-trash-o"></i> </a>
                                    </td>
                                    <td><?php echo $rpf->kdpendidikan;?></td>
                                    <td><?php echo $rpf->nmpendidikan;?></td>
                                    <td><?php echo $rpf->nmstudy;?></td>
                                    <td><?php echo $rpf->jurusan;?></td>
                                    <td><?php echo $rpf->tahun_masuk;?></td>
                                    <td><?php echo $rpf->tahun_keluar;?></td>
                                    <td><?php echo $rpf->ipk;?></td>
                                    <td><?php echo $rpf->alamatpendidikan;?></td>
                                    <td><?php echo $rpf->status;?></td>
                                    <td><?php echo $rpf->description;?></td>
                                    <td><?php echo $rpf->inputby;?></td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
        </div>
        <div class="tab-pane" id="tab_3">
            <div class="box">
                <div class="box-header">
                    <div class="">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#kartupendukung" class="btn btn-primary" style="margin:0px; color:#ffffff;"><i class="fa fa-plus"></i> Input Kartu Pendukung</a>
                    </div>

                </div><!-- /.box-header -->
                <div class="box-body table-responsive" style='overflow-x:scroll;'>
                    <table id="example2" class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th>No.</th>
                            <!--<th>NIK</th>
                            <th>Nama Karyawan</th>-->
                            <th>Action</th>
                            <th>KODEJENIS</th>
                            <th>ID</th>
                            <th>NAMA KARTU</th>
                            <th>KETERANGAN</th>
                            <th>STATUS</th>
                            <th>InputBy</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php $no=0; foreach($list_kartupendukung as $lkp): $no++;?>
                            <tr>
                                <td width="2%"><?php echo $no;?></td>
                                <td width="10%">
                                    <a class="btn btn-sm btn-success" href="<?php if(!empty($lkp->attachment)) { echo base_url('/assets/img/kartupendukung'.'/'.trim($lkp->attachment)); } else { echo '#'; } ?>"  title="Detail Attachment" <?php if(!empty($lkp->attachment)) { ?> target="_blank" <?php } ?>><i class="fa fa-eye"></i> </a>
                                    <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#upd_kartupendukung<?= $lkp->id ?>"title="Ubah Data"><i class="fa fa-gear"></i> </a>
                                    <a class="btn btn-sm btn-danger"  onclick="return confirm('Anda Yakin Menghapus Data Ini?')" href="<?= base_url('/trans/karyawan/save_kartupendukung_delete'.'/?id='.trim($lkp->id).'&nik='.trim($lkp->nik))?>"  title="Hapus Data" ><i class="fa fa-trash-o"></i> </a>
                                </td>
                                <td><?php echo $lkp->kdkartu;?></td>
                                <td><?php echo $lkp->idkartu;?></td>
                                <td><?php echo $lkp->nmkartu;?></td>
                                <td><?php echo $lkp->description;?></td>
                                <td><?php echo $lkp->status;?></td>
                                <td><?php echo $lkp->inputby;?></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
        <div class="tab-pane" id="tab_4">



            <div class="row" >
                <!-- START OF OJT -->
                <div class="col-md-6">
                    <div class="box box-success" >
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $title_abscut; ?></h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="t_abscut" class="display nowrap table table-striped no-margin" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th width="1%">No.</th>
                                        <th>Dokumen</th>
                                        <th width="50%">Nama</th>
                                        <th>Tanggal</th>
                                        <th>Durasi Menit</th>
                                        <th width="20%">Jenis</th>
                                        <th width="20%">Keterangan</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $no=0; foreach($list_abscut as $lu): $no++;?>
                                        <tr>
                                            <td width="1%"><?php echo $no;?></td>
                                            <td><?php echo trim($lu->docno);?></td>
                                            <td width="50%"><?php echo trim($lu->nmlengkap);?></td>
                                            <td><?php echo trim($lu->tglawal1);?></td>
                                            <td><?php echo str_replace('.',',',$lu->durasimenit);?></td>
                                            <td width="20%"><span class="label label-success"><?php echo trim($lu->nmtypecuti);?></span></td>
                                            <td width="20%"><?php echo trim($lu->description);?></td>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                        </div>
                        <!-- /.box-footer -->
                    </div>
                </div>
                <!-- END OF OJT -->

                <!--START OF KONTRAK-->
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $title_abstelat; ?></h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="t_abstel" class="display nowrap table table-striped no-margin" style="width:100%">
                                <thead>
                                <tr>
                                    <th width="1%">No.</th>
                                    <th>Dokumen</th>
                                    <th width="50%">Nama</th>
                                    <th>Tanggal</th>
                                    <th>Durasi Menit</th>
                                    <th width="20%">Jenis</th>
                                    <th width="20%">Keterangan</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_abstel as $lu): $no++;?>
                                    <tr>
                                        <td width="1%"><?php echo $no;?></td>
                                        <td><?php echo trim($lu->docno);?></td>
                                        <td width="50%"><?php echo trim($lu->nmlengkap);?></td>
                                        <td><?php echo trim($lu->tglawal1);?></td>
                                        <td><?php echo str_replace('.',',',$lu->durasimenit);?></td>
                                        <td width="20%"><span class="label label-success"><?php echo trim($lu->nmtypecuti);?></span></td>
                                        <td width="20%"><?php echo trim($lu->description);?></td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                        </div>
                        <!-- /.box-footer -->
                    </div>
                </div>
                <!--END OF KONTRAK-->
            </div>
        </div>
        <div class="tab-pane" id="tab_5">
            <div class="row" >
                <!-- GRID COVID-->
                <div class="col-md-12">
                    <div class="box box-success" >
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $title_covidtrack; ?></h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="t_covidtrack"  class="display nowrap table table-striped no-margin" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th width="1%">No.</th>
                                        <th width="20%">Nama</th>
                                        <th>Tanggal</th>
                                        <th width="100%">Dokumen</th>
                                        <th width="100%">Jenis</th>
                                        <th width="20%">Keterangan</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $no=0; foreach($list_covidtrack as $lu): $no++;?>
                                        <tr>
                                            <td width="1%"><?php echo $no;?></td>
                                            <td width="20%"><?php echo trim($lu->nmlengkap);?></td>
                                            <td><?php echo trim($lu->tglawal1);?></td>
                                            <td><?php echo trim($lu->docno);?></td>
                                            <td width="20%"><span class="label label-success"><?php echo trim($lu->nmtypecuti);?></span></td>
                                            <td width="20%"><?php echo trim($lu->description);?></td>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                        </div>
                        <!-- /.box-footer -->
                    </div>
                </div>
                <!-- END OF OJT -->
            </div>
        </div>
    <!-- /.tab-content -->
</div>
<!-- nav-tabs-custom -->
<!-- /.col -->

<!--KUMPULAN MODALNYA DI SINI-->
<div class="modal fade" id="pendidikanf" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Input Riwayat Pendidikan Formal</h4>
            </div>
            <form method="post" enctype="multipart/form-data" action="<?php echo base_url('trans/karyawan/save_pendidikanf') ?>">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Kode Pendidikan</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" id="pend_inp_kdpendidikan" name="pend_inp_kdpendidikan" style="width: 100%">
                            <?php foreach($m_pendidikan as $lspen){?>
                                <option value="<?php echo trim($lspen->kdpendidikan);?>" ><?php echo $lspen->nmpendidikan;?></option>
                            <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Nama Pendidikan/Sekolah/Universitas</label>
                        <div class="col-sm-9">
                            <input type="text" name="pend_inp_nmstudy" id="pend_inp_nmstudy" class="form-control input-sm"  style="text-transform: uppercase" required>
                            <input type="hidden" value="<? 'INPUT' ?>" name="pend_inp_type" id="pend_inp_type" class="form-control input-sm">
                            <input type="hidden" value="<?= trim($docno) ?>"name="pend_inp_nik" id="pend_inp_nik" class="form-control input-sm">
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Alamat Pendidikan/Sekolah/Universitas</label>
                        <div class="col-sm-9">
                            <input type="text" name="pend_inp_alamatpendidikan" id="pend_inp_alamatpendidikan" class="form-control input-sm" style="text-transform: uppercase"  required>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Jurusan</label>
                        <div class="col-sm-9">
                            <input type="text" name="pend_inp_jurusan" id="pend_inp_jurusan" class="form-control input-sm"  style="text-transform: uppercase">
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tahun Masuk</label>
                        <div class="col-sm-9">
                            <input type="text" name="pend_inp_tahun_masuk" id="pend_inp_tahun_masuk" class="form-control input-sm yeardatepicker ratakanan" required>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tahun Lulus</label>
                        <div class="col-sm-9">
                            <input type="text" name="pend_inp_tahun_keluar" id="pend_inp_tahun_keluar" class="form-control input-sm yeardatepicker ratakanan">
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">IPK</label>
                        <div class="col-sm-9">
                            <input type="text" name="pend_inp_ipk" id="pend_inp_ipk" class="form-control input-sm"  style="text-transform: uppercase" required>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Keterangan</label>
                        <div class="col-sm-9">
                            <input type="text" name="pend_inp_description" id="pend_inp_description" class="form-control input-sm" style="text-transform: uppercase"  required>
                        </div>
                    </div>
                    <div class="form-group input-sm">
                        <label class="label-form col-sm-3" for="exampleInputFile">File Image Pendukung</label>
                        <input  class="col-sm-9" type="file" id="pend_inp_attachment" name="pend_inp_attachment" >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Input</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $no=0; foreach($riwayat_pendidikan as $uprpf): $no++;?>
    <div class="modal fade" id="upd_pendidikanf<?= $uprpf->id ?>" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Riwayat Pendidikan Formal</h4>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo base_url('trans/karyawan/save_pendidikanf_update') ?>">
                    <div class="modal-body">
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Kode Pendidikan</label>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" id="pend_upd_kdpendidikan" name="pend_upd_kdpendidikan" style="width: 100%">
                                    <?php foreach($m_pendidikan as $lspen){?>
                                        <option <?php if (trim($lspen->kdpendidikan)==trim($uprpf->kdpendidikan)) { echo 'selected';}?> value="<?php echo trim($lspen->kdpendidikan);?>" ><?php echo $lspen->nmpendidikan;?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Nama Pendidikan/Sekolah/Universitas</label>
                            <div class="col-sm-9">
                                <input type="text" value="<?= $uprpf->nmstudy ?>" name="pend_upd_nmstudy" id="pend_upd_nmstudy" class="form-control input-sm"  style="text-transform: uppercase" required>
                                <input type="hidden" value="<?= $uprpf->id ?>" name="pend_upd_id" id="pend_upd_id" class="form-control input-sm">
                                <input type="hidden" value="<? 'UPDATE' ?>" name="pend_upd_type" id="pend_upd_type" class="form-control input-sm">
                                <input type="hidden" value="<?= trim($docno) ?>"name="pend_upd_nik" id="pend_upd_nik" class="form-control input-sm">
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Alamat Pendidikan/Sekolah/Universitas</label>
                            <div class="col-sm-9">
                                <input type="text" value="<?= $uprpf->alamatpendidikan ?>"  name="pend_upd_alamatpendidikan" id="pend_upd_alamatpendidikan" class="form-control input-sm" style="text-transform: uppercase"  required>
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Jurusan</label>
                            <div class="col-sm-9">
                                <input type="text" value="<?= $uprpf->jurusan ?>" name="pend_upd_jurusan" id="pend_upd_jurusan" class="form-control input-sm"  style="text-transform: uppercase">
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Tahun Masuk</label>
                            <div class="col-sm-9">
                                <input type="text" value="<?= $uprpf->tahun_masuk ?>"  name="pend_upd_tahun_masuk" id="pend_upd_tahun_masuk" class="form-control input-sm yeardatepicker ratakanan" required>
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Tahun Lulus</label>
                            <div class="col-sm-9">
                                <input type="text" value="<?= $uprpf->tahun_keluar ?>"  name="pend_upd_tahun_keluar" id="pend_upd_tahun_keluar" class="form-control input-sm yeardatepicker ratakanan">
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">IPK</label>
                            <div class="col-sm-9">
                                <input type="text" value="<?= $uprpf->ipk ?>"  name="pend_upd_ipk" id="pend_upd_ipk" class="form-control input-sm"  style="text-transform: uppercase" required>
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Keterangan</label>
                            <div class="col-sm-9">
                                <input type="text" value="<?= $uprpf->description ?>"  name="pend_upd_description" id="pend_upd_description" class="form-control input-sm" style="text-transform: uppercase"  required>
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" id="pend_upd_status" name="pend_upd_status" style="width: 100%">
                                <option <?php if ('UNACTIVE'==trim($uprpf->status)) { echo 'selected';}?> value="<?php echo 'UNACTIVE';?>" ><?php echo 'UNACTIVE';?></option>
                                <option <?php if ('ACTIVE'==trim($uprpf->status)) { echo 'selected';}?> value="<?php echo 'ACTIVE';?>" ><?php echo 'ACTIVE';?></option>

                                </select>
                            </div>
                        </div>
                        <div class="form-group input-sm">
                            <label class="label-form col-sm-3" for="exampleInputFile">File Image Pendukung</label>
                            <input  class="col-sm-9" type="file" id="pend_upd_attachment" name="pend_upd_attachment" >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<div class="modal fade" id="kartupendukung" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Input Kartu Pendukung</h4>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo base_url('trans/karyawan/save_kartupendukung') ?>">
                    <div class="modal-body">
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Kode & Jenis Kartu</label>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" id="kartupendukung_inp_kdkartu" name="kartupendukung_inp_kdkartu" style="width: 100%">
                                    <?php foreach($m_kartupendukung as $mkp){?>
                                        <option value="<?php echo trim($mkp->kdkartu);?>" ><?php echo $mkp->kdkartu.'  ||  '.$mkp->nmkartu;?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">ID KARTU/NOMOR KARTU</label>
                            <div class="col-sm-9">
                                <input type="text" name="kartupendukung_inp_idkartu" id="kartupendukung_inp_idkartu" class="form-control input-sm" style="text-transform: uppercase"  required>
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Keterangan</label>
                            <div class="col-sm-9">
                                <input type="hidden" value="<? 'INPUT' ?>" name="kartupendukung_inp_type" id="kartupendukung_inp_type" class="form-control input-sm">
                                <input type="hidden" value="<?= trim($docno) ?>"name="kartupendukung_inp_nik" id="kartupendukung_inp_nik" class="form-control input-sm">
                                <input type="text" name="kartupendukung_inp_description" id="kartupendukung_inp_description" class="form-control input-sm" style="text-transform: uppercase"  required>
                            </div>
                        </div>
                        <div class="form-group input-sm">
                            <label class="label-form col-sm-3" for="exampleInputFile">File Image Pendukung</label>
                            <input  class="col-sm-9" type="file" id="kartupendukung_inp_attachment" name="kartupendukung_inp_attachment" >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Input</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $no=0; foreach($list_kartupendukung as $lkpz): $no++;?>
    <div class="modal fade" id="upd_kartupendukung<?= $lkpz->id ?>" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Kartu Pendukung</h4>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo base_url('trans/karyawan/save_kartupendukung_update') ?>">
                    <div class="modal-body">
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Kode & Jenis Kartu</label>
                            <div class="col-sm-9">
                                <input type="hidden" value="<?= trim($lkpz->kdkartu) ?>" name="kartupendukung_upd_kdkartu" id="kartupendukung_upd_kdkartu" class="form-control input-sm">
                                <input type="hidden" value="<?= trim($lkpz->id) ?>" name="kartupendukung_upd_id" id="kartupendukung_upd_id" class="form-control input-sm">
                                <select class="form-control input-sm" style="width: 100%" disabled>
                                    <?php foreach($m_kartupendukung as $mkp){?>
                                        <option <?php if(trim($mkp->kdkartu)===trim($lkpz->kdkartu)) { echo 'selected' ;} ?> value="<?php echo trim($mkp->kdkartu);?>" ><?php echo $mkp->kdkartu.'  ||  '.$mkp->nmkartu;?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">ID KARTU/NOMOR KARTU</label>
                            <div class="col-sm-9">
                                <input type="text" value="<?=trim($lkpz->idkartu)?>" name="kartupendukung_upd_idkartu" id="kartupendukung_upd_idkartu" class="form-control input-sm" style="text-transform: uppercase"  required>
                            </div>
                        </div>
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Keterangan</label>
                            <div class="col-sm-9">
                                <input type="hidden" value="<?= 'INPUT' ?>" name="kartupendukung_upd_type" id="kartupendukung_upd_type" class="form-control input-sm">
                                <input type="hidden" value="<?= trim($docno) ?>"name="kartupendukung_upd_nik" id="kartupendukung_upd_nik" class="form-control input-sm">
                                <input type="text"  value="<?=trim($lkpz->description)?>" name="kartupendukung_upd_description" id="kartupendukung_upd_description" class="form-control input-sm" style="text-transform: uppercase"  required>
                            </div>
                        </div>
                        <div class="form-group input-sm">
                            <label class="label-form col-sm-3" for="exampleInputFile">File Image Pendukung</label>
                            <input  class="col-sm-9" type="file" id="kartupendukung_upd_attachment" name="kartupendukung_upd_attachment" >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Ubah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<!--KUMPULAN MODALNYA DI SINI-->




<script type="text/javascript" src="<?= base_url('assets/pagejs/master/karyawan.js') ?>"></script>
<script type="text/javascript">
    $('#pend_inp_kdpendidikan').select2();
    $('#kartupendukung_inp_kdkartu').select2();
    $(".yeardatepicker").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });
    $("#t_abscut").dataTable({
        scrollX: true,
        pageLength: 5,
        lengthMenu: [[5, 25, 50, -1], [5, 25, 50, "All"]],
        language: {
            <?php echo $this->fiky_encryption->constant('datatable_language'); ?>
        }
    });
    $("#t_abstel").dataTable({
        scrollX: true,
        pageLength: 5,
        lengthMenu: [[5, 25, 50, -1], [5, 25, 50, "All"]],
        language: {
            <?php echo $this->fiky_encryption->constant('datatable_language'); ?>
        }
    });
    $("#t_covidtrack").dataTable();

    $("#fileUpload").on('change', function () {

        var imgPath = $(this)[0].value;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {

                var image_holder = $("#image-holder");
                image_holder.empty();

                var reader = new FileReader();
                reader.onload = function (e) {
                    $("<img />", {
                        "src": e.target.result,
                        "class": "thumb-image",
                        "style": "max-width: 250px; max-height: 250px;"
                    }).appendTo(image_holder);

                }
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);
            } else {
                alert("This browser does not support FileReader.");
            }
        } else {
            alert("Pls select only images");
        }
    });
</script>

