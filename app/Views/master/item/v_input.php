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
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
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
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- jquery validation -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><small><?php echo ($type == "DETAIL") ? 'Detail' : (($type == "EDIT") ? 'Edit' : 'Input'); ?> Data Master Item</small></h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('#')?>" method="post" id="formInputItem" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="customerTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-bs-toggle="tab"
                            href="#general" role="tab">
                                <i class="fa fa-cubes"></i> General
                            </a>
                        </li>
                        <li class="nav-item" id="tab-umum-wrapper">
                            <a class="nav-link" id="umum-tab" data-bs-toggle="tab"
                            href="#umumtab" role="tab">
                                <i class="fa fa-info-circle"></i> Keterangan umum
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="satuan-tab" data-bs-toggle="tab"
                            href="#satuantab" role="tab">
                                <i class="fa fa-file-text"></i> Satuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="account-tab" data-bs-toggle="tab"
                            href="#accounttab" role="tab">
                                <i class="fa fa-book"></i> Account
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="customerTabContent">

                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-cubes"></i> Informasi Barang
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="hidden" id="type" name="type" value="<?= $type ?>" autocomplete="off">
                                            <input type="hidden" id="id" name="id" value="<?= $id ?>" autocomplete="off">
                                            <label for="idbarang">Item ID</label>
                                            <input type="text" name="idbarang" class="form-control" id="idbarang" maxlength="20" placeholder="Item ID" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nmbarang">Item Name</label>
                                            <input type="text" name="nmbarang" class="form-control" id="nmbarang" maxlength="60" placeholder="Item Name" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="kdtax">Kode Tax</label>
                                            <input type="text" name="kdtax" class="form-control" id="kdtax" maxlength="60" placeholder="Kode Tax" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="originalno">Original No.</label>
                                            <input type="text" name="originalno" class="form-control" id="originalno" maxlength="60" placeholder="Original No." style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="Remark">Remark</label>
                                            <textarea name="description" class="form-control" rows="3" placeholder="Enter Your Remark..."  style="text-transform:uppercase;"></textarea>
                                        </div>  
                                    </div>
                                    <div class="col-md-1" style="margin-top: 30px;">
                                        <div class="form-group">
                                            <!-- Radio untuk Interngroup -->
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="discontinue" id="discontinue" value="YES" <?= isset($data['discontinue']) && $data['discontinue'] == 'YES' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="discontinue">
                                                    Discontinue
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="margin-top: 30px;">
                                        <div class="form-group">
                                            <!-- Radio untuk Blacklist -->
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="issn" id="issn" value="YES" <?= isset($data['issn']) && $data['issn'] == 'YES' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="issn">
                                                    With S/N
                                                </label>
                                            </div>
                                        </div>
                                    </div>        
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="group">Group</label>
                                                <select name="idgroup" id="idgroup" class="form-control" placeholder="Item Group" style="text-transform:uppercase;" >
                                                </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nmbarang">Golongan</label>
                                                <select name="idgolonganbarang" id="idgolonganbarang" class="form-control" placeholder="Golongan" style="text-transform:uppercase;" >
                                                </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nmbarang">Jenis Produk</label>
                                                <select name="idjenisproduk" id="idjenisproduk" class="form-control" placeholder="Jenis Produk" style="text-transform:uppercase;" >
                                                </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nmbarang">Kelompok</label>
                                                <select name="idkelompokbarang" id="idkelompokbarang" class="form-control" placeholder="Kelompok Barang" style="text-transform:uppercase;" >
                                                </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nmbarang">Principal</label>
                                                <select name="idprincipal" id="idprincipal" class="form-control" placeholder="Principal" style="text-transform:uppercase;" >
                                                </select>
                                        </div>
                                    </div>
                                    

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">ID Barcode/SKU</label>
                                            <input type="text" name="idbarcode" class="form-control" id="idbarcode" placeholder="ID Barcode/SKU">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="expdate">MFG Date</label>
                                            <input type="text" name="mfgdate" class="form-control" id="mfgdate" placeholder="MFG Date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="expdate">Expired Date</label>
                                            <input type="text" name="expdate" class="form-control" id="expdate" placeholder="Expire Date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="maks_daystock">Max Save Life (Days)</label>
                                            <input type="text" name="maks_daystock" class="form-control jtsseparator ratakanan" id="maks_daystock" placeholder="Maks Save life (Days)">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="subunitenable">Sub Unit Enable</label>
                                            <select name="subunitenable" class="form-control" style="text-transform:uppercase;" >
                                                <!--option value="">--Pilih Hold--</option-->
                                                <option value="NO"> NO </option>
                                                <option value="YES">YES </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="setminstock">Set Minimum Stock</label>
                                            <select name="setminstock" class="form-control" style="text-transform:uppercase;" >
                                                <!--option value="">--Pilih Hold--</option-->
                                                <option value="NO"> NO </option>
                                                <option value="YES">YES </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="chold">Hold Item</label>
                                            <select name="chold" class="form-control" style="text-transform:uppercase;" >
                                                <!--option value="">--Pilih Hold--</option-->
                                                <option value="NO"> NO </option>
                                                <option value="YES">YES </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="maks_daystock">Minimum Stock(Set Minimum Will Be Appear In Report)</label>
                                            <input type="text" name="minstock" class="form-control jtsseparator ratakanan" id="minstock" placeholder="Set Your Minimum Stock" maxlength="18">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="umumtab" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-info-circle"></i> Keterangan Umum
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="Volume">Volume</label>
                                            <input type="text" name="volume" class="form-control jtsseparator ratakanan" id="volume" placeholder="Volume" maxlength="18">
                                        </div>  
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="Berat">Berat (Gram)</label>
                                            <input type="text" name="berat" class="form-control jtsseparator ratakanan" id="berat" placeholder="Berat" maxlength="18">
                                        </div>  
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="GW">Gross Weight</label>
                                            <input type="text" name="gw" class="form-control jtsseparator ratakanan" id="gw" placeholder="Gross Weight" maxlength="18">
                                        </div>  
                                    </div>
                                </div>
                                <div class="section-header">
                                    <i class="fa fa-info-circle"></i> Size
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="psize">Panjang</label>
                                            <input type="text" name="psize" class="form-control jtsseparator ratakanan" id="gw" placeholder="psize" maxlength="18">
                                        </div>  
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lsize">Lebar</label>
                                            <input type="text" name="lsize" class="form-control jtsseparator ratakanan" id="gw" placeholder="lsize" maxlength="18">
                                        </div>  
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tsize">Tinggi</label>
                                            <input type="text" name="tsize" class="form-control jtsseparator ratakanan" id="gw" placeholder="tsize" maxlength="18">
                                        </div>  
                                    </div>
                                </div>
                                <div class="section-header">
                                    <i class="fa fa-info-circle"></i> Area
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="Unit">Default Location</label>
                                            <select name="deflocation" id="deflocation" class="form-control" placeholder="Default Location" style="text-transform:uppercase;" >
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="Unit">Default Area</label>
                                            <select name="defarea" id="defarea" class="form-control" placeholder="Default Area" style="text-transform:uppercase;" >
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lokasireff">Lokasi (Reff)</label>
                                            <input type="text" name="lokasireff" class="form-control" id="lokasireff" maxlength="60" placeholder="Lokasi (Reff)" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="satuantab" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-cube"></i> Satuan
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Unit">Unit</label>
                                            <select name="unit" id="unit" class="form-control" placeholder="Unit" style="text-transform:uppercase;" >
                                                </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Unit">Sub Unit (Satuan Kecil)</label>
                                            <select name="subunit" id="subunit" class="form-control" placeholder="Sub Unit" style="text-transform:uppercase;" >
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="satuantax">Satuan Tax</label>
                                            <input type="text" name="satuantax" class="form-control" id="satuantax" maxlength="6" placeholder="Satuan Tax" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="accounttab" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-book"></i> Account
                                </div>
                                <div class="row">
                                    <div class="col-md-3 field-ppersediaan">
                                        <div class="form-group">
                                            <label>Perkiraan Persediaan</label>
                                            <select name="ppersediaan" id="ppersediaan" class="form-control"></select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 field-psj">
                                        <div class="form-group">
                                            <label>Perkiraan SJ</label>
                                            <select name="psj" id="psj" class="form-control"></select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 field-salesakun">
                                        <div class="form-group">
                                            <label>Sales Akun</label>
                                            <select name="salesakun" id="salesakun" class="form-control"></select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 field-pcogs">
                                        <div class="form-group">
                                            <label>Perkiraan COGS</label>
                                            <select name="pcogs" id="pcogs" class="form-control"></select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 field-phpproduksi">
                                        <div class="form-group">
                                            <label>Perkiraan HP Produksi</label>
                                            <select name="phpproduksi" id="phpproduksi" class="form-control"></select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 field-pjasa">
                                        <div class="form-group">
                                            <label>Perkiraan Jasa</label>
                                            <select name="pjasa" id="pjasa" class="form-control"></select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 field-pwaste">
                                        <div class="form-group">
                                            <label>Perkiraan Waste</label>
                                            <select name="pwaste" id="pwaste" class="form-control"></select>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('master/data/barang') ?>" class="btn btn-default">Kembali</a>
                    <button id="btnSave" onclick="saveInputItem();" class="btn btn-primary float-right">Submit</button>
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



<script type="application/javascript" src="<?= base_url('assets/pagejs/master/item.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

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


        $('#mfgdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#mfgdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#mfgdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>