<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Web\Login::index');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//NEX PROJECT DISABLING AUTO ROUTES
$routes->get('/rumah', 'Home::index');
$routes->get('/', 'Web\Login::index');
$routes->get('/login', 'Web\Login::index');
$routes->post('/web/login/proses', 'Web\Login::proses');
$routes->get('/lock', 'Web\Relogin::index');
$routes->get('/show', 'Web\Relogin::endDate');
$routes->add('/profile', 'Web\Profile::index');
$routes->post('/profile/saveprofile', 'Web\Profile::saveprofile');

/* DASHBOARD GROUP ROUTE */
$routes->group('dashboard',["namespace" => "App\Controllers\Dashboard"], function ($routes) {
    $routes->add('/', 'Dashboard::index');
    $routes->add('menu', 'Dashboard::menu');
    $routes->add('api_summary_pembelian', 'Dashboard::api_summary_pembelian');
    $routes->add('api_transaction_tahunan', 'Dashboard::api_transaction_tahunan');

    $routes->add('link_dashboard_total_karyawan(:any)', 'Dashboard::link_dashboard_total_karyawan$1');
    $routes->add('link_dashboard_vaksin(:any)', 'Dashboard::link_dashboard_vaksin$1');
    $routes->add('link_dashboard_covid(:any)', 'Dashboard::link_dashboard_covid$1');

    $routes->add('list_minstock', 'Dashboard::list_minstock');
    $routes->add('minstock', 'Dashboard::minstock');

    $routes->add('list_jasapembayaran', 'Dashboard::list_jasapembayaran');

    $routes->get('logout', 'Dashboard::logout');
});

$routes->group('dashboarduser',["namespace" => "App\Controllers\Dashboard"], function ($routes) {
    $routes->add('/', 'DashboardUser::index');
    // $routes->add('api_summary_pembelian', 'DashboardUser::api_summary_pembelian');
    // $routes->add('api_transaction_tahunan', 'DashboardUser::api_transaction_tahunan');

    // $routes->add('link_dashboard_total_karyawan(:any)', 'DashboardUser::link_dashboard_total_karyawan$1');
    // $routes->add('link_dashboard_vaksin(:any)', 'DashboardUser::link_dashboard_vaksin$1');
    // $routes->add('link_dashboard_covid(:any)', 'DashboardUser::link_dashboard_covid$1');

    $routes->add('list_formpp', 'DashboardUser::list_formpp');
    $routes->add('list_formii', 'DashboardUser::list_formii');
    $routes->add('list_formpk', 'DashboardUser::list_formpk');

    // $routes->add('minstock', 'DashboardUser::minstock');

    // $routes->add('list_jasapembayaran', 'DashboardUser::list_jasapembayaran');

    // $routes->get('logout', 'DashboardUser::logout');
});

//fix independent routes
//$routes->get("user/editprofile/(:any)/(:any)", "User::editProfile/$1/$2", ["namespace" => "App\Controllers\Master"]);
/* USER GROUP ROUTE*/
$routes->group('user', ["namespace" => "App\Controllers\Master"], function ($routes) {
    $routes->add('/', 'User::index');
    $routes->get("editprofile/(:any)/(:any)", "User::editProfile/$1/$2");
    $routes->post("saveprofile", "User::saveprofile");
    $routes->get("edit/(:any)/(:any)", "User::edit/$1/$2");
    $routes->get("hps/(:any)/(:any)", "User::hps/$1/$2");
});

//sidebar menu
$routes->get('/master/user', 'Master\User::index');
$routes->add('master/user/list_user', 'Master\User::list_user');
$routes->post('master/user/save', 'Master\User::save');
//menu
$routes->group('/master/menu', ["namespace" => "App\Controllers\Master"], function ($routes) {
    $routes->add('/', 'Menu::index');
    $routes->post('save', 'Menu::save');
    $routes->get("hps/(:any)", "Menu::hps/$1");
    $routes->get("edit/(:any)", "Menu::edit/$1");
});
$routes->group('/master/location', ["namespace" => "App\Controllers\Master"], function ($routes) {
    $routes->add('/', 'Location::index');
    $routes->add('list_mlocation', 'Location::list_mlocation');
    $routes->post("saveEntry", "Location::saveEntry");

    $routes->get("showing_data/(:any)", "Location::showing_data/$1");

    $routes->add('area', 'Location::area');
    $routes->add('list_marea', 'Location::list_marea');
    $routes->post("saveEntryArea", "Location::saveEntryArea");
    $routes->get("showing_data_area/(:any)", "Location::showing_data_area/$1");
    $routes->add('import_area', 'Location::import_area');
    $routes->post("proses_upload", "Location::proses_upload");
    $routes->add("clear_tmp", "Location::clear_tmp");
    $routes->add("final_data", "Location::final_data");
    $routes->add("showlabels", "Location::showlabels");
    $routes->add("api_show_showlabels_area(:any)", "Location::api_show_showlabels_area$1");
    $routes->add("show_showlabels_area", "Location::show_showlabels_area");
    $routes->add("cc", "Location::cc");
    $routes->add("list_costcenter", "Location::list_costcenter");
    $routes->post("saveCostCenter", "Location::saveCostCenter");
    $routes->get("showing_data_costcenter(:any)", "Location::showing_data_costcenter$1");
    $routes->post("show_showlabels_area_partial", "Location::show_showlabels_area_partial");
    $routes->get("api_show_showlabels_area_partial(:any)", "Location::api_show_showlabels_area_partial$1");

});
//Route Role
$routes->group('/master/role', ["namespace" => "App\Controllers\Master"], function ($routes) {
    $routes->add('/', 'Role::index');
    $routes->post("list_mrole", "Role::list_mrole");
    $routes->get("access_permission(:any)", "Role::access_permission$1");
    $routes->post("tambah_menu", "Role::tambah_menu");
    $routes->post("kurangi_menu", "Role::kurangi_menu");
    $routes->get("add_menugrid/(:any)", "Role::add_menugrid/$1");
    $routes->get("list_access_permission(:any)", "Role::list_access_permission$1");
    $routes->get("edit_akses/(:any)", "Role::edit_akses/$1");
    $routes->get("hps_akses/(:any)", "Role::hps_akses/$1");
    $routes->post("save_akses", "Role::save_akses");
    $routes->post("saveEntry", "Role::saveEntry");
    $routes->add("showing_data/(:any)", "Role::showing_data/$1");
});


$routes->group('/arap/report', ["namespace" => "App\Controllers\Arap"], function ($routes) {
    $routes->add('lapndk', 'Arap::lapndk');
    $routes->add('list_lapndk', 'Arap::list_lapndk');
});

$routes->group('/arap/transaksi', ["namespace" => "App\Controllers\Arap"], function ($routes) {
    
    $routes->add('ndk', 'Arap::ndk');
    $routes->add('list_ndk', 'Arap::list_ndk');
    $routes->add('list_ndk_apprv', 'Arap::list_ndk_apprv');

    $routes->add('addNDK', 'Arap::addNDK');
    $routes->add('detailNDK', 'Arap::detailNDK');
    $routes->add('list_tmp_ndk_dtl', 'Arap::list_tmp_ndk_dtl');
    // $routes->add('showing_sikbsp_mst', 'Arap::showing_sikbsp_mst');

    $routes->add('clearEntryNDK', 'Arap::clearEntryNDK');
    $routes->add('finalEntryNDK', 'Arap::finalEntryNDK');
    $routes->add('showing_ndktemp', 'Arap::showing_ndktemp');
    $routes->add('updateStatusNDK', 'Arap::updateStatusNDK');


    $routes->add('save_ndk_detail', 'Arap::save_ndk_detail');
    $routes->get('updateNDK(:any)', 'Arap::updateNDK$1');

    $routes->add('deleteNDKDtl', 'Arap::deleteNDKDtl');
    $routes->add('show_ndk', 'Arap::show_ndk');
    $routes->add('api_ndk(:any)', 'Arap::api_ndk$1');
    $routes->add('list_trx_ndk_dtl', 'Arap::list_trx_ndk_dtl');
    $routes->add('showing_ndktrx', 'Arap::showing_ndktrx');
    $routes->add('getBranchInfoNDK', 'Arap::getBranchInfoNDK');
    $routes->add('getNextSuffixNDK', 'Arap::getNextSuffixNDK');
    $routes->add('initNDKHeader', 'Arap::initNDKHeader');
    $routes->add('saveNDKDetail', 'Arap::saveNDKDetail');
    $routes->add("get_ndk_detail(:any)", "Arap::get_ndk_detail$1");
    $routes->add("delete_ndk_detail", "Arap::delete_ndk_detail");

});



$routes->group('/ka/accounting', ["namespace" => "App\Controllers\Finance"], function ($routes) {

    $routes->add('jup', 'Finance::jup');
    $routes->add('list_jup', 'Finance::list_jup');
    $routes->add('list_jup_apprv', 'Finance::list_jup_apprv');

    $routes->add('addJUP', 'Finance::addJUP');
    $routes->add('detailJUP', 'Finance::detailJUP');
    $routes->add('list_tmp_jup_dtl', 'Finance::list_tmp_jup_dtl');
    // $routes->add('showing_sikbsp_mst', 'Finance::showing_sikbsp_mst');

    $routes->add('clearEntryJUP', 'Finance::clearEntryJUP');
    $routes->add('finalEntryJUP', 'Finance::finalEntryJUP');
    $routes->add('finalEntryJUP_DP', 'Finance::finalEntryJUP_DP');
    $routes->add('showing_juptemp', 'Finance::showing_juptemp');
    $routes->add('updateStatusJUP', 'Finance::updateStatusJUP');


    $routes->add('save_jup_detail', 'Finance::save_jup_detail');
    $routes->get('updateJUP(:any)', 'Finance::updateJUP$1');

    $routes->add('deleteJUPDtl', 'Finance::deleteJUPDtl');
    $routes->add('show_jup', 'Finance::show_jup');
    $routes->add('api_jup(:any)', 'Finance::api_jup$1');
    $routes->add('list_trx_jup_dtl', 'Finance::list_trx_jup_dtl');
    $routes->add('showing_juptrx', 'Finance::showing_juptrx');
    $routes->add('getBranchInfoJUP', 'Finance::getBranchInfoJUP');
    $routes->add('getNextSuffixJUP', 'Finance::getNextSuffixJUP');
    $routes->add('initJUPHeader', 'Finance::initJUPHeader');
    $routes->add('saveJUPDetail', 'Finance::saveJUPDetail');
    $routes->add("get_jup_detail(:any)", "Finance::get_jup_detail$1");
    $routes->add("delete_jup_detail", "Finance::delete_jup_detail");

});


