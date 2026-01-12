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
<div class="row">
    <div class="col-md-12">
        <div class="card card-dark">
            <div class="card-header">
                <a href="#" class="btn btn-primary" onclick="add_pengajuan_it()" style="margin:10px"><i class="fa fa-plus"></i>  Tambah  </a>
                <a href="#" class="btn btn-info" style="margin:10px"  data-bs-toggle="modal" data-bs-target="#filter" ><i class="fa fa-filter"></i>  Filter  </a>
                <a href="#" class="btn btn-info" style="margin:10px"  data-bs-toggle="modal" data-bs-target="#filter_cetak" ><i class="fa fa-filter"></i>  Filter & Cetak </a>
            </div>
            <div class="card-body " style='overflow-x:scroll;'>
                <table id="capital_pengajuan_all" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th width="8%">Action</th>
                        <th>PIC</th>
                        <th>Departement</th>
                        <th>Tgl Pengajuan</th>
                        <th>Status</th>
                        <!--th>B/Unbudget</th-->
                        <!--th>Nilai Budget</th-->
                        <th>Kebutuhan</th>
                        <th>No Assets</th>
                        <th>Gambar</th>
                        <th>Kategori</th>
                        <th>Sub Kategori</th>
                        <th>Spesifikasi</th>
                        <th>Level</th>
                        <th>Perkiraan Harga</th>
                        <th>Referensi</th>
                        <th>Deskripsi</th>
                        <th>Review</th>
                        <th>Alasan Tolak</th>

                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div>
    </div>
</div>

<!--Modal untuk Filter-->
<div class="modal fade" id="filter">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Filtering Data </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kddept_filter_aja">Pilih Department</label>
                        <select name="kddept_filter_aja" id="kddept_filter_aja" class="form-control" placeholder="Pilih Department">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_filter_aja">Pilih Status</label>
                        <select name="status_filter_aja" id="status_filter_aja" class="form-control" placeholder="Pilih Status">
                            <option value=""> --- PILIH STATUS ---</option>
                            <option value="I">INPUT ADMIN</option>
                            <option value="T">DITOLAK</option>
                            <option value="C">DIBATALKAN USER</option>
                            <option value="B">DISETUJUI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idgroup_filter_aja">Pilih Kategori</label>
                        <select name="idgroup_filter_aja" id="idgroup_filter_aja" class="form-control" placeholder="Pilih Kategori">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idsubgroup_filter_aja">Pilih Sub Kategori</label>
                        <select name="idsubgroup_filter_aja" id="idsubgroup_filter_aja" class="form-control" placeholder="Pilih Sub Kategori">
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-reset" class="btn btn-default"><i class="fa fa-print"></i> Reset </button>
                    <button type="button" id="btn-filter" class="btn btn-primary"><i class="fa fa-print"></i> Filter</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!--Modal untuk Filter-->
<div class="modal fade" id="filter_cetak">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter-cetak" method="post"  action="<?php echo base_url('capital/administration/print_pengajuan_dept')?>" >
                <div class="modal-header">
                    <h4 class="modal-title">Filtering Data dan Cetak</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!--div class="form-group">
                        <label for="tanggalpo">Tanggal Kedatangan</label>
                        <input type="text" class="form-control tglrange" id="tglrange"  name="tglrange" data-date-format="dd-mm-yyyy" required placeholder="Entry LPB Date" required>
                    </div--->
                    <div class="form-group">
                        <label for="kddept_filter">Pilih Department</label>
                        <select name="kddept_filter" id="kddept_filter" class="form-control" placeholder="Pilih Department">
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="submit" id="btn-filter-tx" class="btn btn-primary"><i class="fa fa-print"></i> Cetak</button>

                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!--Modal untuk Input Menu Utama-->
