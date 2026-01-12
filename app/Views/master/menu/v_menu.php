<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example3").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        $("#dateinput").datepicker();
        $('#nik').selectize();
    });
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
<div class="col-12 ">
    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab_1-tab" data-bs-toggle="pill" href="#tab_1" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Main Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab_2-tab" data-bs-toggle="pill" href="#tab_2" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Sub Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab_3-tab" data-bs-toggle="pill" href="#tab_3" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Parent Menu</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-one-tabContent">
                <div class="tab-pane fade show active" id="tab_1" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="col-sm-12">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Menu Utama</a>
                            </div>
                        </div><!-- /.card-header -->
                        <div class="card-body table-responsive" style='overflow-x:scroll;'>
                            <table id="example1" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>KODE</th>
                                    <th>Posisi</th>
                                    <th>Nama Modul</th>
                                    <th>Hold</th>
                                    <th>Link Menu</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_menu_utama as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lu->kodemenu;?></td>
                                        <td><?php echo $lu->urut;?></td>
                                        <td><?php echo $lu->namamenu;?></td>
                                        <td><?php echo $lu->holdmenu;?></td>
                                        <td><?php echo $lu->linkmenu;?></td>
                                        <td>
                                            <a href='<?php echo base_url("master/menu/edit/$lu->kodemenu")?>' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <a href='<?php echo base_url("master/menu/hps/$lu->kodemenu")?>' onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-trash-o"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
                <div class="tab-pane fade" id="tab_2" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="col-sm-12">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#input-submenu" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Sub Menu</a>
                            </div>
                        </div><!-- /.card-header -->
                        <div class="card-body table-responsive" style='overflow-x:scroll;'>
                            <table id="example2" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>KODE</th>
                                    <th>Posisi</th>
                                    <th>Nama Modul</th>
                                    <th>Parent Menu</th>
                                    <th>Hold</th>
                                    <th>Link Menu</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_menu_sub as $lus): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lus->kodemenu;?></td>
                                        <td><?php echo $lus->urut;?></td>
                                        <td><?php echo $lus->namamenu;?></td>
                                        <td><?php echo $lus->parentmenu;?></td>
                                        <td><?php echo $lus->holdmenu;?></td>
                                        <td><?php echo $lus->linkmenu;?></td>
                                        <td>
                                            <a href='<?php echo base_url("master/menu/edit/$lus->kodemenu")?>' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <a href='<?php echo base_url("master/menu/hps/$lus->kodemenu")?>' onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-trash-o"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
                <div class="tab-pane fade" id="tab_3" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="col-sm-12">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#input-submenu-sub" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Parent Menu</a>
                            </div>
                        </div><!-- /.card-header -->
                        <div class="card-body table-responsive" style='overflow-x:scroll;'>
                            <table id="example3" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>KODE</th>
                                    <th>Posisi</th>
                                    <th>Nama Modul</th>
                                    <th>Parent Menu</th>
                                    <th>Parent Submenu</th>
                                    <th>Hold</th>
                                    <th>Link Menu</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_menu_submenu as $lusm): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lusm->kodemenu;?></td>
                                        <td><?php echo $lusm->urut;?></td>
                                        <td><?php echo $lusm->namamenu;?></td>
                                        <td><?php echo $lusm->parentmenu;?></td>
                                        <td><?php echo $lusm->parentsub;?></td>
                                        <td><?php echo $lusm->holdmenu;?></td>
                                        <td><?php echo $lusm->linkmenu;?></td>
                                        <td>
                                            <a href='<?php echo base_url("master/menu/edit/$lusm->kodemenu")?>' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <a href='<?php echo base_url("master/menu/hps/$lusm->kodemenu")?>' onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
                                                <i class="fa fa-trash-o"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>



<!--Modal untuk Input Menu Utama-->
<div class="modal fade" id="input" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Input Main Menu</h4>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
	  <form action="<?php echo base_url('master/menu/save')?>" method="post">
      <div class="modal-body">										
		<div class="row">
			<div class="col-sm-12">
				<div class="card card-danger">
					<div class="card-body">
						<div class="form-horizontal">							
							<div class="form-group">
								<label>Kode Menu</label>
								<div>
									<input type="hidden" class="form-control input-sm" value="input" id="tipe" name="tipe" required>									
									<input type="text" class="form-control input-sm" value="" id="tipe" maxlength='10' name="kdmenu" style="text-transform: uppercase" required>
								</div>
							</div>							
							<div class="form-group">
								<label>Nama Menu</label>
								<div>
									<input type="text" class="form-control input-sm" value="" id="tipe" maxlength='25' name="namamenu"  style="text-transform: uppercase" required>
									<input type="hidden" class="form-control input-sm" value="U" id="tipe" maxlength='25' name="childmenu" required>
									<input type="hidden" class="form-control input-sm" value="" id="tipe" maxlength='25' name="parentmenu" required>
								</div>
							</div>
							<div class="form-group">
								<label>Posisi</label>
								<div>
									<input type="number" class="form-control input-sm" id="urut" maxlength='4' name="urut"  style="text-transform: uppercase"  required>
								</div>
							</div>
							<div class="form-group">
                                <label>Hold</label>
                                <div>
                                    <select class="form-control input-sm"  name="hold" class="col-sm-12" style="text-transform: uppercase" >
                                        <option value="F">No</option>;
                                        <option value="T">Yes</option>;
                                    </select>
                                </div>
                            </div>
							<div class="form-group">
								<label>Link Menu</label>
								<div>
									<input type="text" class="form-control input-sm" value="" id="tipe" name="linkmenu" required>
								</div>
							</div>
							<div class="form-group">
								<label>Icon Menu</label>
								<div>
									<input type="text" class="form-control input-sm" value="" id="tipe" name="iconmenu"  required>
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

