<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('ru', 'Home::index_ru'); 

$routes->view('thankyou', 'thankyou'); // uri and pagename

$routes->group('admin', static function ($routes) {
    $routes->get('register', 'AuthController::register');
    $routes->post('register', 'AuthController::store');
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::authenticate');
    $routes->get('logout', 'AuthController::logout');

    $routes->get('/', 'AdminController::index', ['filter' => 'adminauth']);
    $routes->get('settings', 'CafeSettingsController::edit', ['filter' => 'adminauth']);
    $routes->post('settings', 'CafeSettingsController::update', ['filter' => 'adminauth']);
    $routes->post('settings/password', 'CafeSettingsController::updatePassword', ['filter' => 'adminauth']);

    $routes->get('categories', 'CategoryController::index', ['filter' => 'adminauth']);
    $routes->get('categories/new', 'CategoryController::new', ['filter' => 'adminauth']);
    $routes->post('categories', 'CategoryController::create', ['filter' => 'adminauth']);
    $routes->get('categories/(:num)/edit', 'CategoryController::edit/$1', ['filter' => 'adminauth']);
    $routes->post('categories/(:num)', 'CategoryController::update/$1', ['filter' => 'adminauth']);
    $routes->post('categories/(:num)/delete', 'CategoryController::delete/$1', ['filter' => 'adminauth']);

    $routes->get('menu-items', 'MenuItemController::index', ['filter' => 'adminauth']);
    $routes->get('menu-items/new', 'MenuItemController::new', ['filter' => 'adminauth']);
    $routes->post('menu-items', 'MenuItemController::create', ['filter' => 'adminauth']);
    $routes->get('menu-items/(:num)/edit', 'MenuItemController::edit/$1', ['filter' => 'adminauth']);
    $routes->post('menu-items/(:num)', 'MenuItemController::update/$1', ['filter' => 'adminauth']);
    $routes->post('menu-items/(:num)/delete', 'MenuItemController::delete/$1', ['filter' => 'adminauth']);
});

$routes->get('(:segment)/manifest.webmanifest', 'PwaController::manifest/$1');
$routes->get('(:segment)/sw.js', 'PwaController::serviceWorker/$1');
$routes->get('(:segment)/menu.json', 'MenuJsonController::index/$1', ['filter' => 'menuthrottle']);
$routes->get('(:segment)/menu', 'MenuJsonController::index/$1', ['filter' => 'menuthrottle']);
$routes->get('(:segment)', 'PublicController::index/$1');
