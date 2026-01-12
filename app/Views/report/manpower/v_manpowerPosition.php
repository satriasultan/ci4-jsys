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
                    <a href="<?php echo base_url('report/manpower/downloadManpowerPosition') ?>" type="button" class="btn btn-success pull-left"><i class="fa fa-file-excel-o"></i> Unduh Excel </a>

                </div>
                <div class="box-body table-responsive" style='overflow-x:scroll;'>
                <table id="example1" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <td style="width: 594px;" colspan="3"><strong>&nbsp;STRUCTURE MAN POWER POSITION</strong></td>
                        <td style="width: 594px;" colspan="9"><strong>STATUS TETAP</strong></td>
                        <td style="width: 594px;" colspan="5"><strong>PKWT</strong></td>
                        <td style="width: 122px;" rowspan="2"><strong>OUTSOURCE</strong></td>
                        <td style="width: 122px;" rowspan="2"><strong>GRANDTOTAL</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 147.198px;">DEPARTMENT</td>
                        <td style="width: 147.198px;">SECTION</td>
                        <td style="width: 147.198px;">POSITION</td>
                        <td style="width: 147.469px;">PRESDIR</td>
                        <td style="width: 100px;">VICE</td>
                        <td style="width: 100px;">DIR</td>
                        <td style="width: 100px;">GM</td>
                        <td style="width: 100px;">MGR</td>
                        <td style="width: 100px;">AM</td>
                        <td style="width: 100px;">SPV</td>
                        <td style="width: 100px;">STAFF</td>
                        <td style="width: 100px;">OPR</td>

                        <td style="width: 100px;">MGR</td>
                        <td style="width: 100px;">AM</td>
                        <td style="width: 100px;">SPV</td>
                        <td style="width: 100px;">STAFF</td>
                        <td style="width: 100px;">OPR</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach($manpower as $lar): $no++;?>
                        <tr>
                            <td><?php echo $lar->nmdept;?></td>
                            <td><?php echo $lar->nmsubdept;?></td>
                            <td><?php echo $lar->nmjabatan;?></td>
                            <td><?php echo $lar->presdir;?></td>
                            <td><?php echo $lar->vp;?></td>
                            <td><?php echo $lar->dir;?></td>
                            <td><?php echo $lar->gm;?></td>
                            <td><?php echo $lar->mng;?></td>
                            <td><?php echo $lar->am;?></td>
                            <td><?php echo $lar->spv;?></td>
                            <td><?php echo $lar->staff;?></td>
                            <td><?php echo $lar->opr;?></td>
                            <td><?php echo $lar->pkwt_mng;?></td>
                            <td><?php echo $lar->pkwt_am;?></td>
                            <td><?php echo $lar->pkwt_spv;?></td>
                            <td><?php echo $lar->pkwt_staff;?></td>
                            <td><?php echo $lar->pkwt_opr;?></td>
                            <td><?php echo $lar->ttl_os;?></td>
                            <td><?php echo $lar->grandtotal;?></td>

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


<script type="text/javascript">
    $('#example1').dataTable({
        aLengthMenu: [
            [25, 50, 100, 200, -1],
            [25, 50, 100, 200, "All"]
        ],
        iDisplayLength: -1
    });
</script>
