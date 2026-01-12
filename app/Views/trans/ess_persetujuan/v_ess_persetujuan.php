<?php
/*
 * Author: Fiky Ashariza
 * Create Date: 8/29/21, 4:17 PM
 * Path Directory: v_ess_dashboard.php
 */

?>

<?php /*
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.1/css/select.dataTables.min.css">
*/ ?>

<ol class="breadcrumb">
    <div class="pull-right"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
    <?php foreach ($y as $y1) { ?>
        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
            <li><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
        <?php } else { ?>
            <li class="active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
        <?php } ?>
    <?php } ?>
</ol>
<!--h3><?php echo $title; ?></h3-->
<?php echo $message;?>
<style>
/*
    table.dataTable tr th.select-checkbox.selected::after {
        content: "✔";
        margin-top: -11px;
        margin-left: -4px;
        text-align: center;
        text-shadow: rgb(176, 190, 217) 1px 1px, rgb(176, 190, 217) -1px -1px, rgb(176, 190, 217) 1px -1px, rgb(176, 190, 217) -1px 1px;
    }
*/


    .checkbox-xl {
        top: 1.2rem;
        width: 6rem;
        height: 4rem;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <form role="form" action="<?php echo base_url("trans/ess_persetujuan/proses");?>" method="post">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-header">
                        <h4 align="center"><?php echo $title1;?></h4>
                        <a class="btn btn-default pull-left btn-lg" style="margin:10px; color:#000000;" href="<?php echo base_url('trans/ess_dashboard')?>"><i class="fa fa-arrow-circle-left"></i> KEMBALI</a>
                        <?php if ($count_approval>0) { ?>
                        <button name="bsubmit" value="setujui" class="btn btn-success pull-right btn-lg" style="margin:10px; color:#ffffff;" type="submit"><i class="fa fa-check-circle-o"></i> SETUJUI</button>
                        <button name="bsubmit" value="batalkan" class="btn btn-danger pull-left btn-lg" style="margin:10px; color:#ffffff;" type="submit"><i class="fa fa-close"></i> BATALKAN</button>
                        <?php } ?>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive" style='overflow-x:scroll;'>
                        <table id="table1" class="table table-bordered table-striped" >
                            <thead>
                            <tr>
                                <th width="1%">ACTION</th>
                                <th>NIK</th>
                                <th>NAMA LENGKAP</th>
                                <th>DEPARTMEN</th>
                                <th>DOKUMEN</th>
                                <th>TANGGAL</th>
                                <th>JENIS</th>
                                <th>KETERANGAN</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no=0; foreach($list_approval as $lu): $no++;?>
                                <tr>
                                    <td width="1%" align="center">
                                        <input checked type="checkbox" name="centang[]" class="checkbox-xl" value="<?php echo trim($lu->docno);?>" ><br>
                                    </td>
                                    <td><?php echo $lu->nik;?></td>
                                    <td><?php echo $lu->nmlengkap;?></td>
                                    <td><?php echo $lu->nmdept;?></td>
                                    <td><?php echo $lu->docno;?></td>
                                    <td><?php echo $lu->tglawal1;?></td>
                                    <td><?php echo $lu->doctypename;?></td>
                                    <td><?php echo $lu->reason;?></td>

                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                        <?php /*
                        <input type="hidden" name="id" value="<?php echo trim($dtl['id']);?>" >
                        <input type="hidden" name="roleid" value="<?php echo trim($dtl['roleid']);?>" >
 */ ?>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </form>
    </div>
</div>
<div class="row">

</div>
<?php /*
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js"></script>
 */ ?>
<script type="text/javascript">
    $("#table1").dataTable();

    let example = $('#example').DataTable({
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'os',
            selector: 'td:first-child'
        },
        order: [
            [1, 'asc']
        ]
    });
    example.on("click", "th.select-checkbox", function() {
        if ($("th.select-checkbox").hasClass("selected")) {
            example.rows().deselect();
            $("th.select-checkbox").removeClass("selected");
        } else {
            example.rows().select();
            $("th.select-checkbox").addClass("selected");
        }
    }).on("select deselect", function() {
        ("Some selection or deselection going on")
        if (example.rows({
            selected: true
        }).count() !== example.rows().count()) {
            $("th.select-checkbox").removeClass("selected");
        } else {
            $("th.select-checkbox").addClass("selected");
        }
    });

</script>
<!--<script type="text/javascript" src="<?/*= base_url('assets/pagejs/trans/ess_persetujuan.js') */?>"></script>-->




