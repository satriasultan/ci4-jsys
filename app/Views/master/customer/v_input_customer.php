<style>
    
    .section-block {
        background-color: #e8e8e8;
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

    .section-divider {
        height: 1px;
        background: linear-gradient(
            to right,
            #007bff 0%,
            #cfe2ff 30%,
            #e8e8e8 100%
        );
        margin: 24px 0;
        border: none;
    }

    /* Tabs */
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 18px;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active {
        color: #007bff;
        border-bottom: 3px solid #007bff;
        background-color: transparent;
    }

    .nav-tabs .nav-link:hover {
        color: #0056b3;
    }

    .tab-content {
        margin-top: 20px;
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
                <h3 class="card-title"><?=  $typeTitle = ($type == 'INPUT') ? 'Input' : ($type == 'UPDATE' ? 'Edit' : 'Detail'); ?> Data Master Customer</h3>            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('#')?>" method="post" id="formInputCustomer" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="customerTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-bs-toggle="tab"
                            href="#general" role="tab">
                                <i class="fa fa-info-circle"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="npwp-tab" data-bs-toggle="tab"
                            href="#npwptab" role="tab">
                                <i class="fa fa-file-text"></i> NPWP
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="others-tab" data-bs-toggle="tab"
                            href="#others" role="tab">
                                <i class="fa fa-ellipsis-h"></i> Others
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="customerTabContent">

                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-address-card"></i> Informasi Customer
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="kdcustomer">Kode Customer</label>
                                            <input type="hidden" id="type" name="type" value="<?= $type ?>" autocomplete="off">
                                            <input type="hidden" id="id" name="id" value="<?= $id ?>" autocomplete="off">
                                            <input type="text" name="kdcustomer" class="form-control" id="kdcustomer" maxlength="60" <?= $type == 'UPDATE' ? 'disabled' : '' ?> placeholder="Kode Customer" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nmcustomer">Nama Customer</label>
                                            <input type="text" name="nmcustomer" class="form-control" id="nmcustomer" maxlength="250" placeholder="Nama Customer" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                        <label class="">Hold</label>
                                            <select name="chold" id="chold" class="form-control inform" style="text-transform:uppercase;" >
                                                <!--option value="">--Pilih Hold--</option-->
                                                <option value="NO"> NO </option>
                                                <option value="YES">YES </option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <!-- <div class="section-divider"></div> -->
                                    <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="alamat">Alamat </label>
                                            <textarea type="text" name="alamat" class="form-control" row="3" id="alamat" placeholder="Alamat" style="text-transform:uppercase;"></textarea>
                                        </div>
                                    </div> -->
                                    <!-- <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="idkota">Kota</label>
                                            <select name="idkota" id="idkota" class="form-control" required>
                                            </select>
                                        </div>
                                    </div> -->
                                    <!-- <div class="row"> -->  
                                        <!--  +++++++++++++++++++++++ KANTOR ++++++++++++++++++++++++ -->
                                    <div class="col-md-12"><hr></div>
                                    <div class="col-md-2">
                                        <label for="inputdept" >Provinsi Kantor</label>
                                        <select class="form-control input-sm" id="provinsi_kantor" name="provinsi_kantor">
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="inputdept" >Kota Kantor</label>
                                        <select class="form-control input-sm"  id="kota_kantor" name="kota_kantor" >
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="inputdept" >Kecamatan Kantor</label>
                                        <select class="form-control input-sm"  id="kec_kantor" name="kec_kantor">
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="inputdept" >Kel/Desa Kantor</label>
                                        <select class="form-control input-sm"  id="kel_kantor" name="kel_kantor">
                                            </select>
                                        </div>
                                    <div class="col-md-3">
                                        <label>Alamat Kantor</label>
                                        <textarea name="alamat_kantor" class="form-control" style="text-transform: uppercase;" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <label>Kode Pos Kantor</label>
                                        <input type="text" name="kodepos_kantor" class="form-control">
                                    </div>
                                    <div class="col-md-12"><hr></div>
                                    <!--  +++++++++++++++++++++++ END KANTOR ++++++++++++++++++++++++ -->
                                    <!--  +++++++++++++++++++++++ PENGIRIMAN ++++++++++++++++++++++++ -->
                                    <div class="col-md-12">
                                        <div class="form-check mt-1 mb-2">
                                            <input type="checkbox" class="form-check-input" id="copy_alamat_pengiriman" onclick="copyAlamatKantorKePengiriman()">
                                            <label class="form-check-label" for="copy_alamat_pengiriman">
                                                Alamat Pengiriman sama dengan Alamat Kantor
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="inputdept" >Provinsi Pengiriman</label>
                                        <select class="form-control input-sm" id="provinsi_pengiriman" name="provinsi_pengiriman">
                                    </select>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="inputdept" >Kota Pengiriman</label>
                                        <select class="form-control input-sm"  id="kota_pengiriman" name="kota_pengiriman" >
                                        </select>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="inputdept" >Kecamatan Pengiriman</label>
                                        <select class="form-control input-sm"  id="kec_pengiriman" name="kec_pengiriman">
                                        </select>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="inputdept" >Kel/Desa Pengiriman</label>
                                        <select class="form-control input-sm"  id="kel_pengiriman" name="kel_pengiriman">
                                        </select>
                                    </div>
                                    <div class="col-md-3 mt-2">
                                        <label>Alamat Pengiriman</label>
                                        <textarea name="alamat_pengiriman" style="text-transform: uppercase;" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <label>Kode Pos Pengiriman</label>
                                        <input type="text" name="kodepos_pengiriman" class="form-control">
                                    </div>

                                    <div class="col-md-12"><hr></div>
                                    <!--  +++++++++++++++++++++++ END PENGIRIMAN ++++++++++++++++++++++++ -->
                                    <!--  +++++++++++++++++++++++ PENAGIHAN ++++++++++++++++++++++++ -->
                                    <div class="col-md-12">
                                        <div class="form-check mt-1 mb-2">
                                            <input type="checkbox" class="form-check-input" id="copy_alamat_penagihan" onclick="copyAlamatKantorKePenagihan()">
                                            <label class="form-check-label" for="copy_alamat_penagihan">
                                                Alamat Penagihan sama dengan Alamat Kantor
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="inputdept" >Provinsi Penagihan</label>
                                        <select class="form-control input-sm" id="provinsi_penagihan" name="provinsi_penagihan">
                                        </select>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="inputdept" >Kota Penagihan</label>
                                        <select class="form-control input-sm"  id="kota_penagihan" name="kota_penagihan" >
                                        </select>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="inputdept" >Kecamatan Penagihan</label>
                                        <select class="form-control input-sm"  id="kec_penagihan" name="kec_penagihan">
                                        </select>
                                    </div>
                                    <div class="col-md-2 mt-2">
                                        <label for="inputdept" >Kel/Desa Penagihan</label>
                                        <select class="form-control input-sm"  id="kel_penagihan" name="kel_penagihan">
                                        </select>
                                    </div>
                                    <div class="col-md-3 mt-2">
                                        <label>Alamat Penagihan</label>
                                        <textarea style="text-transform: uppercase;" name="alamat_penagihan" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <label>Kode Pos Penagihan</label>
                                        <input type="text" name="kodepos_penagihan" class="form-control">
                                    </div>
                                    <div class="col-md-12"><hr></div>
                                        <!-- <div class="col-md-12"><hr></div> -->
                                        <!--  +++++++++++++++++++++++ END OF PENAGIHAN ++++++++++++++++++++++++ -->
                                        <!-- <div class="col-md-6 mt-2">
                                            <label>Alamat 1</label>
                                            <textarea name="alamat1" class="form-control" rows="2"></textarea>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label>Alamat 2</label>
                                            <textarea name="alamat2" class="form-control" rows="2"></textarea>
                                        </div> -->
                                    <!-- </div> -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="phone">No. Telp</label>
                                            <input type="text" name="phone" class="form-control" id="phone" maxlength="20" placeholder="No. Telp" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" name="email" class="form-control" id="email" maxlength="150" placeholder="Email" style="text-transform:lowercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fax">Fax</label>
                                            <input type="text" name="fax" class="form-control" id="fax" maxlength="30" placeholder="Fax" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="npwp">NPWP</label>
                                            <input type="text" name="npwp" class="form-control" id="npwp" maxlength="50" placeholder="NPWP" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="npkp">NPKP</label>
                                            <input type="text" name="npkp" class="form-control" id="npkp" maxlength="50" placeholder="NPKP" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="plafon">Plafon</label>
                                            <input type="text"
                                                name="plafon"
                                                id="plafon"
                                                class="form-control ratakanan jtsseparator"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="jthtempo">Jatuh Tempo</label>
                                            <input type="text"
                                                name="jthtempo"
                                                id="jthtempo"
                                                class="form-control ratakanan jtsseparator"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="idmarket">Market</label>
                                            <!-- <input type="text" name="idmarket" class="form-control" id="idmarket" maxlength="50" placeholder="Market" style="text-transform:uppercase;"> -->
                                            <select name="idmarket" id="idmarket" class="form-control" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cp">Contact Person</label>
                                            <input type="text" name="cp" class="form-control" id="cp" maxlength="100" placeholder="Contact Person" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="jabatan">Jabatan</label>
                                            <input type="text" name="jabatan" class="form-control" id="jabatan" maxlength="50" placeholder="Jabatan" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <!-- Radio untuk Interngroup -->
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="coa" id="coa" value="1" <?= isset($data['coa']) && $data['coa'] == '1' ? 'checked' : '' ?> <?= $disabled ?>>
                                                <label class="form-check-label" for="coa">
                                                    COA
                                                </label>
                                            </div>
                                            
                                            <!-- Radio untuk Blacklist -->
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="blacklist" id="blacklist" value="1" <?= isset($data['blacklist']) && $data['blacklist'] == '1' ? 'checked' : '' ?> <?= $disabled ?>>
                                                <label class="form-check-label" for="blacklist">
                                                    Blacklist
                                                </label>
                                            </div>
                                        </div>
                                    </div>                            
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="Description">Keterangan</label>
                                            <textarea name="keterangan"  id="keterangan" class="form-control" rows="3" placeholder="Catatan/Informasi..."  style="text-transform:uppercase;" <?= $disabled ?>></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="npwptab" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-file-text"></i> Informasi NPWP
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="namanpwp">Nama NPWP</label>
                                            <input type="text" name="namanpwp" style="text-transform: uppercase;" id="namanpwp"
                                                class="form-control" maxlength="50">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="alamatnpwp">Alamat NPWP</label>
                                            <textarea name="alamatnpwp" style="text-transform: uppercase;" id="alamatnpwp"
                                            class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="idkotanpwp">Kota NPWP</label>
                                            <select name="idkotanpwp" id="idkotanpwp" class="form-control" required>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <!-- <div class="section-header"> -->
                                    <div class="" style="font-weight: bold;
                                        color: #007bff;
                                        margin-bottom: 20px;
                                        font-size: 1.1rem;">
                                        <i class="fa fa-percent"></i>Coretax    
                                    </div>
                                    <!-- </div> -->
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="idcoretax">Jenis ID</label>
                                            <input type="text" style="text-transform: uppercase;" name="idcoretax" id="idcoretax"
                                                class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="idtku">ID TKU</label>
                                            <input type="text" style="text-transform: uppercase;" name="idtku" id="idtku"
                                                class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="docnopembeli">No Doc Pembeli</label>
                                            <input type="text" style="text-transform: uppercase;" name="docnopembeli" id="docnopembeli"
                                                class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="others" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-ellipsis-h"></i> Informasi Lain
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="grade">Grade</label>
                                            <select name="grade" id="grade" class="form-control" required>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="salesman">Salesman</label>
                                            <select name="salesman" id="salesman" class="form-control" required>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="kolektor">Kolektor</label>
                                            <select name="kolektor" id="kolektor" class="form-control" required>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label for="koderetur">Kode Retur</label>
                                            <input type="text" style="text-transform: uppercase;" name="koderetur" id="koderetur"
                                                class="form-control" maxlength="3">
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="margin-top: 25px;">
                                        <div class="form-group">
                                            <!-- Radio untuk Interngroup -->
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="isfaktur" id="isfaktur" value="1" <?= isset($data['isfaktur']) && $data['isfaktur'] == '1' ? 'checked' : '' ?> <?= $disabled ?>>
                                                <label class="form-check-label" for="isfaktur">
                                                    Customer ini dibuatkan faktur pajak
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="harifakturin">Hari Faktur Masuk</label>
                                            <select name="harifakturin[]" id="harifakturin" class="form-control" multiple required>
                                                <option value="MINGGU">MINGGU</option>
                                                <option value="SENIN">SENIN</option>
                                                <option value="SELASA">SELASA</option>
                                                <option value="RABU">RABU</option>
                                                <option value="KAMIS">KAMIS</option>
                                                <option value="JUMAT">JUMAT</option>
                                                <option value="SABTU">SABTU</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="haripenagihan">Hari Penagihan</label>
                                            <select name="haripenagihan[]" id="haripenagihan" class="form-control" multiple required>
                                                <option value="MINGGU">MINGGU</option>
                                                <option value="SENIN">SENIN</option>
                                                <option value="SELASA">SELASA</option>
                                                <option value="RABU">RABU</option>
                                                <option value="KAMIS">KAMIS</option>
                                                <option value="JUMAT">JUMAT</option>
                                                <option value="SABTU">SABTU</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="haripembayaran">Hari Pembayaran</label>
                                            <select name="haripembayaran[]" id="haripembayaran" class="form-control" multiple required>
                                                <option value="MINGGU">MINGGU</option>
                                                <option value="SENIN">SENIN</option>
                                                <option value="SELASA">SELASA</option>
                                                <option value="RABU">RABU</option>
                                                <option value="KAMIS">KAMIS</option>
                                                <option value="JUMAT">JUMAT</option>
                                                <option value="SABTU">SABTU</option>
                                            </select>
                                        </div>
                                    </div>
 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="defaultfor">Default For</label>
                                            <select name="defaultfor" id="defaultfor" class="form-control" required>
                                                <option value="NONE" selected>NONE</option>
                                                <option value="PENJUALAN">PENJUALAN</option>
                                                <option value="PENJUALAN FO">PENJUALAN FO</option>
                                                <option value="SERVICE">SERVICE</option>
                                            </select>
                                        </div>
                                    </div>   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('master/data/customer') ?>" class="btn btn-default">Kembali</a>
                    <button id="btnSave" onclick="saveInputCustomer();" class="btn btn-primary float-right">Submit</button>
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





<script type="application/javascript" src="<?= base_url('assets/pagejs/master/customer/customer.js') ?>"></script>
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