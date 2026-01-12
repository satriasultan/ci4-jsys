
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verified Document</title>
    <meta content='width=device-width, initial-scale=1, user-scalable=yes' name='viewport'>

    <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo-depan/jts-icon.png') ?>">
    <?php echo $_ini_stylenya;?>
</head>
<body class="hold-transition layout-top-nav">

<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container">
            <a href="" class="navbar-brand">
                <img src="<?php echo base_url('assets/img/logo-depan/jts-icon.png'); ?>" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">PT Jatim Taman Steel Mfg.</span>
            </a>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="#" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Contact</a>
                    </li>

                </ul>

                <!-- SEARCH FORM -->
                <form class="form-inline ml-0 ml-md-3">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>


        </div>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"> Document Verification <small>1.1</small></h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Layout</a></li>
                            <li class="breadcrumb-item active">Top Navigation</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <!-- /.col-md-12 -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title m-0">Document Verification Data</h5>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title"><b>Jenis Dokumen</b></h6>
                                <p class="card-text"><h7><?= $dtl['nmkriteria'] ?></h7></p>
                                <h6 class="card-title"><b>No Dokumen</b></h6>
                                <p class="card-text"><h7><?= $dtl['docno'] ?></h7></p>
                                <h6 class="card-title"><b>Nama Dokumen</b></h6>
                                <p class="card-text"><h7> <?= $dtl['docname'] ?></h7></p>
                                <h6 class="card-title"><b>Tanggal Dokumen</b></h6>
                                <p class="card-text"><h7> <?= $dtl['docdate1'] ?></h7></p>
                                <h6 class="card-title"><b>Keterangan</b></h6>
                                <p class="card-text"><h7> <?= $dtl['description'] ?></h7></p>
                                <h6 class="card-title"><b>Disetujui Tanggal</b></h6>
                                <p class="card-text"><h7><?= $dtl['approvaldate1'] ?></h7></p>
                                <h6 class="card-title"><b>Disetujui Oleh</b></h6>
                                <p class="card-text"><h7><?= $dtl['approvalby'] ?></h7></p>
                                <a href="<?= base_url().'/assets/files/imgbarcode/attfile/'.$dtl['attname'] ?>" class="btn btn-primary float-right"><i class="fa-circle-o"></i> Open Document</a>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-md-6 -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline">

        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; 2021-2022 <a href="#">IT Jatim Taman Steel</a>.</strong> All rights reserved.
    </footer>
</div>

<?php echo $_ini_jsnya;?>
</body>
</html>