$routes->group('/ka/finance', ["namespace" => "App\Controllers\Finance"], function ($routes) {

    $routes->add('umt', 'Finance::umt');
    $routes->add('list_umt', 'Finance::list_umt');
    $routes->add('list_umt_apprv', 'Finance::list_umt_apprv');

    $routes->add('addUMT', 'Finance::addUMT');
    $routes->add('detailUMT', 'Finance::detailUMT');
    // $routes->add('showing_sikbsp_mst', 'Finance::showing_sikbsp_mst');

    $routes->add('clearEntryUMT', 'Finance::clearEntryUMT');
    $routes->add('finalEntryUMT', 'Finance::finalEntryUMT');
    $routes->add('showing_umttemp', 'Finance::showing_umttemp');
    $routes->add('updateStatusUMT', 'Finance::updateStatusUMT');


    $routes->add('save_umt_detail', 'Finance::save_umt_detail');
    $routes->get('updateUMT(:any)', 'Finance::updateUMT$1');

    $routes->add('deleteUMTDtl', 'Finance::deleteUMTDtl');
    $routes->add('show_umt', 'Finance::show_umt');
    $routes->add('api_umt(:any)', 'Finance::api_umt$1');
    $routes->add('showing_umttrx', 'Finance::showing_umttrx');
    $routes->add('getBranchInfoUMT', 'Finance::getBranchInfoUMT');
    $routes->add('getNextSuffixUMT', 'Finance::getNextSuffixUMT');
    $routes->add('initUMTHeader', 'Finance::initUMTHeader');
    $routes->add('saveUMTDetail', 'Finance::saveUMTDetail');





    $routes->add('penerimaankb', 'Finance::penerimaankb');
    $routes->add('list_penerimaankb', 'Finance::list_penerimaankb');
    $routes->add('list_penerimaankb_apprv', 'Finance::list_penerimaankb_apprv');

    $routes->add('getPenjualanCust', 'Finance::getPenjualanCust');
    $routes->add('addPenerimaanKB', 'Finance::addPenerimaanKB');
    $routes->add('detailPenerimaanKB', 'Finance::detailPenerimaanKB');
    $routes->add('list_tmp_penerimaankb_dtl', 'Finance::list_tmp_penerimaankb_dtl');
    // $routes->add('showing_sikbsp_mst', 'Finance::showing_sikbsp_mst');

    $routes->add('clearEntryPenerimaanKB', 'Finance::clearEntryPenerimaanKB');
    $routes->add('finalEntryPenerimaanKB', 'Finance::finalEntryPenerimaanKB');
    $routes->add('showing_penerimaankbtemp', 'Finance::showing_penerimaankbtemp');
    $routes->add('updateStatusPenerimaanKB', 'Finance::updateStatusPenerimaanKB');


    $routes->add('save_penerimaankb_detail', 'Finance::save_penerimaankb_detail');
    $routes->get('updatePenerimaanKB(:any)', 'Finance::updatePenerimaanKB$1');

    $routes->add('update_status_penerimaankb_dtl', 'Finance::update_status_penerimaankb_dtl');
    $routes->add('deletePenerimaanKBDtl', 'Finance::deletePenerimaanKBDtl');
    $routes->add('show_penerimaankb', 'Finance::show_penerimaankb');
    $routes->add('api_penerimaankb(:any)', 'Finance::api_penerimaankb$1');
    $routes->add('list_trx_penerimaankb_dtl', 'Finance::list_trx_penerimaankb_dtl');
    $routes->add('showing_penerimaankbtrx', 'Finance::showing_penerimaankbtrx');
    $routes->add('getBranchInfoPenerimaanKB', 'Finance::getBranchInfoPenerimaanKB');
    $routes->add('getNextSuffixPenerimaanKB', 'Finance::getNextSuffixPenerimaanKB');
    $routes->add('initPenerimaanKBHeader', 'Finance::initPenerimaanKBHeader');
    $routes->add('savePenerimaanKBDetail', 'Finance::savePenerimaanKBDetail');
    $routes->add("get_penerimaankb_detail(:any)", "Finance::get_penerimaankb_detail$1");
    $routes->add("delete_penerimaankb_detail", "Finance::delete_penerimaankb_detail");






    


    $routes->add('pengeluarankb', 'Finance::pengeluarankb');
    $routes->add('list_pengeluarankb', 'Finance::list_pengeluarankb');
    $routes->add('list_pengeluarankb_apprv', 'Finance::list_pengeluarankb_apprv');

    $routes->add('getPembelianSup', 'Finance::getPembelianSup');
    $routes->add('addPengeluaranKB', 'Finance::addPengeluaranKB');
    $routes->add('detailPengeluaranKB', 'Finance::detailPengeluaranKB');
    $routes->add('list_tmp_pengeluarankb_dtl', 'Finance::list_tmp_pengeluarankb_dtl');
    // $routes->add('showing_sikbsp_mst', 'Finance::showing_sikbsp_mst');

    $routes->add('clearEntryPengeluaranKB', 'Finance::clearEntryPengeluaranKB');
    $routes->add('finalEntryPengeluaranKB', 'Finance::finalEntryPengeluaranKB');
    $routes->add('showing_pengeluarankbtemp', 'Finance::showing_pengeluarankbtemp');
    $routes->add('updateStatusPengeluaranKB', 'Finance::updateStatusPengeluaranKB');


    $routes->add('save_pengeluarankb_detail', 'Finance::save_pengeluarankb_detail');
    $routes->get('updatePengeluaranKB(:any)', 'Finance::updatePengeluaranKB$1');

    $routes->add('update_status_pengeluarankb_dtl', 'Finance::update_status_pengeluarankb_dtl');
    $routes->add('deletePengeluaranKBDtl', 'Finance::deletePengeluaranKBDtl');
    $routes->add('show_pengeluarankb', 'Finance::show_pengeluarankb');
    $routes->add('api_pengeluarankb(:any)', 'Finance::api_pengeluarankb$1');
    $routes->add('list_trx_pengeluarankb_dtl', 'Finance::list_trx_pengeluarankb_dtl');
    $routes->add('showing_pengeluarankbtrx', 'Finance::showing_pengeluarankbtrx');
    $routes->add('getBranchInfoPengeluaranKB', 'Finance::getBranchInfoPengeluaranKB');
    $routes->add('getNextSuffixPengeluaranKB', 'Finance::getNextSuffixPengeluaranKB');
    $routes->add('initPengeluaranKBHeader', 'Finance::initPengeluaranKBHeader');
    $routes->add('savePengeluaranKBDetail', 'Finance::savePengeluaranKBDetail');
    $routes->add("get_pengeluarankb_detail(:any)", "Finance::get_pengeluarankb_detail$1");
    $routes->add("delete_pengeluarankb_detail", "Finance::delete_pengeluarankb_detail");


});


$routes->group('/tools/settingawal', ["namespace" => "App\Controllers\Tools"], function ($routes) {
    $routes->add('/', 'Tools::index');
    $routes->add('processTglAwal', 'Tools::processTglAwal');


    $routes->add('saldoawalhp', 'Tools::saldoawalhp');
    $routes->add('list_saldoawalhp', 'Tools::list_saldoawalhp');
    $routes->add('list_saldoawalhp_apprv', 'Tools::list_saldoawalhp_apprv');

    $routes->add('addSAHP', 'Tools::addSAHP');
    $routes->add('detailSAHP', 'Tools::detailSAHP');
    $routes->add('list_tmp_saldoawalhp_dtl', 'Tools::list_tmp_saldoawalhp_dtl');
    // $routes->add('showing_sikbsp_mst', 'Tools::showing_sikbsp_mst');

    $routes->add('clearEntrySAHP', 'Tools::clearEntrySAHP');
    $routes->add('finalEntrySAHP', 'Tools::finalEntrySAHP');
    $routes->add('showing_saldoawalhptemp', 'Tools::showing_saldoawalhptemp');
    $routes->add('updateStatusSAHP', 'Tools::updateStatusSAHP');


    $routes->add('save_saldoawalhp_detail', 'Tools::save_saldoawalhp_detail');
    $routes->get('updateSAHP(:any)', 'Tools::updateSAHP$1');

    $routes->add('deleteSAHPDtl', 'Tools::deleteSAHPDtl');
    $routes->add('show_saldoawalhp', 'Tools::show_saldoawalhp');
    $routes->add('api_saldoawalhp(:any)', 'Tools::api_saldoawalhp$1');
    $routes->add('list_trx_saldoawalhp_dtl', 'Tools::list_trx_saldoawalhp_dtl');
    $routes->add('showing_saldoawalhptrx', 'Tools::showing_saldoawalhptrx');
    $routes->add('getBranchInfoSAHP', 'Tools::getBranchInfoSAHP');
    $routes->add('getNextSuffixSAHP', 'Tools::getNextSuffixSAHP');
    $routes->add('initSAHPHeader', 'Tools::initSAHPHeader');
    $routes->add('saveSAHPDetail', 'Tools::saveSAHPDetail');
    $routes->add("get_saldoawalhp_detail(:any)", "Tools::get_saldoawalhp_detail$1");
    $routes->add("delete_saldoawalhp_detail", "Tools::delete_saldoawalhp_detail");





    $routes->add('prosessaldoawalhp', 'Tools::prosessaldoawalhp');
    $routes->add('prosesSAHP', 'Tools::prosesSAHP');

});



