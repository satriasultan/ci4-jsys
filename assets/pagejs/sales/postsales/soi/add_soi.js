
$(".fjurnal").on("change", function () {
    //$("#njurnal").val('00011');
    //Ajax Load data from ajax
    njurnal();
});

let isInitializingPO = false;
    

function njurnal(){
    //$("#njurnal").val('00011');
    //Ajax Load data from ajax
    var idjurnal = $("#idjurnal").val();
    var periode = $("#periode").val();
    var xsuffix = $("#xsuffix").val();
    var grouptype = 'MRP';
    var modul = 'I.E.A.2';

    $.ajax({
        url: HOST_URL + 'api/globalmodule/njurnal',
        type: "POST",
        data:  {
            idjurnal: idjurnal,
            periode: periode,
            grouptype: grouptype,
            xsuffix: xsuffix,
            modul: modul,
        },
        dataType: "JSON",
        success: function(data)
        {
            $("#idjurnal").val(data.idjurnal);
            $("#periode").val(data.periode);
            $("#xsuffix").val(data.xsuffix);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
let skipRoleChange = false;

function documentReadable(){
    var docno = $('[name="docno"]').val()
    $.ajax({
        type: 'GET',
        url: HOST_URL + 'sales/postsales/showing_soitemp',
        data: { docno: docno },
        dataType: 'json',
        success: function(json) {
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;
            if(json.dataTables.items.length != 0){
                if (json.dataTables.items[0].status.trim() === 'E') {
                    console.log('--'+ json.dataTables.items[0].status );
                    $('[name="docno"]').val(json.dataTables.items[0].docno).prop('readonly', true);
                    var docnoData = json.dataTables.items[0].docno.trim();
                    let parts = docnoData.split('/'); 

                    // prefix = gabungan bagian sebelum angka
                    let prefix = parts[0];

                    // infix = angka urut
                    let infix = parts[1];

                    // sufix = gabungan setelah angka
                    let sufix = parts[2];

                    // set ke field
                    $('[name="prefix"]').val(prefix).prop('readonly', true);
                    $('[name="infix"]').val(infix).prop('readonly', true);
                    $('[name="sufix"]').val(sufix).prop('readonly', true);

    
    
    
                    
                    // var docdateValue = json.dataTables.items[0].docdate;
                    // if (docdateValue) {
                    //     $('[name="docdate"]').val(docdateValue).prop('readonly', true);
                    // } else {
                    //     $('[name="docdate"]').val("").prop('readonly', false);
                    // }
    
                    // Ambil data customer
                    // $.ajax({
                    //     type: 'GET',
                    //     url: HOST_URL + 'api/globalmodule/list_customer_new?var=' + json.dataTables.items[0].cust,
                    //     dataType: 'json',
                    // }).then(function (datax) {
                    //     var option = new Option(datax.items[0].nama_customer, datax.items[0].docno, true, true);
                    //     $('[name="cust"]').append(option).trigger('change').trigger({
                    //         type: 'select2:select',
                    //         params: { data: datax }
                    //     });
                    // });
                    isInitializingPO = true; // matikan dulu event PO


                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_sales_order?var=' + json.dataTables.items[0].po,
                        dataType: 'json',
                    }).then(function (datax) {
                        var option = new Option(datax.items[0].docno, datax.items[0].docno, true, true);
                        $('[name="po"]').append(option).trigger('change').trigger({
                            type: 'select2:select',
                            params: { data: datax }
                        });

                        isInitializingPO = false; // aktifkan lagi event normal 
                    });
    
                    // Set field lain
                    skipRoleChange = true;
                    $('[name="rolejob"]').val(json.dataTables.items[0].rolejob.trim()).trigger('change').prop('disabled',true);
                    $('[name="docdate"]').val(json.dataTables.items[0].docdate.trim()).prop('readonly', false);
                    // $('[name="jnsinvoice"]').val(json.dataTables.items[0].jnsinvoice.trim()).trigger('change').prop('disabled',true);
                    
                    // $('[name="po"]').val(dbToIndoFormat(json.dataTables.items[0].po)).prop('readonly', false);
                    // $('[name="cust"]').val(dbToIndoFormat(json.dataTables.items[0].cust)).prop('disabled', true);
                    $('[name="cust"]')
                        .val( safeVal(json.dataTables.items[0].cust)?.trim() )
                        .trigger('change')
                        .prop('disabled', true);

                    $('[name="pocust"]')
                        .val( safeVal(json.dataTables.items[0].pocust) )
                        .prop('readonly', false);

                    $('[name="revno"]')
                        .val( safeVal(json.dataTables.items[0].revno) )
                        .prop('readonly', false);

                    $('[name="desc"]')
                        .val( safeVal(json.dataTables.items[0].description) )
                        .prop('readonly', false);

                    // $('[name="facrisk"]').val(json.dataTables.items[0].facrisk.trim()).prop('readonly', false);
                    // $('[name="shipper"]').val(json.dataTables.items[0].shipper.trim()).prop('readonly', false);
                    // $('[name="consignee"]').val(json.dataTables.items[0].consignee.trim()).prop('readonly', false);
                    // $('[name="shippingmark"]').val(json.dataTables.items[0].shippingmark.trim()).prop('readonly', false);
                    // $('[name="notifyparty"]').val(json.dataTables.items[0].notifyparty.trim()).prop('readonly', false);

                    // $('[name="nmbank"]').val(json.dataTables.items[0].nmbank).prop('readonly', true);
                    // $('[name="alamatbank"]').val(json.dataTables.items[0].alamatbank).prop('readonly', true);
                    // $('[name="accno"]').val(json.dataTables.items[0].accno).prop('readonly', true);
                    // $('[name="accname"]').val(json.dataTables.items[0].accname).prop('readonly', true);
                    // $('[name="swiftcode"]').val(json.dataTables.items[0].swiftcode).prop('readonly', true);

                    
                    // $('[name="brand"]').val(json.dataTables.items[0].brand.trim()).prop('readonly', false);
                    // $('[name="size"]').val(json.dataTables.items[0].size.trim()).prop('readonly', false);
                    // $('[name="qty"]').val(json.dataTables.items[0].qty.trim()).prop('readonly', false);
                   // $('[name="pengiriman"]').val(json.dataTables.items[0].pengiriman.trim()).prop('readonly', false);
                    // $('[name="expdateph"]').val(json.dataTables.items[0].expdate.trim()).prop('readonly', false);
                    // $('[name="ketentuan"]').val(json.dataTables.items[0].ketentuan.trim()).prop('readonly', false);

                }
            }
        },
        complete: function(){
            $("#loadMe").modal("hide");
        },
        error: function(){
            console.log("Failed To Loading Data");
            $("#loadMe").modal("hide");
        }
    });
}

function submitSOI() {
    const formData = new FormData();
    const formElements = document.getElementById('soiForm').elements;
    
    for (let element of formElements) {
        if (element.name) {
            // if (element.name === 'exchangerate') {
            //     const convertedValue = convertToDbNumber(element.value);
            //     formData.append(element.name, convertedValue);
            // } else {
                formData.append(element.name, element.value);
            // }
        }
    }

    // // Validasi
    // if (!$('#exchangerate').val()) {
    //     Swal.fire('Error', 'Exchange rate tidak boleh kosong', 'error');
    //     return;
    // }

    const submitBtn = $('#submitBtn');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

    $.ajax({
        url: HOST_URL + 'sales/postsales/saveSOI',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(data) {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function safeTrim(val) {
    return (val && typeof val === "string") ? val.trim() : (val || "0");
}

function safeVal(val) {
    return (val === null || val === undefined || val === "null") ? "" : val;
}


$(document).on('keyup blur change', '.jtsseparator', function () {
    _jtsseparator(this);
});

function openAddNewItemModal() {
    $('#modalAddItem').modal('show');
    $('#idgroup').val(null).trigger('change');
    $('#subunitenable').val(null).trigger('change');
    $('#unit').val(null).trigger('change');
    $('#subunit').val(null).trigger('change');
    $('#deflocation').val(null).trigger('change');
    $('#defarea').val(null).trigger('change');
    $('#setminstock').val(null).trigger('change');
    $('#chold').val(null).trigger('change');


    $('#formAddItem')[0].reset();
}

function saveNewItem() {
    var formData = $('#formAddItem').serialize();

    $.ajax({
        url: HOST_URL + 'master/item/saveDataItem',
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (response) {
            if (response.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.messages,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    $('#formAddItem')[0].reset();
                    $('#idgroup').val(null).trigger('change');
                    $('#subunitenable').val(null).trigger('change');
                    $('#unit').val(null).trigger('change');
                    $('#subunit').val(null).trigger('change');
                    $('#deflocation').val(null).trigger('change');
                    $('#defarea').val(null).trigger('change');
                    $('#setminstock').val(null).trigger('change');
                    $('#chold').val(null).trigger('change');
                    $('#modalAddItem').modal('hide');
                    // refresh data list kalau perlu
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.messages
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menghubungi server.'
            });
        }
    });
}



// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH GROUP ++++++++++++++++++++++++++++++++++++++++//

var defaultInitialGroup = 'BRG';
$("#idgroup").select2({
    placeholder: "Type/Chose Category Item",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_mgroup',
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
                _paramglobal_: defaultInitialGroup,
                _parameterx_: defaultInitialGroup,
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
}).on("select2:selecting", function () {
    //$("#newsubdept option[value]").remove();
    //var newOptions = []; // the result of your JSON request
    //$("#id_subdept").val(null).trigger('change');
    //console.log($("#newdept").val());
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



var defaultInitialUnit = 'UNIT';
$("#unit").select2({
    placeholder: "Type/Chose Your Unit",
    allowClear: true,
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

// trigger ketika rolejob berubah
$('#rolejob').on('change', generateDocNo);

// trigger juga ketika jnsinvoice berubah
// $('#jnsinvoice').on('change', generateDocNo);

// -------------------------- end of selectize ---------------------------------


function generateDocNo() {
        if (skipRoleChange) return; // skip kalau lagi inisialisasi

        let rolejob = $('#rolejob').val();
        // let jnsinvoice = $('#jnsinvoice').val();
        if (rolejob) {
            $.ajax({
                url: HOST_URL + '/sales/postsales/getRolePOSOI',
                method: 'GET',
                data: {
                    rolejob: rolejob,
                    codemenu: 'I.S.A.2'
                },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        // let prefixParts = res.prefix.split('/'); // ["JTS", "PH", "25", "08"]

                        $('#prefix').val(res.prefix);       // PREFIX
                        $('#infix').val(res.infix);       // INFIX
                        $('#sufix').val(res.suffix);           // SUFIX

                      // parse infix YYMM -> year, month
                        var infix = (res.infix || '').toString();
                        if (infix.length === 4) {
                            $('#docdate').prop('disabled', false);
                            var yy = infix.substring(0,2);
                            var mm = infix.substring(2,4);
                            var year = 2000 + parseInt(yy,10);
                            var month = parseInt(mm,10) - 1; // moment month index

                            var startDate = moment([year, month, 1]);
                            var endDate = moment(startDate).endOf('month');

                            var $el = $('#docdate');
                            var drp = $el.data('daterangepicker');

                            if (drp) {
                                // update limits & selected date
                                drp.minDate = startDate;
                                drp.maxDate = endDate;
                                drp.setStartDate(startDate);
                                drp.setEndDate(startDate);
                            } else {
                                // fallback: (re)initialize with limits
                                $el.daterangepicker({
                                    autoUpdateInput: false,
                                    singleDatePicker: true,
                                    showDropdowns: true,
                                    startDate: startDate,
                                    minDate: startDate,
                                    maxDate: endDate,
                                    locale: { format: 'YYYY-MM-DD' },
                                    cancelLabel: 'Clear'
                                });
                                // rebind handlers jika perlu (apply/cancel)
                                $el.on('apply.daterangepicker', function(ev, picker) {
                                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                                });
                                $el.on('cancel.daterangepicker', function(ev, picker) {
                                    $(this).val('');
                                });
                            }

                            // isi input langsung (opsional)
                            $el.val(startDate.format('YYYY-MM-DD'));
                        }
                        // Jika tetap ingin punya 1 full docno juga:
                        $('#docno').val(res.prefix + '/'+ res.infix +'/' + res.suffix);
                        console.info($('#docno').val())
                    } else {
                        Swal.fire('Not Found', res.message, 'warning');
                    }
                },

                error: function () {
                    Swal.fire('Error', 'Server error', 'error');
                }
            });
        }
    };

function tableSOIDtl(){
    //"url": HOST_URL + 'stock/bbm/list_tmp_bbm_dtl',
    var docno = $('#docno').val(); // ambil dari hidden input
    //var table = $('#tabbmdtl');
    var table = $('#t_soi_dtl').DataTable({
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "language":  languageDatatable(),
        "paging": false,
        "lengthChange": true,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": false,
        "bFilter":true,
        "iDisplayLength": -1,
        "ajax": {
            "url": HOST_URL + 'sales/postsales/list_t_soi_dtl',
            "type": "POST",
            "data": function(data) {
                data.docno = $('#docno').val(); // tambahkan parameter docno
                //data.searchfilter = $('#searchitem').val()+'';
                //data.idbarang = $('#idbarang').val()+'';
                //data.idposition = $('#idposition').val()+'';
            },
            "dataFilter": function(data) {
                var json = jQuery.parseJSON(data);
                json.draw = json.dataTables.draw;
                json.recordsTotal = json.dataTables.recordsTotal;
                json.recordsFiltered = json.dataTables.recordsFiltered;
                json.data = json.dataTables.data;
                // const dataDetail = json.data;
                return JSON.stringify(json); // return JSON string
            }
        },
        //Set column definition initialisation properties.
        "columnDefs": [
            {
                "targets": [ -1 ], //last column
                "render": function (data, type, full, meta) {
                    return "" + data + "</div>";
                },
                "orderable": false, //set not orderable
            },
            {
                'targets': 0,
                'checkboxes': {
                    'selectRow': true
                }
            },
        ],
        "select": {
            'style': 'multi'
        },
        "order": [[1, 'asc']],
        "initComplete": function () {
            checkTableData(); // Panggil fungsi setelah DataTable selesai diinisialisasi
        }
    });

    // Event listener untuk checkbox di header
    $('#t_soi_dtl thead input[type="checkbox"]').on('click', function () {
        var isChecked = this.checked; // Status checkbox header
        
        // Pilih semua checkbox di body dan ubah statusnya sesuai checkbox header
        $('#t_soi_dtl tbody input[type="checkbox"]').each(function () {
            this.checked = isChecked;
        });
    });

    

    $('#delete_item').on('click', function(e){
        // ajax adding data to database
        e.preventDefault();
        const $button = $(this);
        if(isEditing) {
            reloadtableSOIDtl();  // Reload table to restore initial data
            $button.html('<i class="fa fa-trash"></i> Delete').removeClass('btn-secondary').addClass('btn-danger');
            
            // // Disable and style specific inputs (ttlch, ttlseq, ttlton)
            // $("#ttlch, #ttlseq, #ttlton").prop("disabled", true).each(function () {
            //     $(this).css("background-color", "#d6d5d5"); // Grey-out the background to show it's disabled
            // });

            // $("#groupFilter").prop("disabled", false).css("background-color", "#ffffff");

            // $("#periodeFilter").prop("disabled", false).css("background-color", "#ffffff");

            $("#update_item").html('<i class="fa fa-gear"></i> Update');  // Kembali ke tombol Update
            $(".btn-final-entry").show();
            $(".btn-insert-new").show();

            isEditing = false;

        } else {
            var form = $('#frm-example');
            var formdata = false;
            if (window.FormData){
                formdata = new FormData(form[0]);
            } var docno = $('[name="docno"]').val();

            var rows_selected = table.column(0).checkboxes.selected().toArray(); // Konversi ke array
            var length_selected = rows_selected.length;
            //console.log(" TEST " + table.column(0).checkboxes.selected().length);

            if (length_selected > 0) {
                // Iterate over all selected checkboxes
                $.each(rows_selected, function(index, rowId){
                    // console
                    // Create a hidden element
                    $(form).append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'id[]')
                            .val(rowId)
                    );
                    /* DELETE PP SETELAH PEMILIHAN & CLICK*/
                    Swal.fire({
                        title: 'Warning..!!!',
                        text: 'Some Item details will be removed, are you sure? ' + rows_selected,
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
                            /* AJAX EDIT HERE */
                            // console.log('BEKANTAL');
                            var formdata = new FormData();
                            formdata.append('docno', $('[name="docno"]').val()); // Tambahkan docno
                            rows_selected.forEach(function(rowId) {
                                formdata.append('id[]', rowId); // Tambahkan ID dari database
                            });
                            $.ajax({
                                url: HOST_URL + 'sales/postsales/deleteSOIDtl',
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
                                            title: 'Success...!!!',
                                            text: json.messages,
                                            backdrop: true,
                                            allowOutsideClick: false,
                                            showConfirmButton: true,
                                            showDenyButton: false,
                                            showCancelButton: false,
                                            confirmButtonText: `Ok`,
                                            icon: 'success',
                                            //denyButtonText: `Don't save`,
                                        });
                                        // Reload table dulu, setelah selesai baru checkTableData()
                                        $('#t_soi_dtl').DataTable().ajax.reload(function () {
                                            checkTableData(); // Panggil setelah reload selesai
                                        });

                                    }
                                }, error: function (jqXHR, textStatus, errorThrown) {
                                    // alert('Gagal Menyimpan / Ubah data / data sudah ada');
                                    swal({
                                        title: "Galat!!",
                                        text: json.messages,
                                        type: "error"
                                    });
                                    reloadtableSOIDtl();
                                    checkTableData()

                                }
                            });

                        }

                    })
                });
                // Edit
            } else {
                Swal.fire({
                    title: 'Ooops..!!!',
                    text: 'Minimal 1 Pilihan' + e,
                    backdrop: true,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: `Ok`,
                    icon: 'question',
                    //denyButtonText: `Don't save`,
                });
            }
            // Prevent actual form submission
            e.preventDefault();
        }
    });

}
function reloadtableSOIDtl()
{
    var table = $('#t_soi_dtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}

function checkTableData() {
    var table = $('#t_soi_dtl').DataTable();
    var rowCount = table.rows().count(); // Ambil jumlah baris

    if (rowCount === 0) {
        $(".btn-final-entry").hide(); // Sembunyikan tombol Final Entry
    } else {
        $(".btn-final-entry").show(); // Tampilkan tombol Final Entry
    }

}


$('#t_soi_dtl').on('draw.dt', function () {
    $('.unit-dropdown').select2({
        // theme: 'classic', // Gunakan tema klasik agar lebih mirip select default
        // minimumResultsForSearch: -1,
         // Hilangkan kotak pencarian
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_unit',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term // Kirim teks pencarian ke server
                };
            },
            processResults: function (data) {
                return {
                    results: data.items.map(function(item) {
                        return {
                            id: item.idunit,
                            text: item.idunit
                        };
                    })
                };
            }
        },
        // minimumInputLength: 1 // Search aktif setelah 1 karakter
    });
});


