<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 5/3/19 8:44 AM
 *  * Last Modified: 4/12/19 11:11 AM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2019 .All rights reserved.
 *
 */
namespace App\Libraries;


#[\AllowDynamicProperties]
class Default_style
{

    protected $_CI;
    function __construct(){
        $this->fiky_encryption = new Fiky_encryption();
        $this->db = \Config\Database::connect('default');
    }

    function coba(){
        return 'TEST';
        /**
         * P1 : KODEMENU
         * P2 : NAMA VERSI
         * P3 : SESSION
         */
    }

    function _getCss($param = null){
        $str = '';
        $str.=
            '
                <link rel="stylesheet" href="'.base_url('assets/customonline/css/jquery-ui.min.css'). '">
                <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /-->
                <!-- Google Font: Source Sans Pro -->
                <link rel="stylesheet" href="'.base_url('assets/customonline/css/font_gogleapis_css.css'). '">
                <!--link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"-->
                <!-- Font Awesome -->
                <link rel="stylesheet" href="'.base_url('assets/customonline/css/font-awesome.min.css'). '">
                <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"-->
                <!-- Ionicons -->
                <link rel="stylesheet" href="'.base_url('assets/customonline/css/ionicons.min.css'). '">
                <!--link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"-->
                <!-- Tempusdominus Bootstrap 4 -->
                <!--link rel="stylesheet" href="'.base_url('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'). '"-->
                
      
                
                <!-- iCheck -->
                <link rel="stylesheet" href="'.base_url('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css'). '">
                <!-- jqvmap -->
                <link rel="stylesheet" href="'.base_url('assets/plugins/jqvmap/jqvmap.min.css'). '">
             
                <!-- Select2 -->
                <link rel="stylesheet" href="'.base_url('assets/plugins/select2/css/select2.min.css'). '">
                <link rel="stylesheet" href="'.base_url('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css'). '">
                                
                
                <!-- overlayScrollbars -->
                <link rel="stylesheet" href="'.base_url('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css'). '">
                <!-- Daterange picker -->
                <link rel="stylesheet" href="'.base_url('assets/plugins/daterangepicker/daterangepicker.css'). '">
                <!-- summernote -->
                <link rel="stylesheet" href="'.base_url('assets/plugins/summernote/summernote-bs4.min.css'). '">
     
     
                <!--link href="'.base_url('assets/css/fiky-css-loader.css'). '" rel="stylesheet" type="text/css" /-->
                <link href="'.base_url('assets/css/fiky-selectize.css'). '" rel="stylesheet" type="text/css" />
                <link href="'.base_url('assets/css/fiky-jts-custom.css'). '" rel="stylesheet" type="text/css" />
                
                  <!-- DataTables -->
                <link rel="stylesheet" href="'.base_url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'). '">
                <link rel="stylesheet" href="'.base_url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css'). '">
                <link rel="stylesheet" href="'.base_url('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css'). '">
                <!-- bootstrap validator -->
                <!--link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/css/bootstrapValidator.min.css"/-->
                <link rel="stylesheet" href="'.base_url('assets/plugins/fiky-validator/css/bootstrapValidator.css'). '" type="text/css">
                <link rel="stylesheet" href="'.base_url('assets/plugins/iCheck/minimal/_all.css'). '">
      
                <!-- Theme style -->
                <link rel="stylesheet" href="'.base_url('assets/dist/css/adminlte.min.css'). '">
                <!-- Selectize css -->
                <link rel="stylesheet" href="'.base_url('assets/customonline/css/selectize.css'). '">
                <link rel="stylesheet" href="'.base_url('assets/customonline/css/selectize.default.css'). '">
                <link rel="stylesheet" href="'.base_url('assets/customonline/css/selectize.bootstrap4.min.css'). '">
                
                <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.css" integrity="sha512-85w5tjZHguXpvARsBrIg9NWdNy5UBK16rAL8VWgnWXK2vMtcRKCBsHWSUbmMu0qHfXW2FVUDiWr6crA+IFdd1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.default.css" integrity="sha512-J0JTRxsBEJ99DP4GamwciDi8VjALSrZneBYrv98BhqHPvlQP6gLbmmuddlYXHDB8hyLtefOIJFlpZBhksCtK8g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.min.css" integrity="sha512-MMojOrCQrqLg4Iarid2YMYyZ7pzjPeXKRvhW9nZqLo6kPBBTuvNET9DBVWptAo/Q20Fy11EIHM5ig4WlIrJfQw==" crossorigin="anonymous" referrerpolicy="no-referrer" /-->
                <link type="text/css" href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />

                <!-- SWEET ALERT -->
                <link rel="stylesheet" href="'.base_url('assets/plugins/sweetalert2/sweetalert2.css'). '">
                <!-- MORIS -->
                <link rel="stylesheet" href="'.base_url('assets/ea/package/node_modules/morrisjs/morris.css'). '">
                <!-- EA Bootstrap 5 -->
                <link rel="stylesheet" href="'.base_url('assets/ea/dist/css/style.min.css'). '">
                <!--link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css"-->
                <!-- zTree css -->
                <link rel="stylesheet" href="'.base_url('assets/css/ztree/zTreeStyle/zTreeStyle.css'). '">
                
                <!-- Bapak NU ibu Muslimat -->
                    <!-- Font Awesome -->
                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
        ';
        return $str;
    }

