<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/', 'C_Auth::index');
$routes->get('/changePassword', 'C_Auth::changePassword');
$routes->post('/login', 'C_Auth::login');
$routes->get('logout', 'C_Auth::logout');
$routes->post('C_Auth/login', 'C_Auth::login');

$routes->get('/get_apps', 'C_MenuAccess::get_apps');
$routes->get('/get_groupnames', 'C_MenuAccess::get_groupnames');
$routes->get('/get_menus', 'C_MenuAccess::get_menus');

$routes->get('C_MenuAccess/get_employee', 'C_MenuAccess::get_employee');
$routes->get('C_MenuAccess/get_user', 'C_MenuAccess::get_user');
$routes->get('C_MenuAccess/get_users_change', 'C_MenuAccess::get_users_change');
$routes->get('C_MenuAccess/get_app', 'C_MenuAccess::get_app');
$routes->get('C_MenuAccess/get_group', 'C_MenuAccess::get_group');
$routes->get('C_MenuAccess/get_menu', 'C_MenuAccess::get_menu');

$routes->post('C_MenuAccess/get_useraccess', 'C_MenuAccess::get_useraccess');
$routes->post('C_MenuAccess/save_access', 'C_MenuAccess::save_access');
$routes->post('C_MenuAccess/save_app', 'C_MenuAccess::save_app');
$routes->post('C_MenuAccess/save_group', 'C_MenuAccess::save_group');
$routes->post('C_MenuAccess/save_menu', 'C_MenuAccess::save_menu');

//09-04 master equipment
$routes->get('/MstEquipment', 'MstEquipment\MstEquipmentController::index');
$routes->get('/MstEquipment/getDataMfg', 'MstEquipment\MstEquipmentController::getDataMfg');
$routes->post('/MstEquipment/add', 'MstEquipment\MstEquipmentController::store');
$routes->post('/MstEquipment/edit', 'MstEquipment\MstEquipmentController::edit');
$routes->post('/MstEquipment/update', 'MstEquipment\MstEquipmentController::update');
$routes->post('/MstEquipment/delete', 'MstEquipment\MstEquipmentController::delete');
$routes->post('MstEquipment/checkAssetInAcceptance', 'MstEquipment\MstEquipmentController::checkAssetInAcceptance');
$routes->post('MstEquipment/checkDuplicate', 'MstEquipment\MstEquipmentController::checkDuplicate');
$routes->get('/MstEquipment/getAssetNumbers', 'MstEquipment\MstEquipmentController::getAssetNumbers');

// 08-04 tambahan monic master category
$routes->get('/MstCategory', 'MstCategory\MstCategoryController::index');
$routes->get('/MstCategory/getDataCategory', 'MstCategory\MstCategoryController::getDataCategory');
$routes->post('/MstCategory/add', 'MstCategory\MstCategoryController::store');
$routes->post('/MstCategory/edit', 'MstCategory\MstCategoryController::edit');
$routes->post('/MstCategory/update', 'MstCategory\MstCategoryController::update');
$routes->post('/MstCategory/delete', 'MstCategory\MstCategoryController::delete');

// 09-04 tambahan transaksi sparepart
$routes->get('/TransSparepart', 'TransSparepart\TransSparepartController::index');
$routes->get('/TransSparepart/getData', 'TransSparepart\TransSparepartController::getData');
$routes->get('/TransSparepart/getAvailableAssets', 'TransSparepart\TransSparepartController::getAvailableAssets');
$routes->get('/TransSparepart/searchEmployees', 'TransSparepart\TransSparepartController::searchEmployees');
$routes->post('/TransSparepart/add', 'TransSparepart\TransSparepartController::store');
$routes->post('/TransSparepart/edit', 'TransSparepart\TransSparepartController::edit');
$routes->post('/TransSparepart/update', 'TransSparepart\TransSparepartController::update');
$routes->post('/TransSparepart/delete', 'TransSparepart\TransSparepartController::delete');