$('#t_soi_dtl').on('draw.dt', function () {
    $('.idbarang-dropdown').select2({
        // theme: 'classic', // Gunakan tema klasik agar lebih mirip select default
        // minimumResultsForSearch: -1,
         // Hilangkan kotak pencarian
        ajax: {
            url: HOST_URL + 'api/globalmodule/list_itemsales',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _search_: params.term // Kirim teks pencarian ke server
                };
            },
            processResults: function (data) {
                return {
                    results: data.items.map(function(item) {
                        return {
                            id: item.idbarang,
                            text: item.nmbarang,
                            idbarang: item.idbarang, // simpan idbarang untuk nanti di render
                            nmbarang: item.nmbarang
                        };
                    })
                };
            },
            templateResult: function (data) {
                if (!data.id) return data.text; // placeholder

                return $(
                    `<span>
                        <strong>${data.idbarang}</strong> 
                        <i class="fa fa-circle text-success" style="font-size:8px;margin:0 4px;"></i> 
                        ${data.nmbarang}
                    </span>`
                );
            },
            templateSelection: function (data) {
                if (!data.id) return data.text;
                return `${data.idbarang} ${data.nmbarang}`;
            }
        },
        // minimumInputLength: 1 // Search aktif setelah 1 karakter
    });
});

