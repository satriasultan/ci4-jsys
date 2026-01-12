

    <!DOCTYPE html>
    <!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
    <!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
    <!--[if !IE]><!-->
    <html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Profile Karyawan</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
		<!-- STIMULSOFT REPORT CSS----->
		<!--link href="http://localhost/crm/assets/global/plugins/stimulsoft/css/stimulsoft.viewer.office2013.white.green.css" rel="stylesheet" type="text/css" />
        <link href="http://localhost/crm/assets/global/plugins/stimulsoft/css/stimulsoft.designer.office2013.white.green.css" rel="stylesheet" type="text/css" /-->
		<link href="<?php echo base_url('assets/global/plugins/stimulsoft/css/stimulsoft.viewer.office2013.white.green.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('assets/global/plugins/stimulsoft/css/stimulsoft.designer.office2013.white.green.css'); ?>" rel="stylesheet" type="text/css" />

		
		

        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
		
		
        <!-- REPORT FILE INDEPENDEN BASE URL -->
       <script type="text/javascript">	   
            //<![CDATA[
            var base = function(e){
                return '<?php echo base_url();?>' + e;
            }
            //]]>
        </script>

    </head>
    <!-- END HEAD -->

    <body>
    <!-- BEGIN : REPORT PAGE -->
    <div id="viewer" data-jsonfile="<?php echo $jsonfile; ?>" data-reportfile="<?php echo $report_file; ?>"></div>
    <div id="designer" data-jsonfile="<?php echo $jsonfile; ?>" data-reportfile="<?php echo $report_file; ?>"></div>
    <!-- END : REPORT PAGE -->
    <!-- BEGIN CORE PLUGINS -->
   <script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js')?>"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
		<script src="<?php echo base_url('assets/global/plugins/stimulsoft/js/stimulsoft.reports.js');?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/global/plugins/stimulsoft/js/stimulsoft.viewer.js');?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/global/plugins/stimulsoft/js/stimulsoft.designer.js');?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/global/reports.js');?>" type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <!-- END THEME LAYOUT SCRIPTS -->
    </body>
    </html>
