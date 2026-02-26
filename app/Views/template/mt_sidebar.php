<!-- Main Sidebar Container -->
<!-- Main Sidebar Container -->
<aside class="left-sidebar">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">

                <!-- DASHBOARD -->
                <li>
                    <a class="has-arrow waves-effect waves-dark"
                       href="javascript:void(0)"
                       aria-expanded="false">
                        <i class="icon-speedometer"></i>
                        <span class="hide-menu">
                            Dashboard
                            <span class="badge rounded-pill bg-cyan ms-auto">4</span>
                        </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li>
                            <a href="<?= base_url('dashboard') ?>">Dashboard</a>
                        </li>
                    </ul>
                </li>

                <?php foreach ($list_menu_main as $lm): ?>
                    <?php
                    $linkMain = trim($lm->linkmenu);
                    $isInvalidMain = (empty($linkMain) || strtoupper($linkMain) === 'NULL' || $linkMain === '#');
                    ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark"
                           href="<?= !$isInvalidMain ? base_url($linkMain) : '#' ?>"
                                <?= $isInvalidMain ? 'onclick="return false;"' : '' ?>
                           aria-expanded="false">
                            <i class="fa <?= trim($lm->iconmenu); ?>"></i>
                            <span class="hide-menu">
                            <?= ucwords(strtolower(trim($lm->namamenu))); ?>
                            <span class="badge rounded-pill bg-info">25</span>
                        </span>
                        </a>

                        <ul aria-expanded="false" class="collapse"
                            style="text-wrap: nowrap; min-width: 250px; position: absolute; left: 0;">

                            <?php foreach ($list_menu_sub as $lms): ?>
                                <?php if (trim($lms->parentmenu) == trim($lm->kodemenu)): ?>

                                    <?php
                                    $linkSub = trim($lms->linkmenu);
                                    $isInvalidSub = (empty($linkSub) || strtoupper($linkSub) === 'NULL' || $linkSub === '#');
                                    ?>

                                    <li>
                                        <a href="<?= !$isInvalidSub ? base_url($linkSub) : '#' ?>"
                                                <?= $isInvalidSub ? 'onclick="return false;"' : '' ?>
                                           class="has-arrow">
                                            <i class="fa <?= !empty($lms->iconmenu)
                                                    ? trim($lms->iconmenu)
                                                    : 'fa-angle-double-right'; ?>"></i>
                                            <?= ucwords(strtolower(trim($lms->namamenu))); ?>
                                            <span class="badge rounded-pill bg-success pull-right">6</span>
                                        </a>

                                        <ul aria-expanded="false" class="collapse">

                                            <?php foreach ($list_menu_submenu as $lmp): ?>
                                                <?php
                                                if (trim($lmp->parentmenu) == trim($lm->kodemenu) &&
                                                        trim($lmp->parentsub) == trim($lms->kodemenu)):

                                                    $linkChild = trim($lmp->linkmenu);
                                                    $isInvalidChild = (empty($linkChild) || strtoupper($linkChild) === 'NULL' || $linkChild === '#');
                                                    ?>

                                                    <li>
                                                        <a href="<?= !$isInvalidChild ? base_url($linkChild) : '#' ?>"
                                                                <?= $isInvalidChild ? 'onclick="return false;"' : '' ?>
                                                           class="<?= trim($lmp->idkodemenu) ?>"
                                                                <?= !$isInvalidChild ? "onclick=\"crutz('".trim($lmp->kodemenu)."')\"" : '' ?>>
                                                            <i class="fa fa-angle-double-right"></i>
                                                            <?= ' '.ucwords(strtolower(trim($lmp->namamenu))); ?>
                                                        </a>
                                                    </li>

                                                <?php endif; ?>
                                            <?php endforeach; ?>

                                        </ul>
                                    </li>

                                <?php endif; ?>
                            <?php endforeach; ?>

                        </ul>
                    </li>
                <?php endforeach; ?>

            </ul>
        </nav>
    </div>
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
                    <b>Version</b> 0.0.1
                </div>
                <strong>Copyright &copy; IT Development-JTS 2026 <a href="#"></a>.</strong> All rights
                reserved.
            </div>
            </form>
        </div>
    </div>
</div>