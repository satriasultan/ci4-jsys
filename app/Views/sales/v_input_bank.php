<style>
    .text-wrap{
        white-space:normal;
    }
    .width-200{
        width:200px;
    }
    /* Styling tooltip agar lebih rapi */
    .tooltip-inner {
        background-color: #333 !important; 
        color: #fff !important;
        max-width: 250px; /* Default ukuran lebih kecil */
        padding: 10px;
        text-align: left;
        border-radius: 8px;
        font-size: 14px;
    }

    /* Khusus untuk tooltip "Jenis Object Pembelian" */
    #objcapex + label .tooltip-inner {
        max-width: 600px !important;
    }

    /* Tooltip default */
    .tooltip-small .tooltip-inner {
        max-width: 250px;
    }

    /* Tooltip besar hanya untuk "Jenis Object Pembelian" */
    .tooltip-large .tooltip-inner {
        max-width: 600px;
    }

    .section-block {
        background-color: #f9f9fc;
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
        transform: scale(1.02);
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


</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <?php if ($typeform !== 'DETAIL' && $typeform !== 'UPDATE'): ?>
                    <h1 class="m-0"> Input Bank Data</h1>
                <?php elseif ($typeform === 'UPDATE'): ?>
                    <h1 class="m-0"> Update Bank Data</h1>
                <?php else: ?>
                    <h1 class="m-0"> Detail Bank Data</h1>
                <?php endif; ?>
                    
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php //echo $t; ?></i> Versi: <?php echo $version; ?></div>
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


<div id="form-container" name="<?php echo $typeform; ?>" data-typeform="<?php echo $typeform; ?>">
</div>
<div id="form-status" name="<?php echo $status; ?>" data-typeform="<?php echo $status; ?>">
</div>

<!-- <div id="bagian" name="<?php echo $bagian; ?>" data-typeform="<?php echo $bagian; ?>">
</div> -->

<?php echo $message;?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <!-- Card Header -->
            <div class="card-header">
                <h3 class="card-title">Data Bank</h3>
            </div>

            <!-- Card Body -->
            <div class="card-body">

                <!-- SECTION: Informasi Umum -->
                <div class="section-block">
                    <div class="section-header">
                        <i class="fa fa-building"></i> Informasi Umum Bank
                    </div>
                    <div class="row">
                        <input type="hidden" name="typeform" value="<?php echo $typeform; ?>" data-typeform="<?php echo $typeform; ?>">

                        <input type="hidden" name="idbank" id="idbank" value="<?= ($typeform == 'UPDATE') ? $bank['id'] : '' ?>">
                        <div class="col-md-4">
                            <label>Nama Bank</label>
                            <input type="text" name="nmbank" maxlength="100" class="form-control" style="text-transform: uppercase;" required>
                        </div>

                        <div class="col-md-6">
                            <label>Alamat Bank</label>
                            <textarea type="text" name="alamat" class="form-control" style="text-transform: uppercase;" required></textarea>
                        </div>
                        <div class="col-md-2">
                            <label>Kode Pos Bank</label>
                            <input type="text" name="kodepos" maxlength="100" class="form-control" style="text-transform: uppercase;" required>
                        </div>
                        <div class="col-md-4">
                            <label>Account Name</label>
                            <input type="text" name="accname" maxlength="100" class="form-control" style="text-transform: uppercase;" required>
                        </div>
                        <div class="col-md-6">
                            <label>Account Number</label>
                            <textarea type="text" name="accno" class="form-control" style="text-transform: uppercase;" required></textarea>
                        </div>
                        <div class="col-md-2">
                            <label>Swit Code</label>
                            <input type="text" name="swiftcode" maxlength="100" class="form-control" style="text-transform: uppercase;" required>
                        </div>
                        <div class="col-md-6">
                            <label>Remarks</label>
                            <textarea type="text" name="catatan" class="form-control" style="text-transform: uppercase;" required></textarea>
                        </div>
                        
                    </div>
                </div>

            </div>



            <!-- /.card-body -->
            <div class="card-footer">
                <?php if ($typeform !== 'DETAIL'): ?>
                    <button type="button" id="btnSave" onclick="submitBank()" class="btn btn-success float-right">Save Bank</button>
                <?php endif; ?>
                <a href="<?= $back_url; ?>" class="btn btn-default float-left">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
<!-- </form> -->
<!-- Bootstrap modal -->




<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/bank.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $(".sidebar-mini").addClass( "sidebar-collapse" );
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
            $('#formSubmitCapex').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        /* ESTIMASI DATE */
        $('#estdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#estdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#formInputTransfers').bootstrapValidator('updateStatus', 'estdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#estdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        document.querySelectorAll(".tooltip-icon").forEach(function (tooltipIcon) {
            var content = tooltipIcon.getAttribute("data-tooltip-content");

            if (content) {
            new bootstrap.Tooltip(tooltipIcon, {
                title: content,
                sanitize: false, // Mengizinkan HTML di dalam tooltip
                placement: "right",
                customClass: tooltipIcon.closest('.col-sm-3')?.querySelector('#objcapex') ? 'tooltip-large' : 'tooltip-small'
            });
            }
        });
    });

</script>