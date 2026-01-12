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
            <!-- Bootstrap modal -->
            <div id=\"modal_unfinish\" class=\"modal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">
                <div class=\"modal-dialog\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <h3 class=\"modal-title\" align=\"center\"> $title   $name </h3>
                            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-hidden=\"true\"></button>
                        </div>
                        <div class=\"modal-body\">
                             <h6 class=\"modal-title\"  align=\"center\"> $body </h6>
                        </div>
                        <div class=\"modal-footer\">
                <a href=\"$urlnext\" type=\"button\" class=\"btn btn-primary pull-right\"><i class='fa fa-arrow-right'></i> Continue </a>
                <a href=\"$urlclear\" type=\"button\" class=\"btn btn-danger pull-left\" ><i class='fa fa-trash'></i> Clear</a>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

<script type=\"text/javascript\">
$(window).on('load', function() {
            $('#modal_unfinish').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            }); // show bootstrap modal
});

</script>            
            ";

    }

}