$routes->group('/tools/konfigurasi', ["namespace" => "App\Controllers\Tools"], function ($routes) {
    $routes->add('/', 'Tools::konfigurasi');
    $routes->add('updateKonfigurasi', 'Tools::updateKonfigurasi');
    $routes->add('showing_konfigurasimst', 'Tools::showing_konfigurasimst');

    $routes->add('blockunblockperiod', 'Tools::blockunblockperiod');
    $routes->add('processClosePeriod', 'Tools::processClosePeriod');

});




$routes->group('/tools/proses', ["namespace" => "App\Controllers\Tools"], function ($routes) {
    $routes->add('tutupbulan', 'Tools::tutupbulan');
    $routes->add('processTutupBulan', 'Tools::processTutupBulan');

});




$routes->group('/pajak/transaksi', ["namespace" => "App\Controllers\Pajak"], function ($routes) {
    $routes->add('laporan', 'Pajak::laporan');
    $routes->add('list_lappajak', 'Pajak::list_lappajak');

});


$routes->group('/purchase/trans', ["namespace" => "App\Controllers\Purchase"], function ($routes) {


    $routes->add('pp', 'Purchase::pp');
    $routes->add('list_pp', 'Purchase::list_pp');
    $routes->add('list_pp_apprv', 'Purchase::list_pp_apprv');

    $routes->add('addPP', 'Purchase::addPP');
    $routes->add('detailPP', 'Purchase::detailPP');
    $routes->add('list_tmp_pp_dtl', 'Purchase::list_tmp_pp_dtl');
    $routes->add('updateStatusPP', 'Purchase::updateStatusPP');
    // $routes->add('showing_sikbsp_mst', 'Purchase::showing_sikbsp_mst');

    $routes->add('clearEntryPP', 'Purchase::clearEntryPP');
    $routes->add('finalEntryPP', 'Purchase::finalEntryPP');
    $routes->add('showing_pptemp', 'Purchase::showing_pptemp');

    $routes->add('save_pp_detail', 'Purchase::save_pp_detail');
    $routes->get('updatePP(:any)', 'Purchase::updatePP$1');

    $routes->add('deletePPDtl', 'Purchase::deletePPDtl');
    $routes->add('show_pp', 'Purchase::show_pp');
    $routes->add('api_pp(:any)', 'Purchase::api_pp$1');
    $routes->add('list_trx_pp_dtl', 'Purchase::list_trx_pp_dtl');
    $routes->add('showing_pptrx', 'Purchase::showing_pptrx');
    $routes->add('getBranchInfo', 'Purchase::getBranchInfo');
    $routes->add('getNextSuffixPP', 'Purchase::getNextSuffixPP');
    $routes->add('initPPHeader', 'Purchase::initPPHeader');
    $routes->add('savePPDetail', 'Purchase::savePPDetail');
    $routes->add("get_pp_detail(:any)", "Purchase::get_pp_detail$1");
    $routes->add("delete_pp_detail", "Purchase::delete_pp_detail");






    $routes->add('voidpp', 'Purchase::voidpp');
    $routes->add('list_voidpp', 'Purchase::list_voidpp');
    $routes->add('addVoidPP', 'Purchase::addVoidPP');
    $routes->add('detailVoidPP', 'Purchase::detailVoidPP');
    $routes->add('list_tmp_voidpp_dtl', 'Purchase::list_tmp_voidpp_dtl');
    // $routes->add('showing_sikbsp_mst', 'Purchase::showing_sikbsp_mst');

    $routes->add('clearEntryVoidPP', 'Purchase::clearEntryVoidPP');
    $routes->add('finalEntryVoidPP', 'Purchase::finalEntryVoidPP');
    $routes->add('showing_voidpptemp', 'Purchase::showing_voidpptemp');

    $routes->add('save_voidpp_detail', 'Purchase::save_voidpp_detail');
    $routes->get('updateVoidPP(:any)', 'Purchase::updateVoidPP$1');

    $routes->add('deleteVoidPPDtl', 'Purchase::deleteVoidPPDtl');
    $routes->add('show_voidpp', 'Purchase::show_voidpp');
    $routes->add('api_voidpp(:any)', 'Purchase::api_voidpp$1');
    $routes->add('list_trx_voidpp_dtl', 'Purchase::list_trx_voidpp_dtl');
    $routes->add('showing_voidpptrx', 'Purchase::showing_voidpptrx');
    $routes->add('getBranchInfoVoid', 'Purchase::getBranchInfoVoid');
    $routes->add('getNextSuffixVoidPP', 'Purchase::getNextSuffixVoidPP');
    $routes->add('initVoidPPHeader', 'Purchase::initVoidPPHeader');
    $routes->add('saveVoidPPDetail', 'Purchase::saveVoidPPDetail');
    $routes->add("get_voidpp_detail(:any)", "Purchase::get_voidpp_detail$1");
    $routes->add("delete_voidpp_detail", "Purchase::delete_voidpp_detail");




    $routes->add('po', 'Purchase::po');
    $routes->add('list_po', 'Purchase::list_po');
    $routes->add('list_po_apprv', 'Purchase::list_po_apprv');

    $routes->add('addPO', 'Purchase::addPO');
    $routes->add('detailPO', 'Purchase::detailPO');
    $routes->add('list_tmp_po_dtl', 'Purchase::list_tmp_po_dtl');
    // $routes->add('showing_sikbsp_mst', 'Purchase::showing_sikbsp_mst');

    $routes->add('clearEntryPO', 'Purchase::clearEntryPO');
    $routes->add('finalEntryPO', 'Purchase::finalEntryPO');
    $routes->add('finalEntryPO_DP', 'Purchase::finalEntryPO_DP');
    $routes->add('showing_potemp', 'Purchase::showing_potemp');
    $routes->add('updateStatusPO', 'Purchase::updateStatusPO');


    $routes->add('save_po_detail', 'Purchase::save_po_detail');
    $routes->get('updatePO(:any)', 'Purchase::updatePO$1');

    $routes->add('deletePODtl', 'Purchase::deletePODtl');
    $routes->add('show_po', 'Purchase::show_po');
    $routes->add('api_po(:any)', 'Purchase::api_po$1');
    $routes->add('list_trx_po_dtl', 'Purchase::list_trx_po_dtl');
    $routes->add('showing_potrx', 'Purchase::showing_potrx');
    $routes->add('getBranchInfoPO', 'Purchase::getBranchInfoPO');
    $routes->add('getNextSuffixPO', 'Purchase::getNextSuffixPO');
    $routes->add('initPOHeader', 'Purchase::initPOHeader');
    $routes->add('savePODetail', 'Purchase::savePODetail');
    $routes->add("get_po_detail(:any)", "Purchase::get_po_detail$1");
    $routes->add("delete_po_detail", "Purchase::delete_po_detail");





    $routes->add('voidpo', 'Purchase::voidpo');
    $routes->add('list_voidpo', 'Purchase::list_voidpo');
    $routes->add('list_voidpo_apprv', 'Purchase::list_voidpo_apprv');

    $routes->add('addVoidPO', 'Purchase::addVoidPO');
    $routes->add('detailVoidPO', 'Purchase::detailVoidPO');
    $routes->add('list_tmp_voidpo_dtl', 'Purchase::list_tmp_voidpo_dtl');
    // $routes->add('showing_sikbsp_mst', 'Purchase::showing_sikbsp_mst');

    $routes->add('clearEntryVoidPO', 'Purchase::clearEntryVoidPO');
    $routes->add('finalEntryVoidPO', 'Purchase::finalEntryVoidPO');
    $routes->add('showing_voidpotemp', 'Purchase::showing_voidpotemp');
    $routes->add('updateStatusVoidPO', 'Purchase::updateStatusVoidPO');


    $routes->add('save_voidpo_detail', 'Purchase::save_voidpo_detail');
    $routes->get('updateVoidPO(:any)', 'Purchase::updateVoidPO$1');

    $routes->add('deleteVoidPODtl', 'Purchase::deleteVoidPODtl');
    $routes->add('show_voidpo', 'Purchase::show_voidpo');
    $routes->add('api_voidpo(:any)', 'Purchase::api_voidpo$1');
    $routes->add('list_trx_voidpo_dtl', 'Purchase::list_trx_voidpo_dtl');
    $routes->add('showing_voidpotrx', 'Purchase::showing_voidpotrx');
    $routes->add('getBranchInfoVoidPO', 'Purchase::getBranchInfoVoidPO');
    $routes->add('getNextSuffixVoidPO', 'Purchase::getNextSuffixVoidPO');
    $routes->add('initVoidPOHeader', 'Purchase::initVoidPOHeader');
    $routes->add('saveVoidPODetail', 'Purchase::saveVoidPODetail');
    $routes->add("get_voidpo_detail(:any)", "Purchase::get_voidpo_detail$1");
    $routes->add("delete_voidpo_detail", "Purchase::delete_voidpo_detail");



    $routes->add('umb', 'Purchase::umb');
    $routes->add('list_umb', 'Purchase::list_umb');
    $routes->add('list_umb_apprv', 'Purchase::list_umb_apprv');

    $routes->add('addUMB', 'Purchase::addUMB');
    $routes->add('detailUMB', 'Purchase::detailUMB');
    // $routes->add('showing_sikbsp_mst', 'Purchase::showing_sikbsp_mst');

    $routes->add('clearEntryUMB', 'Purchase::clearEntryUMB');
    $routes->add('finalEntryUMB', 'Purchase::finalEntryUMB');
    $routes->add('showing_umbtemp', 'Purchase::showing_umbtemp');
    $routes->add('updateStatusUMB', 'Purchase::updateStatusUMB');


    $routes->add('save_umb_detail', 'Purchase::save_umb_detail');
    $routes->get('updateUMB(:any)', 'Purchase::updateUMB$1');

    $routes->add('deleteUMBDtl', 'Purchase::deleteUMBDtl');
    $routes->add('show_umb', 'Purchase::show_umb');
    $routes->add('api_umb(:any)', 'Purchase::api_umb$1');
    $routes->add('showing_umbtrx', 'Purchase::showing_umbtrx');
    $routes->add('getBranchInfoUMB', 'Purchase::getBranchInfoUMB');
    $routes->add('getNextSuffixUMB', 'Purchase::getNextSuffixUMB');
    $routes->add('initUMBHeader', 'Purchase::initUMBHeader');
    $routes->add('saveUMBDetail', 'Purchase::saveUMBDetail');



    $routes->add('lpb', 'Purchase::lpb');
    $routes->add('list_lpb', 'Purchase::list_lpb');
    $routes->add('list_lpb_apprv', 'Purchase::list_lpb_apprv');

    $routes->add('addLPB', 'Purchase::addLPB');
    $routes->add('detailLPB', 'Purchase::detailLPB');
    $routes->add('list_tmp_lpb_dtl', 'Purchase::list_tmp_lpb_dtl');
    // $routes->add('showing_sikbsp_mst', 'Purchase::showing_sikbsp_mst');

    $routes->add('clearEntryLPB', 'Purchase::clearEntryLPB');
    $routes->add('finalEntryLPB', 'Purchase::finalEntryLPB');
    $routes->add('showing_lpbtemp', 'Purchase::showing_lpbtemp');
    $routes->add('updateStatusLPB', 'Purchase::updateStatusLPB');


    $routes->add('save_lpb_detail', 'Purchase::save_lpb_detail');
    $routes->get('updateLPB(:any)', 'Purchase::updateLPB$1');

    $routes->add('deleteLPBDtl', 'Purchase::deleteLPBDtl');
    $routes->add('show_lpb', 'Purchase::show_lpb');
    $routes->add('api_lpb(:any)', 'Purchase::api_lpb$1');
    $routes->add('list_trx_lpb_dtl', 'Purchase::list_trx_lpb_dtl');
    $routes->add('showing_lpbtrx', 'Purchase::showing_lpbtrx');
    $routes->add('getBranchInfoLPB', 'Purchase::getBranchInfoLPB');
    $routes->add('getNextSuffixLPB', 'Purchase::getNextSuffixLPB');
    $routes->add('initLPBHeader', 'Purchase::initLPBHeader');
    $routes->add('saveLPBDetail', 'Purchase::saveLPBDetail');
    $routes->add("get_lpb_detail(:any)", "Purchase::get_lpb_detail$1");
    $routes->add("delete_lpb_detail", "Purchase::delete_lpb_detail");




    $routes->add('returbeli', 'Purchase::returbeli');
    $routes->add('list_returbeli', 'Purchase::list_returbeli');
    $routes->add('list_returbeli_apprv', 'Purchase::list_returbeli_apprv');

    $routes->add('addReturBeli', 'Purchase::addReturBeli');
    $routes->add('detailReturBeli', 'Purchase::detailReturBeli');
    $routes->add('list_tmp_returbeli_dtl', 'Purchase::list_tmp_returbeli_dtl');
    // $routes->add('showing_sikbsp_mst', 'Purchase::showing_sikbsp_mst');

    $routes->add('clearEntryReturBeli', 'Purchase::clearEntryReturBeli');
    $routes->add('finalEntryReturBeli', 'Purchase::finalEntryReturBeli');
    $routes->add('showing_returbelitemp', 'Purchase::showing_returbelitemp');
    $routes->add('updateStatusReturBeli', 'Purchase::updateStatusReturBeli');


    $routes->add('save_returbeli_detail', 'Purchase::save_returbeli_detail');
    $routes->get('updateReturBeli(:any)', 'Purchase::updateReturBeli$1');

    $routes->add('deleteReturBeliDtl', 'Purchase::deleteReturBeliDtl');
    $routes->add('show_returbeli', 'Purchase::show_returbeli');
    $routes->add('api_returbeli(:any)', 'Purchase::api_returbeli$1');
    $routes->add('list_trx_returbeli_dtl', 'Purchase::list_trx_returbeli_dtl');
    $routes->add('showing_returbelitrx', 'Purchase::showing_returbelitrx');
    $routes->add('getBranchInfoReturBeli', 'Purchase::getBranchInfoReturBeli');
    $routes->add('getNextSuffixReturBeli', 'Purchase::getNextSuffixReturBeli');
    $routes->add('initReturBeliHeader', 'Purchase::initReturBeliHeader');
    $routes->add('saveReturBeliDetail', 'Purchase::saveReturBeliDetail');
    $routes->add("get_returbeli_detail(:any)", "Purchase::get_returbeli_detail$1");
    $routes->add("delete_returbeli_detail", "Purchase::delete_returbeli_detail");



});



