<?php
use App\Libraries\Fiky_encryption;
$this->session = \Config\Services::session();
?>

<style>
    .top-navbar{
        height:64px;
        padding:0 24px;
    }
    .navbar-header{
        width:auto;
        display:flex;
        align-items:center;
        padding-right:24px;
    }
    .navbar-brand{display:flex;align-items:center;gap:14px;}
    .navbar-brand img{width:48px;height:48px;}

    .module-dropdown{
        width:900px;
        border:0;
        border-radius:18px;
        overflow:hidden;
        margin-top:12px;
        box-shadow:0 20px 60px rgba(0,0,0,.18);
    }
    .module-header{
        background:linear-gradient(135deg,#0d47a1,#1976d2);
        color:#fff;
        padding:18px 24px;
        display:flex;
        justify-content:space-between;
        align-items:center;
    }
    .module-version{
        background:rgba(255,255,255,.15);
        padding:6px 12px;
        border-radius:20px;
    }
    .module-body{padding:20px;}
    .module-column{border-right:1px solid #eee;}
    .module-column:last-child{border-right:0;}
    .module-category{
        font-size:11px;
        font-weight:700;
        letter-spacing:1px;
        color:#888;
        margin-bottom:15px;
    }
    .module-item{
        display:flex;
        gap:14px;
        align-items:center;
        padding:12px;
        border-radius:12px;
        text-decoration:none;
        color:#333;
        transition:.25s;
    }
    .module-item:hover{
        background:#f5f9ff;
        transform:translateX(4px);
    }
    .module-icon{
        width:50px;height:50px;border-radius:14px;
        display:flex;align-items:center;justify-content:center;
        color:#fff;font-size:20px;
    }
    .bg-accounting{background:#1565C0;}
    .bg-production{background:#EF6C00;}
    .bg-warehouse{background:#00897B;}
    .bg-purchasing{background:#43A047;}
    .bg-sales{background:#C62828;}
    .bg-hr{background:#8E24AA;}
    .bg-dcms{background:#5E35B1;}
    .bg-admin{background:#455A64;}
    .module-title{font-weight:600;}
    .module-desc{font-size:12px;color:#777;}
</style>


<!-- Navbar -->
<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <div class="navbar-header">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url('dashboard/menu') ?>">

                <!-- LOGO -->
                <img src="<?= base_url('assets/img/logo-depan/jts.ico') ?>"
                     style="width: 52px; margin-right: 32px;"
                     alt="logo" />

                <!-- TEXT -->
                <div style="line-height: 1.1;">
                    <div style="font-size: 32px; font-weight: 800; color:#ffffff; letter-spacing:1px;">
                        J-SYS
                    </div>
                    <div style="font-size: 18px; color:rgba(255,255,255,0.75); letter-spacing:1.5px;">
                        J-ACCOUNTING
                    </div>
                </div>

            </a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav me-auto">
                <!-- This is  -->
                <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                <li class="nav-item"> <a class="nav-link sidebartoggler d-none waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a> </li>

                <!-- ============================================================== -->
                <!-- Search -->
                <!-- ============================================================== -->
                <!--<li class="nav-item">
                    <form class="app-search d-none d-md-block d-lg-block">
                        <input type="text" class="form-control" placeholder="Search & enter">
                    </form>
                </li>-->
                <li class="nav-item">
                    <form class="app-search d-none d-md-block d-lg-block">

 <!--                   <a href="<?php /*= base_url('assets/attachment/User_manual_newest.pdf') */?>"
                       target="_blank"
                       class="btn btn-success btn text-uppercase btn-rounded text-white">
                        Download Manual Book
                    </a>-->

                    </form>
                </li>


                <li class="nav-item dropdown ms-3">

                    <a class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown" href="#">
                        <i class="fa fa-th-large me-1"></i> Modules
                    </a>

                    <div class="dropdown-menu module-dropdown">

                        <div class="module-header">
                            <div>
                                <h5 class="mb-0"><i class="fa fa-th-large me-2"></i>ERP Applications</h5>
                                <small>Select your application</small>
                            </div>
                            <span class="module-version">J-SYS</span>
                        </div>

                        <div class="module-body">

                            <div class="row">

                                <div class="col-md-4 module-column">
                                    <div class="module-category">BUSINESS</div>

                                    <a href="<?= base_url('accounting')?>" class="module-item"><div class="module-icon bg-accounting"><i class="fa fa-calculator"></i></div><div><div class="module-title">Accounting</div><div class="module-desc">Journal & Finance</div></div></a>

                                    <a href="<?= base_url('purchasing')?>" class="module-item"><div class="module-icon bg-purchasing"><i class="fa fa-shopping-cart"></i></div><div><div class="module-title">Purchasing</div><div class="module-desc">Supplier & PO</div></div></a>

                                    <a href="<?= base_url('sales')?>" class="module-item"><div class="module-icon bg-sales"><i class="fa fa-line-chart"></i></div><div><div class="module-title">Sales</div><div class="module-desc">Customer & Invoice</div></div></a>

                                    <a href="<?= base_url('dashboard')?>" class="module-item"><div class="module-icon bg-accounting"><i class="fa fa-chart-pie"></i></div><div><div class="module-title">Dashboard BI</div><div class="module-desc">Executive Dashboard</div></div></a>

                                </div>

                                <div class="col-md-4 module-column">

                                    <div class="module-category">OPERATION</div>

                                    <a href="<?= base_url('production')?>" class="module-item"><div class="module-icon bg-production"><i class="fa fa-industry"></i></div><div><div class="module-title">Production</div><div class="module-desc">Manufacturing</div></div></a>

                                    <a href="<?= base_url('warehouse')?>" class="module-item"><div class="module-icon bg-warehouse"><i class="fa fa-cubes"></i></div><div><div class="module-title">Warehouse</div><div class="module-desc">Inventory</div></div></a>

                                    <a href="<?= base_url('quality')?>" class="module-item"><div class="module-icon bg-production"><i class="fa fa-check-circle"></i></div><div><div class="module-title">Quality</div><div class="module-desc">Quality Control</div></div></a>

                                    <a href="<?= base_url('maintenance')?>" class="module-item"><div class="module-icon bg-warehouse"><i class="fa fa-wrench"></i></div><div><div class="module-title">Maintenance</div><div class="module-desc">Machine</div></div></a>

                                </div>

                                <div class="col-md-4">

                                    <div class="module-category">CORPORATE</div>

                                    <a href="<?= base_url('hris')?>" class="module-item"><div class="module-icon bg-hr"><i class="fa fa-users"></i></div><div><div class="module-title">HRIS</div><div class="module-desc">Employee</div></div></a>

                                    <a href="<?= base_url('dcms')?>" class="module-item"><div class="module-icon bg-dcms"><i class="fa fa-folder-open"></i></div><div><div class="module-title">DCMS</div><div class="module-desc">Documents</div></div></a>

                                    <a href="<?= base_url('approval')?>" class="module-item"><div class="module-icon bg-hr"><i class="fa fa-check-square"></i></div><div><div class="module-title">Approval</div><div class="module-desc">Workflow</div></div></a>

                                    <a href="<?= base_url('administrator')?>" class="module-item"><div class="module-icon bg-admin"><i class="fa fa-cogs"></i></div><div><div class="module-title">Administrator</div><div class="module-desc">Security</div></div></a>

                                </div>

                            </div>

                        </div>

                    </div>

                </li>
            </ul>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
            <ul class="navbar-nav my-lg-0">
                <!-- ============================================================== -->
                <!-- Comment -->

                <li class="nav-item dropdown u-pro">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo base_url('assets/img/profile').'/'.trim($userInfo['image']); ?>" alt="user" class=""> <span class="hidden-md-down"><?= ucwords(strtolower(trim($userInfo['username']))).'  ( '.trim($userInfo['rolename']).' )' ?> &nbsp;<i class="fa fa-angle-down"></i></span> </a>
                    <div class="dropdown-menu dropdown-menu-end animated flipInY">
                        <!-- text-->
                        <a href="<?php echo base_url('profile') ?>" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                        <!-- text-->
                        <a href="<?php echo base_url('profile') ?>" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a>
                        <!-- text-->
                        <div class="dropdown-divider"></div>
                        <!-- text-->
                        <a  onclick="return confirm_logout()"  href="<?php echo base_url('dashboard/logout') ?>"  class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                        <!-- text-->
                    </div>
                    <script type="text/javascript">
                        function confirm_logout() {
                            return confirm('Are u sure for logged Out ?');
                        }
                    </script>
                </li>
                <!-- ============================================================== -->
                <!-- End User Profile -->
                <!-- ============================================================== -->
                <li class="nav-item right-side-toggle"> <a class="nav-link  waves-effect waves-light" href="javascript:void(0)"><i class="ti-settings"></i></a></li>
            </ul>
        </div>
    </nav>
</header>
<!-- /.navbar -->