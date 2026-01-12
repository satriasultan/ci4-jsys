<?php
require_once $helper;
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?= $title ;?></title>

    <?= $header ;?>
    <?php
    $options = StiHelper::createOptions();
    $options->handler = $handler ;
    $options->timeout = 30;
    StiHelper::initialize($options);
    ?>
    <script type="text/javascript">
        Stimulsoft.Base.StiLicense.loadFromFile("<?= $license ;?>");

        var options = new Stimulsoft.Viewer.StiViewerOptions();
        options.appearance.fullScreenMode = true;
        options.toolbar.showSendEmailButton = false;
        options.toolbar.showOpenButton = false;
        options.toolbar.showPrintButton = true;
        options.toolbar.printDestination = Stimulsoft.Viewer.StiPrintDestination.Direct;
        options.toolbar.showDesignButton = false;
        options.toolbar.showAboutButton = false;


        // options.width = "1000px";
        // options.height = "1000px";
        options.appearance.scrollbarsMode = true;
        options.appearance.backgroundColor = Stimulsoft.System.Drawing.Color.lightGray;
        options.appearance.showTooltips = false;

        options.exports.showExportToPdf = true;
        options.exports.ShowExportToWord2007 = true;
        options.appearance.fullScreenMode = true;



        var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);

        // Process SQL data source
        viewer.onBeginProcessData = function (event, callback) {
            <?php StiHelper::createHandler(); ?>
        }

        // Manage export settings on the server side
        viewer.onBeginExportReport = function (args) {
            <?php //StiHelper::createHandler(); ?>
            //args.fileName = "MyReportName";
        }

        // Process exported report file on the server side
        /*viewer.onEndExportReport = function (event) {
            event.preventDefault = true; // Prevent client default event handler (save the exported report as a file)
<?php StiHelper::createHandler(); ?>
		}*/

        // Send exported report to Email
        viewer.onEmailReport = function (event) {
            <?php StiHelper::createHandler(); ?>
        }

        // Load and show report
        var report = new Stimulsoft.Report.StiReport();

        report.loadFile("<?= $reportnya ?>");

        // Create new DataSet object
        var dataSet = new Stimulsoft.System.Data.DataSet("Demo");
        // Load JSON data file from specified URL to the DataSet object
        dataSet.readJsonFile("<?= $jsonfile ?>");
        // Remove all connections from the report template
        report.dictionary.databases.clear();
        // Register DataSet object
        report.regData("Demo", "Demo", dataSet);
        // Render report with registered data


        //viewer.report = report;
        viewer.report = report;

        function onLoad() {
            viewer.renderHtml("viewerContent");
        }
    </script>
</head>
<body onload="onLoad();">
<div id="viewerContent"></div>
</body>
</html>