let isEditing = false;
 // Handle Update Button Click
$("#update_item").click(function (e) {
    e.preventDefault();
    const $button = $(this);

    if (!isEditing) {

        // $('[name="ttlch"]').prop('readonly', false);
        // $('[name="ttlseq"]').prop('readonly', false);
        // $('[name="ttlton"]').prop('readonly', false);
        // $(".option-dropdown").prop("disabled", false);

        
        // Enable all unit dropdowns
        $(".unit-dropdown").prop("disabled", false).each(function () {
            $(this).next(".select2-container").css("background-color", "#ffffff"); // Enable dropdown visually
        });

        $(".idbarang-dropdown").prop("disabled", false).each(function () {
            $(this).next(".select2-container").css("background-color", "#ffffff"); // Enable dropdown visually
        });

        // Enable kolom type=number khusus di t_soi_dtl
        $("input[id^='qty_'], input[id^='usdmt_'], input[id^='exchange_'], input[id^='price_'], input[id^='amount_'], input[id^='totaldelivery_'], input[id^='balanceorder_']")
            .prop("disabled", false)
            .css("background-color", "#ffffff");

        // Enable kolom type=text khusus di t_soi_dtl
        $("input[id^='description_'], input[id^='grade_'], input[id^='size_'], input[id^='cutlength_'], input[id^='specno_'], input[id^='ordernumbermsr_'], input[id^='etd_']")
            .prop("disabled", false)
            .css("background-color", "#ffffff");
        // $('[name="docno"]').prop('disabled', true);
        // $('[name="penerima"]').prop('disabled', true);
        // $('[name="dateout"]').prop('disabled', true);
        // $('[name="datereturn"]').prop('disabled', true);
        // $('[name="nopol"]').prop('disabled', true);
        // $('[name="baranglain"]').prop('disabled', true);


        $(".btn-final-entry").hide();
        $(".btn-insert-new").hide();
        $button.html('<i class="fa fa-save"></i> Save Update');
        isEditing = true;

        $("#delete_item").html('<i class="fa fa-times"></i> Cancel Update').removeClass('btn-danger').addClass('btn-secondary');

    } else {
        // Collect updated data
        let updatedData = [];
        $("#t_soi_dtl tbody tr").each(function () {
            const idurut = $(this).find(".unit-dropdown").data("id");

            // ambil idbarang & nmbarang dari select2
            const idbarang = $(this).find(".idbarang-dropdown").val();
            const nmbarang = $(this).find(".idbarang-dropdown option:selected").text().trim();

            // ambil unit (id & text)
            const idunit = $(this).find(".unit-dropdown").val();
            const nmunit = $(this).find(".unit-dropdown option:selected").text().trim();

            // input number/text
            const qty = convertToDbNumber($("#qty_" + idurut).val()); 
            const usdmt = convertToDbNumber($("#usdmt_" + idurut).val()); 
            const exchange = convertToDbNumber($("#exchange_" + idurut).val()); 
            const price = convertToDbNumber($("#price_" + idurut).val()); 
            const amount = convertToDbNumber($("#amount_" + idurut).val()); 
            const totaldelivery = convertToDbNumber($("#totaldelivery_" + idurut).val()); 
            const balanceorder = convertToDbNumber($("#balanceorder_" + idurut).val()); 
            
            // const exchange = convertToDbNumber($("#exchange_" + idurut).val()); 
            const grade = $("#grade_" + idurut).val();
            const size = $("#size_" + idurut).val();
            const cutlength = $("#cutlength_" + idurut).val();
            const specno = $("#specno_" + idurut).val();
            const ordernumbermsr = $("#ordernumbermsr_" + idurut).val();
            const etd = $("#etd_" + idurut).val();

            const description = $("#description_" + idurut).val();


            updatedData.push({
                idurut,
                idbarang,
                nmbarang,
                idunit,
                nmunit,
                qty,
                grade,
                size,
                cutlength,
                usdmt,
                price,
                amount,
                exchange,
                etd,
                ordernumbermsr,
                specno,
                totaldelivery,
                balanceorder,

                // exchange,
                description
            });
        });

        const masterData = {
            docno: $('[name="docno"]').val()
        };


        // Send data to server via AJAX
        $.ajax({
            url: "update_detail_soi", // Ganti dengan endpoint update di backend
            type: "POST",
            data: { 
                updates: updatedData,
                masterData: masterData
            },
            success: function (response) {
                Swal.fire({
                    title: 'Success...!!!',
                    text: 'Successfully update data',
                    backdrop: true,
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: `Ok`,
                    icon: 'success',
                    //denyButtonText: `Don't save`,
                });
                reloadtableSOIDtl();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to update data. Please try again.'
                });
            }
        });
        calculateSummary()
        // Disable all dropdowns
        $(".unit-dropdown").prop("disabled", true).each(function () {
            $(this).next(".select2-container").css("background-color", "#d6d5d5"); // Disable dropdown visually
        });


       // Disable semua input yang id-nya diawali qty_, price_, exchange_, usdmt_, atau description_
        $("input[id^='qty_'], input[id^='price_'], input[id^='amount_'], input[id^='exchange_'], input[id^='usdmt_'], input[id^='description_'], input[id^='totaldelivery_'], input[id^='balanceorder_'], input[id^='grade_'], input[id^='size_'], input[id^='cutlength_'], input[id^='specno_'], input[id^='ordernumbermsr_'], input[id^='etd_']")
            .prop("disabled", true)
            .css("background-color", "#d6d5d5");
        // $("input[type='text']").prop("disabled", true).each(function () {
        //     $(this).css("background-color", "#d6d5d5"); // Disable input visually
        // });

        // $("input[type='number']").prop("disabled", true).each(function () {
        //     $(this).css("background-color", "#d6d5d5"); // Disable input visually
        // });

        $button.html('<i class="fa fa-gear"></i> Update');
        $(".btn-final-entry").show();
        $(".btn-insert-new").show();
        isEditing = false;
        $("#delete_item").html('<i class="fa fa-trash"></i> Delete').removeClass('btn-secondary').addClass('btn-danger');

        reloadtableSOIDtl()
    }
});



