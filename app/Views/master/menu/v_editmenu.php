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

<form action="<?php echo base_url('master/menu/save')?>" method="post">
<div class="row">
	<div class="col-sm-6">
		<div class="card card-danger">
			<div class="card-body">
				<div class="form-horizontal">							
					<div class="form-group">
						<label>KODE MENU</label>
						<div>
							<input type="hidden" class="form-control input-sm" value="edit" id="tipe" name="tipe" required>									
							<input type="text" class="form-control input-sm" value="<?php echo $dtl_menu['kodemenu'];?>" id="tipe" maxlength='10' name="kdmenu" readonly>									
						</div>
					</div>							
					<div class="form-group">
						<label>NAMA MENU</label>
						<div>
							<input type="text" class="form-control input-sm" value="<?php echo trim($dtl_menu['namamenu']);?>" id="tipe" maxlength='25' name="namamenu" required>
						</div>
					</div>	
					<?php if ($dtl_menu['child']=='S' or $dtl_menu['child']=='P'){?>
					<div class="form-group">
						<label>PARENT MENU</label>
						<div>
							<select name="parentmenu" id='pmnu' class="form-control input-sm col-sm-12">
								<?php foreach ($list_menu_opt_utama as $lom){ ?>
								<option value="<?php echo trim($lom->kodemenu);?>" <?php if (trim($dtl_menu['parentmenu'])==trim($lom->kodemenu)) { echo 'selected';}?>><?php echo trim($lom->namamenu);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>					
					<?php }?>
					<script type="text/javascript" charset="utf-8">
							  $(function() {	
								$("#pmsu").chained("#pmnu");		
								$("#cjabt").chained("#csubdept");		
							  });
							</script>
					<?php if ($dtl_menu['child']=='P'){?>
					<div class="form-group">
						<label>PARENT SUBMENU</label>
						<div>
							<select name="parentsubmenu" id='pmsu' class="form-control input-smcol-sm-12">
								<?php foreach ($list_menu_opt_sub as $loms){ ?>
								<option value="<?php echo trim($loms->kodemenu);?>" class="<?php echo trim($loms->parentmenu);?>" <?php if (trim($dtl_menu['parentsub'])==trim($loms->kodemenu)) { echo 'selected';}?>><?php echo trim($loms->namamenu);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>					
					<?php }?>
					<div class="form-group">
						<label>POSISI</label>
						<div>
							<input type="text" class="form-control input-sm" value="<?php echo $dtl_menu['urut'];?>" id="urut" name="urut" required>
						</div>
					</div>
					<div class="form-group">
						<label>HOLD</label>
						<div>
							<select name="holdmenu" class="form-control input-sm col-sm-12">
								<option value="F" <?php if (trim($dtl_menu['holdmenu'])=='f') { echo 'selected';}?>>TIDAK</option>
								<option value="T" <?php if (trim($dtl_menu['holdmenu'])=='t') { echo 'selected';}?>>IYA</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label>LINK MENU</label>
						<div>
							<input type="text" class="form-control input-sm" value="<?php echo $dtl_menu['linkmenu'];?>" id="tipe" name="linkmenu" required>
						</div>
					</div>
					<div class="form-group">
						<label>ICON MENU</label>
						<div>
							<input type="text" class="form-control input-sm" value="<?php echo $dtl_menu['iconmenu'];?>" id="tipe" name="iconmenu" required>
						</div>
					</div>																																			
				</div>
			</div><!-- /.card-body -->													
		</div><!-- /.card --> 
	</div>					
</div>
<div class="row">
	<div class="col-sm-6">		
		<a href="<?php echo base_url('master/menu');?>" class="btn btn-primary" style="margin:10px">Kembali</a>
		<button type='submit' onclick="return confirm('Anda Yakin Ubah Data ini?')" class="btn btn-primary" style="margin:10px">Ubah Data</button>
	</div>
	<div class="col-sm-6">		
		
	</div>
</div>
</form>

