<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->group("auth", ["namespace" => "App\Controllers\Auth"], function ($routes) {
    $routes->get('login', 'Login::index');
    $routes->post('login-post', 'Login::admin_login_post');
   
});
