<!--trans/jadwal_new/proses_xls_jadwal-->

<script type="text/javascript">
    $(function() {
        $("#example1").dataTable({ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
        $("#example2").dataTable({ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
        $("#example3").dataTable({ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
        $("#dateinput").datepicker();
        $("#dateinput1").datepicker();
        $("#dateinput2").datepicker();
        $("#dateinput3").datepicker();
        $("[data-mask]").inputmask();
        $('#pilihkaryawan').selectize();
    });
</script>
<script>
    /*$(document).ready(function() {
        function disableBack() { window.history.forward() }

        window.onload = disableBack();
        window.onpageshow = function(evt) { if (evt.persisted) disableBack() }
    });*/
</script>

<ol class="breadcrumb">
    <div class="pull-right"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
    <?php foreach ($y as $y1) { ?>
        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
            <li><a href="<?php echo site_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
        <?php } else { ?>
            <li class="active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
        <?php } ?>
    <?php } ?>
</ol>
<h3><?php echo $title; ?></h3>
<?php echo $message;?>

<div class="row">
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
            </div><!-- /.box-header -->
            <!-- form start -->
            <form action="<?php echo base_url('trans/jadwal_new/proses_xls_jadwal_nik')?>" method="post" enctype="multipart/form-data" role="form">
                <div class="box-body">
                    <div class="form-group">
                        <label for="exampleInputFile">File Import Jadwal Kerja Karyawan</label>
                        <input type="file" id="import" name="import" required>
                        <p class="help-block">Data Harus Berextensi xls/x (Excel).</p>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" required> Saya Bertanggung Jawab atas data yang saya Upload ke Sistem
                        </label>
                    </div>
                </div><!-- /.box-body -->

                <div class="box-footer">
                    <button type="submit" value="Import" name="save" class="btn btn-success"><i class="fa fa-sign-in"></i> Proses Excel </button>
                    <a href="<?= base_url('assets/files/jadwalkerja/jadwalregu/sample/template_jadwalkerja_nik.xlsx') ?>" class="btn btn-default"><i class="fa fa-file-excel-o"></i> Download Template </a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#aturanMain" class="btn btn-default pull-right"><i class="fa fa-question-circle"></i></a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#jamKerja" class="btn btn-default pull-right" title="Jam Kerja Karyawan"><i class="fa fa-clock-o"></i></a>
                    <?php if ($adaisi>0) { ?>
                        <a href="<?= base_url('trans/jadwal_new/clear_tmp_jadwal_per_nik') ?>" class="btn btn-danger pull-right"><i class="fa fa-trash-o"></i> Clear Data </a>
                        <a href="<?= base_url('trans/jadwal_new/final_data_jadwal_per_nik') ?>" class="btn btn-primary pull-right"><i class="fa fa-sign-in"></i> Final Data </a>
                    <?php } ?>

                </div>
            </form>
        </div><!-- /.box -->
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
            </div><!-- /.box-header -->
            <div class="box-body table-responsive" style='overflow-x:scroll;'>
                <table id="#" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nik</th>
                        <th>Nama</th>
                        <th>BLN/THN</th>
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
                    <?php $no=0; foreach ($list_tmp_jadwalkerja_import as $ls): $no++ ?>
                        <tr>
                            <td><?php echo $no;?></td>
                            <td><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->nik)."</b></div>"; ?></td>
                            <td><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->nmlengkap)."</b></div>"; ?></td>
                            <td><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->bulan.'/'.$ls->tahun)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl1)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl1)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl1)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl2)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl2)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl2)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl3)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl3)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl3)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl4)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl4)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl4)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl5)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl5)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl5)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl6)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl6)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl6)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl7)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl7)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl7)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl8)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl8)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl8)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl9)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl9)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl9)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl10)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl10)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl10)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl11)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl11)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl11)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl12)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl12)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl12)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl13)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl13)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl13)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl14)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl14)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl14)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl15)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl15)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl15)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl16)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl16)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl16)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl17)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl17)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl17)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl18)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl18)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl18)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl19)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl19)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl19)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl20)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl20)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl20)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl21)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl21)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl21)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl22)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl22)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl22)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl23)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl23)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl23)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl24)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl24)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl24)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl25)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl25)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl25)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl26)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl26)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl26)."</b></div>"; ?></td>
                            <td <?php if (trim($ls->tgl27)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl27)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl27)."</b></div>"; ?></td>
                            <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=28){ ?><td <?php if (trim($ls->tgl28)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl28)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl28)."</b></div>"; ?></a></td><?php } ?>			<!-- Fleksibel tanggal bro-->
                            <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=29){ ?><td <?php if (trim($ls->tgl29)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl29)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl29)."</b></div>"; ?></a></td><?php } ?>			<!-- Fleksibel tanggal bro-->
                            <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=30){ ?><td <?php if (trim($ls->tgl30)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl30)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl30)."</b></div>"; ?></a></td><?php } ?>			<!-- Fleksibel tanggal bro-->
                            <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=31){ ?><td <?php if (trim($ls->tgl31)=='OFF') { ?> bgcolor="#FF0000" <?php } else if (trim($ls->tgl31)=='UNM') { ?> bgcolor="#FFE333" <?php } ?>><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($ls->tgl31)."</b></div>"; ?></a></td><?php } ?>			<!-- Fleksibel tanggal bro-->

                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>

<!--Modal untuk Help Desk-->
<div class="modal fade" id="aturanMain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Help Desk Center ?</h4>
            </div>
            <div class="modal-body">
                <h5>
                    <p> Aturan untuk melakukan import jadwalkerja </p>
                    <p> 1. Semua area kolom format harus berbentuk text. </p>
                    <p> 2. Kode regu harus sudah terdaftar di master regu, jika tidak regu tidak akan teridentifikasi. </p>
                    <p> 3. File harus berupa file excel xls/xlsx. </p>
                    <p> 4. Pastikan entrian data dengan kolom yang disediakan dengan format tanggal dsb sama dengan contoh template. </p>
                    <p> 5. Pastikan entrian data 1 Nik tidak ada double/kembar. </p>
                </h5>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!--Modal untuk Help Desk-->
<div class="modal fade" id="jamKerja" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Referensi Jam Kerja Karyawan</h4>
            </div>
            <div class="modal-body">
                <table id="#" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Kode</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Keterangan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach ($list_jamkerja as $row): $no++ ?>
                        <tr>
                            <td><?php echo $no;?></td>
                            <td><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($row->kdjam_kerja)."</b></div>"; ?></td>
                            <td><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($row->jam_masuk)."</b></div>"; ?></td>
                            <td><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($row->jam_pulang)."</b></div>"; ?></td>
                            <td><?php echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#000000'><b>".($row->nmjam_kerja)."</b></div>"; ?></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>