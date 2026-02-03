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
//"use strict";
function tableItem(){
    // var lg = languageDatatable;
    var initTable = function () {
        var table = $('#titem');
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
            "responsive": false,
            "lengthMenu": [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength','excel'
            ],
            "ajax": {
                "url": HOST_URL + 'master/data/list_mitem',
                "type": "POST",
                "data": function(data) {
                    //data.tglrange = $('#tglrange').val();
                    data.idgroup = $('#idgroup').val();
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
    var table = $('#titem');
    table.DataTable().ajax.reload(); //reload datatable ajax
    //console.log('HALO HALO BANDUNG');
}


$('#btn-filter').click(function(){ //button filter event click
    var table = $('#titem');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});
$('#btn-reset').click(function(){ //button reset event click
    $('#form-filter')[0].reset();
    var table = $('#titem');
    table.DataTable().ajax.reload(); //reload datatable ajax
    $('#filter').modal('hide');
});

//EDIT ITEM
function documentReadable(){
    // $("#loadMe").modal({
    //     backdrop: "static", //remove ability to close modal with click
    //     keyboard: false, //remove option to close with keyboard
    //     show: false //Display loader!
    // });
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'master/data/showDetailItem' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            // Fetch the preselected item, and add to the control
            $('[name="id"]').val(json.dataTables.items[0].id);
            $('[name="idbarang"]').val(json.dataTables.items[0].idbarang);
            $('[name="nmbarang"]').val(json.dataTables.items[0].nmbarang);
            $('[name="kdtax"]').val(json.dataTables.items[0].kdtax);
            $('[name="originalno"]').val(json.dataTables.items[0].originalno.trim());
            $('[name="lokasireff"]').val(json.dataTables.items[0].lokasireff);
            $('[name="satuantax"]').val(json.dataTables.items[0].satuantax);

            setJtsValue('[name="berat"]',convertToDbNumber(json.dataTables.items[0].berat));
            setJtsValue('[name="gw"]',convertToDbNumber(json.dataTables.items[0].gw));
            setJtsValue('[name="volume"]',convertToDbNumber(json.dataTables.items[0].volume));
            setJtsValue('[name="psize"]',convertToDbNumber(json.dataTables.items[0].psize));
            setJtsValue('[name="lsize"]',convertToDbNumber(json.dataTables.items[0].lsize));
            setJtsValue('[name="tsize"]',convertToDbNumber(json.dataTables.items[0].tsize));

            setJtsValue('[name="maks_daystock"]',convertToDbNumber(json.dataTables.items[0].maks_daystock));
            setJtsValue('[name="minstock"]',convertToDbNumber(json.dataTables.items[0].minstock1));

            $('[name="discontinue"]').prop(
                    'checked',
                    $.trim((json.dataTables.items[0].discontinue || '')).toUpperCase() === 'YES'
                );

            $('[name="issn"]').prop(
                    'checked',
                    $.trim((json.dataTables.items[0].issn || '')).toUpperCase() === 'YES'
                );
            //$('[name="idgroup"]').val(json.dataTables.items[0].idgroup);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mgroup' + '?var=' + json.dataTables.items[0].idgroup,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                const item = datax.items[0];
                var option = new Option(datax.items[0].nmgroup, datax.items[0].idgroup, true, true);
                $('[name="idgroup"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgroup"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: item
                    }
                });
            });
            $('[name="subunitenable"]').val(json.dataTables.items[0].subunitenable.trim()).trigger('change');
            //$('[name="unit"]').val(json.dataTables.items[0].unit);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_unit' + '?var=' + json.dataTables.items[0].unit,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
                $('[name="unit"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="unit"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="subunit"]').val(json.dataTables.items[0].subunit);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_subunit' + '?var=' + json.dataTables.items[0].subunit,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
                $('[name="subunit"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="subunit"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="idbarcode"]').val(json.dataTables.items[0].idbarcode);
            $('[name="mfgdate"]').val(json.dataTables.items[0].mfgdate1);
            $('[name="expdate"]').val(json.dataTables.items[0].expdate1);
            // $('[name="maks_daystock"]').val(json.dataTables.items[0].maks_daystock);
            //$('[name="deflocation"]').val(json.dataTables.items[0].deflocation);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + json.dataTables.items[0].deflocation,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlocation, datax.items[0].idlocation, true, true);
                $('[name="deflocation"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="deflocation"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="defarea"]').val(json.dataTables.items[0].defarea);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_marea' + '?var=' + json.dataTables.items[0].defarea,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmarea, datax.items[0].idarea, true, true);
                $('[name="defarea"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="defarea"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });



             $('[name="discontinue"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].discontinue || '')).toUpperCase() === 'YES'
            );


             $('[name="issn"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].issn || '')).toUpperCase() === 'YES'
            );


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_golonganbarang' + '?var=' + json.dataTables.items[0].idgolonganbarang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgolonganbarang, datax.items[0].idgolonganbarang, true, true);
                $('[name="idgolonganbarang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgolonganbarang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


             $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_jenisproduk' + '?var=' + json.dataTables.items[0].idjenisproduk,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmjenisproduk, datax.items[0].idjenisproduk, true, true);
                $('[name="idjenisproduk"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idjenisproduk"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


             $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_kelompokbarang' + '?var=' + json.dataTables.items[0].idkelompokbarang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmkelompokbarang, datax.items[0].idkelompokbarang, true, true);
                $('[name="idkelompokbarang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idkelompokbarang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


             $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_principal' + '?var=' + json.dataTables.items[0].idprincipal,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmprincipal, datax.items[0].idprincipal, true, true);
                $('[name="idprincipal"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idprincipal"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            setCoaSelect2('ppersediaan',  json.dataTables.items[0].ppersediaan);
            setCoaSelect2('psj',          json.dataTables.items[0].psj);
            setCoaSelect2('salesakun',    json.dataTables.items[0].salesakun);
            setCoaSelect2('pcogs',        json.dataTables.items[0].pcogs);
            setCoaSelect2('phpproduksi',  json.dataTables.items[0].phpproduksi);
            setCoaSelect2('pjasa',        json.dataTables.items[0].pjasa);
            setCoaSelect2('pwaste',       json.dataTables.items[0].pwaste);


            loadCoaByVisibleFields(json.dataTables.items[0]);


            // $('[name="minstock"]').val(json.dataTables.items[0].minstock1);
            $('[name="setminstock"]').val(json.dataTables.items[0].setminstock.trim()).trigger('change');

            $('[name="description"]').val(json.dataTables.items[0].description);
            $('[name="chold"]').val(json.dataTables.items[0].chold.trim()).trigger('change');

            $('[name="idbarang"]').prop('readonly', true);

        },
        complete: function(){
            $("#loadMe").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });

    $("#loadMe").modal("hide");
}


function loadCoaByVisibleFields(data) {
    if ($('.field-ppersediaan').is(':visible')) {
        setCoaSelect2('ppersediaan', data.ppersediaan);
    }
    if ($('.field-psj').is(':visible')) {
        setCoaSelect2('psj', data.psj);
    }
    if ($('.field-salesakun').is(':visible')) {
        setCoaSelect2('salesakun', data.salesakun);
    }
    if ($('.field-pcogs').is(':visible')) {
        setCoaSelect2('pcogs', data.pcogs);
    }
    if ($('.field-phpproduksi').is(':visible')) {
        setCoaSelect2('phpproduksi', data.phpproduksi);
    }
    if ($('.field-pjasa').is(':visible')) {
        setCoaSelect2('pjasa', data.pjasa);
    }
    if ($('.field-pwaste').is(':visible')) {
        setCoaSelect2('pwaste', data.pwaste);
    }
}



function setCoaSelect2(fieldName, coaValue) {

    // guard: null / undefined / empty
    if (!coaValue || typeof coaValue !== 'string') {
        return;
    }

    var coaTrim = coaValue.trim();
    if (coaTrim === '') {
        return;
    }

    $.ajax({
        type: 'GET',
        url: HOST_URL + 'api/globalmodule/list_coa' + '?var=' + coaTrim,
        dataType: 'json',
        delay: 100
    }).then(function (res) {

        if (!res.items || res.items.length === 0) return;

        var item = res.items[0];

        var option = new Option(
            item.nmcoa,
            item.idcoa,
            true,
            true
        );

        $('[name="' + fieldName + '"]')
            .append(option)
            .trigger('change')
            .trigger({
                type: 'select2:select',
                params: { data: item }
            });
    });
}




function setJtsValue(selector, value) {
    $(selector).val(value);
    _jtsseparator($(selector)[0]);
}




$(document).on('input', '.jtsseparator', function () {
    _jtsseparator(this);
});
/* FOR INPUT FUNCTION */

$('#formInputItem').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'fa fa-check',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        idbarang: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                },
            }
        },
        nmbarang: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idgroup: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        unit: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        idbarcode: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
        chold: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        }
    },
    excluded: [':disabled']
});