$routes->group('MstAdjustAssy', ['namespace' => 'App\Controllers\master_assy'], function ($routes) {
    $routes->get('/', 'MstAdjustAssy::index');
    $routes->get('get_data', 'MstAdjustAssy::get_data');
    $routes->post('cek_data', 'MstAdjustAssy::cek_data');
    $routes->post('update_data', 'MstAdjustAssy::update_data');
    $routes->post('update_status_data', 'MstAdjustAssy::update_status_data');
});

// 10-04 tambahan monic master reason
$routes->get('/MstReason', 'MstReason\MstReasonController::index');
$routes->get('/MstReason/getDataReason', 'MstReason\MstReasonController::getDataReason');
$routes->post('/MstReason/add', 'MstReason\MstReasonController::store');
$routes->post('/MstReason/edit', 'MstReason\MstReasonController::edit');
$routes->post('/MstReason/update', 'MstReason\MstReasonController::update');
$routes->post('/MstReason/delete', 'MstReason\MstReasonController::delete');

// // 16-04 transaksi ticketing
// $routes->get('/TransTicketing', 'TransTicketing\TransTicketingController::index');
// $routes->get('/TransTicketing/getData', 'TransTicketing\TransTicketingController::getData');
// $routes->get('/TransTicketing/getTicketById', 'TransTicketing\TransTicketingController::getTicketById');
// $routes->get('/TransTicketing/searchEmployees', 'TransTicketing\TransTicketingController::searchEmployees');
// $routes->get('/TransTicketing/searchSystemEmployees', 'TransTicketing\TransTicketingController::searchSystemEmployees');
// $routes->get('/TransTicketing/getEmployeeDetails', 'TransTicketing\TransTicketingController::getEmployeeDetails');
// $routes->post('/TransTicketing/add', 'TransTicketing\TransTicketingController::store');
// $routes->post('/TransTicketing/update', 'TransTicketing\TransTicketingController::update');
// $routes->post('/TransTicketing/delete', 'TransTicketing\TransTicketingController::delete');

// 09-06 Ticketing Transaction
// Ticketing Transaction
$routes->group('TransTicketing', ['namespace' => 'App\Controllers\TransTicketing'], function ($routes) {
    $routes->get('/', 'TransTicketingController::index');
    $routes->get('getData', 'TransTicketingController::getData');
    $routes->get('getTicketById', 'TransTicketingController::getTicketById');
    $routes->post('store', 'TransTicketingController::store');
    $routes->post('update', 'TransTicketingController::update');
    $routes->post('delete', 'TransTicketingController::delete');
    $routes->get('getEmployees', 'TransTicketingController::getEmployees');
    $routes->get('searchEmployees', 'TransTicketingController::searchEmployees');
    $routes->get('getSystemEmployees', 'TransTicketingController::getSystemEmployees');
    $routes->get('searchAssetNo', 'TransTicketingController::searchAssetNo');
    $routes->get('getAssetNo', 'TransTicketingController::getAssetNo');
    $routes->get('getCategories', 'TransTicketingController::getCategories');
});

// 08-04 tambahan monic transaksi disposal
$routes->get('/TransDisposal', 'TransDisposal\TransDisposalController::index');
$routes->get('/TransDisposal/getDataDisposal', 'TransDisposal\TransDisposalController::getDataDisposal');
$routes->post('/TransDisposal/add', 'TransDisposal\TransDisposalController::store');
$routes->post('/TransDisposal/edit', 'TransDisposal\TransDisposalController::edit');
$routes->post('/TransDisposal/update', 'TransDisposal\TransDisposalController::update');
$routes->post('/TransDisposal/delete', 'TransDisposal\TransDisposalController::delete');
$routes->get('/TransDisposal/getAssetNumbers', 'TransDisposal\TransDisposalController::getAssetNumbers');
$routes->post('/TransDisposal/checkUnique', 'TransDisposal\TransDisposalController::checkUnique');
$routes->post('/TransDisposal/checkUniqueEdit', 'TransDisposal\TransDisposalController::checkUniqueEdit');
$routes->post('/TransDisposal/checkUnique', 'TransDisposal\TransDisposalController::checkUnique');
$routes->get('/TransDisposal/getReasons', 'TransDisposal\TransDisposalController::getReasons');

