<?php
/*
 * Author: Fiky Ashariza
 * Create Date: 3/23/21, 8:06 AM
 * Path Directory: v_input_karyawan.php
 */


?>
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
<form action="<?php echo base_url('#')?>" method="post" id="formInputKaryawan" enctype="multipart/form-data" role="form">
    <div class="col-lg-12">
        <h3 ALIGN="center"> <?php echo $type ;?> KARYAWAN (OS) <?php echo $docno ;?></h3>
    </div>
<div class="row">
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
                                <input type="hidden" class="form-control input-sm" id="nik" name="nik" autocomplete="off" style="text-transform: uppercase" >
                                <input type="text" class="form-control input-sm" id="nikupdated" name="nikupdated" autocomplete="off" style="text-transform: uppercase" >
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
                                <label for="inputnama form-group">Tempat Lahir</label>
                                <input type="text" class="form-control input-sm" id="alamatlahir" name="alamatlahir" style="text-transform: uppercase">
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
                                <label for="inputatasan2" >Atasan 3</label>
                                <select class="form-control input-sm"  id="nikatasan3" name="nikatasan3">
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
                                <div class="checkbox">
                                    <label for="inputdept">
                                        <input type="checkbox" id="checkboxdomisili" name="checkboxdomisili">
                                        Sama Dengan Alamat KTP
                                    </label>
                                </div>
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
                                <label for="inputdept" >Nama Pasangan</label>
                                <input type="text" class="form-control input-sm" id="nmpasangan" name="nmpasangan"  >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="inputdept" >Hubungan Dengan Pasangan</label>
                                <input type="text" class="form-control input-sm" id="hubunganpasangan" name="hubunganpasangan" >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="inputdept" >Pekerjaan Pasangan</label>
                                <input type="text" class="form-control input-sm" id="pekerjaanpasangan" name="pekerjaanpasangan" >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="inputdept" >No HP Pasangan</label>
                                <input type="text" class="form-control input-sm" id="contactpasangan" name="contactpasangan" style="text-transform: uppercase" >
                            </div>
                        </div>
                        <!-- END NAMA PASANGAN -->
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="inputdept" >Nama Kontak Darurat</label>
                                <input type="text" class="form-control input-sm" id="emergency_name" name="emergency_name" >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="inputdept" >Hubungan Dengan Kontak Darurat</label>
                                <input type="text" class="form-control input-sm" id="emergency_relation" name="emergency_relation"  >
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
                                <label for="inputdept" >No KK</label>
                                <input type="text" class="form-control input-sm" id="nokk" name="nokk">
                            </div>
                        </div>
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
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="inputdept" >Status Covid</label>
                                <select class="form-control input-sm" id="statuscovid" name="statuscovid" placeholder="Status Covid">
                                    <option value="NEGATIF"> NEGATIF </option>
                                    <option value="POSITIF"> POSITIF </option>

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
				<!--BUTTON-->
                <div class="col-sm-12">
					<div class="form-group">
                        <div class="col-sm-3" style="margin-top: 10px">
                            <a href="<?php echo base_url('trans/candidateos/')?>" type="button" class="btn btn-default pull-left"><i class="fa fa-arrow-left"></i> Kembali</a>
                        </div>
						<div class="col-sm-9" style="margin-top: 10px">
							<?php /* <a href="<?php echo base_url('trans/candidateos/edit').'/'.$lp['nik'];?>" class="btn btn-primary pull-right" onclick="return confirm('Anda Yakin Ubah Data ini?')" style="margin:10px"><i class="fa fa-gear"></i> EDIT PROFILE KARYAWAN </a> */ ?>
                            <button class="btn btn-success dropdown-toggle pull-right" style="margin:0px; color:#ffffff;" type="button" id="btnSave" onclick="saveInputKaryawan();"> Proses <i class="fa fa-arrow-right"></i></button>
						</div>
						<div class="col-sm-10"></div>
					</div>
                </div>
			<!--</div>-->

</div>
</form>

<script type="text/javascript" src="<?= base_url('assets/pagejs/master/candidateos.js') ?>"></script>
<script type="text/javascript">
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
