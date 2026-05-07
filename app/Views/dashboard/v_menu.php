<style>
    body {
        background: #eef2f6;
    }

    /* CARD */
    .card-erp {
        height: 280px;
        border-radius: 18px;
        padding: 35px 20px;
        background: #f8f9fb;
        border: 1px solid #e6e9ef;

        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;

        position: relative;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    /* ACCENT */
    .card-erp::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 4px;
        width: 100%;
        border-radius: 18px 18px 0 0;
        background: var(--menu-color, #0d6efd);
    }

    .card-erp:hover {
        transform: translateY(-5px);
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(0,0,0,0.08);
    }

    /* ICON */
    .icon-erp {
        font-size: 90px;
    }
    .title-erp {
        font-size: 18px;
        font-weight: 600;
        margin-top: 10px;
    }

    /* HIDE CHECKBOX */
    .dropdown-toggle-input {
        display: none;
    }

    /* DROPDOWN */
    .dropdown {
        position: relative;
    }

    /* ===============================
   DROPDOWN DARK CORPORATE
=============================== */
    .dropdown-menu {
        display: none;

        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.95);

        width: 85%;
        padding: 12px;
        border-radius: 14px;

        background: #2f3540;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 18px 40px rgba(0,0,0,0.25);

        transition: 0.2s;
    }

    /* ===============================
       ITEM (SINGLE SOURCE)
    =============================== */
    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;

        padding: 12px;
        border-radius: 10px;

        color: #e9ecef; /* 🔥 konsisten dark theme */
        font-size: 14px;

        transition: 0.2s;
    }

    /* ICON */
    .dropdown-item i {
        width: 18px;
        font-size: 14px;
        opacity: 0.8;
    }

    /* HOVER */
    .dropdown-item:hover {
        background: var(--menu-color, #0d6efd);
        color: #fff;
        transform: translateX(4px);
    }

    /* ===============================
       CLICK TRIGGER (CHECKBOX)
    =============================== */
    .dropdown-toggle-input:checked + label + .dropdown-menu {
        display: block;
        transform: translate(-50%, -50%) scale(1);
    }

    /* ===============================
       AUTO HIDE SAAT MOUSE KELUAR
    =============================== */
    .dropdown:not(:hover) .dropdown-menu {
        display: none;
    }

    /* ===============================
       COLOR SYSTEM
    =============================== */
    .blue { color:#0d6efd; }
    .green { color:#198754; }
    .teal { color:#0dcaf0; }
</style>

<style>
    /* HEADER WRAPPER */
    .erp-header {
        background: #ffffff;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 20px;

        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* TITLE */
    .erp-title {
        font-size: 22px;
        font-weight: 600;
        color: #2c313a;
    }

    /* SUBTITLE */
    .erp-subtitle {
        font-size: 13px;
        color: #6c757d;
    }

    /* VERSION BADGE */
    .erp-version {
        font-size: 12px;
        background: #f1f4f8;
        padding: 5px 10px;
        border-radius: 8px;
        color: #495057;
        margin-left: 10px;
    }

    /* BREADCRUMB */
    .erp-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #6c757d;
    }

    .erp-breadcrumb a {
        color: #0d6efd;
        text-decoration: none;
    }

    .erp-breadcrumb a:hover {
        text-decoration: underline;
    }

    /* SEPARATOR */
    .erp-breadcrumb span {
        color: #adb5bd;
    }
</style>


<div class="erp-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">

        <!-- LEFT -->
        <div>
            <div class="erp-title">
                J-Accounting Starting System
                <span class="erp-version">JSYS-v1.0.0</span>
            </div>
            <div class="erp-subtitle">
                Integrated Business Process Management
            </div>
        </div>

        <!-- RIGHT (BREADCRUMB) -->
        <div class="erp-breadcrumb mt-2 mt-md-0">
            <i class="fa fa-home"></i>
            <a href="#">Home</a>
            <span>/</span>

            <i class="fa fa-layer-group"></i>
            <a href="#">Main Menu</a>
            <span>/</span>

            <i class="fa fa-chart-pie"></i>
            <span>Dashboard</span>
        </div>

    </div>
</div>
<div class="container-fluid mt-4">
    <div class="row g-4">

        <!-- DASHBOARD -->
        <div class="col-md-3">
            <div class="card card-erp" onclick="location.href='/dashboard'" style="--menu-color:#0d6efd;">
                <div class="icon-erp blue"><i class="fa-solid fa-chart-pie"></i></div>
                <div class="title-erp">Dashboard</div>
            </div>
        </div>

        <!-- PURCHASING -->
        <div class="col-md-3">
            <div class="dropdown">
                <input type="checkbox" id="menu1" class="dropdown-toggle-input">
                <label for="menu1" class="card card-erp" style="--menu-color:#0d6efd;">
                    <div class="icon-erp blue"><i class="fa-solid fa-cart-shopping"></i></div>
                    <div class="title-erp">Purchasing</div>
                </label>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-file-lines"></i> PR</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-file-invoice"></i> PO</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-box"></i> LPB / GR</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-truck"></i> Supplier</a></li>
                </ul>
            </div>
        </div>

        <!-- PRODUCTION -->
        <div class="col-md-3">
            <div class="dropdown">
                <input type="checkbox" id="menu2" class="dropdown-toggle-input">
                <label for="menu2" class="card card-erp" style="--menu-color:#198754;">
                    <div class="icon-erp green"><i class="fa-solid fa-industry"></i></div>
                    <div class="title-erp">Production</div>
                </label>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-gears"></i> SPK</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-layer-group"></i> WIP</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-cubes"></i> Hasil Produksi</a></li>
                </ul>
            </div>
        </div>

        <!-- INVENTORY -->
        <div class="col-md-3">
            <div class="dropdown">
                <input type="checkbox" id="menu3" class="dropdown-toggle-input">
                <label for="menu3" class="card card-erp" style="--menu-color:#0dcaf0;">
                    <div class="icon-erp teal"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <div class="title-erp">Inventory</div>
                </label>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-box"></i> Stock</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-right-left"></i> Mutasi</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-barcode"></i> Batch</a></li>
                </ul>
            </div>
        </div>

        <!-- MRP -->
        <div class="col-md-3">
            <div class="dropdown">
                <input type="checkbox" id="menu4" class="dropdown-toggle-input">
                <label for="menu4" class="card card-erp" style="--menu-color:#6f42c1;">
                    <div class="icon-erp" style="color:#6f42c1;"><i class="fa-solid fa-diagram-project"></i></div>
                    <div class="title-erp">MRP</div>
                </label>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-list-check"></i> Planning</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-cubes-stacked"></i> Material Req</a></li>
                </ul>
            </div>
        </div>

        <!-- SALES -->
        <div class="col-md-3">
            <div class="dropdown">
                <input type="checkbox" id="menu5" class="dropdown-toggle-input">
                <label for="menu5" class="card card-erp" style="--menu-color:#fd7e14;">
                    <div class="icon-erp" style="color:#fd7e14;"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="title-erp">Sales</div>
                </label>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-file-signature"></i> SO</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-truck-fast"></i> Delivery</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-receipt"></i> Invoice</a></li>
                </ul>
            </div>
        </div>

        <!-- FINANCE -->
        <div class="col-md-3">
            <div class="dropdown">
                <input type="checkbox" id="menu6" class="dropdown-toggle-input">
                <label for="menu6" class="card card-erp" style="--menu-color:#20c997;">
                    <div class="icon-erp" style="color:#20c997;"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div class="title-erp">Finance</div>
                </label>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-wallet"></i> Kas</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-money-check"></i> Pembayaran</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-hand-holding-dollar"></i> Penerimaan</a></li>
                </ul>
            </div>
        </div>

        <!-- ACCOUNTING -->
        <div class="col-md-3">
            <div class="dropdown">
                <input type="checkbox" id="menu7" class="dropdown-toggle-input">
                <label for="menu7" class="card card-erp" style="--menu-color:#6c757d;">
                    <div class="icon-erp" style="color:#6c757d;"><i class="fa-solid fa-calculator"></i></div>
                    <div class="title-erp">Accounting</div>
                </label>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-book"></i> Jurnal</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-book-open"></i> Buku Besar</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-check-double"></i> Posting</a></li>
                </ul>
            </div>
        </div>

        <!-- AR/AP -->
        <div class="col-md-3">
            <div class="dropdown">
                <input type="checkbox" id="menu8" class="dropdown-toggle-input">
                <label for="menu8" class="card card-erp" style="--menu-color:#dc3545;">
                    <div class="icon-erp red"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div class="title-erp">AR / AP</div>
                </label>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-handshake"></i> Piutang</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-credit-card"></i> Hutang</a></li>
                </ul>
            </div>
        </div>

        <!-- APPROVAL -->
        <div class="col-md-3">
            <div class="dropdown">
                <input type="checkbox" id="menu9" class="dropdown-toggle-input">
                <label for="menu9" class="card card-erp" style="--menu-color:#dc3545;">
                    <div class="icon-erp red"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="title-erp">Approval</div>
                </label>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-check"></i> PR Approval</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-check-double"></i> PO Approval</a></li>
                </ul>
            </div>
        </div>

    </div>
</div>

<script>
    document.querySelectorAll('.dropdown-toggle-input').forEach((checkbox) => {
        checkbox.addEventListener('change', function () {

            // kalau checkbox ini di-check
            if (this.checked) {

                // uncheck semua checkbox lain
                document.querySelectorAll('.dropdown-toggle-input').forEach((other) => {
                    if (other !== this) {
                        other.checked = false;
                    }
                });

            }
        });
    });
</script>

<script>
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-toggle-input').forEach(cb => cb.checked = false);
        }
    });
</script>