function saveInputItem() {

    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Data akan disimpan, Apakah anda yakin...?',
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {

            var validator = $('#formInputItem').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                
                const numericFields = [
                    'berat',
                    'gw',
                    'volume',
                    'psize',
                    'lsize',
                    'tsize',
                    'maks_daystock',
                    'minstock'
                ];

                numericFields.forEach(function (field) {
                    const $el = $('[name="' + field + '"]');
                    if ($el.length) {
                        $el.val(convertToDbNumber($el.val()));
                    }
                });

                
                $('[name="interngroup"]').val(
                    $('#interngroup').is(':checked') ? 'YES' : 'NO'
                );

                $('[name="blacklist"]').val(
                    $('#blacklist').is(':checked') ? 'YES' : 'NO'
                );
                // ajax adding data to database
                var form = $('#formInputItem');
                var formdata = false;
                if (window.FormData){
                    formdata = new FormData(form[0]);
                }
                $.ajax({
                    url: HOST_URL + 'master/data/saveDataItem',
                    type: "POST",
                    data: formdata ? formdata : form.serialize(),
                    cache       : false,
                    contentType : false,
                    processData : false,
                    datatype : "JSON",
                    dataFilter: function (data) {
                        var json = jQuery.parseJSON(data);
                        if (json.status) //if success close modal and reload ajax table
                        {
                            Swal.fire({
                                title: 'Berhasil...!!!',
                                text: json.messages,
                                backdrop: true,
                                allowOutsideClick: false,
                                showConfirmButton: true,
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: `Ok`,
                                icon: 'success',
                                //denyButtonText: `Don't save`,
                            }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    //Swal.fire('Saved!', '', 'success')
                                    window.location.replace(HOST_URL + 'master/data/barang')
                                    //window.location.reload()
                                }
                                // } else if (result.isDenied) {
                                //     Swal.fire('Changes are not saved', '', 'info')
                                // }
                            })
                            // var x = confirm(json.messages);
                            // if (x) return
                            //else return window.location.replace("<?php echo site_url('/trans/skperingatan')?>") + "";
                        } else {
                            //alert(json.messages);
                            Swal.fire({
                                title: 'Galat...!!!',
                                text: json.messages,
                                backdrop: true,
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                showDenyButton: false,
                                showCancelButton: false,
                                icon: 'error',
                                //denyButtonText: `Don't save`,
                            });
                        }
                        $('#btnSave').text('save'); //change button text
                        $('#btnSave').attr('disabled', false); //set button enable


                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // alert('Gagal Menyimpan / Ubah data / data sudah ada');
                        swal({
                            title: "Galat!!",
                            text: json.messages,
                            type: "error"
                        });
                        $('#btnSave').text('save'); //change button text
                        $('#btnSave').attr('disabled', false); //set button enable

                    }
                });
                //alert("FInish Input");
            }


        } else if (result.isDenied) {
            return false;
        }
    })

}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH GROUP ++++++++++++++++++++++++++++++++++++++++//

