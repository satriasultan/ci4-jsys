/*
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


var save_method; //for save method string
var table;
var initTable;
//"use strict";
function tableNDKTrx(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tablelapndkTrx');
        table.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "bFilter":true,
            // "lengthMenu": [
            //     [ 10, 25, 50, -1 ],
            //     [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            // ],
            
            // "buttons": [
            //     'pageLength','excel'
            // ],
            "ajax": {
                "url": HOST_URL + 'arap/report/list_lapndk',
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                },
                "dataFilter": function(data) {
                    var json = jQuery.parseJSON(data);
                    
                    // TAMPILKAN ke footer
                    var total_debit = json.dataTables.total_debit || 0;
                    var total_kredit = json.dataTables.total_kredit || 0;
                    $('#total_debit').html((total_debit));
                    $('#total_kredit').html((total_kredit));
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

    };


    return initTable();
}


function reload_tableNDKTrx()
{
    var table = $('#tablelapndkTrx');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

// let table;

var table;

$('#btn-filter').on('click', function(){

    $('#wrapperTable').show();

    // CEK: sudah pernah init atau belum
    if (!$.fn.DataTable.isDataTable('#tablelapndkTrx')) {

        tableNDKTrx()

    } else {

        reload_tableNDKTrx()

    }

});

$('#btn-print').on('click', function () {

    var tgl = $('#tglrange').val();

    var tableHtml = $('#tablelapndkTrx').clone();

    // hapus tbody border (opsional via class)
    var printWindow = window.open('', '', 'width=900,height=600');

    printWindow.document.write(`
        <html>
        <head>
            <title>Cetak Laporan</title>
            <style>
                @media print {
                    @page {
                        size: A4 portrait;
                        margin: 15mm;
                        padding: 10px;
                    }

                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        counter-reset: page 1;
                    }

                    .header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 10px;
                    }

                    .title {
                        background: #1f3a5f;
                        color: #1f3a5f;
                        padding: 8px 12px;
                        font-weight: bold;
                        font-size: 20px;
                        text-decoration: underline;
                        display: inline-block;
                    }

                    .periode {
                        margin-top: 5px;
                        font-size: 12px;
                    }

                    @page {
                        counter-reset: page 1;
                    }

                    .page-number:after {
                        content: "Hal " counter(page);
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    thead th {
                        border-top: 1px solid black;
                        border-bottom: 1px solid black;
                        border-left: none;
                        border-right: none;
                        text-align: center;
                        padding: 5px;
                    }

                    tbody td {
                        border: none;
                        padding: 4px;
                        font-size: 12px;
                    }

                     /* KHUSUS DEBIT & KREDIT */
                    tbody td:nth-child(6),
                    tbody td:nth-child(7) {
                        text-align: right;
                    }

                    tfoot th {
                        border-top: 1px solid black;
                        border-bottom: 1px solid black;
                        border-left: none;
                        border-right: none;
                        padding: 5px;
                        font-size: 14px;
                    }

                    tfoot {
                        display: table-footer-group;
                    }

                    tfoot th:nth-child(2),
                    tfoot th:nth-child(3) {
                        text-align: right;
                    }

                    thead {
                        display: table-header-group;
                    }

                    tr {
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>

        <body>

            <div class="header">
                <div>
                    <div class="title">LAPORAN NOTA DEBIT KREDIT</div>
                    <div class="periode">Tanggal: ${tgl}</div>
                </div>
                <div class="page-number"></div>
            </div>

            ${tableHtml.prop('outerHTML')}

        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
});


$(document).ready(function() {

//    tableNDKTrx()
//     $('#wrapperTable').show();

});