//POST SALES
$routes->group('/sales/postsales', ["namespace" => "App\Controllers\Sales"], function ($routes) {

    $routes->add('salesorder', 'PostSales::salesorder');
    $routes->add('list_salesorder', 'PostSales::list_salesorder');
    $routes->add('list_salesorder_apprv', 'PostSales::list_salesorder_apprv');

    $routes->add('addSalesOrder', 'PostSales::addSalesOrder');
    $routes->add('detailSalesOrder', 'PostSales::detailSalesOrder');
    $routes->add('list_tmp_salesorder_dtl', 'PostSales::list_tmp_salesorder_dtl');
    // $routes->add('showing_sikbsp_mst', 'PostSales::showing_sikbsp_mst');

    $routes->add('clearEntrySalesOrder', 'PostSales::clearEntrySalesOrder');
    $routes->add('finalEntrySalesOrder', 'PostSales::finalEntrySalesOrder');
    $routes->add('showing_salesordertemp', 'PostSales::showing_salesordertemp');
    $routes->add('updateStatusSalesOrder', 'PostSales::updateStatusSalesOrder');


    $routes->add('save_salesorder_detail', 'PostSales::save_salesorder_detail');
    $routes->get('updateSalesOrder(:any)', 'PostSales::updateSalesOrder$1');

    $routes->add('deleteSalesOrderDtl', 'PostSales::deleteSalesOrderDtl');
    $routes->add('show_salesorder', 'PostSales::show_salesorder');
    $routes->add('api_salesorder(:any)', 'PostSales::api_salesorder$1');
    $routes->add('list_trx_salesorder_dtl', 'PostSales::list_trx_salesorder_dtl');
    $routes->add('showing_salesordertrx', 'PostSales::showing_salesordertrx');
    $routes->add('getBranchInfoSalesOrder', 'PostSales::getBranchInfoSalesOrder');
    $routes->add('getNextSuffixSalesOrder', 'PostSales::getNextSuffixSalesOrder');
    $routes->add('initSalesOrderHeader', 'PostSales::initSalesOrderHeader');
    $routes->add('saveSalesOrderDetail', 'PostSales::saveSalesOrderDetail');
    $routes->add("get_salesorder_detail(:any)", "PostSales::get_salesorder_detail$1");
    $routes->add("delete_salesorder_detail", "PostSales::delete_salesorder_detail");





    $routes->add('penjualan', 'PostSales::penjualan');
    $routes->add('list_penjualan', 'PostSales::list_penjualan');
    $routes->add('list_penjualan_apprv', 'PostSales::list_penjualan_apprv');

    $routes->add('addPenjualan', 'PostSales::addPenjualan');
    $routes->add('detailPenjualan', 'PostSales::detailPenjualan');
    $routes->add('list_tmp_penjualan_dtl', 'PostSales::list_tmp_penjualan_dtl');
    // $routes->add('showing_sikbsp_mst', 'PostSales::showing_sikbsp_mst');

    $routes->add('clearEntryPenjualan', 'PostSales::clearEntryPenjualan');
    $routes->add('finalEntryPenjualan', 'PostSales::finalEntryPenjualan');
    $routes->add('showing_penjualantemp', 'PostSales::showing_penjualantemp');
    $routes->add('updateStatusPenjualan', 'PostSales::updateStatusPenjualan');


    $routes->add('save_penjualan_detail', 'PostSales::save_penjualan_detail');
    $routes->get('updatePenjualan(:any)', 'PostSales::updatePenjualan$1');

    $routes->add('deletePenjualanDtl', 'PostSales::deletePenjualanDtl');
    $routes->add('show_penjualan', 'PostSales::show_penjualan');
    $routes->add('api_penjualan(:any)', 'PostSales::api_penjualan$1');
    $routes->add('list_trx_penjualan_dtl', 'PostSales::list_trx_penjualan_dtl');
    $routes->add('showing_penjualantrx', 'PostSales::showing_penjualantrx');
    $routes->add('getBranchInfoPenjualan', 'PostSales::getBranchInfoPenjualan');
    $routes->add('getNextSuffixPenjualan', 'PostSales::getNextSuffixPenjualan');
    $routes->add('initPenjualanHeader', 'PostSales::initPenjualanHeader');
    $routes->add('savePenjualanDetail', 'PostSales::savePenjualanDetail');
    $routes->add("get_penjualan_detail(:any)", "PostSales::get_penjualan_detail$1");
    $routes->add("delete_penjualan_detail", "PostSales::delete_penjualan_detail");




    $routes->add('salesorderexternal', 'PostSales::salesorderexternal');
    $routes->add('list_salesorderexternal', 'PostSales::list_salesorderexternal');
    $routes->add('addSalesOrderExternal', 'PostSales::addSalesOrderExternal');
    $routes->add('detailSalesOrderExternal', 'PostSales::detailSalesOrderExternal');
    $routes->add('list_t_salesorderexternal_dtl', 'PostSales::list_t_salesorderexternal_dtl');
    // $routes->add('showing_sikbsp_mst', 'PostSales::showing_sikbsp_mst');
    $routes->add('showing_salesorderexternaltemp', 'PostSales::showing_salesorderexternaltemp');
    $routes->add('saveSalesOrderExternal', 'PostSales::saveSalesOrderExternal');
    $routes->add('clearEntrySalesOrderExternal', 'PostSales::clearEntrySalesOrderExternal');
    $routes->add('finalEntrySalesOrderExternal', 'PostSales::finalEntrySalesOrderExternal');
    $routes->get('updateSalesOrderExternal(:any)', 'PostSales::updateSalesOrderExternal$1');
    $routes->get('deleteSalesOrderExternal(:any)', 'PostSales::deleteSalesOrderExternal$1');
    $routes->add('insert_detail_salesorderexternal', 'PostSales::insert_detail_salesorderexternal');
    $routes->add('insertNewSalesOrderExternal', 'PostSales::insertNewSalesOrderExternal');
    $routes->post("update_detail_salesorderexternal", "PostSales::update_detail_salesorderexternal");
    $routes->add('deleteSalesOrderExternalDtl', 'PostSales::deleteSalesOrderExternalDtl');
    $routes->add('show_salesorderexternal', 'PostSales::show_salesorderexternal');
    $routes->add('api_salesorderexternal(:any)', 'PostSales::api_salesorderexternal$1');
    $routes->add('list_t_salesorderexternal_dtltrx', 'PostSales::list_t_salesorderexternal_dtltrx');
    $routes->add('showing_salesorderexternaltrx', 'PostSales::showing_salesorderexternaltrx');
    $routes->add('getRolePOSOE(:any)', 'PostSales::getRolePOSOE$1');
    $routes->add('getRate(:any)', 'PostSales::getRate$1');


    $routes->add('soi', 'PostSales::soi');
    $routes->add('list_soi', 'PostSales::list_soi');
    $routes->add('addSOI', 'PostSales::addSOI');
    $routes->add('detailSOI', 'PostSales::detailSOI');
    $routes->add('list_t_soi_dtl', 'PostSales::list_t_soi_dtl');
    // $routes->add('showing_sikbsp_mst', 'PostSales::showing_sikbsp_mst');
    $routes->add('showing_soitemp', 'PostSales::showing_soitemp');
    $routes->add('saveSOI', 'PostSales::saveSOI');
    $routes->add('clearEntrySOI', 'PostSales::clearEntrySOI');
    $routes->add('finalEntrySOI', 'PostSales::finalEntrySOI');
    $routes->get('updateSOI(:any)', 'PostSales::updateSOI$1');
    $routes->get('deleteSOI(:any)', 'PostSales::deleteSOI$1');
    $routes->add('insert_detail_soi', 'PostSales::insert_detail_soi');
    $routes->add('insertNewSOI', 'PostSales::insertNewSOI');
    $routes->post("update_detail_soi", "PostSales::update_detail_soi");
    $routes->add('deleteSOIDtl', 'PostSales::deleteSOIDtl');
    $routes->add('show_soi', 'PostSales::show_soi');
    $routes->add('api_soi(:any)', 'PostSales::api_soi$1');
    $routes->add('list_t_soi_dtltrx', 'PostSales::list_t_soi_dtltrx');
    $routes->add('showing_soitrx', 'PostSales::showing_soitrx');
    $routes->add('getRolePOSOI(:any)', 'PostSales::getRolePOSOI$1');
    $routes->add('getRate(:any)', 'PostSales::getRate$1');
    $routes->add('importSOIDetailFromPO', 'PostSales::importSOIDetailFromPO');
    $routes->add('clearTmpSOIDetail', 'PostSales::clearTmpSOIDetail');


});


