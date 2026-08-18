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
    <title>J-Accounting</title>
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
        /*#app-zoom {*/
        /*    zoom: 0.75; */
        /*    width: 100%; */
        /*}*/

        #app-zoom {
            zoom: 0.90;
            width: 100%;
        }

        #app-zoom .modal {
            zoom: 1.333; 
        }

        /*paksa global colour*/
        .nav-link {
            color: #3232f3 !important;
        }
        .nav-link,
        .nav-link:hover,
        .nav-link:focus,
        .nav-link.active {
            color: #3232f3 !important;
        }
    </style>
    <style> .ratakanan { text-align : right; }


        /* =========================================
           FIXED FOOTER
           ========================================= */

        .footer {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 9999 !important;

            height: 35px;
            padding: 8px 20px !important;

            background: rgba(255, 255, 255, 0.95) !important;
            border-top: 1px solid rgba(0, 0, 0, 0.1);

            text-align: center;
            font-size: 12px;
        }

        .page-wrapper {
            padding-bottom: 45px !important;
        }


    </style>
    <style>
        /*Global Tabs*/

        /* =====================================================
   GLOBAL CORPORATE TABS
   ===================================================== */
        /* =========================================
           GLOBAL CORPORATE TABS
           ========================================= */

        .nav-tabs {
            display: flex;
            align-items: flex-end;
            gap: 2px;

            margin: 0 !important;
            padding: 0 8px;

            border-bottom: 1px solid #d9dee7 !important;
            background: #ffffff;
        }

        /* ITEM */
        .nav-tabs .nav-item {
            margin-bottom: -1px !important;
        }

        /* TAB */
        .nav-tabs .nav-link {
            position: relative;

            padding: 8px 18px !important;

            border: 1px solid transparent !important;
            border-bottom: 0 !important;

            border-radius: 6px 6px 0 0 !important;

            background: transparent !important;

            color: #6b7280 !important;

            font-size: 13px;
            font-weight: 600;

            transition: all .2s ease;
        }

        /* HOVER */
        .nav-tabs .nav-link:hover {
            color: #0042c3 !important;

            background: linear-gradient(
                    to bottom,
                    rgba(0, 66, 195, 0.06),
                    rgba(0, 66, 195, 0)
            ) !important;
        }

        /* ACTIVE */
        .nav-tabs .nav-link.active {
            color: #0042c3 !important;

            background: linear-gradient(
                    to bottom,
                    rgba(0, 66, 195, 0.10) 0%,
                    rgba(0, 66, 195, 0.03) 45%,
                    #ffffff 100%
            ) !important;

            border: 1px solid #d9dee7 !important;
            border-bottom: 1px solid #ffffff !important;

            font-weight: 600;
        }

        /* GARIS ACTIVE */
        .nav-tabs .nav-link.active::after {
            content: "";

            position: absolute;

            left: 14px;
            right: 14px;
            bottom: -1px;

            height: 2px;

            background: #0042c3;

            border-radius: 2px 2px 0 0;
        }

        /* FOCUS */
        .nav-tabs .nav-link:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
    <style>
        /* =========================================
   GLOBAL CORPORATE DATATABLE
   ========================================= */

        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            font-size: 13px;
        }

        table.dataTable thead th {
            background: linear-gradient(
                    135deg,
                    #1f2937,
                    #374151
            ) !important;

            color: #ffffff !important;
            font-weight: 600;
            text-align: center;

            padding: 10px 8px !important;

            border: 1px solid #dee2e6 !important;

            vertical-align: middle;
            white-space: nowrap;

            position: sticky;
            top: 0;
            z-index: 2;
        }

        table.dataTable thead th:first-child {
            border-top-left-radius: 8px;
        }

        table.dataTable thead th:last-child {
            border-top-right-radius: 8px;
        }

        table.dataTable tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table.dataTable tbody tr:hover {
            background-color: #e9f2ff !important;
            transition: .2s ease-in-out;
        }


        /* =========================================
           GLOBAL CORPORATE CARD
           ========================================= */

        .card {
            border: none !important;
            border-radius: 14px !important;

            box-shadow:
                    0 10px 25px rgba(0,0,0,.05) !important;
        }

        .card-header {
            background: linear-gradient(
                    135deg,
                    #4b5563,
                    #6b7280
            ) !important;

            border-bottom: none !important;

            padding: 12px 18px !important;

            border-top-left-radius: 14px !important;
            border-top-right-radius: 14px !important;
        }


        /* =========================================
           GLOBAL CARD HEADER BUTTON
           ========================================= */

        .card-header .btn-primary {
            background: #e5e7eb !important;
            color: #374151 !important;

            border: none !important;

            font-weight: 600;

            padding: 8px 16px !important;

            border-radius: 8px !important;

            transition: all .2s ease-in-out;
        }

        .card-header .btn-primary:hover {
            background: #d1d5db !important;

            transform: translateY(-2px);
        }


        /* =========================================
           GLOBAL DROPDOWN
           ========================================= */

        .dropdown-menu {
            border-radius: 10px !important;

            border: none !important;

            box-shadow:
                    0 10px 25px rgba(0,0,0,.10) !important;

            padding: 6px 0 !important;
        }

        .dropdown-item {
            padding: 8px 16px !important;

            font-size: 14px;

            color: #374151 !important;

            transition: .2s ease-in-out;
        }

        .dropdown-item:hover {
            background: #f3f4f6 !important;

            color: #111827 !important;

            padding-left: 20px !important;
        }

        .dropdown-item i {
            width: 20px;

            color: #6b7280;
        }

        /* =========================================
   GLOBAL DROPDOWN - BLUE CORPORATE
   ========================================= */

        .dropdown-menu {
            border-radius: 10px !important;
            border: 1px solid #d7e3f5 !important;

            background: linear-gradient(
                    to bottom,
                    #eef5ff 0%,
                    #f7faff 55%,
                    #ffffff 100%
            ) !important;

            box-shadow:
                    0 10px 25px rgba(0, 66, 195, 0.15) !important;

            padding: 6px 0 !important;
        }


        /* SEMUA LI */
        .dropdown-menu li {
            background: transparent !important;
            margin: 0 !important;
            padding: 0 !important;
        }


        /* ITEM */
        .dropdown-menu li > a,
        .dropdown-menu .dropdown-item {
            display: block !important;
            width: 100% !important;

            padding: 8px 16px !important;

            font-size: 14px;
            font-weight: 500;

            color: #1f2937 !important;

            background: transparent !important;

            transition: all .2s ease;
        }


        /* HOVER */
        .dropdown-menu li > a:hover,
        .dropdown-menu .dropdown-item:hover {
            color: #0042c3 !important;

            background: linear-gradient(
                    to right,
                    rgba(0, 66, 195, 0.16),
                    rgba(0, 66, 195, 0.04),
                    rgba(255,255,255,0)
            ) !important;

            padding-left: 20px !important;
        }


        /* ICON */
        .dropdown-menu li > a i,
        .dropdown-menu .dropdown-item i {
            width: 20px;

            color: #0042c3 !important;

            margin-right: 5px;
        }


        /* ACTIVE */
        .dropdown-menu li > a.active,
        .dropdown-menu .dropdown-item.active {
            color: #0042c3 !important;

            background: linear-gradient(
                    to right,
                    rgba(0, 66, 195, 0.18),
                    rgba(0, 66, 195, 0.05),
                    transparent
            ) !important;

            font-weight: 600;

            border-left: 3px solid #0042c3;
        }
    </style>
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
    <footer class="footer">
        Copyright © 2026  IT
        <a href="https://www.jts.co.id">PT. Jatim Taman Steel.MFG</a>
        2026
    </footer>
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