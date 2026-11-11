<!-- =========================================================
     v_add_jup.php - Corporate Style
     Referensi tampilan : finance/finance/v_add_penerimaankb.php
     Catatan : perubahan hanya pada font, warna, spacing, dan
               kerapian layout. Nama field, id, dan fungsi
               JavaScript tidak diubah.
     ========================================================= -->
<link rel="stylesheet" href="<?= base_url('assets/css/corp-form.css') ?>">
<style>
    /* Suplemen khusus Jurnal Umum Perkiraan (JUP): tabel detail & separator angka
       mengikuti tema corporate yang sama (referensi v_add_penerimaankb.php). */
    .corp-form-container #tabjupdtl thead th {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        border-color: var(--corp-border) !important;
        padding: 10px 12px !important;
        vertical-align: middle;
    }

    .corp-form-container #tabjupdtl tbody td {
        padding: 9px 12px !important;
        border-color: var(--corp-border) !important;
        vertical-align: middle;
        color: var(--corp-text);
    }

    .corp-form-container #tabjupdtl tbody tr:hover {
        background-color: var(--corp-primary-light) !important;
    }

    .corp-form-container .form-control.jtsseparator,
    .corp-form-container .form-control.ratakanan {
        text-align: right;
    }

    /* No. Jurnal JUP: prefix / infix / sufix sejajar & proporsional */
    .corp-form-container #prefix {
        min-width: 80px;
        flex: 0 0 80px;
    }

    .corp-form-container #infix {
        min-width: 70px;
        flex: 0 0 70px;
        background-color: var(--corp-bg-disabled);
    }

    .corp-form-container #sufix {
        min-width: 110px;
        flex: 0 0 110px;
    }

    .corp-form-container .is-invalid {
        border-color: var(--corp-danger) !important;
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
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Menu ID <?php echo $version; ?></div>
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
<div class="corp-form-container">
    <div class="row">
        <!-- left column -->
        <form action="<?= base_url('ka/accounting/finalEntryJUP') ?>" method="post" id="formPO">
            <div class="col-md-12">
                <!-- jquery validation -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-file-text-o"></i> <?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Jurnal Umum Perkiraan</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Jurnal Umum Perkiraan (JUP)
                            </div>

                            <!-- ROW 1 : Informasi dokumen -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cabang">Cabang / Job</label>
                                        <select name="cabang" id="cabang" class="form-control" required></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="docdate">Tanggal</label>
                                        <input type="text" name="docdate" id="docdate" class="form-control" placeholder="Tanggal">
                                    </div>
                                </div>
                            </div>

                            <!-- ROW 2 : No. Jurnal & keterangan -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. Jurnal JUP</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" name="prefix" id="prefix" class="form-control me-1" maxlength="3" style="text-transform: uppercase;" pattern="[A-Z0-9]+">
                                            <span class="px-2 align-self-center">/</span>
                                            <input type="text" name="infix" id="infix" class="form-control mx-1" readonly>
                                            <span class="px-2 align-self-center">/</span>
                                            <input type="text" name="sufix" id="sufix" class="form-control ms-1" readonly>
                                        </div>
                                        <input type="hidden" name="docno" id="docno" value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Keterangan" style="text-transform:uppercase;"></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- DETAIL PERKIRAAN -->
                <div class="card mt-3 card-primary">
                    <div class="card-header d-flex align-items-center flex-wrap gap-2">
                        <h3 class="card-title mb-0 me-3">
                            <i class="fa fa-book"></i> Detail Perkiraan
                        </h3>

                        <div class="ms-auto d-flex align-items-center gap-2">
                            <button type="button"
                                    class="btn btn-success btn-sm"
                                    data-bs-toggle="tooltip"
                                    title="Input Data"
                                    onclick="btnInputDetail()">
                                <i class="fa fa-plus"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="tooltip"
                                    title="Update Data"
                                    onclick="btnUpdateDetail()">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="tooltip"
                                    title="Hapus Data"
                                    onclick="btnDeleteDetail()">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table id="tabjupdtl" class="table table-bordered table-hover table-sm mb-0" width="100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="checkAll">
                                        </th>
                                        <th>Kode Perk.</th>
                                        <th>Nama Perkiraan</th>
                                        <th>Keterangan</th>
                                        <th>DK</th>
                                        <th>Cost/Profit Center</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end align-items-center">
                            <label for="balance" class="me-2 fw-semibold mb-0">Balance</label>
                            <input type="text"
                                   name="balance"
                                   id="balance"
                                   class="form-control jtsseparator ratakanan"
                                   style="max-width: 220px;"
                                   readonly>
                        </div>
                        <hr>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('ka/accounting/jup') ?>"
                               class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i>
                                Kembali
                            </a>
                            <?php if ($typeform != 'DETAIL'): ?>
                                <button type="submit"
                                        id="btnSimpanFinalJUP"
                                        class="btn btn-success">
                                    <i class="fa fa-save me-1"></i> Simpan Final Data
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </form>
        <!--/.col (left) -->
    </div>
</div><!-- /.corp-form-container -->


<!-- ================= MODAL INPUT DETAIL JUP ================= -->
<div class="modal fade" id="modalDetailJUP" tabindex="-1" role="dialog" aria-labelledby="modalDetailJUPLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDetailJUPLabel">
                    Input Perkiraan Detail
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM -->
            <form id="formJUPDetail">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="idurut" id="idurut">
                    <input type="hidden" name="docno" id="docno">
                    <!-- <input type="hidden" name="status" id="status" value="P">
                    <input type="hidden" name="chold" id="chold" value="NO"> -->

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ID Perkiraan</label>
                                <select name="idcoa" id="idcoa"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Perkiraan</label>
                                <input type="text"
                                       name="nmcoa"
                                       id="nmcoa"
                                       class="form-control"
                                       placeholder="Nama Perkiraan"
                                       style="text-transform:uppercase" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="dk">Debit/Kredit</label>
                                <select name="dk" id="dk" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="D">D</option>
                                    <option value="K">K</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Cost Center</label>
                                <select name="cabangdtl" id="cabangdtl"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea type="text"
                                          name="remarks"
                                          rows="4"
                                          style="text-transform: uppercase;"
                                          id="remarks"
                                          class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Nilai</label>
                                <input type="text"
                                       name="nilai"
                                       id="nilai"
                                       class="form-control jtsseparator ratakanan"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    <button type="button"
                            class="btn btn-primary"
                            onclick="saveJUPDetail()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="$('#modalDetailJUP').modal('hide')">
                        <i class="fa fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="application/javascript" src="<?= base_url('assets/pagejs/finance/jup.js') ?>"></script>
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



        $('#senddate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'YYYY-MM-DD' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#senddate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'senddate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'senddate');
        });
        $('#senddate').on('cancel.daterangepicker', function(ev, picker) {
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

        document.addEventListener('DOMContentLoaded', function() {

            document.addEventListener('keydown', function(e) {

                const activeTag = document.activeElement.tagName;

                if (
                    e.ctrlKey &&
                    e.shiftKey &&
                    e.code === 'KeyE' &&
                    !['INPUT','TEXTAREA','SELECT'].includes(activeTag)
                ) {
                    e.preventDefault();
                    console.log('Shortcut triggered');
                    btnUpdateDetail();
                }

            });

        });

    });

</script>