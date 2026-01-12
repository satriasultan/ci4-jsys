/*
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


function chartStackedx(){
var departmen = null;
var title_ttltelat = null;
var title_durasimenit = null;
var ttlnik = null;
var ttlkaryawantelat = null;
var durasimenit = null;

    $.ajax({
        url: HOST_URL + 'dashboard/frekuensiTerlambat',
        async: false,
        type: "POST",
        global: false,
        dataType: 'html',
        success: function (data) {
            var json = jQuery.parseJSON(data);
            departmen = json.departmen;
            title_ttltelat = json.title_ttltelat;
            title_durasimenit = json.title_durasimenit;
            ttlnik = json.ttlnik;
            ttlkaryawantelat = json.ttlkaryawantelat;
            durasimenit = json.durasimenit;

        }
    });

    var speedCanvasd = $('#frekuensiTerlambat');

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 15;

    var densityData = {
      label: title_ttltelat,
      data: ttlkaryawantelat,
      backgroundColor: "#396cb7",
      borderWidth: 0,
      yAxisID: "y-axis-density"
    };

    var gravityData = {
      label: title_durasimenit,
      data: durasimenit,
      backgroundColor: "#fd1212",
      borderWidth: 0,
      yAxisID: "y-axis-gravity"
    };

    var planetData = {
      labels: departmen,
      datasets: [densityData, gravityData]
    };

    var chartOptions = {
      scales: {
        xAxes: [{
          barPercentage: 1,
          categoryPercentage: 0.6
        }],
        yAxes: [{
          id: "y-axis-density"
        }, {
          id: "y-axis-gravity"
        }]
      }
    };

    var barChart = new Chart(speedCanvasd, {
      type: 'bar',
      data: planetData,
      options: chartOptions
    });
}

var save_method; //for save method string
var table;
//"use strict";
function tableMinstock(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#tminimumstock');
        table.DataTable({
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
            "responsive": true,
            "lengthMenu": [ 5, 25, 50, 75, 100 ],
            "pageLength": 5,
            "ajax": {
                "url": HOST_URL + 'dashboard/list_minstock',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
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
function reload_table()
{
    var table = $('#tminimumstock');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}
$('#btn-filter').click(function(){ //button filter event click
    var table = $('#tminimumstock');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#tminimumstock');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

/* FUNCTION JASA & PEMBAYARAN */
function tableJasaPembayaran(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#jasapembayaran');
        table.DataTable({
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
            "responsive": true,
            "lengthMenu": [ 5, 25, 50, 75, 100 ],
            "pageLength": 5,
            "ajax": {
                "url": HOST_URL + 'dashboard/list_jasapembayaran',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
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



/* FUNCTION FORM PP */
function tableFormPP(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#formpp');
        table.DataTable({
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
            "responsive": true,
            "lengthMenu": [ 5, 25, 50, 75, 100 ],
            "pageLength": 5,
            "ajax": {
                "url": HOST_URL + 'dashboarduser/list_formpp',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
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


/* FUNCTION FORM II */
function tableFormII(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#formii');
        table.DataTable({
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
            "responsive": true,
            "lengthMenu": [ 5, 25, 50, 75, 100 ],
            "pageLength": 5,
            "ajax": {
                "url": HOST_URL + 'dashboarduser/list_formii',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
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



/* FUNCTION FORM PK */
function tableFormPK(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#formpk');
        table.DataTable({
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
            "responsive": true,
            "lengthMenu": [ 5, 25, 50, 75, 100 ],
            "pageLength": 5,
            "ajax": {
                "url": HOST_URL + 'dashboarduser/list_formpk',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
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

/* Summary pembelian */
function summaryPembelian()
{
    var sum_po = 0;
    var sum_ots_po = 0;
    var sum_finish_po = 0;
    var sum_void = 0;


    $.ajax({
        url: HOST_URL + 'dashboard/api_summary_pembelian',
        async: false,
        type: "POST",
        global: false,
        dataType: 'html',
        success: function (data) {
            var json = jQuery.parseJSON(data);
            sum_po = json.sum_po;
            sum_ots_po = json.sum_ots_po;
            sum_finish_po = json.sum_finish_po;
            sum_void = json.sum_void;

        }
    });

    $('#sum_po').html(+sum_po);
    $('#sum_ots_po').html(+sum_ots_po);
    $('#sum_finish_po').html(+sum_finish_po);
    $('#sum_void').html(+sum_void);

}
/* End Summary pembelian */
function transactionStacked()
{
    //lembur global
    var bulan = null;
    var dt = null;
    var iz = null;
    var sk = null;
    var ct = null;
    var terlambat = null;
    var izin = null;
    var cuti = null;
    var sakit = null;

    $.ajax({
        url: HOST_URL + 'dashboard/api_transaction_tahunan',
        async: false,
        type: "POST",
        global: false,
        dataType: 'html',
        success: function (data) {
            var json = jQuery.parseJSON(data);
            bulan = json.bulan;

            dt = json.dt;
            iz = json.iz;
            sk = json.sk;
            ct = json.ct;
            terlambat = json.terlambat;
            izin = json.izin;
            cuti = json.cuti;
            sakit = json.sakit;
        }
    });

    var crutx = $('#transactionStacked');
    var myChart = new Chart(crutx, {
        type: 'bar',
        data: {
            labels: bulan,
            datasets: [{
                label: dt,
                backgroundColor: "#3bf3b9",
                data: terlambat,
            }, {
                label: iz,
                backgroundColor: "#a142e5",
                data: izin,
            }, {
                label: sk,
                backgroundColor: "#fd1212",
                data: sakit,
            },{
                label: ct,
                backgroundColor: "#a3fd12",
                data: cuti,
            }],
        },
        options: {
            tooltips: {
                displayColors: true,
                callbacks:{
                    mode: 'x',
                },
            },
            scales: {
                xAxes: [{
                    stacked: false,
                    gridLines: {
                        display: false,
                    }
                }],
                yAxes: [{
                    stacked: false,
                    ticks: {
                        beginAtZero: true,
                    },
                    type: 'linear',
                }]
            },
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'bottom' },
        }
    });

}

$(document).ready(function() {

    //chartStackedx();
    //summaryPembelian();
    //tableMinstock();
    //transactionStacked();
    tableJasaPembayaran();
    tableFormPP();
    tableFormII();
    tableFormPK();
});