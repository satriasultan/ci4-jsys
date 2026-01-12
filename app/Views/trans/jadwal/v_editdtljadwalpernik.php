<?php 
/*
	@author : Fiky
*/
?>

<script type="text/javascript">
            $(function() {
                $("#table1").dataTable();
                $("#example2").dataTable();
                $("#example3").dataTable();                             
				$("#dateinput").datepicker();                               
				$("#dateinput1").datepicker(); 
				$("#dateinput2").datepicker(); 
				$("#dateinput3").datepicker(); 
				$("[data-mask]").inputmask();	
				//$('#kdregu').prop('disabled',true);
				//$('#bln1').prop('disabled',true);
				//$('#thn1').prop('disabled',true);
            });
		
</script>
<legend><?php echo $title;?></legend>
<?php// echo $message;?>

<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		

								
							  </div>
							<div>&nbsp </div>
                                <form action="<?php echo site_url('trans/jadwal_new/edit_jadwalpernik')?>" method="post">
                                <div class="form-group input-sm">
                                    <label class="label-form col-sm-2">NIK Dan Nama Lengkap</label>
                                    <input type="hidden" value="<?php if (isset($dtl['kdregu'])){ echo trim($dtl['kdregu']); } else { echo ''; } ?>" id="kdregu" name="kdregu" style="text-transform:uppercase" maxlength="200" class="form-control" readonly required />
                                    <input type="hidden" value="<?php echo $bln;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
                                    <input type="hidden" value="<?php echo $thn;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm sl" name="nik" required>
                                            <option value=""> --- PILIH NIK KARYAWAN --- </option>
                                            <?php foreach ($list_karyawan as $ld){ ?>
                                                <option <?php if (isset($dtl['nik'])){ $niknya=trim($dtl['nik']); } else { $niknya=''; } if ($niknya==trim($ld->nik)) { echo 'selected';} ?> value="<?php echo trim($ld->nik);?>"><?php echo trim($ld->nik).' || '.$ld->nmlengkap;?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="submit" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Load </button>
                                    </div>
                                </div>
                                </form>

                                 <div class="row">
                                     <form action="<?php echo site_url('trans/jadwal_new/simpan_edit_dtljadwalkerja')?>" method="post">
                                      <div class="col-md-4" >
                                          <div class="form-group input-sm">
                                              <label class="control-label col-sm-4">Kode Regu Karyawan</label>
                                              <div class="col-sm-8">
                                                  <input type="text" value="<?php if (isset($dtl['kdregu'])){ echo trim($dtl['kdregu']); } else { } ?>" id="kdregu" name="kdregu" style="text-transform:uppercase" maxlength="200" class="form-control" readonly required />
                                                  <input type="hidden" value="<?php if (isset($dtl['nik'])){ echo trim($dtl['nik']); } else { echo ''; } ?>" id="nik" name="nik" style="text-transform:uppercase" maxlength="200" class="form-control" readonly required />
                                                  <input type="hidden" value="<?php echo $bln;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
                                                  <input type="hidden" value="<?php echo $thn;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
                                              </div>
                                          </div>
                                          <div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 1</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl1" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>

                                                          <option <?php if ($tgl1==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 2</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl2" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>

                                                          <option <?php if ($tgl2==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 3</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl3" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl3==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 4</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl4" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl4==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 5</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm"  name="tgl5" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl5==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 6</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl6" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl6==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 7</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl7" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl7==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 8</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl8" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl8==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="col-md-4">
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 9</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl9" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl9==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 10</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm"  name="tgl10" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl10==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                      </div>
                                      </div>
                                      <div class="col-md-4" >
                                          <div class="form-group input-sm ">
                                              <label class="label-form col-sm-4">Bulan</label>
                                              <div class="col-sm-8">

                                                  <input type="text" value="<?php echo $bln;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly></input>
                                                  <!--select class="form-control input-sm" id="bln1" name='bln' required readonly >

                                                        <option value="01" <?php $tgl=$bln; if($tgl=='01') echo "selected"; ?>>Januari</option>
                                                        <option value="02" <?php $tgl=$bln; if($tgl=='02') echo "selected"; ?>>Februari</option>
                                                        <option value="03" <?php $tgl=$bln; if($tgl=='03') echo "selected"; ?>>Maret</option>
                                                        <option value="04" <?php $tgl=$bln; if($tgl=='04') echo "selected"; ?>>April</option>
                                                        <option value="05" <?php $tgl=$bln; if($tgl=='05') echo "selected"; ?>>Mei</option>
                                                        <option value="06" <?php $tgl=$bln; if($tgl=='06') echo "selected"; ?>>Juni</option>
                                                        <option value="07" <?php $tgl=$bln; if($tgl=='07') echo "selected"; ?>>Juli</option>
                                                        <option value="08" <?php $tgl=$bln; if($tgl=='08') echo "selected"; ?>>Agustus</option>
                                                        <option value="09" <?php $tgl=$bln; if($tgl=='09') echo "selected"; ?>>September</option>
                                                        <option value="10" <?php $tgl=$bln; if($tgl=='10') echo "selected"; ?>>Oktober</option>
                                                        <option value="11" <?php $tgl=$bln; if($tgl=='11') echo "selected"; ?>>November</option>
                                                        <option value="12" <?php $tgl=$bln; if($tgl=='12') echo "selected"; ?>>Desember</option>
                                                    </select-->
                                              </div>
                                          </div>
                                          <div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 11</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl11" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl11==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 12</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl12" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl12==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 13</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl13" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl13==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 14</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl14" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl14==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 15</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl15" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl15==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>

                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 16</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl16" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl16==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 17</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl17" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl17==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 18</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl18" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl18==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 19</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl19" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl19==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 20</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl20" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl20==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                      </div>
                                      </div>
                                      <div class="col-md-4">
                                          <div class="form-group input-sm ">
                                              <label class="label-form col-sm-4">Tahun</label>
                                              <div class="col-sm-8">
                                                  <input type="text" value="<?php echo $thn;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly></input>
                                                  <!--select class="form-control input-sm" id="thn1" name="thn" required readonly >
                                                    <option value="<?php echo $thn; ?>"><?php echo $thn; ?></option>

                                                </select-->
                                              </div>
                                          </div>
                                          <div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 21</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl21" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl21==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 22</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl22" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl22==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 23</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl23" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl23==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 24</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl24" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl24==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 25</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl25" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl25==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 26</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl26" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl26==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div><div class="form-group input-sm">
                                              <label class="label-form col-sm-4">Tgl 27</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl27" required>
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl27==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <?php $a_date=$thn.'-'.$bln;$cekdate=date("t",strtotime($a_date)); if($cekdate>=28){ ?>
                                          <div class="form-group input-sm tgl28" id="tgl28">
                                              <label class="label-form col-sm-4">Tgl 28</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl28" >
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl28==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <?php } ?>
                                          <?php $a_date=$thn.'-'.$bln;$cekdate=date("t",strtotime($a_date)); if($cekdate>=29){ ?>
                                          <div class="form-group input-sm tgl29" id="tgl29">
                                              <label class="label-form col-sm-4">Tgl 29</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl29" >
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl29==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <?php } ?>
                                          <?php $a_date=$thn.'-'.$bln;$cekdate=date("t",strtotime($a_date)); if($cekdate>=30){ ?>
                                          <div class="form-group input-sm tgl30" id="tgl30">
                                              <label class="label-form col-sm-4">Tgl 30</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl30" >
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl30==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <?php } ?>
                                          <?php $a_date=$thn.'-'.$bln;$cekdate=date("t",strtotime($a_date)); if($cekdate>=31){ ?>
                                          <div class="form-group input-sm tgl31" id="tgl31">
                                              <label class="label-form col-sm-4">Tgl 31</label>
                                              <div class="col-sm-8">
                                                  <select class="form-control input-sm" name="tgl31" >
                                                      <option value="OFF">OFF</option>
                                                      <?php foreach ($list_jamkerja as $ld){ ?>
                                                          <option <?php if ($tgl31==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>" ><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?> </option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <?php } ?>

                                      </div>
                                         <div class="col-sm-3">
                                             <button type="submit" class="btn btn-warning">SIMPAN</button>
                                             <a href="<?php echo site_url("trans/jadwal_new/index")?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Kembali</a>
                                         </div>
                                     </form>
                                      </div>
                                  </div>



									</div>

								 
									

					
			</div><!-- /.box-header -->
		
		</div><!-- /.box -->								
	</div>
</div>

 <script>
 
	//Date range picker
    $('#tgl').datepicker();
    $('#tgl2').datepicker();
	$('#pilihkaryawan').selectize();
	$('#pilihkaryawan2').selectize();
	$('.sl').selectize();
	//$('#thn1').selectize();
	//$('#bln1').selectize();
	//$('#kdregu').selectize();
	$('#kdregu2').selectize();
	$("[data-mask]").inputmask();
	$("#nmjamkerja1").chained("#kdjamkerja1");		
	$("#disb").chained("#city");	

</script>