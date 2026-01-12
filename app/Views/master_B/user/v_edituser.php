<?php
/*
	@author : hanif_anak_metal \m/
*/
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
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

<form action="<?php echo base_url('master/user/save')?>" method="post">
<div class="row">
	<div class="col-sm-6">
		<div class="card card-danger">
			<div class="card-body">
				<div class="form-horizontal">
					<div class="form-group">
						<label>USERNAME</label>
						<div>
						<input type="text" class="form-control input-sm" value="<?php echo $dtl_user['username'];?>" name="username" readonly>
                        <input class="form-control input-sm" type="hidden" id="input" name="tipe" value="edit">
                        <input class="form-control input-sm" type="hidden" id="iduser" name="iduser" value="<?php echo trim($dtl_user['iduser']);?>">
						</div>
					</div>
					<div class="form-group">
						<label>PASSWORD</label>
						<div>
							<input class="form-control input-sm" type="password" id="password1" name="passwordweb" placeholder="Kosongkan Jika Tidak Ingin Merubah Password">
						</div>
					</div>
					<div class="form-group">
						<label>ULANG PASSWORD</label>
						<div>
							<input class="form-control input-sm" type="password" id="password2" name="passwordweb2" title="Masukan Ulang Password Sama dengan sebelumnya" placeholder="Kosongkan Jika Tidak Ingin Merubah Password">
						</div>
					</div>
                    <?php /*
					<div class="form-group">
						<label class="col-sm-4">LEVEL ID</label>
						<div class="col-sm-8">
									<select class="form-control input-sm" name="lvlid" id="lvlid">
									  <?php foreach($list_lvljbt as $listkan){?>
									  <option <?php if (trim($listkan->kdlvl)==trim($dtl_user['level_id'])) { echo 'selected';}?> value="<?php echo trim($listkan->kdlvl);?>" ><?php echo $listkan->kdlvl.' || '.$listkan->nmlvljabatan;?></option>
									  <?php }?>
									</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4">LEVEL AKSES</label>
						<div class="col-sm-8">
									<select class="form-control input-sm" name="lvlakses" id="lvlakses">
									  <?php foreach($list_lvljbt as $listkan){?>
									  <option <?php if (trim($listkan->kdlvl)==trim($dtl_user['level_akses'])) { echo 'selected';}?> value="<?php echo trim($listkan->kdlvl);?>" ><?php echo $listkan->kdlvl.' || '.$listkan->nmlvljabatan;?></option>
									  <?php }?>
									</select>
						</div>
					</div>*/
					?>
                    <div class="form-group">
                        <label>Hold ID</label>
                        <div>
                            <select class="form-control input-sm"  name="hold" class="col-sm-12">
                                <option value="NO">TIDAK</option>;
                                <option value="YES">IYA</option>;
                            </select>
                        </div>
                    </div>
					<div class="form-group">
						<label>EXPIRED DATE</label>
							<div>
								<input  class="form-control input-sm" type="text" name="expdate" value="<?php echo $dtl_user['exdate'];?>" id="dateinput"  required data-date-format="dd-mm-yyyy">
							</div>
					</div>
                    <div class="form-group">
                        <label>DEPARTMENT</label>
                        <div>
                            <select class="form-control input-sm" name="kddept" id="kddept">
                                <?php foreach($list_dept as $ldp){?>
                                    <option <?php if (trim($ldp->kddept)===trim($dtl_user['kddept'])) { echo 'selected';}?> value="<?php echo trim($ldp->kddept);?>" ><?php echo $ldp->kddept.' || '.$ldp->nmdept;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>LOKASI PLANT</label>
                        <div>
                            <select class="form-control input-sm" name="loccode" id="loccode">
                                <?php foreach($list_gudang as $lg){?>
                                    <option <?php if (trim($lg->idlocation)===trim($dtl_user['loccode'])) { echo 'selected';}?> value="<?php echo trim($lg->idlocation);?>" ><?php echo $lg->idlocation.' || '.$lg->nmlocation;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>ROLE</label>
                        <div>
                            <select class="form-control input-sm" name="roleid" id="roleid">
                                <?php foreach($list_role as $lrl){?>
                                    <option <?php if (trim($lrl->roleid)===trim($dtl_user['roleid'])) { echo 'selected';}?>  value="<?php echo trim($lrl->roleid);?>" ><?php echo $lrl->roleid.' || '.$lrl->rolename;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
				</div>
			</div><!-- /.card-body -->
            <div class="card-footer">
                <a href="<?php echo base_url('user');?>" class="btn btn-default" style="margin:10px"><i class="fa fa-arrow-left"></i> Kembali </a>
                <button type='submit' onclick="return confirm('Anda Yakin Ubah Data ini?')" class="btn btn-primary" style="margin:10px"><i class="fa fa-save"></i> Ubah Data</button>
            </div>
		</div><!-- /.card -->
	</div>
</div>
</form>