// 23-04 software license
$routes->get('/SoftwareLicense', 'SoftwareLicense\SoftwareLicenseController::index');
$routes->get('/SoftwareLicense/getDataSoftwareLicense', 'SoftwareLicense\SoftwareLicenseController::getDataSoftwareLicense');
$routes->post('/SoftwareLicense/add', 'SoftwareLicense\SoftwareLicenseController::add');
$routes->post('/SoftwareLicense/edit', 'SoftwareLicense\SoftwareLicenseController::edit');
$routes->post('/SoftwareLicense/update', 'SoftwareLicense\SoftwareLicenseController::update');
$routes->post('/SoftwareLicense/delete', 'SoftwareLicense\SoftwareLicenseController::delete');
$routes->get('/SoftwareLicense/getPOData', 'SoftwareLicense\SoftwareLicenseController::getPOData');
$routes->post('/SoftwareLicense/checkDuplicate', 'SoftwareLicense\SoftwareLicenseController::checkDuplicate');
$routes->get('/SoftwareLicense/getNextId', 'SoftwareLicense\SoftwareLicenseController::getNextId');

// --- Rute untuk Licensed PC ---
$routes->get('/SoftwareLicense/getLicensedPcs/(:num)', 'SoftwareLicense\SoftwareLicenseController::getLicensedPcs/$1');
// !!! NEW ROUTE !!!
$routes->get('/SoftwareLicense/countLicensedPcs/(:num)', 'SoftwareLicense\SoftwareLicenseController::countLicensedPcs/$1');
// !!! END NEW ROUTE !!!
$routes->post('/SoftwareLicense/addLicensedPc', 'SoftwareLicense\SoftwareLicenseController::addLicensedPc');
$routes->post('/SoftwareLicense/editLicensedPc', 'SoftwareLicense\SoftwareLicenseController::editLicensedPc');
$routes->post('/SoftwareLicense/updateLicensedPc', 'SoftwareLicense\SoftwareLicenseController::updateLicensedPc');
$routes->post('/SoftwareLicense/deleteLicensedPc', 'SoftwareLicense\SoftwareLicenseController::deleteLicensedPc');
// Ganti dengan ini:
$routes->get('/SoftwareLicense/getEquipmentData', 'SoftwareLicense\SoftwareLicenseController::getEquipmentData'); // <-- UBAH RUTE INI!
$routes->get('/SoftwareLicense/getEmployeeData', 'SoftwareLicense\SoftwareLicenseController::getEmployeeData');
// Existing routes...

// --- Rute untuk Export Excel ---
$routes->get('/SoftwareLicense/exportExcel', 'SoftwareLicense\SoftwareLicenseController::exportExcel');


