<style>
    .text-wrap{
        white-space:normal;
    }
    .width-200{
        width:200px;
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <h1 class="m-0"> Penawaran Harga</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php //echo $t; ?></i> Menu ID <?php echo $version; ?></div>
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

<audio id="chatAudio" >
    <source src=
            "<?php echo base_url('assets/sound/beepscan.mp3'); ?>"
            type="audio/mpeg">
</audio>
<?php /*<button onclick="play()">Press Here!</button> */ ?>

<?php echo $message;?>
<?php echo $showUnfinish; ?>
<div class="row">
    <div class="col-12 col-sm-12">
        <div class="card card-cyan card-tabs">
            <!-- <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                    <li class="pt-2 px-3"><h3 class="card-title">STD</h3></li>
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-two-home-tab" data-bs-toggle="pill" href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">Std/CH</a>
                    </li>
                </ul>
            </div> -->
            <div class="card-body">
                <div class="card-header">
                    <a href="<?= base_url('sales/presales/addOffering') ?>" class="btn btn-primary" style="margin:0px"><i class="fa fa-plus"></i>  Input Penawaran Harga  </a>
                    <!-- <a href="#" class="btn btn-default float-right" style="margin:0px"  data-bs-toggle="modal" data-bs-target="#filter" ><i class="fa fa-filter"></i>  Filter  </a> -->
                </div><!-- /.card-header -->
                <div class="tab-content" id="custom-tabs-two-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body table-responsive" style='overflow-x:scroll;'>
                                    <table id="t_offering" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="1%">Action</th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-30"> Document No. </div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-30"> Document Date </div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-30"> Role Job</div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-150"> Customer </div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-150"> Addresss </div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-30"> Phone </div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-30"> Fax </div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-90"> Up </div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-60"> Status </div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-60"> Input By </div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-60"> Input Date </div></th>

                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-50"> Print By</div></th>
                                                <th><div style="vertical-align: middle;" class="text-center text-wrap width-50"> Print Date</div></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div><!-- /.card-body -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>


</div>
<div class="row">

</div>


<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/presales/offering.js') ?>"></script>
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






