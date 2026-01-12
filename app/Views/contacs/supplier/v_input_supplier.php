<?php
/**
 * *
 *  * Created by PhpStorm.
 *  *  * User: FIKY-PC
 *  *  * Date: 4/29/19 1:34 PM
 *  *  * Last Modified: 12/18/16 10:51 AM.
 *  *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  *  Copyright© 2019 .All rights reserved.
 *  *
 *
 */

?>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#dateinput").datepicker();
    });
</script>
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
<?php echo $message; ?>
<div class="row">
        <div class="card col-md-12">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item"> <a id="profile_tab" class="nav-link" data-bs-toggle="tab" href="#profile" role="tab"><span class="hidden-sm-up"><i class="ti-home"></i></span> <span class="hidden-xs-down">Profile</span></a> </li>
                    <li class="nav-item"> <a id="alamat_tab" class="nav-link" data-bs-toggle="tab" href="#alamat" role="tab"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down">Alamat</span></a> </li>
                    <li class="nav-item"> <a id="bank_tab" class="nav-link" data-bs-toggle="tab" href="#bank" role="tab"><span class="hidden-sm-up"><i class="ti-email"></i></span> <span class="hidden-xs-down">Bank/Pajak</span></a> </li>
                    <li class="nav-item"> <a id="hutang_tab" class="nav-link" data-bs-toggle="tab" href="#hutang" role="tab"><span class="hidden-sm-up"><i class="ti-email"></i></span> <span class="hidden-xs-down">Hutang/Piutang</span></a> </li>
                    <?php
                    /*<li class="nav-item"> <a id="npwp_tab" class="nav-link" data-bs-toggle="tab" href="#npwp" role="tab"><span class="hidden-sm-up"><i class="ti-email"></i></span> <span class="hidden-xs-down">Foto Npwp/KTP</span></a> </li> */ ?>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content tabcontent-border">
                    <div class="tab-pane p-20" id="profile" role="tabpanel">
                        <div class="p-20">
                            <input type="hidden" value="INPUT" name="type"/>
                            <input type="hidden" name="id"/>
                            <div class="form-body">
                                <div class="row">
                                    <div class="form-group col-lg-2">
                                        <label class="control-label col-md-12">ID OTOMATIS</label>
                                        <div class="col-md-12">
                                            <select name="idotomatis" class="form-control chold inform" style="text-transform:uppercase;" >
                                                <option value="YES">YES </option>
                                                <option value="NO"> NO </option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label class="control-label col-md-12">ID</label>
                                        <div class="col-md-12">
                                            <input name="idsupplier" placeholder="ID Supplier" class="form-control inform" type="text" MAXLENGTH="20" style="text-transform:uppercase;" disabled>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label class="control-label col-md-12">Supplier Name</label>
                                        <div class="col-md-12">
                                            <input name="nmsupplier" placeholder="Supplier Name" class="form-control inform" type="text" style="text-transform:uppercase;">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label class="control-label col-md-12">Owner</label>
                                        <div class="col-md-12">
                                            <input name="ownsupplier" placeholder="Owner" class="form-control inform" type="text" style="text-transform:uppercase;">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-4">
                                        <label class="control-label col-md-12">Contact 1</label>
                                        <div class="col-md-12">
                                            <input name="contact1" placeholder="Contact Supplier 1" class="form-control inform" type="text" style="text-transform:uppercase;">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label class="control-label col-md-12">Contact 2</label>
                                        <div class="col-md-12">
                                            <input name="contact2" placeholder="Contact Supplier 2" class="form-control inform" type="text" style="text-transform:uppercase;">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label class="control-label col-md-12">Fax</label>
                                        <div class="col-md-12">
                                            <input name="fax" placeholder="Fax" class="form-control inform" type="text" style="text-transform:uppercase;">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-4">
                                        <label class="control-label col-md-12">Email</label>
                                        <div class="col-md-12">
                                            <input name="email" placeholder="Email Address" class="form-control inform" type="text" style="text-transform:uppercase;">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label class="control-label col-md-12">Supplier Group</label>
                                        <div class="col-md-12">
                                            <select name="idgroup" id="idgroup" class="form-control" style="text-transform:uppercase;" >
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-4">
                                        <label class="control-label col-md-12">Hold</label>
                                        <div class="col-md-12">
                                            <select name="chold" class="form-control chold inform" style="text-transform:uppercase;" >
                                                <!--option value="">--Pilih Hold--</option-->
                                                <option value="NO"> NO </option>
                                                <option value="YES">YES </option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane  p-20" id="alamat" role="tabpanel">
                        <div class="p-20">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="form-group col-lg-12"  style="background-color: #f6c6d7">
                                                        <label class="control-label col-md-12">Alamat Kantor</label>
                                                    </div>
                                                    <div class="form-group col-lg-6">
                                                        <label class="control-label col-md-12">Alamat</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_office" placeholder="Address" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label class="control-label col-md-12">Telepon</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_tlp_office" placeholder="Telepon" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label class="control-label col-md-12">Kota</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_city_office" placeholder="Kota" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label class="control-label col-md-12">Kode Pos</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_kodepos_office" placeholder="Kode Pos" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-lg-12"  style="background-color: #c0dff3">
                                                        <label class="control-label col-md-12">Alamat Pengiriman</label>
                                                    </div>
                                                    <div class="form-group col-lg-6">
                                                        <label class="control-label col-md-12">Alamat</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_delivery" placeholder="Address" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label class="control-label col-md-12">Telepon</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_tlp_delivery" placeholder="Telepon" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label class="control-label col-md-12">Kota</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_city_delivery" placeholder="Kota" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label class="control-label col-md-12">Kode Pos</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_kodepos_delivery" placeholder="Kode Pos" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-lg-12"  style="background-color: #d4ade8">
                                                        <label class="control-label col-md-12">Alamat Penagihan</label>
                                                    </div>
                                                    <div class="form-group col-lg-6">
                                                        <label class="control-label col-md-12">Alamat</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_tagih" placeholder="Address" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label class="control-label col-md-12">Telepon</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_tlp_tagih" placeholder="Telepon" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label class="control-label col-md-12">Kota</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_city_tagih" placeholder="Kota" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label class="control-label col-md-12">Kode Pos</label>
                                                        <div class="col-md-12">
                                                            <input name="addsupplier_kodepos_tagih" placeholder="Kode Pos" class="form-control inform" type="text" style="text-transform:uppercase;">
                                                            <span class="help-block"></span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div><!-- /.card-header -->
                                            <?php /*
                                            <div class="card-body table-responsive" style='overflow-x:scroll;'>
                                                <table id="tableHobi" class="table table-bordered table-striped" >
                                                    <thead>
                                                    <tr>
                                                        <th width="1%">No.</th>
                                                        <th width="2%">Action</th>
                                                        <th>Jenis Alamat</th>
                                                        <th>Alamat</th>
                                                        <th>Kota</th>
                                                        <th>Tagihan/Kiriman</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div><!-- /.card-body --> */ ?>
                                        </div><!-- /.card -->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane p-20" id="bank" role="tabpanel">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="control-label col-md-12">Bank</label>
                                <div class="col-md-12">
                                    <input name="banksupplier" placeholder="Bank" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-4">
                                <label class="control-label col-md-12">No Rek Bank</label>
                                <div class="col-md-12">
                                    <input name="norekbank" placeholder="Nomor Rekening" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-4">
                                <label class="control-label col-md-12">A/N Bank</label>
                                <div class="col-md-12">
                                    <input name="atsnbank" placeholder="Atasnama Bank" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label class="control-label col-md-12">NPWP</label>
                                <div class="col-md-12">
                                    <input name="npwp" placeholder="NPWP" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="control-label col-md-12">A/N NPWP</label>
                                <div class="col-md-12">
                                    <input name="atsnnpwp" placeholder="Atasnama NPWP" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label class="control-label col-md-12">NIK/NOKTP</label>
                                <div class="col-md-12">
                                    <input name="noktp" placeholder="NO KTP" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="control-label col-md-12">A/N NIK/NOKTP</label>
                                <div class="col-md-12">
                                    <input name="nmktp" placeholder="Atasnama KTP" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label class="control-label col-md-12">USER PKP</label>
                                <div class="col-md-12">
                                    <select name="pkp" class="form-control chold inform" style="text-transform:uppercase;" >
                                        <!--option value="">--Pilih Hold--</option-->
                                        <option value="NO"> NO </option>
                                        <option value="YES">YES </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="control-label col-md-12">TANGGAL PKP</label>
                                <div class="col-md-12">
                                    <input name="pkpdate" placeholder="Address Supplier" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane p-20" id="hutang" role="tabpanel">
                        <div class="row" style="background-color: #0a58ca">
                            <h3> HUTANG PERUSAHAAN </h3>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">TERMIN</label>
                                <div class="col-md-12">
                                    <select name="termin_ht" class="form-control chold inform" style="text-transform:uppercase;" >
                                        <option value="YES">YES </option>
                                        <option value="NO"> NO </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">TEMPO</label>
                                <div class="col-md-12">
                                    <input name="tempo_ht" placeholder="Tempo" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">PLAFOUND/BATAS HUTANG</label>
                                <div class="col-md-12">
                                    <input name="plafound_ht" placeholder="Tempo" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">BATAS FREKUENSI</label>
                                <div class="col-md-12">
                                    <input name="frekuensi_ht" placeholder="Tempo" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">AKUN</label>
                                <div class="col-md-12">
                                    <select name="costcenter_ht" class="form-control chold inform" style="text-transform:uppercase;" >
                                        <option value="YES">YES </option>
                                        <option value="NO"> NO </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">SALDO</label>
                                <div class="col-md-12">
                                    <input name="sld_ht" placeholder="Saldo HT" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="background-color: #ff5d75">
                            <h3> PIUTANG PERUSAHAAN </h3>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">TERMIN</label>
                                <div class="col-md-12">
                                    <select name="termin_pt" class="form-control chold inform" style="text-transform:uppercase;" >
                                        <option value="YES">YES </option>
                                        <option value="NO"> NO </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">TEMPO</label>
                                <div class="col-md-12">
                                    <input name="tempo_pt" placeholder="Tempo" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">PLAFOUND/BATAS HUTANG</label>
                                <div class="col-md-12">
                                    <input name="plafound_pt" placeholder="Tempo" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">BATAS FREKUENSI</label>
                                <div class="col-md-12">
                                    <input name="frekuensi_pt" placeholder="Tempo" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">AKUN</label>
                                <div class="col-md-12">
                                    <select name="costcenter_pt" class="form-control chold inform" style="text-transform:uppercase;" >
                                        <option value="YES">YES </option>
                                        <option value="NO"> NO </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-2">
                                <label class="control-label col-md-12">SALDO</label>
                                <div class="col-md-12">
                                    <input name="sld_pt" placeholder="Saldo Hutang" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php /*
                    <div class="tab-pane p-20" id="npwp" role="tabpanel">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="control-label col-md-12">Upload NPWP/KTP</label>
                                <div class="col-md-12">
                                    <input name="addsupplier" placeholder="Address Supplier" class="form-control inform" type="file" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-8">
                                <label class="control-label col-md-12">Alamat NPWP/KTP</label>
                                <div class="col-md-12">
                                    <input name="addsupplier" placeholder="Address Supplier" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label class="control-label col-md-12">Batas Kredit</label>
                                <div class="col-md-12">
                                    <input name="creditlimit" placeholder="Address Supplier" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-8">
                                <label class="control-label col-md-12">Alamat NPWP/KTP</label>
                                <div class="col-md-12">
                                    <input name="addsupplier" placeholder="Address Supplier" class="form-control inform" type="input" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    */ ?>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" style="margin: 2px;" class="btn btn-warning btn-md float-right" onClick="ubahThick('bahasa')"><i class="fa fa-save"></i> Simpan</button>
            </div>
        </div>
</div>
<script type="application/javascript" src="<?= base_url('assets/pagejs/contacs/msupplier.js') ?>"></script>
<script type="application/javascript">
    $(".tglrange").daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $(".tglrange").on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
    });

    $(".tglrange").on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
</script>