// // 14-05 transaksi handover
// // Transaksi Handover
// $routes->get('/TransHandover', 'TransHandover\TransHandoverController::index');
// $routes->get('/TransHandover/getHandoverData', 'TransHandover\TransHandoverController::getHandoverData');
// $routes->get('/TransHandover/getHandoverDetailData', 'TransHandover\TransHandoverController::getHandoverDetailData');
// $routes->get('/TransHandover/getHandoverById', 'TransHandover\TransHandoverController::getHandoverById');
// $routes->get('/TransHandover/getHandoverDetailById', 'TransHandover\TransHandoverController::getHandoverDetailById');
// $routes->get('/TransHandover/checkRecordNoExists', 'TransHandover\TransHandoverController::checkRecordNoExists');
// $routes->get('/TransHandover/searchEmployees', 'TransHandover\TransHandoverController::searchEmployees');
// $routes->get('/TransHandover/searchSystemEmployees', 'TransHandover\TransHandoverController::searchSystemEmployees');
// $routes->get('/TransHandover/getEmployeeDetails', 'TransHandover\TransHandoverController::getEmployeeDetails');
// $routes->get('TransHandover/searchAssets', 'TransHandover\TransHandoverController::searchAssets');
// $routes->get('TransHandover/getEquipmentByAssetNo', 'TransHandover\TransHandoverController::getEquipmentByAssetNo');
// $routes->get('TransHandover/searchEquipmentBySerialNumber', 'TransHandover\TransHandoverController::searchEquipmentBySerialNumber');
// $routes->get('TransHandover/getEquipmentBySerialNumber', 'TransHandover\TransHandoverController::getEquipmentBySerialNumber');
// $routes->get('/TransHandover/getEquipmentCategories', 'TransHandover\TransHandoverController::getEquipmentCategories');
// $routes->post('/TransHandover/addHandover', 'TransHandover\TransHandoverController::storeHandover');
// $routes->post('/TransHandover/addHandoverDetail', 'TransHandover\TransHandoverController::storeHandoverDetail');
// $routes->post('/TransHandover/updateHandover', 'TransHandover\TransHandoverController::updateHandover');
// $routes->post('/TransHandover/updateHandoverDetail', 'TransHandover\TransHandoverController::updateHandoverDetail');
// $routes->post('/TransHandover/deleteHandover', 'TransHandover\TransHandoverController::deleteHandover');
// $routes->post('/TransHandover/deleteHandoverDetail', 'TransHandover\TransHandoverController::deleteHandoverDetail');
// $routes->get('/TransHandover/export_pdf', 'TransHandover\TransHandoverController::export_pdf');



// 09-05 Handover Transaction
$routes->group('TransHandover', ['namespace' => 'App\Controllers\TransHandover'], function ($routes) {
    $routes->get('/', 'TransHandoverController::index');
    $routes->get('getHandoverData', 'TransHandoverController::getHandoverData');
    $routes->get('getHandoverById', 'TransHandoverController::getHandoverById');
    $routes->post('storeHandover', 'TransHandoverController::storeHandover');
    $routes->post('updateHandover', 'TransHandoverController::updateHandover');
    $routes->post('deleteHandover', 'TransHandoverController::deleteHandover');
    
    // Perubahan: Mengganti VerifyRecordNo menjadi checkRecordNoExists agar sesuai dengan nama fungsi di controller lama
    $routes->get('checkRecordNoExists', 'TransHandoverController::checkRecordNoExists'); 

    $routes->get('getHandoverDetailData', 'TransHandoverController::getHandoverDetailData');
    $routes->get('getHandoverDetailById', 'TransHandoverController::getHandoverDetailById');
    $routes->post('storeHandoverDetail', 'TransHandoverController::storeHandoverDetail');
    $routes->post('updateHandoverDetail', 'TransHandoverController::updateHandoverDetail');
    $routes->post('deleteHandoverDetail', 'TransHandoverController::deleteHandoverDetail');
    
    // Menggunakan getEmployeeDetails agar konsisten dengan penamaan di controller lama
    $routes->get('getEmployeeDetails', 'TransHandoverController::getEmployeeDetails'); 
    $routes->get('searchEmployees', 'TransHandoverController::searchEmployees');
    // Menggunakan searchSystemEmployees agar konsisten dengan penamaan di controller lama
    $routes->get('searchSystemEmployees', 'TransHandoverController::searchSystemEmployees'); 

    // Menggunakan searchAssets agar konsisten dengan penamaan di controller lama
    $routes->get('searchAssets', 'TransHandoverController::searchAssets'); 
    $routes->get('getEquipmentByAssetNo', 'TransHandoverController::getEquipmentByAssetNo');
    $routes->get('searchEquipmentBySerialNumber', 'TransHandoverController::searchEquipmentBySerialNumber');
    $routes->get('getEquipmentBySerialNumber', 'TransHandoverController::getEquipmentBySerialNumber');
    
    // Menggunakan getEquipmentCategories agar konsisten dengan penamaan di controller lama
    $routes->get('getEquipmentCategories', 'TransHandoverController::getEquipmentCategories'); 

    // Menambahkan kembali rute untuk export PDF
    $routes->get('export_pdf', 'TransHandoverController::export_pdf');
});

