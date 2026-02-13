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


$routes->group('/purchase/trans', ["namespace" => "App\Controllers\Purchase"], function ($routes) {
    

    $routes->add('pp', 'Purchase::pp');
    $routes->add('list_pp', 'Purchase::list_pp');
    $routes->add('addPP', 'Purchase::addPP');
    $routes->add('detailPP', 'Purchase::detailPP');
    $routes->add('list_tmp_pp_dtl', 'Purchase::list_tmp_pp_dtl');
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
    $routes->add('globalmodule/list_customer', 'Globalmodule::list_customer');
    $routes->add('globalmodule/list_golonganbarang', 'Globalmodule::list_golonganbarang');
    $routes->add('globalmodule/list_jenisproduk', 'Globalmodule::list_jenisproduk');
    $routes->add('globalmodule/list_kelompokbarang', 'Globalmodule::list_kelompokbarang');
    $routes->add('globalmodule/list_principal', 'Globalmodule::list_principal');
    $routes->add('globalmodule/list_supplier_new', 'Globalmodule::list_supplier_new');

    $routes->add('globalmodule/list_pp', 'Globalmodule::list_pp');

    $routes->add('globalmodule/list_branchjob', 'Globalmodule::list_branchjob');


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