var defaultInitialGroup = 'BRG';
$("#idgroup").select2({
    placeholder: "Type/Chose Category Item",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mgroup',
        type: 'POST',
        width: '100%',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialGroup,
                _parameterx_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatGroup, // omitted for brevity, see the source of this page
    templateSelection: formatGroupSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
    const data = e.params.data;

    // normalize text (trim + uppercase)
    const groupName = (data.nmgroup || '').trim().toUpperCase();

    if (ALLOWED_GROUPS.includes(groupName)) {
        showUmumTab();
    } else {
        hideUmumTab();
    }


    applyGroupAccountRule(groupName);
});
/* Format Group */
function formatGroup(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idgroup +"   <i class='fa fa-circle-o'></i>   "+ repo.nmgroup +"</div>";
    return markup;
}

function formatGroupSelection(repo) {
    return repo.nmgroup || repo.text;
}

// $('#idgroup').on('select2:select', function (e) {
//     const data = e.params.data;

//     // normalize text (trim + uppercase)
//     const groupName = (data.items[0].nmgroup || '').trim().toUpperCase();

//     if (ALLOWED_GROUPS.includes(groupName)) {
//         showUmumTab();
//     } else {
//         hideUmumTab();
//     }


//     applyGroupAccountRule(groupName);
// });

