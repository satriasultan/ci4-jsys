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
                    <h1 class="m-0"> Input Currency Data</h1>
                <?php elseif ($typeform === 'UPDATE'): ?>
                    <h1 class="m-0"> Update Currency Data</h1>
                <?php else: ?>
                    <h1 class="m-0"> Detail Currency Data</h1>
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
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                    <li class="pt-2 px-3"><h3 class="card-title">Currency</h3></li>
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-two-home-tab" data-bs-toggle="pill" href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">Exchange Rate</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-two-profile-tab" data-bs-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile" aria-selected="false">Perkiraan Pembelian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-bs-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Perkiraan Penjualan</a>
                    </li>
                <!-- <h3 class="card-title">Data Currency</h3> -->
            </div>

            <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- SECTION: Informasi Umum -->
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-usd"></i> Informasi Umum Currency
                            </div>
                            <div class="row">
                                <input type="hidden" name="typeform" value="<?php echo $typeform; ?>" data-typeform="<?php echo $typeform; ?>">

                                <input type="hidden" name="idcurrency" id="idcurrency" value="<?= ($typeform == 'UPDATE') ? $currency['id'] : '' ?>">
                                
                                <div class="col-md-3">
                                    <label>Currency Code</label>
                                    <input type="text" name="currcode" disabled maxlength="3" class="form-control" style="text-transform: uppercase;" required></input>
                                </div>
                                <div class="col-md-9">
                                    <label>Currency Name</label>
                                    <input type="text" name="currname" disabled maxlength="50" class="form-control" style="text-transform: uppercase;" required>
                                </div>
                                
                            </div>
                        </div>

                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-exchange"></i> Exchange Rate
                            </div>
                            <div class="row">
                                <div class=" col-md-2">
                                    <div class="input-group">
                                        <button type="button" id="btn_insert_" onclick="insertNewExchange()" class="btn btn-success form-control float-right"><i class="fa fa-plus"></i> Insert Exchange Rate</button>
                                    </div>
                                </div>
                                <div class=" col-md-4">
                                    <!-- <div class="input-group">
                                    </div> -->
                                </div>
                                <div class="col-sm-6 ">
                                    <!--<form id="formLoadLpbWin">-->
                                    <div class=" float-right">
                                        <div class="input-group">
                                            <button href="#" id="update_exchange" class="btn btn-primary float-left" style="margin-left:5px"><i class="fa fa-gear"></i> Update </button>
                                            <button href="#" id="delete_exchange" class="btn btn-danger float-left" style="margin-left:5px"><i class="fa fa-trash"></i> Delete </button>
                                        </div>
                                    </div>
                                    <!--</form>-->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="charge"></label>
                                </div>
                                <div class="col-sm-12">
                                    <form id="frm-examplee" action="#" method="POST">
                                        <div class="table-wrapper" style="overflow-x: auto;">
                                            <table id="t_exchange" class="table table-bordered table-striped"  style="text-wrap:nowrap; text-align: center;"  cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    <td style="font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Act</td>
                                                    <td style="font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle;min-width: 200px;">Exchange Date</td>
                                                    <td style="font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle;min-width: 100px;">Price</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                    <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                            </div>
                            <div class="row">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                    <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                            </div>
                            <div class="row">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <!-- <?php if ($typeform !== 'DETAIL'): ?>
                    <button type="button" id="btnSave" onclick="submitCurrency()" class="btn btn-success float-right">Save Currency</button>
                <?php endif; ?> -->
                <a href="<?= $back_url; ?>" class="btn btn-default float-left">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
<!-- </form> -->
<!-- Bootstrap modal -->




<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/currency.js') ?>"></script>
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
        // $('#exchangedate').daterangepicker({
        //     autoUpdateInput: false,
        //     singleDatePicker: true,
        //     showDropdowns: true,
        //     locale: {
        //         format: 'DD-MM-YYYY'
        //     },
        //     cancelLabel: 'Clear',
        // });
        // $('#exchangedate').on('apply.daterangepicker', function(ev, picker) {
        //     $(this).val(picker.startDate.format('DD-MM-YYYY'));
        //     $('#formSubmitCapex').bootstrapValidator('updateStatus', 'exchangedate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'exchangedate');
        // });
        // $('#exchangedate').on('cancel.daterangepicker', function(ev, picker) {
        //     $(this).val('');
        // });
        $('#t_exchange').on('draw.dt', function () {
            // Temukan semua input yang ID-nya diawali dengan "exchangedate_"
            $('[id^="exchangedate_"]').each(function () {
                const $input = $(this);

                // Pastikan tidak menginisialisasi 2x
                if ($input.data('daterangepicker')) return;

                $input.daterangepicker({
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    showDropdowns: true,
                    locale: {
                        format: 'DD-MM-YYYY'
                    },
                    cancelLabel: 'Clear'
                });

                $input.on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD-MM-YYYY'));
                });

                $input.on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
            });
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