//PRE SALES
$routes->group('/sales/presales', ["namespace" => "App\Controllers\Sales"], function ($routes) {

    $routes->add('/', 'PreSales::taskmanagement');
    $routes->add('list_task', 'PreSales::list_task');
    $routes->add('list_task_board', 'PreSales::list_task_board');
    $routes->add('addTask', 'PreSales::addTask');
    $routes->post('updateTask', 'PreSales::updateTask');



    $routes->add('offering', 'PreSales::offering');
    $routes->add('list_offering', 'PreSales::list_offering');
    $routes->post('saveOffering', 'PreSales::saveOffering');
    $routes->add('input_offering', 'PreSales::input_offering');
    $routes->get('showing_offering(:any)', 'PreSales::showing_offering$1');
    $routes->add('submitOffering', 'PreSales::saveOffering');
    $routes->get("editOffering/(:any)/(:any)", "PreSales::editOffering/$1/$2");
    $routes->get("hapusOffering/(:any)/(:any)", "PreSales::hapusOffering/$1/$2");
    // $routes->add('showing_exchange_rate', 'PreSales::showing_exchange_rate');
    $routes->add('showing_item(:any)', 'PreSales::showing_item$1');
    $routes->add('getRolePO(:any)', 'PreSales::getRolePO$1');


    $routes->add('insertNewItem', 'PreSales::insertNewItem');
    $routes->post("update_item", "PreSales::update_item");
    $routes->add('deleteItem', 'PreSales::deleteItem');


    //PENAWARAN HARGA
    $routes->add('priceproposal', 'Presales::offering');
    $routes->add('list_offering', 'Presales::list_offering');
    $routes->add('addOffering', 'Presales::addOffering');
    $routes->add('detailOffering', 'Presales::detailOffering');
    $routes->add('list_t_offering_dtl', 'Presales::list_t_offering_dtl');
    // $routes->add('showing_sikbsp_mst', 'Presales::showing_sikbsp_mst');
    $routes->add('saveOffering', 'Presales::saveOffering');
    $routes->add('clearEntryOffering', 'Presales::clearEntryOffering');
    $routes->add('finalEntryOffering', 'Presales::finalEntryOffering');
    $routes->add('showing_offeringtemp', 'Presales::showing_offeringtemp');

    $routes->get('updateOffering(:any)', 'Presales::updateOffering$1');
    $routes->get('deleteOffering(:any)', 'Presales::deleteOffering$1');
    $routes->add('insert_detail_offering', 'Presales::insert_detail_offering');
    $routes->add('insertNewOffering', 'Presales::insertNewOffering');
    $routes->post("update_detail_offering", "Presales::update_detail_offering");
    $routes->add('deleteOfferingDtl', 'Presales::deleteOfferingDtl');
    $routes->add('show_offering', 'Presales::show_offering');
    $routes->add('api_offering(:any)', 'Presales::api_offering$1');
    $routes->add('list_t_offering_dtltrx', 'Presales::list_t_offering_dtltrx');
    $routes->add('showing_offeringtrx', 'Presales::showing_offeringtrx');


    //PROFORMA INVOICE
    $routes->add('performainvoice', 'Presales::proforma');
    $routes->add('list_proforma', 'Presales::list_proforma');
    $routes->add('addProforma', 'Presales::addProforma');
    $routes->add('detailProforma', 'Presales::detailProforma');
    $routes->add('list_t_proforma_dtl', 'Presales::list_t_proforma_dtl');
    // $routes->add('showing_sikbsp_mst', 'Presales::showing_sikbsp_mst');
    $routes->add('showing_proformatemp', 'Presales::showing_proformatemp');
    $routes->add('saveProforma', 'Presales::saveProforma');
    $routes->add('clearEntryProforma', 'Presales::clearEntryProforma');
    $routes->add('finalEntryProforma', 'Presales::finalEntryProforma');
    $routes->get('updateProforma(:any)', 'Presales::updateProforma$1');
    $routes->get('deleteProforma(:any)', 'Presales::deleteProforma$1');
    $routes->add('insert_detail_proforma', 'Presales::insert_detail_proforma');
    $routes->add('insertNewProforma', 'Presales::insertNewProforma');
    $routes->post("update_detail_proforma", "Presales::update_detail_proforma");
    $routes->add('deleteProformaDtl', 'Presales::deleteProformaDtl');
    $routes->add('show_proforma', 'Presales::show_proforma');
    $routes->add('api_proforma(:any)', 'Presales::api_proforma$1');
    $routes->add('list_t_proforma_dtltrx', 'Presales::list_t_proforma_dtltrx');
    $routes->add('showing_proformatrx', 'Presales::showing_proformatrx');
    $routes->add('getRolePOProforma(:any)', 'Presales::getRolePOProforma$1');

});


