<?php
if (!function_exists('base_url')) {
    function base_url($path = '') { return '/' . ltrim($path, '/'); }
}
?>
<style>
    /* =============================================
       HEADER
    ============================================= */
    .erp-header {
        background: #ffffff;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .erp-title {
        font-size: 22px;
        font-weight: 600;
        color: #2c313a;
    }
    .erp-subtitle {
        font-size: 13px;
        color: #6c757d;
    }
    .erp-version {
        font-size: 12px;
        background: #f1f4f8;
        padding: 5px 10px;
        border-radius: 8px;
        color: #495057;
        margin-left: 10px;
    }
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
    .erp-breadcrumb a:hover { text-decoration: underline; }
    .erp-breadcrumb span   { color: #adb5bd; }

    /* =============================================
       CARD ERP
    ============================================= */
    .card-erp {
        height: 200px;
        border-radius: 18px;
        padding: 28px 20px;
        background: #f8f9fb;
        border: 1px solid #e6e9ef;

        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;

        position: relative;
        cursor: pointer;
        transition: all 0.25s ease;
        text-decoration: none;
        color: inherit;
        width: 100%;
    }
    .card-erp::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        height: 4px; width: 100%;
        border-radius: 18px 18px 0 0;
        background: var(--menu-color, #0d6efd);
    }
    .card-erp:hover {
        transform: translateY(-5px);
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(0,0,0,0.10);
    }

    .icon-erp  { font-size: 52px; line-height: 1; }
    .title-erp { font-size: 15px; font-weight: 600; margin-top: 12px; color: #2c313a; text-align: center; }
    .count-erp { font-size: 11px; color: #9aa0b2; margin-top: 4px; }

    /* =============================================
       DROPDOWN — kunci: overflow visible pada semua ancestor
    ============================================= */

    /* Row Bootstrap default overflow visible, tapi pastikan tidak ada yang clip */
    .erp-menu-row {
        /* pakai flex manual agar overflow bisa keluar */
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        /* JANGAN overflow:hidden */
    }

    .erp-col {
        /* lebar responsif */
        flex: 1 1 200px;
        max-width: calc(25% - 12px);
        /* WAJIB: overflow visible supaya dropdown tidak terpotong */
        overflow: visible;
        position: relative;
    }

    @media (max-width: 992px) { .erp-col { max-width: calc(33.33% - 12px); } }
    @media (max-width: 768px) { .erp-col { max-width: calc(50% - 8px); } }
    @media (max-width: 480px) { .erp-col { max-width: 100%; } }

    .erp-dropdown {
        position: relative;
        overflow: visible; /* PENTING */
    }

    /* HIDE CHECKBOX */
    .dd-toggle { display: none; }

    /* =============================================
       DROPDOWN PANEL
    ============================================= */
    .dd-menu {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%);

        min-width: 230px;
        width: max-content;
        max-width: 270px;

        padding: 8px;
        border-radius: 14px;

        background: #1e2335;
        border: 1px solid rgba(255,255,255,0.09);
        box-shadow: 0 20px 48px rgba(0,0,0,0.35);

        /* PENTING: z-index tinggi agar tidak tertutup card lain */
        z-index: 1050;

        /* max-height + scroll kalau banyak item */
        max-height: 320px;
        overflow-y: auto;
        overflow-x: hidden;

        /* scrollbar tipis */
        scrollbar-width: thin;
        scrollbar-color: #2f3a55 transparent;
    }
    .dd-menu::-webkit-scrollbar       { width: 4px; }
    .dd-menu::-webkit-scrollbar-track { background: transparent; }
    .dd-menu::-webkit-scrollbar-thumb { background: #2f3a55; border-radius: 4px; }

    /* GROUP LABEL */
    .dd-group-label {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #4a5068;
        padding: 8px 10px 4px;
    }
    .dd-divider {
        height: 1px;
        background: rgba(255,255,255,0.06);
        margin: 6px 4px;
    }

    /* ITEM */
    .dd-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border-radius: 9px;
        color: #c8cde0;
        font-size: 13px;
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease;
        white-space: nowrap;
    }
    .dd-item i {
        width: 16px;
        font-size: 13px;
        text-align: center;
        opacity: 0.7;
        flex-shrink: 0;
        color: var(--menu-color, #4f8ef7);
    }
    .dd-item:hover {
        background: var(--menu-color, #0d6efd);
        color: #ffffff;
        transform: translateX(3px);
    }
    .dd-item:hover i { opacity: 1; color: #fff; }

    /* OPEN via hover atau checkbox */
    .erp-dropdown:hover .dd-menu,
    .dd-toggle:checked + label + .dd-menu {
        display: block;
    }
    .erp-dropdown:not(:hover) .dd-menu { display: none; }
</style>

<!-- ══════════════════════════════════════
    HEADER
══════════════════════════════════════ -->
<div class="erp-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">

        <div>
            <div class="erp-title">
                J-Accounting Starting System
                <span class="erp-version">JSYS-v1.0.0</span>
            </div>
            <div class="erp-subtitle">Integrated Business Process Management</div>
        </div>

        <div class="erp-breadcrumb mt-2 mt-md-0">
            <i class="fa fa-home"></i>
            <a href="#">Home</a>
            <span>/</span>
            <i class="fa fa-th-large"></i>
            <a href="#">Main Menu</a>
            <span>/</span>
            <i class="fa fa-pie-chart"></i>
            <span>Dashboard</span>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════
     MENU GRID
══════════════════════════════════════ -->
<div class="erp-menu-row">

    <!-- MASTER DATA -->
    <div class="erp-col">
        <div class="erp-dropdown" style="--menu-color:#0d6efd;">
            <input type="checkbox" id="dd0" class="dd-toggle">
            <label for="dd0" class="card-erp">
                <div class="icon-erp" style="color:#0d6efd;"><i class="fa fa-database"></i></div>
                <div class="title-erp">Master Data</div>
                <div class="count-erp">14 menu</div>
            </label>
            <div class="dd-menu">
                <div class="dd-group-label">Umum</div>
                <a class="dd-item" href="<?= base_url('master/data/customer') ?>"><i class="fa fa-users"></i> Master Customer</a>
                <a class="dd-item" href="<?= base_url('master/data/supplier') ?>"><i class="fa fa-truck"></i> Master Supplier</a>
                <a class="dd-item" href="<?= base_url('master/data/tax') ?>"><i class="fa fa-percent"></i> Master Tax</a>
                <a class="dd-item" href="<?= base_url('master/data/currency') ?>"><i class="fa fa-money"></i> Master Currency</a>
                <a class="dd-item" href="<?= base_url('master/data/coa') ?>"><i class="fa fa-list-alt"></i> Master COA</a>
                <a class="dd-item" href="<?= base_url('master/data/job') ?>"><i class="fa fa-briefcase"></i> Master Job</a>
                <a class="dd-item" href="<?= base_url('master/data/location') ?>"><i class="fa fa-building"></i> Master Warehouse</a>
                <a class="dd-item" href="<?= base_url('master/data/area') ?>"><i class="fa fa-map-marker"></i> Master Sub Location</a>
                <a class="dd-item" href="<?= base_url('master/data/cc') ?>"><i class="fa fa-sitemap"></i> Master Cost Center</a>
                <div class="dd-divider"></div>
                <div class="dd-group-label">Barang</div>
                <a class="dd-item" href="<?= base_url('master/data/barang') ?>"><i class="fa fa-barcode"></i> Master Barang</a>
                <a class="dd-item" href="<?= base_url('master/data/golonganbarang') ?>"><i class="fa fa-folder-open"></i> Golongan Barang</a>
                <a class="dd-item" href="<?= base_url('master/data/jenisproduk') ?>"><i class="fa fa-tags"></i> Jenis Produk</a>
                <a class="dd-item" href="<?= base_url('master/data/kelompokbarang') ?>"><i class="fa fa-th-large"></i> Kelompok Barang</a>
                <a class="dd-item" href="<?= base_url('master/data/principal') ?>"><i class="fa fa-star"></i> Master Principal</a>
            </div>
        </div>
    </div>

    <!-- PEMBELIAN -->
    <div class="erp-col">
        <div class="erp-dropdown" style="--menu-color:#0d6efd;">
            <input type="checkbox" id="dd1" class="dd-toggle">
            <label for="dd1" class="card-erp">
                <div class="icon-erp" style="color:#0d6efd;"><i class="fa fa-cart-plus"></i></div>
                <div class="title-erp">Pembelian</div>
                <div class="count-erp">7 menu</div>
            </label>
            <div class="dd-menu">
                <div class="dd-group-label">Transaksi</div>
                <a class="dd-item" href="<?= base_url('purchase/trans/pp') ?>"><i class="fa fa-file-text-o"></i> Permintaan Pembelian (PP)</a>
                <a class="dd-item" href="<?= base_url('purchase/trans/voidpp') ?>"><i class="fa fa-ban"></i> Void PP</a>
                <a class="dd-item" href="<?= base_url('purchase/trans/po') ?>"><i class="fa fa-file-text"></i> Purchase Order (PO)</a>
                <a class="dd-item" href="<?= base_url('purchase/trans/voidpo') ?>"><i class="fa fa-ban"></i> Void PO</a>
                <a class="dd-item" href="<?= base_url('purchase/trans/umb') ?>"><i class="fa fa-money"></i> Uang Muka Pembelian</a>
                <a class="dd-item" href="<?= base_url('purchase/trans/lpb') ?>"><i class="fa fa-inbox"></i> Penerimaan Pembelian</a>
                <a class="dd-item" href="<?= base_url('purchase/trans/returbeli') ?>"><i class="fa fa-reply"></i> Retur Pembelian</a>
            </div>
        </div>
    </div>

    <!-- PERSEDIAAN -->
    <div class="erp-col">
        <div class="erp-dropdown" style="--menu-color:#0dcaf0;">
            <input type="checkbox" id="dd2" class="dd-toggle">
            <label for="dd2" class="card-erp">
                <div class="icon-erp" style="color:#0dcaf0;"><i class="fa fa-cubes"></i></div>
                <div class="title-erp">Persediaan</div>
                <div class="count-erp">6 menu</div>
            </label>
            <div class="dd-menu">
                <div class="dd-group-label">Transaksi</div>
                <a class="dd-item" href="<?= base_url('persediaan/trans/perintah_transfer') ?>"><i class="fa fa-arrow-right"></i> Perintah Transfer Lokasi</a>
                <a class="dd-item" href="<?= base_url('persediaan/trans/transfer_lokasi') ?>"><i class="fa fa-exchange"></i> Transfer Antar Lokasi</a>
                <a class="dd-item" href="<?= base_url('persediaan/trans/ajustment_stock') ?>"><i class="fa fa-sliders"></i> Adjustment Stock</a>
                <a class="dd-item" href="<?= base_url('persediaan/trans/ajustment_item_value') ?>"><i class="fa fa-pencil-square-o"></i> Adjustment Item (Value)</a>
                <a class="dd-item" href="<?= base_url('persediaan/trans/pmk_brng') ?>"><i class="fa fa-minus-circle"></i> Pemakaian Barang</a>
                <a class="dd-item" href="<?= base_url('persediaan/trans/pnm_barang') ?>"><i class="fa fa-plus-circle"></i> Penerimaan Barang</a>
            </div>
        </div>
    </div>

    <!-- PENJUALAN -->
    <div class="erp-col">
        <div class="erp-dropdown" style="--menu-color:#fd7e14;">
            <input type="checkbox" id="dd3" class="dd-toggle">
            <label for="dd3" class="card-erp">
                <div class="icon-erp" style="color:#fd7e14;"><i class="fa fa-bar-chart"></i></div>
                <div class="title-erp">Penjualan</div>
                <div class="count-erp">6 menu</div>
            </label>
            <div class="dd-menu">
                <div class="dd-group-label">Pre Penjualan</div>
                <a class="dd-item" href="<?= base_url('sales/presales/priceproposal') ?>"><i class="fa fa-tag"></i> Price Proposal</a>
                <a class="dd-item" href="<?= base_url('sales/presales/performainvoice') ?>"><i class="fa fa-file-o"></i> Proforma Invoice</a>
                <div class="dd-divider"></div>
                <div class="dd-group-label">Post Penjualan</div>
                <a class="dd-item" href="<?= base_url('sales/postsales/salesorder') ?>"><i class="fa fa-file-text"></i> Sales Order</a>
                <a class="dd-item" href="<?= base_url('sales/postsales/penjualan') ?>"><i class="fa fa-shopping-cart"></i> Penjualan</a>
                <a class="dd-item" href="<?= base_url('sales/postsales/soi') ?>"><i class="fa fa-file-text-o"></i> SOI</a>
                <a class="dd-item" href="<?= base_url('sales/postsales/salesorderexternal') ?>"><i class="fa fa-external-link"></i> SO External</a>
            </div>
        </div>
    </div>

    <!-- PRODUKSI -->
    <div class="erp-col">
        <div class="erp-dropdown" style="--menu-color:#198754;">
            <input type="checkbox" id="dd4" class="dd-toggle">
            <label for="dd4" class="card-erp">
                <div class="icon-erp" style="color:#198754;"><i class="fa fa-industry"></i></div>
                <div class="title-erp">Produksi</div>
                <div class="count-erp">10 menu</div>
            </label>
            <div class="dd-menu">
                <div class="dd-group-label">Transaksi</div>
                <a class="dd-item" href="<?= base_url('production/trans/standart_cost') ?>"><i class="fa fa-calculator"></i> Standart Cost</a>
                <a class="dd-item" href="<?= base_url('production/trans/biaya_standart') ?>"><i class="fa fa-dollar"></i> Biaya Standart Cost</a>
                <a class="dd-item" href="<?= base_url('production/trans/bom') ?>"><i class="fa fa-list-ol"></i> Bill of Material (BOM)</a>
                <a class="dd-item" href="<?= base_url('production/trans/workingorder') ?>"><i class="fa fa-gears"></i> Work Order</a>
                <a class="dd-item" href="<?= base_url('production/trans/woe') ?>"><i class="fa fa-play-circle"></i> Work Order Execution</a>
                <!-- <a class="dd-item" href="<?= base_url('production/trans/setorbagian') ?>"><i class="fa fa-upload"></i> Setor Antar Bagian</a>
                <a class="dd-item" href="<?= base_url('production/trans/terimabagian') ?>"><i class="fa fa-download"></i> Terima Antar Bagian</a> -->
                <a class="dd-item" href="<?= base_url('production/trans/materialrelease') ?>"><i class="fa fa-share-square-o"></i> Material Release</a>
                <a class="dd-item" href="<?= base_url('production/trans/penerimaanbp') ?>"><i class="fa fa-check-square-o"></i> Penerimaan Brg Produksi</a>
                <a class="dd-item" href="<?= base_url('production/trans/biaya_produksi_non_material') ?>"><i class="fa fa-cog"></i> Biaya Prod Non Material</a>
            </div>
        </div>
    </div>

    <!-- KEUANGAN & AKUNTANSI -->
    <div class="erp-col">
        <div class="erp-dropdown" style="--menu-color:#20c997;">
            <input type="checkbox" id="dd5" class="dd-toggle">
            <label for="dd5" class="card-erp">
                <div class="icon-erp" style="color:#20c997;"><i class="fa fa-money"></i></div>
                <div class="title-erp">Keuangan & Akuntansi</div>
                <div class="count-erp">4 menu</div>
            </label>
            <div class="dd-menu">
                <div class="dd-group-label">Accounting</div>
                <a class="dd-item" href="<?= base_url('ka/accounting/jup') ?>"><i class="fa fa-book"></i> Jurnal Umum Perkiraan</a>
                <div class="dd-divider"></div>
                <div class="dd-group-label">Finance</div>
                <a class="dd-item" href="<?= base_url('ka/finance/umt') ?>"><i class="fa fa-credit-card"></i> Uang Muka Titipan</a>
                <a class="dd-item" href="<?= base_url('ka/finance/penerimaankb') ?>"><i class="fa fa-arrow-circle-down"></i> Penerimaan Kas/Bank</a>
                <a class="dd-item" href="<?= base_url('ka/finance/pengeluarankb') ?>"><i class="fa fa-arrow-circle-up"></i> Pengeluaran Kas/Bank</a>
            </div>
        </div>
    </div>

    <!-- AR / AP -->
    <div class="erp-col">
        <div class="erp-dropdown" style="--menu-color:#dc3545;">
            <input type="checkbox" id="dd6" class="dd-toggle">
            <label for="dd6" class="card-erp">
                <div class="icon-erp" style="color:#dc3545;"><i class="fa fa-handshake-o"></i></div>
                <div class="title-erp">AR / AP</div>
                <div class="count-erp">2 menu</div>
            </label>
            <div class="dd-menu">
                <div class="dd-group-label">Transaksi</div>
                <a class="dd-item" href="<?= base_url('arap/transaksi/ndk') ?>"><i class="fa fa-exchange"></i> Nota Debit / Kredit</a>
                <div class="dd-divider"></div>
                <div class="dd-group-label">Laporan</div>
                <a class="dd-item" href="<?= base_url('arap/report/lapndk') ?>"><i class="fa fa-file-pdf-o"></i> Lap. Nota Debit & Kredit</a>
            </div>
        </div>
    </div>

    <!-- PAJAK -->
    <div class="erp-col">
        <div class="erp-dropdown" style="--menu-color:#6f42c1;">
            <input type="checkbox" id="dd7" class="dd-toggle">
            <label for="dd7" class="card-erp">
                <div class="icon-erp" style="color:#6f42c1;"><i class="fa fa-percent"></i></div>
                <div class="title-erp">Pajak</div>
                <div class="count-erp">1 menu</div>
            </label>
            <div class="dd-menu">
                <div class="dd-group-label">Laporan</div>
                <a class="dd-item" href="<?= base_url('pajak/transaksi/laporan') ?>"><i class="fa fa-file-text"></i> Laporan Pajak</a>
            </div>
        </div>
    </div>

    <!-- TOOLS -->
    <div class="erp-col">
        <div class="erp-dropdown" style="--menu-color:#6c757d;">
            <input type="checkbox" id="dd8" class="dd-toggle">
            <label for="dd8" class="card-erp">
                <div class="icon-erp" style="color:#6c757d;"><i class="fa fa-wrench"></i></div>
                <div class="title-erp">Tools</div>
                <div class="count-erp">7 menu</div>
            </label>
            <div class="dd-menu">
                <div class="dd-group-label">Setting Awal</div>
                <a class="dd-item" href="<?= base_url('tools/settingawal') ?>"><i class="fa fa-calendar"></i> Setting Tanggal Awal</a>
                <a class="dd-item" href="<?= base_url('tools/settingawal/saldoawalhp') ?>"><i class="fa fa-balance-scale"></i> Saldo Awal H/P</a>
                <a class="dd-item" href="<?= base_url('tools/settingawal/prosessaldoawalhp') ?>"><i class="fa fa-refresh"></i> Proses Saldo Awal H/P</a>
                <div class="dd-divider"></div>
                <div class="dd-group-label">Konfigurasi</div>
                <a class="dd-item" href="<?= base_url('tools/konfigurasi') ?>"><i class="fa fa-cog"></i> Konfigurasi</a>
                <a class="dd-item" href="<?= base_url('tools/konfigurasi/setting') ?>"><i class="fa fa-sliders"></i> Setting</a>
                <a class="dd-item" href="<?= base_url('tools/konfigurasi/blockunblockperiod') ?>"><i class="fa fa-lock"></i> Block/Unblock Period</a>
                <div class="dd-divider"></div>
                <div class="dd-group-label">Proses</div>
                <a class="dd-item" href="<?= base_url('tools/proses/tutupbulan') ?>"><i class="fa fa-calendar-check-o"></i> Proses Tutup Bulan</a>
            </div>
        </div>
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