function applyGroupAccountRule(groupName) {
    resetAccountFields();

    const fields = GROUP_FIELD_RULES[groupName];
    if (!fields) return;

    fields.forEach(f => {
        $('.field-' + f).show();
        $('#' + f).prop('required', true);
    });
}

const GROUP_FIELD_RULES = {
    'BARANG': [
        'ppersediaan', 'psj', 'salesakun', 'pcogs', 'phpproduksi'
    ],
    'PRODUKSI': [
        'ppersediaan', 'psj', 'salesakun', 'pcogs', 'phpproduksi'
    ],
    'BAHAN BAKU': [
        'ppersediaan', 'psj', 'salesakun', 'pcogs', 'phpproduksi'
    ],
    'BAHAN PEMBANTU': [
        'ppersediaan', 'psj', 'salesakun', 'pcogs', 'phpproduksi'
    ],
    'JASA': [
        'pjasa', 'salesakun'
    ],
    'PAKET': [
        'salesakun'
    ],
    'WIP': [
        'pwaste', 'salesakun'
    ]
};



function resetAccountFields() {
    const allFields = [
        'ppersediaan','psj','salesakun','pcogs',
        'phpproduksi','pjasa','pwaste'
    ];

    allFields.forEach(f => {
        $('.field-' + f).hide();
        $('#' + f).prop('required', false).val(null).trigger('change');
    });
}


const ALLOWED_GROUPS = [
    'BARANG',
    'PRODUKSI',
    'BAHAN BAKU',
    'BAHAN PEMBANTU'
];


function showUmumTab() {
    $('#tab-umum-wrapper').show();

    // optional: auto pindah ke tab umum
    // $('#umum-tab').tab('show');
}

function hideUmumTab() {
    // jika tab umum sedang aktif, pindahkan ke General
    if ($('#umum-tab').hasClass('active')) {
        $('#general-tab').tab('show');
    }

    $('#tab-umum-wrapper').hide();
}



var defaultInitialUnit = 'UNIT';
$("#unit").select2({
    placeholder: "Type/Chose Your Unit",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_unit',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialUnit,
                _parameterx_: defaultInitialUnit,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatUnit, // omitted for brevity, see the source of this page
    templateSelection: formatUnitSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#subunit option[value]").remove();
    var newOptions = []; // the result of your JSON request
    $("#subunit").val(null).trigger('change');
    //console.log($("#newdept").val());
});
/* Format formatUnit */
function formatUnit(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idunit +"</div>";
    return markup;
}

function formatUnitSelection(repo) {
    return repo.idunit || repo.text;
}



