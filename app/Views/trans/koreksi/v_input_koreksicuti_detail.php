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

?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
				$(".datePick").datepicker();

                // $(".tglrangetime").datetimepicker({
                //     format: 'DD-MM-YYYY H:mm',
                // }).on('dp.change', function(e) {
                //     // Revalidate the date field
                //     //console.log('DD-MM-YYYY' + 'X');
                //     $('#formBeritaAcara').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
                // });
                // $(".tglrangetime").daterangepicker({
                //     timePicker: true,
                //     singleDatePicker: true,
                //     showDropdowns: true,
                //     minYear: 1901,
                //     maxYear: parseInt(moment().format('YYYY'), 10),
                //     locale: {
                //         format: 'DD-MM-YYYY H:mm'
                //     }
                // });

                $(".dateRangePick").daterangepicker({
                    //singleDatePicker: true,
                    //showDropdowns: true
                },function () {
                    //console.log('TEST');
                    $('#formMutasiPromosi').bootstrapValidator('validateField', 'startdate');
                });
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
            <div class="col-sm-12">
                <a href="<?php echo site_url("trans/koreksi/clearEntry_Koreksi_Cuti")?>" class="btn btn-default dropdown-toggle " style="margin:0px; color:#000000;" type="button"><i class="fa fa-arrow-left"></i> Kembali </a>
                <?php if (trim($dtl['status'])==='') { ?>
                    <button class="btn btn-success dropdown-toggle pull-right" style="margin:0px; color:#ffffff;" type="button" id="btnSave" onclick="save();"><i class="fa fa-check"></i> Simpan Master </button>
                <?php } else { ?>
                    <button class="btn btn-primary dropdown-toggle pull-right" style="margin:0px; color:#ffffff;" type="button" id="btnSave" onclick="finishInput();"><i class="fa fa-save"></i> Simpan Final </button>
                <?php } ?>
            </div>
    </div><!-- /.box-header -->
    <div class="box-body" >
        <form id="form" action="<?php echo site_url('#')?>" method="post" enctype="multipart/form-data" role="form">
        <div class="col-lg-12">
            <input type="hidden" class="form-control" name="docno" value="<?php echo trim($docno) ;?>">
            <input type="hidden" class="form-control" name="type" value="<?php echo trim($type) ;?>">
            <input type="hidden" class="form-control" name="status" value="<?php echo trim($dtl['status']);?>">
        </div>
        <div class="col-lg-4">
            <div class="form-group">
               <label>Pilih Penambahan / Pengurangan</label>
               <div class="input-group">
                   <div class="input-group-addon">
                       <i class="fa fa-circle-o"></i>
                   </div>
                   <select class="form-control" name="doctype" id="doctype">
                        <option value="PLUS">+ PENAMBAHAN</option>
                        <option value="MIN">- PENGURANGAN</option>
                   </select>
               </div>
               <!-- /.input group -->
           </div>
            <div class="form-group">
                <label>Jumlah Penyesuaian</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-check"></i>
                    </div>
                    <input type="text" class="form-control" name="qty" id="qty" style="text-transform: uppercase;" placeholder="Jumlah">
                </div>
                <!-- /.input group -->
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label>Tanggal Transaksi</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control datepicker inform" name="docdate" id="docdate" data-date-format="dd-mm-yyyy" placeholder="Tanggal Transaksi" >
                </div>
                <!-- /.input group -->
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label>Dokumen Referensi</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-check"></i>
                    </div>
                    <input type="text" class="form-control" name="docref" id="docref" style="text-transform: uppercase;" placeholder="Dokumen Referensi (Optional)">
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Deskripsi</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-pencil"></i>
                    </div>
                    <textarea class="form-control" name="description" id="description" placeholder="Deskripsi & Keterangan" style="text-transform:uppercase" ></textarea>
                </div>
                <!-- /.input group -->
            </div>
        </div>
        </form>
    </div><!-- /.box-body -->
    <div class="box-footer">
        <div class="col-sm-12">
            <?php if (trim($dtl['status'])!=='') { ?>
            <div  class="box-body">
                <form action="#" id="formInductionDetail" class="form-horizontal">
                    <input type="hidden" value="INPUTDETAIL" name="type"/>
                    <div class="form-body">
                        <div class="form-group loccode_destination">
                           <label>PILIH NIK </label>
                           <div class="input-group">
                               <div class="input-group-addon">
                                   <i class="fa fa-circle-o"></i>
                               </div>
                               <select class="form-control" name="nik" id="nik">
                               </select>
                           </div>
                           <!-- /.input group -->
                       </div>
                    </div>
                </form>
            </div>
            <?php } ?>
        </div>
        <div class="box-body table-responsive" style='overflow-x:scroll;'>
            <table id="t_nikdtl" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th width="1%" >No.</th>
                    <th width="5%">Action</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Departement</th>
                    <th>Position</th>
                    <th>Section</th>
                    <th>Doctype</th>
                    <th>Jumlah</th>
                    <th>Deskripsi</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div>
</div><!-- /.box -->

<!-- MODAL ADD DETAIL INDUKSI -->
<div class="modal fade" id="modalItemTransDtl" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Detail Karyawan</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="formItemTransDtl" class="form-horizontal">
                    <input type="hidden" value="EDITDETAIL" name="type"/>
                    <input type="hidden" name="docno"/>
                    <input type="hidden" name="stockcode"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">ID</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control inform" name="id" placeholder="ID"/>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nik</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control inform" name="nik" placeholder="Nik"/>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control inform" name="nmlengkap" placeholder="Nama"/>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Penambahan/Pengurangan</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control inform" name="doctypedetail" placeholder="Type"/>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Jumlah</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control ratakanan inform" name="qtydetail" placeholder="Jumlah"/>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Department</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control inform" name="nmdept" placeholder="Department Pemohon" style="text-transform: uppercase;"/>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Position</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control inform" name="nmjabatan" placeholder="Position"  style="text-transform: uppercase;"/>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Description</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control inform" name="descriptiondetail" placeholder="Write Description Detail"  style="text-transform: uppercase;"/>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSaveDtl" onclick="save_itemtrans_dtl()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript" src="<?= base_url('assets/pagejs/trans/koreksi.js') ?>"></script>