$routes->group('/master/data', ["namespace" => "App\Controllers\Master"], function ($routes) {
    $routes->add('supplier', 'Suppliers::supplier');
    $routes->post("list_suppliers", "Suppliers::list_suppliers");
    $routes->post("saveDataSupplier", "Suppliers::saveDataSupplier");
    $routes->add("showDetailSupplier(:any)", "Suppliers::showDetailSupplier$1");
    $routes->add("edit_suppliers(:any)", "Suppliers::edit_suppliers$1");
    $routes->add("detail_suppliers(:any)", "Suppliers::detail_suppliers$1");
    $routes->add("hapus_suppliers(:any)", "Suppliers::hapus_suppliers$1");
    $routes->add("del_suppliers(:any)", "Suppliers::del_suppliers$1");
    $routes->add("input_suppliers", "Suppliers::input_suppliers");


    $routes->add('customer', 'Customer::customer');
    $routes->post("list_customer", "Customer::list_customer");
    $routes->post("saveDataCustomer", "Customer::saveDataCustomer");
    $routes->add("showDetailCustomer(:any)", "Customer::showDetailCustomer$1");
    $routes->add("edit_customer(:any)", "Customer::edit_customer$1");
    $routes->add("detail_customer(:any)", "Customer::detail_customer$1");
    $routes->add("hapus_customer(:any)", "Customer::hapus_customer$1");
    $routes->add("del_customer(:any)", "Customer::del_customer$1");
    $routes->add("input_customer", "Customer::input_customer");



    $routes->add('currency', 'Currency::currency');
    $routes->add('list_currency', 'Currency::list_currency');
    $routes->post('saveCurrency', 'Currency::saveCurrency');
    $routes->add('input_currency', 'Currency::input_currency');
    $routes->get('showing_currency(:any)', 'Currency::showing_currency$1');
    $routes->add('submitCurrency', 'Currency::saveCurrency');

    $routes->add('saveFinalCurrency', 'Currency::saveFinalCurrency');
    $routes->get("editCurrency/(:any)/(:any)", "Currency::editCurrency/$1/$2");
    $routes->get("detailCurrency/(:any)/(:any)", "Currency::detailCurrency/$1/$2");
    $routes->get("hapusCurrency/(:any)/(:any)", "Currency::hapusCurrency/$1/$2");
    // $routes->add('showing_exchange_rate', 'Currency::showing_exchange_rate');
    $routes->add('showing_exchange_rate(:any)', 'Currency::showing_exchange_rate$1');

    $routes->add('insertNewExchange', 'Currency::insertNewExchange');
    $routes->post("update_exchangerate", "Currency::update_exchangerate");
    $routes->add('deleteExchangeRate', 'Currency::deleteExchangeRate');


    $routes->add('coa', 'Coa::coa');
    $routes->add('js_vtree_query', 'Coa::js_vtree_query');
    $routes->add('get_coa_detail', 'Coa::get_coa_detail');
    $routes->add('saveCOA', 'Coa::saveCOA');
    $routes->add('delete_coa', 'Coa::delete_coa');

    $routes->add('job', 'Job::job');
    $routes->add('js_vtree_job_query', 'Job::js_vtree_job_query');
    $routes->add('get_job_detail', 'Job::get_job_detail');
    $routes->add('saveJob', 'Job::saveJob');
    $routes->add('delete_job', 'Job::delete_job');

    $routes->add('location', 'Location::index');
    $routes->add('list_mlocation', 'Location::list_mlocation');
    $routes->post("saveEntry", "Location::saveEntry");

    $routes->get("showing_data/(:any)", "Location::showing_data/$1");



    
    $routes->add('area', 'Location::area');
    $routes->add('list_marea', 'Location::list_marea');
    $routes->post("saveEntryArea", "Location::saveEntryArea");
    $routes->get("showing_data_area/(:any)", "Location::showing_data_area/$1");
    $routes->add('import_area', 'Location::import_area');
    $routes->post("proses_upload", "Location::proses_upload");
    $routes->add("clear_tmp", "Location::clear_tmp");
    $routes->add("final_data", "Location::final_data");
    $routes->add("showlabels", "Location::showlabels");
    $routes->add("api_show_showlabels_area(:any)", "Location::api_show_showlabels_area$1");
    $routes->add("show_showlabels_area", "Location::show_showlabels_area");


    $routes->add("cc", "Location::cc");
    $routes->add("list_costcenter", "Location::list_costcenter");
    $routes->post("saveCostCenter", "Location::saveCostCenter");
    $routes->get("showing_data_costcenter(:any)", "Location::showing_data_costcenter$1");
    $routes->post("show_showlabels_area_partial", "Location::show_showlabels_area_partial");
    $routes->get("api_show_showlabels_area_partial(:any)", "Location::api_show_showlabels_area_partial$1");


    /* Master Data Tax */
    $routes->add('tax', 'Tax::tax');
    $routes->add("input_tax", "Tax::input_tax");
    $routes->post("list_tax", "Tax::list_tax");
    $routes->add("save_tax_master", "Tax::save_tax_master");

    $routes->add("getMasterTax(:any)", "Tax::getMasterTax$1");
    $routes->add("update_tax(:any)", "Tax::update_tax$1");
    $routes->add("detail_tax(:any)", "Tax::detail_tax$1");
    $routes->add("get_tax_detail(:any)", "Tax::get_tax_detail$1");

    $routes->add("list_tax_detail", "Tax::list_tax_detail");
    $routes->add("save_tax_detail", "Tax::save_tax_detail");
    $routes->add("delete_tax_detail", "Tax::delete_tax_detail");
    $routes->add("hapus_master_tax", "Tax::hapus_master_tax");





    $routes->add('barang', 'Item::index');
    $routes->post("list_mitem", "Item::list_mitem");
    $routes->post("saveDataItem", "Item::saveDataItem");
    $routes->add("showDetailItem(:any)", "Item::showDetailItem$1");
    $routes->add("edit(:any)", "Item::edit$1");
    $routes->add("detail(:any)", "Item::detail$1");
    $routes->add("del_item(:any)", "Item::del_item$1");
    $routes->add("input", "Item::input");
    $routes->add("import", "Item::import");
    $routes->post("proses_upload", "Item::proses_upload");
    $routes->add("clear_tmp", "Item::clear_tmp");
    $routes->add("final_data", "Item::final_data");
    $routes->add("unit", "Item::unit");

    $routes->add("golonganbarang", "GolonganBarang::golonganbarang");
    $routes->add("list_golonganbarang", "GolonganBarang::list_golonganbarang");
    $routes->post("saveGolonganBarang", "GolonganBarang::saveGolonganBarang");
    $routes->get("showing_data_golonganbarang(:any)", "GolonganBarang::showing_data_golonganbarang$1");


    
    $routes->add("jenisproduk", "JenisProduk::jenisproduk");
    $routes->add("list_jenisproduk", "JenisProduk::list_jenisproduk");
    $routes->post("saveJenisProduk", "JenisProduk::saveJenisProduk");
    $routes->get("showing_data_jenisproduk(:any)", "JenisProduk::showing_data_jenisproduk$1");


    

    
    $routes->add("kelompokbarang", "KelompokBrg::kelompokbarang");
    $routes->add("list_kelompokbarang", "KelompokBrg::list_kelompokbarang");
    $routes->post("saveKelompokBarang", "KelompokBrg::saveKelompokBarang");
    $routes->get("showing_data_kelompokbarang(:any)", "KelompokBrg::showing_data_kelompokbarang$1");


    

    
    $routes->add("principal", "Principal::principal");
    $routes->add("list_principal", "Principal::list_principal");
    $routes->post("savePrincipal", "Principal::savePrincipal");
    $routes->get("showing_data_principal(:any)", "Principal::showing_data_principal$1");
});


/* MASTER ITEM */
$routes->group('/master/item', ["namespace" => "App\Controllers\Master"], function ($routes) {
    $routes->add('/', 'Item::index');
    $routes->post("list_mitem", "Item::list_mitem");
    $routes->post("saveDataItem", "Item::saveDataItem");
    $routes->add("showDetailItem(:any)", "Item::showDetailItem$1");
    $routes->add("edit(:any)", "Item::edit$1");
    $routes->add("del_item(:any)", "Item::del_item$1");
    $routes->add("input", "Item::input");
    $routes->add("import", "Item::import");
    $routes->post("proses_upload", "Item::proses_upload");
    $routes->add("clear_tmp", "Item::clear_tmp");
    $routes->add("final_data", "Item::final_data");
    $routes->add("unit", "Item::unit");
    $routes->add("showing_data_unit(:any)", "Item::showing_data_unit$1");
    $routes->post("list_unit", "Item::list_unit");
    $routes->post("saveUnit", "Item::saveUnit");
    $routes->add("print_labels", "Item::print_labels");
    $routes->add("itemgroup", "Item::itemgroup");
    $routes->add('list_mgroup', 'Item::list_mgroup');
    $routes->add('saveMgroup', 'Item::saveMgroup');
    $routes->add("showing_data_mgroup(:any)", "Item::showing_data_mgroup$1");
    $routes->add("showlabels", "Item::showlabels");
    $routes->add("api_show_showlabels_item(:any)", "Item::api_show_showlabels_item$1");
    $routes->add("show_showlabels_item", "Item::show_showlabels_item");
    $routes->post("show_showlabels_item_post", "Item::show_showlabels_item_post");
    $routes->add("api_show_showlabels_item_post(:any)", "Item::api_show_showlabels_item_post$1");
});