var defaultInitialSubUnit = 'SUBUNIT';
$("#subunit").select2({
    placeholder: "Type/Chose Your Sub Unit",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_subunit',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $("#unit").val(),
                _parameterx_: $("#unit").val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatUnit, // omitted for brevity, see the source of this page
    templateSelection: formatUnitSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {

});


var defaultInitialLocation = '';
$("#deflocation").select2({
    placeholder: "Type/Chose Location",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mlocation',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitialLocation,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatLocation, // omitted for brevity, see the source of this page
    templateSelection: formatLocationSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    $("#defarea option[value]").remove();
    var newOptions = []; // the result of your JSON request
    $("#defarea").val(null).trigger('change');
    //console.log($("#newdept").val());
});

/*Location*/
function formatLocation(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idlocation +"   <i class='fa fa-circle-o'></i>   "+ repo.nmlocation +"</div>";
    return markup;
}

function formatLocationSelection(repo) {
    return repo.nmlocation || repo.text;
}



var defaultInitialArea = '';
$("#defarea").select2({
    placeholder: "Type/Chose Your Sub Unit",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_marea',
        type: 'POST',
        dataType: 'json',
        width: '100%',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: $("#deflocation").val(),
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: false
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatArea, // omitted for brevity, see the source of this page
    templateSelection: formatAreaSelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {

});
/*Location*/
function formatArea(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idarea +"   <i class='fa fa-circle-o'></i>   "+ repo.nmarea +"</div>";
    return markup;
}

function formatAreaSelection(repo) {
    return repo.nmarea || repo.text;
}

function editItem(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Would be change? ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'master/data/edit' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}



function detailItem(e){
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Would be change? ' + e,
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            window.location.replace(HOST_URL + 'master/data/detail' + '/?var=' + e)
        } else if (result.isDenied) {
            return false;
        }
    })
}




function formatGolonganBarang(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idgolonganbarang +"   <i class='fa fa-circle'></i>   "+ repo.nmgolonganbarang +"  </div>";
    return markup;
}
function formatGolonganBarangSelection(repo) {
    return repo.nmgolonganbarang || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idgolonganbarang").select2({
    placeholder: "Ketik/Pilih Golongan Barang",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_golonganbarang',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatGolonganBarang, // omitted for brevity, see the source of this page
    templateSelection: formatGolonganBarangSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});



function formatJenisProduk(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idjenisproduk +"   <i class='fa fa-circle'></i>   "+ repo.nmjenisproduk +"  </div>";
    return markup;
}
function formatJenisProdukSelection(repo) {
    return repo.nmjenisproduk || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idjenisproduk").select2({
    placeholder: "Ketik/Pilih Jenis Produk",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_jenisproduk',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatJenisProduk, // omitted for brevity, see the source of this page
    templateSelection: formatJenisProdukSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});



function formatKelompokBarang(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idkelompokbarang +"   <i class='fa fa-circle'></i>   "+ repo.nmkelompokbarang +"  </div>";
    return markup;
}
function formatKelompokBarangSelection(repo) {
    return repo.nmkelompokbarang || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idkelompokbarang").select2({
    placeholder: "Ketik/Pilih Kelompok Barang",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_kelompokbarang',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatKelompokBarang, // omitted for brevity, see the source of this page
    templateSelection: formatKelompokBarangSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});



function formatPrincipal(repo) {
if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idprincipal +"   <i class='fa fa-circle'></i>   "+ repo.nmprincipal +"  </div>";
    return markup;
}
function formatPrincipalSelection(repo) {
    return repo.nmprincipal || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#idprincipal").select2({
    placeholder: "Ketik/Pilih Principal",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_principal',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatPrincipal, // omitted for brevity, see the source of this page
    templateSelection: formatPrincipalSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
});




function formatCOA(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idcoa +"   <i class='fa fa-circle'></i>   "+ repo.nmcoa +" <i class='fa fa-circle'></i> LEVEL "+ repo.level + " </div>";
    return markup;
}
function formatCOASelection(repo) {
    return repo.nmcoa || repo.text;
}

// ======================= PEMBELIAN ==================================

//var defaultInitialGol = $("#newdept").val();
$("#ppersediaan").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