<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Input</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="formPengajuan" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <div class="form-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Kebutuhan</label>
                                <div class="col-md-12">
                                    <select name="kebutuhanjenis" id="kebutuhanjenis" class="form-control inform" style="text-transform:uppercase;" >
                                        <!--option value="">--Pilih Hold--</option-->
                                        <option value="BARU">BARU </option>
                                        <option value="REPLACEMENT"> REPLACEMENT </option>

                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Nomor Asset IT</label>
                                <div class="col-md-12">
                                    <input name="noassetit" id="noassetit"  placeholder="Input Nomor Asset Jika Replacement" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">User Pemakai/Penanggung Jawab</label>
                                <div class="col-md-12">
                                    <select name="nik" id="nik" class="form-control select2_kary" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <!--div class="form-group col-md-3">
                                <label class="control-label col-md-12">User Pengguna/PIC</label>
                                <div class="col-md-12">
                                    <input name="nmlengkap" id="nmlengkap"  placeholder="Mohon Input User Pengguna/ PIC" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div-->
                            <div class="form-group col-md-3">
                                <label for="kddept">Pilih Department</label>
                                <select name="kddept" id="kddept" class="form-control inform" placeholder="Pilih Department">
                                </select>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Kategori</label>
                                <div class="col-md-12">
                                    <select name="idgroup" id="idgroup" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Sub Kategori</label>
                                <div class="col-md-12">
                                    <select name="idsubgroup" id="idsubgroup" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Level</label>
                                <div class="col-md-12">
                                    <select name="kdlvl" id="kdlvl" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Perkiraan Harga</label>
                                <div class="col-md-12">
                                    <input name="estprice_kdlvl" placeholder="Ketik perkiraan harga" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Budget/Unbudget</label>
                                <div class="col-md-12">
                                    <select name="budgeting" id="budgeting" class="form-control inform" style="text-transform:uppercase;" >
                                        <!--option value="">--Pilih Hold--</option-->
                                        <option value="BUDGET">BUDGET </option>
                                        <option value="UNBUDGET"> UNBUDGET </option>

                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Nilai Budget/Unbudget</label>
                                <div class="col-md-12">
                                    <input name="vbudgeting" id="vbudgeting"  placeholder="Ketik Nilai Budget Yang Diajukan" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Tanggal Pengajuan</label>
                                <div class="col-md-12">
                                    <input name="docdate" id="docdate" class="form-control" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Cost Center</label>
                                <div class="col-md-12">
                                    <select name="idcostcenter" id="idcostcenter" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Tanggal Rencana Realisasi</label>
                                <div class="col-md-12">
                                    <input name="datevrealisasi" id="datevrealisasi" class="form-control" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Spesifikasi</label>
                                <div class="col-md-12">
                                    <input name="spec" placeholder="Ketik spesifikasi sesuai ketentuan" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Referensi/link</label>
                                <div class="col-md-12">
                                    <input name="estvendor" placeholder="Referensi bisa nama vendor/ contact / Link toko online/ " class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <?php /*
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Nama Pembanding Vendor 1</label>
                                <div class="col-md-12">
                                    <input name="vendor_ref1" placeholder="Ketik nama vendor 1" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Harga Perolehan Vendor 1</label>
                                <div class="col-md-12">
                                    <input name="pricev_ref1" id="pricev_ref1"  placeholder="Tulis Harga Vendor 1" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Nama Pembanding Vendor 2</label>
                                <div class="col-md-12">
                                    <input name="vendor_ref2" id="vendor_ref2" placeholder="Ketik nama vendor 2" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Harga Perolehan Vendor 2</label>
                                <div class="col-md-12">
                                    <input name="pricev_ref2" id="pricev_ref2"  placeholder="Tulis Harga Vendor 2" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Nama Pembanding Vendor 3</label>
                                <div class="col-md-12">
                                    <input name="vendor_ref3" id="vendor_ref3" placeholder="Ketik nama vendor 3" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Harga Perolehan Vendor 3</label>
                                <div class="col-md-12">
                                    <input name="pricev_ref3"  id="pricev_ref3" placeholder="Tulis Harga Vendor 3" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
 */ ?>
                        <div class="row">

                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Keterangan</label>
                                <div class="col-md-12">
                                    <textarea  style="text-transform: uppercase"  name="keterangan" id="keterangan" class="form-control inform" rows="4" placeholder="Tulis keterangan ..." ></textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="savePengajuan()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<!-- Bootstrap modal -->
