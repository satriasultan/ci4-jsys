<style>
    
    .section-block {
        background-color: #e8e8e8;
        border-left: 4px solid #007bff;
        /* border-bottom: 4px solid #007bff;  */
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                    box-shadow 0.4s ease, 
                    border-color 0.4s ease;
        
        transform: scale(1);
        will-change: transform;
    }

    .section-header {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 20px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 8px;
    }

    .section-block:hover {
        /* transform: scale(1.02); */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-left-color: #0056b3;
        border-bottom-color: #0056b3;
    }

    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
    }

    .form-control:disabled, .form-control[readonly] {
        background-color: #dfdfdf;
        opacity: 1;
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(
            to right,
            #007bff 0%,
            #cfe2ff 30%,
            #e8e8e8 100%
        );
        margin: 24px 0;
        border: none;
    }

</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
                    <?php foreach ($y as $y1) { ?>
                        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                            <li class="breadcrumb-item"><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<?php echo $message;?>

<?php /*
<div class="row">
	<div class="col-sm-12">
		<a href="<?= base_url('master/item/input') ?>" class="btn btn-primary" style="margin:10px"><i class="fa fa-plus"></i>  Input Master Item  </a>
	</div>
</div> */ ?>
<div class="row">
	<div class="col-sm-12">
		<div class="card">
            <div class="card-header">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-bs-toggle="dropdown"><?php echo 'Menu'; ?>
                    </button>
                    <div class="dropdown-menu">
                        <!-- <a class="dropdown-item" href="<?= base_url('master/data/input_suppliers') ?>"><i class="fa fa-plus"></i><?php echo '   Input'; ?> </a> -->
                        <!-- <a class="dropdown-item disabled" data-bs-toggle="modal" data-bs-target="#filter"  href="#"><i class="fa fa-filter"></i><?php echo '   Filter'; ?></a> -->
                        <a class="dropdown-item" href="#"  onclick="refreshCoaTree()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
                    </div>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body " style='overflow-x:scroll;'>
                <div class="row">
                    <div class="form-group">
                        <!-- <div class="col-md-3">
                            <div class="alert alert-info">
                                Double click salah satu COA untuk melihat detail
                            </div>
                        </div> -->
                        <div class="col-md-6">
                            <input style="background-color: #a1ccff" type="text" id="key" value="" class="empty form-control" placeholder="Ketik Untuk Mencari Nama COA"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-pie-chart"></i> List Chart Of Account
                            </div>
                            <hr>
                            <div class="content_wrap">
                                <div class="zTreeDemoBackground left">
                                    <ul id="treeDemo" class="ztree">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="coaDetail">
                        </div>
                    </div>
                </div>
            </div>
		</div><!-- /.card -->
	</div>
</div>

<script type="application/javascript" src="<?= base_url('assets/pagejs/master/coa/coa.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
        $(".tglrange").daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $(".tglrange").on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
        });

        $(".tglrange").on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#dateinputx').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-M-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#dateinputx').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-M-YYYY'));
        });

        $('#dateinputx').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>
<script type="text/javascript">

        var setting = {
            check: {
                enable: true
            },
            view: {
                nameIsHTML: true,
                selectedMulti: false
            },
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "pid",
                    rootPId: "0"
                }
            },
            callback: {
                onDblClick: onNodeDblClick
            }
        };

        var zNodes = null;

        $.ajax({
            url: HOST_URL + 'master/data/js_vtree_query',
            async: false,
            type: "POST",
            global: false,
            dataType: 'json',
            success: function (data) {
                zNodes = data;
                // console.log(zNodes);
                // $.fn.zTree.init($("#treeDemo"), setting, zNodes);
                 // Fungsi search sederhana
                // $("#key").on("keyup", function() {
                //     var value = $(this).val().trim();
                //     if (value) {
                //         var nodes = zTreeObj.getNodesByParamFuzzy("name", value);
                //         if (nodes.length > 0) {
                //             zTreeObj.selectNode(nodes[0]);
                //             zTreeObj.expandNode(nodes[0], true, false, true);
                //         }
                //     }
                // });
            }
        });

        
        function initCurrencySelect2() {

            $('#idcur').select2({
                placeholder: "Ketik/Pilih Currency",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: HOST_URL + 'api/globalmodule/list_currency',
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _search_: params.term,
                            _page_: params.page,
                            _draw_: true,
                            _start_: 1,
                            _perpage_: 2,
                            _paramglobal_: '',
                        };
                    },
                    // processResults: function (data) {
                    //     return {
                    //         results: data.items.map(item => ({
                    //             id: item.idcur,
                    //             currcode: item.currcode,
                    //             currname: item.currname
                    //         }))
                    //     };
                    // }
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
                templateResult: formatCurrency,
                templateSelection: formatCurrencySelection,
                escapeMarkup: markup => markup
            });
        }

        function initIdcurByAjax() {

            let idcur = $('#coa_idcur').val();
            if (!idcur) return;

            $.ajax({
                type: 'GET',
                url: HOST_URL + 'api/globalmodule/list_currency?var=' + idcur,
                dataType: 'json'
            }).then(function (res) {

                if (!res.items || res.items.length === 0) return;

                let item = res.items[0];

                let option = new Option(
                    item.currname,
                    item.idcur,
                    true,
                    true
                );

                $('#idcur').append(option).trigger('change');

                $('#idcur').trigger({
                    type: 'select2:select',
                    params: { data: item }
                });
            });
        }
        
        function onNodeDblClick(event, treeId, treeNode) {

            // Swal.fire({
            //     title: 'Detail COA',
            //     text: 'Melihat detail COA: ' + node.text,
            //     icon: 'info'
            // });

            $.ajax({
                    url: HOST_URL + 'master/data/get_coa_detail',
                    type: 'POST',
                    data: { idcoa: treeNode.id },
                    success: function (html) {
                        $('#coaDetail').html(html);

                        $('#formCoa')[0].reset();

                        initCurrencySelect2();

                        initIdcurByAjax();       

                        $('#formCoa').data('type', 'DETAIL');
                        $('#submitCOA').hide();
                        lockCoaForm();
                    }
                });
            }


        $(document).ready(function(){
            // $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            fuzzySearch('treeDemo','#key',null,true); //initialize fuzzysearch function
            // setInitialCurrency();
        });
</script>