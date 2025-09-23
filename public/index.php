<?php

use App\Controller\FavoritoController;
use App\Controller\GaleriaController;
use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\NotFoundController;
use App\Controller\VelaController;
use App\Model\Vela;

require_once __DIR__ . '/../vendor/autoload.php';

$uri = $_SERVER['REQUEST_URI'];

$routes = [
    '/home' => [new HomeController, 'render'],
    '/galeria' => [new VelaController, 'listar'],
    '/login' => [new LoginController, 'render'],
    '/login/autenticar' => [new LoginController, 'autenticar'],
    '/logout' => [new LoginController, 'logout'],
    '/favorito' =>[new FavoritoController, 'listar'],
    '/favorito/toggle' =>[new FavoritoController, 'toggle'],
    //'/product' => [new ProductController, 'render'],
    //'/product/create' => [new ProductController, 'create']
];

$controller = $routes[$uri] ?? [new NotFoundController, 'render'];

call_user_func($controller);