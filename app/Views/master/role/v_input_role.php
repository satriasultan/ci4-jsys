<?php
/**
 * *
 *  * Created by PhpStorm.
 *  *  * User: FIKY-PC
 *  *  * Date: 4/29/19 1:34 PM
 *  *  * Last Modified: 12/18/16 10:51 AM.
 *  *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  *  Copyright© 2019 .All rights reserved.
 *  *
 *
 */

/**
 * Created by PhpStorm.
 * User: FIKY-PC
 * Date: 13/04/2019
 * Time: 10:26
 */
?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
				$(".datePick").datepicker();

                $(".tglrangetime").datetimepicker({
                    format: 'DD-MM-YYYY H:mm',
                }).on('dp.change', function(e) {
                    // Revalidate the date field
                    //console.log('DD-MM-YYYY' + 'X');
                    $('#formBeritaAcara').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
                });
                /*$(".tglrangetime").daterangepicker({
                    timePicker: true,
                    singleDatePicker: true,
                    showDropdowns: true,
                    minYear: 1901,
                    maxYear: parseInt(moment().format('YYYY'), 10),
                    locale: {
                        format: 'DD-MM-YYYY H:mm'
                    }
                });*/
            });
</script>


<ol class="breadcrumb">
    <div class="pull-right"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
    <?php foreach ($y as $y1) { ?>
        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
            <li><a href="<?php echo site_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
        <?php } else { ?>
            <li class="active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
        <?php } ?>
    <?php } ?>
</ol>
<!--?php echo $message;?-->


<div class="box">
    <div class="box-header">

    </div><!-- /.box-header -->
    <div class="box-body" >
        <div class="col-lg-12">
            <h3 ALIGN="center"> INPUT BERITA ACARA</h3>
        </div>
        <form action="<?php echo site_url('#')?>" method="post" id="formBeritaAcara" enctype="multipart/form-data" role="form">
        <div class="col-lg-12">
                <input type="hidden" class="form-control" name="type" value="INPUT">
                <div class="form-group">
                    <label class="label-form col-sm-2">PENANGGUNG JAWAB</label>
                    <?php /*<input type="hidden" value="<?php if (isset($dtl['kdregu'])){ echo trim($dtl['kdregu']); } else { echo ''; } ?>" id="kdregu" name="kdregu" style="text-transform:uppercase" maxlength="200" class="form-control" readonly required />
                <input type="hidden" value="<?php echo $blnx;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
                <input type="hidden" value="<?php echo $thnx;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly /> */ ?>
                    <div class="col-md-9">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-user-plus"></i>
                            </div>
                            <select class="select2_kary form-control col-sm-12" name="nik" id="nik">
                            </select>
                        </div>
                    </div>
                    <!--<div class="col-md-1">
                        <button type="submit" class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i> Load </button>
                    </div>-->
                </div>

        </div>

        <div class="col-lg-4">
            <!-- Date dd/mm/yyyy -->
            <div class="form-group">
                <label>Departement</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="department" placeholder="Department Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
            <!-- Date dd/mm/yyyy -->
            <div class="form-group">
                <label>Jabatan</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="jabatan" placeholder="Jabatan Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>

            <div class="form-group">
                <label>Atasan 1</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="atasan1" placeholder="Atasan 1 Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
            <div class="form-group">
                <label>Atasan 2</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="atasan2" placeholder="Atasan 2 Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="alamattinggal" placeholder="Alamat Domisili" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Contact</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="nohp1" placeholder="Contact & Ponsel" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Contact</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="email" placeholder="Email / Surel" disabled>
                </div>
                <!-- /.input group -->
            </div>
        </div>
        <div class="col-lg-4">
            <?php /*
            <div class="form-group">
                <label>Tanggal Awal s/d Tanggal Selesai</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control dateRangePick zz" name="startdate" id="startdate" data-date-format="dd-mm-yyyy" placeholder="Tanggal Periode" >
                </div>
                <!-- /.input group -->
            </div>
 */ ?>
            <?php /*
            <div class="form-group">
                <label>Tanggal Akhir</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control datePick zz" name="enddate" id="enddate" data-date-format="dd-mm-yyyy" placeholder="Tanggal Akhir" >
                </div>
                <!-- /.input group -->
            </div> */ ?>
            <div class="form-group">
                <label>Tanggal & Jam Kejadian</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control tglrangetime zz" name="docdate" id="docdate" placeholder="Tanggal Periode" >
                </div>
                <!-- /.input group -->
            </div>

            <div class="form-group">
                <label>Laporan Kejadian</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-pencil"></i>
                    </div>
                    <textarea class="form-control zz" name="laporan" id="laporan" placeholder="Laporan Kejadian" style="text-transform:uppercase" ></textarea>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Lokasi Kejadian</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-pencil"></i>
                    </div>
                    <textarea class="form-control zz" name="lokasi" id="lokasi" placeholder="Lokasi Kejadian" style="text-transform:uppercase" ></textarea>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Uraian Kejadian</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-pencil"></i>
                    </div>
                    <textarea class="form-control zz" name="uraian" id="uraian" placeholder="Uraian Kejadian" style="text-transform:uppercase" ></textarea>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Solusi / Perbaikan</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-pencil"></i>
                    </div>
                    <textarea class="form-control zz" name="solusi" id="solusi" placeholder="Solusi / Perbaikan" style="text-transform:uppercase" ></textarea>
                </div>
                <!-- /.input group -->
            </div>
        </div>
        <div class="col-lg-4">
            <!-- /.form group -->
            <div class="form-group">
                <label>Saksi 1</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <select class="form-control col-sm-12 zz" name="saksi1" id="saksi1" placeholder="Saksi 1 Karyawan" >
                    </select>
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
            <div class="form-group">
                <label>Saksi 2</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <select class="form-control col-sm-12 zz" name="saksi2" id="saksi2" placeholder="Saksi 2 Karyawan" >
                    </select>
                </div>
                <!-- /.input group -->
            </div>

        </div>
        </form>
    </div><!-- /.box-body -->

    <div class="box-footer">
        <div class="col-sm-12">
            <a href="<?php echo site_url('trans/sberitaacara/clearEntry') ?>" class="btn btn-default dropdown-toggle " style="margin:0px; color:#000000;" type="button"><i class="fa fa-arrow-left"></i> Kembali </a>
            <button class="btn btn-success dropdown-toggle pull-right" style="margin:0px; color:#ffffff;" type="button" id="btnSave" onclick="finishInput();"> Proses <i class="fa fa-arrow-right"></i></button>
        </div>
    </div>