//MASTER CUSTOMER

// $("#cust").select2({
//     placeholder: "Cari Customer",
//     allowClear: true,
//     maximumSelectionLength: 1,
//     width: '100%',
//     multiple: false,
//     ajax: {
//         url: HOST_URL + 'api/globalmodule/list_customer_new',
//         type: 'POST',
//         dataType: 'json',
//         delay: 250,
//         data: function(params) {
//             return {
//                 _search_: params.term,
//                 _page_: params.page,
//                 _draw_: true,
//                 _start_: 1,
//                 _perpage_: 2,
//                 _paramglobal_: '',
//                 _parameterx_: '',
//                 term: params.term,
//             };
//         },
//         processResults: function(data, params) {
//             params.page = params.page || 1;

//             // ubah struktur hasil agar id = nama_customer
//             const results = data.items.map(function(item) {
//                 return {
//                     id: item.nama_customer,        // <== yang ditampilkan di val()
//                     text: item.nama_customer,      // <== yang ditampilkan di dropdown
//                     docno: item.docno,             // tetap disimpan untuk referensi
//                     nama_customer: item.nama_customer,
//                     alamat_kantor: item.alamat_kantor,
//                     telepon: item.telepon,
//                     fax: item.fax,
//                     kontak_pic: item.kontak_pic
//                 };
//             });

