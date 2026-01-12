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
<h3><?php echo $title; ?></h3>
<?php echo $message; ?>

<div class="row">
	<div class="col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">


							  </div>
							<div>&nbsp </div>
                                <form action="<?php echo base_url('trans/jadwal_new/load_verifikasi_mutasi_jadwalpernik')?>" method="post">
                                <div class="form-group input-sm">
                                    <label class="label-form col-sm-2">NIK Dan Nama Lengkap</label>
                                    <input type="hidden" value="<?php if (isset($dtl['kdregu'])){ echo trim($dtl['kdregu']); } else { echo ''; } ?>" id="kdregu" name="kdregu" style="text-transform:uppercase" maxlength="200" class="form-control" readonly required />
                                    <input type="hidden" value="<?php echo $blnx;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
                                    <input type="hidden" value="<?php echo $thnx;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
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
                                     <div class="box-body table-responsive" style='overflow-x:scroll;'>
                                         <table id="table1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                             <thead>
                                             <tr>
                                                 <th>No.</th>
                                                 <th>KET</th>
                                                 <th>KDREGU</th>
                                                 <th>PERIODE</th>
                                                 <th>1</th>
                                                 <th>2</th>
                                                 <th>3</th>
                                                 <th>4</th>
                                                 <th>5</th>
                                                 <th>6</th>
                                                 <th>7</th>
                                                 <th>8</th>
                                                 <th>9</th>
                                                 <th>10</th>
                                                 <th>11</th>
                                                 <th>12</th>
                                                 <th>13</th>
                                                 <th>14</th>
                                                 <th>15</th>
                                                 <th>16</th>
                                                 <th>17</th>
                                                 <th>18</th>
                                                 <th>19</th>
                                                 <th>20</th>
                                                 <th>21</th>
                                                 <th>22</th>
                                                 <th>23</th>
                                                 <th>24</th>
                                                 <th>25</th>
                                                 <th>26</th>
                                                 <th>27</th>
                                                 <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=28){ ?><th>28</th><?php } ?> <!-- Fleksibel tanggal bro-->
                                                 <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=29){ ?><th>29</th><?php } ?> <!-- Fleksibel tanggal bro-->
                                                 <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=30){ ?><th>30</th><?php } ?> <!-- Fleksibel tanggal bro-->
                                                 <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=31){ ?><th>31</th><?php } ?> <!-- Fleksibel tanggal bro-->


                                             </tr>
                                             </thead>
                                             <tbody>
                                             <?php $no=0; foreach ($list_jadwal as $ls): $no++ ?>
                                                 <tr>
                                                     <td><?php echo $no;?></td>
                                                     <td><?php echo $ls->ket;?></td>
                                                     <td><?php echo $ls->kdregu;?></td>
                                                     <td><?php echo $ls->periode;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl1'])!==trim($tgl1)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl1;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl2'])!==trim($tgl2)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl2;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl3'])!==trim($tgl3)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl3;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl4'])!==trim($tgl4)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl4;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl5'])!==trim($tgl5)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl5;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl6'])!==trim($tgl6)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl6;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl7'])!==trim($tgl7)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl7;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl8'])!==trim($tgl8)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl8;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl9'])!==trim($tgl9)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl9;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl10'])!==trim($tgl10)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl10;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl11'])!==trim($tgl11)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl11;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl12'])!==trim($tgl12)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl12;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl13'])!==trim($tgl13)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl13;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl14'])!==trim($tgl14)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl14;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl15'])!==trim($tgl15)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl15;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl16'])!==trim($tgl16)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl16;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl17'])!==trim($tgl17)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl17;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl18'])!==trim($tgl18)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl18;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl19'])!==trim($tgl19)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl19;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl20'])!==trim($tgl20)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl20;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl21'])!==trim($tgl21)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl21;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl22'])!==trim($tgl22)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl22;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl23'])!==trim($tgl23)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl23;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl24'])!==trim($tgl24)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl24;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl25'])!==trim($tgl25)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl25;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl26'])!==trim($tgl26)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl26;?></td>
                                                     <td <?php if (trim($dtlcucu['tgl27'])!==trim($tgl27)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl27;?></td>
                                                     <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=28){ ?><td <?php if (trim($dtlcucu['tgl28'])!=trim($tgl28)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl28;?></td><?php } ?>			<!-- Fleksibel tanggal bro-->
                                                     <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=29){ ?><td <?php if (trim($dtlcucu['tgl29'])!=trim($tgl29)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl29;?></td><?php } ?>			<!-- Fleksibel tanggal bro-->
                                                     <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=30){ ?><td <?php if (trim($dtlcucu['tgl30'])!=trim($tgl30)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl30;?></td><?php } ?>			<!-- Fleksibel tanggal bro-->
                                                     <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=31){ ?><td <?php if (trim($dtlcucu['tgl31'])!=trim($tgl31)) { echo 'bgcolor="yellow"'; } ?> ><?php echo $ls->tgl31;?></td><?php } ?>			<!-- Fleksibel tanggal bro-->

                                                 </tr>
                                             <?php endforeach ?>
                                             </tbody>
                                         </table>
                                     </div><!-- /.box-body -->
                                 </div>
                                 <div class="row">
                                     <form action="<?php echo base_url('trans/jadwal_new/simpan_verifikasi_mutasi_jadwalpernik')?>" method="post">
                                      <div class="col-md-4" >
                                          <div class="form-group input-sm">
                                              <label class="control-label col-sm-4">Kode Regu Karyawan</label>
                                              <div class="col-sm-8">
                                                  <input type="text" value="<?php if (isset($dtl['kdregu'])){ echo trim($dtl['kdregu']); } else { } ?>" id="kdregu" name="kdregu" style="text-transform:uppercase" maxlength="200" class="form-control" readonly required />
                                                  <input type="hidden" value="<?php if (isset($dtl['nik'])){ echo trim($dtl['nik']); } else { echo ''; } ?>" id="nik" name="nik" style="text-transform:uppercase" maxlength="200" class="form-control" readonly required />
                                                  <input type="hidden" value="<?php echo $blnx;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
                                                  <input type="hidden" value="<?php echo $thnx;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
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

                                                  <input type="text" value="<?php echo $blnx;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly>
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
                                                  <input type="text" value="<?php echo $thnx;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly>
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
                                         <div class="col-sm-12">
                                             <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> PROCESS  </button>
                                             <a href="<?php echo base_url("trans/jadwal_new/clear_verifikasi_mutasi_jadwalpernik")?>"  class="btn btn-default" style="margin:10px; color:#000000;"><i class="fa fa-arrow-left"></i> Kembali</a>
                                             <a href="<?php echo base_url("trans/jadwal_new/finalisasi_verifikasi_mutasi_jadwalpernik")?>"  class="btn btn-success pull-right" style="margin:10px; color:#ffffff;"><i class="fa fa-check"></i> FINAL </a>
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
