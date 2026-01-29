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
                <h3 class="card-title"><?=  $typeTitle = ($type == 'INPUT') ? 'Input' : ($type == 'UPDATE' ? 'Edit' : 'Detail'); ?> Data Master Supplier</h3>            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('#')?>" method="post" id="formInputSupplier" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-address-card"></i> Informasi Supplier
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kdsupplier">Kode Supplier</label>
                                    <input type="hidden" id="type" name="type" value="<?= $type ?>" autocomplete="off">
                                    <input type="hidden" id="id" name="id" value="<?= $id ?>" autocomplete="off">
                                    <input type="text" name="kdsupplier" class="form-control" id="kdsupplier" <?= $type == 'UPDATE' ? 'disabled' : '' ?> maxlength="60" placeholder="Kode Supplier" style="text-transform:uppercase;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nmsupplier">Nama Supplier</label>
                                    <input type="text" name="nmsupplier" class="form-control" id="nmsupplier" maxlength="250" placeholder="Nama Supplier" style="text-transform:uppercase;">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="alamat">Alamat </label>
                                    <textarea type="text" name="alamat" class="form-control" row="3" id="alamat" placeholder="Alamat" style="text-transform:uppercase;"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="idprovinsi">Wilayah/Provinsi</label>
                                    <select name="idprovinsi" id="idprovinsi" class="form-control" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="idkota">Kota</label>
                                    <select name="idkota" id="idkota" class="form-control" required>
                                    </select>
                                </div>
                            </div>
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
                            <div class="col-md-1">
                                <div class="form-group">
                                    <!-- Radio untuk Interngroup -->
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="interngroup" id="interngroup" value="1" <?= isset($data['interngroup']) && $data['interngroup'] == '1' ? 'checked' : '' ?> <?= $disabled ?>>
                                        <label class="form-check-label" for="interngroup">
                                            Interngroup
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="Description">Keterangan</label>
                                    <textarea name="keterangan"  id="keterangan" class="form-control" rows="3" placeholder="Catatan/Informasi..."  style="text-transform:uppercase;" <?= $disabled ?>></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-laptop"></i> Informasi Supplier
                        </div>
                        <div class="row">                            
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-file-text"></i> Informasi Penggunaan
                        </div>
                        <div class="row">                            
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-exchange-alt"></i> Informasi Perizinan 
                        </div>
                        <div class="row">
                        </div>
                    </div> -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('master/data/supplier') ?>" class="btn btn-default">Kembali</a>
                    <button id="btnSave" onclick="saveInputSupplier();" class="btn btn-primary float-right">Submit</button>
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





<script type="application/javascript" src="<?= base_url('assets/pagejs/master/supplier/suppliers.js') ?>"></script>
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