<?php
/*UPGRADE CI 4 */
namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class Fiky_pdf extends Dompdf {

    private $view;
    private $paper;
    private $orientation;
    private $width = array();
    private $header_bold = TRUE;
    private $start = 1;

    public function setHtml($view) {
        $this->loadHtml($view);
        return $this;
    }

    //$size 'letter', 'legal', 'A4', etc.,custom=array(0,0,407,1148);
    public function setupPaper($paper,$orientation) {
        $this->setPaper($paper, $orientation);
        return $this;
    }

    public function fRender(){
        $this->render();
        return $this;
    }
    public function fStream(){
        $this->stream();
        return $this;
    }

    public function addOptions($deffont='defaultFont',$deffonttype='Courier'){
        $options = new Options();
        $options->set($deffont, $deffonttype);
        $thisx = new Dompdf($options);
        return $thisx;
    }

    public function optionsx(){
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        return $dompdf;
    }
}