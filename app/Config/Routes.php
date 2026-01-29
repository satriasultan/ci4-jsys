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
});


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

//stock terkumpul di sini
$routes->group('/stock/balance', ["namespace" => "App\Controllers\Stock"], function ($routes) {
    $routes->add('/', 'Balance::index');
    $routes->add('import_stock', 'Balance::import_stock');
    $routes->add('proses_upload', 'Balance::proses_upload');
    $routes->add('clear_tmp', 'Balance::clear_tmp');
    $routes->add('final_data', 'Balance::final_data');
    $routes->post('list_balance', 'Balance::list_balance');
    $routes->add('allocation_map', 'Balance::allocation_map');
    $routes->add('apiAlocationMap', 'Balance::apiAlocationMap');
    //stimulus
    $routes->add('show_sti_allocation_map(:any)', 'Balance::show_sti_allocation_map$1');
    $routes->add('api_sti_allocation_map(:any)', 'Balance::api_sti_allocation_map$1');

    $routes->add('moving', 'Balance::moving');
    $routes->add('input_moving', 'Balance::input_moving');
    $routes->post('list_item', 'Balance::list_item');
    $routes->post('list_balance_moving', 'Balance::list_balance_moving');
    $routes->post('list_area', 'Balance::list_area');
    $routes->add('showing_detail_moving(:any)', 'Balance::showing_detail_moving$1');
    $routes->post('saveMovingStock', 'Balance::saveMovingStock');
    $routes->add('list_moving', 'Balance::list_moving');
    $routes->get('cancelmoveitem', 'Balance::cancelmoveitem');
    $routes->add('transfers', 'Balance::transfers');
    $routes->add('input_stock_transfers', 'Balance::input_stock_transfers');
    $routes->add('clearEntryTransfers', 'Balance::clearEntryTransfers');
    $routes->add('verifikasi_mst_stock_transfers', 'Balance::verifikasi_mst_stock_transfers');
    $routes->add('showing_item_transfers_mst', 'Balance::showing_item_transfers_mst');
    $routes->add('list_balance_transfers', 'Balance::list_balance_transfers');
    $routes->add('list_tmp_transfers_dtl', 'Balance::list_tmp_transfers_dtl');
    $routes->add('showing_stkgdw(:any)', 'Balance::showing_stkgdw$1');
    $routes->post('saveDetailTransfer', 'Balance::saveDetailTransfer');
    $routes->add('showing_item_transfers_dtl(:any)', 'Balance::showing_item_transfers_dtl$1');
    $routes->add('update_stock_transfers(:any)', 'Balance::update_stock_transfers$1');
    $routes->add('detail_stock_transfers(:any)', 'Balance::detail_stock_transfers$1');
    $routes->add('cancel_stock_transfers(:any)', 'Balance::cancel_stock_transfers');
    $routes->add('finalEntryTransfers', 'Balance::finalEntryTransfers');
    $routes->add('list_transfers', 'Balance::list_transfers');
    //ajustment
    $routes->add('ajustment', 'Balance::ajustment');
    $routes->add('input_stock_ajustment', 'Balance::input_stock_ajustment');
    $routes->add('list_tmp_ajustments_dtl', 'Balance::list_tmp_ajustments_dtl');
    $routes->add('list_balance_ajustments', 'Balance::list_balance_ajustments');
    $routes->add('showing_item_ajustments_mst', 'Balance::showing_item_ajustments_mst');
    $routes->add('verifikasi_mst_stock_ajustment', 'Balance::verifikasi_mst_stock_ajustment');
    $routes->add('showing_item_ajustments_mst', 'Balance::showing_item_ajustments_mst');
    $routes->add('clearEntryAjustment', 'Balance::clearEntryAjustment');
    $routes->add('list_balance_ajustments', 'Balance::list_balance_ajustments');
    $routes->add('list_tmp_ajustments_dtl', 'Balance::list_tmp_ajustments_dtl');
    $routes->post('saveDetailajustment', 'Balance::saveDetailajustment');
    $routes->add('showing_item_ajustment_dtl(:any)', 'Balance::showing_item_ajustment_dtl$1');
    $routes->add('finalEntryAjustment', 'Balance::finalEntryAjustment');
    $routes->add('import_balance_winacc', 'Balance::import_balance_winacc');
    $routes->add('proses_balance_winacc_upload', 'Balance::proses_balance_winacc_upload');
    $routes->add('clear_balance_winacc_tmp', 'Balance::clear_balance_winacc_tmp');
    $routes->add('final_balance_winacc_data', 'Balance::final_balance_winacc_data');
});
//PURCHASING
$routes->group('/purchase/purchaseorder', ["namespace" => "App\Controllers\Purchase"], function ($routes) {
    $routes->add('/', 'Purchaseorder::index');
    $routes->add('import_purchase', 'Purchaseorder::import_purchase');
    $routes->add('proses_upload', 'Purchaseorder::proses_upload');
    $routes->add('clear_tmp', 'Purchaseorder::clear_tmp');
    $routes->add('final_data', 'Purchaseorder::final_data');
    $routes->add('listpurchase', 'Purchaseorder::listpurchase');
    $routes->add('list_po', 'Purchaseorder::list_po');
    $routes->post('saveListPo', 'Purchaseorder::saveListPo');
    $routes->add('outstanding', 'Purchaseorder::outstanding');
    $routes->add('list_outstanding', 'Purchaseorder::list_outstanding');
    $routes->add('showing_data_po(:any)', 'Purchaseorder::showing_data_po$1');
    $routes->add('createpp', 'Purchaseorder::createpp');

});