<div class="modal fade" id="modal_review" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title-review" id="myModalLabel">Input</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="<?php echo base_url('#')?>" method="post" id="formReview" enctype="multipart/form-data" role="form">
                    <input type="hidden" value="REVIEW" name="type"/>
                    <input type="hidden" name="id"/>
                    <div class="form-body">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for="inputimage" >Upload Gambar Assets</label>
                                    <!--<input type="text" class="form-control input-sm" id="cimage" name="cimage">-->
                                    <div id="wrapper" class="imagewrapper">
                                        <div align="center" id="image-holder">
                                            <img id=imgshow" src="<?php echo base_url('assets/img/user.png') ?>" class="thumb-image" style="max-width: 200px; max-height: 200px;">
                                        </div>
                                        <input id="fileUpload" type="file" name="cimage" />
                                        <input id="preview" type="hidden" name="cimage_base64" />
                                        <!--img id="preview"></img-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="control-label col-md-12">PIC Permintaan</label>
                                <div class="col-md-12">
                                    <input name="picx" placeholder="Piic" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label col-md-12">Deskripsi Review</label>
                                <div class="col-md-12">
                                    <textarea  style="text-transform: uppercase"  name="review" id="review" class="form-control inform" rows="4" placeholder="Tulis Deskripsi Review ..." ></textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSaveReview" onclick="saveReview()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="application/javascript" src="<?= base_url('assets/pagejs/capital/administration/it_pengajuan_processing.js') ?>"></script>
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
            $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        /* --- DOCUMENT DATE PENGAJUAN --- */
        $('#datevrealisasi').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#datevrealisasi').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#datevrealisasi').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });


    /* Utility function to convert a canvas to a BLOB */
    var dataURLToBlob = function(dataURL) {
        var BASE64_MARKER = ';base64,';
        if (dataURL.indexOf(BASE64_MARKER) == -1) {
            var parts = dataURL.split(',');
            var contentType = parts[0].split(':')[1];
            var raw = parts[1];

            return new Blob([raw], {type: contentType});
        }

        var parts = dataURL.split(BASE64_MARKER);
        var contentType = parts[0].split(':')[1];
        var raw = window.atob(parts[1]);
        var rawLength = raw.length;

        var uInt8Array = new Uint8Array(rawLength);

        for (var i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }

        return new Blob([uInt8Array], {type: contentType});
    }
    /* End Utility function to convert a canvas to a BLOB      */
    /* IMAGE UPLOAD */
    $("#fileUpload").on('change', function (e) {

        var imgPath = $(this)[0].value;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                /*RESIZE HERE*/
                if (e.target.files) {
                    //console.log('s' + e.target.files[0]);
                    let imageFile = e.target.files[0];
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var img = document.createElement("img");
                        img.onload = function (event) {
                            // Dynamically create a canvas element
                        var canvas = document.createElement("canvas"),
                            max_size = 544,// TODO : pull max size from a site config
                                width = img.width,
                                height = img.height;
                            if (width > height) {
                                if (width > max_size) {
                                    height *= max_size / width;
                                    width = max_size;
                                }
                            } else {
                                if (height > max_size) {
                                    width *= max_size / height;
                                    height = max_size;
                                }
                            }
                            canvas.width = width;
                            canvas.height = height;
                            // var canvas = document.getElementById("canvas");
                            var ctx = canvas.getContext("2d");

                            // Actual resizing

                            ctx.drawImage(img, 0, 0, width, height);
                            //ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, canvas.width, canvas.height);
                            //ctx.drawImage(img, 0, 0, 1200, 900, 0, 0, 1200, 900);

                            // Show resized image in preview element
                            var dataurl = canvas.toDataURL(imageFile.type);
                            // untuk preview
                            //document.getElementById("preview").src = dataurl;
                            var resizedImage = dataURLToBlob(dataurl);
                            $('[name="cimage_base64"]').val(dataurl);
                            //var xx = dataurl.replace(/^data:image\/(png|jpg);base64,/, "");
                            console.log("BEKANTAL" + dataurl)
                            console.log("RESIZE IMAGE" + resizedImage)
                        }
                        img.src = e.target.result;
                    }
                    reader.readAsDataURL(imageFile);
                }
                /*RESIZE HERE*/


                var image_holder = $("#image-holder");
                image_holder.empty();

                var reader = new FileReader();
                reader.onload = function (e) {
                    $("<img />", {
                        "src": e.target.result,
                        "class": "thumb-image",
                        "style": "max-width: 250px; max-height: 250px;"
                    }).appendTo(image_holder);

                }
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);

                /*TRYING RESIZE
                let imgInput = document.getElementById('fileUpload');
                imgInput.addEventListener('change', function (e) {
                    if (e.target.files) {
                        console.log('s' + e.target.files[0]);
                        let imageFile = e.target.files[0];
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var img = document.createElement("img");
                            img.onload = function (event) {
                                // Dynamically create a canvas element
                                var canvas = document.createElement("canvas");

                                // var canvas = document.getElementById("canvas");
                                var ctx = canvas.getContext("2d");

                                // Actual resizing
                                ctx.drawImage(img, 0, 0, 300, 300);

                                // Show resized image in preview element
                                var dataurl = canvas.toDataURL(imageFile.type);
                                document.getElementById("preview").src = dataurl;
                            }
                            img.src = e.target.result;
                        }
                        reader.readAsDataURL(imageFile);
                    }
                });

                TRYING RESIZE*/



            } else {
                alert("This browser does not support FileReader.");
            }
        } else {
            alert("Pls select only images");
        }
    });
</script>