//API GROUP
$routes->group('api', ["namespace" => "App\Controllers\Api"], function ($routes) {
    $routes->add('geolocation/list_negara', 'Geolocation::list_negara');
    $routes->add('geolocation/list_provinsi', 'Geolocation::list_provinsi');
    $routes->add('geolocation/list_kota', 'Geolocation::list_kota');
    $routes->add('geolocation/list_kecamatan', 'Geolocation::list_kecamatan');
    $routes->add('geolocation/list_desa', 'Geolocation::list_desa');

    //$routes->add('globalmodule/jenis_kelamin(:any)', 'Globalmodule::jenis_kelamin$');
    $routes->add('globalmodule/jenis_kelamin', 'Globalmodule::jenis_kelamin');
    $routes->add('globalmodule/agama', 'Globalmodule::agama');
    $routes->add('globalmodule/golongan_darah', 'Globalmodule::golongan_darah');
    $routes->add('globalmodule/list_division', 'Globalmodule::list_division');
    $routes->add('globalmodule/list_departmen', 'Globalmodule::list_departmen');
    $routes->add('globalmodule/list_departmen_nm', 'Globalmodule::list_departmen_nm');
    $routes->add('globalmodule/list_lvljabatan_nm', 'Globalmodule::list_lvljabatan_nm');

    $routes->add('globalmodule/list_subdepartmen', 'Globalmodule::list_subdepartmen');
    $routes->add('globalmodule/list_jabatan', 'Globalmodule::list_jabatan');
    $routes->add('globalmodule/list_lvljabatan', 'Globalmodule::list_lvljabatan');
    $routes->add('globalmodule/list_golongan', 'Globalmodule::list_golongan');
    $routes->add('globalmodule/list_plant', 'Globalmodule::list_plant');
    $routes->add('globalmodule/list_kepegawaian', 'Globalmodule::list_kepegawaian');
    $routes->add('globalmodule/list_karyawan', 'Globalmodule::list_karyawan');
    $routes->add('globalmodule/list_pendidikan', 'Globalmodule::list_pendidikan');
    $routes->add('globalmodule/list_ptkp', 'Globalmodule::list_ptkp');
    $routes->add('globalmodule/list_bank', 'Globalmodule::list_bank');
    $routes->add('globalmodule/list_costcenter', 'Globalmodule::list_costcenter');
    $routes->get("globalmodule/list_karyawan_by_id(:any)", "Globalmodule::list_karyawan_by_id$1");

    $routes->add('globalmodule/list_mlocation', 'Globalmodule::list_mlocation');
    $routes->add('globalmodule/list_marea', 'Globalmodule::list_marea');
    $routes->add('globalmodule/list_mgroup', 'Globalmodule::list_mgroup');
    $routes->add('globalmodule/list_msubgroup', 'Globalmodule::list_msubgroup');
    $routes->add('globalmodule/list_unit', 'Globalmodule::list_unit');
    $routes->add('globalmodule/list_subunit', 'Globalmodule::list_subunit');
    $routes->add('globalmodule/list_item', 'Globalmodule::list_item');
    $routes->add('globalmodule/list_supplier', 'Globalmodule::list_supplier');
    $routes->add('globalmodule/list_outstanding_po', 'Globalmodule::list_outstanding_po');
    $routes->add('globalmodule/list_user', 'Globalmodule::list_user');

    $routes->add('globalmodule/list_mlocation', 'Globalmodule::list_mlocation');
    $routes->add('globalmodule/list_marea', 'Globalmodule::list_marea');
    $routes->add('globalmodule/list_mgroup', 'Globalmodule::list_mgroup');
    $routes->add('globalmodule/list_unit', 'Globalmodule::list_unit');
    $routes->add('globalmodule/list_subunit', 'Globalmodule::list_subunit');
    $routes->add('globalmodule/list_item', 'Globalmodule::list_item');
    $routes->add('globalmodule/list_supplier', 'Globalmodule::list_supplier');
    $routes->add('globalmodule/list_outstanding_po', 'Globalmodule::list_outstanding_po');
    $routes->add('globalmodule/list_batch_item', 'Globalmodule::list_batch_item');
    $routes->add('globalmodule/add_newbatch', 'Globalmodule::add_newbatch');
    $routes->add('globalmodule/list_market', 'Globalmodule::list_market');
    /*adding 27/01/2026 for jsystem*/
    $routes->add('globalmodule/list_gradecust', 'Globalmodule::list_gradecust');
    $routes->add('globalmodule/list_salesman', 'Globalmodule::list_salesman');
    $routes->add('globalmodule/list_kolektor', 'Globalmodule::list_kolektor');
    $routes->add('globalmodule/list_coa', 'Globalmodule::list_coa');
    $routes->add('globalmodule/list_currency', 'Globalmodule::list_currency');
    $routes->add('globalmodule/list_tax', 'Globalmodule::list_tax');
    $routes->add('globalmodule/list_customer', 'Globalmodule::list_customer');
    $routes->add('globalmodule/list_golonganbarang', 'Globalmodule::list_golonganbarang');
    $routes->add('globalmodule/list_jenisproduk', 'Globalmodule::list_jenisproduk');
    $routes->add('globalmodule/list_kelompokbarang', 'Globalmodule::list_kelompokbarang');
    $routes->add('globalmodule/list_principal', 'Globalmodule::list_principal');
    $routes->add('globalmodule/list_supplier_new', 'Globalmodule::list_supplier_new');
    $routes->add('globalmodule/list_cust_and_supplier', 'Globalmodule::list_cust_and_supplier');

    $routes->add('globalmodule/list_pp', 'Globalmodule::list_pp');
    $routes->add('globalmodule/list_po', 'Globalmodule::list_po');
    $routes->add('globalmodule/list_lpb', 'Globalmodule::list_lpb');

    $routes->add('globalmodule/list_so', 'Globalmodule::list_so');
    $routes->add('globalmodule/get_tax_percent', 'Globalmodule::get_tax_percent');

    $routes->add('globalmodule/list_branchjob', 'Globalmodule::list_branchjob');

    $routes->add('globalmodule/list_bank_sales', 'Globalmodule::list_bank_sales');

    $routes->add('globalmodule/updatePrintStatus', 'Globalmodule::updatePrintStatus');
    $routes->add('globalmodule/list_avg_stock', 'Globalmodule::list_avg_stock');


    //validator & request keluar
    $routes->add('validatorabsensi', 'ValidatorAbsensi::index');
    $routes->add('validatorabsensi/getfinger', 'ValidatorAbsensi::getFinger');
    $routes->add('validatorabsensi/getfinger2', 'ValidatorAbsensi::getFinger2');
    $routes->add('validatorabsensi/getfinger3', 'ValidatorAbsensi::getFinger3');
    $routes->add('validatorabsensi/gettransready', 'ValidatorAbsensi::getTransready');
    $routes->add('validatorabsensi/getdbfrommachine', 'ValidatorAbsensi::getDBfromMachine');
    $routes->add('validatorabsensi/clearmccheckinout', 'ValidatorAbsensi::clearMC_Checkinout');
    $routes->add('validatorabsensi/getDBfromMachine', 'ValidatorAbsensi::getDBfromMachine');

    $routes->add('validatorpush/renderpdf(:any)', 'ValidatorPush::renderpdf$1');
    $routes->add('validatorpush/renderpdf_pbl(:any)', 'ValidatorPush::renderpdf_pbl$1');

    //validator

    //validator mailer
    $routes->add('validatormailer/cli_mailoutbox_sent', 'ValidatorMailer::cli_mailoutbox_sent');
    $routes->add('validatormailer/cli_mailtest', 'ValidatorMailer::cli_mailtest');
    $routes->add('validatormailer/capture_it_reminder', 'ValidatorMailer::capture_it_reminder');

});


$routes->group('/persediaan/trans', ["namespace" => "App\Controllers\Persediaan"], function ($routes) {


    $routes->add('perintah_transfer', 'Persediaan::perintah_transfer');
    $routes->add('addSPKtransfers', 'Persediaan::addSPKtransfers');
    $routes->add('saveSPKTransferDetail', 'Persediaan::saveSPKTransferDetail');
    $routes->add('showing_spk_mst_tmp', 'Persediaan::showing_spk_mst_tmp');
    $routes->add('clearSpkTransfers', 'Persediaan::clearSpkTransfers');
    $routes->add('list_tmp_spk_transfers_dtl', 'Persediaan::list_tmp_spk_transfers_dtl');
    $routes->add('get_tmp_spk_transfer_dtl(:any)', 'Persediaan::get_tmp_spk_transfer_dtl$1');
    $routes->add('deleteSPKTransferDetail', 'Persediaan::deleteSPKTransferDetail');
    $routes->add('finalSpkTransfers', 'Persediaan::finalSpkTransfers');
    $routes->add('list_spk_transfers', 'Persediaan::list_spk_transfers');
    $routes->add('updateSPKTransfers', 'Persediaan::updateSPKTransfers');
    $routes->add('detailSpkTransfers(:any)', 'Persediaan::detailSpkTransfers$1');
//TRX
    $routes->add('get_trx_spk_transfer_dtl(:any)', 'Persediaan::get_trx_spk_transfer_dtl$1');
    $routes->add('showing_spk_mst_trx', 'Persediaan::showing_spk_mst_trx');
    $routes->add('list_trx_spk_transfers_dtl', 'Persediaan::list_trx_spk_transfers_dtl');


    // TRANSFER LOKASI

    $routes->add('transfer_lokasi', 'Persediaan::transfer_lokasi');
    $routes->add('addTransferLokasi', 'Persediaan::addTransferLokasi');
    $routes->add('saveTransferLocationDetail', 'Persediaan::saveTransferLocationDetail');
    $routes->add('showing_transfer_location_mst_tmp', 'Persediaan::showing_transfer_location_mst_tmp');
    $routes->add('clearTransferLokasi', 'Persediaan::clearTransferLokasi');
    $routes->add('list_tmp_transfer_location_dtl', 'Persediaan::list_tmp_transfer_location_dtl');
    $routes->add('get_tmp_transfer_location_dtl(:any)', 'Persediaan::get_tmp_transfer_location_dtl$1');
    $routes->add('deleteSPKTransferDetail', 'Persediaan::deleteSPKTransferDetail');
    $routes->add('finalTransferLocation', 'Persediaan::finalTransferLocation');
    $routes->add('list_trx_transfer_location', 'Persediaan::list_trx_transfer_location');
    $routes->add('updateTransfersLocation', 'Persediaan::updateTransfersLocation');
    $routes->add('detailTransfersLocation(:any)', 'Persediaan::detailTransfersLocation$1');
//TRX
    $routes->add('get_trx_transfer_location_dtl(:any)', 'Persediaan::get_trx_transfer_location_dtl$1');
    $routes->add('showing_transfer_mst_trx', 'Persediaan::showing_transfer_mst_trx');
    $routes->add('list_trx_spk_transfers_dtl', 'Persediaan::list_trx_spk_transfers_dtl');
$routes->add('getBranchInfoStockTransfers', 'Persediaan::getBranchInfoStockTransfers');
    $routes->add('getNextSuffixStockTransfers', 'Persediaan::getNextSuffixStockTransfers');


    /* ajustment stock */
    $routes->add('ajustment_stock', 'Persediaan::ajustment_stock');
    $routes->add('add_ajustment_stock_mst', 'Persediaan::add_ajustment_stock_mst');
    $routes->add('list_tmp_ajustment_stock_dtl', 'Persediaan::list_tmp_ajustment_stock_dtl');
    $routes->add('save_ajustment_stock_detail', 'Persediaan::save_ajustment_stock_detail');
    $routes->add('showing_ajustment_stock_mst_tmp', 'Persediaan::showing_ajustment_stock_mst_tmp');
    $routes->add('clear_ajustment_stock', 'Persediaan::clear_ajustment_stock');
    $routes->add('list_tmp_transfer_location_dtl', 'Persediaan::list_tmp_transfer_location_dtl');
    $routes->add('get_tmp_ajustment_stock_dtl(:any)', 'Persediaan::get_tmp_ajustment_stock_dtl$1');
    $routes->add('deleteAjustmentStockDetail', 'Persediaan::deleteAjustmentStockDetail');
    $routes->add('final_ajustment_stock_mst', 'Persediaan::final_ajustment_stock_mst');

    $routes->add('updateAjustmentStock', 'Persediaan::updateAjustmentStock');
    $routes->add('detailAjustmentStock(:any)', 'Persediaan::detailAjustmentStock$1');
    $routes->add('cancelAjustmentStock(:any)', 'Persediaan::cancelAjustmentStock$1');
//TRX
    $routes->add('get_trx_ajustment_stock_mst_dtl(:any)', 'Persediaan::get_trx_ajustment_stock_mst_dtl$1');
    $routes->add('showing_ajustment_stock_mst', 'Persediaan::showing_ajustment_stock_mst');
    $routes->add('list_trx_ajustment_stock_mst', 'Persediaan::list_trx_ajustment_stock_mst');
    $routes->add('list_trx_ajustment_stock_dtl', 'Persediaan::list_trx_ajustment_stock_dtl');
    $routes->add('getBranch_ajustment_stock', 'Persediaan::getBranch_ajustment_stock');
    $routes->add('getNextSuffix_ajustment_stock', 'Persediaan::getNextSuffix_ajustment_stock');


    /* ajustment item value  */
    $routes->add('ajustment_item_value', 'Persediaan::ajustment_item_value');


    /* Pemakaian Barang */
    $routes->add('pmk_brng', 'Persediaan::pmk_brng');
    $routes->add('add_pmk_brng_mst', 'Persediaan::add_pmk_brng_mst');
    $routes->add('list_trx_pmk_brng_mst', 'Persediaan::list_trx_pmk_brng_mst');
    $routes->add('save_pmk_brng_detail', 'Persediaan::save_pmk_brng_detail');
    $routes->add('showing_pmk_brng_mst_tmp', 'Persediaan::showing_pmk_brng_mst_tmp');
    $routes->add('clear_pmk_brng', 'Persediaan::clear_pmk_brng');
    $routes->add('list_tmp_pmk_brng_dtl', 'Persediaan::list_tmp_pmk_brng_dtl');
    $routes->add('get_tmp_pmk_brng_dtl(:any)', 'Persediaan::get_tmp_pmk_brng_dtl$1');
    $routes->add('delete_pmk_brng', 'Persediaan::delete_pmk_brng');
    $routes->add('final_pmk_barang', 'Persediaan::final_pmk_barang');

    $routes->add('updatePmkBrg', 'Persediaan::updatePmkBrg');
    $routes->add('detail_pmk_brng_mst(:any)', 'Persediaan::detail_pmk_brng_mst$1');
//TRX
    $routes->add('get_trx_pmk_brng_mst_dtl(:any)', 'Persediaan::get_trx_pmk_brng_mst_dtl$1');
    $routes->add('showing_pmk_brng_mst', 'Persediaan::showing_pmk_brng_mst');
    $routes->add('list_pmk_brng', 'Persediaan::list_trx_pmk_brng');
    $routes->add('getBranch_pmk_brng', 'Persediaan::getBranch_pmk_brng');
    $routes->add('getNextSuffix_pmk_brng', 'Persediaan::getNextSuffix_pmk_brng');



    /* Penerimaan Barang */
    $routes->add('pnm_barang', 'Persediaan::pnm_barang');
    $routes->add('add_pnm_brng_mst', 'Persediaan::add_pnm_brng_mst');
    $routes->add('list_trx_pnm_brng_mst', 'Persediaan::list_trx_pnm_brng_mst');
    $routes->add('save_pnm_brng_detail', 'Persediaan::save_pnm_brng_detail');
    $routes->add('showing_pnm_brng_mst_tmp', 'Persediaan::showing_pnm_brng_mst_tmp');
    $routes->add('clear_pnm_brng', 'Persediaan::clear_pnm_brng');
    $routes->add('list_tmp_pnm_brng_dtl', 'Persediaan::list_tmp_pnm_brng_dtl');
    $routes->add('get_tmp_pnm_brng_dtl(:any)', 'Persediaan::get_tmp_pnm_brng_dtl$1');
    $routes->add('delete_pnm_brng', 'Persediaan::delete_pnm_brng');
    $routes->add('final_pnm_barang', 'Persediaan::final_pnm_barang');
    $routes->add('get_summary_pnm', 'Persediaan::get_summary_pnm');

    $routes->add('updatepnmBrg', 'Persediaan::updatepnmBrg');
    $routes->add('detail_pnm_brng_mst(:any)', 'Persediaan::detail_pnm_brng_mst$1');
//TRX
    $routes->add('get_trx_pnm_brng_mst_dtl(:any)', 'Persediaan::get_trx_pnm_brng_mst_dtl$1');
    $routes->add('showing_pnm_brng_mst', 'Persediaan::showing_pnm_brng_mst');
    $routes->add('list_pnm_brng', 'Persediaan::list_trx_pnm_brng');
    $routes->add('getBranch_pnm_brng', 'Persediaan::getBranch_pnm_brng');
    $routes->add('getNextSuffix_pnm_brng', 'Persediaan::getNextSuffix_pnm_brng');
});




