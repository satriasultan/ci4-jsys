<style>
    .text-wrap{
        white-space:normal;
    }
    .width-200{
        width:200px;
    }
    /* Styling tooltip agar lebih rapi */
    .tooltip-inner {
        background-color: #333 !important; 
        color: #fff !important;
        max-width: 250px; /* Default ukuran lebih kecil */
        padding: 10px;
        text-align: left;
        border-radius: 8px;
        font-size: 14px;
    }

    /* Khusus untuk tooltip "Jenis Object Pembelian" */
    #objcapex + label .tooltip-inner {
        max-width: 600px !important;
    }

    /* Tooltip default */
    .tooltip-small .tooltip-inner {
        max-width: 250px;
    }

    /* Tooltip besar hanya untuk "Jenis Object Pembelian" */
    .tooltip-large .tooltip-inner {
        max-width: 600px;
    }

    .section-block {
        background-color: #f8f6f6;
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


</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <?php if ($typeform !== 'DETAIL' && $typeform !== 'UPDATE'): ?>
                    <h1 class="m-0"> Input Customer Data</h1>
                <?php elseif ($typeform === 'UPDATE'): ?>
                    <h1 class="m-0"> Update Customer Data</h1>
                <?php else: ?>
                    <h1 class="m-0"> Detail Customer Data</h1>
                <?php endif; ?>
                    
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php //echo $t; ?></i> Versi: <?php echo $version; ?></div>
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


<div id="form-container" name="<?php echo $typeform; ?>" data-typeform="<?php echo $typeform; ?>">
</div>
<div id="form-status" name="<?php echo $status; ?>" data-typeform="<?php echo $status; ?>">
</div>

<!-- <div id="bagian" name="<?php echo $bagian; ?>" data-typeform="<?php echo $bagian; ?>">
</div> -->

<?php echo $message;?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <!-- Card Header -->
            <div class="card-header">
                <h3 class="card-title">Data Customer</h3>
            </div>

            <!-- Card Body -->
            <div class="card-body" style="background-color: #e2e2f9">

                <!-- SECTION: Informasi Umum -->
                <div class="section-block">
                    <div class="section-header">
                        <i class="fa fa-building"></i> Informasi Umum Perusahaan
                    </div>
                    <div class="row">
                        <input type="hidden" name="typeform" value="<?php echo $typeform; ?>" data-typeform="<?php echo $typeform; ?>">

                        <input type="hidden" name="docno" id="docno" value="<?= ($typeform == 'UPDATE') ? $customer['docno'] : '' ?>">
                        <div class="col-md-4">
                            <label>Nama Perusahaan/Customer</label>
                            <input type="text" name="customername" maxlength="100" class="form-control" style="text-transform: uppercase;" required>
                        </div>
                        <div class="col-md-2">
                            <label>Kode Customer <small class="text-muted">(Kode dari tim sales)</small></label>
                            <input type="text" name="kodecust" maxlength="30" class="form-control" style="text-transform: uppercase;" required>
                        </div>
                        <div class="col-md-3">
                            <label>Negara</label>
                            <select class="form-control input-sm" id="negara" name="negara">
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Bidang Perusahaan</label>
                            <select name="tipe_customer" class="form-control">
                                <option value="SERVICE">SERVICE</option>
                                <option value="ACCESSORIES">ACCESSORIES</option>
                                <option value="VEHICLE">VEHICLE</option>
                                <option value="PART">PART</option>

                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION: Informasi Kontak -->
                <div class="section-block">
                    <div class="section-header">
                        <i class="fa fa-phone"></i> Informasi Kontak
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Nama PIC</label>
                            <input type="text"  style="text-transform: uppercase;" name="kontak_pic" class="form-control" maxlength="100">
                        </div>

                        <div class="col-md-2">
                            <label>Jabatan PIC</label>
                            <input type="text"  style="text-transform: uppercase;" name="jabatan_pic" class="form-control" maxlength="100">
                        </div>

                        <div class="col-md-3">
                            <label>Email</label>
                            <input type="email" name="email" maxlength="100"  style="text-transform: lowercase;" class="form-control" id="inputEmail">
                        </div>

                        <div class="col-md-2">
                            <label>No. Telepon</label>
                            <input type="text" name="telepon" class="form-control" maxlength="16" id="inputTelepon">
                        </div>

                        <div class="col-md-2">
                            <label>No. Fax</label>
                            <input type="text" name="fax" class="form-control" maxlength="50">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Nama PIC Alternatif</label>
                            <input type="text" name="kontak_pic2" style="text-transform: uppercase;" class="form-control" maxlength="100">
                        </div>

                        <div class="col-md-2 mt-2">
                            <label>Jabatan PIC Alternatif</label>
                            <input type="text" name="jabatan_pic2" style="text-transform: uppercase;" class="form-control" maxlength="100">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Email Alternatif</label>
                            <input type="email" name="email2" maxlength="100" class="form-control" style="text-transform: lowercase;" id="inputEmail2">
                        </div>

                        <div class="col-md-2 mt-2">
                            <label>No. Telepon Alternatif</label>
                            <input type="text" name="telepon2" class="form-control" maxlength="16" id="inputTelepon2">
                        </div>

                        <div class="col-md-2 mt-2">
                            <label>No. Fax Alternatif</label>
                            <input type="text" name="fax2" class="form-control" maxlength="50">
                        </div>

                        <!-- <div class="col-md-3">
                            <label>Kontak PIC</label>
                            <input type="text" name="kontak_pic" class="form-control">
                        </div> -->
                    </div>
                </div>

                <!-- SECTION: Informasi Alamat -->
                <div class="section-block">
                    <div class="section-header">
                        <i class="fa fa-map"></i> Informasi Alamat
                    </div>
                    <div class="row">
                        <!--  +++++++++++++++++++++++ KANTOR ++++++++++++++++++++++++ -->
                        <div class="col-md-2">
                            <label for="inputdept" >Provinsi Kantor</label>
                            <select class="form-control input-sm" id="provinsi_kantor" name="provinsi_kantor">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="inputdept" >Kota Kantor</label>
                            <select class="form-control input-sm"  id="kota_kantor" name="kota_kantor" >
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="inputdept" >Kecamatan Kantor</label>
                            <select class="form-control input-sm"  id="kec_kantor" name="kec_kantor">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="inputdept" >Kel/Desa Kantor</label>
                            <select class="form-control input-sm"  id="kel_kantor" name="kel_kantor">
                                </select>
                            </div>
                        <div class="col-md-3">
                            <label>Alamat Kantor</label>
                            <textarea name="alamat_kantor" class="form-control" style="text-transform: uppercase;" rows="2"></textarea>
                        </div>
                        <div class="col-md-1">
                            <label>Kode Pos Kantor</label>
                            <input type="text" name="kodepos_kantor" class="form-control">
                        </div>
                        <div class="col-md-12"><hr></div>
                        <!--  +++++++++++++++++++++++ END KANTOR ++++++++++++++++++++++++ -->
                        <!--  +++++++++++++++++++++++ PENGIRIMAN ++++++++++++++++++++++++ -->
                        <div class="col-md-12">
                            <div class="form-check mt-1 mb-2">
                                <input type="checkbox" class="form-check-input" id="copy_alamat_pengiriman" onclick="copyAlamatKantorKePengiriman()">
                                <label class="form-check-label" for="copy_alamat_pengiriman">
                                    Alamat Pengiriman sama dengan Alamat Kantor
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2 mt-2">
                            <label for="inputdept" >Provinsi Pengiriman</label>
                            <select class="form-control input-sm" id="provinsi_pengiriman" name="provinsi_pengiriman">
                        </select>
                        </div>
                        <div class="col-md-2 mt-2">
                            <label for="inputdept" >Kota Pengiriman</label>
                            <select class="form-control input-sm"  id="kota_pengiriman" name="kota_pengiriman" >
                            </select>
                        </div>
                        <div class="col-md-2 mt-2">
                            <label for="inputdept" >Kecamatan Pengiriman</label>
                            <select class="form-control input-sm"  id="kec_pengiriman" name="kec_pengiriman">
                            </select>
                        </div>
                        <div class="col-md-2 mt-2">
                            <label for="inputdept" >Kel/Desa Pengiriman</label>
                            <select class="form-control input-sm"  id="kel_pengiriman" name="kel_pengiriman">
                            </select>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label>Alamat Pengiriman</label>
                            <textarea name="alamat_pengiriman" style="text-transform: uppercase;" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-1 mt-2">
                            <label>Kode Pos Pengiriman</label>
                            <input type="text" name="kodepos_pengiriman" class="form-control">
                        </div>

                        <div class="col-md-12"><hr></div>
                        <!--  +++++++++++++++++++++++ END PENGIRIMAN ++++++++++++++++++++++++ -->
                        <!--  +++++++++++++++++++++++ PENAGIHAN ++++++++++++++++++++++++ -->
                        <div class="col-md-12">
                            <div class="form-check mt-1 mb-2">
                                <input type="checkbox" class="form-check-input" id="copy_alamat_penagihan" onclick="copyAlamatKantorKePenagihan()">
                                <label class="form-check-label" for="copy_alamat_penagihan">
                                    Alamat Penagihan sama dengan Alamat Kantor
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2 mt-2">
                            <label for="inputdept" >Provinsi Penagihan</label>
                            <select class="form-control input-sm" id="provinsi_penagihan" name="provinsi_penagihan">
                            </select>
                        </div>
                        <div class="col-md-2 mt-2">
                            <label for="inputdept" >Kota Penagihan</label>
                            <select class="form-control input-sm"  id="kota_penagihan" name="kota_penagihan" >
                            </select>
                        </div>
                        <div class="col-md-2 mt-2">
                            <label for="inputdept" >Kecamatan Penagihan</label>
                            <select class="form-control input-sm"  id="kec_penagihan" name="kec_penagihan">
                            </select>
                        </div>
                        <div class="col-md-2 mt-2">
                            <label for="inputdept" >Kel/Desa Penagihan</label>
                            <select class="form-control input-sm"  id="kel_penagihan" name="kel_penagihan">
                            </select>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label>Alamat Penagihan</label>
                            <textarea style="text-transform: uppercase;" name="alamat_penagihan" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-1 mt-2">
                            <label>Kode Pos Penagihan</label>
                            <input type="text" name="kodepos_penagihan" class="form-control">
                        </div>

                        <!-- <div class="col-md-12"><hr></div> -->
                        <!--  +++++++++++++++++++++++ END OF PENAGIHAN ++++++++++++++++++++++++ -->
                        <!-- <div class="col-md-6 mt-2">
                            <label>Alamat 1</label>
                            <textarea name="alamat1" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label>Alamat 2</label>
                            <textarea name="alamat2" class="form-control" rows="2"></textarea>
                        </div> -->
                    </div>
                </div>

                <!-- SECTION: Legal & Tagihan -->
                <div class="section-block">
                    <div class="section-header">
                        <i class="fa fa-file-text-o"></i> Informasi Legal & Pembayaran
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label>NPWP</label>
                            <input type="text" maxlength="100" name="npwp" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>NPKP</label>
                            <input type="text" name="npkp" maxlength="100" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Plafon</label>
                            <input type="text" name="plafon" class="form-control ratakanan jtsseparator">
                        </div>
                        <div class="col-md-2">
                            <label>Terms of Payment</label>
                            <select name="top" class="form-control" required>
                                <option value="">-- Pilih Terms of Payment --</option>
                                <option value="CASH">Cash</option>
                                <option value="COD">Cash On Delivery (COD)</option>
                                <option value="CIA">Cash In Advance (CIA)</option>
                                <option value="NET 7">Net 7 Days</option>
                                <option value="NET 14">Net 14 Days</option>
                                <option value="NET 30">Net 30 Days</option>
                                <option value="NET 45">Net 45 Days</option>
                                <option value="NET 60">Net 60 Days</option>
                                <option value="EOM 30">End of Month + 30 Days (EOM 30)</option>
                            </select>
                        </div>

                        <!-- <div class="col-md-3 mt-2">
                            <label>Status Customer</label>
                            <select name="status_customer" class="form-control">
                                <option value="AKTIF">AKTIF</option>
                                <option value="TIDAK AKTIF">TIDAK AKTIF</option>
                            </select>
                        </div> -->
                        <div class="col-md-2">
                            <label>Kategori Customer</label>
                            <select name="kategori_customer" class="form-control">
                                <option value="">-- Pilih Kategori Customer --</option>
                                <option value="A">Kategori A</option>
                                <option value="B">Kategori B</option>
                                <option value="C">Kategori C</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION: Tambahan -->
                <div class="section-block">
                    <div class="section-header">
                        <i class="fa fa-info-circle"></i> Informasi Tambahan
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mt-4">
                                <div class="form-check">
                                    <input type="checkbox" name="is_verified" class="form-check-input" id="is_verified" value="1">
                                    <label class="form-check-label" for="is_verified">Terverifikasi</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <label>Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

            </div>



            <!-- /.card-body -->
            <div class="card-footer">
                <?php if ($typeform !== 'DETAIL'): ?>
                    <button type="button" id="btnSave" onclick="submitCustomer()" class="btn btn-success float-right">Save Customer</button>
                <?php endif; ?>
                <a href="<?= $back_url; ?>" class="btn btn-default float-left">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
<!-- </form> -->
<!-- Bootstrap modal -->




<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/customer.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $(".sidebar-mini").addClass( "sidebar-collapse" );
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
            $('#formSubmitCapex').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        /* ESTIMASI DATE */
        $('#estdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#estdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#formInputTransfers').bootstrapValidator('updateStatus', 'estdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#estdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        const budgetJns = document.getElementById("budgetjns");
        const valBudget = document.getElementById("valbudget");
        const valPrice = document.getElementById("valprice");
        const valRemainBudget = document.getElementById("valremainbudget");
        const budgetId = $("#budgetid"); // Gunakan jQuery untuk Select2
        budgetJns.addEventListener("change", function () {
            if (this.value === "UNBUDGET") {
                valBudget.value = "0";
                valPrice.value = "0";
                valRemainBudget.value = "0";
                budgetId.val(null).trigger("change"); // Hapus pilihan di Select2
            }
        });

        




        document.querySelectorAll(".tooltip-icon").forEach(function (tooltipIcon) {
            var content = tooltipIcon.getAttribute("data-tooltip-content");

            if (content) {
            new bootstrap.Tooltip(tooltipIcon, {
                title: content,
                sanitize: false, // Mengizinkan HTML di dalam tooltip
                placement: "right",
                customClass: tooltipIcon.closest('.col-sm-3')?.querySelector('#objcapex') ? 'tooltip-large' : 'tooltip-small'
            });
            }
        });
    });

</script>