    function _getJs($param = null){
        $str = '';
        $str.= '
        <!-- jQuery -->
        <script src="'.base_url('assets/plugins/jquery/jquery.min.js'). '"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="'.base_url('assets/plugins/jquery-ui/jquery-ui.min.js'). '"></script>
      
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip--> 
        <script>
          $.widget.bridge("uibutton", $.ui.button)
        </script>
        <!-- Bootstrap 4 -->
        
        <!--script src="'.base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js'). '"></script-->
        <!-- Bootstrap 5 -->
        <script src="'.base_url('assets/ea/package/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'). '"></script>

        <!-- slimscrollbar scrollbar JavaScript -->
        <script src="'.base_url('assets/ea/dist/js/perfect-scrollbar.jquery.min.js'). '"></script>
        <!-- ChartJS -->
        <script src="'.base_url('assets/plugins/chart.js/Chart.min.js'). '"></script>
        <!-- Sparkline -->
        <script src="'.base_url('assets/plugins/sparklines/sparkline.js'). '"></script>
        <!-- JQVMap-->
        <script src="'.base_url('assets/plugins/jqvmap/jquery.vmap.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/jqvmap/maps/jquery.vmap.usa.js'). '"></script>
        <!-- jQuery Knob Chart -->
        <script src="'.base_url('assets/plugins/jquery-knob/jquery.knob.min.js'). '"></script>
        <!-- daterangepicker -->
        <script src="'.base_url('assets/plugins/moment/moment.min.js').'"></script>
        <script src="'.base_url('assets/plugins/daterangepicker/daterangepicker.js'). '"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="'.base_url('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js'). '"></script>
        <!-- Summernote -->
        <script src="'.base_url('assets/plugins/summernote/summernote-bs4.min.js'). '"></script>
        <!-- overlayScrollbars -->
        <script src="'.base_url('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'). '"></script>

        <!-- Select2 -->
        <script src="'.base_url('assets/plugins/select2/js/select2.full.min.js'). '"></script>
                <!-- DataTables  & Plugins -->
        <script src="'.base_url('assets/plugins/datatables/jquery.dataTables.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/jszip/jszip.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/pdfmake/pdfmake.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/pdfmake/vfs_fonts.js'). '"></script>
        <script src="'.base_url('assets/plugins/datatables-buttons/js/buttons.html5.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/datatables-buttons/js/buttons.print.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/datatables-buttons/js/buttons.colVis.min.js'). '"></script>
         <!-- AdminLTE App -->
        <!--script src="'.base_url('assets/dist/js/adminlte.min.js'). '"></script-->
        <!-- EA BS5 -->
        <script src="'.base_url('assets/ea/dist/js/sidebarmenu.js'). '"></script>
        <script src="'.base_url('assets/ea/dist/js/custom.min.js'). '"></script>
        <!--Sky Icons JavaScript -->
        <script src="'.base_url('assets/ea/package/node_modules/skycons/skycons.js'). '"></script>
        <!--morris JavaScript -->
        <script src=""'.base_url('assets/ea/package/node_modules/raphael/raphael-min.js'). '"></script>
        <script src=""'.base_url('assets/ea/package/node_modules/morrisjs/morris.min.js'). '"></script>
        <script src=""'.base_url('assets/ea/package/node_modules/jquery-sparkline/jquery.sparkline.min.js'). '"></script>
        <script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

        
        <!--Validator -->
        <!--script type="text/javascript" src="'.base_url('assets/plugins/fiky-validator/js/bootstrapValidator.js'). '"></script-->
        <script src="'.base_url('assets/customonline/js/bootstrapValidator.min.js'). '"></script>
        <!--script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script-->
        <!-- iCheck 1.0.1 -->
        <script src="'.base_url('assets/plugins/iCheck/icheck.js'). '"></script>
        <!-- Chained JS 1.01 -->
        <script src="'.base_url('assets/customonline/js/jquery.chained.min.js'). '"></script>
        <!--script-- src="https://cdnjs.cloudflare.com/ajax/libs/jquery-chained/1.0.1/jquery.chained.min.js" integrity="sha512-rcWQG55udn0NOSHKgu3DO5jb34nLcwC+iL1Qq6sq04Sj7uW27vmYENyvWm8I9oqtLoAE01KzcUO6THujRpi/Kg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script-->
        <!-- Selectize 0.13.3 -->
        <script src="'.base_url('assets/customonline/js/selectize.min.js'). '"></script>
        <!--script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js" integrity="sha512-pF+DNRwavWMukUv/LyzDyDMn8U2uvqYQdJN0Zvilr6DDo/56xPDZdDoyPDYZRSL4aOKO/FGKXTpzDyQJ8je8Qw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script-->
        <!-- Sweet Alert 2 -->
        <script src="'.base_url('assets/plugins/sweetalert2/sweetalert2.all.min.js'). '"></script>
        <script src="'.base_url('assets/plugins/sweetalert2/sweetalert2.min.js'). '"></script>
        <!--ztree js--> 
        <script src="'.base_url('assets/css/ztree/js/jquery.ztree.core.js'). '"></script>
        <script src="'.base_url('assets/css/ztree/js/jquery.ztree.exhide.min.js'). '"></script>
        <script src="' . base_url('assets/css/ztree/js/fuzzysearch.js') . '"></script>
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

        ';

        return $str;

    }

