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

    /* MENU AKTIF SAAT DI-CLICK */
    .dd-toggle:checked + .card-erp {
        background: var(--menu-color, #0d6efd) !important;
        color: #fff !important;
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0,0,0,.25);
        z-index: 1001;
    }

    .dd-toggle:checked + .card-erp .icon-erp,
    .dd-toggle:checked + .card-erp .title-erp {
        color: #fff !important;
    }

    .dd-toggle:checked + .card-erp .count-erp {
        color: rgba(255,255,255,.8) !important;
    }

    .erp-col:has(.dd-toggle:checked),
    .erp-dropdown:has(.dd-toggle:checked) {
        z-index: 9999 !important;
    }

    .dd-menu {
        z-index: 99999 !important;
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
        z-index: 99999 !important;
    }

    .dd-toggle:checked + label + .dd-menu {
        display: block !important;
    }

    /* =========================================
   FIX DROPDOWN TERPOTONG
   ========================================= */

    #app-zoom,
    #main-wrapper,
    .page-wrapper,
    .container-fluid,
    .erp-menu-row,
    .erp-col,
    .erp-dropdown {
        overflow: visible !important;
    }

    /* Menu yang sedang aktif paling depan */
    .erp-col:has(.dd-toggle:checked),
    .erp-dropdown:has(.dd-toggle:checked) {
        position: relative !important;
        z-index: 99999 !important;
    }

    /* Dropdown */
    .dd-menu {
        z-index: 999999 !important;
    }

    /* Card aktif */
    .dd-toggle:checked + .card-erp {
        position: relative;
        z-index: 100000 !important;
    }

    /* Jangan paksa dropdown tertutup */
    .dd-toggle:checked + label + .dd-menu {
        display: block !important;
    }
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
     MENU GRID — digenerate dari sc_mst.menuprg + sc_mst.role_permissions
       card = menu root (child='U') | grup = child='S' | item = child='P'
       hanya menu dengan a_view='t' untuk role yang login yang tampil
══════════════════════════════════════ -->
<?php
$erpCards = isset($menu_cards) ? $menu_cards : [];
$erpItems = isset($menu_items) ? $menu_items : [];

/* warna card per kodemenu root (agar stabil walau urutan menuprg berubah) */
$erpColors = [
        'I.M' => '#0d6efd', 'I.P' => '#0d6efd', 'I.S' => '#fd7e14', 'I.Q' => '#0dcaf0',
        'I.R' => '#198754', 'I.J' => '#6f42c1', 'I.L' => '#dc3545', 'I.K' => '#20c997',
        'I.T' => '#6c757d', 'I.H' => '#ff00a1',
];
$erpPalette = ['#0d6efd','#fd7e14','#0dcaf0','#198754','#20c997','#dc3545','#6f42c1','#6c757d','#ff00a1','#17a2b8'];

/* menuprg disimpan HURUF BESAR -> tampilkan Title Case,
   singkatan (PP, PO, SO, AR, AP, ...) tetap kapital */
if (!function_exists('erp_menu_title')) {
    function erp_menu_title($s)
    {
        $acr   = ['PP','PO','SO','SOI','DO','WO','AR','AP','COA','BOM','PPH','PPN','XLS','PDF','NDK',
                'KB','UMT','JUP','BRG','H/P','P/L'];
        $kecil = ['of','and','or','the','to','in','on','at','by','for','per','dan','di','ke','dengan','yang'];

        $t = ucwords(strtolower(trim((string) $s)), " \t\r\n\f\v(/-&.");
        foreach ($acr as $a) {
            $t = preg_replace('/\b' . preg_quote($a, '/') . '\b/i', $a, $t);
        }
        /* kata sambung tetap huruf kecil (kecuali di awal kalimat) */
        $t = preg_replace_callback('/\s(\w+)/', function ($m) use ($kecil) {
            return ' ' . (in_array(strtolower($m[1]), $kecil, true) ? strtolower($m[1]) : $m[1]);
        }, $t);
        return $t;
    }
}

/* kelompokkan item: parentmenu -> parentsub (urutan sudah diatur query) */
$erpGrouped = [];
foreach ($erpItems as $it) {
    $erpGrouped[trim($it->parentmenu)][trim($it->parentsub)][] = $it;
}
?>
<div class="erp-menu-row">
    <?php if (empty($erpCards)): ?>
        <div class="erp-col" style="max-width:100%;flex:1 1 100%;">
            <div class="card-erp" style="cursor:default;">
                <div class="icon-erp" style="color:#adb5bd;"><i class="fa fa-lock"></i></div>
                <div class="title-erp">Tidak ada menu yang bisa diakses</div>
                <div class="count-erp">Hubungi administrator untuk pengaturan role</div>
            </div>
        </div>
    <?php else: ?>
        <?php $ci = 0; foreach ($erpCards as $card): ?>
            <?php
            $ck     = trim($card->kodemenu);
            $color  = isset($erpColors[$ck]) ? $erpColors[$ck] : $erpPalette[$ci % count($erpPalette)];
            $ddId   = 'dd' . $ci;
            $groups = isset($erpGrouped[$ck]) ? $erpGrouped[$ck] : [];
            $total  = 0;
            foreach ($groups as $g) { $total += count($g); }
            $cicon  = trim((string) $card->iconmenu) !== '' ? trim($card->iconmenu) : 'fa-folder';
            ?>
            <!-- <?= trim($card->namamenu) ?> -->
            <div class="erp-col">
                <div class="erp-dropdown" style="--menu-color:<?= $color ?>;">
                    <input type="checkbox" id="<?= $ddId ?>" class="dd-toggle">
                    <label for="<?= $ddId ?>" class="card-erp">
                        <div class="icon-erp" style="color:<?= $color ?>;"><i class="fa <?= $cicon ?>"></i></div>
                        <div class="title-erp"><?= erp_menu_title($card->namamenu) ?></div>
                        <div class="count-erp"><?= $total ?> menu</div>
                    </label>
                    <div class="dd-menu">
                        <?php $first = true; foreach ($groups as $subKode => $rows): ?>
                            <?php if (!$first): ?>                <div class="dd-divider"></div>
                            <?php endif; $first = false; ?>
                            <?php $subLabel = trim((string) ($rows[0]->namasub ?? '')) !== '' ? $rows[0]->namasub : $subKode; ?>
                            <div class="dd-group-label"><?= erp_menu_title($subLabel) ?></div>
                            <?php foreach ($rows as $row): ?>
                                <?php
                                $link  = trim((string) $row->linkmenu);
                                $href  = ($link === '' || $link === '#') ? '#' : base_url($link);
                                $ricon = trim((string) $row->iconmenu) !== '' ? trim($row->iconmenu) : 'fa-circle-o';
                                ?>
                                <a class="dd-item" href="<?= $href ?>"><i class="fa <?= $ricon ?>"></i> <?= erp_menu_title($row->namamenu) ?></a>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php $ci++; endforeach; ?>
    <?php endif; ?>
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