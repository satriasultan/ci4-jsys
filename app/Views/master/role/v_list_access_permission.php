<?php
/*
	@author : hanif_anak_metal \m/
*/
use App\Libraries\Fiky_encryption;

$this->fiky_encryption = new Fiky_encryption();
?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
                $("#example2").dataTable();
                $("#example4").dataTable();
				$("#menu").selectize();
				//datemask
				//$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
				//$("#datemaskinput").daterangepicker();
				$("#dateinput").datepicker();
            });
			//form validation

			window.onload = function () {
				document.getElementById("password1").onchange = validatePassword;
				document.getElementById("password2").onchange = validatePassword;
			}
			function validatePassword(){
			var pass2=document.getElementById("password2").value;
			var pass1=document.getElementById("password1").value;
			if(pass1!=pass2)
				document.getElementById("password2").setCustomValidity("Passwords Tidak Sama");
			else
				document.getElementById("password2").setCustomValidity('');
			//empty string means no validation error
			}

</script>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
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
<?php echo $message;?>
<div class="row">
	<div class="col-sm-12">
		<a href="<?php echo base_url("master/role");?>" class="btn btn-default" style="margin:10px"><i class="fa fa-arrow-left"></i> <?= 'Back' ?> </a>

	</div>
</div>
</br>
<div class="row">
	<div class="col-sm-12">
		<div class="box">
			<div class="box-header">
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example1" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<th>Kode</th>
							<th>Menu</th>
							<th>Hold</th>
							<th>View</th>
							<th>Input</th>
							<th>Update</th>
							<th>Delete</th>
							<th>Approve1</th>
							<th>Approve2</th>
							<th>Approve3</th>
							<th>Filter</th>
							<th>Report</th>
							<th width="8%">Action</th>

						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_akses as $lu): $no++;?>
						<tr>
							<td width="2%"><?php echo $no;?></td>
							<td><?php echo $lu->kodemenu;?></td>
							<td><?php echo $lu->namamenu;?></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->chold)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->chold;?></span></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->a_view)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->a_view;?></span></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->a_input)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->a_input;?></span></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->a_update)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->a_update;?></span></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->a_delete)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->a_delete;?></span></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->a_approve1)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->a_approve1;?></span></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->a_approve2)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->a_approve2;?></span></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->a_approve3)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->a_approve3;?></span></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->a_filter)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->a_filter;?></span></td>
                            <td align="center"><span style="font-size:12px;" class="label <?php if(trim($lu->a_report)==='YES') { ?> label-success <?php } else { ?> label-danger <?php } ?>"><?php echo $lu->a_report;?></span></td>
							<td width="8%">
								<a href='<?php $roleid=trim($lu->roleid); $kodemenu=trim($lu->kodemenu);
								$enc_roleid=$this->fiky_encryption->sealed($roleid);
								$enc_kodemenu=$this->fiky_encryption->sealed($kodemenu);
								echo base_url("master/role/edit_akses/$enc_roleid/$enc_kodemenu")?>' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-primary  btn-sm">
                                    <i class="fa fa-gear"></i>
                                </a>
								<a href='<?php  echo base_url("master/role/hps_akses/$enc_roleid/$enc_kodemenu")?>' onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-danger  btn-sm">
                                    <i class="fa fa-trash-o"></i>
                                </a>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>

