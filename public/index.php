<?php

use App\Controller\HomeController;
use App\Controller\NotFoundController;
use App\Controller\ProductController;

require_once __DIR__ . '/../vendor/autoload.php';

$uri = $_SERVER['REQUEST_URI'];

$routes = [
    '/' => [new HomeController, 'render'],
    '/product' => [new ProductController, 'render'],
    '/product/create' => [new ProductController, 'create']
];

$controller = $routes[$uri] ?? [new NotFoundController, 'render'];

call_user_func($controller);