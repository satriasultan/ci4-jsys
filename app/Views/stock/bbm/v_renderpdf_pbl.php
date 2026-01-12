<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/adminlte.min.css') ?>">
    <!--link rel="stylesheet" href="style.css"-->
    <style> .ratakanan { text-align : right; } </style>
    <style>
        @page { margin: 0px;
        padding-right: 10px}
        body { margin: 0px; padding-right: 10px}

    </style>
    <title>Receipt example</title>
</head>
<body>
<!-- Main content -->
<!-- Table row -->
<!--a href="#" onclick="return sendUrlToPrint('<?php echo base_url('assets/files/lpb_print/kuntul.pdf') ?>');"> Test Print </a--->
<div class="row">
    <div class="col-12">
        <h2 class="text-center">PT Jatim Taman Steel Mfg</h2>
        <h3 class="text-center">Pemakaian Barang Langsung</h3>
    </div>
        <?php /*
        <img src="<?php echo base_url('assets/img/logo-depan/jts-icon-mercury.png'); ?>" style="display:block; width:100px;height:80px;" class="float-left" alt="Logo">
        <img src="<?php echo base_url($barcode); ?>" style="display:block; width:100px;height:100px;" class="float-right" alt="Logo"> */ ?>
        <img src="<?php echo base_url($barcode); ?>" style="display: block;padding-left: 15%;width=40%" alt="Logo">
    <div class="col-12">
        <h5 class="text-left" style="text-align: right;">DOC NO    : <?= trim($master['docno']) ?></h5>
        <h5 class="text-left" style="text-align: right;">TANGGAL      : <?= trim($master['docdate1']) ?></h5>
        <h5 class="text-left" style="text-align: right;">JENIS     : <?= trim($master['docjns']) ?></h5>
        <h5 class="text-left" style="text-align: right;">REFERENSI    : <?= trim($master['docref']) ?></h5>
        <h5 class="text-left" style="text-align: right;">KET      : <?= ucfirst(trim($master['description'])) ?></h5>
    </div>
    <div class="col-12 ">
        <table class="table ">
            <thead>
            <tr>
                <th style="width:10px;text-align: left; font-size: 20px;font-weight: bold; width:100%;">ID BRG</th>
                <th style="text-align: left; font-size: 20px;font-weight: bold; width:100%;">NAMA BRG</th>
                <th style="text-align: left; font-size: 20px;font-weight: bold; width:90%;">QTY</th>
                <th style="text-align: left; font-size: 20px;font-weight: bold; width:90%;">UNIT</th>
            </tr>
            </thead>
            <tbody>
            <?php $no=0; foreach($detail as $lar): $no++;?>
                <tr>
                    <td style="text-align: left; font-size: 20px;width:90%"><?php echo $lar->idbarang;?></td>
                    <td style="text-align: left; font-size: 20px;font-weight: bold;width:90%"><?php echo $lar->nmbarang;?></td>
                    <td class="ratakanan" style="font-size: 20px;font-weight: bold;width:90%"><?php echo $lar->onhandtrans;?></td>
                    <td style="text-align: left;font-size: 20px;font-weight: bold;width:90%"><?php echo $lar->unit;?></td>
                </tr>
            <?php endforeach;?>
            </tbody>

        </table>
    </div>
    <!-- /.col -->
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <div class="col-11">
        <h5 class="text-right" style="text-align: right;">Paraf Disini</h5>
    </div>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <div class="col-12">
        <h5 class="text-center" style="text-align: center;">*******--Sobek Disini--******</h5>
    </div>
</div>
<!-- /.row -->
<!-- /.content -->

<!-- jQuery -->
<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js') ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url('assets/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>


<script>
    function sendUrlToPrint(url){
        var  beforeUrl = 'intent:';
        var  afterUrl = '#Intent;';
        // Intent call with component
        afterUrl += 'component=ru.a402d.rawbtprinter.activity.PrintDownloadActivity;'
        afterUrl += 'package=ru.a402d.rawbtprinter;end;';
        document.location=beforeUrl+encodeURI(url)+afterUrl;
        return false;
    }
    // jQuery: set onclick hook for css class print-file
    $(document).ready(function(){
        $('.print-file').click(function () {
            return sendUrlToPrint($(this).attr('href'));
        });
    });


    // or direct
    /*
    <a href="#" onclick="return sendUrlToPrint('http(s)://yousite.com/test.txt');">.txt</a>";
*/

</script>

</body>

</html>