$routes->group('/production/trans', ["namespace" => "App\Controllers\Production"], function ($routes) {


    $routes->add('/', 'Production::index');
    $routes->add('standart_cost', 'Production::standart_cost');
    $routes->add('list_standart_cost_mst', 'Production::list_standart_cost_mst');
    $routes->add('add_standart_cost', 'Production::add_standart_cost');
    $routes->get('showing_tmp_standart_cost_mst(:any)', 'Production::showing_tmp_standart_cost_mst$1');
    $routes->get('showing_mst_standart_cost_mst(:any)', 'Production::showing_mst_standart_cost_mst$1');
    $routes->add('getNextSuffix_standart_cost_mst', 'Production::getNextSuffix_standart_cost_mst');
    $routes->add('save_standart_cost_dtl', 'Production::save_standart_cost_dtl');
    $routes->add('save_standart_cost_mst', 'Production::save_standart_cost_mst');
    $routes->add('clearStandartCostTmp', 'Production::clearStandartCostTmp');
    $routes->add('list_tmp_standart_cost_dtl', 'Production::list_tmp_standart_cost_dtl');
    $routes->add('list_mst_standart_cost_dtl', 'Production::list_mst_standart_cost_dtl');
    $routes->add('get_standart_cost_dtl(:any)', 'Production::get_standart_cost_dtl$1');
    $routes->add('final_input_standart_cost', 'Production::final_input_standart_cost');

    /*UPDATE STANDART COST*/
    $routes->add('updateStandartCost(:any)', 'Production::updateStandartCost$1');
    $routes->add('detailStandartCost(:any)','Production::detailStandartCost$1');
    $routes->add('cancelStandartCost(:any)','Production::cancelStandartCost$1');


    /* BIAYA STANDART */

    $routes->add('biaya_standart', 'Production::biaya_standart');
    $routes->add('list_biaya_standart_mst', 'Production::list_biaya_standart_mst');
    $routes->add('add_biaya_standart', 'Production::add_biaya_standart');
    $routes->get('showing_tmp_biaya_standart_mst(:any)', 'Production::showing_tmp_biaya_standart_mst$1');
    $routes->get('showing_mst_biaya_standart_mst(:any)', 'Production::showing_mst_biaya_standart_mst$1');
    $routes->add('getNextSuffix_biaya_standart_mst', 'Production::getNextSuffix_biaya_standart_mst');
    $routes->add('save_biaya_standart_dtl', 'Production::save_biaya_standart_dtl');
    $routes->add('save_biaya_standart_mst', 'Production::save_biaya_standart_mst');
    $routes->add('clearBiayaStandartTmp', 'Production::clearBiayaStandartTmp');
    $routes->add('list_tmp_biaya_standart_dtl', 'Production::list_tmp_biaya_standart_dtl');
    $routes->add('list_mst_biaya_standart_dtl', 'Production::list_mst_biaya_standart_dtl');
    $routes->add('get_biaya_standart_dtl(:any)', 'Production::get_biaya_standart_dtl$1');
    $routes->add('final_input_biaya_standart', 'Production::final_input_biaya_standart');

    /*UPDATE STANDART COST*/
    $routes->add('updateBiayaStandart(:any)', 'Production::updateBiayaStandart$1');
    $routes->add('detailBiayaStandart(:any)','Production::detailBiayaStandart$1');
    $routes->add('cancelBiayaStandart(:any)','Production::cancelBiayaStandart$1');


    /* BOM */

    $routes->add('bom', 'Production::bom');
    $routes->add('list_bom_mst', 'Production::list_bom_mst');
    $routes->add('add_bom', 'Production::add_bom');
    $routes->get('showing_tmp_bom_mst(:any)', 'Production::showing_tmp_bom_mst$1');
    $routes->get('showing_mst_bom_mst(:any)', 'Production::showing_mst_bom_mst$1');
    $routes->add('getNextSuffix_bom_mst', 'Production::getNextSuffix_bom_mst');
    $routes->add('save_bom_dtl', 'Production::save_bom_dtl');
    $routes->add('save_bom_mst', 'Production::save_bom_mst');
    $routes->add('clear_bom_Tmp', 'Production::clear_bom_Tmp');
    $routes->add('list_tmp_bom_dtl', 'Production::list_tmp_bom_dtl');
    $routes->add('list_mst_bom_dtl', 'Production::list_mst_bom_dtl');
    $routes->add('get_bom_dtl(:any)', 'Production::get_bom_dtl$1');
    $routes->add('final_input_bom', 'Production::final_input_bom');

    /* BUILD OF MATERIAL */
    $routes->add('update_bom_(:any)', 'Production::update_bom_$1');
    $routes->add('detail_bom_(:any)','Production::detail_bom_$1');
    $routes->add('cancel_bom_(:any)','Production::cancel_bom_$1');
});




/*
$routes->add('/dashboard', 'Dashboard/Dashboard::index');
$routes->post('/dashboard/api_jam_lembur', 'Dashboard/Dashboard::api_jam_lembur');
$routes->post('/dashboard/api_absensi_tahunan', 'Dashboard/Dashboard::api_absensi_tahunan');
$routes->post('/dashboard/api_summary_karyawan', 'Dashboard/Dashboard::api_summary_karyawan');
$routes->add('/dashboard/api_summary_karyawan_vaksin', 'Dashboard/Dashboard::api_summary_karyawan_vaksin');
$routes->add('/dashboard/api_summary_karyawan_positif_covid', 'Dashboard/Dashboard::api_summary_karyawan_positif_covid');
$routes->add('/dashboard/api_summary_dept', 'Dashboard/Dashboard::api_summary_dept');
$routes->get('/dashboard/logout', 'Dashboard/Dashboard::logout');
*/

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