$("#psj").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#salesakun").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});



$("#pcogs").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});





$("#phpproduksi").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#pjasa").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




$("#pwaste").select2({
    placeholder: "Ketik/Pilih COA",
    allowClear: true,
    width: '100%',
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_coa',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: '',
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatCOA, // omitted for brevity, see the source of this page
    templateSelection: formatCOASelection // omitted for brevity, see the source of this page
}).on("select2:selecting", function () {
    // $("#id_desaktp").val(null).trigger('change');
    // $("#id_kecktp").val(null).trigger('change');
});




function documentReadableDetail(){
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'master/data/showDetailItem' + '?var=' + $('[name="id"]').val(),
        dataType: 'json',
        dataFilter: function(data) {
            var json = jQuery.parseJSON(data);
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;

            // Fetch the preselected item, and add to the control
            $('[name="id"]').val(json.dataTables.items[0].id);
            $('[name="idbarang"]').val(json.dataTables.items[0].idbarang);
            $('[name="nmbarang"]').val(json.dataTables.items[0].nmbarang);
            $('[name="kdtax"]').val(json.dataTables.items[0].kdtax);
            $('[name="originalno"]').val(json.dataTables.items[0].originalno.trim());
            $('[name="lokasireff"]').val(json.dataTables.items[0].lokasireff);
            $('[name="satuantax"]').val(json.dataTables.items[0].satuantax);

            setJtsValue('[name="berat"]',convertToDbNumber(json.dataTables.items[0].berat));
            setJtsValue('[name="gw"]',convertToDbNumber(json.dataTables.items[0].gw));
            setJtsValue('[name="volume"]',convertToDbNumber(json.dataTables.items[0].volume));
            setJtsValue('[name="psize"]',convertToDbNumber(json.dataTables.items[0].psize));
            setJtsValue('[name="lsize"]',convertToDbNumber(json.dataTables.items[0].lsize));
            setJtsValue('[name="tsize"]',convertToDbNumber(json.dataTables.items[0].tsize));

            setJtsValue('[name="maks_daystock"]',convertToDbNumber(json.dataTables.items[0].maks_daystock));
            setJtsValue('[name="minstock"]',convertToDbNumber(json.dataTables.items[0].minstock1));

            $('[name="discontinue"]').prop(
                    'checked',
                    $.trim((json.dataTables.items[0].discontinue || '')).toUpperCase() === 'YES'
                );

            $('[name="issn"]').prop(
                    'checked',
                    $.trim((json.dataTables.items[0].issn || '')).toUpperCase() === 'YES'
                );
            //$('[name="idgroup"]').val(json.dataTables.items[0].idgroup);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mgroup' + '?var=' + json.dataTables.items[0].idgroup,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                const item = datax.items[0];
                var option = new Option(datax.items[0].nmgroup, datax.items[0].idgroup, true, true);
                $('[name="idgroup"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgroup"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: item
                    }
                });
            });
            $('[name="subunitenable"]').val(json.dataTables.items[0].subunitenable.trim()).trigger('change');
            //$('[name="unit"]').val(json.dataTables.items[0].unit);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_unit' + '?var=' + json.dataTables.items[0].unit,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
                $('[name="unit"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="unit"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="subunit"]').val(json.dataTables.items[0].subunit);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_subunit' + '?var=' + json.dataTables.items[0].subunit,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].idunit, datax.items[0].idunit, true, true);
                $('[name="subunit"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="subunit"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            $('[name="idbarcode"]').val(json.dataTables.items[0].idbarcode);
            $('[name="mfgdate"]').val(json.dataTables.items[0].mfgdate1);
            $('[name="expdate"]').val(json.dataTables.items[0].expdate1);
            // $('[name="maks_daystock"]').val(json.dataTables.items[0].maks_daystock);
            //$('[name="deflocation"]').val(json.dataTables.items[0].deflocation);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_mlocation' + '?var=' + json.dataTables.items[0].deflocation,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmlocation, datax.items[0].idlocation, true, true);
                $('[name="deflocation"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="deflocation"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });
            //$('[name="defarea"]').val(json.dataTables.items[0].defarea);
            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_marea' + '?var=' + json.dataTables.items[0].defarea,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmarea, datax.items[0].idarea, true, true);
                $('[name="defarea"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="defarea"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });



             $('[name="discontinue"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].discontinue || '')).toUpperCase() === 'YES'
            );


             $('[name="issn"]').prop(
                'checked',
                $.trim((json.dataTables.items[0].issn || '')).toUpperCase() === 'YES'
            );


            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_golonganbarang' + '?var=' + json.dataTables.items[0].idgolonganbarang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmgolonganbarang, datax.items[0].idgolonganbarang, true, true);
                $('[name="idgolonganbarang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idgolonganbarang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


             $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_jenisproduk' + '?var=' + json.dataTables.items[0].idjenisproduk,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmjenisproduk, datax.items[0].idjenisproduk, true, true);
                $('[name="idjenisproduk"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idjenisproduk"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


             $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_kelompokbarang' + '?var=' + json.dataTables.items[0].idkelompokbarang,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmkelompokbarang, datax.items[0].idkelompokbarang, true, true);
                $('[name="idkelompokbarang"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idkelompokbarang"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });


             $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_principal' + '?var=' + json.dataTables.items[0].idprincipal,
                dataType: 'json',
                delay: 250,
            }).then(function (datax) {
                // create the option and append to Select2
                var option = new Option(datax.items[0].nmprincipal, datax.items[0].idprincipal, true, true);
                $('[name="idprincipal"]').append(option).trigger('change');

                // manually trigger the `select2:select` event
                $('[name="idprincipal"]').trigger({
                    type: 'select2:select',
                    params: {
                        data: datax
                    }
                });
            });

            setCoaSelect2('ppersediaan',  json.dataTables.items[0].ppersediaan);
            setCoaSelect2('psj',          json.dataTables.items[0].psj);
            setCoaSelect2('salesakun',    json.dataTables.items[0].salesakun);
            setCoaSelect2('pcogs',        json.dataTables.items[0].pcogs);
            setCoaSelect2('phpproduksi',  json.dataTables.items[0].phpproduksi);
            setCoaSelect2('pjasa',        json.dataTables.items[0].pjasa);
            setCoaSelect2('pwaste',       json.dataTables.items[0].pwaste);


            loadCoaByVisibleFields(json.dataTables.items[0]);


            // $('[name="minstock"]').val(json.dataTables.items[0].minstock1);
            $('[name="setminstock"]').val(json.dataTables.items[0].setminstock.trim()).trigger('change');

            $('[name="description"]').val(json.dataTables.items[0].description);
            $('[name="chold"]').val(json.dataTables.items[0].chold.trim()).trigger('change');

            $('[name="idbarang"]').prop('readonly', true);
            $('form').find('input, select, textarea, button').not('[type="hidden"]').prop('disabled', true);
            $('form').find('button').hide()
        },
        complete: function(){
            $("#loadMe").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });

    $("#loadMe").modal("hide");
}



$(document).ready(function() {

    tableItem();
    $('#checkboxnik').change(function() {
        // this will contain a reference to the checkbox
        if (this.checked) {
            var valnik = $('#nik').val();
            // the checkbox is now checked
            //alert('Checked');
            $('#username').prop('readonly', true);
            $('#username').val(valnik);
        } else {
            // the checkbox is now no longer checked
            //alert('Un Checked');
            $('#username').prop('readonly', false);
            $('#username').val('');
        }
    });
    $('#nik').change(function() {
        if ($('#checkboxnik').is(':checked')){
            var valnik = $(this).val();
            $('#username').val(valnik);
        }
    });
    //* input form */
    // var valueToScroll = 80;
    // $(".card").scrollTop(valueToScroll);
    if ($('[name="type"]').val() === 'EDIT') {
        documentReadable();
    }
    if ($('[name="type"]').val() === 'DETAIL') {
        documentReadableDetail();
    }


});