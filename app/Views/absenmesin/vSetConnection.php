<?php
/*
    author : Fiky Ashariza
 */
?>
<style>

    /** SPINNER CREATION **/

    .loader {
        position: relative;
        text-align: center;
        margin: 15px auto 35px auto;
        z-index: 9999;
        display: block;
        width: 80px;
        height: 80px;
        border: 10px solid rgba(0, 0, 0, .3);
        border-radius: 50%;
        border-top: 16px solid #00802d;
        border-right: 16px solid #fff700;
        border-bottom: 16px solid red;
        border-left: 16px solid #b6b6b6;
        animation: spin 1s ease-in-out infinite;
        -webkit-animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            -webkit-transform: rotate(360deg);
        }
    }

    @-webkit-keyframes spin {
        to {
            -webkit-transform: rotate(360deg);
        }
    }


    @keyframes blink {
        /**
         * At the start of the animation the dot
         * has an opacity of .2
         */
        0% {
            opacity: .2;
        }
        /**
         * At 20% the dot is fully visible and
         * then fades out slowly
         */
        20% {
            opacity: 1;
        }
        /**
         * Until it reaches an opacity of .2 and
         * the animation can start again
         */
        100% {
            opacity: .2;
        }
    }

    .saving span {
        /**
         * Use the blink animation, which is defined above
         */
        animation-name: blink;
        /**
         * The animation should take 1.4 seconds
         */
        animation-duration: 1.4s;
        /**
         * It will repeat itself forever
         */
        animation-iteration-count: infinite;
        /**
         * This makes sure that the starting style (opacity: .2)
         * of the animation is applied before the animation starts.
         * Otherwise we would see a short flash or would have
         * to set the default styling of the dots to the same
         * as the animation. Same applies for the ending styles.
         */
        animation-fill-mode: both;
    }

    .saving span:nth-child(2) {
        /**
         * Starts the animation of the third dot
         * with a delay of .2s, otherwise all dots
         * would animate at the same time
         */
        animation-delay: .2s;
    }

    .saving span:nth-child(3) {
        /**
         * Starts the animation of the third dot
         * with a delay of .4s, otherwise all dots
         * would animate at the same time
         */
        animation-delay: .4s;
    }
    /** MODAL STYLING **/

/*
    .modal-content {
        border-radius: 0px;
        box-shadow: 0 0 20px 8px rgba(0, 0, 0, 0.7);
    }

    .modal-backdrop.show {
        opacity: 0.75;
    }
*/

    .loader-txt {
    p {
        font-size: 13px;
        color: #666;
    small {
        font-size: 11.5px;
        color: #999;
    }
    }
    }

</style>
<script type="text/javascript">

    var save_method; //for save method string
    var table;
    $(document).ready(function() {
        table = $('#table').DataTable();
        $('#tgl').daterangepicker();

        $('.xy').on('click', function() {
            $("#loadMe").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });

            console.log('COK');
            console.log($(this).attr('data-url'));
            var linknya = $(this).attr('data-url');
            /* Ajax Request Disini */
            url = linknya;
            //data = $('#downloadform').serialize();
            $.ajax({
                url : url,
                type: "GET",
                //data: $('#form').serialize(),
                ///data: data,
                dataType: "JSON",

                success: function(data)
                {   //$('[name="onhand"]').val(data.conhand);
                    console.log('TEST'+ data.status);
                    console.log(data.status === true);
                    if (data.status === true){
                        $("#loadMe").modal("hide");
                        //alert('Koneksi Sukses');
                        $("#message").html("<div class='alert alert-success alert-dismissable'><i class='fa fa-check'></i><button type='button' class='close' data-bs-dismiss='alert' aria-hidden='true'>×</button><b>Koneksi Mesin Berhasil</b> </div>");
                    } else {
                        $("#loadMe").modal("hide");
                        $("#message").html("<div class='alert alert-danger alert-dismissable'><i class='fa fa-eject'></i><button type='button' class='close' data-bs-dismiss='alert' aria-hidden='true'>×</button><b>Data Gagal.. Mohon Cek Koneksi IP</b> </div>");
                       //alert('Data Gagal.. Mohon Cek Koneksi IP');
                    }

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#loadMe").modal("hide");
                    $("#message").html("<div class='alert alert-danger alert-dismissable'><i class='fa fa-eject'></i><button type='button' class='close' data-bs-dismiss='alert' aria-hidden='true'>×</button><b>Data Gagal.. Data Tidak Valid</b> </div>");
                }
            });



            /*setTimeout(function() {
                $("#loadMe").modal("hide");
            }, 3500);*/

        });
    });


</script>
<ol class="breadcrumb">
    <div class="pull-right"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
    <?php foreach ($y as $y1) { ?>
        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
            <li><a href="<?php echo site_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
        <?php } else { ?>
            <li class="active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
        <?php } ?>
    <?php } ?>
</ol>