//             return {
//                 results: results,
//                 pagination: {
//                     more: (params.page * 30) < data.total_count
//                 }
//             };
//         },
//         cache: true
//     },
//     escapeMarkup: function(markup) { return markup; },
//     templateResult: formatCust,
//     templateSelection: formatCustSelection
// }).on('select2:select', function (e) {
//     var data = e.params.data;
//     console.info($('#cust').val()); // sekarang akan menampilkan nama_customer
// });

/* Format tampilan hasil */
function formatCust(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__description'>" + 
                repo.docno + " <i class='fa fa-circle-o'></i> " + repo.nama_customer + 
                "</div>";
    return markup;
}

function formatCustSelection(repo) {
    return repo.nama_customer || repo.text;
}

$('#po').on('change', function() {
    const po = $(this).val();
    const nama ='' // kalau kamu punya input hidden berisi session nama

    if (isInitializingPO) return;

    // konfirmasi (opsional)
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Ganti Sales Order/PO akan menghapus semua item detail saat ini. Lanjutkan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // 1️⃣ kosongkan tabel di frontend
            // const table = $('#t_soi_dtl').DataTable();
            // table.clear().draw();

            // 2️⃣ panggil backend untuk hapus data di sc_tmp.soidtl
            $.ajax({
                url: HOST_URL + 'sales/postsales/clearTmpSOIDetail',
                type: 'POST',
                dataType: 'json',
                data: { inputby: nama },
                success: function(res) {
                    if (res.status === 'success') {
                        console.log('Data temporary SOI detail dihapus.');
                        documentReadable()
                        reloadtableSOIDtl()
                    } else {
                        Swal.fire('Gagal', res.message, 'warning');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal menghapus data temporary di server.', 'error');
                }
            });
        } else {
            // kalau user batal, reset select2 ke nilai sebelumnya
            $(this).val($(this).data('oldValue')).trigger('change.select2');
        }
    });

}).on('select2:selecting', function(e) {
    // simpan nilai sebelumnya sebelum diubah
    $(this).data('oldValue', $(this).val());
});



