<?php
/**
 * author : Fiky Ashariza
 */

?>

<legend><?php echo $title;?></legend>
<?php //echo $message;?>
<div id="message" >
</div>
<div><?php //echo 'Total data: '.$ttldata['jumlah']; ?></div>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <div class="col-sm-12">
                    <a href="<?php echo site_url('trans/absensi/filter_detail');?>"  class="btn btn-default" style="color:#000000;">Kembali</a>

                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive" style='overflow-x:scroll;'>
                <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>

                        <th width="5%">No.</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Kode Shift</th>
                        <th>Keterangan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach ($list_absen as $la): $no++ ?>
                        <tr>
                            <td><?php echo $no;?></td>
                            <td><?php echo $la->nik;?></td>
                            <td><?php echo $la->nmlengkap;?></td>
                            <td><?php echo $la->tgl;?></td>
                            <td><?php echo $la->masuk;?></td>
                            <td><?php echo $la->keluar;?></td>
                            <td><?php echo $la->kdpokok;?></td>
                            <td><?php echo $la->keterangan;?></td>

                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>

<!-- INPUT MODAL FILTER -->
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">FILTER ABSENSI</h4>
            </div>

            <div class="modal-body">
                <form role="form" action="<?php echo site_url('trans/absensi/detail');?>" method="post">
                    <div class="row">

                        <div class="form-group">
                            <label class="col-sm-12">Status</label>
                            <div class="col-sm-12">
                                <select class="form-control input-sm" name="status" id="status">

                                    <option value="TIDAK MASUK KERJA">TIDAK MASUK KERJA</option>
                                    <option value="TERLAMBAT">TERLAMBAT</option>

                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
                                <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
                            </div>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>




<script>




    //Date range picker
    $('#tgl').datepicker();
    $('#pilihkaryawan').selectize();
    $("[data-mask]").inputmask();
    $("table").dataTable();

</script>