// New: Master PC Client
// app/Config/Routes.php
$routes->group('MstPCClient', ['namespace' => 'App\Controllers\PCClient'], function ($routes) {
    $routes->get('/', 'PCClientController::index');
    // ... rest of the routes (no change to the controller names here, just the namespace)
    $routes->get('getData', 'PCClientController::getData');
    $routes->get('getPCClientById', 'PCClientController::getPCClientById');
    $routes->post('store', 'PCClientController::store');
    $routes->post('update', 'PCClientController::update');
    $routes->post('delete', 'PCClientController::delete');
    $routes->get('getAssetNo', 'PCClientController::getAssetNo');
    $routes->get('searchAssetNo', 'PCClientController::searchAssetNo');
    $routes->get('getIPAddresses', 'PCClientController::getIPAddresses');
    $routes->get('searchIPAddresses', 'PCClientController::searchIPAddresses');
    $routes->post('updateIPStatus', 'PCClientController::updateIPStatus');
    $routes->get('getEmployees', 'PCClientController::getEmployees');
    $routes->get('searchEmployees', 'PCClientController::searchEmployees');
    $routes->get('getAreas', 'PCClientController::getAreas');
});

// // 09-05 PC Client Transaction
// $routes->group('TransPCClient', ['namespace' => 'App\Controllers\TransPCClient'], function ($routes) {
//     $routes->get('/', 'TransPCClientController::index');
//     $routes->get('getData', 'TransPCClientController::getData');
//     $routes->get('getPCClientById', 'TransPCClientController::getPCClientById');
//     $routes->post('store', 'TransPCClientController::store');
//     $routes->post('update', 'TransPCClientController::update');
//     $routes->post('delete', 'TransPCClientController::delete');
//     $routes->get('getAssetNo', 'TransPCClientController::getAssetNo');
//     $routes->get('searchAssetNo', 'TransPCClientController::searchAssetNo');
//     $routes->get('getIPAddresses', 'TransPCClientController::getIPAddresses');
//     $routes->get('searchIPAddresses', 'TransPCClientController::searchIPAddresses');
//     $routes->post('updateIPStatus', 'TransPCClientController::updateIPStatus');
//     $routes->get('getEmployees', 'TransPCClientController::getEmployees');
//     $routes->get('searchEmployees', 'TransPCClientController::searchEmployees');
//     $routes->get('getAreas', 'TransPCClientController::getAreas');
// });



// --- Rute untuk Master IP (MstIP) ---
$routes->get('/MstIP', 'MstIP\MstIPController::index');
$routes->get('/MstIP/getData', 'MstIP\MstIPController::getData');
$routes->post('/MstIP/getDetail', 'MstIP\MstIPController::getDetail');
$routes->post('/MstIP/toggleStatus', 'MstIP\MstIPController::toggleStatus'); // Rute baru untuk toggle status

// --- Rute untuk PC Server (Aligned with usermenu "MstPCServer") ---
$routes->get('/MstPCServer', 'PCServer\PCServerController::index'); // Main page route
$routes->get('/MstPCServer/getDataPCServer', 'PCServer\PCServerController::getDataPCServer'); // AJAX route for DataTable
$routes->post('/MstPCServer/add', 'PCServer\PCServerController::add');
$routes->post('/MstPCServer/edit', 'PCServer\PCServerController::edit');
$routes->post('/MstPCServer/update', 'PCServer\PCServerController::update');
$routes->post('/MstPCServer/delete', 'PCServer\PCServerController::delete');
$routes->post('/MstPCServer/checkDuplicate', 'PCServer\PCServerController::checkDuplicate');

// Rute baru untuk Export Excel
$routes->get('/MstPCServer/exportExcel', 'PCServer\PCServerController::exportExcel'); // New route for exporting Excel

