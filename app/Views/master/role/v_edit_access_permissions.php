<?php
/*
	@author : hanif_anak_metal \m/
*/
?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
                $("#example2").dataTable();
                $("#example4").dataTable();
				//datemask
				//$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
				//$("#datemaskinput").daterangepicker();
				$("#dateinput").datepicker();
            });
			//form validation
</script>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?>:  <?php echo trim($rolename);?></h1>
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

</br>
<form action="<?php echo base_url('master/role/save_akses')?>" method="post">
<div class="row">
		<div class="col-sm-6">
			<div class="card card-danger">
				<div class="card-body">
					<div class="form-horizontal">
						<div class="form-group">
							<label>MENU</label>
							<div>
								<input type="hidden" class="form-control input-sm" value="edit" id="tipe" name="tipe" required>
								<input type="text" class="form-control input-sm" value="<?php echo trim($akses['kodemenu']);?>" name="menu" readonly>
							</div>
						</div>
						<div class="form-group">
							<label>NAMA MENU</label>
							<div>
								<input type="text" class="form-control input-sm" value="<?php echo trim($akses['namamenu']);?>" name="namamenu" readonly>
								<input type="hidden" class="form-control input-sm" value="<?php echo trim($akses['roleid']);?>" name="roleid" readonly>
							</div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">HOLD MODUL</label>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="chold" id="hold1" value="t" <?php if ($akses['chold']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="chold" id="hold2" value="f" <?php if ($akses['chold']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">AKSES VIEW</label>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_view" id="view1" value="t" <?php if ($akses['a_view']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_view" id="view2" value="f" <?php if ($akses['a_view']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">INPUT</label>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_input" id="input1" value="t" <?php if ($akses['a_input']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_input" id="input2" value="f" <?php if ($akses['a_input']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">UPDATE</label>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_update" id="a_update1" value="t" <?php if ($akses['a_update']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_update" id="a_update2" value="f" <?php if ($akses['a_update']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">DELETE</label>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_delete" id="delete1" value="t" <?php if ($akses['a_delete']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_delete" id="delete2" value="f" <?php if ($akses['a_delete']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">APPROVE 1</label>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_approve1" id="approve1" value="t" <?php if ($akses['a_approve1']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_approve1" id="approve2" value="f" <?php if ($akses['a_approve1']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">APPROVE 2</label>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_approve2" id="approve21" value="t" <?php if ($akses['a_approve2']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_approve2" id="approve22" value="f" <?php if ($akses['a_approve2']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">APPROVE 3</label>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_approve3" id="approve31" value="t" <?php if ($akses['a_approve3']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_approve3" id="approve32" value="f" <?php if ($akses['a_approve3']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">FILTER</label>
                            <div class="radio col-6">
                                <label class="">
                                    <input type="radio" name="a_filter" id="a_filter1" value="t" <?php if ($akses['a_filter']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-6">
                                <label class="">
                                    <input type="radio" name="a_filter" id="a_filter2" value="f" <?php if ($akses['a_filter']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-12" style="font-size: 150%">REPORT</label>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_report" id="a_report1" value="t" <?php if ($akses['a_report']==='t') { echo 'checked';}?>>
                                    YES
                                </label>
                            </div>
                            <div class="radio col-sm-6">
                                <label class="">
                                    <input type="radio" name="a_report" id="a_report2" value="f" <?php if ($akses['a_report']==='f') { echo 'checked';}?>>
                                    NO
                                </label>
                            </div>
						</div>
					</div>
				</div><!-- /.card-body -->
			</div><!-- /.card -->
		</div>
	</div><!--row-->
<div class="row">
	<div class="col-sm-6">
		<a href="<?php echo base_url('master/role/list_access_permission'.'/?id='.$enc_id.'');?>" class="btn btn-default" style="margin:0px"><i class="fa fa-arrow-left"></i> Kembali</a>
		<button type='submit' onclick="return confirm('Anda Yakin Ubah Data ini?')" class="btn btn-primary" style="margin:0px"><i class="fa fa-gear"></i> Ubah Data</button>
	</div>
	<div class="col-sm-6">

	</div>
</div>
</form>

