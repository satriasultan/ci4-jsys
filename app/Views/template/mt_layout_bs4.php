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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>IMS - Inventory Management System</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo-depan/jts.ico'); ?>">
    <?php $this->fiky_encryption = new Fiky_encryption(); ?>
    <?php echo $_ini_stylenya;?>
    <!-- CUSTOM CSS TARUH DI BAWAH SINI -->
    <style> .ratakanan { text-align : right; } </style>
    <!-- END CUSTOM CSS  -->
    <?php echo $_ini_jsnya;?>
    <?php echo $_ini_keyaccess;?>
    <?php echo $_ini_customnya;?>
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
            $('.nav-item').find('.'+ classmenu)
                .addClass('menu-open')
                .addClass('active')
                .parents('li')
                .addClass('menu-open')
                .addClass('active')
                .parents('ul.nav-treeview')
                .addClass('active')
                .addClass('open')
                .css({ 'display': 'block' });

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
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed layout-navbar-fixed">
<div class="wrapper">
    <!-- Preloader
    <div class="preloader flex-column justify-content-center align-items-center" style="background: #cccce2">
        <img class="animation__shake" src="<?php echo base_url('assets/img/logo-depan/jts-icon.png') ?>" alt="App Logo" height="250" width="300">
        <h2>IT - Jatim Taman Steel</h2>
    </div>-->
    <!-- Preloader
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__wobble" src="<?php echo base_url('assets/dist/img/AdminLTELogo.png') ?>" alt="AdminLTELogo" height="60" width="60">
    </div>-->
    <?php echo $_header;?>
    <?php echo $_sidebar;?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php echo $_content;?>
            </div><!--/. container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2021-2022 <a href="#">IT Jatim Taman Steel</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 4.1.1
        </div>
    </footer>
</div>
<!-- ./wrapper -->
</body>
</html>