<!--Modal untuk Input Sub Menu-->
<div class="modal fade" id="input-submenu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Input Sub Menu</h4>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
	  <form action="<?php echo base_url('master/menu/save')?>" method="post">
      <div class="modal-body">										
		<div class="row">
			<div class="col-sm-12">
				<div class="card card-danger">
					<div class="card-body">
						<div class="form-horizontal">							
							<div class="form-group">
								<label>Kode Sub Menu</label>
								<div>
									<input type="hidden" class="form-control input-sm" value="input" id="tipe" name="tipe" required>									
									<input type="hidden" class="form-control input-sm" value="S" id="tipe" maxlength='25' name="childmenu" required>									
									<input type="text" class="form-control input-sm" value="" id="tipe" maxlength='10' name="kdmenu" style="text-transform: uppercase" required>
								</div>
							</div>							
							<div class="form-group">
								<label>Nama Menu</label>
								<div>
									<input type="text" class="form-control input-sm" value="" id="tipe" maxlength='25' name="namamenu" style="text-transform: uppercase" required>
								</div>
							</div>							
							<div class="form-group">
								<label>Main Menu</label>
								<div>
									<select name="parentmenu" class="form-control input-sm"  style="text-transform: uppercase">
										<option value="">-- Pilih Main Menu --</option>
										<?php foreach ($list_menu_opt_utama as $lomu){ ?>
										<option value="<?php echo trim($lomu->kodemenu);?>"><?php echo trim($lomu->namamenu);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label>Position</label>
								<div>
									<input type="number" class="form-control input-sm" id="urut" maxlength='4' name="urut"  style="text-transform: uppercase" required>
								</div>
							</div>
							<div class="form-group">
								<label>Hold</label>
								<div>
									<select name="holdmenu" class="form-control input-sm">
										<option value="F">NO</option>;
										<option value="T">YES</option>;
									</select>
								</div>
							</div>
							<div class="form-group">
								<label>Link Menu</label>
								<div>
									<input type="text" class="form-control input-sm" value="" id="tipe" name="linkmenu" required>
								</div>
							</div>
							<div class="form-group">
								<label>Icon Menu</label>
								<div>
									<input type="text" class="form-control input-sm" value="" id="tipe" name="iconmenu" required>
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

<!--Modal untuk Input SUB-MENU-SUB-->
<div class="modal fade" id="input-submenu-sub" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Input Parent Menu</h4>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
	  <form action="<?php echo base_url('master/menu/save')?>" method="post">
      <div class="modal-body">										
		<div class="row">
			<div class="col-sm-12">
				<div class="card card-danger">
					<div class="card-body">
						<div class="form-horizontal">							
							<div class="form-group">
								<label>KODE MENU</label>
								<div>
									<input type="hidden" class="form-control input-sm" value="input" id="tipe" name="tipe" required>									
									<input type="hidden" class="form-control input-sm" value="P" id="tipe" name="childmenu" required>									
									<input type="text" class="form-control input-sm" value="" id="tipe" maxlength='10' name="kdmenu"  style="text-transform: uppercase" required>
								</div>
							</div>							
							<div class="form-group">
								<label>NAMA MENU</label>
								<div>
									<input type="text" class="form-control input-sm" value="" id="tipe" maxlength='25' name="namamenu"  style="text-transform: uppercase" required>
								</div>
							</div>							
							<div class="form-group">
								<label>PARENT MENU</label>
								<div>
									<select name="parentmenu" id='pmnu' class="form-control input-sm">
										<option value="">-KOSONG-</option>
										<?php foreach ($list_menu_opt_utama as $lom){ ?>
										<option value="<?php echo trim($lom->kodemenu);?>"><?php echo trim($lom->namamenu);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<script type="text/javascript" charset="utf-8">
							  $(function() {	
								$("#pmsu").chained("#pmnu");		
								$("#cjabt").chained("#csubdept");		
							  });
							</script>
							<div class="form-group">
								<label>PARENT SUB MENU</label>
								<div>
									<select name="parentsubmenu" id='pmsu' class="form-control input-sm">
										<option value="">-KOSONG-</option>
										<?php foreach ($list_menu_opt_sub as $lom){ ?>
										<option value="<?php echo trim($lom->kodemenu);?>" class="<?php echo trim($lom->parentmenu);?>"><?php echo trim($lom->namamenu);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label>POSISI</label>
								<div>
									<input type="number" class="form-control input-sm" id="urut" maxlength='4' name="urut"  style="text-transform: uppercase" required>
								</div>
							</div>
							<div class="form-group">
								<label>HOLD</label>
								<div>
									<select name="holdmenu" class="form-control input-sm">
										<option value="F">NO</option>;
										<option value="T">YES</option>;
									</select>
								</div>
							</div>
							<div class="form-group">
								<label>LINK MENU</label>
								<div>
									<input type="text" class="form-control input-sm" value="" id="tipe" name="linkmenu" required>
								</div>
							</div>
							<div class="form-group">
								<label>ICON MENU</label>
								<div>
									<input type="text" class="form-control input-sm" value="" id="tipe" name="iconmenu" required>
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