<style>
    .gap-2 > *{
        margin-left:8px;
    }

    .action-btn{
        width:38px;
        height:38px;
        padding:0;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .action-btn:hover{
        transform:translateY(-3px) scale(1.05);
        box-shadow:0 8px 18px rgba(0,0,0,.18);
    }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= ucwords(strtolower(trim($title))); ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right:10px;padding-top:.7%">
                        Versi: <?= $version; ?>
                    </div>
                    <input type="hidden" id="classmenu"
                           value="<?= str_replace('.','_',$kodemenu) ?>">

                    <?php foreach ($y as $y1): ?>
                        <?php if(trim($y1->kodemenu)!=trim($kodemenu)): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= base_url(trim($y1->linkmenu)); ?>">
                                    <i class="fa <?= trim($y1->iconmenu); ?>"></i>
                                    <?= trim($y1->namamenu); ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active">
                                <i class="fa <?= trim($y1->iconmenu); ?>"></i>
                                <?= trim($y1->namamenu); ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<?= $message; ?>

<?php
$isIT     = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
$disabled = $isIT ? '' : 'disabled';
?>

<div class="row">
    <div class="col-md-12">
        <!-- ================= FORM ================= -->
        <div class="card card-primary">
            <div class="card-header clearfix">
                <h3 class="card-title">
                  Data Master Tax
                </h3>

                <div class="float-right d-flex align-items-center gap-2">
                    <!-- UPDATE -->
                    <button type="button"
                            id="btnUpdate"
                            class="btn btn-warning btn-lg action-btn"
                            data-bs-original-title="Update Master"
                            onclick="updateData()">
                        <i class="fa fa-edit"></i>
                    </button>

                    <button type="button"
                            id="btnSave"
                            class="btn btn-primary btn-lg action-btn d-none"
                            data-bs-original-title="Simpan Master"
                            onclick="saveData()">
                        <i class="fa fa-save"></i>
                    </button>

                    <button type="button"
                            id="btnCancel"
                            class="btn btn-secondary btn-lg action-btn d-none"
                            data-bs-original-title="Cancel Data"
                            onclick="cancelUpdate()">
                        <i class="fa fa-undo"></i>
                    </button>
                </div>


            </div>



            <form id="formInputTaxMst" method="post" enctype="multipart/form-data">
                <div class="card-body">

                    <div class="section-block">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>KODE PAJAK</label>
                                    <input type="hidden" name="type" value="<?= $type ?>">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="text" name="idtax" id="idtax"
                                           class="form-control form-master"
                                           placeholder="KODE PAJAK"
                                           style="text-transform:uppercase;"
                                           readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>KETERANGAN</label>
                                    <input type="text" name="nmtax" id="nmtax"
                                           class="form-control form-master"
                                           placeholder="Nama Tax"
                                           style="text-transform:uppercase;"
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </form>
        </div>

        <!-- ================= GRID LIST ================= -->
        <div class="card mt-3 card-primary">
            <div class="card-header clearfix">
                <h3 class="card-title">
                   Detail Master Tax
                </h3>

                <div class="float-right d-flex align-items-center gap-2">
                    <button type="button"
                            class="btn btn-success btn-lg action-btn"
                            data-toggle="tooltip"
                            title="Input Data"
                            onclick="btnInputDetail()">
                        <i class="fa fa-plus"></i>
                    </button>

                    <button type="button"
                            class="btn btn-warning btn-lg action-btn"
                            data-toggle="tooltip"
                            title="Update Data"
                            onclick="btnUpdateDetail()">
                        <i class="fa fa-edit"></i>
                    </button>

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
                <table id="tblMasterPajakDetail" class="table table-bordered table-striped">
                    <thead class="bg-primary text-white">
                    <tr>
                        <th width="30">
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th>ID Tax</th>
                        <th>Jenis</th>
                        <th>Nama</th>
                        <th>Kode Pajak</th>
                        <th>PRK Masukan</th>
                        <th>PRK Keluaran</th>
                        <th>PRK FPJ Msk</th>
                        <th>PRK FPJ Klr</th>
                        <th>PRK FPJ Msk Rep.</th>
                        <th>PRK FPJ Klr Rep.</th>
                        <th>Dpp Lain</th>
                        <th>Dpp Pajak</th>
                        <th>% Pajak</th>
                    </tr>
                    </thead>

                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light d-flex align-items-center">
                <a href="<?= base_url('master/data/tax') ?>"
                   class="btn btn-default">
                    <i class="fa fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>

        </div>

    </div>
</div>

<!-- ================= MODAL TAX DETAIL ================= -->
<div class="modal fade" id="modalTaxDetail" tabindex="-1" role="dialog" aria-labelledby="modalTaxDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalTaxDetailLabel">
                    <i class="fa fa-plus"></i> Input Tax Detail
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM -->
            <form id="formTaxDetail">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="iddtl" id="iddtl">
                    <input type="hidden" name="idtax" id="idtax">
                    <input type="hidden" name="status" id="status" value="P">
                    <input type="hidden" name="chold" id="chold" value="NO">

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jenis</label>
                                <input type="text"
                                       name="idgrouptax"
                                       id="idgrouptax"
                                       class="form-control"
                                       placeholder="Jenis Pajak"
                                       style="text-transform:uppercase">
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text"
                                       name="nmgrouptax"
                                       id="nmgrouptax"
                                       class="form-control"
                                       placeholder="Nama Pajak"
                                       style="text-transform:uppercase">
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kode Pajak</label>
                                <input type="text"
                                       name="kodepajak"
                                       id="kodepajak"
                                       class="form-control"
                                       placeholder="Kode Pajak"
                                       style="text-transform:uppercase">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>% Pajak</label>
                                <input type="number"
                                       step="0.01"
                                       name="percentation"
                                       id="percentation"
                                       class="form-control"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- ROW 3 -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PRK Masukan</label>
                                <select name="prk_masukan" id="prk_masukan"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PRK Keluaran</label>
                                <select name="prk_keluaran" id="prk_keluaran"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PRK FPJ Masuk</label>
                                <select name="prk_fpj_msk" id="prk_fpj_msk"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PRK FPJ Keluar</label>
                                <select name="prk_fpj_klr" id="prk_fpj_klr"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 4 -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PRK FPJ Masuk Rep.</label>
                                <select name="prk_fpj_msk_rep" id="prk_fpj_msk_rep"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PRK FPJ Keluar Rep.</label>
                                <select name="prk_fpj_klr_rep" id="prk_fpj_klr_rep"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>DPP Lain</label>
                                <input type="text"
                                       name="rumus_dpp_lain"
                                       id="rumus_dpp_lain"
                                       class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>DPP Pajak</label>
                                <input type="text"
                                       name="rumus_pajak"
                                       id="rumus_pajak"
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                            onclick="$('#modalTaxDetail').modal('hide')">
                        <i class="fa fa-times"></i> Batal
                    </button>

                    <button type="button"
                            class="btn btn-primary"
                            onclick="saveTaxDetail()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ================= END MODAL ================= -->

<script type="application/javascript" src="<?= base_url('assets/pagejs/master/tax/tax.js') ?>"></script>
<script>
    function btnInput(){
        $('#formInputTax')[0].reset();
    }

    function btnUpdate(){
        alert('Mode Update');
    }

    function btnDelete(){
        if(confirm('Yakin hapus data?')){
            alert('Delete action');
        }
    }

    function btnCancel(){
        $('#formInputTax')[0].reset();
    }
</script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>


<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker


        $('#docdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
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