    function _getCustom($param = null){
        $str = '';
        //$str .=''. $this->fiky_encryption->getAccessPage('TEMPLATE_PAGE') .'';
        //////$str .=''. $this->fiky_encryption->onExpireMacLock() .'';
        //////$str .=''. $this->fiky_encryption->onExpireLc() .'';
        //////$str .=''. $this->fiky_encryption->checkDirectLc() .'';
        //////$str .=''. $this->fiky_encryption->checkDirectMac() .'';
        //$str .=''. $this->fiky_encryption->checkdelfiles() .'';
        $str .= '
        <script type="text/javascript">
             $(document).ready(function() {
                $(\'#form\').bootstrapValidator({
                    message: \'This value is not valid\',
                    feedbackIcons: {
                        valid: \'glyphicon glyphicon-ok\',
                        invalid: \'glyphicon glyphicon-remove\',
                        validating: \'glyphicon glyphicon-refresh\'
                    },
                    excluded: [\':disabled\']
                });
                
            
             });
             
             $(function() {
                $(\'.fikyseparator\').on(\'ready load live select keyup change\',function(e) {
                    _fikyseparator(this);
                });
            });
             
             function inArray(needle, haystack) {
                var length = haystack.length;
                for(var i = 0; i < length; i++) {
                    if(haystack[i] == needle) return true;
                }
                return false;
            }
            function _fikyseparator(objek)
                {
                    var formatNumber = function(input, fractionSeparator, thousandsSeparator, fractionSize) {

                          fractionSeparator = fractionSeparator || \',\';
                          thousandsSeparator = thousandsSeparator || \'.\';
                          fractionSize = fractionSize || 3;
                          var output = \'\',
                            parts = [];
                        
                          input = input.toString();
                          parts = input.split(",");
                          output = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator).trim();
                          if (parts.length > 1) {
                            output += fractionSeparator;
                            output += parts[1].substring(0, fractionSize);
                          }
                          return output;
                        };

