<?php
$this->session = \Config\Services::session();
$this->fiky_encryption = new \App\Libraries\Fiky_encryption();
?>
<style>
    a .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    a .card:hover {
        transform: scale(1.05); /* sedikit membesar */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* bayangan lebih tegas */
        cursor: pointer;
    }

</style>
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">Dashboard IT</h4>
    </div>
    <div class="col-md-7 align-self-center text-end">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Dashboard IT</li>
            </ol>
            <!--button type="button" class="btn btn-info d-none d-lg-block m-l-15 text-white"><i class="fa fa-plus-circle"></i> Create New</button-->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-sm-6">
                <a href="<?= base_url('form/pa'); ?>" style="text-decoration:none; color:inherit;">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Form Perangkat Pribadi</h5>
                            <div class="d-flex no-block align-items-center m-t-20 m-b-20">
                                <div id="sparklinedash2"></div>
                                <div class="ms-auto">
                                    <h2 class="text-purple"><i class="ti-arrow-up"></i> <span class="counter"><?= $totalformppcount; ?></span></h2>
                                </div>
                            </div>
                        </div>
                        <div id="sparkline8" class="sparkchart"></div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6">
                <a href="<?= base_url('form/pa/internet'); ?>" style="text-decoration:none; color:inherit;">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Form Izin System & Jaringan</h5>
                            <div class="d-flex no-block align-items-center m-t-20 m-b-20">
                                <div id="sparklinedash2"></div>
                                <div class="ms-auto">
                                    <h2 class="text-green"><i class="ti-arrow-up"></i> <span class="counter"><?= $totalformiicount; ?></span></h2>
                                </div>
                            </div>
                        </div>
                        <div id="sparkline8" class="sparkchart"></div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6">
                <a href="<?= base_url('form/pa/laptop'); ?>" style="text-decoration:none; color:inherit;">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Form Perangkat Keluar</h5>
                            <div class="d-flex no-block align-items-center m-t-20 m-b-20">
                                <div id="sparklinedash2"></div>
                                <div class="ms-auto">
                                    <h2 class="text-blue"><i class="ti-arrow-up"></i> <span class="counter"><?= $totalformpkcount; ?></span></h2>
                                </div>
                            </div>
                        </div>
                        <div id="sparkline8" class="sparkchart"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-dark">
                    <div class="card-header">
                        <a href="<?php echo base_url('capital/administration/jasa_pembayaran')?>" class="btn btn-info" style="margin:0px" target=”_blank”><i class="fa fa-print"></i> Click Untuk Update Jasa Pembayaran -25 Hari</a>
                        <button class="btn btn-tool" type="button" data-bs-toggle="collapse" data-bs-target="#tablePembayaran" aria-expanded="false" aria-controls="tablePembayaran">
                        <i class="fa fa-chevron-circle-down"></i>
                        </button>
                    </div>
                    <div id="tablePembayaran" class="card-body collapse" style='overflow-x:scroll;'>
                        <table id="jasapembayaran" class="table table-bordered table-striped" >
                            <thead>
                            <tr>
                                <th>Jenis Pembayaran</th>
                                <th>Nama Supplier</th>
                                <th>Nama Pembayaran </th>
                                <th>Jatuh Tempo</th>
                                <th class="text-center">Day Remind</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.card-body -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="card card-dark">
                    <div class="card-header">
                        <a href="#" class="btn btn-info" style="margin:0px" target=”_blank”><i class="fa fa-print"></i> Update Minimum Stock Barang</a>
                        <button class="btn btn-tool" type="button" data-bs-toggle="collapse" data-bs-target="#tableMinimum" aria-expanded="false" aria-controls="tableMinimum">
                            <i class="fa fa-chevron-circle-down"></i>
                        </button>
                    </div>
                    <div  id="tableMinimum"  class="card-body collapse" style='overflow-x:scroll;'>
                        <table id="!!" class="table table-bordered table-striped" >
                            <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Batchr</th>
                                <th>XX</th>
                                <th>P-Minimum</th>
                                <th>Stock </th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.card-body -->
                </div>
            </div>
            <div class="col-md-12">
                <!-- <a href="<?= base_url('form/pa/internet'); ?>" style="text-decoration:none; color:inherit;"> -->
            <div class="card card-primary">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="card-title m-0">Reminder H-7 Form Membawa Perangkat Pribadi</h3>
                        <h5 class="m-0 ms-3">(<?= $formppcount ?> Data)</h5>
                        <button class="btn btn-tool ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#tableFormpp" aria-expanded="false" aria-controls="tableFormpp">
                            <i class="fa fa-chevron-circle-down"></i>
                        </button>
                    </div>
                    <div id="tableFormpp" class="collapse">
                        <div class="card-body" style="overflow-x:scroll;">
                            <table id="formpp" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>No. Dokumen</th>
                                        <th>Form Berakhir</th>
                                        <th>Due Date</th>
                                        <th>Nama Pemohon</th>
                                        <th>Status</th>
                                        <th>Jenis Perangkat</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <!-- </a> -->
        </div>

        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title m-0">Reminder H-7 Form Izin System & Jaringan</h3>
                    <h5 class="m-0 ms-3">(<?= $formiicount ?> Data)</h5>
                    <button class="btn btn-tool ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#tableFormii" aria-expanded="false" aria-controls="tableFormii">
                        <i class="fa fa-chevron-circle-down"></i>
                    </button>
                </div>
                <div id="tableFormii" class="collapse">
                    <div class="card-body" style="overflow-x:scroll;">
                        <table id="formii" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Dokumen</th>
                                    <th>Form Berakhir</th>
                                    <th>Due Date</th>
                                    <th>Nama Pemohon</th>
                                    <th>Status</th>
                                    <th>Jenis Akses</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title m-0">Reminder H-7 Form Izin Perangkat IT Keluar</h3>
                    <h5 class="m-0 ms-3">(<?= $formpkcount ?> Data)</h5>
                    <button class="btn btn-tool ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#tableFormpk" aria-expanded="false" aria-controls="tableFormpk">
                        <i class="fa fa-chevron-circle-down"></i>
                    </button>
                </div>
                <div id="tableFormpk" class="collapse">
                    <div class="card-body" style="overflow-x:scroll;">
                        <table id="formpk" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. Dokumen</th>
                                    <th>Form Berakhir</th>
                                    <th>Due Date</th>
                                    <th>Nama Pemohon</th>
                                    <th>Status</th>
                                    <th>Jenis Form</th>
                                    <th>Jenis Perangkat</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    
