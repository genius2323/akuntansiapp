<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

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

        // Master Produk
        $routes->get('products', 'ProductController::index');
        $routes->post('products/create', 'ProductController::create');
        $routes->get('products/(:num)/edit', 'ProductController::edit/$1');
        $routes->put('products/(:num)', 'ProductController::update/$1');
        $routes->delete('products/(:num)', 'ProductController::delete/$1');

        // Input Penjualan
        $routes->get('penjualan', 'PenjualanController::index');
        $routes->post('penjualan/store', 'PenjualanController::store');
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
