<h3><?php echo $title; ?></h3>
<?php echo $message;?>


<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <div class="col-sm-12">
                    <a href="<?php echo base_url("dashboard")?>"  class="btn btn-default" style="margin:0px; color:#000000;"><i class="fa fa-arrow-left"></i>  Kembali </a>
                </div>

            </div><!-- /.box-header -->
            <div class="box-body table-responsive" style='overflow-x:scroll;'>
                <table id="example1" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th>No.</th>
                        <!--<th>NIK</th>
                        <th>Nama Karyawan</th>-->
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Department</th>
                        <th>Section</th>
                        <th>Group</th>
                        <th>Plant</th>
                        <th>Tanggal Masuk</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach($list as $lu): $no++;?>
                        <tr>
                            <td width="2%"><?php echo $no;?></td>
                            <td><?php echo $lu->nik;?></td>
                            <td><?php echo $lu->nmlengkap;?></td>
                            <td><?php echo $lu->nmdept;?></td>
                            <td><?php echo $lu->nmsubdept;?></td>
                            <td><?php echo $lu->nmgroupgol;?></td>
                            <td><?php echo $lu->locaname;?></td>
                            <td><?php echo date('d-m-Y H:i:s',strtotime($lu->tglmasukkerja));?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>

<script type="application/javascript">
    $('#example1').dataTable();
</script>