<legend><?php echo $title;?></legend>
<?php echo $message;?>
<div id="message" >
</div>
<div class="row">
    <div class="col-sm-3">
        <!--div class="container"--->
        <div class="dropdown ">
            <button class="btn btn-primary dropdown-toggle " style="margin:10px; color:#ffffff;" id="menu1" type="button" data-bs-toggle="dropdown"><i class="fa fa-arrow-down"></i>  Menu
                <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" >
                <!--li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#gPrabsensi"  href="#"><i class="fa fa-gear"></i>Generate Laporan</a></li-->
                <li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#x"  href="#"><i class="fa fa-search"></i>No Menu</a></li>
                <!--li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#lAbsenRegu"  href="#"><i class="fa fa-download"></i>Laporan Absen Regu</a></li-->
                <!--li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#lAbsenDept"  href="#"><i class="fa fa-download"></i>Laporan Absen Department</a></li-->
                <!--li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#ChoiceOfLetter"  href="#"><i class="fa fa-plus"></i>INPUT TYPE PDCA</a></li-->
                <!--li role="presentation"><a role="menuitem" tabindex="-1" href="<!--?php echo site_url("ga/ajustment/input_ajustment_in_trgd")?>">Input Transfer Antar Gudang</a></li-->
            </ul>
        </div>
        <!--/div-->
    </div><!-- /.box-header -->
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">

            </div><!-- /.box-header -->
            <div class="box-body table-responsive" style='overflow-x:scroll;'>
                <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>CABANG</th>
                        <th>NAMA MESIN</th>
                        <th>TYPE</th>
                        <th>IPADDRESS</th>
                        <th>COM</th>
                        <th>PORT</th>
                        <th>METHOD</th>
                        <th>HOLD</th>
                        <th width="8%">AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; foreach ($list_set as $la){ ?>
                        <tr>
                            <td><?php echo $la->cabang;?></td>
                            <td><?php echo $la->machinename;?></td>
                            <td><?php echo $la->ftype;?></td>
                            <td><?php echo $la->ipaddress;?></td>
                            <td><?php echo $la->com_key;?></td>
                            <td><?php echo $la->soap_port;?></td>
                            <td><?php echo $la->read_method;?></td>
                            <td><?php echo $la->chold;?></td>
                            <td width="8%">
                                <!--<a href="!?php
/*                                $enc_kdcabang=bin2hex($this->encrypt->encode(trim($la->kdcabang)));
                                $enc_ctype=bin2hex($this->encrypt->encode(trim($la->ctype)));
                                echo site_url("ga/pembelian/input_supplier_po_mst/$enc_kdcabang");*/?>" onclick="return confirm('Ubah Koneksi')" class="btn btn-primary  btn-sm" title="Ubah Koneksi"><i class="fa fa-gear"></i> </a>-->

                                <a href="<?php
                                echo '#';?>" onclick="return confirm('Ubah Koneksi')" class="btn btn-primary  btn-sm"  data-bs-toggle="modal"
                                   data-bs-target="#mL<?php echo trim($la->id); ?>"  title="Ubah Koneksi"><i class="fa fa-gear"></i> </a>

                                <a data-url="<?php
                                echo base_url("/absenmesin/testKoneksiMesin").'/'.trim($la->id)?>"
                                   class="btn btn-success  btn-sm xy"  title="Ping Server Mesin"><i class="fa fa-play-circle"></i> </a>
                            </td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>

<?php foreach ($list_set as $lx) { ?>
<div class="modal fade" id="mL<?php echo trim($lx->id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"> UBAH KONEKSI MESIN </h4>
            </div>
            <form action="<?php echo base_url('absenmesin/mc_vAttendance');?>" name="form" role="form" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3">CABANG</label>
                            <div class="col-sm-9">
                                <input type="hidden" id="id" name="id"  value="<?php echo trim($lx->id); ?>" readonly>
                                <input type="text" id="cabang" name="cabang"  value="<?php echo trim($lx->cabang); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">NAMA MESIN</label>
                            <div class="col-sm-9">
                                <input type="text" id="machinename" name="machinename"  value="<?php echo trim($lx->machinename); ?>" class="form-control" style="text-transform:uppercase">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">TYPE</label>
                            <div class="col-sm-9">
                                <input type="text" id="ftype" name="ftype"  value="<?php echo trim($lx->ftype); ?>" class="form-control" style="text-transform:uppercase" maxlength="20" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">IP ADDRESS</label>
                            <div class="col-sm-9">
                                <input type="text" id="ipaddress" name="ipaddress"  value="<?php echo trim($lx->ipaddress); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">PORT</label>
                            <div class="col-sm-9">
                                <input type="text" id="soap_port" name="soap_port"  value="<?php echo trim($lx->soap_port); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">READ METHOD</label>
                            <div class="col-sm-9">
                                <select id="read_method" name="read_method"  class="form-control" required>
                                    <option <?php if (trim($lx->read_method)==='BAUD_TAD'){ echo 'SELECTED' ;} ?> value="BAUD_TAD">BAUD_TAD</option>
                                    <option <?php if (trim($lx->read_method)==='BAUD_ZK'){ echo 'SELECTED' ;} ?>  value="BAUD_ZK">BAUD_ZK</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">HOLD</label>
                            <div class="col-sm-9">
                                <select id="chold" name="chold"  class="form-control" required>
                                    <option <?php if (trim($lx->chold)=='NO'){ echo 'SELECTED' ;} ?> value="NO">NO</option>
                                    <option <?php if (trim($lx->chold)=='YES'){ echo 'SELECTED' ;} ?>  value="YES">YES</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="submit"  class="btn btn-primary"><i class="fa fa-gear"></i> Ubah </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>


<!-- Modal -->
<div class="modal fade" id="loadMe" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="loader"></div>
                <div clas="loader-txt">
                    <h4><p class="saving"><span>Mohon </span><span> Tunggu</span></p></h4>
                    <h5>
                        <p class="saving">Sedang Melakukan Ping Mesin <span>.</span><span>.</span><span>.</span></p>
                    </h5>
                </div>
            </div>
        </div>
    </div>
</div>