                      a = objek.value.toString();
                      //val = a.replace(/[^0-9\.]/g,\'\');
                      //original
                      //val = a.replace(/[^-?(\d*\.)?\d+$;]/g,\'\');
                      //custom
                      val = a.replace(/[^-?(\d*\,)?\d+$;]/g,\'\');
                      
                      if(val != "") {
                        val = formatNumber(val);
                        //valArr = val.split(\'.\');
                        //valArr[0] = (parseInt(valArr[0],10)).toLocaleString();
                        //valArr[0] = (parseInt(valArr[0],10)).toLocaleString();
                        //valArr[0] = formatNumber(valArr[0]).toLocaleString();
                        // = valArr.join(\'.\');
                        //console.log(valArr +" z");
                      }
                      objek.value = val;
                     // console.log(val);
                }
                function formatangkaobjek(objek) {
                    a = objek.value.toString();
                    //  alert(a);
                    //  alert(objek);
                    b = a.replace(/(\d+)(\d{3})/);
                    c = "";
                    panjang = b.length;
                    j = 0;
                    for (i = panjang; i > 0; i--) {
                        j = j + 1;
                        if (((j % 3) == 1) && (j != 1)) {
                            c = b.substr(i-1,1) + "," + c;
                        } else {
                            c = b.substr(i-1,1) + c;
                        }
                    }
                    objek.value = c;
                }