// Rute untuk finder m_itequipment
$routes->get('/MstPCServer/getEquipmentData', 'PCServer\PCServerController::getEquipmentData');

/////////////////////////////
// --- Routes for Master VLAN (Aligned with usermenu "MstVlan") ---
// 'id' in these routes refers to the auto-increment primary key (tv_id)
$routes->get('/MstVlan', 'MstVlan\MstVlanController::index'); // Main page route
$routes->get('/MstVlan/getDataVlan', 'MstVlan\MstVlanController::getDataVlan'); // AJAX route for DataTable
$routes->post('/MstVlan/add', 'MstVlan\MstVlanController::add'); // Add data
$routes->post('/MstVlan/edit', 'MstVlan\MstVlanController::edit'); // Fetch data for editing
$routes->post('/MstVlan/update', 'MstVlan\MstVlanController::update'); // Update data
$routes->post('/MstVlan/delete', 'MstVlan\MstVlanController::delete'); // Delete data
$routes->post('/MstVlan/checkDuplicateName', 'MstVlan\MstVlanController::checkDuplicateName'); // Check for duplicate VLAN Name
$routes->post('/MstVlan/checkDuplicateVlanId', 'MstVlan\MstVlanController::checkDuplicateVlanId'); // Check for duplicate User-Input VLAN ID

/////////////////////////////


// --- Rute untuk Master Switch Managed (Aligned with usermenu "MstSwitchManaged") ---
$routes->get('/MstSwitchManaged', 'SwitchManaged\SwitchManagedController::index'); // Main page route
$routes->get('/MstSwitchManaged/getDataSwitchManaged', 'SwitchManaged\SwitchManagedController::getDataSwitchManaged'); // AJAX route for DataTable
$routes->post('/MstSwitchManaged/add', 'SwitchManaged\SwitchManagedController::add');
$routes->post('/MstSwitchManaged/edit', 'SwitchManaged\SwitchManagedController::edit');
$routes->post('/MstSwitchManaged/update', 'SwitchManaged\SwitchManagedController::update');
$routes->post('/MstSwitchManaged/delete', 'SwitchManaged\SwitchManagedController::delete');
$routes->post('/MstSwitchManaged/checkDuplicate', 'SwitchManaged\SwitchManagedController::checkDuplicate');

// Rute baru untuk Export Excel
$routes->get('/MstSwitchManaged/exportExcel', 'SwitchManaged\SwitchManagedController::exportExcel'); // New route for exporting Excel

// Rute untuk finder m_itequipment (if you want to use existing equipment data)
$routes->get('/MstSwitchManaged/getEquipmentData', 'SwitchManaged\SwitchManagedController::getEquipmentData');

// --- NEW: Rute untuk Switch Managed Detail ---
$routes->get('/MstSwitchManaged/getSwitchDetailPorts/(:num)', 'SwitchManaged\SwitchManagedController::getSwitchDetailPorts/$1'); // Get details for a specific switch (by sm_id_switch)
$routes->get('/MstSwitchManaged/countSwitchDetailPorts/(:num)', 'SwitchManaged\SwitchManagedController::countSwitchDetailPorts/$1'); // Count details for a specific switch
$routes->post('/MstSwitchManaged/addSwitchDetailPort', 'SwitchManaged\SwitchManagedController::addSwitchDetailPort');
$routes->post('/MstSwitchManaged/editSwitchDetailPort', 'SwitchManaged\SwitchManagedController::editSwitchDetailPort');
$routes->post('/MstSwitchManaged/updateSwitchDetailPort', 'SwitchManaged\SwitchManagedController::updateSwitchDetailPort');
$routes->post('/MstSwitchManaged/deleteSwitchDetailPort', 'SwitchManaged\SwitchManagedController::deleteSwitchDetailPort');

// NEW: Rute untuk mendapatkan data VLAN
$routes->get('/MstSwitchManaged/getVlanData', 'SwitchManaged\SwitchManagedController::getVlanData');