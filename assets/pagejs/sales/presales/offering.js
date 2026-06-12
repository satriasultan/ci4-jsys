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
    var table_sikbsp = $('#t_offering');
    
    // Pastikan untuk menyertakan data filter dalam permintaan AJAX
    var ctypeFilter = $('#ctypeFilter').val(); // Ambil nilai filter ctypeFilter

    // Reload datatable dan kirim data filter ke server
    table_sikbsp.DataTable().ajax.reload(function(json) {
        // Setelah reload, bisa melakukan tambahan jika perlu
        console.log('Filter applied:', ctypeFilter);
    });

    $('#filter').modal('hide'); // Tutup modal setelah klik filter
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table_offering = $('#t_offering');
    table_offering.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

/* MRP */
var save_method; //for save method string
var table_offering;
function offeringTable(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table_offering = $('#t_offering');
        table_offering.DataTable({
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
                var valueForm = $('td', row).eq(3).text().trim();
                // var formType = valueForm.slice(-1).toUpperCase().trim(); // Ambil huruf terakhir (A/B)

                if (valueForm === 'JTS') {
                    $('td', row).eq(3).css('background-color', '#446edfff'); // biru
                } else if (valueForm === 'MSMI') {
                    $('td', row).eq(3).css('background-color', '#ff4646ff'); // hijau
                } else if (valueForm === 'MSMJ') {
                    $('td', row).eq(3).css('background-color', '#eb59e9ff'); // hijau
                }
                
            },
            // "dom": 'Bfrtip',
            // "buttons": [
            //     'pageLength','excel'
            // ],
            "ajax": {
                "url": HOST_URL + 'sales/presales/list_offering',
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

function reloadToffering()
{
    var table_offering = $('#t_offering');
    table_offering.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}

$(document).ready(function() {
    // steelgradeTable();
    // stdUsage();
    // mrpGroupTable();
    offeringTable();
});