//BBM
$routes->group('/stock/bbm', ["namespace" => "App\Controllers\Stock"], function ($routes) {
    $routes->add('/', 'Bbm::index');
    $routes->add('input_bbm', 'Bbm::input_bbm');
    $routes->add('showing_item_bbm_mst(:any)', 'Bbm::showing_item_bbm_mst$1');
    $routes->add('showing_item_bbm_dtl(:any)', 'Bbm::showing_item_bbm_dtl$1');
    $routes->add('verifikasi_mst_stock_bbm', 'Bbm::verifikasi_mst_stock_bbm');
    $routes->post('saveDetailBbm', 'Bbm::saveDetailBbm');
    $routes->post('list_tmp_bbm_dtl', 'Bbm::list_tmp_bbm_dtl');
    $routes->add('finalEntryBbm', 'Bbm::finalEntryBbm');
    $routes->add('clearEntryBbm', 'Bbm::clearEntryBbm');
    $routes->add('clearQty0', 'Bbm::clearQty0');
    $routes->add('list_bbm', 'Bbm::list_bbm');
    $routes->add('update_stock_bbm(:any)', 'Bbm::update_stock_bbm$1');
    $routes->add('detail_stock_bbm(:any)', 'Bbm::detail_stock_bbm$1');
    $routes->add('cancel_stock_bbm(:any)', 'Bbm::cancel_stock_bbm$1');
    $routes->add('api_print_stock_bbm(:any)', 'Bbm::api_print_stock_bbm$1');
    $routes->add('print_stock_bbm(:any)', 'Bbm::print_stock_bbm$1');
    $routes->add('reprint', 'Bbm::reprint');
    $routes->add('void', 'Bbm::void');
    $routes->add('void_stock_bbm', 'Bbm::void_stock_bbm');
    $routes->add('loadItemVoidLpb', 'Bbm::loadItemVoidLpb');
    $routes->add('clearEntryVoidBbm', 'Bbm::clearEntryVoidBbm');
    $routes->add('deleteUnheckVoid', 'Bbm::deleteUnheckVoid');
    $routes->add('progresVoid', 'Bbm::progresVoid');
    $routes->add('saveTmpMstVoid', 'Bbm::saveTmpMstVoid');
    $routes->add('showing_item_void_mst', 'Bbm::showing_item_void_mst');
    $routes->add('showing_item_void_dtl', 'Bbm::showing_item_void_dtl');
    $routes->add('showing_stkgdw(:any)', 'Bbm::showing_stkgdw$1');
    $routes->add('showing_item(:any)', 'Bbm::showing_item$1');
    $routes->add('list_tmp_void_dtl', 'Bbm::list_tmp_void_dtl');
    $routes->add('verifikasi_void_mst_stock_bbm', 'Bbm::verifikasi_void_mst_stock_bbm');
    $routes->add('load_po_to_bbm', 'Bbm::load_po_to_bbm');
    //LBM IMPORT
    $routes->get('import_lbm', 'Bbm::import_lbm');
    $routes->add('list_lbm', 'Bbm::list_lbm');
    $routes->add('proses_upload_lbm', 'Bbm::proses_upload_lbm');
    $routes->add('list_lbm_pagination', 'Bbm::list_lbm_pagination');
    $routes->add('clear_tmp_lbm', 'Bbm::clear_tmp_lbm');
    $routes->add('final_data_lbm', 'Bbm::final_data_lbm');
    $routes->post('list_balance_bbm', 'Bbm::list_balance_bbm');

    $routes->add('cetak_bt_bbm(:any)', 'Bbm::cetak_bt_bbm$1');
    $routes->add('testpdf(:any)', 'Bbm::testpdf$1');
    $routes->add('renderpdf(:any)', 'Bbm::renderpdf$1');


});
//BBK
$routes->group('/stock/bbk', ["namespace" => "App\Controllers\Stock"], function ($routes) {
    $routes->add('/', 'Bbk::index');
    $routes->add('input_bbk', 'Bbk::input_bbk');
    $routes->add('showing_item_bbk_mst(:any)', 'Bbk::showing_item_bbk_mst$1');
    $routes->add('showing_item_bbk_dtl(:any)', 'Bbk::showing_item_bbk_dtl$1');
    $routes->add('verifikasi_mst_stock_bbk', 'Bbk::verifikasi_mst_stock_bbk');
    $routes->post('saveDetailbbk', 'Bbk::saveDetailbbk');
    $routes->post('list_tmp_bbk_dtl', 'Bbk::list_tmp_bbk_dtl');
    $routes->post('list_balance_bbk', 'Bbk::list_balance_bbk');
    $routes->post('load_bbm_to_bbk', 'Bbk::load_bbm_to_bbk');
    $routes->add('finalEntrybbk', 'Bbk::finalEntrybbk');
    $routes->add('clearEntrybbk', 'Bbk::clearEntrybbk');
    $routes->add('list_bbk', 'Bbk::list_bbk');
    $routes->add('update_stock_bbk(:any)', 'Bbk::update_stock_bbk$1');
    $routes->add('detail_stock_bbk(:any)', 'Bbk::detail_stock_bbk$1');
    $routes->add('cancel_stock_bbk(:any)', 'Bbk::cancel_stock_bbk$1');
    $routes->add('api_print_stock_bbk(:any)', 'Bbk::api_print_stock_bbk$1');
    $routes->add('print_stock_bbk(:any)', 'Bbk::print_stock_bbk$1');
    $routes->add('reprint', 'Bbk::reprint');

    $routes->add('void', 'Bbk::void');
    $routes->add('void_stock_bbk', 'Bbk::void_stock_bbk');
    $routes->add('clearEntryVoidbbk', 'Bbk::clearEntryVoidbbk');
    $routes->add('showing_item_void_mst', 'Bbk::showing_item_void_mst');
    $routes->add('showing_item_void_dtl', 'Bbk::showing_item_void_dtl');
    $routes->add('list_tmp_void_dtl', 'Bbk::list_tmp_void_dtl');
    $routes->add('verifikasi_void_mst_stock_bbk', 'Bbk::verifikasi_void_mst_stock_bbk');
    $routes->add('list_pbl', 'Bbk::list_pbl');
    $routes->add('import_pbl', 'Bbk::import_pbl');
    $routes->add('test_print', 'Bbk::test_print');
    $routes->add('final_tmp_pbl', 'Bbk::final_tmp_pbl');
    $routes->add('clear_tmp_pbl', 'Bbk::clear_tmp_pbl');
    $routes->add('proses_upload_pbl', 'Bbk::proses_upload_pbl');
    $routes->add('list_pbl_wacc_pagination', 'Bbk::list_pbl_wacc_pagination');



});

