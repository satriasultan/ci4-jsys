<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">

            <!-- LEFT -->
            <div class="col-sm-6">
                <h1 class="m-0">Standart Cost Produksi</h1>
            </div>

            <!-- RIGHT -->
            <div class="col-sm-6 d-flex justify-content-end align-items-center">

                <!-- VERSION -->
                <div style="margin-right: 10px; font-size: 13px;">
                    Menu ID <?php echo $version; ?>
                </div>

                <!-- BREADCRUMB -->
                <ol class="breadcrumb mb-0">

                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>">

                    <?php foreach ($y as $y1) { ?>
                        <?php if (trim($y1->kodemenu) != trim($kodemenu)) { ?>
                            <li class="breadcrumb-item">
                                <a href="<?= base_url(trim($y1->linkmenu)) ?>">
                                    <i class="fa <?= trim($y1->iconmenu); ?>"></i>
                                    <?= trim($y1->namamenu); ?>
                                </a>
                            </li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active">
                                <i class="fa <?= trim($y1->iconmenu); ?>"></i>
                                <?= trim($y1->namamenu); ?>
                            </li>
                        <?php } ?>
                    <?php } ?>

                </ol>
            </div>

        </div>
    </div>
</div>

<?php echo $message;?>
<?php echo $showUnfinish; ?>
<style>
    .text-wrap{
        white-space:normal;
    }
    .width-90{
        width:90px;
    }
    .width-150{
        width:150px;
    }
    .width-200{
        width:200px;
    }

    /* ===============================
   TABLE HEADER STYLE - CORPORATE
================================ */

    #tTrxTransferLocaton {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    /* HEADER */
    #tTrxTransferLocaton thead th {
        background: linear-gradient(135deg, #1f2937, #374151);
        color: #ffffff;
        font-weight: 600;
        text-align: center;
        padding: 10px 8px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    /* Rounded corner atas */
    #tTrxTransferLocaton thead th:first-child {
        border-top-left-radius: 8px;
    }

    #tTrxTransferLocaton thead th:last-child {
        border-top-right-radius: 8px;
    }

    /* Zebra row */
    #tTrxTransferLocaton tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    /* Hover row */
    #tTrxTransferLocaton tbody tr:hover {
        background-color: #e9f2ff;
        transition: 0.2s ease-in-out;
    }

    /* Center kolom kecil */
    #tTrxTransferLocaton tbody td:nth-child(1),
    #tTrxTransferLocaton tbody td:nth-child(2),
    #tTrxTransferLocaton tbody td:nth-child(4),
    #tTrxTransferLocaton tbody td:nth-child(7),
    #tTrxTransferLocaton tbody td:nth-child(9) {
        text-align: center;
    }

/* header */

    /* ===============================
   CORPORATE GREY CARD STYLE
================================ */

    .card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    /* Header Grey Gradient */
    .card-header {
        background: linear-gradient(135deg, #4b5563, #6b7280);
        border-bottom: none;
        padding: 12px 18px;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
    }

    /* Dropdown Button Grey */
    .card-header .btn-primary {
        background: #e5e7eb;
        color: #374151;
        border: none;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s ease-in-out;
    }

    .card-header .btn-primary:hover {
        background: #d1d5db;
        transform: translateY(-2px);
    }

    /* Dropdown Menu */
    .dropdown-menu {
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        padding: 6px 0;
    }

    /* Dropdown Item */
    .dropdown-item {
        padding: 8px 16px;
        font-size: 14px;
        color: #374151;
        transition: 0.2s ease-in-out;
    }

    .dropdown-item:hover {
        background: #f3f4f6;
        color: #111827;
        padding-left: 20px;
    }

    /* Icon spacing */
    .dropdown-item i {
        width: 20px;
        color: #6b7280;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-bs-toggle="dropdown"><?php echo 'Menu'; ?>
                    </button>
                    <div class="dropdown-menu">
                        <?php if (isset($dtl_akses['a_input']) && trim($dtl_akses['a_input']) === 't'): ?>
                            <a class="dropdown-item" href="<?= base_url('production/trans/add_standart_cost') ?>"><i class="fa fa-plus"></i><?php echo '   Input'; ?> </a>
                        <?php endif; ?>
                        <!-- <a class="dropdown-item disabled" data-bs-toggle="modal" data-bs-target="#filter"  href="#"><i class="fa fa-filter"></i><?php echo '   Filter'; ?></a> -->
                        <a class="dropdown-item" href="#"  onclick="reload_standart_cost()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
                    </div>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body table-responsive" style='overflow-x:scroll;'>
                <table id="tstandart_cost" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th width="2%">Action</th>

                        <th>Document</th>
                        <th>Cabang</th>
                        <th>Pemohon</th>

                        <th>Doc Date</th>
                        <th>Active Date</th>

                        <th>Doc Ref</th>

                        <th>Description</th>


                        <th>Status</th>

                        <th>Input By</th>
                        <th>Input Date</th>

                        <th>Update By</th>
                        <th>Update Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div>
</div>


<!--Modal untuk Filter-->
<div class="modal fade" id="filter">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter">
                <div class="modal-header">
                    <h4 class="modal-title">Filtering Data</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggalpo">Tanggal Kedatangan</label>
                        <input type="text" class="form-control tglrange" id="tglrange"  name="tglrange" data-date-format="dd-mm-yyyy" required placeholder="Entry LPB Date" required>
                    </div>
                    <div class="form-group">
                        <label for="itembarang">Item Barang</label>
                        <select name="idbarang_filter" id="idbarang_filter" class="form-control" placeholder="Pilih Item Barang">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="suppliername">Nama Supplier</label>
                        <select name="namasupplier" id="namasupplier" class="form-control" placeholder="Pilih Item Barang">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_filter">Status</label>
                        <select name="status_filter" id="status_filter" class="form-control" placeholder="Pilih Status Filter">
                            <option value=""> Semua Status</option>
                            <option value="A"> Outstanding</option>
                            <option value="S"> Outstanding Sebagian</option>
                            <option value="P"> Diterima Penuh</option>
                        </select>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-reset-tx" class="btn btn-default">Reset</button>
                    <button type="button" id="btn-filter-tx" class="btn btn-primary">Filter</button>

                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->






<script type="application/javascript" src="<?= base_url('assets/pagejs/production/standart_cost/standart_cost.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

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

    $('#activedate').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        locale: { format: 'YYYY-MM-DD' },
        cancelLabel: 'Clear'
    });

    // handler apply/cancel
    $('#activedate').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
        // jika butuh validasi bootstrapValidator:
        // $('#formInputTransfers').bootstrapValidator('updateStatus', 'activedate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'activedate');
    });
    $('#activedate').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
</script>
<script type="application/javascript">
    $(".tglrange").daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $("#status_filter").select2();

    $(".tglrange").on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
    });

    $(".tglrange").on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
</script>