$("#po").select2({
    placeholder: "Cari Sales Order/PO",
    allowClear: true,
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    width: '100%', // Pastikan Select2 mengikuti lebar parent-nya
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_sales_order',
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
                _parameterx_: '',
                term: params.term,
            };
        },
        processResults: function(data, params) {
            var searchTerm = $("#po").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].docno, data.items[0].docno, true, true);
                $('#po').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#po').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            }
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
    templateResult: formatPO, // omitted for brevity, see the source of this page
    templateSelection: formatPOSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
    var data = e.params.data;

    if (data.cust) $('#cust').val(data.cust);
    // $.ajax({
    //     type: 'GET',
    //     url: HOST_URL + 'api/globalmodule/list_customer_new?var=' + data.cust.trim(),
    //     dataType: 'json',
    // }).then(function (datax) {
    //     var option = new Option(datax.items[0].nama_customer, datax.items[0].docno, true, true);
    //     $('[name="cust"]').append(option).trigger('change').trigger({
    //         type: 'select2:select',
    //         params: { data: datax }
    //     });
    // });
    if (data.alamat_kantor) $('#address').val(data.address);
    if (data.telepon) $('#phone').val(data.phone);
    if (data.fax) $('#fax').val(data.fax);
    if (data.kontak_pic) $('#pic').val(data.pic);
});

/* Format Group */
function formatPO(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>  " + repo.docno +"</div>";
    return markup;
}

function formatPOSelection(repo) {
    return repo.docno || repo.text;
}


function loadItemSO() {
    let po = $('#po').val()
    let cust = $('#cust').val()
    $.ajax({
        url: HOST_URL + 'sales/postsales/importSOIDetailFromPO',
        type: 'POST',
        dataType: 'json',
        data: { po: po, cust: cust },
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire('Berhasil', res.message, 'success');
                reloadtableSOIDtl();
                isInitializingPO = false; // aktifkan lagi event normal 
            } else {
                Swal.fire('Gagal', res.message, 'warning');
            }
        },
        error: function() {
            Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
        }
    });
}


//MASTER CURRENCY

$("#currency").select2({
    placeholder: "Cari Currency",
    allowClear: true,
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    width: '100%', // Pastikan Select2 mengikuti lebar parent-nya
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_currency',
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
                _parameterx_: '',
                term: params.term,
            };
        },
        processResults: function(data, params) {
            var searchTerm = $("#currency").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].currname, data.items[0].id, true, true);
                $('#currency').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#currency').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            }
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
    templateResult: formatCurrency, // omitted for brevity, see the source of this page
    templateSelection: formatCurrencySelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
    var data = e.params.data;

    var currcode = data.currcode;
    var docdate = $('#docdate').val()


    if (currcode && docdate) {
        $.ajax({
            url: HOST_URL + 'sales/postsales/getRate',
            type: 'GET',
            dataType: 'json',
            data: {
                currcode: currcode,
                docdate: docdate
            },
            success: function (res) {
                if (res.nilai) {
                    $('#exchangerate').val(dbToIndoFormat(res.nilai));
                } else {
                    alert(res.message);
                    $('#exchangerate').val('');
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
                alert('Gagal mengambil data kurs. Coba lagi.');
            }
        });
    } else {
        // $('#exchangerate').val('');
    }

    // if (data.alamat_kantor) $('#address').val(data.alamat_kantor);
    // if (data.telepon) $('#phone').val(data.telepon);
    // if (data.fax) $('#fax').val(data.fax);
    // if (data.kontak_pic) $('#up').val(data.kontak_pic);
});

