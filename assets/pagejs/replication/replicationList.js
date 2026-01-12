/*
 * *
 *  * Created by PhpStorm.
 *  *  * User: FIKY-PC
 *  *  * Date: 4/29/19 1:34 PM
 *  *  * Last Modified: 12/18/16 10:51 AM.
 *  *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  *  Copyright© 2019 .All rights reserved.
 *  *
 *
 */


var save_method; //for save method string
var table;
"use strict";
function tableListReplikasi(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tableListReplikasi');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "ajax": {
                "url": base('api/replication/list_tarikdata'),
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                },
                "dataFilter": function(data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json); // return JSON string
                }
            },

            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [ -1 ], //last column
                    "orderable": false, //set not orderable
                },
            ],
        });

        //alert("CONTOL SINGA xx");
        //console.log("BASEURL" + base('assets/cok') );
        //console.log("SITE" + site('assets/cok'));


    }
    return initTable();
}

function reload_table()
{
    var table = $('#tableListReplikasi');
    table.DataTable().ajax.reload(); //reload datatable ajax


}

function getDataOnline(){


    var url= base('api/replication/captureFromOnline');
    var confirmText = "Anda akan melakukan sinkron data dari online server, Pastikan Konfigurasi Benar";
    if(confirm(confirmText)) {
        $('#loadMe').modal({backdrop: 'static', keyboard: false,show:true});

        console.log('HOI JANCOK');
        $.ajax({
            type:"GET",
            dataType: 'json',
            url:url,
            success:function (data) {
                // Here goes something...
                if (data.status){
                    $('#loadMe').modal('hide');
                    //alert(data.messages);
                    reload_table();
                } else {
                    $('#loadMe').modal('hide');
                    alert(data.messages);
                    reload_table();
                }
                console.log('DATA ENTRY KOMPLIT');
            },
            error: function(xhr, status, error) {
                $('#loadMe').modal('hide');
                var err = eval("(" + xhr.responseText + ")");
                alert(err.Message);
            }
        });
    }
    return false;
}

$(document).ready(function() {
    tableListReplikasi()
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);

    console.log("JEMBUT_XXVID");

});