</div>

<!-- ============================================================== -->
<!-- End Review -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Comment - chats -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- End Comment - chats -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Right sidebar -->
<!-- ============================================================== -->
<!-- .right-sidebar -->
<div class="right-sidebar">
    <div class="slimscrollright">
        <div class="rpanel-title"> Service Panel <span><i class="ti-close right-side-toggle"></i></span> </div>
        <div class="r-panel-body">
            <ul id="themecolors" class="m-t-20">
                <li><b>With Light sidebar</b></li>
                <li><a href="javascript:void(0)" data-skin="skin-default" class="default-theme">1</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-green" class="green-theme">2</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-red" class="red-theme">3</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-blue" class="blue-theme">4</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-purple" class="purple-theme">5</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-megna" class="megna-theme working">6</a></li>
                <li class="d-block m-t-30"><b>With Dark sidebar</b></li>
                <li><a href="javascript:void(0)" data-skin="skin-default-dark" class="default-dark-theme ">7</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-green-dark" class="green-dark-theme">8</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-red-dark" class="red-dark-theme">9</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-blue-dark" class="blue-dark-theme">10</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-purple-dark" class="purple-dark-theme">11</a></li>
                <li><a href="javascript:void(0)" data-skin="skin-megna-dark" class="megna-dark-theme ">12</a></li>
            </ul>
            <ul class="m-t-20 chatonline">
                <li><b>Chat option</b></li>
                <li>
                    <a href="javascript:void(0)"><img src="<?php echo base_url('assets/ea/package/images/users/1.jpg') ?>" alt="user-img" class="img-circle"> <span>Varun Dhavan <small class="text-success">online</small></span></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><img src="<?php echo base_url('assets/ea/package/images/users/2.jpg') ?>" alt="user-img" class="img-circle"> <span>Genelia Deshmukh <small class="text-warning">Away</small></span></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><img src="<?php echo base_url('assets/ea/package/images/users/3.jpg') ?>" alt="user-img" class="img-circle"> <span>Ritesh Deshmukh <small class="text-danger">Busy</small></span></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><img src="<?php echo base_url('assets/ea/package/images/users/4.jpg') ?>" alt="user-img" class="img-circle"> <span>Arijit Sinh <small class="text-muted">Offline</small></span></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><img src="<?php echo base_url('assets/ea/package/images/users/5.jpg') ?>" alt="user-img" class="img-circle"> <span>Govinda Star <small class="text-success">online</small></span></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><img src="<?php echo base_url('assets/ea/package/images/users/6.jpg') ?>" alt="user-img" class="img-circle"> <span>John Abraham<small class="text-success">online</small></span></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><img src="<?php echo base_url('assets/ea/package/images/users/7.jpg') ?>" alt="user-img" class="img-circle"> <span>Hritik Roshan<small class="text-success">online</small></span></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><img src="<?php echo base_url('assets/ea/package/images/users/8.jpg') ?>" alt="user-img" class="img-circle"> <span>Pwandeep rajan <small class="text-success">online</small></span></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Right sidebar -->
<!-- ============================================================== -->
<!-- /.content -->
<?php if ($roleid === 'BM') { ?>
    <!-- INISIALISASI TOMBOL BARANG MASUK JS-->

<?php } else if ($roleid === 'BK') { ?>
    <!-- INISIALISASI TOMBOL BARANG KELUAR JS-->

<?php } else { ?>
    <script type="application/javascript" src="<?= base_url('assets/pagejs/dashboard/dashboard.js') ?>"></script>
<?php } ?>
