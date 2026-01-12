<div class="box-header">
    <h4><?php echo 'Tanggal Presensi Mesin Finger : '.date('d-m-Y',strtotime($dtl['tgl'])); ?> </h4>
    <h4><?php echo $dtlk['nmlengkap'].' / '.$dtlk['nmdept'].' / '.$dtlk['nmsubdept'].' / '.$dtlk['nmjabatan']; ?> </h4>
</div>
<div class="box-body table-responsive" style='overflow-x:scroll;'>
    <table id="example2" class="table table-bordered table-striped" >
        <thead>
        <tr>
            <th>No.</th>
            <th>Userid</th>
            <th>Badgenumber</th>
            <th>Checktime</th>
            <th>O/L</th>
            <th>Type</th>
        </tr>
        </thead>
        <tbody>
        <?php $no=0; foreach($list_checktime as $lu): $no++;?>
            <tr>
                <td width="2%"><?php echo $no;?></td>
                <td><?php echo $lu->userid;?></td>
                <td><?php echo $lu->badgenumber;?></td>
                <td><?php echo $lu->checktime;?></td>
                <td><?php echo $lu->inputan;?></td>
                <td><?php echo $lu->ctype;?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div><!-- /.box-body -->