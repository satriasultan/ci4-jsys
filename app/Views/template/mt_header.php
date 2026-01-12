<?php
use App\Libraries\Fiky_encryption;
$this->session = \Config\Services::session();
?>
<!-- Navbar -->
<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <div class="navbar-header">
            <a class="navbar-brand" href="index.html">
                <!-- Logo icon -->
                <b>
                    <!-- Dark Logo icon -->
                    <img src="<?php echo base_url('assets/img/logo-depan/jts.ico') ?>" style="width: 30%"  alt="homepage" class="dark-logo" />
                    <!-- Light Logo icon -->
                    <img src="<?php echo base_url('assets/img/logo-depan/jts.ico') ?>" style="width: 30%" alt="homepage" class="light-logo" />
                    <span class="brand-text font-weight-light" style="font-size: 130%; font-weight: bolder;">IT-JTS</span>
                </b>
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