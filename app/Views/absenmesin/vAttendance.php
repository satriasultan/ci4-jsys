<?php
/*
    author : Fiky Ashariza
 */
?>
<script type="text/javascript">

    var save_method; //for save method string
    var table;
    $(document).ready(function() {
        table = $('#table').DataTable();
        $('#tgl').daterangepicker();
    });

</script>
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
                <li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#lAbsenWil"  href="#"><i class="fa fa-search"></i>Filter</a></li>
                <!--li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#lAbsenRegu"  href="#"><i class="fa fa-download"></i>Laporan Absen Regu</a></li-->
                <!--li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#lAbsenDept"  href="#"><i class="fa fa-download"></i>Laporan Absen Department</a></li-->
                <!--li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#ChoiceOfLetter"  href="#"><i class="fa fa-plus"></i>INPUT TYPE PDCA</a></li-->
                <!--li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo base_url("ga/ajustment/input_ajustment_in_trgd")?>">Input Transfer Antar Gudang</a></li-->
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
                        <th>USERID</th>
                        <th>BADGENUMBER</th>
                        <th>NAMA ABSEN</th>
                        <th>NIK</th>
                        <th>CHECKTIME</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; foreach ($list_absen as $la){ ?>
                        <tr>
                            <td><?php echo $la->userid;?></td>
                            <td><?php echo $la->badgenumber;?></td>
                            <td><?php echo $la->nama;?></td>
                            <td><?php echo $la->nik;?></td>
                            <td><?php echo date('d-m-Y H:i:s',strtotime($la->checktime));?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>

<div class="modal fade" id="lAbsenWil" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"> FILTER DETAIL ABSENSI MESIN </h4>
            </div>
            <form action="<?php echo base_url('absenmesin/mc_vAttendance');?>" name="form" role="form" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3">Pilih Wilayah</label>
                            <div class="col-sm-9">
                                <select id="kdcabang" name="kdcabang" class="form-control pull-right"  required>
                                    <option value="">--Pilih Wilayan--</option>
                                    <?php foreach ($list_kanwil as $ld){ ?>
                                        <option value="<?php echo trim($ld->kdcabang);?>"><?php echo $ld->desc_cabang;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        </div>
                        <div class="col-sm-12">
                            <label class="col-sm-12"></label>
                        </div>
                        <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3">Tanggal</label>
                            <div class="col-sm-9">
                                    <input type="text" id="tgl" name="tgl"   class="form-control pull-right" required>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="submit"  class="btn btn-primary"><i class="glyphicon glyphicon-search"></i>Cari</button>
                </div>
            </form>
        </div>
    </div>
</div>