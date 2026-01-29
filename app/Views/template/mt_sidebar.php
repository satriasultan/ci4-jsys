<!-- Main Sidebar Container -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!--li class="nav-small-cap">--- EXTRA COMPONENTS</li-->
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="icon-speedometer"></i><span class="hide-menu">Dashboard <span class="badge rounded-pill bg-cyan ms-auto">4</span></span></a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="<?php echo base_url('dashboard') ?>">Dashboard </a></li>
                        </ul>
                        <!-- <ul aria-expanded="false" class="collapse">
                            <li><a href="<?php echo base_url('dashboarduser') ?>">Dashboard User </a></li>
                        </ul> -->
                </li>
                <?php foreach ($list_menu_main as $lm) { ?>
                <li> <a class="has-arrow waves-effect waves-dark" href="<?php if (trim($lm->linkmenu)!=='NULL') {echo base_url(trim($lm->linkmenu));} else { echo '#';}?>" aria-expanded="false"><i class="fa <?php echo trim($lm->iconmenu);?>"></i><span class="hide-menu"> <?php echo ucwords(strtolower(trim($lm->namamenu)));?> <span class="badge rounded-pill bg-info">25</span></span></a>
                    <ul aria-expanded="false" class="collapse"  style=" text-wrap: nowrap; min-width: 250px; position: absolute;left: 0;">
                        <?php foreach ($list_menu_sub as $lms) {
                        if (trim($lms->parentmenu)==trim($lm->kodemenu)){
                        ?>
                        <li><a href="<?php if ( trim($lms->linkmenu)!=='NULL') {echo base_url(trim($lms->linkmenu));} else { echo '#';}?>" class="has-arrow"><i class="fa <?php if (!empty($lms->iconmenu)) { echo trim($lms->iconmenu);} else { echo 'fa-angle-double-right';}?>"></i> <?php echo ucwords(strtolower(trim($lms->namamenu)));?> <span class="badge rounded-pill bg-success pull-right">6</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <?php foreach ($list_menu_submenu as $lmp){
                                if (trim($lmp->parentmenu)==trim($lm->kodemenu) and trim($lmp->parentsub)==trim($lms->kodemenu)){?>
                                <li><a href="<?php if (trim($lmp->linkmenu)!=='NULL') {echo base_url(trim($lmp->linkmenu));} else { echo '#';}?>" class="<?= trim($lmp->idkodemenu) ?> " onclick="crutz('<?php echo trim($lmp->kodemenu);?>')"><i class="fa fa-angle-double-right"></i><?php echo '     '.ucwords(strtolower(trim($lmp->namamenu)));?></a>
                                </li>
                                <?php }}?>
                            </ul>
                        </li>
                        <?php }}?>
                    </ul>
                </li>

                <?php } ?>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<!--Modal untuk Input-->
<div class="modal fade " id="aboutUsProfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">About Us</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="box box-warning">
                        <div class="box-body">
                            <p><img style="width:600px;height:300px; padding-left: 30%" src="<?php echo base_url('assets/img/logo-depan/jts-icon.png')?>" alt="logo" align="center"><hr class="space s" style="alignment: center;"></p>
                            <?php /*<p style="color: #000000;" align="center">JTS <em>Jatim Taman Steel</em></p>*/ ?>
                        </div>
                    </div>
                </div><!--row-->
            </div>
            <div class="modal-footer" align="center">
                <div class="hidden-xs">
                    <b>Version</b> 4.1.1
                </div>
                <strong>Copyright &copy; IT Development-JTS 2021 <a href="#"></a>.</strong> All rights
                reserved.
            </div>
            </form>
        </div>
    </div>
</div>