<?php
/*
	@author : Fiky 07/01/2016
*/

use App\Libraries\Fiky_encryption;

$this->fiky_encryption = new Fiky_encryption();
?>
<script type="text/javascript">
    $(function() {
        $("#table1").dataTable();
        $("#table2").dataTable();
        $("#example3").dataTable();
        $("#dateinput").datepicker();
        $("#dateinput1").datepicker();
        $("#dateinput2").datepicker();
        $("#dateinput3").datepicker();
        $("[data-mask]").inputmask();
        //$("#tglberangkat").datepicker();
        //$("#tglkembali").datepicker();
    });

</script>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?>:  <?php echo trim($dtl['rolename']);?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
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
<?php echo $message; ?>

<div class="row">
<div class="col-sm-12">
    <a href="<?php echo base_url("master/role/add_menugrid/".$this->fiky_encryption->sealed(trim($dtl['roleid']))); ?>"  class="btn btn-success pull-right" style="margin:10px; color:#ffffff;"><i class="fa fa-arrow-right"></i> Lanjutkan</a>

</div>

<div class="col-sm-6">
<form role="form" action="<?php echo base_url("master/role/tambah_menu");?>" method="post">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4 align="center"><?php echo $title1;?></h4>
                <button class="btn btn-primary pull-right" onClick="TEST" style="margin:10px; color:#ffffff;" type="submit">>> >></button>
            </div><!-- /.card-header -->
            <div class="card-body table-responsive" style='overflow-x:scroll;'>
                <table id="table1" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Action</th>
                        <th>MENU</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach($list_menu_child as $lu): $no++;?>
                        <tr>
                            <td width="2%"><?php echo $no;?></td>
                            <td width="8%">
                                <input type="checkbox" name="centang[]" value="<?php echo trim($lu->kodemenu);?>" ><br>
                            </td>
                            <td><?php echo $lu->menunya;?></td>

                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <input type="hidden" name="id" value="<?php echo trim($dtl['id']);?>" >
                <input type="hidden" name="roleid" value="<?php echo trim($dtl['roleid']);?>" >
            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div>
</form>
</div>
<div class="col-sm-6">
<form role="form" action="<?php echo base_url("master/role/kurangi_menu");?>" method="post">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4 align="center"><?php echo $title2;?></h4>
                <button class="btn btn-primary pull-left" onClick="TEST" style="margin:10px; color:#ffffff;" type="submit" <?php if($cek_user==0) { ?> disabled <?php }?>><< <<</button>
            </div><!-- /.card-header -->
            <div class="card-body table-responsive" style='overflow-x:scroll;'>
                <table id="table2" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Action</th>
                        <th>MENU</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach($list_menu_user as $lu): $no++;?>
                        <tr>
                            <td width="2%"><?php echo $no;?></td>
                            <td width="8%">
                                <input type="checkbox" name="centang[]" value="<?php echo trim($lu->kodemenu);?>" ><br>
                            </td>
                            <td><?php echo $lu->menunya;?></td>

                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <input type="hidden" name="id" value="<?php echo trim($dtl['id']);?>" >
                <input type="hidden" name="roleid" value="<?php echo trim($dtl['roleid']);?>" >
            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div>
</form>
</div>




</div>





