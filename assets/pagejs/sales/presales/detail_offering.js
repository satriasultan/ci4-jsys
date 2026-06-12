
$(".fjurnal").on("change", function () {
    //$("#njurnal").val('00011');
    //Ajax Load data from ajax
    njurnal();
});

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
        url: HOST_URL + 'sales/presales/showing_offeringtrx',
        data: { docno: docno },
        dataType: 'json',
        success: function(json) {
            json.status = json.dataTables.status;
            json.total_count = json.dataTables.total_count;
            json.items = json.dataTables.items;
            json.incomplete_results = json.dataTables.incomplete_results;
            if(json.dataTables.items.length != 0){
                // if (json.dataTables.items[0].status.trim() === 'E') {
                    console.log('--'+ json.dataTables.items[0].status );
                    $('[name="docno"]').val(json.dataTables.items[0].docno).prop('readonly', true);
                    var docnoData = json.dataTables.items[0].docno.trim();
                    let prefixParts = docnoData.split('/'); // ["JTS", "PH", "25", "08"]
                    $('[name="prefix"]').val(prefixParts[0]).prop('readonly', true);
                    $('[name="infix"]').val(prefixParts[1]).prop('readonly', true);
                    $('[name="sufix"]').val(prefixParts[2]).prop('readonly', true);
    
    
    
                    
                    // var docdateValue = json.dataTables.items[0].docdate;
                    // if (docdateValue) {
                    //     $('[name="docdate"]').val(docdateValue).prop('readonly', true);
                    // } else {
                    //     $('[name="docdate"]').val("").prop('readonly', false);
                    // }
    
                    // Ambil data customer
                    $.ajax({
                        type: 'GET',
                        url: HOST_URL + 'api/globalmodule/list_customer_new?var=' + json.dataTables.items[0].cust,
                        dataType: 'json',
                    }).then(function (datax) {
                        var option = new Option(datax.items[0].nama_customer, datax.items[0].docno, true, true);
                        $('[name="cust"]').append(option).trigger('change').trigger({
                            type: 'select2:select',
                            params: { data: datax }
                        });
                    });
    
                    // Set field lain
                    skipRoleChange = true;
                    $('[name="rolejob"]').val(json.dataTables.items[0].rolejob.trim()).trigger('change').prop('disabled',true);
                    $('[name="docdate"]').val(json.dataTables.items[0].docdate.trim()).prop('readonly', true);
                    $('[name="phone"]').val(json.dataTables.items[0].phone).prop('readonly', true);
                    $('[name="fax"]').val(json.dataTables.items[0].fax).prop('readonly', true);
                    $('[name="up"]').val(json.dataTables.items[0].up).prop('readonly', true);
                    $('[name="address"]').val(json.dataTables.items[0].address).prop('readonly', true);
                    $('[name="desc"]').val(json.dataTables.items[0].description).prop('readonly', true);
                    $('[name="cust"]').val(json.dataTables.items[0].cust).prop('disabled', true);

                    $('[name="brand"]').val(json.dataTables.items[0].brand.trim()).prop('readonly', true);
                    $('[name="size"]').val(json.dataTables.items[0].size.trim()).prop('readonly', true);
                    $('[name="qty"]').val(json.dataTables.items[0].qty.trim()).prop('readonly', true);
                    $('[name="pembayaran"]').val(json.dataTables.items[0].pembayaran.trim()).prop('readonly', true);
                    $('[name="pengiriman"]').val(json.dataTables.items[0].pengiriman.trim()).prop('readonly', true);
                    $('[name="expdateph"]').val(json.dataTables.items[0].expdate.trim()).prop('readonly', true);
                    $('[name="ketentuan"]').val(json.dataTables.items[0].ketentuan.trim()).prop('readonly', true);

                // }
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

$(document).on('keyup blur change', '.jtsseparator', function () {
    _jtsseparator(this);
});


// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH GROUP ++++++++++++++++++++++++++++++++++++++++//


// -------------------------- end of selectize ---------------------------------


$('#rolejob').on('change', function () {
        if (skipRoleChange) return; // skip kalau lagi inisialisasi

        let rolejob = $(this).val();

        if (rolejob) {
            $.ajax({
                url: HOST_URL + '/sales/presales/getRolePO',
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
                    } else {
                        Swal.fire('Not Found', res.message, 'warning');
                    }
                },

                error: function () {
                    Swal.fire('Error', 'Server error', 'error');
                }
            });
        }
    });

