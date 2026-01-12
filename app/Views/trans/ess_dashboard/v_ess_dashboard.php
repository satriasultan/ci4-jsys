<?php
/*
 * Author: Fiky Ashariza
 * Create Date: 8/29/21, 4:17 PM
 * Path Directory: v_ess_dashboard.php
 */

?>

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
<!--h3><?php echo $title; ?></h3-->
<?php echo $message;?>
<style>
    .lbprofile{
        font-size: 1.1em;
        font-weight: 700;
        /*font-family: "Times New Roman";*/
    }
    .lbprofile2{
        font-size: 1em;
       /* font-family: "Times New Roman";*/
    }
    .lbprofile3{
        font-size: 4em;
        /*font-family: "Times New Roman";*/
        font-weight: bold;
    }
    .form-group {
        margin-bottom: 0px;
    }
    .small-box h3 {
        font-size: 50px;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }

    .small-box {
        margin-bottom: 0px;
    }
    .small-box > .small-box-footer {
        position: relative;
        text-align: center;
        padding: 10px 0;
        color: #fff;
        color: rgba(255, 255, 255, 0.8);
        display: block;
        z-index: 10;
        background: rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }
</style>

<div class="row">
    <?php /*
	<div class="col-xs-9">
        <div class="box box-solid box-default col-xs-12">
            <div class="box-header">
                <h3 class="box-title">Informasi Pribadi / Profile</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Nama</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nmlengkap']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Nik</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nik']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Tanggal lahir</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= date('d-m-Y',strtotime(trim($dtlkary['tgllahir']))).'' ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Departemen</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nmdept']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Section</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nmsubdept']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Position</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nmjabatan']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Plant</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['locaname']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">No HP Aktif</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nohp1']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Masuk Tanggal</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= date('d-m-Y',strtotime(trim($dtlkary['tglmasukkerja']))).'' ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Sisa Cuti</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= $dtlkary['sisacuti'] ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Atasan 1</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nmatasan1']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Atasan 2</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nmatasan2']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">Atasan 3</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nmatasan3']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">KTP</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['id_ktp']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">KK</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['nokk']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">NPWP</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['id_npwp']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">STATUS PTKP</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['sts_ptkp']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">ID BPJS Kesehatan</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['id_bpjs_kes']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">ID BPJS Naker</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['id_bpjs_naker']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">ALAMAT KTP</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['desc_ktp']) ?></label>
                    </div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-3">
                        <label for="1" class="lbprofile">ALAMAT DOMISILI</label>
                    </div>
                    <div class="col-xs-1">
                        <label for="2" class="lbprofile" >:</label>
                    </div>
                    <div class="col-xs-8">
                        <label for="3" class="lbprofile" ><?= trim($dtlkary['desc_domisili']) ?></label>
                    </div>
                </div>
            </div><!-- /.box-body -->
        </div>
        */ ?>
    <div class="col-xs-9">
        <div class="box box-solid box-default col-xs-12">
            <div class="box-header">
                <h3 class="box-title">Informasi Pribadi / Profile</h3>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive" style='overflow-x:scroll;'>
            <table id="#" class="table table-striped" >
                <thead>
                <tr>
                    <th width="20%"></th>
                    <th width="2%"></th>
                    <th width="88%"></th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Nama'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nmlengkap']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Nik'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nik']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Tanggal lahir'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= date('d-m-Y',strtotime(trim($dtlkary['tgllahir']))).'' ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Departemen'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nmdept']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Section'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nmsubdept']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Position'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nmjabatan']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Plant'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['locaname']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'No HP Aktif'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nohp1']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Masuk Tanggal'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= date('d-m-Y',strtotime(trim($dtlkary['tglmasukkerja']))).'' ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Sisa Cuti'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= $dtlkary['sisacuti'] ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Atasan 1'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nmatasan1']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Atasan 2'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nmatasan2']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Atasan 3'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nmatasan3']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'KTP'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['id_ktp']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'KK'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['nokk']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'NPWP'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['id_npwp']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'PTKP'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['sts_ptkp']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'ID BPJS Kesehatan'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['id_bpjs_kes']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'ID BPJS Naker'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['id_bpjs_naker']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Alamat KTP'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['desc_ktp']) ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="lbprofile"><?php echo 'Alamat Domisili'; ?></td>
                        <td width="2%" class="lbprofile"><?php echo ':'; ?></td>
                        <td width="88%" class="lbprofile"><?= trim($dtlkary['desc_domisili']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div><!-- /.box-body -->
        </div>
	</div>
    <div class="col-xs-3">
        <div class="box box-solid box-warning">
            <div class="box-header">
                <h3 class="box-title">Notifikasi Persetujuan</h3>
            </div><!-- /.box-header -->
            <div class="small-box" style="background-color: #baf100">
                <div class="inner">
                    <h3><span id="sup_positif"><?php echo $count_approval; ?></span></h3>
                    <p>DOKUMEN</p>
                </div>
                <div class="icon">
                    <i class="ion ion-checkmark"></i>
                </div>
                <a href="<?= base_url('/trans/ess_persetujuan') ?>" class="small-box-footer">  <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <?php /*
        <div class="box box-solid box-default">
            <div class="box-header">
                <h3 class="box-title">Daftar Hari Libur Tahun <?= date('Y') ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="box-body table-responsive" style='overflow-x:scroll;'>
                    <table id="example1" class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no=0; foreach($list_libur as $lu): $no++;?>
                            <tr>
                                <td><?php echo date('d-m-Y',strtotime($lu->tgl_libur));?></td>
                                <td><?php echo trim($lu->ket_libur);?></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div>
        </div>
        */ ?>
    </div>
</div>
<div class="row">

</div>


<script type="text/javascript">
    $(function() {
        $("#example1").dataTable({ "pageLength": 5,"lengthMenu": [[5, 10, -1], [5, 10, "All"]] });
    });
</script>
<script type="text/javascript" src="<?= base_url('assets/pagejs/trans/ess_dashboard.js') ?>"></script>




