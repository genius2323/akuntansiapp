<?php
namespace Config;
// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Master Model
$routes->get('model', 'ModelController::index');
$routes->post('model/create', 'ModelController::create');
$routes->get('model/(:num)/edit', 'ModelController::edit/$1');
$routes->put('model/(:num)', 'ModelController::update/$1');
$routes->delete('model/(:num)', 'ModelController::delete/$1');

// Master Fiting
$routes->get('fiting', 'FitingController::index');
$routes->post('fiting/create', 'FitingController::create');
$routes->get('fiting/(:num)/edit', 'FitingController::edit/$1');
$routes->put('fiting/(:num)', 'FitingController::update/$1');
$routes->delete('fiting/(:num)', 'FitingController::delete/$1');

// Master Daya
$routes->get('daya', 'DayaController::index');
$routes->post('daya/create', 'DayaController::create');
$routes->get('daya/(:num)/edit', 'DayaController::edit/$1');
$routes->put('daya/(:num)', 'DayaController::update/$1');
$routes->delete('daya/(:num)', 'DayaController::delete/$1');

// Master Jumlah Mata
$routes->get('jumlah-mata', 'JumlahMataController::index');
$routes->post('jumlah-mata/create', 'JumlahMataController::create');
$routes->get('jumlah-mata/(:num)/edit', 'JumlahMataController::edit/$1');
$routes->put('jumlah-mata/(:num)', 'JumlahMataController::update/$1');
$routes->delete('jumlah-mata/(:num)', 'JumlahMataController::delete/$1');

