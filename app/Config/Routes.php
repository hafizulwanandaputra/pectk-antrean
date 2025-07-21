<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// AUTH
$routes->get('/', 'Auth::index');
$routes->post('/(?i)check-login', 'Auth::check_login');
$routes->get('/(?i)logout', 'Auth::logout');

// HOME
$routes->get('/(?i)home', 'Home::index');
$routes->get('/(?i)home/(?i)list_antrean', 'Home::list_antrean');
$routes->get('/(?i)home/(?i)cetak_antrean', 'Home::cetak_antrean');

// ANTREAN
$routes->get('/(?i)antrean', 'Antrean::index');
$routes->get('/(?i)antrean/(?i)list_antrean', 'Antrean::list_antrean');
$routes->post('/(?i)antrean/(?i)panggil_antrean', 'Antrean::panggil_antrean');
$routes->delete('/(?i)antrean/(?i)hapus_antrean', 'Antrean::hapus_antrean');

// JAMINAN
$routes->get('/(?i)jaminan', 'Jaminan::index');
$routes->post('/(?i)jaminan/(?i)jaminanlist', 'Jaminan::jaminanlist');
$routes->get('/(?i)jaminan/(?i)jaminan/(:any)', 'Jaminan::jaminan/$1');
$routes->post('/(?i)jaminan/(?i)create', 'Jaminan::create');
$routes->post('/(?i)jaminan/(?i)update', 'Jaminan::update');
$routes->delete('/(?i)jaminan/(?i)delete/(:any)', 'Jaminan::delete/$1');

// PENGGUNA
$routes->get('/(?i)admin', 'Admin::index');
$routes->post('/(?i)admin/(?i)adminlist', 'Admin::adminlist');
$routes->get('/(?i)admin/(?i)admin/(:any)', 'Admin::admin/$1');
$routes->post('/(?i)admin/(?i)create', 'Admin::create');
$routes->post('/(?i)admin/(?i)update', 'Admin::update');
$routes->post('/(?i)admin/(?i)resetpassword/(:any)', 'Admin::resetpassword/$1');
$routes->post('/(?i)admin/(?i)activate/(:any)', 'Admin::activate/$1');
$routes->post('/(?i)admin/(?i)deactivate/(:any)', 'Admin::deactivate/$1');
$routes->delete('/(?i)admin/(?i)delete/(:any)', 'Admin::delete/$1');

// SETTINGS
$routes->get('/(?i)settings', 'Settings::index');

// CHANGE CASHIER PASSWORD
$routes->get('/(?i)settings/(?i)pwdtransaksi', 'Settings::pwdTransaksi');
$routes->post('/(?i)settings/(?i)updatepwdtransaksi', 'Settings::updatePwdTransaksi');

// SESSION MANAGER
$routes->get('/(?i)settings/(?i)sessions', 'Sessions::index');
$routes->post('/(?i)settings/(?i)sessionslist', 'Sessions::sessionslist');
$routes->delete('/(?i)settings/(?i)flush', 'Sessions::flush');
$routes->delete('/(?i)settings/(?i)deleteexpired', 'Sessions::deleteexpired');
$routes->delete('/(?i)settings/(?i)deletesession/(:any)', 'Sessions::deletesession/$1');

// CHANGE USER INFORMATION
$routes->get('/(?i)settings/(?i)edit', 'Settings::edit');
$routes->post('/(?i)settings/(?i)update', 'Settings::update');

// CHANGE PASSWORD
$routes->get('/(?i)settings/(?i)changepassword', 'ChangePassword::index');
$routes->post('/(?i)settings/(?i)changepassword/(?i)update', 'ChangePassword::update');

// ABOUT SYSTEM
$routes->get('/(?i)settings/(?i)about', 'Settings::about');
