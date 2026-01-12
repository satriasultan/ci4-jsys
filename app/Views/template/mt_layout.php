<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 4/25/19 8:49 AM
 *  * Last Modified: 4/24/19 11:44 AM.
 *  Developed By: Fiky Ashariza Powered By PHPStorm
 *  Copyright© 2019 .All rights reserved.
 *
 */

use App\Libraries\Fiky_encryption;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/img/logo-depan/jts.ico') ?>">
    <title>IT - Jatim Taman Steel Mfg</title>
    <?php $this->fiky_encryption = new Fiky_encryption(); ?>
    <?php echo $_ini_stylenya;?>


    <!-- CUSTOM CSS TARUH DI BAWAH SINI -->
    <style>
        [class*=sidebar-dark-] {
            background-color: #300a40;
        }
        .btn-primary {
            color: #000;
            background-color: #029eff;
            border-color: #029eff;
        }

        .btn-info {
            color: #fff;
            background-color: #4fe6ff;
            border-color: #4fe6ff;
            box-shadow: none;
        }

        /* Warna Untuk Sidebar Menu */
        .sidebar-nav ul li a {
            color: #000000;
            padding: 10px 35px 10px 15px;
            display: block;
            align-items: center;
            font-size: 14px;
            font-weight: 400;
        }
        .sidebar-nav>ul>li>a i {
            width: 25px;
            font-size: 16px;
            display: inline-block;
            vertical-align: middle;
            color: #000000;
        }

        /* Admin Template */
        .navbar-dark {
            background: linear-gradient(
                    90deg,
                    #004fa3 0%,
                    #0066cd 50%,
                    #1a82ff 100%
            );
            border-color: #4b545c;
        }

        .navbar-dark .navbar-nav .active>.nav-link, .navbar-dark .navbar-nav .nav-link.active, .navbar-dark .navbar-nav .nav-link.show, .navbar-dark .navbar-nav .show>.nav-link {
            color: #000000;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        .btn-primary {
            color: #000;
            background-color: #03a9f3;
            border-color: #03a9f3;
        }
        .btn-primary:hover {
            color: #000;
            background-color: #0082bd;
            border-color: #0082bd;
        }
    </style>
    <style>
        #app-zoom {
            transform: scale(0.75);
            transform-origin: top left;
            width: 133.33%;
        }
    </style>
    <style> .ratakanan { text-align : right; } </style>
    <!-- END CUSTOM CSS  -->
    <?php echo $_ini_jsnya;?>
    <?php echo $_ini_keyaccess;?>
    <?php echo $_ini_customnya;?>
    <script type="text/javascript">
        var HOST_URL = '<?php echo base_url().'/';?>';
        //<![CDATA[
        var base = function(url){
            return '<?php echo base_url();?>' + url;
        }
        var site = function(url){
            return base(url) + '.html';
        }
        var languageDatatable = function ()  { return { <?php echo $this->fiky_encryption->constant('datatable_language'); ?>  }  }
        //]]>
    </script>
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js') ?>"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') ?>"></script>
    <![endif]-->
</head>

<body class="horizontal-nav skin-megna fixed-layout">
<div id="app-zoom">
<!-- ============================================================== -->
<!-- Preloader - style you can find in spinners.css -->
<!-- ============================================================== -->
<div class="preloader">
    <div class="loader">
        <div class="loader__figure"></div>
        <p class="loader__label">PT.Jatim Taman Steel.Mfg</p>
    </div>
</div>
<!-- ============================================================== -->
<!-- Main wrapper - style you can find in pages.scss -->
<!-- ============================================================== -->
<div id="main-wrapper">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <?php echo $_header;?>
    <!-- ============================================================== -->
    <!-- End Topbar header -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <?php echo $_sidebar;?>

    <!-- ============================================================== -->
    <!-- End Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="container-fluid">
            <?php echo $_content;?>
        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- footer -->
    <!-- ============================================================== -->
  <!--  <footer class="footer">
        Copyright © 2022  IT
        <a href="https://www.jts.co.id">PT. Jatim Taman Steel.MFG</a>
        2022
    </footer>-->
    <!-- ============================================================== -->
    <!-- End footer -->
    <!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Wrapper -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->

    <!--end app zoom-->
</div>

<!-- CUSTOM JS TARUH DI BAWAH SINI -->
<script type="text/javascript">
    var HOST_URL = '<?php echo base_url().'/';?>';
    //<![CDATA[
    var base = function(url){
        return '<?php echo base_url();?>' + url;
    }
    var site = function(url){
        return base(url) + '.html';
    }
    var languageDatatable = function ()  { return { <?php echo $this->fiky_encryption->constant('datatable_language'); ?>  }  }
    //]]>
</script>
<!-- END SCRIPT HELPER -->
<script>
    $(document).ready(function() {
        //window.onload = disableBack();
        //window.onpageshow = function(evt) { if (evt.persisted) disableBack() }
        var href = window.location.href,
            idle = false,
            timer = null;
        /*ACTIVE SIDEBAR OPEN*/
        var pathArray = href.split( '/' );
        var protocol = pathArray[0];
        var host = pathArray[2];


        var classmenu = $('#classmenu').val();
        //console.log(classmenu);
        //$('.treeview').find('a[href=\'' + urlxz + '\']')
        ////// $('.nav-item').find('.'+ classmenu)
        //////     .addClass('menu-open')
        //////     .addClass('active')
        //////     .parents('li')
        //////     .addClass('menu-open')
        //////     .addClass('active')
        //////     .parents('ul.nav-treeview')
        //////     .addClass('active')
        //////     .addClass('open')
        //////     .css({ 'display': 'block' });

        var timeout;
        clearTimeout(timeout); // Remove any timers from previous clicks
        timeout = setTimeout(function() {
            $('.x').removeClass("x").addClass("sidebar-collapse");
        }, 500000); // Schedule an event for 10 seconds in the future, and store it

        $( ":input" ).attr('autocomplete','off');
        $('form').on('focus', 'input[type=number]', function (e) {
            $(this).on('mousewheel.disableScroll', function (e) {
                e.preventDefault()
            })
        });

        $('.separator').on('ready', function (e) {
            formatangkaobjek( $(this).val());
        });

        function formatangkaobjek(objek) {
            a = objek.value.toString();
            //  alert(a);
            //  alert(objek);
            b = a.replace(/[^\d]/g,"");
            c = "";
            panjang = b.length;
            j = 0;
            for (i = panjang; i > 0; i--) {
                j = j + 1;
                if (((j % 3) == 1) && (j != 1)) {
                    c = b.substr(i-1,1) + "." + c;
                } else {
                    c = b.substr(i-1,1) + c;
                }
            }
            objek.value = c;
        }

    });
    function crutz(xx) {
        console.log(xx);
    }
</script>



</body>

</html>