$routes->group('/stock/report', ["namespace" => "App\Controllers\Stock"], function ($routes) {
    $routes->add('/', 'Report::index');
    $routes->add('sisastockgdg', 'Report::sisastockgdg');
    $routes->add('show_sisastockgdg', 'Report::show_sisastockgdg');
    $routes->add('api_sisastockgdg(:any)', 'Report::api_sisastockgdg$1');
    $routes->add('sisastockposition', 'Report::sisastockposition');
    $routes->add('show_sisastockgdg_position', 'Report::show_sisastockgdg_position');
    $routes->add('api_sisastockgdg_position(:any)', 'Report::api_sisastockgdg_position$1');
    $routes->add('kstockgdg', 'Report::kstockgdg');
    $routes->add('show_kstockgdg', 'Report::show_kstockgdg');
    $routes->add('api_kstockgdg(:any)', 'Report::api_kstockgdg$1');
    $routes->add('lbmbarang', 'Report::lbmbarang');
    $routes->add('api_lbmbarang(:any)', 'Report::api_lbmbarang$1');
    $routes->add('show_lbmbarang', 'Report::show_lbmbarang');

    $routes->add('po_peritem', 'Report::po_peritem');
    $routes->add('api_po_peritem(:any)', 'Report::api_po_peritem$1');
    $routes->add('show_po_peritem', 'Report::show_po_peritem');

    $routes->add('lbkbarang', 'Report::lbkbarang');
    $routes->add('api_lbkbarang(:any)', 'Report::api_lbkbarang$1');
    $routes->add('show_lbkbarang', 'Report::show_lbkbarang');
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
    $routes->add('globalmodule/list_gradecust', 'Globalmodule::list_gradecust');
    $routes->add('globalmodule/list_salesman', 'Globalmodule::list_salesman');
    $routes->add('globalmodule/list_kolektor', 'Globalmodule::list_kolektor');
    $routes->add('globalmodule/list_coa', 'Globalmodule::list_coa');
    $routes->add('globalmodule/list_currency', 'Globalmodule::list_currency');
    $routes->add('globalmodule/list_customer', 'Globalmodule::list_customer');


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

//Absensi Mesin & Koneksi
$routes->group('/absenmesin', ["namespace" => "App\Controllers\Absenmesin"], function ($routes) {
    $routes->add('/', 'Absenmesin::index');
    $routes->add("set_mConnection", "Absenmesin::set_mConnection");
    $routes->add("mc_vAttendance", "Absenmesin::mc_vAttendance");
    $routes->add("mc_userabsen", "Absenmesin::mc_userabsen");
    $routes->add("testKoneksiMesin(:any)", "Absenmesin::testKoneksiMesin$1");
    $routes->post("save_mc_userabsen", "Absenmesin::save_mc_userabsen");
});

//Abscut
$routes->group('/trans/abscut', ["namespace" => "App\Controllers\Trans"], function ($routes) {
    $routes->add('/', 'Abscut::index');
    $routes->add("mkriteria", "Abscut::mkriteria");
    $routes->add("list_mkriteria", "Abscut::list_mkriteria");
    $routes->get("showing_data_kriteria(:any)", "Abscut::showing_data_kriteria$1");
    $routes->post("saveEntryKriteria", "Abscut::saveEntryKriteria");
    $routes->add("input_new", "Abscut::input_new");
    $routes->add("list_karyawan", "Abscut::list_karyawan");
    $routes->get("showDetailNik(:any)", "Abscut::showDetailNik$1");
    $routes->post("list_mtypecuti", "Abscut::list_mtypecuti");
    $routes->post("saveAbscut", "Abscut::saveAbscut");
    $routes->get("edit(:any)", "Abscut::edit$1");
    $routes->get("showDetailAbscut(:any)", "Abscut::showDetailAbscut$1");
    $routes->get("list_mtypecuti(:any)", "Abscut::list_mtypecuti$1");
    $routes->get("detail(:any)", "Abscut::detail$1");
    $routes->post("downloadExcel", "Abscut::downloadExcel");
    $routes->post("downloadResumeExcel", "Abscut::downloadResumeExcel");
    $routes->get("test_download", "Abscut::test_download");
    $routes->get("importabscut", "Abscut::importabscut");
    $routes->get("clear_tmp", "Abscut::clear_tmp");
    $routes->get("final_data", "Abscut::final_data");
    $routes->post("proses_upload", "Abscut::proses_upload");
    $routes->get("cutibalance", "Abscut::cutibalance");
    $routes->post("cutibalancedtl", "Abscut::cutibalancedtl");
    $routes->post("pr_hitungallcuti", "Abscut::pr_hitungallcuti");
    $routes->get("excel_blc(:any)", "Abscut::excel_blc$1");
    $routes->get("hps_abscut(:any)", "Abscut::hps_abscut$1");
});

//Lembur Karyawan
$routes->group('/trans/lembur', ["namespace" => "App\Controllers\Trans"], function ($routes) {
    $routes->add('/', 'Lembur::index');
    $routes->add("input_new", "Lembur::input_new");
    $routes->add("list_karyawan", "Lembur::list_karyawan");
    $routes->get("showDetailNik(:any)", "Lembur::showDetailNik$1");
    $routes->post("saveLembur", "Lembur::saveLembur");
    $routes->post("recalculate", "Lembur::recalculate");
    $routes->post("downloadExcel", "Lembur::downloadExcel");
    $routes->get("import_lembur", "Lembur::import_lembur");
    $routes->add("proses_xls_lembur", "Lembur::proses_xls_lembur");
    $routes->get("clear_tmp", "Lembur::clear_tmp");
    $routes->get("immgp", "Lembur::immgp");
    $routes->get("edit(:any)", "Lembur::edit$1");
    $routes->get("showDetailLembur(:any)", "Lembur::showDetailLembur$1");
    $routes->post("proses_komponen_gp", "Lembur::proses_komponen_gp");
    $routes->get("final_data", "Lembur::final_data");
    $routes->get("hps_lembur(:any)", "Lembur::hps_lembur$1");

});

//Share Location Report
$routes->group('/trans/shareloc', ["namespace" => "App\Controllers\Trans"], function ($routes) {
    $routes->add('/', 'Shareloc::index');
    $routes->add("report_checkinout", "Shareloc::report_checkinout");
    $routes->post("list_report_checkinout", "Shareloc::list_report_checkinout");
});

//Trans / Koreksi / Koreksi CUti
$routes->group('/trans/koreksi', ["namespace" => "App\Controllers\Trans"], function ($routes) {
    $routes->add('/', 'Koreksi::index');
    $routes->add("koreksicuti", "Koreksi::koreksicuti");
    $routes->post("list_koreksi_cuti", "Koreksi::list_koreksi_cuti");
    $routes->get("showOn_Koreksi_Cuti", "Koreksi::showOn_Koreksi_Cuti");
    $routes->get("input_koreksicuti", "Koreksi::input_koreksicuti");
    $routes->get("clearEntry_Koreksi_Cuti", "Koreksi::clearEntry_Koreksi_Cuti");
    $routes->post("list_tmp_koreksi_cuti_dtl", "Koreksi::list_tmp_koreksi_cuti_dtl");
    $routes->post("saveKoreksiCutiTmpMst", "Koreksi::saveKoreksiCutiTmpMst");
    $routes->post("saveKoreksiCutiTmpDtl", "Koreksi::saveKoreksiCutiTmpDtl");
    $routes->get("showing_koreksi_cuti_dtl(:any)", "Koreksi::showing_koreksi_cuti_dtl$1");
    $routes->get("cancelKoreksiCuti(:any)", "Koreksi::cancelKoreksiCuti$1");
    $routes->get("detailKoreksiCuti(:any)", "Koreksi::detailKoreksiCuti$1");
    $routes->get("showOn_Koreksi_Cuti_Trx(:any)", "Koreksi::showOn_Koreksi_Cuti_Trx$1");
    $routes->post("finishInputKoreksiCuti", "Koreksi::finishInputKoreksiCuti");
    $routes->post("list_trx_koreksi_cuti_dtl", "Koreksi::list_trx_koreksi_cuti_dtl");

});



//report
$routes->group('/report/presensi', ["namespace" => "App\Controllers\Report"], function ($routes) {
    $routes->add('', 'Presensi::index');
    $routes->add('showupPresensi', 'Presensi::showupPresensi');
    $routes->get('downloadPresensi(:any)', 'Presensi::downloadPresensi$1');
    $routes->get('detail', 'Presensi::detail');
    $routes->add('showupPresensiTransready', 'Presensi::showupPresensiTransready');
});

//manpower
$routes->group('/report/manpower', ["namespace" => "App\Controllers\Report"], function ($routes) {
    $routes->add('', 'Manpower::index');
    $routes->add('downloadManpowerPosition', 'Manpower::downloadManpowerPosition');
});

//Contacs
$routes->group('/contacts/supplier', ["namespace" => "App\Controllers\Contacts"], function ($routes) {
    $routes->add('/', 'Supplier::index');
    $routes->add('list_msupplier', 'Supplier::list_msupplier');
    $routes->add('list_mgroup', 'Supplier::list_mgroup');
    $routes->add('import_suppliers', 'Supplier::import_suppliers');
    $routes->add('suppliers_group', 'Supplier::suppliers_group');
    $routes->add('input_supplier', 'Supplier::input_supplier');
    $routes->get("showing_data_supplier/(:any)", "Supplier::showing_data_supplier/$1");
    $routes->post("saveSupplier", "Supplier::saveSupplier");

});

/* GROUP FOR MOBILE INVENTORY */
$routes->group('/intern/mobile', ["namespace" => "App\Controllers\Intern"], function ($routes) {
    $routes->add('/', 'Mobile::index');
    $routes->post("downloadMst", "Mobile::downloadMst");
});

//Capital
$routes->group('/capital/masters', ["namespace" => "App\Controllers\Capital"], function ($routes) {
    $routes->add('/', 'Masters::index');
    $routes->add('group', 'Masters::group');
    $routes->add('list_mgroup', 'Masters::list_mgroup');
    $routes->get('showing_data_mgroup/(:any)', 'Masters::showing_data_mgroup/$1');
    $routes->post('saveGrouping', 'Masters::saveGrouping');
    /*subgroup*/
    $routes->add('list_msubgroup', 'Masters::list_msubgroup');
    $routes->get('showing_data_msubgroup/(:any)', 'Masters::showing_data_msubgroup/$1');
    $routes->get('showing_data_kdlvl/(:any)', 'Masters::showing_data_kdlvl/$1');
    $routes->post('saveSubGrouping', 'Masters::saveSubGrouping');
    /*mtype*/
    $routes->add('list_mtype', 'Masters::showing_data_mtype');
    $routes->get('showing_data_mtype/(:any)', 'Masters::showing_data_mtype/$1');
    $routes->post('saveMtype', 'Masters::saveMtype');
    /*master_capital_authorization*/
    $routes->add('list_capital_authorization', 'Masters::list_capital_authorization');
    $routes->add('save_capital_authorization', 'Masters::save_capital_authorization');

    $routes->get('showing_data_kdlvl_msubgroup(:any)', 'Masters::showing_data_kdlvl_msubgroup$1');


});

$routes->group('/capital/administration', ["namespace" => "App\Controllers\Capital"], function ($routes) {
    $routes->add('/', 'Administration::index');
    $routes->add('admin_transaction', 'Administration::admin_transaction');
    $routes->add('list_karyawan', 'Administration::list_karyawan');
    $routes->add('list_pengajuan', 'Administration::list_pengajuan');

    $routes->add('save_capital_pengajuan', 'Administration::save_capital_pengajuan');
    $routes->add('showing_data_pengajuan/(:any)', 'Administration::showing_data_pengajuan/$1');
    $routes->add('permintaan_persetujuan_mngr/(:any)', 'Administration::permintaan_persetujuan_mngr/$1');

    $routes->add('approvals_admin', 'Administration::approvals_admin');
    $routes->add('list_pengajuan_approvals_admin', 'Administration::list_pengajuan_approvals_admin');

    $routes->add('persetujuan_mngr/(:any)', 'Administration::persetujuan_mngr/$1');
    $routes->add('tolak_persetujuan_mngr(:any)', 'Administration::tolak_persetujuan_mngr$1');

    $routes->add('input_transaction', 'Administration::input_transaction');
    $routes->add('list_pengajuan_all', 'Administration::list_pengajuan_all');
    $routes->add('save_capital_it_input_transaction', 'Administration::save_capital_it_input_transaction');

    $routes->add('print_pengajuan_dept_get', 'Administration::print_pengajuan_dept_get');
    $routes->add('print_pengajuan_dept', 'Administration::print_pengajuan_dept');
    $routes->add('api_print_capital_pengajuan(:any)', 'Administration::api_print_capital_pengajuan');
    $routes->add('saveReview', 'Administration::saveReview');

    $routes->add('jasa_pembayaran', 'Administration::jasa_pembayaran');
    $routes->add('save_jasa_pembayaran', 'Administration::save_jasa_pembayaran');
    $routes->add('list_jasa_pembayaran', 'Administration::list_jasa_pembayaran');
    $routes->add('delete_pembayaran(:any)', 'Administration::delete_pembayaran$1');
    $routes->add('detail_jasa_pembayaran(:any)', 'Administration::detail_jasa_pembayaran$1');
    $routes->add('showing_jasa_pembayaran/(:any)', 'Administration::showing_jasa_pembayaran/$1');
    $routes->add('delete_detail_pembayaran/(:any)', 'Administration::delete_detail_pembayaran/$1');
    $routes->add('showing_jasa_pembayaran_detail/(:any)', 'Administration::showing_jasa_pembayaran_detail/$1');
    $routes->add('save_jasa_pembayaran_detail', 'Administration::save_jasa_pembayaran_detail');
    $routes->add('list_jasa_pembayaran_detail', 'Administration::list_jasa_pembayaran_detail');
});
$routes->group('/capital/budget', ["namespace" => "App\Controllers\Capital"], function ($routes) {
    $routes->add('/', 'Budget::index');
    $routes->add('admin_transaction', 'Budget::admin_transaction');
    $routes->add('save_capital_pengajuan_budget', 'Administration::save_capital_pengajuan_budget');
});

//BBM
$routes->group('/import/winacc', ["namespace" => "App\Controllers\Import"], function ($routes) {
    $routes->add('/', 'Winacc::index');
    $routes->add('pp', 'Winacc::pp');
    $routes->add('proses_upload_pp', 'Winacc::proses_upload_pp');
    $routes->add('po', 'Winacc::po');
    $routes->add('proses_upload_po', 'Winacc::proses_upload_po');
    $routes->add('penerimaan', 'Winacc::penerimaan');
    $routes->add('proses_upload_penerimaan', 'Winacc::proses_upload_penerimaan');
    $routes->add('syncronize', 'Winacc::syncronize');

    $routes->add('cetak_bt_bbm(:any)', 'Winacc::cetak_bt_bbm$1');
    $routes->add('testpdf(:any)', 'Winacc::testpdf$1');
    $routes->add('renderpdf(:any)', 'Winacc::renderpdf$1');


});



//FORM
$routes->group('/form/pa', ["namespace" => "App\Controllers\Form"], function ($routes) {

    //PERANGKAT PRIBADI
    $routes->add('/', 'Form::index');
    $routes->post("list_perangkat", "Form::list_perangkat");
    $routes->post("saveDataPerangkat", "Form::saveDataPerangkat");
    $routes->add("showDetailPerangkat(:any)", "Form::showDetailPerangkat$1");
    $routes->add("edit_perangkat(:any)", "Form::edit_perangkat$1");
    $routes->add("detail_perangkat(:any)", "Form::detail_perangkat$1");
    $routes->add("del_perangkat(:any)", "Form::del_perangkat$1");
    $routes->add("input_perangkat", "Form::input_perangkat");

    $routes->add("updateIzinMasuk", "Form::updateIzinMasuk");
    $routes->add("updateIzinKeluar", "Form::updateIzinKeluar");
    $routes->add("updateStatus", "Form::updateStatus");
    $routes->get('getDataFormPerangkat/(:any)', 'Form::getDataFormPerangkat/$1');
    $routes->get('print_perangkat(:any)', 'Form::print_perangkat$1');
    $routes->get('api_print_perangkat(:any)', 'Form::api_print_perangkat$1');


    //IZIN INTERNET
    $routes->add('internet', 'Form::internet');
    $routes->post("list_internet", "Form::list_internet");
    $routes->post("saveDataInternet", "Form::saveDataInternet");
    $routes->add("showDetailInternet(:any)", "Form::showDetailInternet$1");
    $routes->add("edit_internet(:any)", "Form::edit_internet$1");
    $routes->add("detail_internet(:any)", "Form::detail_internet$1");
    $routes->add("del_internet(:any)", "Form::del_internet$1");
    $routes->add("updateStatusInternet", "Form::updateStatusInternet");
    $routes->add("input_internet", "Form::input_internet");
    $routes->post("saveDataInternet", "Form::saveDataInternet");
    $routes->add("print_internet", "Form::print_internet");
    $routes->get('getDataFormInternet/(:any)', 'Form::getDataFormInternet/$1');
    $routes->add("api_print_internet(:any)", "Form::api_print_internet$1");
    $routes->add("verifikasi_internet(:any)", "Form::verifikasi_internet$1");


    
    //KELUAR LAPTOP
    $routes->add('laptop', 'Form::laptop');
    $routes->post("list_perangkatkeluar", "Form::list_laptop");
    $routes->post("saveDataPerangkatKeluar", "Form::saveDataPerangkatKeluar");
    $routes->add("showDetailPerangkatKeluar(:any)", "Form::showDetailPerangkatKeluar$1");
    $routes->add("edit_perangkatkeluar(:any)", "Form::edit_perangkatkeluar$1");
    $routes->add("detail_perangkatkeluar(:any)", "Form::detail_perangkatkeluar$1");
    $routes->add("del_perangkatkeluar(:any)", "Form::del_perangkatkeluar$1");
    $routes->add("input_perangkatkeluar", "Form::input_perangkatkeluar");
    $routes->add("updateStatusPK", "Form::updateStatusPK");
    $routes->get('getDataFormPerangkatKeluar/(:any)', 'Form::getDataFormPerangkatKeluar/$1');
    $routes->get('print_perangkatkeluar(:any)', 'Form::print_perangkatkeluar$1');
    $routes->get('api_print_perangkatkeluar(:any)', 'Form::api_print_perangkatkeluar$1');

    //MEDIA PENYIMPANAN
    $routes->add('media', 'Form::media');
    $routes->post("list_media", "Form::list_media");
    $routes->post("saveDataMedia", "Form::saveDataMedia");
    $routes->add("showDetailMedia(:any)", "Form::showDetailMedia$1");
    $routes->add("edit_media(:any)", "Form::edit_media$1");
    $routes->add("del_media(:any)", "Form::del_media$1");
    $routes->add("input_media", "Form::input_media");


    //SOFTWARE HARDWARE
    $routes->add('pengadaan_hwsw', 'Form::pengadaan_hwsw');
    $routes->post("list_pengadaan_hwsw", "Form::list_pengadaan_hwsw");
    $routes->post("saveDataPengadaanHwsw", "Form::saveDataPengadaanHwsw");
    $routes->add("showDetailReq(:any)", "Form::showDetailReq$1");
    $routes->add("edit_pengadaan_hwsw(:any)", "Form::edit_pengadaan_hwsw$1");
    $routes->add("del_pengadaan_hwsw(:any)", "Form::del_pengadaan_hwsw$1");
    $routes->add("input_pengadaan_hwsw", "Form::input_pengadaan_hwsw");
    $routes->get('print_pengadaan_hwsw(:any)', 'Form::print_pengadaan_hwsw$1');
    $routes->get('api_print_pengadaan_hwsw(:any)', 'Form::api_print_pengadaan_hwsw$1');

    //SOFTWARE HARDWARE
    $routes->add('list_label_form_temporary', 'Form::list_label_form_temporary');
    $routes->add('print_labels_form', 'Form::print_labels_form');
    $routes->add('getDataFormLabel', 'Form::getDataFormLabel');
    $routes->add('printFromListPrinter', 'Form::printFromListPrinter');
    $routes->add('api_show_form_labels(:any)', 'Form::api_show_form_labels$1');
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
