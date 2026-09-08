<?php
if (!function_exists('base_url')) {
    function base_url($path = '') { return '/' . ltrim($path, '/'); }
}
?>
<style>
    /* =========================================================
      J-SYSTEM APPLICATION LAUNCHER
      WHITE PREMIUM EDITION
   ========================================================= */
    /* =========================================================
       J-SYSTEM ERP APPLICATION PORTAL
       CORPORATE / FUTURISTIC / WHITE
    ========================================================= */

    .erp-portal {
        position: relative;
        width: 100%;
        padding: 28px 28px 35px;

        background:
                radial-gradient(
                        circle at 85% 8%,
                        rgba(13, 110, 253, .055),
                        transparent 30%
                ),
                linear-gradient(
                        180deg,
                        #ffffff 0%,
                        #f8faff 100%
                );

        border: 1px solid #e2e8f0;
        border-radius: 22px;

        overflow: hidden;

        box-shadow:
                0 8px 30px rgba(15, 23, 42, .045);
    }


    /* =========================================================
       FUTURISTIC GRID BACKGROUND
    ========================================================= */

    .erp-portal::before {
        content: "";

        position: absolute;
        inset: 0;

        background-image:
                linear-gradient(
                        rgba(13, 110, 253, .025) 1px,
                        transparent 1px
                ),
                linear-gradient(
                        90deg,
                        rgba(13, 110, 253, .025) 1px,
                        transparent 1px
                );

        background-size: 32px 32px;

        pointer-events: none;
    }


    /* =========================================================
       HEADER
    ========================================================= */

    .erp-header {
        position: relative;
        z-index: 2;

        display: flex;
        align-items: center;
        justify-content: space-between;

        padding-bottom: 22px;

        margin-bottom: 22px;

        border-bottom: 1px solid #e5eaf1;
    }


    /* LEFT */

    .erp-header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }


    /* BLUE SYSTEM ICON */

    .erp-header-icon {
        width: 52px;
        height: 52px;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #ffffff;

        background:
                linear-gradient(
                        145deg,
                        #0d6efd,
                        #084298
                );

        border-radius: 13px;

        font-size: 21px;

        box-shadow:
                0 7px 18px rgba(13, 110, 253, .20);
    }

    .erp-header-icon i {
        transform: translateY(-1px);
    }


    /* TITLE */

    .erp-header-title {
        margin: 0;

        font-size: 22px;
        line-height: 1.15;

        font-weight: 800;

        letter-spacing: -.4px;

        color: #172033;
    }

    .erp-header-subtitle {
        margin-top: 5px;

        font-size: 11px;

        letter-spacing: .65px;

        color: #8993a3;

        text-transform: uppercase;
    }


    /* =========================================================
       SYSTEM STATUS
    ========================================================= */

    .erp-system-status {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 8px 13px;

        background: #ffffff;

        border: 1px solid #dce4ee;

        border-radius: 9px;

        font-size: 9px;
        font-weight: 800;

        letter-spacing: 1px;

        color: #647084;

        box-shadow:
                0 3px 10px rgba(15, 23, 42, .035);
    }

    .erp-system-status span {
        width: 7px;
        height: 7px;

        border-radius: 50%;

        background: #20c997;

        box-shadow:
                0 0 0 4px rgba(32, 201, 151, .10);
    }


    /* =========================================================
       SECTION LABEL
    ========================================================= */

    .erp-menu-heading {
        position: relative;
        z-index: 2;

        display: flex;
        align-items: center;
        justify-content: space-between;

        margin-bottom: 13px;
    }

    .erp-menu-heading-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .erp-menu-heading-left::before {
        content: "";

        width: 3px;
        height: 16px;

        border-radius: 3px;

        background: #0d6efd;
    }

    .erp-menu-heading strong {
        font-size: 11px;

        font-weight: 800;

        letter-spacing: 1.4px;

        color: #435066;

        text-transform: uppercase;
    }

    .erp-menu-heading small {
        font-size: 9px;

        font-weight: 700;

        letter-spacing: .7px;

        color: #a0a9b7;

        text-transform: uppercase;
    }


    /* =========================================================
       MENU GRID
    ========================================================= */

    .erp-menu-row {
        position: relative;
        z-index: 2;

        display: grid;

        grid-template-columns:
        repeat(4, minmax(180px, 1fr));

        gap: 15px;

        width: 100%;

        margin-top: 0;
    }


    /* Remove old wrapper effect */

    .erp-col {
        display: contents;
    }


    /* =========================================================
       ERP BUTTON
    ========================================================= */

    .card-erp {
        --menu-color: #0d6efd;

        position: relative;

        width: 100%;
        height: 168px;

        padding: 17px 18px;

        display: flex;
        flex-direction: column;

        justify-content: space-between;

        text-decoration: none !important;

        background: rgba(255,255,255,.94);

        border: 1px solid #dce3ec;

        border-radius: 14px;

        overflow: hidden;

        cursor: pointer;

        box-shadow:
                0 2px 5px rgba(15,23,42,.035),
                0 7px 18px rgba(15,23,42,.045);

        transition:
                transform .22s ease,
                border-color .22s ease,
                box-shadow .22s ease,
                background .22s ease;
    }


    /* =========================================================
       LEFT ACCENT
    ========================================================= */

    .card-erp::before {
        content: "";

        position: absolute;

        left: 0;
        top: 0;
        bottom: 0;

        width: 3px;

        background: var(--menu-color);

        opacity: .75;

        transition:
                width .22s ease,
                opacity .22s ease;
    }


    /* =========================================================
       FUTURISTIC CORNER
    ========================================================= */

    .card-erp::after {
        content: "";

        position: absolute;

        right: -38px;
        bottom: -38px;

        width: 105px;
        height: 105px;

        border: 1px solid
        color-mix(
                in srgb,
                var(--menu-color) 18%,
                transparent
        );

        border-radius: 50%;

        opacity: .6;

        transition:
                transform .35s ease,
                opacity .35s ease;
    }


    /* =========================================================
       TOP META
    ========================================================= */

    .card-top {
        display: flex;

        align-items: center;
        justify-content: space-between;

        position: relative;
        z-index: 2;
    }

    .module-number {
        font-family: monospace;

        font-size: 9px;

        font-weight: 700;

        letter-spacing: 1px;

        color: #a2abb8;
    }

    .module-status {
        display: flex;

        align-items: center;

        gap: 5px;

        font-size: 7px;

        font-weight: 800;

        letter-spacing: 1px;

        color: #929cab;
    }

    .module-status i {
        width: 5px;
        height: 5px;

        border-radius: 50%;

        background: var(--menu-color);
    }


    /* =========================================================
       ICON
    ========================================================= */

    .icon-erp {
        position: relative;

        z-index: 2;

        width: 48px;
        height: 48px;

        display: flex;

        align-items: center;
        justify-content: center;

        border-radius: 12px;

        background:
                color-mix(
                        in srgb,
                        var(--menu-color) 7%,
                        #ffffff
                );

        border: 1px solid
        color-mix(
                in srgb,
                var(--menu-color) 18%,
                #e2e7ee
        );

        color: var(--menu-color);

        font-size: 20px;

        box-shadow:
                inset 0 1px 0 rgba(255,255,255,.9);

        transition:
                transform .22s ease,
                background .22s ease,
                color .22s ease,
                box-shadow .22s ease;
    }


    /* =========================================================
       CONTENT
    ========================================================= */

    .card-content {
        position: relative;
        z-index: 2;
    }

    .title-erp {
        margin-top: 9px;

        font-size: 16px;
        line-height: 19px;

        font-weight: 750;

        letter-spacing: -.25px;

        color: #1d2737;

        transition:
                color .2s ease;
    }

    .count-erp {
        margin-top: 3px;

        font-size: 9px;
        line-height: 14px;

        font-weight: 700;

        letter-spacing: .55px;

        color: #8993a2;

        text-transform: uppercase;
    }


    /* =========================================================
       OPEN INDICATOR
    ========================================================= */

    .card-action {
        position: absolute;

        right: 17px;
        bottom: 15px;

        z-index: 3;

        width: 27px;
        height: 27px;

        display: flex;

        align-items: center;
        justify-content: center;

        border-radius: 8px;

        background: #f5f7fa;

        border: 1px solid #e3e8ef;

        color: #8b95a5;

        font-size: 10px;

        transition:
                background .22s ease,
                color .22s ease,
                border-color .22s ease,
                transform .22s ease;
    }


    /* =========================================================
       HOVER
    ========================================================= */

    .card-erp:hover {
        transform: translateY(-4px);

        background: #ffffff;

        border-color: var(--menu-color);

        box-shadow:
                0 5px 10px rgba(15,23,42,.05),
                0 16px 30px
                color-mix(
                        in srgb,
                        var(--menu-color) 13%,
                        rgba(15,23,42,.08)
                );
    }


    /* ACCENT */

    .card-erp:hover::before {
        width: 5px;
        opacity: 1;
    }


    /* CIRCLE */

    .card-erp:hover::after {
        transform: scale(1.65);
        opacity: 1;
    }


    /* ICON */

    .card-erp:hover .icon-erp {
        transform: translateY(-2px);

        background: var(--menu-color);

        color: #ffffff;

        border-color: var(--menu-color);

        box-shadow:
                0 7px 16px
                color-mix(
                        in srgb,
                        var(--menu-color) 25%,
                        transparent
                );
    }


    /* TITLE */

    .card-erp:hover .title-erp {
        color: var(--menu-color);
    }


    /* ACTION */

    .card-erp:hover .card-action {
        background: var(--menu-color);

        border-color: var(--menu-color);

        color: #ffffff;

        transform: translateX(2px);
    }


    /* =========================================================
       CLICK
    ========================================================= */

    .card-erp:active {
        transform:
                translateY(-1px)
                scale(.985);
    }


    /* =========================================================
       FOCUS
    ========================================================= */

    .card-erp:focus-visible {
        outline: none;

        border-color: var(--menu-color);

        box-shadow:
                0 0 0 3px
                color-mix(
                        in srgb,
                        var(--menu-color) 15%,
                        transparent
                );
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 1200px) {

        .erp-menu-row {
            grid-template-columns:
            repeat(3, minmax(180px, 1fr));
        }

    }


    @media (max-width: 850px) {

        .erp-menu-row {
            grid-template-columns:
            repeat(2, minmax(160px, 1fr));
        }

        .erp-portal {
            padding: 22px;
        }

    }


    @media (max-width: 560px) {

        .erp-header {
            align-items: flex-start;
        }

        .erp-system-status {
            display: none;
        }

        .erp-menu-row {
            grid-template-columns: 1fr;
        }

    }
