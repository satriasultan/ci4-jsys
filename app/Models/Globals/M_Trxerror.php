<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 7/7/20, 9:42 AM
 *  * Last Modified: 7/7/20, 9:42 AM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


namespace App\Models\Globals;
use CodeIgniter\Model;


class M_Trxerror extends Model
{

    function q_versidb($kodemenu){
        return $this->db->query("select * from sc_mst.version where kodemenu='$kodemenu'");
    }

    function q_trxerror($paramtrxerror){
        return $this->db->query("select * from (
								select a.*,b.description from sc_mst.trxerror a
								left outer join sc_mst.errordesc b on a.modul=b.modul and a.errorcode=b.errorcode) as x
								where userid is not null $paramtrxerror");
    }

    function q_deltrxerror($paramtrxerror){
        return $this->db->query("delete from sc_mst.trxerror where userid is not null $paramtrxerror");
    }

    function ins_trxerror($userid,$errorcode,$nomorakhir1,$nomorakhir2,$modul){
        $this->db->where('userid',$userid);
        $this->db->where('modul',$modul);
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $userid,
            'errorcode' => $errorcode,
            'nomorakhir1' => $nomorakhir1,
            'nomorakhir2' => $nomorakhir2,
            'modul' => $modul,
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return $this->db->affected_rows();
    }

    function unfinish($name,$urlclear,$urlnext,$title,$body){
        return"
           <!-- Bootstrap 5 modal -->
<div id=\"modal_unfinish\" class=\"modal fade\" tabindex=\"-1\" aria-hidden=\"true\">
    <div class=\"modal-dialog\">
        <div class=\"modal-content\">
            <div class=\"modal-header\">
                <h3 class=\"modal-title text-center w-100\">
                    $title $name
                </h3>
            </div>
            <div class=\"modal-body\">
                <h6 class=\"text-center\"> $body </h6>
            </div>
            <div class=\"modal-footer d-flex justify-content-between\">
                <a href=\"$urlclear\" class=\"btn btn-danger\">
                    <i class=\"fa fa-trash\"></i> Clear
                </a>
                <a href=\"$urlnext\" class=\"btn btn-primary\">
                    <i class=\"fa fa-arrow-right\"></i> Continue
                </a>
            </div>
        </div>
    </div>
</div>

<script type=\"text/javascript\">
window.addEventListener('load', function () {
    var myModal = new bootstrap.Modal(
        document.getElementById('modal_unfinish'),
        {
            backdrop: 'static',
            keyboard: false
        }
    );
    myModal.show();
});
</script>

   
            ";

    }

}
