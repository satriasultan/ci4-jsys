<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

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
<a href="#" onclick="return sendUrlToPrint('<?php echo base_url('assets/files/lpb_print/kuntul.pdf') ?>');"> Test Print </a>
<a href="#" onclick="return sendUrlToPrint('http://192.168.100.2/ims/assets/files/lpb_print/kuntul.pdf');"> Test Print Direct </a>
<a href="http://192.168.100.2:99/ims/assets/files/lpb_print/kuntul.pdf" > Open PDF </a>
<div class="row">
    <div class="col-12">
        <h2 class="text-center">PT Jatim Taman Steel Mfg</h2>
        <h3 class="text-center">LPB - Gudang</h3>
    </div>
    <div class="col-12">
        <img src="<?php echo base_url('assets/img/logo-depan/jts-icon-mercury.png'); ?>" style="display:block; width:100px;height:80px;" class="float-left" alt="Logo">
        <img src="<?php echo base_url($barcode); ?>" style="display:block; width:100px;height:100px;" class="float-right" alt="Logo">

    </div>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <div class="col-12">
        <h5 class="text-left" style="text-align: right;">Jurnal No : BBK20210201010</h5>
        <h5 class="text-left" style="text-align: right;">Date      : 2022/02/01</h5>
        <h5 class="text-left" style="text-align: right;">Jenis     : PO</h5>
        <h5 class="text-left" style="text-align: right;">Rev No    : PO/01002/02010</h5>
        <h5 class="text-left" style="text-align: right;">Desc      : Deskripsi Barang Dll</h5>
    </div>
    <div class="col-12  table-responsive">
        <table class="table ">
            <thead>
            <tr>
                <th style="text-align: left;">ID Brg</th>
                <th style="text-align: left;">Nama Brg</th>
                <th style="text-align: left;">Unit</th>
                <th style="text-align: left;">Qty</th>

            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="text-align: left;">01040100000010</td>
                <td style="text-align: left;">SCRAP (HMS NO. 1)</td>
                <td class="ratakanan">500000</td>
                <td style="text-align: left;">KG</td>
            </tr>
            <tr>
                <td style="text-align: left;">01040100000011</td>
                <td style="text-align: left;">SCRAP (HMS NO. 2)</td>
                <td class="ratakanan">7000</td>
                <td style="text-align: left;">KG</td>
            </tr>
            <tr>
                <td style="text-align: left;">01040100000012</td>
                <td style="text-align: left;">SCRAP BESI COR</td>
                <td class="ratakanan">5000</td>
                <td style="text-align: left;">KG</td>
            </tr>
            <tr>
                <td style="text-align: left;">01040100000014</td>
                <td style="text-align: left;">SCRAP STAINLEES</td>
                <td class="ratakanan">6000</td>
                <td style="text-align: left;">KG</td>
            </tr>
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