</style>


<!-- ═══════════════════════════════════════════════════════
     J-SYSTEM ERP APPLICATION PORTAL
════════════════════════════════════════════════════════ -->

<div class="erp-portal">

    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="erp-header">

        <div class="erp-header-left">

            <div class="erp-header-icon">
                <i class="fa fa-th-large"></i>
            </div>

            <div class="erp-header-info">

                <div class="erp-header-title">
                    ERP Application Portal
                </div>

                <div class="erp-header-subtitle">
                    J-System Enterprise Resource Planning
                </div>

            </div>

        </div>


        <div class="erp-system-status">
            <span></span>
            SYSTEM ONLINE
        </div>

    </div>


    <!-- =====================================================
         APPLICATION TITLE
    ====================================================== -->

    <div class="erp-menu-heading">

        <div class="erp-menu-heading-left">

            <div>
                <strong>Application Modules</strong>

                <div class="erp-menu-description">
                    Select an application to continue your business process
                </div>
            </div>

        </div>

        <small>
            SMART&nbsp;&nbsp;•&nbsp;&nbsp;INTEGRATED&nbsp;&nbsp;•&nbsp;&nbsp;PRODUCTIVE
        </small>

    </div>


    <!-- =====================================================
         APPLICATION MENU
    ====================================================== -->

    <div class="erp-menu-row">


        <!-- =================================================
             01 ACCOUNTING
        ================================================== -->

        <a href="<?= base_url('launcher/open/accounting') ?>"
           class="card-erp"
           style="--menu-color:#0d6efd;">

            <div class="card-top">

                <span class="module-number">
                    01
                </span>

                <span class="module-status">
                    <i></i>
                    CORE
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-calculator"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    Accounting
                </div>

                <div class="count-erp">
                    Journal & Finance
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             02 PURCHASING
        ================================================== -->

        <a href="<?= base_url('launcher/open/purchasing') ?>"
           class="card-erp"
           style="--menu-color:#198754;">

            <div class="card-top">

                <span class="module-number">
                    02
                </span>

                <span class="module-status">
                    <i></i>
                    CORE
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-shopping-cart"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    Purchasing
                </div>

                <div class="count-erp">
                    Supplier & PO
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             03 SALES
        ================================================== -->

        <a href="<?= base_url('launcher/open/sales') ?>"
           class="card-erp"
           style="--menu-color:#fd7e14;">

            <div class="card-top">

                <span class="module-number">
                    03
                </span>

                <span class="module-status">
                    <i></i>
                    CORE
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-line-chart"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    Sales
                </div>

                <div class="count-erp">
                    Customer & Invoice
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             04 PRODUCTION
        ================================================== -->

        <a href="<?= base_url('launcher/open/production') ?>"
           class="card-erp"
           style="--menu-color:#6f42c1;">

            <div class="card-top">

                <span class="module-number">
                    04
                </span>

                <span class="module-status">
                    <i></i>
                    OPERATION
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-industry"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    Production
                </div>

                <div class="count-erp">
                    Manufacturing
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             05 WAREHOUSE
        ================================================== -->

        <a href="<?= base_url('launcher/open/warehouse') ?>"
           class="card-erp"
           style="--menu-color:#0dcaf0;">

            <div class="card-top">

                <span class="module-number">
                    05
                </span>

                <span class="module-status">
                    <i></i>
                    OPERATION
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-cubes"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    Warehouse
                </div>

                <div class="count-erp">
                    Inventory
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             06 QUALITY
        ================================================== -->

        <a href="<?= base_url('launcher/open/quality') ?>"
           class="card-erp"
           style="--menu-color:#20c997;">

            <div class="card-top">

                <span class="module-number">
                    06
                </span>

                <span class="module-status">
                    <i></i>
                    OPERATION
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-check-circle"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    Quality
                </div>

                <div class="count-erp">
                    Quality Control
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             07 MAINTENANCE
        ================================================== -->

        <a href="<?= base_url('launcher/open/maintenance') ?>"
           class="card-erp"
           style="--menu-color:#dc3545;">

            <div class="card-top">

                <span class="module-number">
                    07
                </span>

                <span class="module-status">
                    <i></i>
                    OPERATION
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-wrench"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    Maintenance
                </div>

                <div class="count-erp">
                    Machine
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             08 HRIS
        ================================================== -->

        <a href="<?= base_url('launcher/open/hris') ?>"
           class="card-erp"
           style="--menu-color:#e83e8c;">

            <div class="card-top">

                <span class="module-number">
                    08
                </span>

                <span class="module-status">
                    <i></i>
                    CORPORATE
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-users"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    HRIS
                </div>

                <div class="count-erp">
                    Human Capital
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             09 DCMS
        ================================================== -->

        <a href="<?= base_url('launcher/open/dcms') ?>"
           class="card-erp"
           style="--menu-color:#795548;">

            <div class="card-top">

                <span class="module-number">
                    09
                </span>

                <span class="module-status">
                    <i></i>
                    CORPORATE
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-file-text"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    DCMS
                </div>

                <div class="count-erp">
                    Document Control
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             10 APPROVAL
        ================================================== -->

        <a href="<?= base_url('launcher/open/approval') ?>"
           class="card-erp"
           style="--menu-color:#6f42c1;">

            <div class="card-top">

                <span class="module-number">
                    10
                </span>

                <span class="module-status">
                    <i></i>
                    WORKFLOW
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-check-square"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    Approval
                </div>

                <div class="count-erp">
                    Workflow
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>



        <!-- =================================================
             11 ADMINISTRATOR
        ================================================== -->

        <a href="<?= base_url('launcher/open/administrator') ?>"
           class="card-erp"
           style="--menu-color:#495057;">

            <div class="card-top">

                <span class="module-number">
                    11
                </span>

                <span class="module-status">
                    <i></i>
                    SYSTEM
                </span>

            </div>


            <div class="icon-erp">
                <i class="fa fa-cogs"></i>
            </div>


            <div class="card-content">

                <div class="title-erp">
                    Administrator
                </div>

                <div class="count-erp">
                    System & Security
                </div>

            </div>


            <div class="card-action">
                <i class="fa fa-arrow-right"></i>
            </div>

        </a>


    </div>

</div>

<script>
    /* Satu checkbox aktif sekaligus */
    document.querySelectorAll('.dd-toggle').forEach(cb => {
        cb.addEventListener('change', function () {
            if (this.checked) {
                document.querySelectorAll('.dd-toggle').forEach(o => {
                    if (o !== this) o.checked = false;
                });
            }
        });
    });

    /* Klik luar → tutup semua */
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.erp-dropdown')) {
            document.querySelectorAll('.dd-toggle').forEach(cb => cb.checked = false);
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarNav = document.querySelector('.sidebar-nav');

        if (sidebarNav) {
            sidebarNav.remove();
        }
    });
</script>