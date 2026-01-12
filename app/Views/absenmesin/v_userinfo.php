<!-- include jQuery -->
<!----script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<!-- include bootstrap files -->
<!-- Latest compiled and minified CSS -->
<!----link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<!----link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<!-- Latest compiled and minified JavaScript -->
<!----script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script--->
<!--<script type="text/javascript">
    $(function() {
        xhr = new XMLHttpRequest();
        xhr.open("GET", "<?php /*echo site_url('absenmesin/absenmesin/xtest')*/?>/", true);
        xhr.onprogress = function(e) {
            alert(e.currentTarget.responseText);
        }
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                console.log("Complete = " + xhr.responseText);
            }
        }
        xhr.send();
    });
</script>-->
<script>
    $(function () {
        $('#qintil').click(function () {
            $('.myprogress').css('width', '0');
            $('.msg').text('');
            //var filename = $('#filename').val();
            var tglakhir = $('#tglakhir').val();
            var kdcabang = $('#kdcabang').val();
            var tgl = $('#tgl').val();
            if (tgl == '') {
                alert('Silahakan isi tanggal');
                return;
            }
            var formData = new FormData();
            //formData.append('myfile', $('#myfile')[0].files[0]);
            //formData.append('filename', filename);
            formData.append('tglakhir', tglakhir);
            formData.append('kdcabang', kdcabang);
            formData.append('tgl', tgl);
            $('#qintil').prop('disabled', true);
            $('#qintil').text('Memproses....!!');
            $('.myprogress').text( parseInt(2 * 10) + '%');
            $('.myprogress').css('width', parseInt(2 * 10) + '%');

            $('.msg').text('Uploading in progress...');
            $.ajax({
                url: "<?php echo site_url('absenmesin/absenmesin/tarik_userinfo')?>/",
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                // this part is progress bar
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    //Upload progress
                    /*                    xhr.upload.addEventListener("progress", function(evt){
                                            if (evt.lengthComputable) {
                                                var percentComplete = evt.loaded / evt.total;
                                                percentComplete = parseInt(percentComplete * 100);
                                                //Do something with upload progress
                                                console.log(percentComplete);
                                                $('.myprogress').text(percentComplete + '%');
                                                $('.myprogress').css('width', percentComplete + '%');
                                            }
                                        }, false);*/
                    //Download progress
                    xhr = new XMLHttpRequest();
                    //xhr.open("GET", "<!?php echo site_url('absenmesin/absenmesin/xtest')?>/", true);
                    xhr.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            //xhr.onreadystatechange = function() {
                                //if (xhr.readyState == 4) {
                                console.log("Complete = " + xhr.responseText);
                                var percentComplete = parseInt(xhr.readyState);
                                //var percentComplete = evt.loaded / evt.total;
                               // var time = parseInt(xhr.readyState);
                                var time = percentComplete;
                                var interval = setInterval(function() {
                                    if (time <= 10) {
                                        console.log("TIMER = " +time);
                                        //time ++;
                                        if (time <= 9 && xhr.status != 200 ) {
                                            console.log(parseInt(xhr.status));
                                            if (xhr.status == 500 ||  xhr.responseText != 'TRUE') {
                                                $('#pgbar').removeClass('progress-bar-success').addClass('progress-bar-danger');
                                                alert('Ada kesalahan data tidak dapat di proses!');
                                                time = 10;
                                                clearInterval(interval);
                                                $('#qintil').prop('disabled', false);
                                                $('#qintil').text('Proses');
                                            } else {
                                                console.log('Belum selesai');
                                                time = 9;
                                            }

                                        }  else {
                                            if (this.status == 200 || xhr.responseText == 'TRUE') {
                                                time = 10;
                                                console.log('sudah selesai');
                                                clearInterval(interval);
                                                $('#qintil').prop('disabled', false);
                                                $('#qintil').text('Proses');
                                            } else {
                                                if (time == 10 ) { time = 10; } else { time ++; }
                                            }

                                        }

                                        percentComplete = parseInt(time * 10);
                                        $('.myprogress').text(percentComplete + '%');
                                        $('.myprogress').css('width', percentComplete + '%');

                                    }
                                    else {
                                        clearInterval(interval);
                                    }
                                }, 1000);

                                ////percentComplete = parseInt(percentComplete * 10);
                                //////Do something with download progress
                                ////console.log(percentComplete);
                                ////console.log(evt.loaded);
                                ////console.log(evt.total);
                                ////console.log(evt.lengthComputable);
                                ////console.log(this.status);
                                ////$('.myprogress').text(percentComplete + '%');
                                ////$('.myprogress').css('width', percentComplete + '%');
                                // }
                           // }

                        }
                    }, false);
                    return xhr;
                },
                success: function (data) {
                    console.log(this.status);
                    console.log(data);
                }
            });
        });
    });
</script>


<legend><?php echo $title;?></legend>
<?php echo $message;?>
<div class="row">
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">

            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <!--form action="<!?php echo site_url('trans/absensi/index');?>" name="form" role="form" method="post"-->
                    <form id="myform" method="post">

                        <!--area-->
                        <div class="form-group">
                            <label class="col-lg-3">Tanggal Tarikan Terakhir</label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <input type="input" id="tglakhir" name="tglakhir"  class="form-control pull-right" readonly>
                                </div><!-- /.input group -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3">Pilih Wilayah</label>
                            <div class="col-lg-9">
                                <select id="kdcabang" name="kdcabang" required>
                                    <option value="">--Pilih Wilayah--</option>
                                    <?php foreach ($list_kanwil as $ld){ ?>
                                        <option value="<?php echo trim($ld->kdcabang);?>"><?php echo $ld->kdcabang.' || '.$ld->desc_cabang;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>


                        <script type="text/javascript" charset="utf-8">
                            $(function() {
                                $('#qintil').prop('disabled', true);
                                $('#kdcabang').change(function(){
                                    $('#qintil').prop('disabled', true);
                                    $('#qintil').text('Memproses....!!');
                                    $('.myprogress').css('width', '0');
                                    var cabang=$(this).val();

                                    $.ajax({
                                        url : "<?php echo site_url('absenmesin/absenmesin/ajax_tglakhir_ci')?>/" + cabang,
                                        type: "GET",
                                        dataType: "JSON",
                                        success: function(data)
                                        {
                                            $('[name="tglakhir"]').val(data.lastdate);
                                            $('#qintil').prop('disabled', false);
                                            $('#qintil').text('Proses');
                                        },
                                        error: function (jqXHR, textStatus, errorThrown)
                                        {
                                            alert('Error get data from ajax');
                                        }
                                    });


                                });

                            });

                        </script>


                        <?php if($akses['aksesconvert']=='t'){?>
                            <div class="form-group">
                                <label class="col-lg-3">Tanggal</label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" id="tgl" name="tgl"  class="form-control pull-right">
                                    </div><!-- /.input group -->
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-3">

                                    <button type='button' id='qintil' class='btn btn-primary' > Proses </button>
                                    <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->

                                </div>
                                <!--<div class="form-group">-->
                                <div class="col-lg-9">
                                    <div id = "pgbar" class=" progress progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
                                </div>

                                    <!--<div class="msg"></div>-->
                                <!--</div>-->
                            </div>
                        <?php } else { echo 'anda tidak diperkenankan mengakses modul ini!!!!';} ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>




    //Date range picker
    $('#tgl').daterangepicker();
    $('#kdcabang').selectize();



</script>