// Batas Tanggal Sistem (General)
$routes->get('general/batasTanggalSistem', 'GeneralController::batasTanggalSistem');
$routes->post('general/setBatasTanggalSistem', 'GeneralController::setBatasTanggalSistem');

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Rute Publik (Login/Logout)
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('authenticate', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

// Rute yang Dilindungi (Memerlukan Login)
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Rute Dashboard Utama per Departemen
    $routes->get('pos', 'PosController::index', ['filter' => 'department:1']);
    $routes->get('accounting', 'AccountingController::index', ['filter' => 'department:2']);
    $routes->get('general', 'GeneralController::index', ['filter' => 'department:3']);

    // Rute untuk Departemen General (ID: 3)
    $routes->group('', ['filter' => 'department:3'], function ($routes) {

        // Manajemen User
        $routes->get('users', 'UserController::index');
        $routes->post('users/create', 'UserController::store');
        $routes->get('users/edit/(:num)', 'UserController::edit/$1');
        $routes->post('users/update/(:num)', 'UserController::update/$1');
        $routes->post('users/delete/(:num)', 'UserController::delete/$1');
        $routes->get('users/trash', 'UserController::trash');
        $routes->post('users/restore/(:num)', 'UserController::restore/$1');
        $routes->post('users/force-delete/(:num)', 'UserController::forceDelete/$1');


        // Menu Otoritas Kategori & Satuan
        $routes->get('general/otoritasKategori', 'GeneralController::otoritasKategori');
        $routes->post('general/setOtoritasKategori', 'GeneralController::setOtoritasKategori');
        $routes->get('general/otoritasSatuan', 'GeneralController::otoritasSatuan');
        $routes->post('general/setOtoritasSatuan', 'GeneralController::setOtoritasSatuan');
        // Otoritas Produk
        $routes->get('general/otoritasProduk', 'GeneralController::otoritasProduk');
        $routes->post('general/setOtoritasProduk', 'GeneralController::setOtoritasProduk');
        // Otoritas User
        $routes->get('general/otoritasUser', 'GeneralOtoritasUserController::otoritasUser');
        $routes->post('general/setOtoritasUser', 'GeneralOtoritasUserController::setOtoritasUser');

        // Master Kategori
        $routes->get('categories', 'CategoryController::index');
        $routes->post('categories/create', 'CategoryController::create');
        $routes->get('categories/(:num)/edit', 'CategoryController::edit/$1');
        $routes->put('categories/(:num)', 'CategoryController::update/$1');
        $routes->delete('categories/(:num)', 'CategoryController::delete/$1');

        // Master Satuan (BARU)
        $routes->get('satuan', 'SatuanController::index');
        $routes->post('satuan/create', 'SatuanController::create');
        $routes->get('satuan/(:num)/edit', 'SatuanController::edit/$1');
        $routes->put('satuan/(:num)', 'SatuanController::update/$1');
        $routes->delete('satuan/(:num)', 'SatuanController::delete/$1');


        // Master Jenis
        $routes->get('jenis', 'JenisController::index');
        $routes->post('jenis/create', 'JenisController::create');
        $routes->get('jenis/(:num)/edit', 'JenisController::edit/$1');
        $routes->put('jenis/(:num)', 'JenisController::update/$1');
        $routes->delete('jenis/(:num)', 'JenisController::delete/$1');

        // Master Pelengkap
        $routes->get('pelengkap', 'PelengkapController::index');
        $routes->post('pelengkap/create', 'PelengkapController::create');
        $routes->get('pelengkap/(:num)/edit', 'PelengkapController::edit/$1');
        $routes->put('pelengkap/(:num)', 'PelengkapController::update/$1');
        $routes->delete('pelengkap/(:num)', 'PelengkapController::delete/$1');

        // Master Gondola
        $routes->get('gondola', 'GondolaController::index');
        $routes->post('gondola/create', 'GondolaController::create');
        $routes->get('gondola/(:num)/edit', 'GondolaController::edit/$1');
        $routes->put('gondola/(:num)', 'GondolaController::update/$1');
        $routes->delete('gondola/(:num)', 'GondolaController::delete/$1');

        // Master Merk
        $routes->get('merk', 'MerkController::index');
        $routes->post('merk/create', 'MerkController::create');
        $routes->get('merk/(:num)/edit', 'MerkController::edit/$1');
        $routes->put('merk/(:num)', 'MerkController::update/$1');
        $routes->delete('merk/(:num)', 'MerkController::delete/$1');

        // Master Warna Sinar
        $routes->get('warnasinar', 'WarnaSinarController::index');
        $routes->post('warnasinar/create', 'WarnaSinarController::create');
        $routes->get('warnasinar/(:num)/edit', 'WarnaSinarController::edit/$1');
        $routes->put('warnasinar/(:num)', 'WarnaSinarController::update/$1');
        $routes->delete('warnasinar/(:num)', 'WarnaSinarController::delete/$1');

        // Master Ukuran Barang
        $routes->get('ukuranbarang', 'UkuranBarangController::index');
        $routes->post('ukuranbarang/create', 'UkuranBarangController::create');
        $routes->get('ukuranbarang/(:num)/edit', 'UkuranBarangController::edit/$1');
        $routes->put('ukuranbarang/(:num)', 'UkuranBarangController::update/$1');
        $routes->delete('ukuranbarang/(:num)', 'UkuranBarangController::delete/$1');

        // Master Voltase
        $routes->get('voltase', 'VoltaseController::index');
        $routes->post('voltase/create', 'VoltaseController::create');
        $routes->get('voltase/(:num)/edit', 'VoltaseController::edit/$1');
        $routes->put('voltase/(:num)', 'VoltaseController::update/$1');
        $routes->delete('voltase/(:num)', 'VoltaseController::delete/$1');

        // Master Dimensi
        $routes->get('dimensi', 'DimensiController::index');
        $routes->post('dimensi/create', 'DimensiController::create');
        $routes->get('dimensi/(:num)/edit', 'DimensiController::edit/$1');
        $routes->put('dimensi/(:num)', 'DimensiController::update/$1');
        $routes->delete('dimensi/(:num)', 'DimensiController::delete/$1');

        // Master Warna Body
        $routes->get('warnabody', 'WarnaBodyController::index');
        $routes->post('warnabody/create', 'WarnaBodyController::create');
        $routes->get('warnabody/(:num)/edit', 'WarnaBodyController::edit/$1');
        $routes->put('warnabody/(:num)', 'WarnaBodyController::update/$1');
        $routes->delete('warnabody/(:num)', 'WarnaBodyController::delete/$1');

        // Master Warna Bibir
        $routes->get('warnabibir', 'WarnaBibirController::index');
        $routes->post('warnabibir/create', 'WarnaBibirController::create');
        $routes->get('warnabibir/(:num)/edit', 'WarnaBibirController::edit/$1');
        $routes->put('warnabibir/(:num)', 'WarnaBibirController::update/$1');
        $routes->delete('warnabibir/(:num)', 'WarnaBibirController::delete/$1');

        // Master Kaki
        $routes->get('kaki', 'KakiController::index');
        $routes->post('kaki/create', 'KakiController::create');
        $routes->get('kaki/(:num)/edit', 'KakiController::edit/$1');
        $routes->put('kaki/(:num)', 'KakiController::update/$1');
        $routes->delete('kaki/(:num)', 'KakiController::delete/$1');

        // Master Produk
        $routes->get('products', 'ProductController::index');
        $routes->post('products/create', 'ProductController::create');
        $routes->get('products/(:num)/edit', 'ProductController::edit/$1');
        $routes->put('products/(:num)', 'ProductController::update/$1');
        $routes->delete('products/(:num)', 'ProductController::delete/$1');

        // Input Penjualan
        $routes->get('penjualan', 'PenjualanController::index');
        $routes->post('penjualan/store', 'PenjualanController::store');
        // Penjualan
        $routes->get('penjualan/dashboard', 'PenjualanController::dashboard');
    });
});


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
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
