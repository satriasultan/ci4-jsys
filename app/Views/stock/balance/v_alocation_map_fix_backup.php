<style>
    .color-palette {
        height: 35px;
        line-height: 35px;
        text-align: right;
        padding-right: .75rem;
        color: #ffffff;
    }

    .color-palette.disabled {
        text-align: center;
        padding-right: 0;
        display: block;
        color: #0a0e14;
    }

    .color-palette-set {
        margin-bottom: 15px;
        color: #0a0e14;
    }

    .color-palette span {
        display: none;
        font-size: 12px;
        color: #0a0e14;
    }

    .color-palette:hover span {
        display: block;
        color: #0a0e14;
    }

    .color-palette.disabled span {
        display: block;
        text-align: left;
        padding-left: .75rem;
        color: #0a0e14;
    }

    .color-palette-box h4 {
        position: absolute;
        left: 1.25rem;
        margin-top: .75rem;
        color: rgba(0, 0, 0, 0.8);
        font-size: 12px;
        display: block;
        z-index: 7;
        color: #0a0e14;
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1>
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

</div>
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-header">
                <h4 class="text-center display-5"><?php echo $title ?></h4>
                <div class="row">
                    <div class="col-md-10 offset-md-2">
                            <div class="input-group">
                                <input type="text" id="searchalocation" class="form-control form-control-md" placeholder="Scan or type item id/barcode to search the location">
                                <div class="input-group-append">
                                    <button class="btn btn-md btn-default">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <input type="text" id="nmbarang" style="font-weight: bolder" class="form-control form-control-md" placeholder="Item Name" disabled>
                            </div>
                    </div>
                </div>
			</div><!-- /.card-header -->
            <div class="card-body">
               <div class="row">
                   <div class="col-sm-2 col-md-2">
                       <div class="color-palette-set">
                           <div class="disabled color-palette bg-white" style="width: 161px; height: 180px;" id="transit"><span></span></div>
                           <div class="disabled color-palette bg-yellow" style="width: 161px; height: 38px;"><span>Area Transit</span></div>
                       </div>
                   </div>
                   <div class="col-sm-10 col-sm-0">
                       <div class="row">
                           <div class="color-palette-set">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="A1"><span>A1</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="B1"><span>B1</span></div>
                           </div>
                           <div class="color-palette-set">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="A2"><span>A2</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="B2"><span>B2</span></div>
                           </div>
                           <div class="color-palette-set">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="A3"><span>A3</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="B3"><span>B3</span></div>
                           </div>
                           <div class="color-palette-set">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="A4"><span>A4</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="B4"><span>B4</span></div>
                           </div>
                           <div class="color-palette-set">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="A5"><span>A5</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="B5"><span>B5</span></div>
                           </div>
                       </div>
                       <div class="row">
                           <div class="color-palette-set">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="C1"><span>C1</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="D1"><span>D1</span></div>
                           </div>
                           <div class="color-palette-set">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="C2"><span>C2</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="D2"><span>D2</span></div>
                           </div>
                           <div class="color-palette-set">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="C3"><span>C3</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="D3"><span>D3</span></div>
                           </div>
                           <div class="color-palette-set">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="C4"><span>C4</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="D4"><span>D4</span></div>
                           </div>
                           <div class="color-palette-set .cd5">
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="C5"><span>C5</span></div>
                               <div class="disabled color-palette bg-white" style="width: 50px; height: 50px;" id="D5"><span>D5</span></div>
                           </div>
                       </div>
                   </div>

               </div>

            </div>
		</div><!-- /.card -->
	</div>
</div>




<!--Modal untuk Input-->
<div class="modal fade" id="input" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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


<script type="application/javascript" src="<?= base_url('assets/pagejs/stock/balance.js') ?>"></script>
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