/* Format Group */
function formatCurrency(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>  " + repo.id +"  <i class='fa fa-circle-o'></i>  "+ repo.currname +"</div>";
    return markup;
}

function formatCurrencySelection(repo) {
    return repo.currname || repo.text;
}



$("#bank").select2({
    placeholder: "Cari Bank",
    allowClear: true,
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    width: '100%', // Pastikan Select2 mengikuti lebar parent-nya
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_bank_sales',
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
                _parameterx_: '',
                term: params.term,
            };
        },
        processResults: function(data, params) {
            var searchTerm = $("#bank").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nmbank, data.items[0].id, true, true);
                $('#bank').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#bank').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            }
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
    templateResult: formatDept, // omitted for brevity, see the source of this page
    templateSelection: formatDeptSelection // omitted for brevity, see the source of this page
}).on('select2:select', function (e) {
    var data = e.params.data;

    if (data.alamat) $('#alamatbank').val(data.alamat);
    // if (data.kodepos) $('#kodeposbank').val(data.kodepos);
    if (data.accname) $('#accname').val(data.accname);
    if (data.accno) $('#accno').val(data.accno);
    if (data.nmbank) $('#nmbank').val(data.nmbank);
    if (data.swiftcode) $('#swiftcode').val(data.swiftcode);    
});

/* Format Group */
function formatDept(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.nmbank +"</div>";
    return markup;
}

function formatDeptSelection(repo) {
    return repo.nmbank || repo.text;
}

/* LOADING UNIT FOR INPUT SQUENCE  */
var defaultInitialUnit = "";
$("#idunitinsert").select2({
    width: '100%',
    placeholder: "Select/type Unit",
    allowClear: true,
    //minimumInputLength: 0, // only start searching when the user has input 3 or more characters
    //maximumSelectionLength: 1,
    multiple: false,
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
                term: params.term,
            };
        },
        processResults: function(data, params) {

            var searchTerm = $("#idunitinsert").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].docno, data.items[0].docno, true, true);
                $('#idunitinsert').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#idunitinsert').trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            }
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
    templateResult: formatUnit, // omitted for brevity, see the source of this page
    templateSelection: formatUnitSelection // omitted for brevity, see the source of this page
}).on("change", function () {
});


/* Format Group */
function formatUnit(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.idunit +"</div>";
    return markup;
}

function formatUnitSelection(repo) {
    return repo.idunit|| repo.text;
}


function finalEntrySOI() {
    // Ambil & convert field dari tfoot
    // let grossSales   = convertToDbNumber($("#grosssales").val());
    // let downPayment  = convertToDbNumber($("#downpayment").val());
    // let netSales     = convertToDbNumber($("#netsales").val());
    // let taxBasis     = convertToDbNumber($("#taxbasis").val());
    // let vat          = convertToDbNumber($("#vat").val());
    // let pph22        = convertToDbNumber($("#pph22").val());
    // let totalPrice   = convertToDbNumber($("#ttlprice").val());
    let po         = $("#po").val();
    let pocust         = $("#pocust").val();
    let revno         = $("#revno").val();
    let desc         = $("#desc").val();
    let cust         = $("#cust").val();
    


    // Bisa tambahkan field lain sesuai kebutuhan
    let data = {
        desc: desc,
        pocust: pocust,
        cust: cust,
        po: po,
        revno: revno,
        // bank: bank,
        // nmbank: nmbank,
        // alamatbank: alamatbank,
        // accno: accno,
        // accname:accname,
        // swiftcode:swiftcode,
    };

    // Konfirmasi sebelum submit
    Swal.fire({
        title: 'Yakin?',
        text: "Finish Entry SOI?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: HOST_URL + 'sales/postsales/finalEntrySOI',
                type: "POST",
                data: data,
                dataType: "json",
                success: function(res) {
                    if (res.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Final entry berhasil disimpan!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Kalau backend redirect, bisa langsung window.location.reload()
                            window.location.href = res.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Final entry gagal disimpan.'
                        }).then(() => {
                            window.location.href = res.redirect;
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan sistem: ' + xhr.status
                    }).then(() => {
                            window.location.href = res.redirect;
                        });
                }
            });
        }
    });

    return false; // cegah submit form default
}


function insertNewSOI() {
    var fillData = {
        'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
        'body': {
            docno : $('[name="docno"]').val()
            // namabarang: $('#namabaranginsert').val(),
            // qty: $('#qtyinsert').val(),
            // description: $('#descriptiondetail').val(),
        }
    };

    $.ajax({
        type: "POST",
        url: HOST_URL + 'sales/postsales/insertNewSOI',
        dataType: 'json',
        contentType: "application/json",
        data: JSON.stringify(fillData),
        success: function (datax) {
            Swal.fire({
                icon: datax.status ? 'success' : 'error',
                title: datax.status ? 'Success!' : 'Failed...',
                text: datax.message,
                allowOutsideClick: false
            });
            if (datax.status) {
                reloadtableSOIDtl();
                $('#insertsikbsp').modal('hide');
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Unable to process request.',
                allowOutsideClick: false
            });
        }
    });
}