                function _jtsseparator(objek) {
                    if (!objek.value) return;

                    // =========================
                    // NORMALISASI TOTAL
                    // =========================
                    let raw = objek.value
                        .replace(/,/g, \'\')        // buang ribuan lama
                        .replace(/[^0-9.]/g, \'\');

                    // cegah lebih dari satu titik
                    if ((raw.match(/\./g) || []).length > 1) {
                        return;
                    }

                    // =========================
                    // SPLIT INTEGER & DECIMAL
                    // =========================
                    let parts = raw.split('.');
                    let integerPart = parts[0];
                    let decimalPart = parts[1] ?? \'\';

                    // =========================
                    // FORMAT INTEGER SAJA
                    // =========================
                    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                    // =========================
                    // GABUNG KEMBALI
                    // =========================
                    objek.value = decimalPart !== \'\'
                        ? integerPart + '.' + decimalPart
                        : integerPart;
                }


                function dbToIndoFormat(angka) {
                    if (!angka) return "0.00";

                    angka = parseFloat(angka).toFixed(2);
                    let parts = angka.split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                    return parts.join(".");
                }


//                function convertToDbNumber(val) {
//                    if (!val || val.trim() === "." || val.trim() === "") return 0;
//
//                    // hapus ribuan
//                    val = val.replace(/,/g, "");
//
//                    return parseFloat(val);
//                }
            // ===== jaccseparator (BEGIN) =====
                function jaccformat(value, opsi) {
                    opsi = opsi || {};
                    var thousandSep = opsi.thousandSep || ",";
                    var decimalSep  = opsi.decimalSep  || ".";
                    var allowNeg    = (opsi.allowNegative === undefined) ? true : !!opsi.allowNegative;
                    var maxDec      = parseInt(opsi.maxDecimals || 0, 10);
                    if (!isFinite(maxDec) || maxDec < 0) { maxDec = 0; }
                    var str = String((value === null || value === undefined) ? "" : value);
                    str = str.split(thousandSep).join("");
                    var raw = str.replace(/[^0-9.\-]/g, "");
                    if (raw === "") { return ""; }
                    var neg = raw.charAt(0) === "-";
                    raw = raw.replace(/-/g, "");
                    if (!allowNeg) { neg = false; }
                    var di = raw.indexOf(".");
                    var intPart = "";
                    var decPart = null;
                    if (di === -1) {
                        intPart = raw;
                    } else {
                        intPart = raw.substring(0, di);
                        decPart = raw.substring(di + 1).replace(/\./g, "");
                        if (maxDec > 0) { decPart = decPart.substring(0, maxDec); }
                    }
                    if (intPart !== "") { intPart = intPart.replace(/^0+(?=\d)/, ""); }
                    intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep);
                    if (intPart === "" && decPart === null) { return neg ? "-" : ""; }
                    if (intPart === "") { intPart = "0"; }
                    return (neg ? "-" : "") + intPart + (decPart !== null ? decimalSep + decPart : "");
                }
                function jaccseparator(objek, opsi) {
                    if (!objek) { return; }
                    opsi = opsi || {};
                    if (objek.dataset) {
                        if (opsi.thousandSep === undefined && objek.dataset.thousandSep) { opsi.thousandSep = objek.dataset.thousandSep; }
                        if (opsi.decimalSep === undefined && objek.dataset.decimalSep) { opsi.decimalSep = objek.dataset.decimalSep; }
                        if (opsi.maxDecimals === undefined && objek.dataset.maxDecimals) { opsi.maxDecimals = objek.dataset.maxDecimals; }
                        if (opsi.allowNegative === undefined && objek.dataset.allowNegative !== undefined) { opsi.allowNegative = (String(objek.dataset.allowNegative) !== "false"); }
                    }
                    var lama  = String((objek.value === null || objek.value === undefined) ? "" : objek.value);
                    var caret = (typeof objek.selectionStart === "number") ? objek.selectionStart : lama.length;
                    var digitKiri = lama.substring(0, caret).replace(/[^0-9]/g, "").length;
                    var baru = jaccformat(lama, opsi);
                    if (baru === lama) { return; }
                    objek.value = baru;
                    if (typeof objek.setSelectionRange === "function") {
                        var pos = 0;
                        if (digitKiri === 0) {
                            pos = baru.length;
                        } else {
                            var hitung = 0;
                            while (pos < baru.length && hitung < digitKiri) {
                                if (baru.charAt(pos) >= "0" && baru.charAt(pos) <= "9") { hitung++; }
                                pos++;
                            }
                        }
                        try { objek.setSelectionRange(pos, pos); } catch (err) { }
                    }
                    return baru;
                }
                function jaccparse(value, opsi) {
                    opsi = opsi || {};
                    var thousandSep = opsi.thousandSep || ",";
                    return String((value === null || value === undefined) ? "" : value).split(thousandSep).join("").replace(/[^0-9.\-]/g, "");
                }
                // ===== jaccseparator (END) =====

                function convertToDbNumber(val) {

                        // =============================================
                        // VALIDASI NULL / UNDEFINED
                        // =============================================
                    
                        if (
                            val === null ||
                            val === undefined
                        ) {
                            return 0;
                        }
                    
                    
                        // =============================================
                        // PASTIKAN MENJADI STRING
                        // Agar .trim() tidak error jika val adalah number
                        // =============================================
                    
                        val = String(val).trim();
                    
                    
                        // =============================================
                        // VALIDASI NILAI KOSONG
                        // =============================================
                    
                        if (
                            val === "" ||
                            val === "."
                        ) {
                            return 0;
                        }
                    
                    
                        // =============================================
                        // HAPUS PEMISAH RIBUAN
                        // =============================================
                    
                        val = val.replace(/,/g, "");
                    
                    
                        // =============================================
                        // CONVERT KE NUMBER
                        // =============================================
                    
                        return parseFloat(val) || 0;
                    }
                        // =============================================================
        // HELPER READ HEADER TANDA TERIMA
        // =============================================================
        function setReadableText(selector, value) {
            const $el = $(selector);

            if (!$el.length) {
                return;
            }

            $el.val(
                value === null || value === undefined
                    ? \'\'
                    : String(value).trim()
            );
        }

        function setReadableNumber(selector, value) {
            const $el = $(selector);

            if (!$el.length) {
                return;
            }

            if (typeof setJtsValue === \'function\') {
                setJtsValue(
                    selector,
                    convertToDbNumber(value)
                );
            } else {
                $el.val(value ?? \'\');
            }
        }

        function setReadableDate(selector, value) {
            const $el = $(selector);

            if (!$el.length) {
                return \'\';
            }

            const raw =
                $.trim(value || \'\');

            if (raw === \'\') {
                $el.val(\'\');
                return \'\';
            }

            let m = moment(
                raw,
                \'YYYY-MM-DD\',
                true
            );

            if (m.isValid()) {
                const display = m.format(\'DD-MM-YYYY\');
                $el.val(display);
                return display;
            }

            m = moment(
                raw,
                \'DD-MM-YYYY\',
                true
            );

            if (m.isValid()) {
                const display = m.format(\'DD-MM-YYYY\');
                $el.val(display);
                return display;
            }

            $el.val(raw);
            return raw;
        }

         function tterimaFormatNumber(value) {

            const n = Number(
                String(value ?? "")
                    .replace(/,/g, "")
            );
        
            if (!Number.isFinite(n)) {
                return "0";
            }
        
            return n.toLocaleString(
                "en-US",
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );
        }    
                    
        </script>
        ';
        $str .= '<!-- Corporate Loader Modal -->
';
        $str .= '
<style>
/* ===============================
   BOOTSTRAP 5 CORPORATE LOADER
================================ */

.corporate-loader {
    background: linear-gradient(145deg, #1e293b, #0f172a);
    border-radius: 18px;
    color: #ffffff;
}

.corporate-spinner {
    width: 3rem;
    height: 3rem;
    border-width: 0.35em;
}

.modal-backdrop.show {
    opacity: 0.8;
    backdrop-filter: blur(4px);
}
@media (min-width: 768px) {
  .modal-xl {
	width: 100%;
   max-width:1800px;
  }
}

.bg-primary {
    background-color: #2d76e0 !important;
}
</style>';
        $str .= '
<script type="text/javascript">
    $(function () {
    $(\'input[type="text"], input[type="number"], textarea\').click(function() {
        $(this).select();
    });
          //iCheck for checkbox and radio inputs
    $(\'input[type="checkbox"].minimal, input[type="radio"].minimal\').iCheck({
      checkboxClass: \'icheckbox_minimal-blue\',
      radioClass: \'iradio_minimal-blue\'
    });
    //Red color scheme for iCheck
    $(\'input[type="checkbox"].minimal-red, input[type="radio"].minimal-red\').iCheck({
      checkboxClass: \'icheckbox_minimal-red\',
      radioClass: \'iradio_minimal-red\'
    });
    //Flat red color scheme for iCheck
    $(\'input[type="checkbox"].flat-red, input[type="radio"].flat-red\').iCheck({
      checkboxClass: \'icheckbox_flat-green\',
      radioClass: \'iradio_flat-green\'
    });
    });
    
   
</script>';

        $str .= '
<script type="text/javascript">
  $(document).ready(function () {

    // Auto select text
    $(\'input[type="text"], input[type="number"], textarea\').on("click", function() {
        $(this).select();
    });

    // iCheck minimal blue
    $(\'input[type="checkbox"].minimal, input[type="radio"].minimal\').iCheck({
        checkboxClass: "icheckbox_minimal-blue",
        radioClass: "iradio_minimal-blue"
    });

    // iCheck minimal red
    $(\'input[type="checkbox"].minimal-red, input[type="radio"].minimal-red\').iCheck({
        checkboxClass: "icheckbox_minimal-red",
        radioClass: "iradio_minimal-red"
    });

    // iCheck flat green
    $(\'input[type="checkbox"].flat-red, input[type="radio"].flat-red\').iCheck({
        checkboxClass: "icheckbox_flat-green",
        radioClass: "iradio_flat-green"
    });

    // ===============================
    // Bootstrap 5 Loader Init
    // ===============================

    const loadMeElement = document.getElementById("loadMe");

    if (loadMeElement) {
        window.loadMeInstance = new bootstrap.Modal(loadMeElement, {
            backdrop: "static",
            keyboard: false
        });
    }

});

// Show Loader
function showLoader() {
    if (window.loadMeInstance) {
        window.loadMeInstance.show();
    }
}

// Hide Loader
function hideLoader() {
    if (window.loadMeInstance) {
        window.loadMeInstance.hide();
    }
} 
</script>';
        return $str;
    }

    function _keyAccess($p1){
        return null;
        //return $this->fiky_encryption->keyAccess($p1);
    }

}
