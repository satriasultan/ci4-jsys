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
<?php
    $isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
    $disabled = $isIT ? '' : 'disabled';
?>
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('purchase/trans/finalEntryVoidPP') ?>" method="post" id="formVoidPP">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Void PP</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Void Permintaan Pembelian
                            </div>
                            <div class="row">

                                <!-- LEFT COLUMN -->
                                <div class="col-md-6">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cabang">Cabang / Job</label>
                                                <select name="cabang" id="cabang" class="form-control" required></select>
                                            </div>
                                        </div>

                                        
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>No. Jurnal</label>

                                                <div class="d-flex">
                                                    <input type="text"
                                                        name="prefix"
                                                        id="prefix"
                                                        class="form-control me-1"
                                                        maxlength="3"
                                                        style="text-transform: uppercase;"
                                                        pattern="[A-Z0-9]+">

                                                    <span class="px-2 align-self-center">/</span>

                                                    <input type="text"
                                                        name="infix"
                                                        id="infix"
                                                        class="form-control mx-1"
                                                        readonly>

                                                    <span class="px-2 align-self-center">/</span>

                                                    <input type="text"
                                                        name="sufix"
                                                        id="sufix"
                                                        class="form-control ms-1"
                                                        readonly>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="20"     value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>" style="text-transform: uppercase;" readonly>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="docdate">Tanggal</label>
                                                    <input type="text"
                                                    name="docdate"
                                                    id="docdate"
                                                    class="form-control"
                                                    placeholder="Tanggal">
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row">
                                        <!-- <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="estpakai">Estimasi Tanggal Pakai</label>
                                                <input type="text"
                                                    name="estpakai"
                                                    id="estpakai"
                                                    class="form-control"
                                                    placeholder="Estimasi Tanggal Pakai">
                                            </div>
                                        </div> -->
                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label for="pemohon">Pemohon</label>
                                                <input type="text"
                                                    name="pemohon"
                                                    id="pemohon"
                                                    class="form-control"
                                                    maxlength="50"
                                                    placeholder="Pemohon"
                                                    readonly
                                                    value="<?= esc(strtoupper($userlogin)) ?>">
                                            </div>
                                        </div>
                                    </div>
                                        
                                </div>

                                <!-- RIGHT COLUMN -->
                                <div class="col-md-6">
                                    <!-- <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan"
                                                id="keterangan"
                                                class="form-control"
                                                rows="10"
                                                placeholder="Catatan / Informasi..."
                                                style="text-transform:uppercase;"></textarea>
                                    </div> -->
                                </div>

                            </div>

                        </div>
                </div>
            </div>

            <div class="card mt-3 card-primary">
                <div class="card-header clearfix">
                    <h3 class="card-title">
                        Detail PP & Barang
                    </h3>

                    <div class="float-right d-flex align-items-center gap-2">
                        <button type="button"
                                class="btn btn-success btn-lg action-btn"
                                data-toggle="tooltip"
                                title="Input Data"
                                onclick="btnInputDetail()">
                            <i class="fa fa-plus"></i>
                        </button>

                        <!-- <button type="button"
                                class="btn btn-warning btn-lg action-btn"
                                data-toggle="tooltip"
                                title="Update Data"
                                onclick="btnUpdateDetail()">
                            <i class="fa fa-edit"></i>
                        </button> -->

                        <button type="button"
                                class="btn btn-danger btn-lg action-btn"
                                data-toggle="tooltip"
                                title="Hapus Data"
                                onclick="btnDeleteDetail()">
                            <i class="fa fa-trash"></i>
                        </button>


                    </div>
                </div>
                <div class="card-body">
                    <table id="tabppdtl" class="table table-bordered table-striped">
                        <thead class="bg-primary text-white">
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>PP</th>
                            <th>ID Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Quantity</th>
                            <th>Keterangan</th>
                        </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?= base_url('purchase/trans/voidpp') ?>"
                        class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <?php if ($typeform != 'DETAIL' && $dtldata != null): ?>
                        <button type="submit"
                                onclick="return confirm('Finish Entry?')"
                                class="btn btn-success btn-lg float-right">
                            <i class="fa fa-save"></i> Simpan Final Data
                        </button>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </form>
        <!-- /.card -->
    <!--/.col (left) -->
    <!-- right column -->
    <div class="col-md-6">

    </div>
    <!--/.col (right) -->
</div>




<!-- ================= MODAL TAX DETAIL ================= -->
<div class="modal fade" id="modalDetailVoidPP" tabindex="-1" role="dialog" aria-labelledby="modalDetailVoidPPLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDetailVoidPPLabel">
                    </i> Input Item Detail
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM -->
            <form id="formVoidPPDetail">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="idurut" id="idurut">
                    <input type="hidden" name="docno" id="docno">
                    <!-- <input type="hidden" name="status" id="status" value="P">
                    <input type="hidden" name="chold" id="chold" value="NO"> -->

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>PP</label>
                                <select name="docnopp" id="docnopp"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>
                    
                        <!-- <div class="col-md-4">
                            <div class="form-group">
                                <label>ID Barang</label>
                                <input name="idbarang" id="idbarang"
                                        class="form-control"
                                        style="text-transform: uppercase;" readonly>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text"
                                        name="nmbarang"
                                        id="nmbarang"
                                        class="form-control"
                                        placeholder="Nama Barang"
                                        style="text-transform:uppercase" readonly>
                            </div>
                        </div> -->
                    </div>

                    <!-- ROW 2 -->
                    <!-- <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Satuan</label>
                                <input name="unit" id="unit"
                                        class="form-control select2"
                                        style="width:100%" readonly>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="text"
                                        name="qty"
                                        id="qty"
                                        class="form-control jtsseparator ratakanan"
                                        placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea type="text"
                                        name="description"
                                        rows="4"
                                        style="text-transform: uppercase;"
                                        id="description"
                                        class="form-control"></textarea>
                            </div>
                        </div>
                    </div> -->
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="$('#modalDetailVoidPP').modal('hide')">
                        <i class="fa fa-times"></i> Batal
                    </button>

                    <button type="button"
                            class="btn btn-primary"
                            onclick="saveVoidPPDetail()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script type="application/javascript" src="<?= base_url('assets/pagejs/purchase/voidpp.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
        var dtldata = <?= json_encode($dtldata ?? null) ?>;

        if (dtldata && dtldata.docno && dtldata.docno.trim() !== '') {
            $('#docno').val(dtldata.docno.trim()).prop('readonly', true);
        }


        $('#docdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'YYYY-MM-DD' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });



        $('#estpakai').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'YYYY-MM-DD' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#estpakai').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'estpakai', 'NOT_VALIDATED').bootstrapValidator('validateField', 'estpakai');
        });
        $('#estpakai').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        
        $('#periodemulai').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodemulai').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodemulai').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        
        $('#periodeakhir').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodeakhir').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodeakhir').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        

    });

</script>