function insert_detail_sikbsp(){

    if(  $('#idunitinsert').val() == null){
        Swal.fire({
            icon: 'error',
            title: 'Failed...',
            text: 'Please fill the unit first',
            backdrop: true,
            allowOutsideClick: false,
        })
        return
    }

    if( $('#namabaranginsert').val() == ''){
        Swal.fire({
            icon: 'error',
            title: 'Failed...',
            text: 'Please fill the Item Name first',
            backdrop: true,
            allowOutsideClick: false,
        })
        return
    }

    if( $('#qtyinsert').val() == ''){
        Swal.fire({
            icon: 'error',
            title: 'Failed...',
            text: 'Please fill the Qty first',
            backdrop: true,
            allowOutsideClick: false,
        })
        return
    }

    if( $('#descriptiondetail').val() == ''){
        Swal.fire({
            icon: 'error',
            title: 'Failed...',
            text: 'Please fill the description first',
            backdrop: true,
            allowOutsideClick: false,
        })
        return
    }


    var fillData = {
        'success': true,
        'key': '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO',
        'message': '',
        'body': {
            idunit: $('#idunitinsert').val(),
            namabarang: $('#namabaranginsert').val(),
            qty: $('#qtyinsert').val(),
            description: $('#descriptiondetail').val(),
        },
    };

    $.ajax({
        type: "POST",
        url: HOST_URL + 'finance/sikbsp/insert_detail_sikbsp',
        dataType: 'json',
        contentType: "application/json",
        data: JSON.stringify(fillData),
        success: function (datax) {
            if (datax.status) {
                reloadtableSOIDtl();
                $('#qtyinsert').val('');
                $('#idunitinsert').val('');
                $('#namabaranginsert').val('');
                $('#descriptiondetail').val('');

                Swal.fire({
                    title: 'Success...!!!',
                    text: datax.message,
                    icon: 'success',
                    backdrop: true,
                    allowOutsideClick: false,
                    confirmButtonText: `Ok`,
                }).then(() => {
                    $('#insertsikbsp').modal('hide'); // Tutup modal
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed...',
                    text: datax.messages,
                    backdrop: true,
                    allowOutsideClick: false,
                })
                reloadtableSOIDtl();
                $('#insertsikbsp').modal('hide');
                btn.prop('disabled', false);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Unable To Response Data',
                backdrop: true,
                allowOutsideClick: false,
            })
            reloadtableSOIDtl();
            $('#insertsikbsp').modal('hide');
            btn.prop('disabled', false);
        }
    });
}

$(document).on('input', '.form-control', function () {
    let id = $(this).attr('id').split('_')[1]; // ambil idurut
    if(id){
        let qty = parseFloat($('#qty_' + id).val().replace(/\./g, '').replace(',', '.')) || 0;
        let price = parseFloat($('#price_' + id).val().replace(/\./g, '').replace(',', '.')) || 0;
        let amount = qty * price;    
        $('#amount_' + id).val(amount.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }
});

function calculateSummary() {
    let gross = 0;

    // Loop semua baris amount di tabel detail
    $("#t_soi_dtl tbody tr").each(function() {
        let amountInput = $(this).find("input[id^='amount_']");
        let val = amountInput.val() || "0";
        gross += parseFloat(convertToDbNumber(val)) || 0;
    });

    // Ambil DP
    let dp = parseFloat(convertToDbNumber($("#downpayment").val())) || 0;

    // Hitung Net Sales
    let net = gross - dp;

    // Tax Basis = Net Sales (asumsi awal, bisa disesuaikan)
    let taxbasis = 11/12*net;

    // V.A.T = 12% tax basis
    let vat = taxbasis * 0.12;

    // PPh 22 = 0.3% net sales
    let pph22 = net * 0.003;

    // Total = Net Sales + VAT + PPh22
    let ttl = net + vat + pph22;

    // Update ke field
    $("#grosssales").val(dbToIndoFormat(gross));
    $("#netsales").val(dbToIndoFormat(net));
    $("#taxbasis").val(dbToIndoFormat(taxbasis));
    $("#vat").val(dbToIndoFormat(vat));
    $("#pph22").val(dbToIndoFormat(pph22));
    $("#ttlprice").val(dbToIndoFormat(ttl));
}

// // Pakai _jtsseparator tapi dibuat versi helper
// function formatUI(val) {
//     let obj = { value: (val || 0).toFixed(2).replace(".", ",") }; 
//     _jtsseparator(obj);
//     return obj.value;
// }

// trigger saat load tabel atau ubah DP
$(document).on("input", "#downpayment", function() {
    calculateSummary();
});

// juga panggil setelah DataTable selesai redraw
$("#t_soi_dtl").on("draw.dt", function() {
    calculateSummary();
});

function delay(callback, ms) {
    var timer = 0;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}


$(document).ready(function() {

    var infix = $('#infix').val(); // misal 2508
    if (!infix) {
        // Kalau kosong/null, disable & clear input
        $('#docdate').val('').prop('disabled', true);
    } else {
        // Kalau ada nilai, enable kembali
        $('#docdate').prop('disabled', false);
        // lanjut proses parsing infix → set datepicker min/max
    }
    documentReadable();
    // checkTableData();
    // calculateSummary();

    
    tableSOIDtl();

});
