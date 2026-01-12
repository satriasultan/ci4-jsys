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
                <h3 class="card-title"><small>Data Master Item</small></h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('#')?>" method="post" id="formInputItem" enctype="multipart/form-data" role="form">
                <div class="card-body row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="hidden" id="type" name="type" value="<?= $type ?>" autocomplete="off">
                            <input type="hidden" name="id" class="form-control" value="<?= $id ?>" id="id" placeholder="ID">
                            <label for="idbarang">Item ID</label>
                            <input type="text" name="idbarang" class="form-control" id="idbarang" maxlength="20" placeholder="Item ID" style="text-transform:uppercase;">
                        </div>
                        <div class="form-group">
                            <label for="nmbarang">Item Name</label>
                            <input type="text" name="nmbarang" class="form-control" id="nmbarang" maxlength="60" placeholder="Item Name" style="text-transform:uppercase;">
                        </div>
                        <div class="form-group">
                            <label for="nmbarang">Group</label>
                                <select name="idgroup" id="idgroup" class="form-control" placeholder="Item Group" style="text-transform:uppercase;" >
                                </select>
                        </div>
                        <div class="form-group">
                            <label for="subunitenable">Sub Unit Enable</label>
                            <select name="subunitenable" class="form-control" style="text-transform:uppercase;" >
                                <!--option value="">--Pilih Hold--</option-->
                                <option value="NO"> NO </option>
                                <option value="YES">YES </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Unit">Unit</label>
                            <select name="unit" id="unit" class="form-control" placeholder="Unit" style="text-transform:uppercase;" >
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Unit">Sub Unit (Satuan Kecil)</label>
                            <select name="subunit" id="subunit" class="form-control" placeholder="Sub Unit" style="text-transform:uppercase;" >
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="exampleInputEmail1">ID Barcode/SKU</label>
                            <input type="text" name="idbarcode" class="form-control" id="idbarcode" placeholder="ID Barcode/SKU">
                        </div>
                        <div class="form-group">
                            <label for="expdate">MFG Date</label>
                            <input type="text" name="mfgdate" class="form-control" id="mfgdate" placeholder="MFG Date">
                        </div>
                        <div class="form-group">
                            <label for="expdate">Expired Date</label>
                            <input type="text" name="expdate" class="form-control" id="expdate" placeholder="Expire Date">
                        </div>
                        <div class="form-group">
                            <label for="maks_daystock">Max Save Life (Days)</label>
                            <input type="text" name="maks_daystock" class="form-control fikyseparator ratakanan" id="maks_daystock" placeholder="Maks Save life (Days)">
                        </div>
                        <div class="form-group">
                            <label for="Unit">Default Location</label>
                            <select name="deflocation" id="deflocation" class="form-control" placeholder="Default Location" style="text-transform:uppercase;" >
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Unit">Default Area</label>
                            <select name="defarea" id="defarea" class="form-control" placeholder="Default Area" style="text-transform:uppercase;" >
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">

                        <?php /*
                        <div class="form-group">
                            <label for="mfgdate">MFG Date</label>
                            <input type="text" name="mfgdate" class="form-control" id="mfgdate" placeholder="MFG Date">
                        </div>
                        <div class="form-group">
                            <label for="mfgdate">Batch</label>
                            <input type="text" name="batch" class="form-control" id="batch" placeholder="Batch">
                        </div>
 */ ?>
                        <div class="form-group">
                            <label for="setminstock">Set Minimum Stock</label>
                            <select name="setminstock" class="form-control" style="text-transform:uppercase;" >
                                <!--option value="">--Pilih Hold--</option-->
                                <option value="NO"> NO </option>
                                <option value="YES">YES </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="maks_daystock">Minimum Stock(Set Minimum Will Be Appear In Report)</label>
                            <input type="text" name="minstock" class="form-control fikyseparator ratakanan" id="minstock" placeholder="Set Your Minimum Stock" maxlength="18">
                        </div>
                        <div class="form-group">
                            <label for="chold">Hold Item</label>
                            <select name="chold" class="form-control" style="text-transform:uppercase;" >
                                <!--option value="">--Pilih Hold--</option-->
                                <option value="NO"> NO </option>
                                <option value="YES">YES </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Description">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Enter Your Description..."  style="text-transform:uppercase;"></textarea>
                        </div>

                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('master/item') ?>" class="btn btn-default">Kembali</a>
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