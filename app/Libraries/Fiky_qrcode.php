<?php
/*UPGRADE CI 4 */
namespace App\Libraries;
use App\Libraries\phpqrcode\QRcode;

$QR_BASEDIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'phpqrcode'.DIRECTORY_SEPARATOR;

// Required libs

include $QR_BASEDIR."qrlib.php";

class Fiky_qrcode {

    private $p1;
    private $p2;
    private $p3;
    private $p4;
    private $p5;
    private $p6;
    private $logopath;
    private $logo;
    private $QR;
    private $tempdir;

    //simpan file qrcode
    function save_image($p1,$p2,$p3,$p4,$p5){
        return QRcode::png($p1,$p2,$p3,$p4,$p5);
    }
    // ambil file qrcode
    function get_qrcode($p6){
        return imagecreatefrompng($p6);
    }

    function createfromstring($logopath){
        $url= $logopath;

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $response = file_get_contents($url, false, stream_context_create($arrContextOptions));
        return imagecreatefromstring($response);
    }
    function proses($logo,$QR,$tempdir){
        imagecolortransparent($logo , imagecolorallocatealpha($logo , 0, 0, 0, 127));
        imagealphablending($logo , false);
        imagesavealpha($logo , true);
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);
        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);
        // Scale logo to fit in the QR Code
        $logo_qr_width = $QR_width/4;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;
        imagecopyresampled($QR, $logo, $QR_width/2.6, $QR_height/2.4, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        // Simpan kode QR lagi, dengan logo di atasnya
        imagepng($QR,$tempdir);
        return $this;
    }

    // memulai menggambar logo dalam file qrcode
}