function tableOfferingDtl(){
    //"url": HOST_URL + 'stock/bbm/list_tmp_bbm_dtl',
    var docno = $('#docno').val(); // ambil dari hidden input
    //var table = $('#tabbmdtl');
    var table = $('#t_offering_dtl').DataTable({
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
            "url": HOST_URL + 'sales/presales/list_t_offering_dtltrx',
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
    $('#t_offering_dtl thead input[type="checkbox"]').on('click', function () {
        var isChecked = this.checked; // Status checkbox header
        
        // Pilih semua checkbox di body dan ubah statusnya sesuai checkbox header
        $('#t_offering_dtl tbody input[type="checkbox"]').each(function () {
            this.checked = isChecked;
        });
    });

    

    $('#delete_item').on('click', function(e){
        // ajax adding data to database
        e.preventDefault();
        const $button = $(this);
        if(isEditing) {
            reloadtableOfferingDtl();  // Reload table to restore initial data
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
                                url: HOST_URL + 'sales/presales/deleteOfferingDtl',
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
                                        $('#t_offering_dtl').DataTable().ajax.reload(function () {
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
                                    reloadtableOfferingDtl();
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
function reloadtableOfferingDtl()
{
    var table = $('#t_offering_dtl');
    table.DataTable().ajax.reload(); //reload datatable ajax
}

function checkTableData() {
    var table = $('#t_offering_dtl').DataTable();
    var rowCount = table.rows().count(); // Ambil jumlah baris

    if (rowCount === 0) {
        $(".btn-final-entry").hide(); // Sembunyikan tombol Final Entry
    } else {
        $(".btn-final-entry").show(); // Tampilkan tombol Final Entry
    }

}


$('#t_offering_dtl').on('draw.dt', function () {
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


$('#t_offering_dtl').on('draw.dt', function () {
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

      // Enable kolom type=number khusus di t_offering_dtl
        $("input[id^='qty_'], input[id^='price_'], input[id^='exchange_'], input[id^='usdmt_']")
            .prop("disabled", false)
            .css("background-color", "#ffffff");

        // Enable kolom type=text khusus di t_offering_dtl
        $("input[id^='description_']")
            .prop("disabled", false)
            .css("background-color", "#ffffff");

        $('[name="docno"]').prop('disabled', true);
        $('[name="penerima"]').prop('disabled', true);
        $('[name="dateout"]').prop('disabled', true);
        $('[name="datereturn"]').prop('disabled', true);
        $('[name="nopol"]').prop('disabled', true);
        $('[name="baranglain"]').prop('disabled', true);


        $(".btn-final-entry").hide();
        $(".btn-insert-new").hide();
        $button.html('<i class="fa fa-save"></i> Save Update');
        isEditing = true;

        $("#delete_item").html('<i class="fa fa-times"></i> Cancel Update').removeClass('btn-danger').addClass('btn-secondary');

    } else {
        // Collect updated data
        let updatedData = [];
        $("#t_offering_dtl tbody tr").each(function () {
            const idurut = $(this).find(".unit-dropdown").data("id");

            // ambil idbarang & nmbarang dari select2
            const idbarang = $(this).find(".idbarang-dropdown").val();
            const nmbarang = $(this).find(".idbarang-dropdown option:selected").text().trim();

            // ambil unit (id & text)
            const idunit = $(this).find(".unit-dropdown").val();
            const nmunit = $(this).find(".unit-dropdown option:selected").text().trim();

            // input number/text
            const qty = convertToDbNumber($("#qty_" + idurut).val()); 
            const price = convertToDbNumber($("#price_" + idurut).val()); 
            const usdmt = convertToDbNumber($("#usdmt_" + idurut).val()); 
            const exchange = convertToDbNumber($("#exchange_" + idurut).val()); 
            const description = $("#description_" + idurut).val();

            updatedData.push({
                idurut,
                idbarang,
                nmbarang,
                idunit,
                nmunit,
                qty,
                price,
                usdmt,
                exchange,
                description
            });
        });

        const masterData = {
            docno: $('[name="docno"]').val()
        };


        // Send data to server via AJAX
        $.ajax({
            url: "update_detail_offering", // Ganti dengan endpoint update di backend
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
                reloadtableOfferingDtl();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to update data. Please try again.'
                });
            }
        });
        // Disable all dropdowns
        $(".unit-dropdown").prop("disabled", true).each(function () {
            $(this).next(".select2-container").css("background-color", "#d6d5d5"); // Disable dropdown visually
        });


        // Disable semua input yang id-nya diawali qty_, price_, exchange_, usdmt_, atau description_
        $("input[id^='qty_'], input[id^='price_'], input[id^='exchange_'], input[id^='usdmt_'], input[id^='description_']")
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

        reloadtableOfferingDtl()
    }
});



//MASTER CUSTOMER

$("#cust").select2({
    placeholder: "Cari Customer",
    allowClear: true,
    //minimumInputLength: 2, // only start searching when the user has input 3 or more characters
    maximumSelectionLength: 1,
    width: '100%', // Pastikan Select2 mengikuti lebar parent-nya
    multiple: false,
    ajax: {
        url: HOST_URL + 'api/globalmodule/list_customer_new',
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
            var searchTerm = $("#cust").data("select2").$dropdown.find("input").val();
            if (data.items.length === 1 && data.items[0].text === searchTerm) {
                var option = new Option(data.items[0].nama_customer, data.items[0].docno, true, true);
                $('#cust').append(option).trigger('change').select2("close");
                // manually trigger the `select2:select` event
                $('#cust').trigger({
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

    if (data.alamat_kantor) $('#address').val(data.alamat_kantor);
    if (data.telepon) $('#phone').val(data.telepon);
    if (data.fax) $('#fax').val(data.fax);
    if (data.kontak_pic) $('#up').val(data.kontak_pic);
});

/* Format Group */
function formatDept(repo) {
    if (repo.loading) return repo.text;
    var markup ="<div class='select2-result-repository__description'>" + repo.docno +"   <i class='fa fa-circle-o'></i>   "+ repo.nama_customer +"</div>";
    return markup;
}

function formatDeptSelection(repo) {
    return repo.nama_customer || repo.text;
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
    
    tableOfferingDtl();

});
