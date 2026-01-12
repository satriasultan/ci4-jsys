<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Control | Log in</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo-depan/jts.ico'); ?>">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/adminlte.min.css') ?>">
</head>
<body class="hold-transition lockscreen">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper">
    <div class="lockscreen-logo">
        <a href="#"><b>Lock Screen</b></a>
    </div>
    <!-- User name -->
    <div class="lockscreen-name">Fill Your Renewal License</div>

    <!-- general form elements -->
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Software Developer System</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form class="form-inline" method="post" action="#" enctype="multipart/form-data" >
            <div class="card-body">
                <div class="form-group">
                    <label for="exampleInputFile">Form Renewal</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="media">
                            <label class="custom-file-label" for="media">Choose file</label>
                        </div>
                        <div class="input-group-append">
                            <span class="input-group-text">Upload</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <br>
            <p id="loaded_n_total"></p>
            <div class="progress" style="display:none">

                <div id="progressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    <span class="sr-only">0%</span>
                </div>

            </div>
            <div class="msg alert alert-info text-left" style="display:none">
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary float-right">Submit</button>
            </div>
        </form>
    </div>
    <!-- /.card -->
    <div class="help-block text-center">
        Please contact your developer software
    </div>
    <div class="lockscreen-footer text-center">
        <div class="float-right d-none d-sm-inline">
            <?php echo "You're coming from : ".$ip; ?>
        </div>
        Copyright IT - Jatim Taman Steel &copy; 2022-2023 <b><a href="#" class="text-black"></a></b><br>
        All rights reserved
    </div>
</div>

<div class="modal fade" id="loadMe" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="loader"></div>
                <div clas="loader-txt">
                    <h4><p class="saving"><span>Mohon </span><span> Tunggu</span></p></h4>
                    <h5>
                        <p class="saving">Sedang Melakukan Proses  <span>*</span><span>*</span><span>*</span></p>
                    </h5>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.center -->

<!-- jQuery -->
<script src="<?php echo base_url('plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script type="text/javascript">
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
    document.onkeydown = function(e) {
        if(event.keyCode == 123) {
            return false;
        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
            return false;
        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
            return false;
        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
            return false;
        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'F'.charCodeAt(0)) {
            return false;
        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'V'.charCodeAt(0)) {
            return false;
        }
        if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
            return false;
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        function _(el) {
            return document.getElementById(el);
        }
        function progressHandler(event) {
            _("loaded_n_total").innerHTML = "Uploaded " + event.loaded + " bytes of " + event.total;
        }

        $('form').on('submit', function(event){
            event.preventDefault();
            //var formData = new FormData($('form')[0]);
            //var file = _("media").files[0];
            var file = $('#media')[0].files[0];
            var formdata = new FormData();
            formdata.append("media", file);

            $('.msg').hide();
            $('.progress').show();

            var filename = $("#media").val();
            var extension = filename.replace(/^.*\./,".");
            if (extension === filename) {
                extension = '.';
            } else {
                extension = extension.toLowerCase();
            }

            if (extension!==".zip"){
                alert("Extensi Tidak Sesuai , Harap melampirkan extensi yang sesuai !!!");
            } else {
                //progresnya
                $.ajax({
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (e) {
                            if (e.lengthComputable) {
                                console.log('Bytes Loaded : ' + e.loaded);
                                console.log('Total Size : ' + e.total);
                                console.log('Persen : ' + (e.loaded / e.total));

                                var percent = Math.round((e.loaded / e.total) * 100);

                                $('#progressBar').attr('aria-valuenow', percent).css('width', percent + '%').text(percent + '%');
                                $('#loaded_n_total').text("Uploaded " + e.loaded + " bytes of " + e.total);
                            }
                        });
                        return xhr;
                    },

                    type: 'POST',
                    url: "<?php echo site_url('lmd/post_lc'); ?>",
                    data: formdata,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('form')[0].reset();
                        $('.progress').hide();
                        $('.msg').show();
                        console.log(response);
                        if (response == "") {
                            console.log(formdata.status);
                            alert(formdata.show);
                        } else {
                            if(response.status==='true'){
                                $("#loadMe").modal({
                                    backdrop: "static", //remove ability to close modal with click
                                    keyboard: false, //remove option to close with keyboard
                                    show: true //Display loader!
                                });
                                //ajax progress here progress replicate data

                                $.ajax({
                                    type: 'POST',
                                    url: "<?php echo site_url('lmd/rest_lc'); ?>",
                                    dataType: "json",
                                    async:false,
                                    success: function (data) {
                                        if (data.status === 'true') {
                                            console.log(data.status);
                                            $("#loadMe").modal("hide");
                                            var msg = 'File berhasil di upload. ID file = ' + response.show;
                                            $('.msg').html(msg);
                                            window.location.replace("<?php echo site_url('lmd/show_success'); ?>");
                                        } else {
                                            //end ajax here
                                            console.log(data.show);
                                            $("#loadMe").modal("hide");
                                            var msg = 'File Dan Data Gagal Di Proses!!!, Pastikan Data Benar!!';
                                            $('.msg').html(msg);
                                        }
                                    } ,
                                    error: function (textStatus, errorThrown) {
                                        $("#loadMe").modal("hide");
                                        var msg = 'File Dan Data Gagal Di Proses!!!, Pastikan Data Benar!!';
                                        $('.msg').html(msg);
                                    }
                                });
                                // console.log(response.status);
                                // var msg = 'File berhasil di upload. ID file = ' + response.show;
                                // $('.msg').html(msg);
                                //end ajax here

                            } else {
                                var msg = 'File Gagal Di Upload';
                                $('.msg').html(msg);
                            }

                        }
                    }
                });
            }
        });
    });

    $(function() {
        $(document).on('change', ':file', function() {
            var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);
        });

        $(document).ready( function() {
            $(':file').on('fileselect', function(event, numFiles, label) {

                var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;

                if( input.length ) {
                    input.val(log);
                } else {
                    if( log ) alert(log);
                }

            });
        });

    });


</script>
</body>
</html>
