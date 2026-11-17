<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 7/7/20, 2:48 PM
 *  * Last Modified: 7/7/20, 2:48 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */

namespace App\Libraries;

use App\Models\Globals\M_Gobals;

class Fiky_report
{
    public function render($datajson,$datamrt,$title,$session,$module,$table,$docno,$menu) {
        $head_def = '';
        $head_def.= '<!-- Office2013 style -->';
        $head_def.= ' <link href="'.base_url('assets/st/css/stimulsoft.viewer.office2013.whiteblue.css'). '"  rel="stylesheet">';
        $head_def.= ' <!-- Stimusloft Reports.JS -->';
        $head_def.= ' <script src="'.base_url('assets/st/scripts/stimulsoft.reports.js'). '" type="text/javascript"></script> ';
        $head_def.= ' <script src="'.base_url('assets/st/scripts/stimulsoft.viewer.js'). '" type="text/javascript"></script> ';


        $data['header'] = $head_def;
        $data['module'] =  $module;
        $data['menu'] =  $menu;
        $data['table'] =  $table;
        $data['title'] =  $title;
        $data['docno'] =  $docno;
        $data['license'] =  $this->loadLicense();
        //$data['reportnya'] =  $this->loadReport();
        $data['reportnya'] =  $datamrt;
        $data['jsonfile'] =  $datajson;
        $data['helper'] =  $this->loadHelper();
        $data['handler'] =  '';
        //$data['handler'] =  $this->handler();
        return view('stimulsoft/viewer', $data);
    }


    public function handler(){
        /* MASIH MENJADI MISTERI */
        //return include(dirname(__FILE__) ."/stimulsoft/handler.php");
        $this->loadHelper();
        error_reporting(0);
// Please configure the security level as you required.
// By default is to allow any requests from any domains.
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token");
        //$handler = new StiHandler();
        //$handler->registerErrorHandlers();

    }

    function cekloadlib(){
        include_once(dirname(__FILE__) ."/stimulsoft/test.php");
    }

    function loadLicense(){
        //include(dirname(__FILE__) ."/stimulsoft/license.php");
        //return include(dirname(__FILE__) ."/stimulsoft/license.php");
        return base_url("assets/st/license.php");
    }

    function loadReport(){
        return base_url("assets/mrt/reports/SimpleList.mrt");
    }

    function loadJson(){
        return base_url("assets/mrt/reports/Demo.json");
    }
    function loadHelper(){
        return dirname(__FILE__) ."/stimulsoft/helper.php";
    }

    function testLoad(){
        include_once(dirname(__FILE__) ."/stimulsoft/test.php");
    }
}
