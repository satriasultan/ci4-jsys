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

<?php echo $message;?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <!-- Card Header -->
            <div class="card-header">
                    <h3 class="card-title">Data Currency</h3>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                    <li class="pt-2 px-3"><h3 class="card-title">Currency</h3></li>
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-two-home-tab" data-bs-toggle="pill" href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">Exchange Rate</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-two-profile-tab" data-bs-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile" <?= ($typeform == 'INPUT') ? 'disabled' : '' ?> aria-selected="false">Perkiraan Pembelian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-bs-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" <?= ($typeform == 'INPUT') ? 'disabled' : '' ?> aria-selected="false">Perkiraan Penjualan</a>
                    </li>
                </ul>
                <div class="tab-content" id="custom-tabs-two-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-usd"></i> Informasi Umum Currency
                            </div>
                            <div class="row">
                                <input type="hidden" name="typeform" value="<?php echo $typeform; ?>" data-typeform="<?php echo $typeform; ?>">

                                <input type="hidden" name="idcurrency" id="idcurrency" value="<?= ($typeform == 'UPDATE' || $typeform == 'DETAIL') ? $currency['id'] : '' ?>">
                                
                                <div class="col-md-3">
                                    <label>Currency Code</label>
                                    <input type="text" name="currcode" maxlength="3" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> style="text-transform: uppercase;" required></input>
                                </div>
                                <div class="col-md-9">
                                    <label>Currency Name</label>
                                    <input type="text" name="currname" maxlength="50" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> style="text-transform: uppercase;" required>
                                </div>
                                <?php if(($typeform == 'INPUT')) {?>
                                <div class="col-md-12"><hr></div>
                                <div class="col-md-12">
                                    <button id="btnSave" onclick="submitCurrency();" class="btn btn-primary float-right">Save Master</button>
                                </div>
                                <?php }?>
                            </div>
                        </div>

                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-exchange"></i> Exchange Rate
                            </div>
                            <?php if($typeform == 'UPDATE'){?>
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
                            <?php }?>
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
                    <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                        <div class="card-body">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-cart-arrow-down"></i> Perkiraan Pembelian
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Hutang</label>
                                            <select name="phutang" id="phutang" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Uang Muka</label>
                                            <select name="pum" id="pum" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Bonus</label>
                                            <select name="pbonus" id="pbonus" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Hutang Antar Cabang</label>
                                            <select name="hutangac" id="hutangac" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Hutang Biaya 1</label>
                                            <select name="hutangbiaya1" id="hutangbiaya1" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Hutang Biaya 1</label>
                                            <select name="hutangbiaya2" id="hutangbiaya2" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                        <div class="card-body">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-bar-chart"></i> Perkiraan Penjualan
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Piutang</label>
                                            <select name="ppiutang" id="ppiutang" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Uang Muka</label>
                                            <select name="pumjual" id="pumjual" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Pendapatan</label>
                                            <select name="ppendapatan" id="ppendapatan" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Retur</label>
                                            <select name="pretur" id="pretur" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Disc</label>
                                            <select name="pdisc" id="pdisc" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Bonus</label>
                                            <select name="pbonusjual" id="pbonusjual" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Tunai</label>
                                            <select name="ptunai" id="ptunai" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Piutang Antar Cabang</label>
                                            <select name="piutangac" id="piutangac" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Pendapatan Antar Cabang</label>
                                            <select name="pendapatanac" id="pendapatanac" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Pendapatan Service</label>
                                            <select name="pps" id="pps" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= $back_url; ?>" class="btn btn-default float-left">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <?php if ($typeform == 'UPDATE'): ?>
                        <button id="btnSave" onclick="saveFinalCurrency();" class="btn btn-primary btn-aksi float-right">Save Final Currency</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- </form> -->
<!-- Bootstrap modal -->




<script type="application/javascript" src="<?= base_url('assets/pagejs/master/currency/currency.js') ?>"></script>
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