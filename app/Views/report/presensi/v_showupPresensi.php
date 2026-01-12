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
<?php echo $message;?>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
                <div class="box-header">
                    <legend><?php echo $title;?></legend>
                    <a href="<?php echo base_url('report/presensi/downloadPresensi'.'?bln='.$bln.'&thn='.$thn) ?>" type="button" class="btn btn-success pull-left"><i class="fa fa-file-excel-o"></i> Unduh Excel </a>

                </div>
                <div class="box-body table-responsive" style='overflow-x:scroll;'>
                <table id="example1" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <td style="width: 594px;" colspan="4"><strong>&nbsp;LAPORAN PRESENSI KARYAWAN</strong></td>
                        <td colspan="3">&nbsp;TGL 1</td>
                        <td colspan="3">&nbsp;TGL 2</td>
                        <td colspan="3">&nbsp;TGL 3</td>
                        <td colspan="3">&nbsp;TGL 4</td>
                        <td colspan="3">&nbsp;TGL 5</td>
                        <td colspan="3">&nbsp;TGL 6</td>
                        <td colspan="3">&nbsp;TGL 7</td>
                        <td colspan="3">&nbsp;TGL 8</td>
                        <td colspan="3">&nbsp;TGL 9</td>
                        <td colspan="3">&nbsp;TGL 10</td>
                        <td colspan="3">&nbsp;TGL 11</td>
                        <td colspan="3">&nbsp;TGL 12</td>
                        <td colspan="3">&nbsp;TGL 13</td>
                        <td colspan="3">&nbsp;TGL 14</td>
                        <td colspan="3">&nbsp;TGL 15</td>
                        <td colspan="3">&nbsp;TGL 16</td>
                        <td colspan="3">&nbsp;TGL 17</td>
                        <td colspan="3">&nbsp;TGL 18</td>
                        <td colspan="3">&nbsp;TGL 19</td>
                        <td colspan="3">&nbsp;TGL 20</td>
                        <td colspan="3">&nbsp;TGL 21</td>
                        <td colspan="3">&nbsp;TGL 22</td>
                        <td colspan="3">&nbsp;TGL 23</td>
                        <td colspan="3">&nbsp;TGL 24</td>
                        <td colspan="3">&nbsp;TGL 25</td>
                        <td colspan="3">&nbsp;TGL 26</td>
                        <td colspan="3">&nbsp;TGL 27</td>
                        <td colspan="3">&nbsp;TGL 28</td>
                        <td colspan="3">&nbsp;TGL 29</td>
                        <td colspan="3">&nbsp;TGL 30</td>
                        <td colspan="3">&nbsp;TGL 31</td>
                        <td colspan="6">&nbsp;COUNT PRESENSI</td>
                    </tr>
                    <tr>
                        <td style="width: 147.198px;">&nbsp;NIK</td>
                        <td style="width: 152.135px;">NAMA KARYAWAN</td>
                        <td style="width: 147.198px;">DEPARTMENT</td>
                        <td style="width: 147.469px;">TANGGAL MASUK</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>
                        <td style="width: 100px;">&nbsp;IN</td>
                        <td style="width: 100px;">&nbsp;OUT</td>
                        <td style="width: 100px;">&nbsp;KET</td>

                        <td>&nbsp;SAKIT</td>
                        <td>&nbsp;IZIN</td>
                        <td>&nbsp;CUTI</td>
                        <td>&nbsp;TELAT</td>
                        <td>&nbsp;P.AWAL</td>
                        <td>&nbsp;ALPHA</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach($listpresensi as $lar): $no++;?>
                        <tr>
                            <td width="2%"><?php echo $lar->nik;?></td>
                            <td><?php echo $lar->nmlengkap;?></td>
                            <td><?php echo $lar->nmdept;?></td>
                            <td><?php echo $lar->tglmasukkerja1;?></td>

                            <td><?php echo $lar->tgl_in_1;?></td>
                            <td><?php echo $lar->tgl_out_1;?></td>
                            <td><?php echo $lar->tgl1;?></td>

                            <td><?php echo $lar->tgl_in_2;?></td>
                            <td><?php echo $lar->tgl_out_2;?></td>
                            <td><?php echo $lar->tgl2;?></td>

                            <td><?php echo $lar->tgl_in_3;?></td>
                            <td><?php echo $lar->tgl_out_3;?></td>
                            <td><?php echo $lar->tgl3;?></td>

                            <td><?php echo $lar->tgl_in_4;?></td>
                            <td><?php echo $lar->tgl_out_4;?></td>
                            <td><?php echo $lar->tgl4;?></td>

                            <td><?php echo $lar->tgl_in_5;?></td>
                            <td><?php echo $lar->tgl_out_5;?></td>
                            <td><?php echo $lar->tgl5;?></td>

                            <td><?php echo $lar->tgl_in_6;?></td>
                            <td><?php echo $lar->tgl_out_6;?></td>
                            <td><?php echo $lar->tgl6;?></td>

                            <td><?php echo $lar->tgl_in_7;?></td>
                            <td><?php echo $lar->tgl_out_7;?></td>
                            <td><?php echo $lar->tgl7;?></td>

                            <td><?php echo $lar->tgl_in_8;?></td>
                            <td><?php echo $lar->tgl_out_8;?></td>
                            <td><?php echo $lar->tgl8;?></td>

                            <td><?php echo $lar->tgl_in_9;?></td>
                            <td><?php echo $lar->tgl_out_9;?></td>
                            <td><?php echo $lar->tgl9;?></td>

                            <td><?php echo $lar->tgl_in_10;?></td>
                            <td><?php echo $lar->tgl_out_10;?></td>
                            <td><?php echo $lar->tgl10;?></td>

                            <td><?php echo $lar->tgl_in_11;?></td>
                            <td><?php echo $lar->tgl_out_11;?></td>
                            <td><?php echo $lar->tgl11;?></td>

                            <td><?php echo $lar->tgl_in_12;?></td>
                            <td><?php echo $lar->tgl_out_12;?></td>
                            <td><?php echo $lar->tgl12;?></td>

                            <td><?php echo $lar->tgl_in_13;?></td>
                            <td><?php echo $lar->tgl_out_13;?></td>
                            <td><?php echo $lar->tgl13;?></td>

                            <td><?php echo $lar->tgl_in_14;?></td>
                            <td><?php echo $lar->tgl_out_14;?></td>
                            <td><?php echo $lar->tgl14;?></td>

                            <td><?php echo $lar->tgl_in_15;?></td>
                            <td><?php echo $lar->tgl_out_15;?></td>
                            <td><?php echo $lar->tgl15;?></td>

                            <td><?php echo $lar->tgl_in_16;?></td>
                            <td><?php echo $lar->tgl_out_16;?></td>
                            <td><?php echo $lar->tgl16;?></td>

                            <td><?php echo $lar->tgl_in_17;?></td>
                            <td><?php echo $lar->tgl_out_17;?></td>
                            <td><?php echo $lar->tgl17;?></td>

                            <td><?php echo $lar->tgl_in_18;?></td>
                            <td><?php echo $lar->tgl_out_18;?></td>
                            <td><?php echo $lar->tgl18;?></td>

                            <td><?php echo $lar->tgl_in_19;?></td>
                            <td><?php echo $lar->tgl_out_19;?></td>
                            <td><?php echo $lar->tgl19;?></td>

                            <td><?php echo $lar->tgl_in_20;?></td>
                            <td><?php echo $lar->tgl_out_20;?></td>
                            <td><?php echo $lar->tgl20;?></td>

                            <td><?php echo $lar->tgl_in_21;?></td>
                            <td><?php echo $lar->tgl_out_21;?></td>
                            <td><?php echo $lar->tgl21;?></td>

                            <td><?php echo $lar->tgl_in_22;?></td>
                            <td><?php echo $lar->tgl_out_22;?></td>
                            <td><?php echo $lar->tgl22;?></td>

                            <td><?php echo $lar->tgl_in_23;?></td>
                            <td><?php echo $lar->tgl_out_23;?></td>
                            <td><?php echo $lar->tgl23;?></td>

                            <td><?php echo $lar->tgl_in_24;?></td>
                            <td><?php echo $lar->tgl_out_24;?></td>
                            <td><?php echo $lar->tgl24;?></td>

                            <td><?php echo $lar->tgl_in_25;?></td>
                            <td><?php echo $lar->tgl_out_25;?></td>
                            <td><?php echo $lar->tgl25;?></td>

                            <td><?php echo $lar->tgl_in_26;?></td>
                            <td><?php echo $lar->tgl_out_26;?></td>
                            <td><?php echo $lar->tgl26;?></td>

                            <td><?php echo $lar->tgl_in_27;?></td>
                            <td><?php echo $lar->tgl_out_27;?></td>
                            <td><?php echo $lar->tgl27;?></td>

                            <td><?php echo $lar->tgl_in_28;?></td>
                            <td><?php echo $lar->tgl_out_28;?></td>
                            <td><?php echo $lar->tgl28;?></td>

                            <td><?php echo $lar->tgl_in_29;?></td>
                            <td><?php echo $lar->tgl_out_29;?></td>
                            <td><?php echo $lar->tgl29;?></td>

                            <td><?php echo $lar->tgl_in_30;?></td>
                            <td><?php echo $lar->tgl_out_30;?></td>
                            <td><?php echo $lar->tgl30;?></td>

                            <td><?php echo $lar->tgl_in_31;?></td>
                            <td><?php echo $lar->tgl_out_31;?></td>
                            <td><?php echo $lar->tgl31;?></td>

                            <td><?php echo $lar->sakit;?></td>
                            <td><?php echo $lar->ijin;?></td>
                            <td><?php echo $lar->cuti;?></td>
                            <td><?php echo $lar->dt;?></td>
                            <td><?php echo $lar->pa;?></td>
                            <td><?php echo $lar->al;?></td>


                        </tr>
                    <?php endforeach;?>
                    </tbody>

                </table>
            </div><!-- /.box-body -->
                <div class="box-footer">
                    <a href="<?php echo base_url('report/presensi')?>" type="button" class="btn btn-primary pull-left"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                </div>
        </div>
    </div>

    <?php /*
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <div class="col-xs-12">
                    <h4>DOWNLOAD XLS</h4>
                </div>
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <!--form action="<?php echo base_url('trans/absensi/report_absensi');?>" name="form" role="form" method="post"-->
                    <form action="#"  id="downloadform">
                        <!--area-->
                        <div class="form-group">
                            <label class="label-form col-sm-3">Bulan</label>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" name='bln' id='blndl' required>
                                    <option value="01" <?php $tgl=date('m'); if($tgl=='01') echo "selected"; ?>>Januari</option>
                                    <option value="02" <?php $tgl=date('m'); if($tgl=='02') echo "selected"; ?>>Februari</option>
                                    <option value="03" <?php $tgl=date('m'); if($tgl=='03') echo "selected"; ?>>Maret</option>
                                    <option value="04" <?php $tgl=date('m'); if($tgl=='04') echo "selected"; ?>>April</option>
                                    <option value="05" <?php $tgl=date('m'); if($tgl=='05') echo "selected"; ?>>Mei</option>
                                    <option value="06" <?php $tgl=date('m'); if($tgl=='06') echo "selected"; ?>>Juni</option>
                                    <option value="07" <?php $tgl=date('m'); if($tgl=='07') echo "selected"; ?>>Juli</option>
                                    <option value="08" <?php $tgl=date('m'); if($tgl=='08') echo "selected"; ?>>Agustus</option>
                                    <option value="09" <?php $tgl=date('m'); if($tgl=='09') echo "selected"; ?>>September</option>
                                    <option value="10" <?php $tgl=date('m'); if($tgl=='10') echo "selected"; ?>>Oktober</option>
                                    <option value="11" <?php $tgl=date('m'); if($tgl=='11') echo "selected"; ?>>November</option>
                                    <option value="12" <?php $tgl=date('m'); if($tgl=='12') echo "selected"; ?>>Desember</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="label-form col-sm-3">Tahun</label>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" name="thn" id='thndl' required>
                                    <option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
                                    <option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
                                    <option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>
                                </select>
                            </div>
                        </div>
                    </form>
                        <button onclick="download()" id='download' class='btn btn-success' ><i class="glyphicon glyphicon-search"></i> Download</button>
                    </div>
            </div>
        </div>
    </div>
 */ ?>
</div>


<script type="text/javascript" src="<?= base_url('assets/pagejs/report/report_hr.js') ?>"></script>
