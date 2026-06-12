/*
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


$('#btn-filter').click(function(){ // button filter event click
    var table_proforma = $('#t_proforma');
    
    // Pastikan untuk menyertakan data filter dalam permintaan AJAX
    var ctypeFilter = $('#ctypeFilter').val(); // Ambil nilai filter ctypeFilter

    // Reload datatable dan kirim data filter ke server
    table_proforma.DataTable().ajax.reload(function(json) {
        // Setelah reload, bisa melakukan tambahan jika perlu
        console.log('Filter applied:', ctypeFilter);
    });

    $('#filter').modal('hide'); // Tutup modal setelah klik filter
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table_proforma = $('#t_proforma');
    table_proforma.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

/* MRP */
var save_method; //for save method string
var table_proforma;
function proformaTable(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table_proforma = $('#t_proforma');
        table_proforma.DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language":  languageDatatable(),
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "createdRow": function (row, data, dataIndex) {
                // var formType = valueForm.slice(-1).toUpperCase().trim(); // Ambil huruf terakhir (A/B)
                var valueRole = $('td', row).eq(3).text().trim();
                if (valueRole === 'JTS') {
                    $('td', row).eq(3).css('background-color', '#446edfff'); // biru
                } else if (valueRole === 'MSMI') {
                    $('td', row).eq(3).css('background-color', '#ff4646ff'); // hijau
                } else if (valueRole === 'MSMJ') {
                    $('td', row).eq(3).css('background-color', '#4ef154ff'); // hijau
                }
                
                var valueForm = $('td', row).eq(4).text().trim();
                if (valueForm === 'PROFORMA') {
                    $('td', row).eq(4).css('background-color', '#ecff73ff'); // biru
                } else if (valueForm === 'INVOICE') {
                    $('td', row).eq(4).css('background-color', '#76e97bff'); // hijau
                }
            },
            // "dom": 'Bfrtip',
            // "buttons": [
            //     'pageLength','excel'
            // ],
            "ajax": {
                "url": HOST_URL + 'sales/presales/list_proforma',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
                    //data.idgroup = $('#idgroup').val();
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

    };
    return initTable();
}

function reloadTproforma()
{
    var table_proforma = $('#t_proforma');
    table_proforma.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$(document).ready(function() {
    // steelgradeTable();
    // stdUsage();
    // mrpGroupTable();
    proformaTable();
});