</div><!-- /.box -->






<script type="application/javascript">
    $(document).ready(function () {
//read editable/input
        $.ajax({
            type: 'GET',
            url: HOST_URL + 'trans/sberitaacara/showOnRead' + '',
            dataType: 'json',
            dataFilter: function(data) {
                var json = jQuery.parseJSON(data);
                json.status = json.dataTables.status;
                json.total_count = json.dataTables.total_count;
                json.items = json.dataTables.items;
                json.incomplete_results = json.dataTables.incomplete_results;

                //console.log("On Ready" + json.dataTables.items[0].nik);
                $('[name="docdate"]').val(json.dataTables.items[0].docdate2);
                $('[name="laporan"]').val(json.dataTables.items[0].laporan);
                $('[name="lokasi"]').val(json.dataTables.items[0].lokasi);
                $('[name="uraian"]').val(json.dataTables.items[0].uraian);
                $('[name="solusi"]').val(json.dataTables.items[0].solusi);
                // Fetch the preselected item, and add to the control
                var select2_kary = $('.select2_kary');
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].nik,
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {
                    // create the option and append to Select2
                    //console.log(datax.items[0].nik + 'KONTOL');
                    var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                    select2_kary.append(option).trigger('change');

                    // manually trigger the `select2:select` event
                    select2_kary.trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });

                var saksi1 = $('#saksi1');
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].saksi1,
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {

                    var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                    saksi1.append(option).trigger('change');

                    // manually trigger the `select2:select` event
                    saksi1.trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });

                var saksi2 = $('#saksi2');
                $.ajax({
                    type: 'GET',
                    url: HOST_URL + 'api/globalmodule/list_karyawan_by_id' + '?var=' + json.dataTables.items[0].saksi2,
                    dataType: 'json',
                    delay: 250,
                }).then(function (datax) {

                    var option = new Option(datax.items[0].nmlengkap, datax.items[0].nik, true, true);
                    saksi2.append(option).trigger('change');

                    // manually trigger the `select2:select` event
                    saksi2.trigger({
                        type: 'select2:select',
                        params: {
                            data: datax
                        }
                    });
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log("Failed To Loading Data");
            }
        });

    });
</script>


































<script type="text/javascript" src="<?= base_url('assets/pagejs/trans/sberitaacara.js') ?>"></script>
