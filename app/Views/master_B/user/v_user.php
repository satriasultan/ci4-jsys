
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h4>
    </div>
    <div class="col-md-7 align-self-center text-end">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version.''; ?></li>
                <li><i class=""></i> - </li>
                <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
                <?php foreach ($y as $y1) { ?>
                    <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                        <li class="breadcrumb-item"><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
                    <?php } else { ?>
                        <li class="breadcrumb-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                    <?php } ?>
                <?php } ?>
            </ol>
        </div>
    </div>
</div>


<?php echo $message;?>


<div class="row">
	<div class="col-sm-12">

	</div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a href="#" data-bs-toggle="modal" data-bs-target="#input"  class="btn btn-primary" style="margin:10px"><i class="fa fa-plus"></i>  Input Master User  </a>
                <div class="table-responsive m-t-40" style='overflow-x:scroll;'>
                    <table id="tuser" class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th width="1%">No.</th>
                            <th width="8%">Action</th>
                            <th>Username</th>
                            <th>Expired</th>
                            <th>Hold</th>
                            <th>Department</th>
                            <th>Location</th>
                            <th>Role</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php /*
						<?php $no=0; foreach($list_user as $lu): $no++;?>
						<tr>
							<td width="2%"><?php echo $no;?></td>
							<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->username;?></td>
							<td><?php echo date('d-m-Y H:i:s',strtotime($lu->expdate));?></td>
							<td><?php if(trim($lu->hold_id)==='N'){ echo 'TIDAK'; } else { echo 'YA'; }?></td>
							<td><?php echo $lu->locaname; ?></td>
							<td width="8%">
								<a href='<?php $enc_nik=$this->fiky_encryption->enkript(trim($lu->nik)); $enc_username=$this->fiky_encryption->enkript(trim($lu->username)); echo base_url("master/user/edit/$enc_nik/$enc_username")?>' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-warning  btn-sm" title="Ubah Password">
                                    <i class="fa fa-gear"></i>
                                </a>
								<a href='<?php echo base_url("master/user/akses/$enc_nik/$enc_username")?>' onclick="return confirm('Anda Yakin Ubah Hak Akses User ini?')" class="btn btn-primary  btn-sm" title="Ubah Akses">
                                    <i class="fa fa-key"></i>
                                </a>
								<a href='<?php echo base_url("master/user/hps/$enc_nik/$enc_username")?>' onclick="return confirm('Hapus data user akan menghilangkan seluruh data user, Anda Yakin?')" class="btn btn-danger  btn-sm" title="Hapus User">
                                    <i class="fa fa-trash-o"></i>
                                </a>
							</td>
						</tr>
						<?php endforeach;?>
                        */
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>





<!--Modal untuk Input-->
<div class="modal fade bs-example-modal-lg" id="input" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Input Master User</h4>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
	  <form action="<?php echo base_url('master/user/save')?>" method="post">
      <div class="modal-body">
		<div class="row">
			<div class="col-sm-12">
				<div class="card card-danger">
					<div class="card-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label>Username</label>
								<div>
									<input class="form-control input-sm" type="input" id="username" name="username" maxlength="15" style="text-transform: uppercase" placeholder="Input Your Username" required>
									<input class="form-control input-sm" type="hidden" id="input" name="tipe" value="input">
								</div>
							</div>
							<div class="form-group">
								<label>Password</label>
								<div>
									<input class="form-control input-sm" type="password" id="password1" name="passwordweb" required placeholder="Input Password (Harus Memiliki Huruf Besar,Kecil & Simbol)">
								</div>
							</div>
							<div class="form-group">
								<label>Password Again</label>
								<div>
									<input class="form-control input-sm"  type="password" id="password2" name="passwordweb2" required title="Masukan Ulang Password Sama dengan sebelumnya(Harus Memiliki Huruf Besar,Kecil & Simbol)" placeholder="Input Ulang Password (Harus Memiliki Huruf Besar,Kecil & Simbol)">
								</div>
							</div>
                            <?php /*
							<div class="form-group">
								<label class="col-sm-4">LEVEL ID</label>
							<div class="col-sm-8">
									<input type="hidden" class="form-control input-sm-4" value="input" id="tipe" name="tipe" required>
									<select class="form-control input-sm"  id="lvlid" name="lvlid" class="col-sm-12">
									<option value=""><?php echo '--LEVEL ID--';?></option>
										<?php
											foreach ($list_lvljbt as $lk){
												echo '<option value="'.$lk->kdlvl.'">'.$lk->kdlvl.'|'.$lk->nmlvljabatan.'</option>';
											}
										?>
									</select>
							</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">LEVEL AKSES</label>
							<div class="col-sm-8">
									<input type="hidden" class="form-control input-sm-4" value="input" id="tipe" name="tipe" required>
									<select class="form-control input-sm"  id="lvlakses" name="lvlakses" class="col-sm-12">
									<option value=""><?php echo '--LEVEL AKSES--';?></option>
										<?php
											foreach ($list_lvljbt as $lk){
												echo '<option value="'.$lk->kdlvl.'">'.$lk->kdlvl.'|'.$lk->nmlvljabatan.'</option>';
											}
										?>
									</select>
							</div>
							</div>*/ ?>
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
								<label>Expired</label>
									<div>
										<input class="form-control input-sm" type="text" name="expdate" id="dateinputx">
									</div>
							</div>
                            <div class="form-group">
                                <label>Department</label>
                                <div>
                                    <select class="form-control input-sm" name="kddept" id="kddept">
                                        <?php foreach($list_dept as $ldp){?>
                                            <option <?php if (trim($ldp->kddept)===trim($dtl_user['kddept'])) { echo 'selected';}?> value="<?php echo trim($ldp->kddept);?>" ><?php echo $ldp->kddept.' || '.$ldp->nmdept;?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Location/Warehouse</label>
                                <div>
                                    <select class="form-control input-sm" name="loccode" id="loccode">
                                        <?php foreach($list_gudang as $lg){?>
                                            <option <?php if (trim($lg->idlocation)===trim($dtl_user['loccode'])) { echo 'selected';}?> value="<?php echo trim($lg->idlocation);?>" ><?php echo $lg->idlocation.' || '.$lg->nmlocation;?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <div>
                                    <select class="form-control input-sm" name="roleid" id="roleid">
                                        <?php foreach($list_role as $lrl){?>
                                            <option <?php if (trim($lrl->roleid)===trim($dtl_user['roleid'])) { echo 'selected';}?> value="<?php echo trim($lrl->roleid);?>" ><?php echo $lrl->roleid.' || '.$lrl->rolename;?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
						</div>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
			</div>
		</div><!--row-->
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>


<script type="application/javascript" src="<?= base_url('assets/pagejs/master/user.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

        $('#dateinputx').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-M-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#dateinputx').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-M-YYYY'));
        });

        $('#dateinputx').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>